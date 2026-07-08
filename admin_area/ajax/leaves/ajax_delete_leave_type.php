<?php
header('Content-Type: application/json');
if (!isset($con)) { include(__DIR__ . '/../../includes/db.php'); }

$response = ['success' => false, 'message' => 'Invalid request'];

if (isset($_POST['id'])) {
    $id  = intval($_POST['id']);
    $now = date('Y-m-d H:i:s');

    $update = "UPDATE leave_types SET deleted_at = '$now' WHERE id = $id AND deleted_at IS NULL";
    if (mysqli_query($con, $update)) {
        $response['success'] = true;
        $response['message'] = 'Leave type deleted successfully';
    } else {
        $response['message'] = 'Database error: ' . mysqli_error($con);
    }
}

echo json_encode($response);
?>
