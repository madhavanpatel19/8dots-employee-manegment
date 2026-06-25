<?php
if (!isset($con)) {
    include(__DIR__ . '/../../includes/db.php');
}

$response = array('status' => 'error', 'message' => 'Unknown error');

if (isset($_POST['industry_id'])) {
    $industry_id = intval($_POST['industry_id']);
    
    $delete = mysqli_query($con, "DELETE FROM client_industries WHERE id = $industry_id");
    
    if ($delete) {
        $response = array('status' => 'success');
    } else {
        $response = array('status' => 'error', 'message' => mysqli_error($con));
    }
}

echo json_encode($response);
?>
