<?php
include 'connection.php';

// Ensure monthly performance table exists
$createPerformance = "CREATE TABLE IF NOT EXISTS `emp_performance` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `emp_id` INT NOT NULL,
    `perf_year` INT NOT NULL,
    `perf_month` INT NOT NULL,
    `absent` TINYINT UNSIGNED DEFAULT 0,
    `late` TINYINT UNSIGNED DEFAULT 0,
    `task_sheet` TINYINT UNSIGNED DEFAULT 0,
    `performance_score` TINYINT UNSIGNED DEFAULT 0,
    `dressing_behaviour` TINYINT UNSIGNED DEFAULT 0,
    `rnd` TINYINT UNSIGNED DEFAULT 0,
    `total` INT DEFAULT 0,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `emp_month` (`emp_id`, `perf_year`, `perf_month`),
    FOREIGN KEY (`emp_id`) REFERENCES `emp_list` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB";
mysqli_query($con, $createPerformance);

// Month/year context (always current)
$currentYear  = (int)date('Y');
$currentMonth = (int)date('m');

$perfMessage = '';
$perfError = '';

// Save performance (upsert)
if (isset($_POST['save_performance'])) {
    $empId = isset($_POST['emp_id']) ? (int)$_POST['emp_id'] : 0;
    $pYear = isset($_POST['perf_year']) ? (int)$_POST['perf_year'] : $currentYear;
    $pMonth = isset($_POST['perf_month']) ? (int)$_POST['perf_month'] : $currentMonth;

    $maxScores = array(
        'absent' => 20,
        'late' => 10,
        'task_sheet' => 10,
        'performance_score' => 35,
        'dressing_behaviour' => 10,
        'rnd' => 15
    );

    $scores = array();
    foreach ($maxScores as $key => $limit) {
        // Use float for performance_score to preserve decimals, int for others
        if ($key === 'performance_score') {
            $val = isset($_POST[$key]) ? (float)$_POST[$key] : 0;
        } else {
            $val = isset($_POST[$key]) ? (int)$_POST[$key] : 0;
        }
        if ($val < 0) $val = 0;
        if ($val > $limit) $val = $limit;
        $scores[$key] = $val;
    }

    $total = array_sum($scores);
    if ($total > 100) {
        $perfError = "Total score cannot exceed 100.";
    } elseif ($empId > 0 && $pMonth >= 1 && $pMonth <= 12) {
        $insert = "INSERT INTO emp_performance 
            (emp_id, perf_year, perf_month, absent, late, task_sheet, performance_score, dressing_behaviour, rnd, total)
            VALUES 
            ('$empId', '$pYear', '$pMonth', '{$scores['absent']}', '{$scores['late']}', '{$scores['task_sheet']}', '{$scores['performance_score']}', '{$scores['dressing_behaviour']}', '{$scores['rnd']}', '$total')
            ON DUPLICATE KEY UPDATE 
            absent=VALUES(absent),
            late=VALUES(late),
            task_sheet=VALUES(task_sheet),
            performance_score=VALUES(performance_score),
            dressing_behaviour=VALUES(dressing_behaviour),
            rnd=VALUES(rnd),
            total=VALUES(total)";
        if (mysqli_query($con, $insert)) {
            $perfMessage = "Performance saved for " . htmlspecialchars($_POST['emp_name'] ?? 'employee');
        } else {
            $perfError = "Could not save performance.";
        }
    } else {
        $perfError = "Invalid performance data.";
    }
}

// Fetch performance map for selected month/year
$performanceMap = array();
$perfQuery = mysqli_query($con, "SELECT * FROM emp_performance WHERE perf_year='$currentYear' AND perf_month='$currentMonth'");
if ($perfQuery && mysqli_num_rows($perfQuery) > 0) {
    while ($p = mysqli_fetch_assoc($perfQuery)) {
        $performanceMap[(int)$p['emp_id']] = $p;
    }
}



// Build last 4 months list (including current) for history display
$historyMonths = array();
for ($i = 0; $i < 4; $i++) {
    $ts = strtotime("-$i month");
    $historyMonths[] = array(
        'year' => (int)date('Y', $ts),
        'month' => (int)date('n', $ts),
        'label' => date('M Y', $ts)
    );
}

// Fetch totals for last 4 months for all employees
$historyTotals = array();
if (count($historyMonths) > 0) {
    $conds = array();
    foreach ($historyMonths as $hm) {
        $conds[] = "(perf_year='{$hm['year']}' AND perf_month='{$hm['month']}')";
    }
    $histSql = "SELECT emp_id, perf_year, perf_month, total FROM emp_performance WHERE " . implode(' OR ', $conds);
    $histRes = mysqli_query($con, $histSql);
    if ($histRes && mysqli_num_rows($histRes) > 0) {
        while ($h = mysqli_fetch_assoc($histRes)) {
            $key = $h['perf_year'] . '-' . $h['perf_month'];
            $historyTotals[(int)$h['emp_id']][$key] = (int)$h['total'];
        }
    }
}

// Pre-compute absent-based default points (3 or fewer absences => 10 points, otherwise 0)
$absencePoints = array();
$attendanceTable = mysqli_query($con, "SHOW TABLES LIKE 'attendance'");
if ($attendanceTable && mysqli_num_rows($attendanceTable) > 0) {
    $absSql = "SELECT emp_id, SUM(CASE WHEN status='absent' THEN 1 ELSE 0 END) AS absences
               FROM attendance
               WHERE MONTH(attendance_date)='$currentMonth' AND YEAR(attendance_date)='$currentYear'
               GROUP BY emp_id";
    $absRes = mysqli_query($con, $absSql);
    if ($absRes && mysqli_num_rows($absRes) > 0) {
        while ($a = mysqli_fetch_assoc($absRes)) {
            $absences = (int)$a['absences'];
            $absencePoints[(int)$a['emp_id']] = ($absences <= 3) ? 10 : 0;
        }
    }
}

