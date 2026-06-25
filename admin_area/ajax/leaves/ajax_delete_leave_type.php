<?php
header('Content-Type: application/json');
if (!isset($con)) { include(__DIR__ . '/../../includes/db.php'); }

$response = ['success' => false, 'message' => 'Invalid request'];

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $delete = "DELETE FROM leave_types WHERE id = $id";
    if (mysqli_query($con, $delete)) {
        $response['success'] = true;
        $response['message'] = 'Leave type deleted successfully';
    } else {
        $response['message'] = 'Database error: ' . mysqli_error($con);
    }
}

echo json_encode($response);
?>
