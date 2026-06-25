<?php
session_start();
if (!isset($con)) { include(__DIR__ . '/../../includes/db.php'); }

header('Content-Type: application/json');

if (!isset($_SESSION['admin_email'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;

if ($project_id == 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid project']);
    exit();
}

$get_project = "SELECT assigned_employees FROM client_projects WHERE id = $project_id";
$run_project = mysqli_query($con, $get_project);
if ($run_project && mysqli_num_rows($run_project) > 0) {
    $project = mysqli_fetch_assoc($run_project);
    $assigned_ids = array_filter(explode(',', $project['assigned_employees']), function($id) {
        return !empty(trim($id));
    });
    
    if (!empty($assigned_ids)) {
        $ids_str = implode(',', array_map('intval', $assigned_ids));
        $get_emps = "SELECT id, name FROM emp_list WHERE id IN ($ids_str) AND status = 'Active' ORDER BY name ASC";
        $run_emps = mysqli_query($con, $get_emps);
        $employees = [];
        while ($e = mysqli_fetch_assoc($run_emps)) {
            $employees[] = $e;
        }
        echo json_encode(['success' => true, 'employees' => $employees]);
        exit();
    }
}

echo json_encode(['success' => true, 'employees' => []]);
