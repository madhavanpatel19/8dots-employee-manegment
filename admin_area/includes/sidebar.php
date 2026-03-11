<?php
if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('login.php','_self')</script>";
} else {
    if (!function_exists('canAdminAccess')) {
        include(__DIR__ . '/admin_permissions.php');
    }
    if (!isset($admin_name) && isset($con) && !empty($_SESSION['admin_email'])) {
        $email = mysqli_real_escape_string($con, $_SESSION['admin_email']);
        $res = @mysqli_query($con, "SELECT admin_id, admin_name FROM admins WHERE admin_email='$email' LIMIT 1");
        if ($res && $row = mysqli_fetch_assoc($res)) {
            $admin_id = isset($admin_id) ? $admin_id : $row['admin_id'];
            $admin_name = $row['admin_name'];
        } else {
            $admin_name = $_SESSION['admin_email'];
        }
    }
    $header_display_name = isset($admin_name) && $admin_name !== '' ? htmlspecialchars($admin_name) : htmlspecialchars($_SESSION['admin_email']);

    // Count pending leave applications
    $count_leave_query = "SELECT count(*) AS total FROM leave_applications WHERE status='pending'";
    $run_count_leave = mysqli_query($con, $count_leave_query);
    $row_count_leave = mysqli_fetch_array($run_count_leave);
    $pending_leave_count = $row_count_leave['total'];
?>
    <nav class="navbar navbar-inverse navbar-fixed-top"><!-- navbar navbar-inverse navbar-fixed-top Starts -->
        <div class="navbar-header"><!-- navbar-header Starts -->
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse"><!-- navbar-ex1-collapse Starts -->
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button><!-- navbar-ex1-collapse Ends -->
            <a class="navbar-brand" href="index.php?dashboard">8dots</a>
        </div><!-- navbar-header Ends -->
        <ul class="nav navbar-right top-nav"><!-- nav navbar-right top-nav Starts -->
            <li class="dropdown"><!-- notification dropdown Starts -->
                <a href="index.php?view_leave_requests" class="dropdown-toggle">
                    <i class="fa fa-bell"></i>
                    <?php if ($pending_leave_count > 0) : ?>
                        <span class="label label-danger" style="position: absolute; top: 10px; right: 5px; border-radius: 50%; padding: 2px 5px; font-size: 10px;"><?php echo $pending_leave_count; ?></span>
                    <?php endif; ?>
                </a>
            </li><!-- notification dropdown Ends -->
            <li class="dropdown"><!-- dropdown Starts -->
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-user"></i> <?php echo $header_display_name; ?>
                </a>
                <ul class="dropdown-menu"><!-- dropdown-menu Starts -->
                    <?php if (canAdminAccess('user_view')): ?>
                        <li><!-- li Starts -->
                            <a href="index.php?user_profile=<?php echo $admin_id; ?>">
                                <i class="fa fa-fw fa-user"></i> Profile
                            </a>
                        </li>
                        <li><!-- li Starts -->
                            <a href="index.php?view_users">
                                <i class="fa fa-fw fa-users"></i> Users
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="divider"></li>
                    <li><!-- li Starts -->
                        <a href="logout.php">
                            <i class="fa fa-fw fa-power-off"> </i> Log Out
                        </a>
                    </li><!-- li Ends -->
                </ul><!-- dropdown-menu Ends -->
            </li><!-- dropdown Ends -->
        </ul><!-- nav navbar-right top-nav Ends -->
        <div class="collapse navbar-collapse navbar-ex1-collapse"><!-- collapse navbar-collapse navbar-ex1-collapse Starts -->
            <ul class="nav navbar-nav side-nav"><!-- nav navbar-nav side-nav Starts -->
                <li><!-- li Starts -->
                    <a href="index.php?dashboard">
                        <i class="fa fa-fw fa-dashboard"></i> Dashboard
                    </a>
                </li><!-- li Ends -->
                <?php if (canAdminAccess('employee_view') || canAdminAccess('attendance_view') || canAdminAccess('salary_view')): ?>
                    <li><!-- li Starts -->
                        <a href="#" data-toggle="collapse" data-target="#employees">
                            <i class="fa fa-fw fa-users"></i> Employees
                            <i class="fa fa-fw fa-caret-down"></i>
                        </a>
                        <ul id="employees" class="collapse">
                            <?php if (canAdminAccess('employee_view')): ?><li><a href="index.php?emp_directory"> View Employees </a></li><?php endif; ?>
                            <?php if (canAdminAccess('attendance_view')): ?><li><a href="attendance.php"> Attendance </a></li><?php endif; ?>
                            <?php if (canAdminAccess('leave_view')): ?><li><a href="index.php?view_leave_requests"> Leave Requests </a></li><?php endif; ?>
                            <?php if (canAdminAccess('salary_view')): ?><li><a href="index.php?salary_slip"> Salary Slip </a></li><?php endif; ?>
                        </ul>
                    </li><!-- li Ends -->
                <?php endif; ?>
                <?php if (canAdminAccess('user_view') || canAdminAccess('user_update') || canAdminAccess('user_insert')): ?>
                    <li><!-- li Starts -->
                        <a href="#" data-toggle="collapse" data-target="#users">
                            <i class="fa fa-fw fa-gear"></i> Users
                            <i class="fa fa-fw fa-caret-down"></i>
                        </a>
                        <ul id="users" class="collapse">
                            <?php if (canAdminAccess('user_insert')): ?><li><a href="index.php?insert_user"> Insert User </a></li><?php endif; ?>
                            <?php if (canAdminAccess('user_view')): ?><li><a href="index.php?view_users"> View Users </a></li><?php endif; ?>
                            <?php if (canAdminAccess('user_view')): ?><li><a href="index.php?user_profile=<?php echo $admin_id; ?>"> Edit Profile </a></li><?php endif; ?>
                        </ul>
                    </li><!-- li Ends -->
                <?php endif; ?>
                <?php if (canAdminAccess('worksheet_view')): ?>
                <li><!-- li Starts -->
                    <a href="index.php?worksheettable">
                        <i class="fa fa-fw fa-table"></i>Worksheet
                    </a>
                </li><!-- li Ends -->
                <?php endif; ?>
                <?php if(canAdminAccess('announcement_view')): ?>
                    <li><!-- li Starts -->
                        <a href="index.php?announcement">
                            <i class="fa fa-fw fa-bullhorn"></i>Announcement
                        </a>
                    </li><!-- li Ends -->
                <?php endif; ?>
                <li><!-- li Starts -->
                    <a href="logout.php">
                        <i class="fa fa-fw fa-power-off"></i> Log Out
                    </a>
                </li><!-- li Ends -->

            </ul><!-- nav navbar-nav side-nav Ends -->
        </div><!-- collapse navbar-collapse navbar-ex1-collapse Ends -->
    </nav><!-- navbar navbar-inverse navbar-fixed-top Ends -->
<?php } ?>