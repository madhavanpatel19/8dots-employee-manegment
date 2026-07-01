<?php
// =============================================================
// emp_area/pages/announcements/view_announcement.php
// Employee announcement detail view – partial included by index.php
// =============================================================
if (!isset($_SESSION['emp_id'])) {
    echo "<script>window.open('../../pages/auth/login.php','_self')</script>";
} else {
    $emp_id = $_SESSION['emp_id'];
    if (isset($_GET['view_announcement'])) {
        $ann_id = mysqli_real_escape_string($con, $_GET['view_announcement']);

        $get_ann = "SELECT * FROM announcements WHERE id='$ann_id'";
        $run_ann = mysqli_query($con, $get_ann);
        $row_ann = ($run_ann) ? mysqli_fetch_array($run_ann) : null;

        if ($row_ann) {
            $title   = $row_ann['title'];
            $message = $row_ann['message'];
            $date    = $row_ann['publish_date'] ?? $row_ann['created_at'];

            // Mark as read
            $check_read = "SELECT * FROM announcement_read WHERE announcement_id='$ann_id' AND emp_id='$emp_id'";
            $run_check  = mysqli_query($con, $check_read);
            if ($run_check && mysqli_num_rows($run_check) == 0) {
                $insert_read = "INSERT INTO announcement_read (announcement_id, emp_id) VALUES ('$ann_id', '$emp_id')";
                mysqli_query($con, $insert_read);
            }
?>

            <div class="premium-ui-enabled">
                <div class="page-header-premium" style="margin-bottom: 25px; border-bottom: none; display: flex; align-items: center; justify-content: space-between;">
                    <h1></h1>
                    <a href="index.php?announcements" class="btn-premium-cancel">
                        <i class="fa fa-arrow-left"></i> Back to Announcements
                    </a>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="premium-card" style="padding: 30px;">
                            <div style="border-bottom: 2px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 20px;">
                                <h2 style="margin: 0; font-size: 24px; font-weight: 800; color: #0f172a; margin-bottom: 10px;">
                                    <?php echo htmlspecialchars($title); ?>
                                </h2>
                                <div style="color: #64748b; font-size: 14px; font-weight: 600;">
                                    <i class="fa fa-clock-o"></i> Posted on: <?php echo date('d M Y, h:i A', strtotime($date)); ?>
                                </div>
                            </div>
                            <div style="font-size: 16px; line-height: 1.8; color: #334155; white-space: pre-wrap; font-family: 'Inter', sans-serif;">
                                <?php echo htmlspecialchars($message); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<?php
        } else {
            echo "<div class='premium-ui-enabled'><div class='premium-card' style='padding:40px;text-align:center;color:#ef4444;font-weight:600;'><i class='fa fa-exclamation-triangle' style='font-size:40px;margin-bottom:15px;display:block;'></i> Announcement not found.</div></div>";
        }
    }
}
?>