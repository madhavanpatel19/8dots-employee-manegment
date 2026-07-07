<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include(__DIR__ . '/../admin_area/includes/db.php');
if (!isset($_SESSION['emp_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Authentication required']);
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$is_pinned = isset($_POST['is_pinned']) ? (int)$_POST['is_pinned'] : 0;

if ($id > 0) {
    $update = "UPDATE company_links SET is_pinned = $is_pinned WHERE id = $id";
    if (mysqli_query($con, $update)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($con)]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
}
