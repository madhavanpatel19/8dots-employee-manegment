<?php
header('Content-Type: application/json');
if (!isset($con)) { include(__DIR__ . '/../../includes/db.php'); }

$response = ['success' => false, 'message' => 'Invalid request'];

if (isset($_POST['leave_name']) && isset($_POST['num_of_leave'])) {
    $name = mysqli_real_escape_string($con, $_POST['leave_name']);
    $count = intval($_POST['num_of_leave']);

    $insert = "INSERT INTO leave_types (leave_name, num_of_leave) VALUES ('$name', '$count')";
    if (mysqli_query($con, $insert)) {
        $response['success'] = true;
        $response['message'] = 'Leave type added successfully';
    } else {
        $response['message'] = 'Database error: ' . mysqli_error($con);
    }
}

echo json_encode($response);
?>
