<?php
header('Content-Type: application/json');
if (!isset($con)) { include(__DIR__ . '/../../includes/db.php'); }

$response = ['success' => false, 'message' => 'Invalid request'];

if (isset($_POST['project_id'])) {
    $project_id = intval($_POST['project_id']);

    // Delete remarks first (to clean up activity history)
    $delete_remarks = "DELETE FROM client_project_remarks WHERE project_id = $project_id";
    mysqli_query($con, $delete_remarks);

    // Delete the project
    $delete_project = "DELETE FROM client_projects WHERE id = $project_id";
    
    if (mysqli_query($con, $delete_project)) {
        $response['success'] = true;
        $response['message'] = 'Project and associated remarks deleted successfully';
    } else {
        $response['message'] = 'Database error: ' . mysqli_error($con);
    }
}

echo json_encode($response);
?>
