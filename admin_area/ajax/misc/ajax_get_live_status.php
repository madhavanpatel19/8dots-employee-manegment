<?php
if (!isset($con)) { include(__DIR__ . '/../../includes/db.php'); }

if (session_status() == PHP_SESSION_NONE) {
    if (session_status() == PHP_SESSION_NONE) { session_start(); }
}

if (!isset($_SESSION['admin_email'])) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

$today = date('Y-m-d');
$response = [];

$q = "SELECT emp_id, is_working, last_resume_time, total_duration_secs, status 
      FROM attendance 
      WHERE attendance_date = '$today'";
$res = mysqli_query($con, $q);

while($row = mysqli_fetch_assoc($res)) {
    $response[$row['emp_id']] = [
        'is_working' => (int)$row['is_working'],
        'last_resume' => $row['last_resume_time'] ? date('Y-m-d\TH:i:s', strtotime($row['last_resume_time'])) : '',
        'total_secs' => (int)$row['total_duration_secs'],
        'status' => $row['status']
    ];
}

echo json_encode($response);
?>
