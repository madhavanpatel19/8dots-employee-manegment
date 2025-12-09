<?php
session_start();
include("includes/db.php");
require_once __DIR__ . '/includes/firebase_sync.php';

$db = firebase_db();

// Check admin session
if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('login.php','_self')</script>";
    exit;
}

// ------------ INPUTS (GET) ------------

// Get current year and month (for monthly view)
$current_year  = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$current_month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');

// Get selected employee (for monthly view)
$selected_emp_id = isset($_GET['emp_id']) ? (int)$_GET['emp_id'] : 0;

// Daily attendance mode?
$is_daily       = isset($_GET['daily']) && $_GET['daily'] == 1;
$selected_date  = isset($_GET['date']) ? $_GET['date'] : '';

// Normalise month
if ($current_month < 1)  $current_month = 1;
if ($current_month > 12) $current_month = 12;

// Days in current month
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $current_month, $current_year);

// ------------ ATTENDANCE TABLE CHECK ------------

$check_table   = mysqli_query($con, "SHOW TABLES LIKE 'attendance'");
$table_exists  = mysqli_num_rows($check_table) > 0;

if (!$table_exists) {
    // Create attendance table if it doesn't exist
    $create_table = "CREATE TABLE `attendance` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `emp_id` INT NOT NULL,
        `attendance_date` DATE NOT NULL,
        `check_in_time` TIME NULL,
        `status` ENUM('present', 'absent', 'leave') DEFAULT 'present',
        `remarks` VARCHAR(255),
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY `emp_date` (`emp_id`, `attendance_date`),
        FOREIGN KEY (`emp_id`) REFERENCES `emp_list` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB";
    mysqli_query($con, $create_table);
} else {
    // Ensure check_in_time column exists
    $col_check = mysqli_query($con, "SHOW COLUMNS FROM attendance LIKE 'check_in_time'");
    if ($col_check && mysqli_num_rows($col_check) === 0) {
        mysqli_query($con, "ALTER TABLE `attendance` ADD `check_in_time` TIME NULL AFTER `attendance_date`");
    }
}

// ------------ HELPER FUNCTIONS ------------

function get_employees($con)
{
    $arr = array();
    $q = "SELECT id, name FROM emp_list ORDER BY name ASC";
    $r = mysqli_query($con, $q);
    while ($row = mysqli_fetch_assoc($r)) {
        $arr[] = $row;
    }
    return $arr;
}

function get_employee($con, $emp_id)
{
    $emp_id = (int)$emp_id;
    $q = "SELECT id, name FROM emp_list WHERE id='$emp_id' LIMIT 1";
    $r = mysqli_query($con, $q);
    return mysqli_num_rows($r) ? mysqli_fetch_assoc($r) : null;
}

function get_attendance_month($con, $emp_id, $month, $year)
{
    $ret   = array();
    $emp_id = (int)$emp_id;
    $month  = (int)$month;
    $year   = (int)$year;
    $q = "SELECT * FROM attendance 
          WHERE emp_id='$emp_id' 
            AND MONTH(attendance_date)='$month' 
            AND YEAR(attendance_date)='$year'";
    $r = mysqli_query($con, $q);
    while ($rec = mysqli_fetch_assoc($r)) {
        $ret[$rec['attendance_date']] = $rec;
    }
    return $ret;
}

function get_daily_attendance($con, $date)
{
    $ret  = array();
    $date = mysqli_real_escape_string($con, $date);
    $q = "SELECT * FROM attendance WHERE attendance_date='$date'";
    $r = mysqli_query($con, $q);
    while ($rec = mysqli_fetch_assoc($r)) {
        $ret[(int)$rec['emp_id']] = $rec;
    }
    return $ret;
}

// Normalize check-in time to 24-hour format (HH:MM:SS) to avoid AM/PM misreads
function normalize_checkin_time($raw)
{
    if ($raw === null) return null;
    $raw = trim($raw);
    if ($raw === '') return null;

    $formats = array('H:i:s', 'H:i', 'g:i a', 'g:i A', 'g:i:s a', 'g:i:s A', 'h:i A', 'h:i a', 'h:i:s A', 'h:i:s a');
    foreach ($formats as $fmt) {
        $dt = DateTime::createFromFormat($fmt, $raw);
        if ($dt instanceof DateTime) {
            return $dt->format('H:i:s');
        }
    }

    // Fallback: basic HH:MM
    if (preg_match('/^(\\d{1,2}):(\\d{2})$/', $raw, $m)) {
        return sprintf('%02d:%02d:00', $m[1], $m[2]);
    }

    return null;
}

