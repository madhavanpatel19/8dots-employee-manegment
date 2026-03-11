<?php
if (!isset($_SESSION['emp_id'])) {
    echo "<script>window.open('emp-login.php','_self')</script>";
} else {
    $emp_name = $_SESSION['emp_name'];
    $header_display_name = htmlspecialchars($emp_name);
?>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="emp_index.php?dashboard">8dots (Employee)</a>
        </div>
        <ul class="nav navbar-right top-nav">
            <?php
            $emp_id = $_SESSION['emp_id'];
            $get_unread_count = "SELECT COUNT(*) AS total FROM announcements WHERE id NOT IN (SELECT announcement_id FROM announcement_read WHERE emp_id='$emp_id')";
            $run_unread_count = mysqli_query($con, $get_unread_count);
            $row_unread_count = mysqli_fetch_array($run_unread_count);
            $unread_count = $row_unread_count['total'];
            ?>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-bell"></i>
                    <?php if ($unread_count > 0) : ?>
                        <span class="label label-danger" style="position: absolute; top: 10px; right: 5px; border-radius: 50%; padding: 2px 5px; font-size: 10px;"><?php echo $unread_count; ?></span>
                    <?php endif; ?>
                </a>
                <ul class="dropdown-menu" style="width: 300px; max-height: 400px; overflow-y: auto;">
                    <li class="header" style="padding: 10px; border-bottom: 1px solid #ddd; font-weight: bold;">Announcements</li>
                    <?php
                    $get_recent_announcements = "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5";
                    $run_recent = mysqli_query($con, $get_recent_announcements);
                    if (mysqli_num_rows($run_recent) > 0) {
                        while ($row_recent = mysqli_fetch_array($run_recent)) {
                            $ann_id = $row_recent['id'];
                            $ann_title = $row_recent['title'];
                            $ann_date = date('M d, H:i', strtotime($row_recent['created_at']));
                            
                            // Check if read
                            $check_read = "SELECT * FROM announcement_read WHERE announcement_id='$ann_id' AND emp_id='$emp_id'";
                            $run_check = mysqli_query($con, $check_read);
                            $is_unread = mysqli_num_rows($run_check) == 0;
                            $bg_style = $is_unread ? "background-color: #f9f9f9;" : "";
                            ?>
                            <li style="<?php echo $bg_style; ?>">
                                <a href="emp_index.php?view_announcement=<?php echo $ann_id; ?>" style="white-space: normal; padding: 10px;">
                                    <strong><?php echo htmlspecialchars($ann_title); ?></strong><br>
                                    <small class="text-muted"><?php echo $ann_date; ?></small>
                                    <?php if ($is_unread) : ?>
                                        <span class="label label-primary pull-right">New</span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <?php
                        }
                    } else {
                        echo "<li style='padding: 10px;'>No announcements found.</li>";
                    }
                    ?>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-user"></i> <?php echo $header_display_name; ?>
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="emp-logout.php">
                            <i class="fa fa-fw fa-power-off"> </i> Log Out
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav side-nav">
                <li>
                    <a href="emp_index.php?dashboard">
                        <i class="fa fa-fw fa-dashboard"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="emp_index.php?worksheet">
                        <i class="fa fa-fw fa-table"></i> Worksheet
                    </a>
                </li>
                <li>
                    <a href="emp_index.php?leave_application">
                        <i class="fa fa-fw fa-paper-plane"></i> Leave Application
                    </a>
                </li>
                    <li>
                            <a href="emp_index.php?emp_salary_slip">
                                <i class="fa fa-fw fa-money"></i> Salary Slip
                            </a>
                    </li>
                <li>
                    <a href="emp-logout.php">
                        <i class="fa fa-fw fa-power-off"></i> Log Out
                    </a>
                </li>
            </ul>
        </div>
    </nav>
<?php } ?>
