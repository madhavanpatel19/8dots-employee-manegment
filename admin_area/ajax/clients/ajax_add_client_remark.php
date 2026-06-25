<?php
header('Content-Type: application/json');
if (!isset($con)) { include(__DIR__ . '/../../includes/db.php'); }

$response = ['success' => false];

if (isset($_POST['project_id']) && isset($_POST['remark'])) {
    $project_id = intval($_POST['project_id']);
    $remark = mysqli_real_escape_string($con, $_POST['remark']);

    if (!empty($remark)) {
        $insert = "INSERT INTO client_project_remarks (project_id, remark) VALUES ($project_id, '$remark')";
        if (mysqli_query($con, $insert)) {
            $response['success'] = true;
        }
    }
}

echo json_encode($response);
?>
