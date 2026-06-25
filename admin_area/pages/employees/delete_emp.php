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
    $id = mysqli_real_escape_string($con, $raw_id);

    /* DELETE CHILD RECORDS FIRST */

    mysqli_query(
        $con,
        "DELETE FROM employee_documents WHERE emp_id='$id'"
    );

    /* DELETE EMPLOYEE */

    $query =
        "DELETE FROM emp_list WHERE id='$id'";

    $result =
        mysqli_query($con, $query);

    if ($result) {
        echo json_encode(['status' => 'success']);
        exit;
    } else {

        echo json_encode([
            'status' => 'error',
            'message' => 'DELETE ERROR: ' . mysqli_error($con)
        ]);
    }
} else {

    echo json_encode(['status' => 'error', 'message' => 'ID NOT FOUND']);
}
