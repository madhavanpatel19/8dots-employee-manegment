<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json');
if (!isset($con)) { include(__DIR__ . '/../../includes/db.php'); }
if (!function_exists('canAdminAccess')) {
    require_once __DIR__ . '/../../includes/admin_permissions.php';
}

$response = ['success' => false, 'message' => 'Invalid request'];

if (!isset($_SESSION['admin_email'])) {
    $response['message'] = 'Unauthorized access';
    echo json_encode($response);
    exit;
}
if (!canAdminAccess('lead_update')) {
    $response['message'] = 'Permission denied';
    echo json_encode($response);
    exit;
}

if (isset($_POST['lead_id']) && isset($_POST['status'])) {
    $lead_id = intval($_POST['lead_id']);
    $status = mysqli_real_escape_string($con, $_POST['status']);

    // Validate status
    $valid_statuses = ['active', 'future', 'expired'];
    if (!in_array($status, $valid_statuses)) {
        $response['message'] = 'Invalid status value';
        echo json_encode($response);
        exit;
    }

    $update_status = "UPDATE leads SET status = '$status' WHERE id = $lead_id";
    
    if (mysqli_query($con, $update_status)) {
        $response['success'] = true;
        $response['message'] = 'Lead status updated to ' . ucfirst($status);
    } else {
        $response['message'] = 'Database error: ' . mysqli_error($con);
    }
}

echo json_encode($response);
?>
