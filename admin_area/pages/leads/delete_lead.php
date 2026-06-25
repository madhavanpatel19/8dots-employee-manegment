<?php
if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('../../pages/auth/login.php','_self')</script>";
    exit;
}

if (isset($_GET['delete_lead'])) {
    $delete_id = mysqli_real_escape_string($con, $_GET['delete_lead']);
    
    // First delete follow-ups
    mysqli_query($con, "DELETE FROM lead_followups WHERE lead_id = '$delete_id'");
    
    // Then delete lead
    $delete_lead = "DELETE FROM leads WHERE id = '$delete_id'";
    $run_delete = mysqli_query($con, $delete_lead);
    
    if ($run_delete) {
        echo "<script>window.open('index.php?leads','_self');</script>";
    } else {
        $err = mysqli_real_escape_string($con, mysqli_error($con));
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Error!',
                    text: '$err',
                    icon: 'error',
                    confirmButtonColor: '#ef4444',
                    customClass: { popup: 'premium-card' }
                }).then(() => {
                    window.location.href = 'index.php?leads';
                });
            });
        </script>";
    }
}
?>