function save_attendance_record($con, $emp_id, $attendance_date, $status, $remarks = '', $check_in_time = null)
{
    global $db;
    $eid  = (int)$emp_id;
    $date = mysqli_real_escape_string($con, $attendance_date);
    $st   = mysqli_real_escape_string($con, $status);
    $rm_raw   = $remarks;
    $normalized_time = normalize_checkin_time($check_in_time);
    $time = $normalized_time ? mysqli_real_escape_string($con, $normalized_time) : null;

    // Auto-flag late if after 10:15 AM when marked present
    $late_cutoff = strtotime('1970-01-01 10:15:00');
    if ($time && $status === 'present') {
        $tstamp = strtotime('1970-01-01 ' . $normalized_time);
        if ($tstamp !== false && $tstamp > $late_cutoff && stripos($rm_raw, 'late') === false) {
            $rm_raw = ($rm_raw ? $rm_raw . ' | ' : '') . 'Late check-in (after 10:15 AM)';
        }
    }   

    $rm   = mysqli_real_escape_string($con, $rm_raw);

    $check = mysqli_query($con, "SELECT id FROM attendance WHERE emp_id='$eid' AND attendance_date='$date'");
    $row_id = null;
    if (mysqli_num_rows($check) > 0) {
        $existing = mysqli_fetch_assoc($check);
        $row_id = (int)$existing['id'];
        $update = "UPDATE attendance 
                   SET status='$st', remarks='$rm', check_in_time " . ($time !== null ? "='$time'" : "=NULL") . " 
                   WHERE emp_id='$eid' AND attendance_date='$date'";
        $ok = mysqli_query($con, $update);
    } else {
        $insert = "INSERT INTO attendance (emp_id, attendance_date, check_in_time, status, remarks) 
                   VALUES ('$eid', '$date', " . ($time !== null ? "'$time'" : "NULL") . ", '$st', '$rm')";
        $ok = mysqli_query($con, $insert);
        if ($ok) {
            $row_id = (int)mysqli_insert_id($con);
        }
    }

    if ($ok && $row_id !== null && isset($db)) {
        $rowRes = mysqli_query($con, "SELECT * FROM attendance WHERE id='{$row_id}' LIMIT 1");
        if ($rowRes && $row = mysqli_fetch_assoc($rowRes)) {
            try {
                firebase_sync_row($db, 'attendance', (string)$row_id, $row);
            } catch (Throwable $e) {
                // do not interrupt flow if Firebase fails
            }
        }
    }

    return $ok;
}

function save_daily_attendance_batch($con, $date, $emp_ids, $statuses, $remarks_arr, $checkins_arr)
{
    if (!$date || !is_array($emp_ids)) return false;
    foreach ($emp_ids as $idx => $e) {
        $eid = (int)$e;
        $st  = isset($statuses[$idx]) ? $statuses[$idx] : 'absent';
        $rm  = isset($remarks_arr[$idx]) ? $remarks_arr[$idx] : '';
        $ci  = isset($checkins_arr[$idx]) ? $checkins_arr[$idx] : null;
        save_attendance_record($con, $eid, $date, $st, $rm, $ci);
    }
    return true;
}

// ------------ FORM HANDLERS ------------

$message = null;

// Single record from modal (monthly view)
if (isset($_POST['save_attendance'])) {
    $emp_id          = isset($_POST['emp_id']) ? (int)$_POST['emp_id'] : 0;
    $attendance_date = isset($_POST['attendance_date']) ? $_POST['attendance_date'] : '';
    $status          = isset($_POST['status']) ? $_POST['status'] : '';
    $remarks         = isset($_POST['remarks']) ? $_POST['remarks'] : '';
    $check_in_time   = isset($_POST['check_in_time']) ? $_POST['check_in_time'] : '';

    // Validate: if status is 'leave', remarks are mandatory
    if ($status === 'leave' && empty(trim($remarks))) {
        $message = "Remarks are mandatory for Leave status!";
        // Repopulate modal fields with previous values
        echo '<script>document.addEventListener("DOMContentLoaded", function() {';
        echo 'openModal(' . json_encode($emp_id) . ', ' . json_encode($attendance_date) . ');';
        echo 'setTimeout(function(){';
        echo 'document.getElementById("status").value = "leave";';
        echo 'document.getElementById("remarks").value = ' . json_encode($remarks) . ';';
        echo '}, 100);';
        echo '});</script>';
    } elseif ($emp_id && $attendance_date && $status) {
        // Require check-in time only for presents
        if ($status === 'present' && empty($check_in_time)) {
            $message = "Check-in time is required for Present status.";
        } else {
            save_attendance_record($con, $emp_id, $attendance_date, $status, $remarks, $check_in_time ?: null);
            $message = "Attendance updated successfully!";
        }
    } else {
        $message = "Invalid attendance data.";
    }
}

