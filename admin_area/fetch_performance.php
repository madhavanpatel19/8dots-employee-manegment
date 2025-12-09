<?php
header('Content-Type: application/json');
include 'connection.php';

$currentYear = (int)date('Y');
$currentMonth = (int)date('m');

$empId = isset($_GET['emp_id']) ? (int)$_GET['emp_id'] : 0;
$year  = isset($_GET['year']) ? (int)$_GET['year'] : $currentYear;
$month = isset($_GET['month']) ? (int)$_GET['month'] : $currentMonth;

if ($empId <= 0 || $month < 1 || $month > 12 || $year < 2000) {
    echo json_encode(array('success' => false, 'message' => 'Invalid parameters.'));
    exit;
}

$perfRow = null;
$perfSql = "SELECT absent, late, task_sheet, performance_score, dressing_behaviour, rnd, total 
            FROM emp_performance 
            WHERE emp_id='$empId' AND perf_year='$year' AND perf_month='$month' LIMIT 1";
$perfRes = mysqli_query($con, $perfSql);
if ($perfRes && mysqli_num_rows($perfRes) === 1) {
    $perfRow = mysqli_fetch_assoc($perfRes);
}

// Defaults from attendance for the selected month/year
$absencePoints = 0;
$latePoints = 0;
$attendanceTable = mysqli_query($con, "SHOW TABLES LIKE 'attendance'");

if ($attendanceTable && mysqli_num_rows($attendanceTable) > 0) {
    $absSql = "SELECT SUM(CASE WHEN status='absent' THEN 1 ELSE 0 END) AS absences
               FROM attendance
               WHERE emp_id='$empId' AND MONTH(attendance_date)='$month' AND YEAR(attendance_date)='$year'";
    $absRes = mysqli_query($con, $absSql);
    if ($absRes) {
        $absData = mysqli_fetch_assoc($absRes);
        $absences = (int)$absData['absences'];
        $absencePoints = ($absences <= 3) ? 10 : 0;
    }

    $lateSql = "SELECT SUM(
                    CASE 
                        WHEN status='present' AND check_in_time IS NOT NULL AND check_in_time > '10:15:00' THEN 1
                        WHEN status='late' THEN 1
                        WHEN remarks LIKE '%late%' THEN 1
                        ELSE 0
                    END
                ) AS lates
                FROM attendance
                WHERE emp_id='$empId' AND MONTH(attendance_date)='$month' AND YEAR(attendance_date)='$year'";
    $lateRes = mysqli_query($con, $lateSql);
    if ($lateRes) {
        $lateData = mysqli_fetch_assoc($lateRes);
        $lateCount = (int)$lateData['lates'];
        $latePoints = 10;
        if ($lateCount >= 4 && $lateCount <= 6) $latePoints = 5;
        if ($lateCount > 6) $latePoints = 0;
    }
}

$data = array(
    'absent' => $perfRow ? (int)$perfRow['absent'] : $absencePoints,
    'late' => $perfRow ? (int)$perfRow['late'] : $latePoints,
    'task_sheet' => $perfRow ? (int)$perfRow['task_sheet'] : 0,
    'performance_score' => $perfRow ? (int)$perfRow['performance_score'] : 0,
    'dressing_behaviour' => $perfRow ? (int)$perfRow['dressing_behaviour'] : 0,
    'rnd' => $perfRow ? (int)$perfRow['rnd'] : 0,
    'total' => $perfRow ? (int)$perfRow['total'] : 0,
    'year' => $year,
    'month' => $month,
    'existing' => $perfRow ? true : false
);

if (!$perfRow) {
    $data['total'] = $data['absent'] + $data['late'] + $data['task_sheet'] + $data['performance_score'] + $data['dressing_behaviour'] + $data['rnd'];
}

echo json_encode(array('success' => true, 'data' => $data));
