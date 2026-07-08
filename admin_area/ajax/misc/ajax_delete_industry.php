<?php
if (!isset($con)) { include(__DIR__ . '/../../includes/db.php'); }

$response = array('status' => 'error', 'message' => 'Unknown error');

if (isset($_POST['industry_id'])) {
    $industry_id = intval($_POST['industry_id']);
    $now         = date('Y-m-d H:i:s');

    $update = mysqli_query($con, "UPDATE client_industries SET deleted_at = '$now' WHERE id = $industry_id AND deleted_at IS NULL");

    if ($update) {
        $response = array('status' => 'success');
    } else {
        $response = array('status' => 'error', 'message' => mysqli_error($con));
    }
}

echo json_encode($response);
?>
