<?php
if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('login.php','_self')</script>";
} else {
?>
<?php
require_once __DIR__ . '/includes/firebase_sync.php';
$db = firebase_db();
?>
<?php
    if (isset($_GET['user_delete'])) {
        $delete_id = $_GET['user_delete'];
        $delete_user = "delete from admins where admin_id='$delete_id'";
        $run_delete = mysqli_query($con, $delete_user);
        if ($run_delete) {
            try {
                firebase_delete_row($db, 'admins', (string)$delete_id);
            } catch (Throwable $e) {
                // still redirect to UI
            }
            echo "<script>alert('One User Has Been Deleted')</script>";
            echo "<script>window.open('index.php?view_users','_self')</script>";
        }
    }
?>
<?php } ?>
