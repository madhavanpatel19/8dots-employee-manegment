<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['emp_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$task_id = isset($_POST['task_id']) ? intval($_POST['task_id']) : 0;
$emp_id = $_SESSION['emp_id'];

if ($task_id > 0) {
    $query = "UPDATE project_team_todos SET status = 1 WHERE id = $task_id AND emp_id = '$emp_id'";
    if (mysqli_query($con, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($con)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid task ID']);
}