// Pre-compute late-based default points using check-in time after 10:15 AM
// Rules: up to 3 late => 10 points, 4-6 late => 5 points, more than 6 late => 0
$latePoints = array();
if ($attendanceTable && mysqli_num_rows($attendanceTable) > 0) {
    $lateSql = "SELECT emp_id, SUM(
                    CASE 
                        WHEN status='present' AND check_in_time IS NOT NULL AND check_in_time > '10:15:00' THEN 1
                        WHEN status='late' THEN 1
                        WHEN remarks LIKE '%late%' THEN 1
                        ELSE 0
                    END
                ) AS lates
                FROM attendance
                WHERE MONTH(attendance_date)='$currentMonth' AND YEAR(attendance_date)='$currentYear'
                GROUP BY emp_id";
    $lateRes = mysqli_query($con, $lateSql);
    if ($lateRes && mysqli_num_rows($lateRes) > 0) {
        while ($l = mysqli_fetch_assoc($lateRes)) {
            $lateCount = (int)$l['lates'];
            $points = 10;
            if ($lateCount >= 4 && $lateCount <= 6) $points = 5;
            if ($lateCount > 6) $points = 0;
            $latePoints[(int)$l['emp_id']] = $points;
        }
    }
}

// Fetch average daily performance from attendance table and convert to 35-point scale
$avgDailyPerformance = array();
if ($attendanceTable && mysqli_num_rows($attendanceTable) > 0) {
    $avgPerfSql = "SELECT emp_id, AVG(performance) as avg_perf, COUNT(performance) as perf_count 
                   FROM attendance 
                   WHERE MONTH(attendance_date)='$currentMonth' AND YEAR(attendance_date)='$currentYear' 
                   AND performance IS NOT NULL 
                   GROUP BY emp_id";
    $avgPerfRes = mysqli_query($con, $avgPerfSql);
    if ($avgPerfRes && mysqli_num_rows($avgPerfRes) > 0) {
        while ($ap = mysqli_fetch_assoc($avgPerfRes)) {
            $empId = (int)$ap['emp_id'];
            $avgVal = (float)$ap['avg_perf'];
            // Convert from 0-100 scale to 0-35 scale
            $convertedScore = round(($avgVal / 100) * 35, 2);
            $avgDailyPerformance[$empId] = array(
                'average' => $avgVal,
                'converted' => $convertedScore,
                'count' => (int)$ap['perf_count']
            );
        }
    }
}

function monthName($m)
{
    return date('F', mktime(0, 0, 0, $m, 10));
}

