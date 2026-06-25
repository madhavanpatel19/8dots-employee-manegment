<?php
session_start();
if (!isset($con)) {
    include(__DIR__ . '/../../includes/db.php');
}

header('Content-Type: application/json');

if (!isset($_SESSION['admin_email'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
$emp_id = isset($_POST['emp_id']) ? intval($_POST['emp_id']) : 0;

if ($emp_id == 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit();
}

$query = "";
if ($project_id == 0) {
    // Global view
    $query = "SELECT t.*, p.project_name FROM project_team_todos t LEFT JOIN client_projects p ON t.project_id = p.id WHERE t.emp_id = $emp_id ORDER BY t.status ASC, t.due_date ASC, t.id DESC";
} else {
    // Project specific view
    $query = "SELECT t.*, p.project_name FROM project_team_todos t JOIN client_projects p ON t.project_id = p.id WHERE t.project_id = $project_id AND t.emp_id = $emp_id ORDER BY t.status ASC, t.due_date ASC, t.id DESC";
}

$result = mysqli_query($con, $query);

$tasks = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $tasks[] = $row;
    }
}

echo json_encode(['success' => true, 'tasks' => $tasks]);
