<?php
if (!isset($_SESSION['emp_id'])) {
    echo "<script>window.open('../../pages/auth/emp-login.php','_self')</script>";
} else {
    $emp_id = $_SESSION['emp_id'];
    if (isset($_GET['view_announcement'])) {
        $ann_id = mysqli_real_escape_string($con, $_GET['view_announcement']);

        // Fetch announcement
        $get_ann = "SELECT * FROM announcements WHERE id='$ann_id'";
        $run_ann = mysqli_query($con, $get_ann);
        
        $row_ann = null;
        if ($run_ann) {
            $row_ann = mysqli_fetch_array($run_ann);
        }

        if ($row_ann) {
            $title = $row_ann['title'];
            $message = $row_ann['message'];
            $date = $row_ann['created_at'];

            // Mark as read
            $check_read = "SELECT * FROM announcement_read WHERE announcement_id='$ann_id' AND emp_id='$emp_id'";
            $run_check = mysqli_query($con, $check_read);
            if ($run_check && mysqli_num_rows($run_check) == 0) {
                $insert_read = "INSERT INTO announcement_read (announcement_id, emp_id) VALUES ('$ann_id', '$emp_id')";
                mysqli_query($con, $insert_read);
            }
?>

<div class="row"><!-- row 1 Starts -->
    <div class="col-lg-12"><!-- col-lg-12 Starts -->
        <ol class="breadcrumb"><!-- breadcrumb Starts -->
            <li class="active">
                <i class="fa fa-dashboard"></i> Dashboard / Announcement Details
            </li>
        </ol><!-- breadcrumb Ends -->
    </div><!-- col-lg-12 Ends -->
</div><!-- row 1 Ends -->

<div class="row"><!-- row 2 Starts -->
    <div class="col-lg-12"><!-- col-lg-12 Starts -->
        <div class="panel panel-default"><!-- panel panel-default Starts -->
            <div class="panel-heading"><!-- panel-heading Starts -->
                <h3 class="panel-title">
                    <i class="fa fa-bullhorn fa-fw"></i> <?php echo htmlspecialchars($title); ?>
                </h3>
            </div><!-- panel-heading Ends -->
            <div class="panel-body"><!-- panel-body Starts -->
                <p class="text-muted"><i class="fa fa-calendar"></i> Posted on: <?php echo $date; ?></p>
                <hr>
                <div style="font-size: 16px; line-height: 1.6; white-space: pre-wrap;">
                    <?php echo htmlspecialchars($message); ?>
                </div>
                <hr>
                <a href="emp_index.php?dashboard" class="btn btn-default">Back to Dashboard</a>
            </div><!-- panel-body Ends -->
        </div><!-- panel panel-default Ends -->
    </div><!-- col-lg-12 Ends -->
</div><!-- row 2 Ends -->

<?php
        } else {
            echo "<div class='alert alert-danger'>Announcement not found.</div>";
        }
    }
}
?>