// Month/year options for the performance modal
$monthLabels = array();
for ($m = 1; $m <= 12; $m++) {
    $monthLabels[$m] = monthName($m);
}
$yearOptions = array();
for ($y = $currentYear - 2; $y <= $currentYear + 1; $y++) {
    $yearOptions[] = $y;
}
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Employee Directory
            <button class="btn btn-primary" data-toggle="modal" data-target="#addEmployeeModal">
                <i class="fa fa-user-plus"></i> Add new Employee
            </button>
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <i class="fa fa-users"></i> Employees
            </li>
        </ol>
    </div>
    <?php if ($perfMessage): ?>
        <div class="col-lg-12">
            <div class="alert alert-success"><?php echo $perfMessage; ?></div>
        </div>
    <?php endif; ?>
    <?php if ($perfError): ?>
        <div class="col-lg-12">
            <div class="alert alert-danger"><?php echo $perfError; ?></div>
        </div>
    <?php endif; ?>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title" style="margin: 0;">
                    <i class="fa fa-users"></i> All Employees
                </h4>
            </div>

            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Blood Group</th>
                                <th>Gender</th>
                                <th>Join Date</th>
                                <th>Salary</th>
                                <th>Performance<br><small><?php echo monthName($currentMonth) . ' ' . $currentYear; ?></small></th>
                                <th>Documents</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM emp_list ORDER BY id ASC";
                            $res = mysqli_query($con, $sql);
                            if ($res && mysqli_num_rows($res) > 0) {
                                while ($row = mysqli_fetch_assoc($res)) {
                                    $pk = $row['id'];
                                    $name = htmlspecialchars($row['name']);
                                    $phone = htmlspecialchars($row['phone_number']);
                                    $address = htmlspecialchars($row['address']);
                                    $email = htmlspecialchars($row['email']);
                                    $blood = htmlspecialchars($row['blood_group']);
                                    $gender = htmlspecialchars($row['gender']);
                                    $join = htmlspecialchars($row['join_date']);
                                    $salary = htmlspecialchars($row['salary']);
                                    $perfRow = isset($performanceMap[$pk]) ? $performanceMap[$pk] : null;
                                    $perfTotal = $perfRow ? (int)$perfRow['total'] : null;
                                    $absentPrefill = $perfRow ? (int)$perfRow['absent'] : (isset($absencePoints[$pk]) ? $absencePoints[$pk] : 0);
                                    $latePrefill = $perfRow ? (int)$perfRow['late'] : (isset($latePoints[$pk]) ? $latePoints[$pk] : 0);
                                    // Build history payload for last 4 months
                                    $histSeries = array();
                                    foreach ($historyMonths as $hm) {
                                        $k = $hm['year'] . '-' . $hm['month'];
                                        $val = isset($historyTotals[$pk][$k]) ? $historyTotals[$pk][$k] : 0;
                                        $histSeries[] = array(
                                            'label' => $hm['label'],
                                            'value' => $val
                                        );
                                    }
                                    $histJson = htmlspecialchars(json_encode($histSeries), ENT_QUOTES, 'UTF-8');
                                    $breakdown = array(
                                        array('label' => 'Absent (auto)', 'max' => 20, 'user' => $absentPrefill),
                                        array('label' => 'Late (auto)', 'max' => 10, 'user' => $latePrefill),
                                        array('label' => 'Task Sheet', 'max' => 10, 'user' => $perfRow ? (int)$perfRow['task_sheet'] : 0),
                                        array('label' => 'Performance', 'max' => 35, 'user' => $perfRow ? (int)$perfRow['performance_score'] : 0),
                                        array('label' => 'Dressing & Behaviour', 'max' => 10, 'user' => $perfRow ? (int)$perfRow['dressing_behaviour'] : 0),
                                        array('label' => 'RND', 'max' => 15, 'user' => $perfRow ? (int)$perfRow['rnd'] : 0)
                                    );
                                    $calculatedTotal = 0;
                                    foreach ($breakdown as $b) {
                                        $calculatedTotal += isset($b['user']) ? (int)$b['user'] : 0;
                                    }
                                    $effectiveTotal = ($perfTotal !== null) ? $perfTotal : $calculatedTotal;
                                    $breakdown[] = array('label' => 'Total', 'max' => 100, 'user' => $effectiveTotal);
                                    $breakdownJson = htmlspecialchars(json_encode($breakdown), ENT_QUOTES, 'UTF-8');
                                    if ($join) {
                                        $ts = strtotime($join);
                                        if ($ts !== false) $join = date('d-m-y', $ts);
                                    }
                            ?>
                                    <tr>
                                        <td><?php echo $pk; ?></td>
                                        <td><?php echo $name; ?></td>
                                        <td><?php echo $phone; ?></td>
                                        <td><?php echo $email; ?></td>
                                        <td><?php echo $address; ?></td>
                                        <td><?php echo $blood; ?></td>
                                        <td><?php echo $gender; ?></td>
                                        <td><?php echo $join; ?></td>
                                        <td><?php echo $salary; ?></td>
                                        <td>
                                            <?php
                                            $scoreClass = 'score-plain';
                                            if ($perfRow) {
                                                if ($perfTotal < 30) {
                                                    $scoreClass = 'score-red';
                                                } elseif ($perfTotal <= 49) {
                                                    $scoreClass = 'score-gray';
                                                } elseif ($perfTotal <= 69) {
                                                    $scoreClass = 'score-amber';
                                                } else {
                                                    $scoreClass = 'score-green';
                                                }
                                            }
                                            ?>
                                            <button
                                                type="button"
                                                class="btn btn-xs score-btn <?php echo $scoreClass; ?> <?php echo $perfRow ? '' : 'btn-default'; ?>"
                                                style="padding: 6px 10px; margin-bottom: 6px; border-width: 1px;"
                                                data-history="<?php echo $histJson; ?>"
                                                data-breakdown="<?php echo $breakdownJson; ?>"
                                                data-total="<?php echo $effectiveTotal; ?>"
                                                data-empname="<?php echo $name; ?>"
                                                onclick="openPerfHistory(this)">
                                                <?php echo $perfRow ? ($perfTotal . ' / 100') : 'Not set'; ?>
                                            </button><br>
                                            <button
                                                class="btn btn-xs btn-warning"
                                                style="padding: 6px 8px;"
                                                data-emp="<?php echo $pk; ?>"
                                                data-name="<?php echo $name; ?>"
                                                data-absent="<?php echo $absentPrefill; ?>"
                                                data-late="<?php echo $latePrefill; ?>"
                                                data-task_sheet="<?php echo $perfRow ? (int)$perfRow['task_sheet'] : 0; ?>"
                                                data-performance_score="<?php echo $perfRow ? (float)$perfRow['performance_score'] : 0; ?>"
                                                data-dressing_behaviour="<?php echo $perfRow ? (int)$perfRow['dressing_behaviour'] : 0; ?>"
                                                data-rnd="<?php echo $perfRow ? (int)$perfRow['rnd'] : 0; ?>"
                                                data-avg_perf="<?php echo isset($avgDailyPerformance[$pk]) ? $avgDailyPerformance[$pk]['converted'] : 0; ?>"
                                                onclick="openPerformance(this)">
                                                <i class="fa fa-line-chart"></i> Set
                                            </button>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" onclick="openDocuments(<?php echo $pk; ?>)" class="btn btn-xs btn-default" style="padding: 7px 8px;" title="View Documents">
                                                <i class="fa fa-file"></i> View
                                            </a>
                                        </td>
                                        <td>
                                            <button
                                                class="btn btn-xs btn-info"
                                                style="padding:6px 8px; display:block; margin-bottom:4px;"
                                                data-toggle="modal"
                                                data-target="#editEmployeeModal"

                                                data-id="<?php echo $pk; ?>"
                                                data-name="<?php echo $name; ?>"
                                                data-phone="<?php echo $phone; ?>"
                                                data-email="<?php echo $email; ?>"
                                                data-address="<?php echo $address; ?>"
                                                data-join="<?php echo $row['join_date']; ?>"
                                                data-basic="<?php echo $row['basic_salary']; ?>"
                                                data-hra="<?php echo $row['hra']; ?>"
                                                data-allowance="<?php echo $row['allowance']; ?>"
                                                data-deductions="<?php echo $row['deductions']; ?>"
                                                data-salary="<?php echo $row['salary']; ?>"

                                                onclick="openEditEmployee(this)">

                                                <i class="fa fa-edit"></i> Edit
                                            </button>
                                            <a href="javascript:void(0)"
                                                onclick="deleteEmployee(<?php echo $pk; ?>)"
                                                class="btn btn-xs btn-danger"
                                                style="padding:6px 4px; display:block;"
                                                title="Delete">
                                                <i class="fa fa-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="12" style="text-align: center; padding: 20px; color: #999;">
                                        <i class="fa fa-inbox"></i> No employees found.
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Popup Modal for Documents -->
<div class="popup-overlay" id="popup" aria-hidden="true">
    <div class="popup-content" role="dialog" aria-modal="true" aria-labelledby="popup-title">
        <div class="popup-header">
            <!-- <button class="popup-back" onclick="backPopup()" title="Back">
                <i class="fa fa-arrow-left"></i> Back
            </button> -->
            <h3 id="popup-title"><i class="fa fa-file"></i> Employee Documents</h3>
            <button class="popup-close" onclick="closePopup()" aria-label="Close">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <div id="popup-docs" class="doc-list"></div>

        <div class="upload-box">
            <h5>Upload New Documents</h5>
            <form id="uploadForm" enctype="multipart/form-data">
                <input type="hidden" name="emp_id" id="emp_id">
                <div class="form-group">
                    <input type="file" name="documents[]" class="form-control" multiple required>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa fa-upload"></i> Upload
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Performance Modal -->
<div class="modal fade" id="performanceModal" tabindex="-1" role="dialog" aria-labelledby="performanceModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" id="performanceForm">
                <div class="modal-header" style="display: flex; align-items: center; justify-content: space-between;">
                    <h4 class="modal-title" id="performanceModalLabel" style="margin: 0;">
                        <i class="fa fa-line-chart"></i> Monthly Performance
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="border: none; background: transparent; font-size: 22px;">&times;</button>
                </div>
                <div class="modal-body">
                    <p style="margin-bottom: 12px; color: #555;">Set the monthly score for <strong id="perfEmpName"></strong> (<span id="perfMonthYearLabel"><?php echo monthName($currentMonth) . ' ' . $currentYear; ?></span>). Max total 100.</p>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Month</label>
                                <select name="perf_month" id="perf_month" class="form-control">
                                    <?php foreach ($monthLabels as $mVal => $mLabel): ?>
                                        <option value="<?php echo $mVal; ?>" <?php echo ($mVal === $currentMonth) ? 'selected' : ''; ?>>
                                            <?php echo $mLabel; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Year</label>
                                <select name="perf_year" id="perf_year" class="form-control">
                                    <?php foreach ($yearOptions as $yr): ?>
                                        <option value="<?php echo $yr; ?>" <?php echo ($yr === $currentYear) ? 'selected' : ''; ?>>
                                            <?php echo $yr; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Absent (auto)</label>
                                <input type="number" name="absent" id="perf_absent" class="form-control" min="0" max="20" value="0" readonly>
                                <small style="color:#64748b;">Auto from monthly absences</small>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Late (auto)</label>
                                <input type="number" name="late" id="perf_late" class="form-control" min="0" max="10" value="0" readonly>
                                <small style="color:#64748b;">Auto from late check-ins</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Task Sheet (Max 10)</label>
                                <input type="number" name="task_sheet" id="perf_task" class="form-control" min="0" max="10" value="0" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Performance (Max 35)</label>
                                <input type="number" name="performance_score" id="perf_core" class="form-control" min="0" max="35" value="0" readonly>
                                <small style="color:#64748b;">Auto from daily average performance</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Dressing & Behaviour (Max 10)</label>
                                <input type="number" name="dressing_behaviour" id="perf_dress" class="form-control" min="0" max="10" value="0" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>RND (Max 15)</label>
                                <input type="number" name="rnd" id="perf_rnd" class="form-control" min="0" max="15" value="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="well well-sm" id="perfTotalBox" style="margin-bottom: 0;">
                        <strong>Total:</strong> <span id="perfTotalValue">0</span> / 100
                    </div>
                    <input type="hidden" name="emp_id" id="perf_emp_id" value="">
                    <input type="hidden" name="emp_name" id="perf_emp_name_field" value="">
                    <input type="hidden" id="perf_avg_performance" value="0">
                    <input type="hidden" name="save_performance" value="1">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Performance</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Performance History Modal -->
<div class="modal fade" id="performanceHistoryModal" tabindex="-1" role="dialog" aria-labelledby="performanceHistoryLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="display: flex; align-items: center; justify-content: space-between;">
                <h4 class="modal-title" id="performanceHistoryLabel" style="margin: 0;">
                    <i class="fa fa-area-chart"></i> Performance History
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="border: none; background: transparent; font-size: 22px;">&times;</button>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 12px; color: #555;">Last 4 months performance for <strong id="historyEmpName"></strong>.</p>
                <div class="history-chart-box">
                    <div id="historyChart" style="width:100%; height:200px; margin-bottom: 10px;"></div>
                </div>
                <div class="history-table-wrap">
                    <div class="history-table-title">
                        <span><i class="fa fa-list-ul"></i> Set Points</span>
                        <span id="historyBreakdownTotal" class="history-total-pill">0 / 100</span>
                    </div>
                    <div class="table-responsive" style="margin-top: 10px;">
                        <table class="table table-condensed history-breakdown-table">
                            <thead>
                                <tr>
                                    <th style="width: 45%;">Type</th>
                                    <th style="width: 25%;">Max Points</th>
                                    <th style="width: 30%;">User Points</th>
                                </tr>
                            </thead>
                            <tbody id="historyBreakdown"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
    // Popup controls
    const popup = document.getElementById('popup');
    const popupDocs = document.getElementById('popup-docs');
    const empIdField = document.getElementById('emp_id');
    const perfMonthSelect = document.getElementById('perf_month');
    const perfYearSelect = document.getElementById('perf_year');
    const monthLabelMap = <?php echo json_encode($monthLabels); ?>;
    const defaultPerfMonth = <?php echo (int)$currentMonth; ?>;
    const defaultPerfYear = <?php echo (int)$currentYear; ?>;

    function openDocuments(empId) {
        empIdField.value = empId;
        popup.style.display = 'flex';

        fetch('fetch_documents.php?id=' + empId)
            .then(response => response.text())
            .then(data => {
                popupDocs.innerHTML = data || '<p style="text-align: center; color: #999;">No documents found.</p>';
            });
    }

    function closePopup() {
        popup.style.display = 'none';
        popupDocs.innerHTML = '';
    }

    function backPopup() {
        // Back in this context will simply close the popup and return focus to the table
        closePopup();
        // return focus to the document view (keeps UX smooth)
        const el = document.querySelector('.table-responsive');
        if (el) el.querySelector('table')?.focus?.();
    }

    // Close popup with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (popup.style.display === 'flex') closePopup();
        }
    });

    // Handle document deletion
    function deleteDocument(docId, empId) {
        if (confirm('Are you sure you want to delete this document?')) {
            fetch('delete_document.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'doc_id=' + docId
                })
                .then(res => res.text())
                .then(result => {
                    if (result.trim() === 'success') {
                        openDocuments(empId);
                    } else {
                        alert('Error deleting file.');
                    }
                });
        }
    }

    // Handle file uploads
    document.getElementById('uploadForm').addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(e.target);

        fetch('upload_ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(result => {
                if (result.trim() === 'success') {
                    alert('Documents uploaded successfully');
                    openDocuments(empIdField.value);
                } else {
                    alert('Upload failed. Please try again.');
                }
            });
    });

    // Delete employee
    function deleteEmployee(id) {
        if (confirm('Are you sure you want to delete this employee? This action cannot be undone.')) {
            fetch('delete_emp.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'id=' + id
                })
                .then(res => res.text())
                .then(result => {
                    if (result.trim() === 'success') {
                        alert('Employee deleted successfully');
                        location.reload();
                    } else {
                        alert('Error deleting employee');
                    }
                });
        }
    }

    // Performance modal handlers
    function updateMonthYearLabel() {
        const labelEl = document.getElementById('perfMonthYearLabel');
        if (!labelEl) return;
        const m = parseInt(perfMonthSelect?.value || '0', 10);
        const y = perfYearSelect?.value || '';
        labelEl.textContent = (monthLabelMap[m] || 'Month') + ' ' + y;
    }

    function applyPerformanceData(data) {
        const safe = (val) => {
            const n = parseInt(val, 10);
            return Number.isFinite(n) ? n : 0;
        };
        const safeFloat = (val) => {
            const n = parseFloat(val);
            return Number.isFinite(n) ? n : 0;
        };
        document.getElementById('perf_absent').value = safe(data.absent);
        document.getElementById('perf_late').value = safe(data.late);
        document.getElementById('perf_task').value = safe(data.task_sheet);
        document.getElementById('perf_core').value = safeFloat(data.performance_score);
        document.getElementById('perf_dress').value = safe(data.dressing_behaviour);
        document.getElementById('perf_rnd').value = safe(data.rnd);
        const total = safeFloat(data.total);
        document.getElementById('perfTotalValue').textContent = total.toFixed(2);
        updatePerformanceTotal();
    }

    function loadPerformanceForMonth(empId) {
        if (!empId || !perfMonthSelect || !perfYearSelect) return;
        const month = parseInt(perfMonthSelect.value, 10);
        const year = parseInt(perfYearSelect.value, 10);
        updateMonthYearLabel();
        fetch(`fetch_performance.php?emp_id=${empId}&month=${month}&year=${year}`)
            .then(res => res.ok ? res.json() : null)
            .then(json => {
                if (json && json.success && json.data) {
                    applyPerformanceData(json.data);
                } else {
                    updatePerformanceTotal();
                }
            })
            .catch(() => updatePerformanceTotal());
    }

    function onMonthYearChange() {
        const empId = document.getElementById('perf_emp_id')?.value || '';
        if (empId) {
            loadPerformanceForMonth(empId);
        }
    }

    if (perfMonthSelect) perfMonthSelect.addEventListener('change', onMonthYearChange);
    if (perfYearSelect) perfYearSelect.addEventListener('change', onMonthYearChange);

    function openPerformance(btn) {
        const data = btn.dataset;
        document.getElementById('perf_emp_id').value = data.emp;
        document.getElementById('perf_emp_name_field').value = data.name;
        document.getElementById('perfEmpName').textContent = data.name;
        document.getElementById('perf_avg_performance').value = data.avg_perf || 0;
        if (perfMonthSelect) perfMonthSelect.value = defaultPerfMonth;
        if (perfYearSelect) perfYearSelect.value = defaultPerfYear;
        updateMonthYearLabel();

        // Always use average performance (readonly field)
        const performanceScore = parseFloat(data.avg_perf || '0');

        applyPerformanceData({
            absent: data.absent || 0,
            late: data.late || 0,
            task_sheet: data.task_sheet || 0,
            performance_score: performanceScore,
            dressing_behaviour: data.dressing_behaviour || 0,
            rnd: data.rnd || 0,
            total: ['absent', 'late', 'task_sheet', 'dressing_behaviour', 'rnd']
                .map(k => parseInt(data[k] || '0', 10))
                .reduce((a, b) => a + (Number.isFinite(b) ? b : 0), 0) + performanceScore
        });
        loadPerformanceForMonth(data.emp);
        $('#performanceModal').modal('show');
    }

    function autoLoadAvgPerformance() {
        const avgPerf = parseFloat(document.getElementById('perf_avg_performance').value || '0') || 0;
        if (avgPerf > 0) {
            document.getElementById('perf_core').value = avgPerf;
            updatePerformanceTotal();
        } else {
            alert('No average daily performance data available for this month.');
        }
    }

    function updatePerformanceTotal() {
        const absent = parseInt(document.getElementById('perf_absent').value || '0', 10);
        const late = parseInt(document.getElementById('perf_late').value || '0', 10);
        const task = parseInt(document.getElementById('perf_task').value || '0', 10);
        const perf = parseFloat(document.getElementById('perf_core').value || '0');
        const dress = parseInt(document.getElementById('perf_dress').value || '0', 10);
        const rnd = parseInt(document.getElementById('perf_rnd').value || '0', 10);

        const total = absent + late + task + perf + dress + rnd;
        const totalBox = document.getElementById('perfTotalBox');
        document.getElementById('perfTotalValue').textContent = total.toFixed(2);
        if (total > 100) {
            totalBox.classList.add('alert', 'alert-danger');
        } else {
            totalBox.classList.remove('alert', 'alert-danger');
        }
    }

    ['perf_absent', 'perf_late', 'perf_task', 'perf_core', 'perf_dress', 'perf_rnd'].forEach(id => {
        const el = document.getElementById(id);
        el.addEventListener('input', updatePerformanceTotal);
    });

    document.getElementById('performanceForm').addEventListener('submit', function(e) {
        const totalText = document.getElementById('perfTotalValue').textContent;
        const total = parseFloat(totalText);
        if (total > 100) {
            e.preventDefault();
            alert('Total cannot exceed 100.');
        }
    });

    // Performance history (last 4 months) modal
    function scoreColor(val) {
        const v = parseInt(val, 10);
        if (v < 30) return '#6b7280'; // gray
        if (v < 50) return '#ef4444'; // red
        if (v < 70) return '#f59e0b'; // amber
        return '#22c55e'; // green
    }

    function openPerfHistory(btn) {
        const series = JSON.parse(btn.dataset.history || '[]');
        const empName = btn.dataset.empname || 'Employee';
        const breakdown = JSON.parse(btn.dataset.breakdown || '[]');
        const totalScore = parseInt(btn.dataset.total || '0', 10);
        document.getElementById('historyEmpName').textContent = empName;

        // Build SVG line + points
        const chartEl = document.getElementById('historyChart');
        const width = chartEl.clientWidth || 520;
        const height = 210;
        const pad = 22;
        const barArea = width - pad * 2;
        const slot = series.length ? (barArea / series.length) : 0;
        const gap = 10;
        const barWidth = slot ? Math.max(10, Math.min(18, slot * 0.55)) : 0;
        let bars = '';
        let labels = '';
        let grid = '';

        // horizontal grid lines every 25
        for (let i = 0; i <= 4; i++) {
            const val = i * 25;
            const y = height - pad - ((height - pad * 2) * val) / 100;
            grid += `<line x1="${pad}" y1="${y}" x2="${width - pad}" y2="${y}" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="3 3"></line>`;
            grid += `<text x="${pad - 6}" y="${y + 4}" fill="#94a3b8" font-size="10" text-anchor="end">${val}</text>`;
        }

        series.forEach((s, idx) => {
            const capped = Math.min(100, Math.max(0, s.value));
            const h = ((height - pad * 2) * capped) / 100;
            const center = pad + idx * slot + slot / 2;
            const x = center - barWidth / 2;
            const y = height - pad - h;
            const c = scoreColor(capped);
            const barW = Math.max(10, Math.min(barWidth, 18));
            bars += `<rect x="${x}" y="${y}" width="${barW}" height="${h}" rx="7" fill="${c}" opacity="0.92" stroke="rgba(15,23,42,0.4)" stroke-width="0.5"></rect>`;
            labels += `<text x="${center}" y="${y - 8}" fill="#0f172a" font-size="11" text-anchor="middle">${capped}</text>`;
            labels += `<text x="${center}" y="${height - pad + 16}" fill="#475569" font-size="11" text-anchor="middle">${s.label}</text>`;
        });

        chartEl.innerHTML = `
            <svg width="${width}" height="${height}" viewBox="0 0 ${width} ${height}">
                <defs>
                    <linearGradient id="histGrad" x1="0" x2="0" y1="0" y2="1">
                        <stop offset="0%" stop-color="#f8fafc"></stop>
                        <stop offset="100%" stop-color="#eef2ff"></stop>
                    </linearGradient>
                    <filter id="histShadow" x="-10%" y="-10%" width="120%" height="120%">
                        <feDropShadow dx="0" dy="6" stdDeviation="6" flood-color="rgba(15,23,42,0.12)" />
                    </filter>
                </defs>
                <rect x="${pad - 6}" y="${pad - 6}" width="${width - (pad - 6) * 2}" height="${height - (pad - 6) * 2}" rx="12" fill="url(#histGrad)" stroke="#e2e8f0" stroke-width="1.1" filter="url(#histShadow)"></rect>
                ${grid}
                <line x1="${pad}" y1="${height - pad}" x2="${width - pad}" y2="${height - pad}" stroke="#cbd5e1" stroke-width="1.3"></line>
                <line x1="${pad}" y1="${pad}" x2="${pad}" y2="${height - pad}" stroke="#cbd5e1" stroke-width="1.3"></line>
                ${bars}
                ${labels}
            </svg>
        `;

        // Set point breakdown table
        const breakdownBody = document.getElementById('historyBreakdown');
        const breakdownTotal = document.getElementById('historyBreakdownTotal');
        if (breakdownBody) {
            const detailRows = breakdown.filter(item => (item.label || '').toLowerCase() !== 'total');
            const totalEntry = breakdown.find(item => (item.label || '').toLowerCase() === 'total');
            const displayedTotal = totalEntry ? (parseInt(totalEntry.user, 10) || 0) : totalScore || 0;
            if (detailRows.length === 0) {
                breakdownBody.innerHTML = `<tr><td colspan="3" class="history-empty-row">No set points yet for this month.</td></tr>`;
            } else {
                breakdownBody.innerHTML = detailRows.map(item => {
                    const userPts = parseInt(item.user, 10) || 0;
                    const badgeColor = scoreColor(userPts);
                    return `
                        <tr>
                            <td>${item.label}</td>
                            <td>${item.max}</td>
                            <td><span class="history-point-badge" style="background:${badgeColor}1a; color:${badgeColor}; border:1px solid ${badgeColor}33;">${userPts}</span></td>
                        </tr>
                    `;
                }).join('');
            }
            if (breakdownTotal) {
                breakdownTotal.textContent = `${displayedTotal} / 100`;
            }
        }

        $('#performanceHistoryModal').modal('show');
    }
