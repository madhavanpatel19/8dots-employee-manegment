<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($con)) { include(__DIR__ . '/../../includes/db.php'); }

if (!isset($_SESSION['admin_email'])) {
    exit;
}

if (isset($_POST['source_id'])) {
    $source_id = mysqli_real_escape_string($con, $_POST['source_id']);
    
    $delete = mysqli_query($con, "DELETE FROM lead_sources WHERE id = '$source_id'");
    if ($delete) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($con)]);
    }
}
?>
