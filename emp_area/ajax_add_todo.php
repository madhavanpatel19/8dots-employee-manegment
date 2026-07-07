<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
include(__DIR__ . '/../admin_area/includes/db.php');
if (!isset($_SESSION['emp_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Authentication required']);
    exit;
}

$emp_id = (int)$_SESSION['emp_id'];
$task_name = isset($_POST['task_name']) ? mysqli_real_escape_string($con, $_POST['task_name']) : '';
$due_date = isset($_POST['due_date']) ? mysqli_real_escape_string($con, $_POST['due_date']) : null;
$priority = isset($_POST['priority']) ? mysqli_real_escape_string($con, $_POST['priority']) : 'Medium';

if (empty($task_name)) {
    echo json_encode(['status' => 'error', 'message' => 'Task name is required']);
    exit;
}

if (empty($due_date)) {
    $due_date_sql = "NULL";
} else {
    $due_date_sql = "'$due_date'";
}

$query = "INSERT INTO project_team_todos (project_id, emp_id, task_name, due_date, priority, status, created_at) 
          VALUES (0, $emp_id, '$task_name', $due_date_sql, '$priority', 0, NOW())";

if (mysqli_query($con, $query)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($con)]);
}
?>