</script>

<style>
    /* Popup Modal Styling */
    .popup-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(180deg, rgba(2, 6, 23, 0.45), rgba(2, 6, 23, 0.6));
        backdrop-filter: blur(4px) saturate(120%);
        justify-content: center;
        align-items: center;
        z-index: 1200;
        transition: opacity .18s ease;
    }

    .popup-content {
        background: #ffffff;
        padding: 18px 20px 22px 20px;
        width: 92%;
        max-width: 640px;
        max-height: 82vh;
        overflow-y: auto;
        border-radius: 8px;
        box-shadow: 0 12px 36px rgba(2, 6, 23, 0.32);
        border: 1px solid rgba(15, 23, 42, 0.06);
    }

    .popup-content h3 {
        margin: 0;
        color: #102a43;
        font-weight: 700;
        text-align: center;
        padding: 8px 0 12px 0;
    }

    .doc-list {
        margin-top: 15px;
        margin-bottom: 15px;
    }

    .doc-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fbfcfd;
        border-radius: 6px;
        padding: 10px 14px;
        margin-bottom: 10px;
        border: 1px solid rgba(15, 23, 42, 0.04);
    }

    .doc-item a {
        color: #050505ff;
        text-decoration: none;
        font-weight: 600;
    }

    .doc-item a:hover {
        text-decoration: underline;
    }

    .delete-doc {
        background: #d9534f;
        color: #fff;
        border: none;
        border-radius: 3px;
        padding: 4px 8px;
        font-size: 12px;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .delete-doc:hover {
        background: #c9302c;
    }

    /* Header controls */
    .popup-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(15, 23, 42, 0.04);
        margin-bottom: 14px;
    }

    .popup-header h3 {
        flex: 1;
        text-align: center;
        margin: 0;
        font-size: 18px
    }

    .popup-back,
    .popup-close {
        background: transparent;
        border: none;
        color: #334155;
        font-size: 14px;
        cursor: pointer;
        padding: 6px 10px;
        border-radius: 6px
    }

    .popup-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #0f172a;
        background: linear-gradient(90deg, #f8fafc, #eef2ff);
        box-shadow: inset 0 -1px 0 rgba(255, 255, 255, 0.4);
    }

    .popup-back i {
        font-size: 13px
    }

    .popup-close {
        color: #64748b
    }

    .upload-box {
        margin-top: 0;
        background: #fff;
        border: 1px dashed rgba(15, 23, 42, 0.06);
        padding: 14px;
        border-radius: 6px
    }

    /* Make popup content scroll nicely on small screens */
    @media (max-width:600px) {
        .popup-content {
            max-width: 94%;
            padding: 14px
        }

        .popup-header h3 {
            font-size: 16px
        }
    }

    /* Performance history modal */
    #performanceHistoryModal .modal-content {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 16px 44px rgba(15, 23, 42, 0.16);
    }

    #performanceHistoryModal .modal-header {
        border-bottom: 1px solid #e2e8f0;
    }

    .history-chart-box {
        background: linear-gradient(180deg, #f8fafc, #eef2ff);
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
    }

    .history-table-wrap {
        margin-top: 14px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px;
        background: #ffffff;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
    }

    .history-table-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        font-weight: 600;
        color: #0f172a;
    }

    .history-total-pill {
        display: inline-block;
        background: #0ea5e9;
        color: #fff;
        padding: 4px 10px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 12px;
        letter-spacing: 0.01em;
    }

    .history-breakdown-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        border-bottom: 1px solid #e2e8f0;
    }

    .history-breakdown-table tbody td {
        vertical-align: middle;
        color: #0f172a;
    }

    .history-point-badge {
        display: inline-block;
        padding: 4px 9px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 12px;
        background: #e2e8f0;
        color: #0f172a;
    }

    .history-empty-row {
        text-align: center;
        color: #94a3b8;
    }

    .score-btn {
        border-color: transparent;
    }

    .score-plain {
        background: #f8fafc;
        color: #0f172a;
        border-color: #e2e8f0;
    }

    .score-red {
        background: #ef4444;
        color: #fff;
        border-color: #dc2626;
    }

    .score-gray {
        background: #94a3b8;
        color: #0f172a;
        border-color: #94a3b8;
    }

    .score-amber {
        background: #f59e0b;
        color: #0f172a;
        border-color: #d97706;
    }

    .score-green {
        background: #22c55e;
        color: #fff;
        border-color: #16a34a;
    }