// Daily batch save
if (isset($_POST['save_daily_attendance'])) {
    $date        = isset($_POST['attendance_date']) ? $_POST['attendance_date'] : '';
    $emp_ids     = isset($_POST['emp_id']) ? $_POST['emp_id'] : array();
    $statuses    = isset($_POST['status']) ? $_POST['status'] : array();
    $remarks_arr = isset($_POST['remarks_arr']) ? $_POST['remarks_arr'] : array();
    $checkins_arr = isset($_POST['check_in_time_arr']) ? $_POST['check_in_time_arr'] : array();

    if ($date && is_array($emp_ids)) {
        // Validate: if any employee has 'leave' status, check remarks
        $validation_error = false;
        $time_error = false;
        foreach ($emp_ids as $idx => $e) {
            $st = isset($statuses[$idx]) ? $statuses[$idx] : '';
            $rm = isset($remarks_arr[$idx]) ? trim($remarks_arr[$idx]) : '';
            $ci = isset($checkins_arr[$idx]) ? $checkins_arr[$idx] : '';
            if ($st === 'leave' && empty($rm)) {
                $validation_error = true;
                break;
            }
            if ($st === 'present' && empty($ci)) {
                $time_error = true;
                break;
            }
        }
        if (!$validation_error && !$time_error) {
            save_daily_attendance_batch($con, $date, $emp_ids, $statuses, $remarks_arr, $checkins_arr);
            $message = "Daily attendance saved successfully!";
            header('Location: attendance.php?daily=1&date=' . urlencode($date));
            exit;
        } else {
            // Repopulate daily form fields with previous values by keeping POST data in memory
            $_POST['emp_id'] = $emp_ids;
            $_POST['status'] = $statuses;
            $_POST['remarks_arr'] = $remarks_arr;
            $_POST['check_in_time_arr'] = $checkins_arr;
            if ($time_error) {
                $message = "Check-in time is required for all employees.";
            }
        }
    }
}

// ------------ LOAD DATA FOR VIEW ------------

$employees_array = get_employees($con);
$employee_data   = null;
$attendance_data = array();

if ($selected_emp_id > 0) {
    $employee_data = get_employee($con, $selected_emp_id);
    if ($employee_data) {
        $attendance_data = get_attendance_month($con, $selected_emp_id, $current_month, $current_year);
    }
}

$daily_attendance = array();
if ($is_daily && $selected_date) {
    $daily_attendance = get_daily_attendance($con, $selected_date);
}

// Decide which screen to show
$showSelectionScreen = (!$is_daily && $selected_emp_id == 0);
$showDataScreen      = ($is_daily && $selected_date) || ($selected_emp_id > 0);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Attendance Sheet</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="css/attendance.css" rel="stylesheet">

</head>

