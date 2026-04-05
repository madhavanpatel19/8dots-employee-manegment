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
// File Upload Function (Auto Remove PDF Password)
function handleFileUpload($fileArray, $targetDir = "uploads/") {

    if (isset($fileArray) && $fileArray['error'] == 0) {

        $file_name = $fileArray['name'];
        $tmp_name = $fileArray['tmp_name'];

        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $new_name = time() . '_' . rand(1000, 9999) . '.' . $ext;

        $target_path = $targetDir . $new_name;

        if (move_uploaded_file($tmp_name, $target_path)) {

            // Only for PDF
            if ($ext === "pdf") {

                $qpdf = "C:/Program Files/qpdf/qpdf 12.3.2/bin/qpdf.exe";

                $unlocked_file = $targetDir . "unlock_" . $new_name;

                $command = "\"$qpdf\" --decrypt \"$target_path\" \"$unlocked_file\" 2>&1";

                exec($command, $output, $return_var);

                if ($return_var === 0 && file_exists($unlocked_file)) {

                    unlink($target_path);
                    rename($unlocked_file, $target_path);

                } else {

                    error_log("QPDF ERROR: " . implode("\n", $output));
                }
            }

            return $new_name;
        }
    }

    return '';
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
        <div class="custom-page-header">
            <h1><i class="fa fa-users"></i> Employee Directory</h1>
            <div class="header-actions">
                <button class="btn btn-primary" data-toggle="modal" data-target="#addEmployeeModal">
                    <i class="fa fa-user-plus"></i> Add new Employee
                </button>
            </div>
        </div>
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
                                <th class="text-center">ID</th>
                                <th class="text-center">Image</th>
                                <th>Employee Name</th>
                                <th class="text-center">Information</th>
                                <th class="text-center">Performance</th>
                                <th class="text-center">Documents</th>
                                <th class="text-center">Actions</th>
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
                                    $email = htmlspecialchars($row['email']);
                                    $img = !empty($row['employee_image']) ? 'uploads/' . $row['employee_image'] : 'admin_images/default.png';
                                    
                                    $perfRow = isset($performanceMap[$pk]) ? $performanceMap[$pk] : null;
                                    $perfTotal = $perfRow ? (int)$perfRow['total'] : null;
                                    $absentPrefill = $perfRow ? (int)$perfRow['absent'] : (isset($absencePoints[$pk]) ? $absencePoints[$pk] : 0);
                                    $latePrefill = $perfRow ? (int)$perfRow['late'] : (isset($latePoints[$pk]) ? $latePoints[$pk] : 0);
                                    
                                    // Build history payload
                                    $histSeries = array();
                                    foreach ($historyMonths as $hm) {
                                        $k = $hm['year'] . '-' . $hm['month'];
                                        $val = isset($historyTotals[$pk][$k]) ? $historyTotals[$pk][$k] : 0;
                                        $histSeries[] = array('label' => $hm['label'], 'value' => $val);
                                    }
                                    $histJson = htmlspecialchars(json_encode($histSeries), ENT_QUOTES, 'UTF-8');
                                    
                                    $breakdown = array(
                                        array('label' => 'Absent (auto)', 'max' => 20, 'user' => $absentPrefill),
                                        array('label' => 'Late (auto)', 'max' => 10, 'user' => $latePrefill),
                                        array('label' => 'Task Sheet', 'max' => 10, 'user' => $perfRow ? (int)$perfRow['task_sheet'] : 0),
                                        array('label' => 'Performance', 'max' => 35, 'user' => $perfRow ? (float)$perfRow['performance_score'] : 0),
                                        array('label' => 'Dressing & Behaviour', 'max' => 10, 'user' => $perfRow ? (int)$perfRow['dressing_behaviour'] : 0),
                                        array('label' => 'RND', 'max' => 15, 'user' => $perfRow ? (int)$perfRow['rnd'] : 0)
                                    );
                                    $calculatedTotal = 0;
                                    foreach ($breakdown as $b) { $calculatedTotal += isset($b['user']) ? (float)$b['user'] : 0; }
                                    $effectiveTotal = ($perfTotal !== null) ? $perfTotal : $calculatedTotal;
                                    $breakdown[] = array('label' => 'Total', 'max' => 100, 'user' => $effectiveTotal);
                                    $breakdownJson = htmlspecialchars(json_encode($breakdown), ENT_QUOTES, 'UTF-8');
                            ?>
                                    <tr>
                                        <td class="text-center" style="vertical-align: middle; font-weight: 700; color: #64748b;"><?php echo $pk; ?></td>
                                        <td class="text-center" style="vertical-align: middle;">
                                            <div style="position: relative; display: inline-block;">
                                                <img src="<?php echo $img; ?>" class="emp-table-img" alt="Profile" 
                                                    onclick="viewImage('<?php echo $img; ?>', '<?php echo $name; ?>')"
                                                    title="Click to zoom">
                                            </div>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <div style="font-weight: 700; color: #1e293b;"><?php echo $name; ?></div>
                                            <div style="font-size: 11px; color: #64748b;"><i class="fa fa-phone"></i> <?php echo $phone; ?></div>
                                            <div style="font-size: 11px; color: #64748b;"><i class="fa fa-envelope"></i> <?php echo $email; ?></div>
                                            <div style="font-size: 11px; color: #64748b; margin-top: 2px;"><i class="fa fa-calendar-check-o"></i> Joined: <?php echo (!empty($row['join_date']) && $row['join_date'] !== '0000-00-00') ? date('d-m-Y', strtotime($row['join_date'])) : '-'; ?></div>
                                        </td>
                                        <td class="text-center" style="vertical-align: middle;">
                                            <button type="button" class="btn btn-xs btn-primary" style="padding: 6px 12px; border-radius: 6px; font-weight: 600;"
                                                data-emp='<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>' 
                                                onclick="openViewEmployee(this, 'section_personal')">
                                                <i class="fa fa-eye"></i> View Profile
                                            </button>
                                        </td>
                                        <td class="text-center" style="vertical-align: middle;">
                                            <?php
                                            $scoreClass = 'score-plain';
                                            if ($perfRow) {
                                                if ($perfTotal < 30) $scoreClass = 'score-red';
                                                elseif ($perfTotal <= 49) $scoreClass = 'score-gray';
                                                elseif ($perfTotal <= 69) $scoreClass = 'score-amber';
                                                else $scoreClass = 'score-green';
                                            }
                                            ?>
                                            <button type="button" class="btn btn-xs score-btn <?php echo $scoreClass; ?> <?php echo $perfRow ? '' : 'btn-default'; ?>"
                                                style="padding: 4px 8px; margin-bottom: 4px; border-radius: 4px;"
                                                data-history="<?php echo $histJson; ?>"
                                                data-breakdown="<?php echo $breakdownJson; ?>"
                                                data-total="<?php echo $effectiveTotal; ?>"
                                                data-empname="<?php echo $name; ?>"
                                                onclick="openPerfHistory(this)">
                                                <?php echo $perfRow ? ($perfTotal . ' / 100') : 'Not set'; ?>
                                            </button><br>
                                            <button class="btn btn-xs btn-warning" style="padding: 4px 6px; font-size: 10px;"
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
                                        <td class="text-center" style="vertical-align: middle;">
                                            <a href="javascript:void(0)" onclick="openDocuments(<?php echo $pk; ?>)" class="btn btn-xs btn-default" style="padding: 6px 12px;" title="View Documents">
                                                <i class="fa fa-file"></i> View
                                            </a>
                                        </td>
                                        <td class="text-center" style="vertical-align: middle;">
                                            <div style="display: flex; gap: 6px; justify-content: center;">
                                                <button class="btn btn-xs btn-info" style="padding: 10px 12px; border-radius: 8px; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);"
                                                    data-toggle="modal" data-target="#editEmployeeModal"
                                                    data-id="<?php echo $pk; ?>" data-name="<?php echo $name; ?>"
                                                    data-img="<?php echo $img; ?>"
                                                    data-phone="<?php echo $phone; ?>" data-email="<?php echo $email; ?>"
                                                    data-address="<?php echo $row['address']; ?>" data-join="<?php echo $row['join_date']; ?>"
                                                    data-basic="<?php echo $row['basic_salary']; ?>" data-hra="<?php echo $row['hra']; ?>"
                                                    data-allowance="<?php echo $row['allowance']; ?>" data-deductions="<?php echo $row['deductions']; ?>"
                                                    data-salary="<?php echo $row['salary']; ?>" data-age="<?php echo $row['age']; ?>"
                                                    data-dob="<?php echo $row['dob']; ?>" data-work_exp="<?php echo $row['work_experience']; ?>"
                                                    data-marital="<?php echo $row['marital_status']; ?>" data-dependents="<?php echo $row['num_dependents']; ?>"
                                                    data-e_name="<?php echo $row['emergency_name']; ?>" data-e_rel="<?php echo $row['emergency_relationship']; ?>"
                                                    data-e_addr="<?php echo $row['emergency_address']; ?>" data-e_phone="<?php echo $row['emergency_phone']; ?>"
                                                    data-gender="<?php echo $row['gender']; ?>" data-blood="<?php echo $row['blood_group']; ?>"
                                                    data-edu='<?php echo htmlspecialchars($row['education_json'] ?: "[]", ENT_QUOTES); ?>' 
                                                    data-emp_hist='<?php echo htmlspecialchars($row['employment_json'] ?: "[]", ENT_QUOTES); ?>'
                                                    data-acc_name="<?php echo $row['account_name']; ?>" data-bank_br="<?php echo $row['bank_branch']; ?>"
                                                    data-acc_num="<?php echo $row['account_number']; ?>" data-acc_ifsc="<?php echo $row['account_type_ifsc']; ?>"
                                                    data-offer_latter="<?php echo $row['offer_latter']; ?>" data-nda="<?php echo $row['NDA']; ?>"
                                                    data-aadhar="<?php echo $row['Aadhar_card']; ?>" data-pan="<?php echo $row['Pan_card']; ?>"
                                                    data-photo="<?php echo $row['Passportsize_photo']; ?>" data-salary_slip="<?php echo $row['old_company_slary_slip']; ?>"
                                                    onclick="openEditEmployee(this)" title="Edit Employee">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button onclick="deleteEmployee(<?php echo $pk; ?>)" class="btn btn-xs btn-danger" style="padding: 6px 10px; border-radius: 8px; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.2);" title="Delete Record">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                            ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
                                        <i class="fa fa-inbox" style="font-size: 24px; display: block; margin-bottom: 10px;"></i> No employees found in the directory.
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>


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
        </div>
    </div>
</div>

<!-- View Employee Modal -->
<div class="modal fade" id="viewEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="viewEmployeeModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%; max-width: 1100px;">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 25px 70px rgba(0,0,0,0.3);">
            <div class="profile-modal-body">
                <!-- Sidebar Navigation -->
                <div class="profile-sidebar">
                    <div class="profile-sidebar-header">
                        <img id="view_img" src="admin_images/default.png" class="view-image-large" style="width: 140px; height: 140px; border-radius: 20px; margin-bottom: 20px;" alt="Profile">
                        <h4 id="view_name" style="font-weight: 800; color: #0f172a; margin: 0 0 5px 0;">Employee Name</h4>
                        <p id="view_id_label" style="color: #64748b; font-size: 13px; font-weight: 600; margin-bottom: 10px;">ID: 001</p>
                        <span id="view_gender_badge" class="label label-primary" style="background: #4f46e5; padding: 5px 12px; border-radius: 30px; font-size: 11px;">Male</span>
                        <div id="view_join_sidebar" style="font-size: 11px; color: #64748b; font-weight: 600; margin-top: 10px;">Joined: -</div>
                    </div>
                    
                    <div class="profile-nav">
                        <div class="profile-nav-item active" data-target="section_personal" onclick="switchProfileTab(this)">
                            <i class="fa fa-user"></i> Personal Information
                        </div>
                        <div class="profile-nav-item" data-target="section_documents" onclick="switchProfileTab(this)">
                            <i class="fa fa-file"></i> Documents
                        </div>
                        <div class="profile-nav-item" data-target="section_emergency" onclick="switchProfileTab(this)">
                            <i class="fa fa-ambulance"></i> Emergency Contact
                        </div>
                        <div class="profile-nav-item" data-target="section_education" onclick="switchProfileTab(this)">
                            <i class="fa fa-graduation-cap"></i> Educational Background
                        </div>
                        <div class="profile-nav-item" data-target="section_history" onclick="switchProfileTab(this)">
                            <i class="fa fa-briefcase"></i> Employment History
                        </div>
                        <div class="profile-nav-item" data-target="section_bank" onclick="switchProfileTab(this)">
                            <i class="fa fa-bank"></i> Bank Details
                        </div>
                        <div class="profile-nav-item" data-target="section_salary" onclick="switchProfileTab(this)">
                            <i class="fa fa-money"></i> Professional & Salary
                        </div>      
                    </div>
                    
                    <!-- <div style="margin-top: auto; padding: 20px 25px;">
                        <button type="button" class="btn btn-default btn-block" data-dismiss="modal" style="border-radius: 8px; font-weight: 600; color: #64748b;">
                            <i class="fa fa-times"></i> Close Profile
                        </button>
                    </div> -->
                </div>

                <!-- Main Content Area -->
                <div class="profile-content">
                    <button type="button" class="close-profile-btn" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                    <!-- Personal Info Section -->
                    <div id="section_personal" class="profile-section active">
                        <h3 class="profile-section-title"><i class="fa fa-user" style="color: #4f46e5;"></i> Personal Information</h3>
                        <div class="profile-data-grid">
                            <div class="profile-data-card">
                                <span class="profile-data-label">Date of Birth</span>
                                <span class="profile-data-value" id="view_dob">-</span>
                            </div>
                            <div class="profile-data-card">
                                <span class="profile-data-label">Age</span>
                                <span class="profile-data-value" id="view_age">-</span>
                            </div>
                            <div class="profile-data-card">
                                <span class="profile-data-label">Marital Status</span>
                                <span class="profile-data-value" id="view_marital">-</span>
                            </div>
                            <div class="profile-data-card">
                                <span class="profile-data-label">Dependents</span>
                                <span class="profile-data-value" id="view_dependents">0</span>
                            </div>
                            <div class="profile-data-card">
                                <span class="profile-data-label">Phone Number</span>
                                <span class="profile-data-value" id="view_phone">-</span>
                            </div>
                            <div class="profile-data-card">
                                <span class="profile-data-label">Email Address</span>
                                <span class="profile-data-value" id="view_email">-</span>
                            </div>
                            <div class="profile-data-card">
                                <span class="profile-data-label">Blood Group</span>
                                <span class="profile-data-value" id="view_blood">-</span>
                            </div>
                            <div class="profile-data-card" style="grid-column: span 2;">
                                <span class="profile-data-label">Residential Address</span>
                                <span class="profile-data-value" id="view_address">-</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Documents Section -->
                    <div id="section_documents" class="profile-section">
                        <h3 class="profile-section-title"><i class="fa fa-file" style="color: #059669;"></i> Documents</h3>
                        <div class="profile-data-grid">
                            <div class="profile-data-card" style="grid-column: span 2;">
                                <span class="profile-data-label">Uploaded Documents</span>
                                <div id="view_documents" style="margin-top: 10px;">
                                    <p style="text-align: center; color: #999;">No documents found.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact Section -->
                    <div id="section_emergency" class="profile-section">
                        <h3 class="profile-section-title"><i class="fa fa-ambulance" style="color: #ef4444;"></i> Emergency Contact</h3>
                        <div class="profile-data-grid">
                            <div class="profile-data-card">
                                <span class="profile-data-label">Contact Person Name</span>
                                <span class="profile-data-value" id="view_e_name">-</span>
                            </div>
                            <div class="profile-data-card">
                                <span class="profile-data-label">Relationship</span>
                                <span class="profile-data-value" id="view_e_rel">-</span>
                            </div>
                            <div class="profile-data-card">
                                <span class="profile-data-label">Contact Phone</span>
                                <span class="profile-data-value" id="view_e_phone">-</span>
                            </div>
                            <div class="profile-data-card" style="grid-column: span 2;">
                                <span class="profile-data-label">Contact Address</span>
                                <span class="profile-data-value" id="view_e_addr">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Education Section -->
                    <div id="section_education" class="profile-section">
                        <h3 class="profile-section-title"><i class="fa fa-graduation-cap" style="color: #4f46e5;"></i> Educational Background</h3>
                        <div class="table-responsive" style="border: 1px solid #f1f5f9; border-radius: 12px; overflow: hidden;">
                            <table class="table table-hover" style="margin-bottom: 0;">
                                <thead style="background: #f8fafc;">
                                    <tr>
                                        <th style="padding: 15px; border: none; color: #64748b; font-size: 12px; text-transform: uppercase;">Degree / Course</th>
                                        <th style="padding: 15px; border: none; color: #64748b; font-size: 12px; text-transform: uppercase;">University / Institute</th>
                                        <th style="padding: 15px; border: none; color: #64748b; font-size: 12px; text-transform: uppercase;">Year</th>
                                        <th style="padding: 15px; border: none; color: #64748b; font-size: 12px; text-transform: uppercase;">Grade</th>
                                    </tr>
                                </thead>
                                <tbody id="view_edu_list"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- History Section -->
                    <div id="section_history" class="profile-section">
                        <h3 class="profile-section-title"><i class="fa fa-briefcase" style="color: #4f46e5;"></i> Employment History</h3>
                        <div class="table-responsive" style="border: 1px solid #f1f5f9; border-radius: 12px; overflow: hidden;">
                            <table class="table table-hover" style="margin-bottom: 0;">
                                <thead style="background: #f8fafc;">
                                    <tr>
                                        <th style="padding: 15px; border: none; color: #64748b; font-size: 12px; text-transform: uppercase;">Company Name</th>
                                        <th style="padding: 15px; border: none; color: #64748b; font-size: 12px; text-transform: uppercase;">Position</th>
                                        <th style="padding: 15px; border: none; color: #64748b; font-size: 12px; text-transform: uppercase;">Year</th>
                                        <th style="padding: 15px; border: none; color: #64748b; font-size: 12px; text-transform: uppercase;">Reason for Leaving</th>
                                    </tr>
                                </thead>
                                <tbody id="view_hist_list"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Bank Details Section -->
                    <div id="section_bank" class="profile-section">
                        <h3 class="profile-section-title"><i class="fa fa-bank" style="color: #4f46e5;"></i> Bank Details</h3>
                        <div class="profile-data-grid">
                            <div class="profile-data-card">
                                <span class="profile-data-label">Account Holder Name</span>
                                <span class="profile-data-value" id="view_acc_name">-</span>
                            </div>
                            <div class="profile-data-card">
                                <span class="profile-data-label">Bank & Branch</span>
                                <span class="profile-data-value" id="view_bank_br">-</span>
                            </div>
                            <div class="profile-data-card">
                                <span class="profile-data-label">Account Number</span>
                                <span class="profile-data-value" id="view_acc_num">-</span>
                            </div>
                            <div class="profile-data-card">
                                <span class="profile-data-label">IFSC Code / Type</span>
                                <span class="profile-data-value" id="view_acc_ifsc">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Professional Section -->
                    <div id="section_salary" class="profile-section">
                        <h3 class="profile-section-title"><i class="fa fa-money" style="color: #059669;"></i> Professional & Salary Details</h3>
                        <div class="profile-data-grid">
                            <div class="profile-data-card" style="background: #ecfdf5; border-color: #d1fae5;">
                                <span class="profile-data-label" style="color: #059669;">Net Monthly Salary</span>
                                <span class="profile-data-value" id="view_salary" style="color: #047857; font-size: 20px;">₹ 0.00</span>
                            </div>
                            <div class="profile-data-card">
                                <span class="profile-data-label">Joining Date</span>
                                <span class="profile-data-value" id="view_join">-</span>
                            </div>
                            <div class="profile-data-card">
                                <span class="profile-data-label">Basic Salary</span>
                                <span class="profile-data-value" id="view_basic">0.00</span>
                            </div>
                            <div class="profile-data-card">
                                <span class="profile-data-label">HRA</span>
                                <span class="profile-data-value" id="view_hra">0.00</span>
                            </div>
                            <div class="profile-data-card">
                                <span class="profile-data-label">Allowance</span>
                                <span class="profile-data-value" id="view_allowance">0.00</span>
                            </div>
                            <div class="profile-data-card">
                                <span class="profile-data-label">Deductions</span>
                                <span class="profile-data-value" id="view_deductions">0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Viewer Modal -->
<div class="modal fade" id="imageViewerModal" tabindex="-1" role="dialog" style="background: rgba(15, 23, 42, 0.9);">
    <div class="modal-dialog" role="document" style="width: fit-content; max-width: 90vw; margin: 10vh auto;">
        <div class="modal-content" style="background: transparent; border: none; box-shadow: none;">
            <div class="modal-body text-center" style="padding: 0; position: relative;">
                <button type="button" class="close" data-dismiss="modal" style="position: absolute; right: -40px; top: -10px; color: white; opacity: 1; font-size: 35px; text-shadow: 0 0 10px rgba(0,0,0,0.5);">&times;</button>
                <img id="viewer_img" src="" class="view-image-round">
                <h3 id="viewer_name" style="color: white; margin-top: 25px; font-weight: 700; font-size: 24px; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">Employee Name</h3>
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

    const formatDate = (dateStr) => {
        if (!dateStr || dateStr === '0000-00-00') return '-';
        const parts = dateStr.split('-');
        if (parts.length !== 3) return dateStr;
        // Handle yyyy-mm-dd (Standard DB format)
        if (parts[0].length === 4) {
            return `${parts[2]}-${parts[1]}-${parts[0]}`;
        }
        // Handle dd-mm-yyyy or other
        return dateStr;
    };

    function switchProfileTab(el) {
        if (!el) return;
        const target = el.getAttribute('data-target');
        
        // Update Nav
        document.querySelectorAll('.profile-nav-item').forEach(item => item.classList.remove('active'));
        el.classList.add('active');
        
        // Update Sections
        document.querySelectorAll('.profile-section').forEach(sec => sec.classList.remove('active'));
        const targetSec = document.getElementById(target);
        if (targetSec) targetSec.classList.add('active');
    }

    function openDocuments(empId) {
        empIdField.value = empId;
        popup.style.display = 'flex';

        fetch('fetch_documents.php?id=' + empId)
            .then(response => response.text())
            .then(data => {
                popupDocs.innerHTML = data || '<p style="text-align: center; color: #999;">No documents found.</p>';
            });
    }

    function openViewEmployee(btn, sectionId) {
        const data = JSON.parse(btn.dataset.emp);
        
        // Profile Summary
        document.getElementById('view_img').src = data.employee_image ? 'uploads/' + data.employee_image : 'admin_images/default.png';
        document.getElementById('view_name').textContent = data.name || '-';
        document.getElementById('view_id_label').textContent = 'ID: ' + data.id;
        document.getElementById('view_gender_badge').textContent = data.gender || 'Other';
        document.getElementById('view_phone').textContent = data.phone_number || '-';
        document.getElementById('view_email').textContent = data.email || '-';
        document.getElementById('view_blood').textContent = data.blood_group || '-';
        document.getElementById('view_join_sidebar').innerHTML = '<i class="fa fa-calendar-check-o"></i> Joined: ' + formatDate(data.join_date);

        // Personal Info
        document.getElementById('view_dob').textContent = formatDate(data.dob);
        document.getElementById('view_age').textContent = data.age || '-';
        document.getElementById('view_marital').textContent = data.marital_status || '-';
        document.getElementById('view_dependents').textContent = data.num_dependents || '0';
        document.getElementById('view_address').textContent = data.address || '-';

        // Emergency
        document.getElementById('view_e_name').textContent = data.emergency_name || '-';
        document.getElementById('view_e_rel').textContent = data.emergency_relationship || '-';
        document.getElementById('view_e_phone').textContent = data.emergency_phone || '-';
        document.getElementById('view_e_addr').textContent = data.emergency_address || '-';

        // Education
        const eduList = document.getElementById('view_edu_list');
        eduList.innerHTML = '';
        try {
            const eduData = JSON.parse(data.education_json || '[]');
            if (eduData.length === 0) {
                eduList.innerHTML = '<tr><td colspan="4" class="text-center">No education records found.</td></tr>';
            } else {
                eduData.forEach(item => {
                    eduList.innerHTML += `<tr>
                        <td>${item.degree || '-'}</td>
                        <td>${item.univ || '-'}</td>
                        <td>${item.year || '-'}</td>
                        <td>${item.grade || '-'}</td>
                    </tr>`;
                });
            }
        } catch(e) { eduList.innerHTML = '<tr><td colspan="4" class="text-center">Error parsing records.</td></tr>'; }

        // History
        const histList = document.getElementById('view_hist_list');
        histList.innerHTML = '';
        try {
            const histData = JSON.parse(data.employment_json || '[]');
            if (histData.length === 0) {
                histList.innerHTML = '<tr><td colspan="4" class="text-center">No employment history found.</td></tr>';
            } else {
                histData.forEach(item => {
                    histList.innerHTML += `<tr>
                        <td>${item.company || '-'}</td>
                        <td>${item.pos || '-'}</td>
                        <td>${item.year || '-'}</td>
                        <td>${item.reason || '-'}</td>
                    </tr>`;
                });
            }
        } catch(e) { histList.innerHTML = '<tr><td colspan="4" class="text-center">Error parsing records.</td></tr>'; }

        // Bank
        document.getElementById('view_acc_name').textContent = data.account_name || '-';
        document.getElementById('view_bank_br').textContent = data.bank_branch || '-';
        document.getElementById('view_acc_num').textContent = data.account_number || '-';
        document.getElementById('view_acc_ifsc').textContent = data.account_type_ifsc || '-';

        // Salary
        document.getElementById('view_join').textContent = formatDate(data.join_date);
        document.getElementById('view_salary').textContent = parseFloat(data.salary || 0).toFixed(2);
        document.getElementById('view_basic').textContent = parseFloat(data.basic_salary || 0).toFixed(2);
        document.getElementById('view_hra').textContent = parseFloat(data.hra || 0).toFixed(2);
        document.getElementById('view_allowance').textContent = parseFloat(data.allowance || 0).toFixed(2);
        document.getElementById('view_deductions').textContent = parseFloat(data.deductions || 0).toFixed(2);

        // Fetch and show all documents (Specific + Extra)
        const viewDocsList = document.getElementById('view_documents');
        viewDocsList.innerHTML = '<div style="text-align:center; padding:20px;"><i class="fa fa-spinner fa-spin"></i> Loading documents...</div>';
        
        fetch('fetch_all_documents.php?id=' + data.id)
            .then(res => res.text())
            .then(html => {
                viewDocsList.innerHTML = html;
            })
            .catch(() => {
                viewDocsList.innerHTML = '<p style="text-align:center; color:#ef4444;">Error loading documents.</p>';
            });

        // Handle Navigation (Switch to clicked category)
        const tabToActivate = document.querySelector(`.profile-nav-item[data-target="${sectionId}"]`);
        if (tabToActivate) {
            switchProfileTab(tabToActivate);
        } else {
            // Default to personal if section doesn't match
            switchProfileTab(document.querySelector('.profile-nav-item[data-target="section_personal"]'));
        }

        // Open Modal
        $('#viewEmployeeModal').modal('show');
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

    /* Comprehensive Form Styles */
    .form-section-title {
        background: #f8fafc;
        padding: 8px 12px;
        border-left: 4px solid #3b82f6;
        margin: 20px 0 15px 0;
        font-weight: 700;
        color: #1e293b;
        font-size: 15px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-section-title:first-child {
        margin-top: 0;
    }

    .modal-lg-custom {
        width: 90%;
        max-width: 1000px;
    }

    .grid-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }

    .grid-col {
        display: flex;
        flex-direction: column;
    }

    .grid-col label {
        font-weight: 600;
        margin-bottom: 5px;
        color: #475569;
        font-size: 13px;
    }

    .table-input {
        width: 100%;
        border: 1px solid #e2e8f0;
        padding: 6px 10px;
        border-radius: 4px;
        font-size: 13px;
    }

    .dynamic-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    .dynamic-table th {
        background: #f1f5f9;
        color: #475569;
        font-weight: 600;
        font-size: 12px;
        padding: 8px;
        text-align: left;
        border: 1px solid #e2e8f0;
    }

    .dynamic-table td {
        padding: 5px;
        border: 1px solid #e2e8f0;
    }

    /* View Modal Specific Styles */
    .view-info-item {
        margin-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 8px;
    }

    .view-info-label {
        font-weight: 700;
        color: #64748b;
        font-size: 11px;
        text-transform: uppercase;
        display: block;
        margin-bottom: 2px;
    }

    .view-info-value {
        color: #1e293b;
        font-size: 14px;
        font-weight: 500;
    }

    .view-image-large {
        width: 120px;
        height: 120px;
        border-radius: 20px;
        object-fit: cover;
        object-position: center 10%; /* Ensures face focus in sidebar */
        border: 3px solid #fff;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        margin-bottom: 15px;
        transition: transform 0.3s;
    }

    .view-modal-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 15px 20px;
    }
    
    .table-view-btn {
        padding: 4px 8px;
        font-size: 11px;
        border-radius: 4px;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
    }
    
    .table-view-btn:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .emp-table-img {
        width: 45px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        object-position: center 10%; /* Focus on the face (top portion) */
        border: 2px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .emp-table-img:hover {
        transform: scale(1.15) rotate(5deg);
        border-color: #4f46e5;
        box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4);
    }

    /* Round Profile Modal & Preview Styles */
    .view-image-round {
        width: 320px;
        height: 320px;
        border-radius: 50%;
        object-fit: cover;
        object-position: center 10%; /* Ensures the face is centered in the circle */
        border: 8px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 0 50px rgba(0, 0, 0, 0.5);
        background: #f8fafc;
        padding: 5px;
    }

    .preview-circle-container {
        display: flex; 
        align-items: center;
        gap: 20px;
        margin-top: 10px;
        padding: 10px;
        background: #f8fafc;
        border-radius: 12px;
        border: 1px dashed #e2e8f0;
    }

    .image-preview-circle {
        width: 70px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        object-position: center 10%; /* Face-first preview */
        border: 3px solid #fff;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        background: #eef2ff;
    }

    /* Professional Profile Modal Styles */
    .profile-modal-body {
        display: flex;
        padding: 0 !important;
        background: #f8fafc;
        min-height: 500px;
    }

    .profile-sidebar {
        width: 280px;
        background: #ffffff;
        border-right: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        padding: 30px 0;
    }

    .profile-sidebar-header {
        padding: 0 25px 25px 25px;
        text-align: center;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 15px;
    }

    .profile-nav {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .profile-nav-item {
        padding: 12px 25px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #64748b;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        border-right: 3px solid transparent;
    }

    .profile-nav-item i {
        width: 20px;
        font-size: 16px;
    }

    .profile-nav-item:hover {
        background: #f1f5f9;
        color: #4f46e5;
    }

    .profile-nav-item.active {
        background: #eef2ff;
        color: #4f46e5;
        border-right-color: #4f46e5;
    }

    .profile-content {
        flex: 1;
        padding: 40px;
        background: #ffffff;
        overflow-y: auto;
    }

    .profile-section-title {
        font-size: 20px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .profile-data-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
    }

    .profile-data-card {
        background: #f8fafc;
        padding: 15px 20px;
        border-radius: 10px;
        border: 1px solid #f1f5f9;
    }

    .profile-data-label {
        font-size: 11px;
        text-transform: uppercase;
        color: #94a3b8;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
        display: block;
    }

    .profile-data-value {
        font-size: 15px;
        color: #1e293b;
        font-weight: 600;
    }

    .profile-section {
        display: none;
    }

    .profile-section.active {
        display: block;
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .close-profile-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        background: #f1f5f9;
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s;
        z-index: 100;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
        /* .close-profile-btn:hover {
            background: #e2e8f0;
            color: #0f172a;
            transform: rotate(90deg);
        } */

    .profile-content {
        position: relative;
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
    <div class="modal-dialog modal-lg modal-lg-custom">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-user-plus"></i> Add New Employee</h4>
            </div>
            <form method="POST" id="add_employee_form" enctype="multipart/form-data">
                <div class="modal-body" style="max-height: 80vh; overflow-y: auto; padding: 25px;">
                    <!-- Personal Information -->
                    <div class="form-section-title">Personal Information</div>
                    <div class="grid-row">
                        <div class="grid-col" style="grid-column: span 3;">
                            <label>Employee Image *</label>
                            <div class="preview-circle-container">
                                <img id="add_preview" src="admin_images/default.png" class="image-preview-circle">
                                <div style="flex: 1;">
                                    <input type="file" name="employee_image" class="form-control" accept="image/*" required 
                                        onchange="handleImagePreview(this, 'add_preview')">
                                    <small style="color: #64748b; margin-top: 5px; display: block;">Select a round profile photo</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid-row">
                        <div class="grid-col" style="grid-column: span 2;">
                            <label>Full Name *</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter Full Name" required>
                        </div>
                        <div class="grid-col">
                            <label>Age (Auto-set)</label>
                            <input type="number" name="age" id="add_age" class="form-control" readonly style="background-color: #f1f5f9; cursor: not-allowed; border: 1px solid #e2e8f0; color: #64748b;" placeholder="Calculated from DoB">
                        </div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-col" style="grid-column: span 3;">
                            <label>Address *</label>
                            <input type="text" name="address" class="form-control" placeholder="House No, Street, City" required>
                        </div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-col">
                            <label>Phone *</label>
                            <input type="tel" name="number" class="form-control" maxlength="10" placeholder="10-digit mobile" required>
                        </div>
                        <div class="grid-col">
                            <label>Blood Group *</label>
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
                        <div class="grid-col">
                            <label>DoB</label>
                            <input type="date" id="add_dob" name="dob" class="form-control" onchange="autoCalculateAge(this, 'add_age')">
                        </div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-col" style="grid-column: span 2;">
                            <label>Email *</label>
                            <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
                        </div>
                        <div class="grid-col">
                            <label>Work Experience</label>
                            <input type="text" name="work_experience" class="form-control" placeholder="e.g. 2 Years">
                        </div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-col">
                            <label>Marital Status</label>
                            <div style="display: flex; gap: 15px; align-items: center; height: 34px;">
                                <label style="margin: 0; font-weight: normal;"><input type="radio" name="marital_status" value="Single"> Single</label>
                                <label style="margin: 0; font-weight: normal;"><input type="radio" name="marital_status" value="Married"> Married</label>
                            </div>
                        </div>
                        <div class="grid-col">
                            <label>Number of Dependent(s)</label>
                            <input type="number" name="num_dependents" class="form-control" value="0">
                        </div>
                        <div class="grid-col">
                            <label>Gender *</label>
                            <select name="gender" class="form-control" required>
                                <option value="">Select</option>
                                <option>Male</option>
                                <option>Female</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>

                    <!-- Documents Section -->
                    <div class="form-section-title">Documents</div>
                    <div class="grid-row">
                        <div class="grid-col">
                            <label>Offer Letter</label>
                            <input type="file" name="offer_latter" class="form-control">
                        </div>
                        <div class="grid-col">
                            <label>Aadhar Card</label>
                            <input type="file" name="Aadhar_card" class="form-control">
                        </div>
                        <div class="grid-col">
                            <label>PAN Card</label>
                            <input type="file" name="Pan_card" class="form-control">
                        </div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-col">
                            <label>NDA</label>
                            <input type="file" name="NDA" class="form-control">
                        </div>
                        <div class="grid-col">
                            <label>Passport Size Photo</label>
                            <input type="file" name="Passportsize_photo" class="form-control">
                        </div>
                        <div class="grid-col">
                            <label>Old Company Salary Slip</label>
                            <input type="file" name="old_company_slary_slip" class="form-control">
                        </div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-col" style="grid-column: span 3;">
                            <label>Additional Documents</label>
                            <input type="file" name="documents[]" class="form-control" multiple>
                            <small style="color: #64748b; margin-top: 5px; display: block;">Select multiple additional files (PDF, Images) if needed</small>
                        </div>
                    </div>

                    <!-- Emergency Contact Details -->
                    <div class="form-section-title">Emergency Contact Details</div>
                    <div class="grid-row">
                        <div class="grid-col" style="grid-column: span 2;">
                            <label>Full Name</label>
                            <input type="text" name="emergency_name" class="form-control" placeholder="Contact Name">
                        </div>
                        <div class="grid-col">
                            <label>Relationship</label>
                            <input type="text" name="emergency_relationship" class="form-control" placeholder="e.g. Father, Spouse">
                        </div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-col" style="grid-column: span 2;">
                            <label>Address</label>
                            <input type="text" name="emergency_address" class="form-control" placeholder="Contact Address">
                        </div>
                        <div class="grid-col">
                            <label>Phone</label>
                            <input type="tel" name="emergency_phone" class="form-control" maxlength="10" placeholder="Mobile Number">
                        </div>
                    </div>

                    <!-- Educational Background -->
                    <div class="form-section-title">Educational Background</div>
                    <table class="dynamic-table" id="edu_table">
                        <thead>
                            <tr>
                                <th>Degree / Course</th>
                                <th>University / Institute</th>
                                <th>Year of Graduate</th>
                                <th>Grade</th>
                                <th>City</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" class="table-input edu-degree"></td>
                                <td><input type="text" class="table-input edu-univ"></td>
                                <td><input type="text" class="table-input edu-year"></td>
                                <td><input type="text" class="table-input edu-grade"></td>
                                <td><input type="text" class="table-input edu-city"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="table-input edu-degree"></td>
                                <td><input type="text" class="table-input edu-univ"></td>
                                <td><input type="text" class="table-input edu-year"></td>
                                <td><input type="text" class="table-input edu-grade"></td>
                                <td><input type="text" class="table-input edu-city"></td>
                            </tr>
                        </tbody>
                    </table>
                    <input type="hidden" name="education_json" id="education_json">

                    <!-- Employment History -->
                    <div class="form-section-title">Employment History</div>
                    <table class="dynamic-table" id="emp_hist_table">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Position</th>
                                <th>Year</th>
                                <th>Reason for Leaving</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" class="table-input hist-company"></td>
                                <td><input type="text" class="table-input hist-pos"></td>
                                <td><input type="text" class="table-input hist-year"></td>
                                <td><input type="text" class="table-input hist-reason"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="table-input hist-company"></td>
                                <td><input type="text" class="table-input hist-pos"></td>
                                <td><input type="text" class="table-input hist-year"></td>
                                <td><input type="text" class="table-input hist-reason"></td>
                            </tr>
                        </tbody>
                    </table>
                    <input type="hidden" name="employment_json" id="employment_json">

                    <!-- Bank Details -->
                    <div class="form-section-title">Bank Details</div>
                    <div class="grid-row">
                        <div class="grid-col">
                            <label>Account Name</label>
                            <input type="text" name="account_name" class="form-control" placeholder="As per Bank Record">
                        </div>
                        <div class="grid-col">
                            <label>Bank & Branch</label>
                            <input type="text" name="bank_branch" class="form-control" placeholder="Bank Name, Branch Name">
                        </div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-col">
                            <label>Account Number</label>
                            <input type="text" name="account_number" class="form-control" placeholder="Bank Account Number">
                        </div>
                        <div class="grid-col">
                            <label>Account Type & IFSC</label>
                            <input type="text" name="account_type_ifsc" class="form-control" placeholder="e.g. Savings / SBIN0001234">
                        </div>
                    </div>

                    <!-- Salary Structure -->
                    <div class="form-section-title">Professional & Salary</div>
                    <div class="grid-row">
                        <div class="grid-col">
                            <label>Join Date *</label>
                            <input type="date" name="joinDate" class="form-control" max="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="grid-col">
                            <label>Basic Pay *</label>
                            <input type="number" id="add_basic_salary" name="basic_salary" class="form-control" required>
                        </div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-col">
                            <label>HRA</label>
                            <input type="number" id="add_hra" name="hra" class="form-control">
                        </div>
                        <div class="grid-col">
                            <label>Allowance</label>
                            <input type="number" id="add_allowance" name="allowance" class="form-control">
                        </div>
                        <div class="grid-col">
                            <label>Deductions</label>
                            <input type="number" id="add_deductions" name="deductions" class="form-control">
                        </div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-col" style="grid-column: span 3;">
                            <label>Total Salary (Auto Calculated)</label>
                            <input type="text" id="add_salary" name="salary" class="form-control" readonly style="background: #f1f5f9; font-weight: 700;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit" class="btn btn-primary" onclick="serializeTables()">
                        <i class="fa fa-save"></i> Add Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function serializeTables() {
        const edu = [];
        document.querySelectorAll('#edu_table tbody tr').forEach(tr => {
            const row = {
                degree: tr.querySelector('.edu-degree').value,
                univ: tr.querySelector('.edu-univ').value,
                year: tr.querySelector('.edu-year').value,
                grade: tr.querySelector('.edu-grade').value,
                city: tr.querySelector('.edu-city').value
            };
            if (row.degree || row.univ) edu.push(row);
        });
        document.getElementById('education_json').value = JSON.stringify(edu);

        const hist = [];
        document.querySelectorAll('#emp_hist_table tbody tr').forEach(tr => {
            const row = {
                company: tr.querySelector('.hist-company').value,
                pos: tr.querySelector('.hist-pos').value,
                year: tr.querySelector('.hist-year').value,
                reason: tr.querySelector('.hist-reason').value
            };
            if (row.company || row.pos) hist.push(row);
        });
        document.getElementById('employment_json').value = JSON.stringify(hist);
    }

    document.addEventListener("DOMContentLoaded", function() {
        function calculateTotalAdd() {
            let basic = parseFloat(document.getElementById('add_basic_salary')?.value) || 0;
            let hra = parseFloat(document.getElementById('add_hra')?.value) || 0;
            let allowance = parseFloat(document.getElementById('add_allowance')?.value) || 0;
            let deduction = parseFloat(document.getElementById('add_deductions')?.value) || 0;
            document.getElementById('add_salary').value = (basic + hra + allowance - deduction).toFixed(2);
        }
        ['add_basic_salary', 'add_hra', 'add_allowance', 'add_deductions'].forEach(id => {
            document.getElementById(id)?.addEventListener('input', calculateTotalAdd);
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

    $age = mysqli_real_escape_string($con, $_POST['age'] ?? '');
    $dob = mysqli_real_escape_string($con, $_POST['dob'] ?? '');
    $work_exp = mysqli_real_escape_string($con, $_POST['work_experience'] ?? '');
    $marital = mysqli_real_escape_string($con, $_POST['marital_status'] ?? '');
    $dependents = mysqli_real_escape_string($con, $_POST['num_dependents'] ?? 0);

    $e_name = mysqli_real_escape_string($con, $_POST['emergency_name'] ?? '');
    $e_rel = mysqli_real_escape_string($con, $_POST['emergency_relationship'] ?? '');
    $e_addr = mysqli_real_escape_string($con, $_POST['emergency_address'] ?? '');
    $e_phone = mysqli_real_escape_string($con, $_POST['emergency_phone'] ?? '');

    $edu_json = mysqli_real_escape_string($con, $_POST['education_json'] ?? '[]');
    $emp_json = mysqli_real_escape_string($con, $_POST['employment_json'] ?? '[]');

    $acc_num = mysqli_real_escape_string($con, $_POST['account_number'] ?? '');
    $acc_ifsc = mysqli_real_escape_string($con, $_POST['account_type_ifsc'] ?? '');


    $offer_letter = handleFileUpload($_FILES['offer_latter']);
    $NDA = handleFileUpload($_FILES['NDA']);
    $Aadhar_card = handleFileUpload($_FILES['Aadhar_card']);
    $Pan_card = handleFileUpload($_FILES['Pan_card']);
    $Passportsize_photo = handleFileUpload($_FILES['Passportsize_photo']);
    $old_company_slary_slip = handleFileUpload($_FILES['old_company_slary_slip']);
    $employee_image = '';
    if (isset($_FILES['employee_image']) && $_FILES['employee_image']['error'] == 0) {
        $img_name = $_FILES['employee_image']['name'];
        $tmp_name = $_FILES['employee_image']['tmp_name'];
        $ext = pathinfo($img_name, PATHINFO_EXTENSION);
        $new_img_name = time() . '_' . rand(1000, 9999) . '.' . $ext;
        if (move_uploaded_file($tmp_name, "uploads/" . $new_img_name)) {
            $employee_image = $new_img_name;
        }
    }

    // Generate random password
    $plainPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

    // Save password directly (NO HASH)
    $query = "INSERT INTO emp_list 
    (name, phone_number, address, email, blood_group, gender, join_date, basic_salary, hra, allowance, deductions, salary, password, 
    age, dob, work_experience, marital_status, num_dependents, emergency_name, emergency_relationship, emergency_address, emergency_phone, 
    education_json, employment_json, account_name, bank_branch, account_number, account_type_ifsc, employee_image, offer_latter, NDA, Aadhar_card, Pan_card, Passportsize_photo, old_company_slary_slip)
    VALUES 
    ('$name', '$contact', '$address', '$email', '$blood', '$gender', '$joinDate', '$basic', '$hra', '$allowance', '$deductions', '$salary', '$plainPassword', 
    '$age', '$dob', '$work_exp', '$marital', '$dependents', '$e_name', '$e_rel', '$e_addr', '$e_phone', 
    '$edu_json', '$emp_json', '$acc_name', '$bank_br', '$acc_num', '$acc_ifsc', '$employee_image', '$offer_letter', '$NDA', '$Aadhar_card', '$Pan_card', '$Passportsize_photo', '$old_company_slary_slip')";

    $run = mysqli_query($con, $query);

    if ($run) {
        $emp_id = mysqli_insert_id($con);
        // Handle additional multiple documents
        if (!empty($_FILES['documents']['name'][0])) {
            foreach ($_FILES['documents']['name'] as $key => $name) {
                if ($_FILES['documents']['error'][$key] == 0) {
                    $tmp_name = $_FILES['documents']['tmp_name'][$key];
                    $ext = pathinfo($name, PATHINFO_EXTENSION);
                    $new_name = time() . '_extra_' . rand(1000, 9999) . '.' . $ext;
                    if (move_uploaded_file($tmp_name, "uploads/" . $new_name)) {
                        mysqli_query($con, "INSERT INTO employee_documents (emp_id, file_name) VALUES ('$emp_id', '$new_name')");
                    }
                }
            }
        }

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

    $offer_letter = mysqli_real_escape_string($con, $_POST['offer_letter']);
    $NDA = mysqli_real_escape_string($con, $_POST['NDA']);
    $Aadhar_card = mysqli_real_escape_string($con, $_POST['Aadhar_card']);
    $Pan_card = mysqli_real_escape_string($con, $_POST['Pan_card']);
    $passportsize_photo = mysqli_real_escape_string($con, $_POST['passportsize_photo']);
    $old_company_salary_slip = mysqli_real_escape_string($con, $_POST['old_company_salary_slip']);

    $basic = mysqli_real_escape_string($con, $_POST['basic_salary']);
    $hra = mysqli_real_escape_string($con, $_POST['hra']);
    $allowance = mysqli_real_escape_string($con, $_POST['allowance']);
    $deductions = mysqli_real_escape_string($con, $_POST['deductions']);
    $salary = mysqli_real_escape_string($con, $_POST['salary']);

    $age = mysqli_real_escape_string($con, $_POST['age'] ?? '');
    $dob = mysqli_real_escape_string($con, $_POST['dob'] ?? '');
    $work_exp = mysqli_real_escape_string($con, $_POST['work_experience'] ?? '');
    $marital = mysqli_real_escape_string($con, $_POST['marital_status'] ?? '');
    $dependents = mysqli_real_escape_string($con, $_POST['num_dependents'] ?? 0);

    $e_name = mysqli_real_escape_string($con, $_POST['emergency_name'] ?? '');
    $e_rel = mysqli_real_escape_string($con, $_POST['emergency_relationship'] ?? '');
    $e_addr = mysqli_real_escape_string($con, $_POST['emergency_address'] ?? '');
    $e_phone = mysqli_real_escape_string($con, $_POST['emergency_phone'] ?? '');

    $edu_json = mysqli_real_escape_string($con, $_POST['education_json'] ?? '[]');
    $emp_json = mysqli_real_escape_string($con, $_POST['employment_json'] ?? '[]');

    $acc_num = mysqli_real_escape_string($con, $_POST['account_number'] ?? '');
    $acc_ifsc = mysqli_real_escape_string($con, $_POST['account_type_ifsc'] ?? '');

    // Handle Specific Document Updates
    $doc_updates = "";
    $doc_fields = [
        'offer_latter' => 'offer_latter', 
        'NDA' => 'NDA', 
        'Aadhar_card' => 'Aadhar_card', 
        'Pan_card' => 'Pan_card', 
        'passportsize_photo' => 'Passportsize_photo', 
        'old_company_slary_slip' => 'old_company_slary_slip'
    ];

    foreach ($doc_fields as $post_key => $db_col) {
        if (isset($_FILES[$post_key]) && $_FILES[$post_key]['error'] == 0) {
            $new_file = handleFileUpload($_FILES[$post_key]);
            if ($new_file) {
                // Delete old file if exists
                $old_file_res = mysqli_query($con, "SELECT $db_col FROM emp_list WHERE id='$id'");
                $old_file_row = mysqli_fetch_assoc($old_file_res);
                if (!empty($old_file_row[$db_col]) && file_exists("uploads/" . $old_file_row[$db_col])) {
                    unlink("uploads/" . $old_file_row[$db_col]);
                }
                $doc_updates .= ", $db_col='$new_file'";
            }
        }
    }

    // Image Upload (Update)
    $img_update = "";
    if (isset($_FILES['employee_image']) && $_FILES['employee_image']['error'] == 0) {
        $new_img_name = handleFileUpload($_FILES['employee_image']);
        if ($new_img_name) {
            $img_update = ", employee_image='$new_img_name'";
        }
    }

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
        salary='$salary',
        age='$age',
        dob='$dob',
        work_experience='$work_exp',
        marital_status='$marital',
        num_dependents='$dependents',
        emergency_name='$e_name',
        emergency_relationship='$e_rel',
        emergency_address='$e_addr',
        emergency_phone='$e_phone',
        education_json='$edu_json',
        employment_json='$emp_json',
        account_name='$acc_name',
        bank_branch='$bank_br',
        account_number='$acc_num',
        account_type_ifsc='$acc_ifsc'
        $doc_updates
        $img_update
        WHERE id='$id'");

    echo "<script>
alert('Employee Updated Successfully');
window.location='index.php?emp_directory';

</script>";
}
?>
<div class="modal fade" id="editEmployeeModal">
    <div class="modal-dialog modal-lg modal-lg-custom">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-edit"></i> Edit Employee</h4>
            </div>
            <form method="POST" id="edit_employee_form" enctype="multipart/form-data">
                <div class="modal-body" style="max-height: 80vh; overflow-y: auto; padding: 25px;">
                    <input type="hidden" name="id" id="edit_id">

                    <!-- Personal Information -->
                    <div class="form-section-title">Personal Information</div>
                    <div class="grid-row">
                        <div class="grid-col" style="grid-column: span 1;">
                            <label>Profile Image</label>
                            <div class="preview-circle-container" style="display:block; background: transparent; border: none; padding: 0;">
                                <img id="edit_preview" src="admin_images/default.png" class="image-preview-circle" style="width: 100px; height: 100px;">
                            </div>
                        </div>
                        <div class="grid-col" style="grid-column: span 2;">
                            <label>Change Photo</label>
                            <input type="file" name="employee_image" class="form-control" accept="image/*" 
                                onchange="handleImagePreview(this, 'edit_preview')" style="margin-top: 10px;">
                            <small class="text-muted" style="margin-top: 5px; display: block;">Select a round profile photo</small>
                            
                            <div class="grid-row" style="margin-top: 15px; grid-template-columns: 2fr 1fr; gap: 10px;">
                                <div class="grid-col">
                                    <label>Full Name *</label>
                                    <input type="text" name="name" id="edit_name" class="form-control" required placeholder="Enter Full Name">
                                </div>
                                <div class="grid-col">
                                    <label>Age (Auto-set)</label>
                                    <input type="number" name="age" id="edit_age" class="form-control" readonly style="background-color: #f1f5f9; cursor: not-allowed; border: 1px solid #e2e8f0; color: #64748b;" placeholder="Age">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-col" style="grid-column: span 3;">
                            <label>Address *</label>
                            <input type="text" name="address" id="edit_address" class="form-control" required>
                        </div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-col">
                            <label>Phone *</label>
                            <input type="tel" name="number" id="edit_phone" class="form-control" maxlength="10" required>
                        </div>
                        <div class="grid-col">
                            <label>Blood Group *</label>
                            <select name="blood" id="edit_blood" class="form-control" required>
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
                        <div class="grid-col">
                            <label>DoB</label>
                            <input type="date" name="dob" id="edit_dob" class="form-control" onchange="autoCalculateAge(this, 'edit_age')">
                        </div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-col" style="grid-column: span 2;">
                            <label>Email *</label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="grid-col">
                            <label>Work Experience</label>
                            <input type="text" name="work_experience" id="edit_work_exp" class="form-control">
                        </div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-col">
                            <label>Marital Status</label>
                            <div style="display: flex; gap: 15px; align-items: center; height: 34px;">
                                <label style="margin: 0; font-weight: normal;"><input type="radio" name="marital_status" id="edit_marital_single" value="Single"> Single</label>
                                <label style="margin: 0; font-weight: normal;"><input type="radio" name="marital_status" id="edit_marital_married" value="Married"> Married</label>
                            </div>
                        </div>
                        <div class="grid-col">
                            <label>Number of Dependent(s)</label>
                            <input type="number" name="num_dependents" id="edit_dependents" class="form-control">
                        </div>
                        <div class="grid-col">
                            <label>Gender *</label>
                            <select name="gender" id="edit_gender" class="form-control" required>
                                <option value="">Select</option>
                                <option>Male</option>
                                <option>Female</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>

                    <!-- Documents Section -->
                    <div class="form-section-title">Documents</div>
                    <div class="grid-row">
                        <div class="grid-col">
                            <label>Offer Letter</label>
                            <input type="file" name="offer_latter" id="edit_offer_letter" class="form-control">
                            <div id="view_edit_offer_letter" class="mt-2"></div>
                        </div>
                        <div class="grid-col">
                            <label>NDA</label>
                            <input type="file" name="NDA" id="edit_NDA" class="form-control">
                            <div id="view_edit_NDA" class="mt-2"></div>
                        </div>
                        <div class="grid-col">
                            <label>Aadhar Card</label>
                            <input type="file" name="Aadhar_card" id="edit_Aadhar_card" class="form-control">
                            <div id="view_edit_Aadhar_card" class="mt-2"></div>
                        </div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-col">
                            <label>Pan Card</label>
                            <input type="file" name="Pan_card" id="edit_Pan_card" class="form-control">
                            <div id="view_edit_Pan_card" class="mt-2"></div>
                        </div>
                        <div class="grid-col">
                            <label>Passport Size Photo</label>
                            <input type="file" name="passportsize_photo" id="edit_passportsize_photo" class="form-control">
                            <div id="view_edit_passportsize_photo" class="mt-2"></div>
                        </div>
                        <div class="grid-col">
                            <label>Old Company Salary Slip</label>
                            <input type="file" name="old_company_slary_slip" id="edit_old_company_salary_slip" class="form-control">
                            <div id="view_edit_old_company_salary_slip" class="mt-2"></div>
                        </div>
                    </div>

                    <!-- Emergency Contact Details -->
                    <div class="form-section-title">Emergency Contact Details</div>
                    <div class="grid-row">
                        <div class="grid-col" style="grid-column: span 2;">
                            <label>Full Name</label>
                            <input type="text" name="emergency_name" id="edit_e_name" class="form-control">
                        </div>
                        <div class="grid-col">
                            <label>Relationship</label>
                            <input type="text" name="emergency_relationship" id="edit_e_rel" class="form-control">
                        </div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-col" style="grid-column: span 2;">
                            <label>Address</label>
                            <input type="text" name="emergency_address" id="edit_e_addr" class="form-control">
                        </div>
                        <div class="grid-col">
                            <label>Phone</label>
                            <input type="tel" name="emergency_phone" id="edit_e_phone" class="form-control" maxlength="10">
                        </div>
                    </div>

                    <!-- Educational Background -->
                    <div class="form-section-title">Educational Background</div>
                    <table class="dynamic-table" id="edit_edu_table">
                        <thead>
                            <tr>
                                <th>Degree / Course</th>
                                <th>University / Institute</th>
                                <th>Year of Graduate</th>
                                <th>Grade</th>
                                <th>City</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" class="table-input edu-degree"></td>
                                <td><input type="text" class="table-input edu-univ"></td>
                                <td><input type="text" class="table-input edu-year"></td>
                                <td><input type="text" class="table-input edu-grade"></td>
                                <td><input type="text" class="table-input edu-city"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="table-input edu-degree"></td>
                                <td><input type="text" class="table-input edu-univ"></td>
                                <td><input type="text" class="table-input edu-year"></td>
                                <td><input type="text" class="table-input edu-grade"></td>
                                <td><input type="text" class="table-input edu-city"></td>
                            </tr>
                        </tbody>
                    </table>
                    <input type="hidden" name="education_json" id="edit_education_json">

                    <!-- Employment History -->
                    <div class="form-section-title">Employment History</div>
                    <table class="dynamic-table" id="edit_emp_hist_table">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Position</th>
                                <th>Year</th>
                                <th>Reason for Leaving</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" class="table-input hist-company"></td>
                                <td><input type="text" class="table-input hist-pos"></td>
                                <td><input type="text" class="table-input hist-year"></td>
                                <td><input type="text" class="table-input hist-reason"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="table-input hist-company"></td>
                                <td><input type="text" class="table-input hist-pos"></td>
                                <td><input type="text" class="table-input hist-year"></td>
                                <td><input type="text" class="table-input hist-reason"></td>
                            </tr>
                        </tbody>
                    </table>
                    <input type="hidden" name="employment_json" id="edit_employment_json">

                    <!-- Bank Details -->
                    <div class="form-section-title">Bank Details</div>
                    <div class="grid-row">
                        <div class="grid-col">
                            <label>Account Name</label>
                            <input type="text" name="account_name" id="edit_acc_name" class="form-control">
                        </div>
                        <div class="grid-col">
                            <label>Bank & Branch</label>
                            <input type="text" name="bank_branch" id="edit_bank_br" class="form-control">
                        </div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-col">
                            <label>Account Number</label>
                            <input type="text" name="account_number" id="edit_acc_num" class="form-control">
                        </div>
                        <div class="grid-col">
                            <label>Account Type & IFSC</label>
                            <input type="text" name="account_type_ifsc" id="edit_acc_ifsc" class="form-control">
                        </div>
                    </div>

                    <!-- Salary Structure -->
                    <div class="form-section-title">Professional & Salary</div>
                    <div class="grid-row">
                        <div class="grid-col">
                            <label>Join Date *</label>
                            <input type="date" name="joinDate" id="edit_join" class="form-control" required>
                        </div>
                        <div class="grid-col">
                            <label>Basic Pay *</label>
                            <input type="number" id="edit_basic" name="basic_salary" class="form-control" required>
                        </div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-col">
                            <label>HRA</label>
                            <input type="number" id="edit_hra" name="hra" class="form-control">
                        </div>
                        <div class="grid-col">
                            <label>Allowance</label>
                            <input type="number" id="edit_allowance" name="allowance" class="form-control">
                        </div>
                        <div class="grid-col">
                            <label>Deductions</label>
                            <input type="number" id="edit_deductions" name="deductions" class="form-control">
                        </div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-col" style="grid-column: span 3;">
                            <label>Total Salary (Auto Calculated)</label>
                            <input type="text" id="edit_salary" name="salary" class="form-control" readonly style="background: #f1f5f9; font-weight: 700;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="update" class="btn btn-primary" onclick="serializeEditTables()" style="padding: 10px 25px; font-weight: 700; border-radius: 8px;">
                        <i class="fa fa-save"></i> Save Changes
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="padding: 10px 20px;">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function serializeEditTables() {
        const edu = [];
        document.querySelectorAll('#edit_edu_table tbody tr').forEach(tr => {
            const row = {
                degree: tr.querySelector('.edu-degree').value,
                univ: tr.querySelector('.edu-univ').value,
                year: tr.querySelector('.edu-year').value,
                grade: tr.querySelector('.edu-grade').value,
                city: tr.querySelector('.edu-city').value
            };
            if (row.degree || row.univ) edu.push(row);
        });
        document.getElementById('edit_education_json').value = JSON.stringify(edu);

        const hist = [];
        document.querySelectorAll('#edit_emp_hist_table tbody tr').forEach(tr => {
            const row = {
                company: tr.querySelector('.hist-company').value,
                pos: tr.querySelector('.hist-pos').value,
                year: tr.querySelector('.hist-year').value,
                reason: tr.querySelector('.hist-reason').value
            };
            if (row.company || row.pos) hist.push(row);
        });
        document.getElementById('edit_employment_json').value = JSON.stringify(hist);
    }

    function openEditEmployee(btn) {
        const d = btn.dataset;
        document.getElementById('edit_id').value = d.id;
        document.getElementById('edit_name').value = d.name;
        document.getElementById('edit_phone').value = d.phone;
        document.getElementById('edit_email').value = d.email;
        document.getElementById('edit_address').value = d.address;
        document.getElementById('edit_join').value = d.join;
        document.getElementById('edit_basic').value = d.basic;
        document.getElementById('edit_hra').value = d.hra;
        document.getElementById('edit_allowance').value = d.allowance;
        document.getElementById('edit_deductions').value = d.deductions;
        document.getElementById('edit_salary').value = d.salary;
        document.getElementById('edit_preview').src = d.img || 'admin_images/default.png';

        document.getElementById('edit_offer_letter').value = d.offer_letter || '';
        document.getElementById('edit_NDA').value = d.NDA || '';
        document.getElementById('edit_Aadhar_card').value = d.Aadhar_card || '';
        document.getElementById('edit_Pan_card').value = d.Pan_card || '';
        document.getElementById('edit_passportsize_photo').value = d.passportsize_photo || '';
        document.getElementById('edit_old_company_salary_slip').value = d.old_company_salary_slip || '';
        
        // Populate fields
        document.getElementById('edit_age').value = d.age || '';
        document.getElementById('edit_dob').value = (d.dob && d.dob !== '0000-00-00') ? d.dob : '';
        document.getElementById('edit_join').value = (d.join && d.join !== '0000-00-00') ? d.join : '';
        document.getElementById('edit_work_exp').value = d.work_exp || '';
        document.getElementById('edit_dependents').value = d.dependents || 0;
        document.getElementById('edit_e_name').value = d.e_name || '';
        document.getElementById('edit_e_rel').value = d.e_rel || '';
        document.getElementById('edit_e_addr').value = d.e_addr || '';
        document.getElementById('edit_e_phone').value = d.e_phone || '';
        document.getElementById('edit_acc_name').value = d.acc_name || '';
        document.getElementById('edit_bank_br').value = d.bank_br || '';
        document.getElementById('edit_acc_num').value = d.acc_num || '';
        document.getElementById('edit_acc_ifsc').value = d.acc_ifsc || '';

        // Documents handling in Edit Modal
        const docs = [
            { id: 'view_edit_offer_letter', file: d.offer_latter },
            { id: 'view_edit_NDA', file: d.nda },
            { id: 'view_edit_Aadhar_card', file: d.aadhar },
            { id: 'view_edit_Pan_card', file: d.pan },
            { id: 'view_edit_passportsize_photo', file: d.photo },
            { id: 'view_edit_old_company_salary_slip', file: d.salary_slip }
        ];

        docs.forEach(doc => {
            const container = document.getElementById(doc.id);
            if (container) {
                if (doc.file) {
                    container.innerHTML = `<div style="display:flex; align-items:center; gap:10px; background:#f1f5f9; padding:5px 10px; border-radius:6px; margin-top:5px;">
                        <i class="fa fa-file-text-o text-primary"></i>
                        <span style="font-size:12px; color:#475569; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:150px;">${doc.file}</span>
                        <a href="uploads/${doc.file}" target="_blank" class="btn btn-xs btn-default" style="margin-left:auto;"><i class="fa fa-eye"></i> View</a>
                    </div>`;
                } else {
                    container.innerHTML = '';
                }
            }
        });

        // Radio & Select
        const mVal = (d.marital || '').trim();
        if (mVal === 'Single') document.getElementById('edit_marital_single').checked = true;
        else if (mVal === 'Married') document.getElementById('edit_marital_married').checked = true;

        const gEl = document.getElementById('edit_gender');
        if (gEl) gEl.value = (d.gender || '').trim();

        const bEl = document.getElementById('edit_blood');
        if (bEl) bEl.value = (d.blood || '').trim();

        // Populate Tables
        try {
            const eduData = JSON.parse(d.edu || '[]');
            const eduRows = document.querySelectorAll('#edit_edu_table tbody tr');
            eduData.forEach((row, index) => {
                if (eduRows[index]) {
                    eduRows[index].querySelector('.edu-degree').value = row.degree || '';
                    eduRows[index].querySelector('.edu-univ').value = row.univ || '';
                    eduRows[index].querySelector('.edu-year').value = row.year || '';
                    eduRows[index].querySelector('.edu-grade').value = row.grade || '';
                    eduRows[index].querySelector('.edu-city').value = row.city || '';
                }
            });
        } catch (e) {
            console.error("Error parsing education JSON", e);
        }

        try {
            const histData = JSON.parse(d.emp_hist || '[]');
            const histRows = document.querySelectorAll('#edit_emp_hist_table tbody tr');
            histData.forEach((row, index) => {
                if (histRows[index]) {
                    histRows[index].querySelector('.hist-company').value = row.company || '';
                    histRows[index].querySelector('.hist-pos').value = row.pos || '';
                    histRows[index].querySelector('.hist-year').value = row.year || '';
                    histRows[index].querySelector('.hist-reason').value = row.reason || '';
                }
            });
        } catch (e) {
            console.error("Error parsing employment history JSON", e);
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        function calculateEditSalary() {
            let basic = parseFloat(document.getElementById('edit_basic')?.value) || 0;
            let hra = parseFloat(document.getElementById('edit_hra')?.value) || 0;
            let allowance = parseFloat(document.getElementById('edit_allowance')?.value) || 0;
            let deduction = parseFloat(document.getElementById('edit_deductions')?.value) || 0;
            document.getElementById('edit_salary').value = (basic + hra + allowance - deduction).toFixed(2);
        }
        ['edit_basic', 'edit_hra', 'edit_allowance', 'edit_deductions'].forEach(id => {
            document.getElementById(id)?.addEventListener('input', calculateEditSalary);
        });

        // Add auto-age listeners
        document.getElementById('add_dob')?.addEventListener('change', function() { autoCalculateAge(this, 'add_age'); });
        document.getElementById('add_dob')?.addEventListener('input', function() { autoCalculateAge(this, 'add_age'); });
        document.getElementById('edit_dob')?.addEventListener('change', function() { autoCalculateAge(this, 'edit_age'); });
        document.getElementById('edit_dob')?.addEventListener('input', function() { autoCalculateAge(this, 'edit_age'); });
    });

    function viewImage(src, name) {
        document.getElementById('viewer_img').src = src;
        document.getElementById('viewer_name').textContent = name;
        $('#imageViewerModal').modal('show');
    }

    function handleImagePreview(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function autoCalculateAge(dobInput, ageInputId) {
        if (!dobInput || !dobInput.value) return;
        const dobValue = dobInput.value;
        if (dobValue === '0000-00-00') return;

        const birthDate = new Date(dobValue);
        const today = new Date();
        
        if (isNaN(birthDate.getTime())) return;

        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        
        const ageField = document.getElementById(ageInputId);
        if (ageField) {
            ageField.value = age > 0 ? age : 0;
            // Force display update
            ageField.setAttribute('value', age > 0 ? age : 0);
        }
    }
</script>