</style>


<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
?>

<div class="modal fade" id="addEmployeeModal">
    <div class="modal-dialog modal-lg" style="width: 650px;">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-user-plus"></i> Add New Employee</h4>
            </div>

            <div class="modal-body">

                <form method="POST" class="form-horizontal">

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Employee Name *</label>
                        <div class="col-sm-9">
                            <input type="text" name="name" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Phone *</label>
                        <div class="col-sm-9">
                            <input type="tel" name="number" class="form-control" maxlength="10" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Email *</label>
                        <div class="col-sm-9">
                            <input type="email" name="email" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Address *</label>
                        <div class="col-sm-9">
                            <input type="text" name="address" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Blood Group *</label>
                        <div class="col-sm-9">
                            <select name="blood" class="form-control" required>
                                <option value="">Select</option>
                                <option>A+</option>
                                <option>B+</option>
                                <option>O+</option>
                                <option>AB+</option>
                                <option>A-</option>
                                <option>B-</option>
                                <option>O-</option>
                                <option>AB-</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Gender *</label>
                        <div class="col-sm-9">
                            <select name="gender" class="form-control" required>
                                <option value="">Select</option>
                                <option>Male</option>
                                <option>Female</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Join Date *</label>
                        <div class="col-sm-9">
                            <input type="date" name="joinDate" class="form-control" max="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>

                    <hr>
                    <h4>Salary Structure</h4>
                    <hr>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Basic Pay *</label>
                        <div class="col-sm-9">
                            <input type="number" id="basic_salary" name="basic_salary" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">HRA</label>
                        <div class="col-sm-9">
                            <input type="number" id="hra" name="hra" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Allowance</label>
                        <div class="col-sm-9">
                            <input type="number" id="allowance" name="allowance" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Deductions</label>
                        <div class="col-sm-9">
                            <input type="number" id="deductions" name="deductions" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Total Salary</label>
                        <div class="col-sm-9">
                            <input type="text" id="salary" name="salary" class="form-control" readonly>
                        </div>
                    </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-default" data-dismiss="modal">
                    Cancel
                </button>

                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Add Employee
                </button>

            </div>

            </form>

        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        function calculateTotal() {

            let basic = parseFloat(document.getElementById('basic_salary')?.value) || 0;
            let hra = parseFloat(document.getElementById('hra')?.value) || 0;
            let allowance = parseFloat(document.getElementById('allowance')?.value) || 0;
            let deduction = parseFloat(document.getElementById('deductions')?.value) || 0;

            let total = basic + hra + allowance - deduction;

            document.getElementById('salary').value = total.toFixed(2);

        }

        ['basic_salary', 'hra', 'allowance', 'deductions'].forEach(function(id) {

            let el = document.getElementById(id);

            if (el) {
                el.addEventListener('input', calculateTotal);
            }

        });

    });
