<?php
session_start();
include 'connection.php';
require_once __DIR__ . '/includes/firebase_sync.php';

$db = firebase_db();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    
    $query = "DELETE FROM emp_list WHERE id = '$id'";
    $result = mysqli_query($con, $query);
    
    if ($result) {
        try {
            firebase_delete_row($db, 'emp_list', (string)$id);
        } catch (Throwable $e) {
            // still report success to the UI
        }
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "error";
}
?>
