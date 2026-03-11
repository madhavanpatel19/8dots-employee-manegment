<?php
if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('login.php','_self')</script>";
} else {
?>

<div class="row"><!-- row 1 Starts -->
    <div class="col-lg-12"><!-- col-lg-12 Starts -->
        <ol class="breadcrumb"><!-- breadcrumb Starts -->
            <li class="active">
                <i class="fa fa-dashboard"></i> Dashboard / Announcement
            </li>
        </ol><!-- breadcrumb Ends -->
    </div><!-- col-lg-12 Ends -->
</div><!-- row 1 Ends -->

<div class="row"><!-- row 2 Starts -->
    <div class="col-lg-12"><!-- col-lg-12 Starts -->
        <div class="panel panel-default"><!-- panel panel-default Starts -->
            <div class="panel-heading"><!-- panel-heading Starts -->
                <h3 class="panel-title">
                    <i class="fa fa-bullhorn fa-fw"></i> Post New Announcement
                </h3>
            </div><!-- panel-heading Ends -->
            <div class="panel-body"><!-- panel-body Starts -->
                <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Announcement Title</label>
                        <div class="col-md-6">
                            <input type="text" name="announcement_title" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Message</label>
                        <div class="col-md-6">
                            <textarea name="announcement_message" class="form-control" rows="6" required></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-6">
                            <input type="submit" name="submit" value="Post Announcement" class="btn btn-primary form-control">
                        </div>
                    </div>
                </form>

                <?php
                if (isset($_POST['submit'])) {
                    $announcement_title = mysqli_real_escape_string($con, $_POST['announcement_title']);
                    $announcement_message = mysqli_real_escape_string($con, $_POST['announcement_message']);

                    $insert_announcement = "INSERT INTO announcements (title, message) VALUES ('$announcement_title', '$announcement_message')";
                    $run_announcement = mysqli_query($con, $insert_announcement);

                    if ($run_announcement) {
                        echo "<script>alert('Announcement has been posted successfully')</script>";
                        echo "<script>window.open('index.php?announcement','_self')</script>";
                    }
                }
                ?>
            </div><!-- panel-body Ends -->
        </div><!-- panel panel-default Ends -->
    </div><!-- col-lg-12 Ends -->
</div><!-- row 2 Ends -->

<div class="row"><!-- row 3 Starts -->
    <div class="col-lg-12"><!-- col-lg-12 Starts -->
        <div class="panel panel-default"><!-- panel panel-default Starts -->
            <div class="panel-heading"><!-- panel-heading Starts -->
                <h3 class="panel-title">
                    <i class="fa fa-list fa-fw"></i> View Announcements
                </h3>
            </div><!-- panel-heading Ends -->
            <div class="panel-body"><!-- panel-body Starts -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            $get_announcements = "SELECT * FROM announcements ORDER BY created_at DESC";
                            $run_announcements = mysqli_query($con, $get_announcements);
                            while ($row_announcements = mysqli_fetch_array($run_announcements)) {
                                $announcement_id = $row_announcements['id'];
                                $announcement_title = $row_announcements['title'];
                                $announcement_message = $row_announcements['message'];
                                $announcement_date = $row_announcements['created_at'];
                                $i++;
                            ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $announcement_title; ?></td>
                                    <td><?php echo substr($announcement_message, 0, 100); ?>...</td>
                                    <td><?php echo $announcement_date; ?></td>
                                    <td>
                                        <a href="index.php?announcement&delete_announcement=<?php echo $announcement_id; ?>" onclick="return confirm('Are you sure you want to delete this announcement?')">
                                            <i class="fa fa-trash-o"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <?php
                if (isset($_GET['delete_announcement'])) {
                    $delete_id = $_GET['delete_announcement'];
                    $delete_query = "DELETE FROM announcements WHERE id='$delete_id'";
                    $run_delete = mysqli_query($con, $delete_query);

                    if ($run_delete) {
                        echo "<script>alert('Announcement has been deleted successfully')</script>";
                        echo "<script>window.open('index.php?announcement','_self')</script>";
                    }
                }
                ?>
            </div><!-- panel-body Ends -->
        </div><!-- panel panel-default Ends -->
    </div><!-- col-lg-12 Ends -->
</div><!-- row 3 Ends -->

<?php } ?>