</script>
<?php

if (isset($_POST['submit'])) {

    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);

    $contact = preg_replace('/\D+/', '', $_POST['number']);

    if (strlen($contact) != 10) {
        echo "<script>alert('Phone must be 10 digits');</script>";
        exit();
    }

    $address = mysqli_real_escape_string($con, $_POST['address']);
    $blood = mysqli_real_escape_string($con, $_POST['blood']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $joinDate = mysqli_real_escape_string($con, $_POST['joinDate']);

    $basic = $_POST['basic_salary'] ?? 0;
    $hra = $_POST['hra'] ?? 0;
    $allowance = $_POST['allowance'] ?? 0;
    $deductions = $_POST['deductions'] ?? 0;
    $salary = $_POST['salary'] ?? 0;

    // Generate random password
    $plainPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

    // Save password directly (NO HASH)
    $query = "INSERT INTO emp_list
    (name,phone_number,address,email,blood_group,gender,join_date,basic_salary,hra,allowance,deductions,salary,password)
    VALUES
    ('$name','$contact','$address','$email','$blood','$gender','$joinDate','$basic','$hra','$allowance','$deductions','$salary','$plainPassword')";

    $run = mysqli_query($con, $query);

    if ($run) {

        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'madhavanpatel19@gmail.com';
            $mail->Password = 'yawi nqpw wbhp icrx';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('madhavanpatel19@gmail.com', '8DOTS');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Employee Login Password';

            $mail->Body = "
            <h3>Welcome to 8DOTS</h3>
            <p>Your login password is: <b>$plainPassword</b></p>
            <p>Please login from Employee Portal.</p>
            ";

            $mail->send();
        } catch (Exception $e) {
            // Mail error ignored
        }

        echo "<script>
        alert('Employee Added Successfully');
        window.location='index.php?emp_directory';
        </script>";
    } else {

        echo "<script>alert('Database Error');</script>";
    }
}
?>


