<?php
include 'connection.php';
require_once __DIR__ . '/includes/firebase_sync.php';

$db = firebase_db();

if (isset($_POST['doc_id'])) {
    $doc_id = intval($_POST['doc_id']);
    $res = mysqli_query($con, "SELECT file_name FROM employee_documents WHERE id='$doc_id'");
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $file = "uploads/" . $row['file_name'];
        if (file_exists($file)) unlink($file);
        mysqli_query($con, "DELETE FROM employee_documents WHERE id='$doc_id'");
        firebase_delete_row($db, 'employee_documents', (string)$doc_id);
        echo "success";
    } else echo "error";
}
?>
