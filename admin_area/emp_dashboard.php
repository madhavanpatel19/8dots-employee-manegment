<?php
if (!isset($con) || !$con) {
    include("connection.php");
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['emp_id'])) {
    echo "<script>window.open('emp-login.php','_self')</script>";
    exit();
}

$emp_id = $_SESSION['emp_id'];
$emp_name = $_SESSION['emp_name'];
$today = date('Y-m-d');

// Fetch some employee specific stats if needed
// For now, let's just show a welcome message and today's status if any
$q = "SELECT * FROM attendance WHERE emp_id = '$emp_id' AND attendance_date = '$today'";
$res = mysqli_query($con, $q);
$today_record = mysqli_fetch_assoc($res);
?>


<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <i class="fa fa-dashboard"></i> Employee Dashboard
            <small>Welcome, <?php echo htmlspecialchars($emp_name); ?></small>
        </h1>
    </div>
</div>
<?php
// Fetch latest announcement for ticker
$ticker_q = "SELECT title FROM announcements ORDER BY created_at DESC LIMIT 1";
$ticker_res = mysqli_query($con, $ticker_q);
if ($ticker_res && mysqli_num_rows($ticker_res) > 0) {
    $ticker_row = mysqli_fetch_array($ticker_res);
    $latest_announcement = $ticker_row['title'];
?>
<div class="row">
    <div class="col-lg-12">
        <!-- Rolling Announcement Ticker -->
        <div style="background-color: #fce4ec; color: #a62047; padding: 10px 15px; margin-bottom: 25px; border-radius: 6px; border-left: 4px solid #e91e63; font-weight: bold; overflow: hidden; white-space: nowrap; display: flex; align-items: center;">
            <i class="fa fa-bullhorn" style="margin-right: 15px; font-size: 16px;"></i>
            <!-- <span style="font-size: 14px; margin-right: 10px;">LATEST ANNOUNCEMENT:</span> -->
            <marquee behavior="scroll" direction="left" scrollamount="6" style="flex-grow: 1; font-weight: normal; font-size: 15px;">
                <?php echo htmlspecialchars($latest_announcement); ?>
            </marquee>
        </div>
    </div>
</div>
<?php } ?>



<div class="row">
    <div class="col-lg-4 col-md-4">
        <div class="panel panel-primary" style="border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: none;">
            <div class="panel-heading" style="background: #3498db; border-radius: 8px 8px 0 0; padding: 20px;">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-calendar fa-4x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge" style="font-size: 36px;"><?php echo date('d'); ?></div>
                        <div style="text-transform: uppercase; font-weight: 600; font-size: 13px;"><?php echo date('M Y'); ?></div>
                    </div>
                </div>
            </div>
            <a href="emp_index.php?worksheet" style="text-decoration: none;">
                <div class="panel-footer" style="background: #fff; border-radius: 0 0 8px 8px; color: #3498db; font-weight: 600;">
                    <span class="pull-left">Fill Worksheet</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-4">
        <div class="panel panel-green" style="border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: none;">
            <div class="panel-heading" style="background: #27ae60; border-radius: 8px 8px 0 0; padding: 20px; color: #fff;">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-tasks fa-4x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div style="text-transform: uppercase; font-weight: 600; font-size: 13px;">Today's Status</div>
                        <div class="huge" style="font-size: 24px; margin-top: 10px;">
                            <?php echo $today_record ? ucfirst($today_record['status']) : "No Record"; ?>
                        </div>
                    </div>
                </div>
            </div>
            <a href="emp_index.php?worksheet" style="text-decoration: none;">
                <div class="panel-footer" style="background: #fff; border-radius: 0 0 8px 8px; color: #27ae60; font-weight: 600;">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>

    <div class="col-lg-4 col-md-4">
        <div class="panel panel-yellow" style="border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: none;">
            <div class="panel-heading" style="background: #f39c12; border-radius: 8px 8px 0 0; padding: 20px; color: #fff;">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-paper-plane fa-4x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div style="text-transform: uppercase; font-weight: 600; font-size: 13px;">Leaves</div>
                        <div class="huge" style="font-size: 24px; margin-top: 10px;">Request</div>
                    </div>
                </div>
            </div>
            <a href="emp_index.php?leave_application" style="text-decoration: none;">
                <div class="panel-footer" style="background: #fff; border-radius: 0 0 8px 8px; color: #f39c12; font-weight: 600;">
                    <span class="pull-left">Apply for Leave</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default" style="border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div class="panel-heading" style="background: #f8fafc; border-radius: 8px 8px 0 0;">
                <h3 class="panel-title" style="font-weight: 600; color: #444;"><i class="fa fa-clock-o fa-fw"></i> Recent Activity</h3>
            </div>
            <div class="panel-body">
                <div class="list-group">
                    <?php
                    $recent_q = "SELECT * FROM attendance WHERE emp_id = '$emp_id' ORDER BY attendance_date DESC LIMIT 5";
                    $recent_res = mysqli_query($con, $recent_q);
                    if (mysqli_num_rows($recent_res) > 0) {
                        while($row = mysqli_fetch_assoc($recent_res)) {
                            $status_class = ($row['status'] == 'present') ? 'label-success' : 'label-danger';
                            echo '<a href="#" class="list-group-item" style="border-left: none; border-right: none;">';
                            echo '<span style="font-size: 13px; float: left;">' . date('d M Y', strtotime($row['attendance_date'])) . '</span>';
                            echo '<span class="label ' . $status_class . ' pull-right" style="font-size: 13px;">' . strtoupper($row['status']) . '</span>';
                            echo '<span style="font-size: 13px;">' . $row['remarks'] . '</span>';
                            echo '</a>';
                        }
                    } else {
                        echo '<p class="text-center" style="padding: 20px; color: #7f8c8d;">No recent activity found.</p>';
                    }
                    ?>
                </div>
                <div class="text-right" style="padding-top: 10px;">
                    <a href="emp_index.php?worksheet" style="font-weight: 600; text-decoration: none; color: #34495e;">View All Worksheet <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