<?php
if (isset($_POST['update'])) {

    $id = mysqli_real_escape_string($con, $_POST['id']);

    $name = mysqli_real_escape_string($con, $_POST['name']);
    $phone = mysqli_real_escape_string($con, $_POST['number']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $joinDate = mysqli_real_escape_string($con, $_POST['joinDate']);

    $basic = mysqli_real_escape_string($con, $_POST['basic_salary']);
    $hra = mysqli_real_escape_string($con, $_POST['hra']);
    $allowance = mysqli_real_escape_string($con, $_POST['allowance']);
    $deductions = mysqli_real_escape_string($con, $_POST['deductions']);
    $salary = mysqli_real_escape_string($con, $_POST['salary']);

    mysqli_query($con, "UPDATE emp_list SET

name='$name',
phone_number='$phone',
email='$email',
address='$address',
join_date='$joinDate',
basic_salary='$basic',
hra='$hra',
allowance='$allowance',
deductions='$deductions',
salary='$salary'

WHERE id='$id'");

    echo "<script>
alert('Employee Updated Successfully');
window.location='index.php?emp_directory';
</script>";
}
?>
<div class="modal fade" id="editEmployeeModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-edit"></i> Edit Employee</h4>
            </div>

            <div class="modal-body">

                <form method="POST" class="form-horizontal">

                    <input type="hidden" name="id" id="edit_id">

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Name</label>
                        <div class="col-sm-9">
                            <input type="text" name="name" id="edit_name" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Phone</label>
                        <div class="col-sm-9">
                            <input type="text" name="number" id="edit_phone" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Email</label>
                        <div class="col-sm-9">
                            <input type="email" name="email" id="edit_email" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Address</label>
                        <div class="col-sm-9">
                            <input type="text" name="address" id="edit_address" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Join Date</label>
                        <div class="col-sm-9">
                            <input type="date" name="joinDate" id="edit_join" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Basic Salary</label>
                        <div class="col-sm-9">
                            <input type="number" name="basic_salary" id="edit_basic" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">HRA</label>
                        <div class="col-sm-9">
                            <input type="number" name="hra" id="edit_hra" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Allowance</label>
                        <div class="col-sm-9">
                            <input type="number" name="allowance" id="edit_allowance" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Deductions</label>
                        <div class="col-sm-9">
                            <input type="number" name="deductions" id="edit_deductions" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Total Salary</label>
                        <div class="col-sm-9">
                            <input type="text" name="salary" id="edit_salary" class="form-control" readonly>
                        </div>
                    </div>

            </div>

            <div class="modal-footer">
                <button type="submit" name="update" class="btn btn-primary">
                    <i class="fa fa-save"></i> Update
                </button>

                <button type="button" class="btn btn-default" data-dismiss="modal">
                    Cancel
                </button>
            </div>

            </form>

        </div>
    </div>
</div>
<script>
    function openEditEmployee(btn) {

        document.getElementById('edit_id').value = btn.dataset.id;
        document.getElementById('edit_name').value = btn.dataset.name;
        document.getElementById('edit_phone').value = btn.dataset.phone;
        document.getElementById('edit_email').value = btn.dataset.email;
        document.getElementById('edit_address').value = btn.dataset.address;
        document.getElementById('edit_join').value = btn.dataset.join;

        document.getElementById('edit_basic').value = btn.dataset.basic;
        document.getElementById('edit_hra').value = btn.dataset.hra;
        document.getElementById('edit_allowance').value = btn.dataset.allowance;
        document.getElementById('edit_deductions').value = btn.dataset.deductions;
        document.getElementById('edit_salary').value = btn.dataset.salary;

    }

    function calculateEditSalary() {

        let basic = parseFloat(document.getElementById('edit_basic').value) || 0;
        let hra = parseFloat(document.getElementById('edit_hra').value) || 0;
        let allowance = parseFloat(document.getElementById('edit_allowance').value) || 0;
        let deduction = parseFloat(document.getElementById('edit_deductions').value) || 0;

        document.getElementById('edit_salary').value = (basic + hra + allowance - deduction).toFixed(2);

    }

    ['edit_basic', 'edit_hra', 'edit_allowance', 'edit_deductions'].forEach(function(id) {

        let el = document.getElementById(id);

        if (el) {
            el.addEventListener('input', calculateEditSalary);
        }

    });
</script>