<?php
session_start();
include 'connection.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    
    $query = "DELETE FROM emp_list WHERE id = '$id'";
    $result = mysqli_query($con, $query);
    
    if ($result) {
        
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "error";
}
?>
