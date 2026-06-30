<?php
if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('../../pages/auth/login.php','_self')</script>";
} else {
?>
<?php
    if (isset($_GET['customer_delete'])) {
        $delete_id = $_GET['customer_delete'];
        $delete_customer = "delete from customers where customer_id='$delete_id'";
        $run_delete = mysqli_query($con, $delete_customer);
        if ($run_delete) {
            echo "<script>Swal.fire({title: 'Notification', text: 'Customer Has Been Deleted', icon: 'info'});</script>";
            echo "<script>window.open('../../index.php?view_customers','_self')</script>";
        }
    }
?>
<?php } ?>