<body>
    <div id="wrapper">
        <?php include("includes/sidebar.php"); ?>

        <div id="page-wrapper">
            <div class="container-fluid">

                <!-- INITIAL SELECTION SCREEN -->
                <div id="selectionScreen" class="initial-selection" style="display: <?php echo $showSelectionScreen ? 'block' : 'none'; ?>;">
                    <h2>
                        <i class="fa fa-calendar" style="color:black"></i> Attendance Management
                        <a href="attendance_report.php" class="btn btn-info" style="background-color: #000000ff; color:white; float:right; font-size:14px;">
                            <i class="fa fa-file-text" style="color:white"></i> View Report
                        </a>
                    </h2>
                    <form id="selectionForm" method="GET" class="selection-form">
                        <div class="selection-controls">
                            <div class="selection-control">
                                <label>Select Mode:</label>
                                <div>
                                    <label>
                                        <input type="radio" name="mode" value="monthly" checked> Monthly
                                    </label>
                                    <label>
                                        <input type="radio" name="mode" value="daily"> Daily
                                    </label>
                                </div>
                            </div>

                            <div class="selection-control" id="empControl">
                                <label for="empSelectInitial">Employee:</label>
                                <select id="empSelectInitial" name="emp_id">
                                    <option value="">-- Choose Employee --</option>
                                    <?php foreach ($employees_array as $emp): ?>
                                        <option value="<?php echo $emp['id']; ?>">
                                            <?php echo htmlspecialchars($emp['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="selection-control" id="monthControl">
                                <label for="monthSelectInitial">Month:</label>
                                <input type="month" id="monthSelectInitial" name="month_date">
                            </div>

                            <div class="selection-control" id="dayControl" style="display:none;">
                                <label for="daySelectInitial">Date:</label>
                                <input type="date" id="daySelectInitial" name="day_date">
                            </div>
                        </div>
                        <button type="submit" class="start-btn">View Attendance</button>
                    </form>
                </div>

                <!-- DATA SCREEN (MONTHLY or DAILY) -->
                <div id="dataScreen" style="display: <?php echo $showDataScreen ? 'block' : 'none'; ?>;">
                    <div class="attendance-sheet-container">
                        <div class="sheet-header">
                            <h1><i class="fa fa-table"></i> Attendance Sheet</h1>
                            <button type="button" class="change-selection-btn" onclick="changeSelection()">
                                <i class="fa fa-arrow-left"></i> back
                            </button>
                        </div>

                        <!-- Monthly navigation only for monthly mode -->
                        <?php if (!$is_daily && $selected_emp_id > 0): ?>
                            <div class="sheet-controls">
                                <div class="control-group">
                                    <span class="month-year">
                                        <?php echo date('F Y', mktime(0, 0, 0, $current_month, 1, $current_year)); ?>
                                    </span>
                                    <div class="nav-buttons">
                                        <?php
                                        $prev_month = $current_month - 1;
                                        $prev_year  = $current_year;
                                        if ($prev_month < 1) {
                                            $prev_month = 12;
                                            $prev_year--;
                                        }
                                        $next_month = $current_month + 1;
                                        $next_year  = $current_year;
                                        if ($next_month > 12) {
                                            $next_month = 1;
                                            $next_year++;
                                        }
                                        $emp_param = "&emp_id=" . $selected_emp_id;
                                        ?>
                                        <a href="attendance.php?month=<?php echo $prev_month; ?>&year=<?php echo $prev_year . $emp_param; ?>" title="Previous Month">
                                            <i class="fa fa-chevron-left"></i>
                                        </a>
                                        <a href="attendance.php?month=<?php echo date('m'); ?>&year=<?php echo date('Y') . $emp_param; ?>" title="Current Month">
                                            <i class="fa fa-calendar"></i>
                                        </a>
                                        <a href="attendance.php?month=<?php echo $next_month; ?>&year=<?php echo $next_year . $emp_param; ?>" title="Next Month">
                                            <i class="fa fa-chevron-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Daily header info -->
                        <?php if ($is_daily && $selected_date): ?>
                            <div class="sheet-controls">
                                <div class="control-group">
                                    <span class="month-year">
                                        Daily Attendance – <?php echo date('d M Y', strtotime($selected_date)); ?>
                                    </span>
                                    <?php
                                    $prev_date = date('Y-m-d', strtotime($selected_date . ' -1 day'));
                                    $next_date = date('Y-m-d', strtotime($selected_date . ' +1 day'));
                                    ?>
                                    <div class="nav-buttons">
                                        <a href="attendance.php?daily=1&date=<?php echo $prev_date; ?>" title="Previous Day">
                                            <i class="fa fa-chevron-left"></i>
                                        </a>

                                        <!-- Calendar icon: toggles a visible date input -->
                                        <a href="javascript:void(0)" onclick="toggleDailyDatePicker()" title="Go to date">
                                            <i class="fa fa-calendar"></i>
                                        </a>

                                        <a href="attendance.php?daily=1&date=<?php echo $next_date; ?>" title="Next Day">
                                            <i class="fa fa-chevron-right"></i>
                                        </a>
                                    </div>

                                    <!-- Visible date input (initially hidden) -->
                                    <input
                                        type="date"
                                        id="dailyDatePicker"
                                        value="<?php echo htmlspecialchars($selected_date); ?>"
                                        style="display:none; margin-left:10px; padding:4px 6px; font-size:12px;">
                                </div>
                            </div>
                        <?php endif; ?>
                        <script>
                            // Toggle and use the date picker in the daily header
                            function toggleDailyDatePicker() {
                                var input = document.getElementById('dailyDatePicker');
                                if (!input) return;

                                // Toggle visibility
                                if (input.style.display === 'none' || input.style.display === '') {
                                    input.style.display = 'inline-block';
                                    input.focus();

                                    // Try showPicker if supported, otherwise rely on normal click
                                    try {
                                        if (input.showPicker) {
                                            input.showPicker();
                                        } else {
                                            input.click();
                                        }
                                    } catch (e) {
                                        input.click();
                                    }
                                } else {
                                    input.style.display = 'none';
                                }
                            }

                            // When user selects a date, load that date's daily attendance
                            document.addEventListener('DOMContentLoaded', function() {
                                var dailyInput = document.getElementById('dailyDatePicker');
                                if (dailyInput) {
                                    dailyInput.addEventListener('change', function() {
                                        if (this.value) {
                                            window.location.href = 'attendance.php?daily=1&date=' + encodeURIComponent(this.value);
                                        }
                                    });
                                }
                            });
                        </script>

                        <?php if ($message): ?>
                            <div class="alert">
                                <i class="fa fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
                            </div>
                        <?php endif; ?>

                        <!-- DAILY VIEW -->
                        <?php if ($is_daily && $selected_date): ?>
                            <div class="employee-info">
                                <h3><i class="fa fa-calendar"></i> Daily Attendance</h3>
                                <p>Date: <?php echo date('d M, Y', strtotime($selected_date)); ?> | Employees: <?php echo count($employees_array); ?></p>
                            </div>

                            <form method="POST">
                                <input type="hidden" name="attendance_date" value="<?php echo htmlspecialchars($selected_date); ?>">
                                <div class="attendance-table-wrapper">
                                    <table class="attendance-table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Employee ID</th>
                                                <th>Employee Name</th>
                                            <th style="min-width:220px;">Status</th>
                                            <th>Check-in Time</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            <?php foreach ($employees_array as $i => $emp):
                                                $eid         = (int)$emp['id'];
                                                // If POST (error), use submitted values, else use DB
                                                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['emp_id'][$i])) {
                                                    $pref_status = isset($_POST['status'][$i]) ? $_POST['status'][$i] : '';
                                                    $pref_remarks = isset($_POST['remarks_arr'][$i]) ? htmlspecialchars($_POST['remarks_arr'][$i]) : '';
                                                    $pref_checkin = isset($_POST['check_in_time_arr'][$i]) ? $_POST['check_in_time_arr'][$i] : '';
                                                } else {
                                                    $pref        = isset($daily_attendance[$eid]) ? $daily_attendance[$eid] : null;
                                                    $pref_status = $pref ? $pref['status'] : '';
                                                    $pref_remarks = $pref ? htmlspecialchars($pref['remarks']) : '';
                                                    $pref_checkin = $pref ? htmlspecialchars($pref['check_in_time']) : '';
                                                }
                                            ?>
                                                <tr>
                                                    <td><?php echo $i + 1; ?></td>
                                                    <td>
                                                        <?php echo $eid; ?>
                                                        <input type="hidden" name="emp_id[]" value="<?php echo $eid; ?>">
                                                    </td>
                                                    <td><?php echo htmlspecialchars($emp['name']); ?></td>
                                                    <td>
                                                        <div class="status-options">
                                                            <label class="status-btn<?php echo ($pref_status === 'present' || $pref_status == '') ? ' active' : ''; ?>">
                                                                <input type="radio" name="status[<?php echo $i; ?>]" value="present" <?php echo ($pref_status === 'present' || $pref_status == '') ? 'checked' : ''; ?>>P
                                                            </label>
                                                            <label class="status-btn<?php echo ($pref_status === 'absent') ? ' active' : ''; ?>">
                                                                <input type="radio" name="status[<?php echo $i; ?>]" value="absent" <?php echo ($pref_status === 'absent') ? 'checked' : ''; ?>>A
                                                            </label>
                                                            <label class="status-btn<?php echo ($pref_status === 'leave') ? ' active' : ''; ?>">
                                                                <input type="radio" name="status[<?php echo $i; ?>]" value="leave" <?php echo ($pref_status === 'leave') ? 'checked' : ''; ?>>L
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="time" name="check_in_time_arr[]" value="<?php echo $pref_checkin; ?>" class="form-control input-sm">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="remarks_arr[]" value="<?php echo $pref_remarks; ?>" placeholder="Optional remarks" class="form-control input-sm">
                                                        <?php
                                                        // Inline error for leave without remarks (after failed POST)
                                                        $show_leave_error = false;
                                                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_daily_attendance'])) {
                                                            if ($pref_status === 'leave' && trim($pref_remarks) === '') {
                                                                $show_leave_error = true;
                                                            }
                                                        }
                                                        if ($show_leave_error): ?>
                                                            <div class="inline-error" style="color:#d9534f; font-size:12px; margin-top:2px;">
                                                                Remarks are mandatory for Leave status!
                                                            </div>
                                                        <?php endif; ?>
                                                        <?php
                                                        $show_time_error = false;
                                                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_daily_attendance'])) {
                                                            if ($pref_status === 'present' && trim($pref_checkin) === '') {
                                                                $show_time_error = true;
                                                            }
                                                        }
                                                        if ($show_time_error): ?>
                                                            <div class="inline-error" style="color:#d9534f; font-size:12px; margin-top:2px;">
                                                                Check-in time is required for Present.
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div style="margin-top:15px;text-align:right;">
                                    <button type="submit" name="save_daily_attendance" class="btn-primary">
                                        <i class="fa fa-save"></i> Save
                                    </button>
                                </div>
                            </form>

                            <!-- MONTHLY VIEW -->
                        <?php elseif ($selected_emp_id > 0 && $employee_data): ?>
                            <div class="employee-info">
                                <h3><i class="fa fa-user"></i> <?php echo htmlspecialchars($employee_data['name']); ?></h3>
                                <p>Employee ID: <?php echo $selected_emp_id; ?></p>
                            </div>

                            <div class="attendance-table-wrapper">
                                <table class="attendance-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Day</th>
                                            <th>Status</th>
                                            <th>Check-in</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $present_count = 0;
                                        $absent_count  = 0;
                                        $leave_count   = 0;
                                        $marked_days   = 0;

                                        for ($day = 1; $day <= $days_in_month; $day++) {
                                            $date     = sprintf('%04d-%02d-%02d', $current_year, $current_month, $day);
                                            $day_name = date('D', strtotime($date));

                                            $status       = '';
                                            $status_class = 'unmarked';
                                            $remarks      = '';

                                            if (isset($attendance_data[$date])) {
                                                $status       = ucfirst($attendance_data[$date]['status']);
                                                $status_class = $attendance_data[$date]['status'];
                                                $remarks      = htmlspecialchars($attendance_data[$date]['remarks'] ?? '');
                                                $checkin      = htmlspecialchars($attendance_data[$date]['check_in_time'] ?? '');
                                                $marked_days++;

                                                if ($attendance_data[$date]['status'] === 'present') $present_count++;
                                                elseif ($attendance_data[$date]['status'] === 'absent')  $absent_count++;
                                                elseif ($attendance_data[$date]['status'] === 'leave')   $leave_count++;
                                            } else {
                                                $status = '-';
                                                $checkin = '';
                                            }

                                            echo '<tr>';
                                            echo '<td><strong>' . date('d-M', strtotime($date)) . '</strong></td>';
                                            echo '<td>' . $day_name . '</td>';
                                            echo '<td class="date-cell ' . $status_class . '" onclick="openModal(' . $selected_emp_id . ', \'' . $date . '\', \'' . $checkin . '\')" title="Click to mark attendance">' . $status . '</td>';
                                            echo '<td>' . ($checkin ? $checkin : '-') . '</td>';
                                            echo '<td class="remarks-cell">' . ($remarks ? $remarks : '-') . '</td>';
                                            echo '</tr>';
                                        }
                                        ?>
                                        <tr class="summary-row">
                                            <td colspan="3">TOTAL</td>
                                            <td>
                                                P: <?php echo $present_count; ?> |
                                                A: <?php echo $absent_count; ?> |
                                                L: <?php echo $leave_count; ?> |
                                                Marked: <?php echo $marked_days; ?>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        <?php else: ?>
                            <div class="alert">
                                <i class="fa fa-info-circle"></i> Please go back and select employee or daily mode.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL (Monthly mark attendance) -->
    <div id="attendanceModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <button type="button" class="close-modal" onclick="closeModal()">&times;</button>
                <h4>Mark Attendance</h4>
            </div>
            <form id="attendanceForm" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Date:</label>
                        <div id="modalDate" style="padding: 6px 10px; background: #f9f9f9; border-radius: 3px;"></div>
                    </div>
                    <div class="form-group">
                        <label for="status">Status: <span style="color: #d9534f;">*</span></label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="">Select Status</option>
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="leave">Leave</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="check_in_time">Check-in Time: <span style="color: #d9534f;">*</span></label>
                        <input type="time" id="check_in_time" name="check_in_time" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="remarks">Remarks: <span id="remarksRequired" style="color: #d9534f; display:none;">*</span></label>
                        <textarea id="remarks" name="remarks" class="form-control" placeholder="Optional remarks..."></textarea>
                    </div>
                    <input type="hidden" id="emp_id" name="emp_id">
                    <input type="hidden" id="attendance_date" name="attendance_date">
                    <input type="hidden" name="save_attendance" value="1">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-primary btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        // ---- LEAVE REMARKS VALIDATION ----
        function validateLeaveRemarks() {
            // For daily form
            const dailyForm = document.querySelector('form[name*="save_daily"]') ||
                document.querySelector('form').parentElement.querySelector('form[method="POST"]');
            let anyError = false;
            if (dailyForm) {
                const statuses = dailyForm.querySelectorAll('input[type="radio"]:checked');
                const remarksInputs = dailyForm.querySelectorAll('input[name*="remarks_arr"]');
                // Remove old errors
                dailyForm.querySelectorAll('.inline-error').forEach(function(el) {
                    el.remove();
                });
                for (let i = 0; i < statuses.length; i++) {
                    if (statuses[i].value === 'leave') {
                        const remark = remarksInputs[i] ? remarksInputs[i].value.trim() : '';
                        if (!remark) {
                            // Insert error below this remarks input
                            const errDiv = document.createElement('div');
                            errDiv.className = 'inline-error';
                            errDiv.style.color = '#d9534f';
                            errDiv.style.fontSize = '12px';
                            errDiv.style.marginTop = '2px';
                            errDiv.textContent = 'Remarks are mandatory for Leave status!';
                            remarksInputs[i].parentNode.appendChild(errDiv);
                            anyError = true;
                        }
                    }
                }
            }
            return !anyError;
        }

        // Intercept daily attendance form submission
        document.addEventListener('DOMContentLoaded', function() {
            const dailyForm = document.querySelector('form');
            if (dailyForm && dailyForm.querySelector('input[name="save_daily_attendance"]')) {
                dailyForm.addEventListener('submit', function(e) {
                    if (!validateLeaveRemarks()) {
                        e.preventDefault();
                        return false;
                    }
                });
            }

            // Modal form validation
            const attendanceForm = document.getElementById('attendanceForm');
            if (attendanceForm) {
                // Insert inline error container if not present
                let inlineErr = document.createElement('div');
                inlineErr.id = 'modalLeaveError';
                inlineErr.style.display = 'none';
                inlineErr.style.color = '#d9534f';
                inlineErr.style.fontSize = '13px';
                inlineErr.style.marginTop = '4px';
                attendanceForm.querySelector('.form-group:last-child').appendChild(inlineErr);

                attendanceForm.addEventListener('submit', function(e) {
                    const status = document.getElementById('status').value;
                    const remarks = document.getElementById('remarks').value.trim();
                    const checkIn = document.getElementById('check_in_time').value;
                    const errDiv = document.getElementById('modalLeaveError');
                    if (status === 'leave' && !remarks) {
                        e.preventDefault();
                        errDiv.textContent = 'Remarks are mandatory for Leave status!';
                        errDiv.style.display = 'block';
                        document.getElementById('remarks').focus();
                        return false;
                    }
                    if (status === 'present' && !checkIn) {
                        e.preventDefault();
                        errDiv.textContent = 'Check-in time is required for Present status.';
                        errDiv.style.display = 'block';
                        document.getElementById('check_in_time').focus();
                        return false;
                    }
                    errDiv.style.display = 'none';
                });
            }
        });

        // ---- SELECTION SCREEN ----
        document.addEventListener('DOMContentLoaded', function() {
            const selectionForm = document.getElementById('selectionForm');
            const modeRadios = document.querySelectorAll('input[name="mode"]');
            const empControl = document.getElementById('empControl');
            const monthControl = document.getElementById('monthControl');
            const dayControl = document.getElementById('dayControl');
            const empSelect = document.getElementById('empSelectInitial');
            const monthInput = document.getElementById('monthSelectInitial');
            const dayInput = document.getElementById('daySelectInitial');

            function updateModeUI() {
                const mode = document.querySelector('input[name="mode"]:checked').value;
                if (mode === 'daily') {
                    empControl.style.display = 'none';
                    monthControl.style.display = 'none';
                    dayControl.style.display = 'block';

                    empSelect.removeAttribute('required');
                    monthInput.removeAttribute('required');
                    dayInput.setAttribute('required', 'required');
                } else {
                    empControl.style.display = 'block';
                    monthControl.style.display = 'block';
                    dayControl.style.display = 'none';

                    empSelect.setAttribute('required', 'required');
                    monthInput.setAttribute('required', 'required');
                    dayInput.removeAttribute('required');
                }
            }

            modeRadios.forEach(function(r) {
                r.addEventListener('change', updateModeUI);
            });
            updateModeUI();

            // Default month: current month
            if (monthInput) {
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                monthInput.value = year + '-' + month;
            }

            // Default date: today
            if (dayInput) {
                const t = new Date();
                const y = t.getFullYear();
                const m = String(t.getMonth() + 1).padStart(2, '0');
                const d = String(t.getDate()).padStart(2, '0');
                dayInput.value = y + '-' + m + '-' + d;
            }

            if (selectionForm) {
                selectionForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const mode = document.querySelector('input[name="mode"]:checked').value;
                    if (mode === 'daily') {
                        const dayVal = dayInput.value;
                        if (!dayVal) {
                            alert('Please select a date for daily attendance.');
                            return;
                        }
                        window.location.href = 'attendance.php?daily=1&date=' + encodeURIComponent(dayVal);
                    } else {
                        const empId = empSelect.value;
                        const monthValue = monthInput.value;
                        if (!empId) {
                            alert('Please select an employee for monthly report.');
                            return;
                        }
                        if (!monthValue) {
                            alert('Please select month.');
                            return;
                        }
                        const parts = monthValue.split('-');
                        const year = parts[0];
                        const month = parts[1];
                        window.location.href = 'attendance.php?emp_id=' + empId + '&month=' + month + '&year=' + year;
                    }
                });
            }
        });

        function changeSelection() {
            window.location.href = 'attendance.php';
        }

        // ---- MODAL (monthly mark) ----
        function openModal(empId, date, checkIn = '') {
            document.getElementById('emp_id').value = empId;
            document.getElementById('attendance_date').value = date;
            document.getElementById('modalDate').textContent = new Date(date).toLocaleDateString('en-GB', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            document.getElementById('status').value = '';
            document.getElementById('remarks').value = '';
            document.getElementById('check_in_time').value = checkIn || '';
            document.getElementById('attendanceModal').style.display = 'block';
            updateRemarksRequirement();
        }

        function closeModal() {
            document.getElementById('attendanceModal').style.display = 'none';
        }

        // Show/hide remarks required indicator based on status
        function updateRemarksRequirement() {
            const status = document.getElementById('status').value;
            const remarksRequired = document.getElementById('remarksRequired');
            const remarksTextarea = document.getElementById('remarks');

            if (status === 'leave') {
                remarksRequired.style.display = 'inline';
                remarksTextarea.placeholder = 'Required - Please provide reason for leave...';
            } else {
                remarksRequired.style.display = 'none';
                remarksTextarea.placeholder = 'Optional remarks...';
            }
        }

        // Attach status change listener
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('status');
            if (statusSelect) {
                statusSelect.addEventListener('change', updateRemarksRequirement);
            }
        });

        window.onclick = function(event) {
            const modal = document.getElementById('attendanceModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        };

        // ---- DAILY STATUS BUTTON ACTIVE STATE ----
        (function() {
            function syncStatusButtons() {
                document.querySelectorAll('.status-options').forEach(function(group) {
                    const radios = group.querySelectorAll('input[type=radio]');
                    const checkInput = group.parentElement.parentElement.querySelector('input[name^="check_in_time_arr"]');
                    radios.forEach(function(r) {
                        const lbl = r.closest('label');
                        if (!lbl) return;
                        if (r.checked) lbl.classList.add('active');
                        else lbl.classList.remove('active');
                        r.addEventListener('change', function() {
                            radios.forEach(function(rr) {
                                const l2 = rr.closest('label');
                                if (!l2) return;
                                l2.classList.remove('active');
                            });
                            const l = this.closest('label');
                            if (l) l.classList.add('active');

                            // Toggle check-in required only for present
                            if (checkInput) {
                                if (this.value === 'present') {
                                    checkInput.setAttribute('required', 'required');
                                } else {
                                    checkInput.removeAttribute('required');
                                }
                            }
                        });
                    });

                    // initial state
                    if (checkInput) {
                        const checked = group.querySelector('input[type=radio]:checked');
                        if (checked && checked.value === 'present') {
                            checkInput.setAttribute('required', 'required');
                        } else {
                            checkInput.removeAttribute('required');
                        }
                    }
                });
            }

            function attachLabelClickers() {
                document.querySelectorAll('.status-btn').forEach(function(lbl) {
                    lbl.addEventListener('click', function() {
                        const input = lbl.querySelector('input[type=radio]');
                        if (input) {
                            input.checked = true;
                            const ev = new Event('change', {
                                bubbles: true
                            });
                            input.dispatchEvent(ev);
                        }
                    });
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    syncStatusButtons();
                    attachLabelClickers();
                });
            } else {
                syncStatusButtons();
                attachLabelClickers();
            }
        })();
    </script>
</body>

</html>
