<?php
header('Content-Type: application/json');
if (!isset($con)) { include(__DIR__ . '/../../includes/db.php'); }

$response = ['success' => false, 'message' => 'Project not found'];

if (isset($_GET['project_id'])) {
    $project_id = intval($_GET['project_id']);
    $get_project = "SELECT * FROM client_projects WHERE id = $project_id";
    $run_project = mysqli_query($con, $get_project);

    if ($run_project && mysqli_num_rows($run_project) > 0) {
        $project_data = mysqli_fetch_assoc($run_project);
        $response['success'] = true;
        $response['data'] = $project_data;
    } else {
        $response['message'] = 'Database error: ' . mysqli_error($con);
    }
}

echo json_encode($response);
?>
