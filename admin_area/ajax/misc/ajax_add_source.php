<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($con)) { include(__DIR__ . '/../../includes/db.php'); }

if (!isset($_SESSION['admin_email'])) {
    exit;
}

if (isset($_POST['source_name'])) {
    $source_name = mysqli_real_escape_string($con, $_POST['source_name']);
    
    // Check if exists
    $check = mysqli_query($con, "SELECT id FROM lead_sources WHERE source_name = '$source_name'");
    if (mysqli_num_rows($check) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Source already exists']);
        exit;
    }
    
    $insert = mysqli_query($con, "INSERT INTO lead_sources (source_name) VALUES ('$source_name')");
    if ($insert) {
        $new_id = mysqli_insert_id($con);
        echo json_encode(['status' => 'success', 'id' => $new_id, 'name' => $source_name]);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($con)]);
    }
}
?>
