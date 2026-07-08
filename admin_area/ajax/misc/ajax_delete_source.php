<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($con)) { include(__DIR__ . '/../../includes/db.php'); }

if (!isset($_SESSION['admin_email'])) {
    exit;
}

if (isset($_POST['source_id'])) {
    $source_id = intval($_POST['source_id']);
    $now       = date('Y-m-d H:i:s');

    $update = mysqli_query($con, "UPDATE lead_sources SET deleted_at = '$now' WHERE id = $source_id AND deleted_at IS NULL");
    if ($update) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($con)]);
    }
}
?>
