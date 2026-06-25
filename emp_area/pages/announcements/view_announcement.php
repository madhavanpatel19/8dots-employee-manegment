<?php
// =============================================================
// emp_area/pages/announcements/view_announcement.php
// Employee announcement detail view – partial included by index.php
// Moved from: admin_area/pages/announcements/view_announcement.php
// Path updated: redirect now points to emp_area login.
// =============================================================
if (!isset($_SESSION['emp_id'])) {
    echo "<script>window.open('../../pages/auth/login.php','_self')</script>";
} else {
    $emp_id = $_SESSION['emp_id'];
    if (isset($_GET['view_announcement'])) {
        $ann_id = mysqli_real_escape_string($con, $_GET['view_announcement']);

        $get_ann = "SELECT * FROM announcements WHERE id='$ann_id'";
        $run_ann = mysqli_query($con, $get_ann);

        $row_ann = null;
        if ($run_ann) {
            $row_ann = mysqli_fetch_array($run_ann);
        }

        if ($row_ann) {
            $title   = $row_ann['title'];
            $message = $row_ann['message'];
            $date    = $row_ann['created_at'];

            // Mark as read
            $check_read = "SELECT * FROM announcement_read WHERE announcement_id='$ann_id' AND emp_id='$emp_id'";
            $run_check  = mysqli_query($con, $check_read);
            if ($run_check && mysqli_num_rows($run_check) == 0) {
                $insert_read = "INSERT INTO announcement_read (announcement_id, emp_id) VALUES ('$ann_id', '$emp_id')";
                mysqli_query($con, $insert_read);
            }
?>

<div class="row">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li class="active">
                <i class="fa fa-dashboard"></i> Dashboard / Announcement Details
            </li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-bullhorn fa-fw"></i> <?php echo htmlspecialchars($title); ?>
                </h3>
            </div>
            <div class="panel-body">
                <p class="text-muted"><i class="fa fa-calendar"></i> Posted on: <?php echo $date; ?></p>
                <hr>
                <div style="font-size: 16px; line-height: 1.6; white-space: pre-wrap;">
                    <?php echo htmlspecialchars($message); ?>
                </div>
                <hr>
                <a href="index.php?dashboard" class="btn btn-default">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>

<?php
        } else {
            echo "<div class='alert alert-danger'>Announcement not found.</div>";
        }
    }
}
?>
