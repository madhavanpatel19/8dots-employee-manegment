<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($con)) {
    include(__DIR__ . '/../../includes/db.php');
}

header('Content-Type: application/json');

if (!isset($_SESSION['admin_email'])) {
    echo json_encode(['status' => 'error', 'message' => 'SESSION ERROR']);
    exit;
}

include(__DIR__ . '/../../includes/admin_permissions.php');
requireAdminPermission('employee_delete');

if (isset($_POST['id']) || isset($_GET['id'])) {

    $raw_id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
    $id = intval($raw_id);

    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'INVALID ID']);
        exit;
    }

    $now = date('Y-m-d H:i:s');

    /* SOFT DELETE CHILD RECORDS (employee_documents) */
    mysqli_query($con, "UPDATE employee_documents SET deleted_at = '$now' WHERE emp_id = $id AND deleted_at IS NULL");

    /* SOFT DELETE EMPLOYEE */
    $query = "UPDATE emp_list SET deleted_at = '$now' WHERE id = $id AND deleted_at IS NULL";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_affected_rows($con) > 0) {
        echo json_encode(['status' => 'success']);
        exit;
    } elseif ($result) {
        echo json_encode(['status' => 'error', 'message' => 'Employee not found or already deleted']);
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'DELETE ERROR: ' . mysqli_error($con)
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID NOT FOUND']);
}
