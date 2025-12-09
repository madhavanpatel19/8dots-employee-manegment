<?php
// ---- Basic safety: DB + session + admin check ----

// Include database connection if not already included
if (!isset($con) || !$con) {
    include("includes/db.php");
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check admin login
if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('login.php','_self')</script>";
    exit();
}

// ---- Fetch admin info & some default counters once ----

if (
    !isset($count_products) || !isset($count_customers) || !isset($count_p_categories) || !isset($count_pending_orders) ||
    !isset($admin_image) || !isset($admin_name) || !isset($admin_job) || !isset($admin_email) ||
    !isset($admin_country) || !isset($admin_contact) || !isset($admin_about) || !isset($admin_id)
) {
    $admin_session = $_SESSION['admin_email'];
    $get_admin = "SELECT * FROM admins WHERE admin_email='" . mysqli_real_escape_string($con, $admin_session) . "'";
    $run_admin = mysqli_query($con, $get_admin);

    if ($run_admin && $row_admin = mysqli_fetch_array($run_admin)) {
        $admin_id      = isset($row_admin['admin_id']) ? $row_admin['admin_id'] : 0;
        $admin_image   = isset($row_admin['admin_image']) ? $row_admin['admin_image'] : '';
        $admin_name    = isset($row_admin['admin_name']) ? $row_admin['admin_name'] : '';
        $admin_job     = isset($row_admin['admin_job']) ? $row_admin['admin_job'] : '';
        $admin_email   = isset($row_admin['admin_email']) ? $row_admin['admin_email'] : '';
        $admin_country = isset($row_admin['admin_country']) ? $row_admin['admin_country'] : '';
        $admin_contact = isset($row_admin['admin_contact']) ? $row_admin['admin_contact'] : '';
        $admin_about   = isset($row_admin['admin_about']) ? $row_admin['admin_about'] : '';
    } else {
        // Fallback values
        $admin_id = 0;
        $admin_image = $admin_name = $admin_job = $admin_email =
        $admin_country = $admin_contact = $admin_about = '';
    }

    // Default 0 to avoid undefined warnings (if you use these elsewhere)
    $count_products       = 0;
    $count_customers      = 0;
    $count_p_categories   = 0;
    $count_pending_orders = 0;

    // Uncomment if you want to actually count these:
    /*
    $res = mysqli_query($con, "SELECT * FROM products");
    if ($res) $count_products = mysqli_num_rows($res);

    $res = mysqli_query($con, "SELECT * FROM client_register");
    if ($res) $count_customers = mysqli_num_rows($res);

    $res = mysqli_query($con, "SELECT * FROM product_categories");
    if ($res) $count_p_categories = mysqli_num_rows($res);

    $res = mysqli_query($con, "SELECT * FROM orders WHERE order_status='pending'");
    if ($res) $count_pending_orders = mysqli_num_rows($res);
    */
}

// ---- Employees stats (for cards) ----

// TOTAL employees
$count_employees = 0;
$emp_sql = "SELECT COUNT(*) AS total_employees FROM emp_list";
$emp_res = mysqli_query($con, $emp_sql);
if ($emp_res && mysqli_num_rows($emp_res) > 0) {
    $emp_row = mysqli_fetch_assoc($emp_res);
    $count_employees = (int)$emp_row['total_employees'];
}

// TODAY'S PRESENT employees
$today = date('Y-m-d');
$count_present_employees = 0;

$q = "
    SELECT COUNT(*) AS total_present
    FROM attendance
    WHERE attendance_date = '$today'
      AND status = 'present'
";
$res = mysqli_query($con, $q);
if ($res && mysqli_num_rows($res) > 0) {
    $row = mysqli_fetch_assoc($res);
    $count_present_employees = (int)$row['total_present'];
}
?>

<!-- Page Header -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <i class="fa fa-dashboard"></i> Dashboard
            <small>System Overview</small>
        </h1>
    </div>
</div>

<!-- Stats Cards Row -->
<div class="row">
    <!-- Present Today Card -->
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="stat-card stat-card-primary">
            <div class="stat-card-header">
                <i class="fa fa-cube"></i>
            </div>
            <div class="stat-card-body">
                <h3 class="stat-number"><?php echo $count_present_employees; ?></h3>
                <p class="stat-label">Present Today</p>
            </div>
            <div class="stat-card-footer">
                <a href="attendance.php">View All <i class="fa fa-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- All Employees Card -->
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="stat-card stat-card-success">
            <div class="stat-card-header">
                <i class="fa fa-users"></i>
            </div>
            <div class="stat-card-body">
                <h3 class="stat-number"><?php echo htmlspecialchars($count_employees); ?></h3>
                <p class="stat-label">All Employees</p>
            </div>
            <div class="stat-card-footer">
                <a href="index.php?emp_directory">View All <i class="fa fa-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Salary Slip Card -->
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="stat-card stat-card-warning">
            <div class="stat-card-header">
                <i class="fa fa-file-text-o"></i>
            </div>
            <div class="stat-card-body">
                <h3 class="stat-number"><?php echo htmlspecialchars($count_employees); ?></h3>
                <p class="stat-label">Monthly Salary Slips</p>
            </div>
            <div class="stat-card-footer">
                <a href="index.php?salary_slip=1&view_all=1">View All <i class="fa fa-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Attendance Report Card -->
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="stat-card stat-card-info">
            <div class="stat-card-header">
                <i class="fa fa-calendar-check-o"></i>
            </div>
            <div class="stat-card-body">
                <h3 class="stat-number">Reports</h3>
                <p class="stat-label">Generate Attendance</p>
            </div>
            <div class="stat-card-footer">
                <a href="attendance_report.php">Generate <i class="fa fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
    
    <!-- (Optional) add more cards here to fill the row -->
</div>


<!-- Second Row: Present Employees (left) + Admin Profile (right) -->
<div class="row" style="margin-top: 30px;">

    <!-- LEFT: Today's Present Employees table -->
    <div class="col-lg-12 col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <i class="fa fa-users"></i> Today's Employee Status
                </h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i = 0;

                        if (!$con) {
                            echo '<tr><td colspan="6" style="text-align:center;color:#999;"><i class="fa fa-warning"></i> Database connection error.</td></tr>';
                        } else {
                            // Get all employees with their today's attendance status (if any)
                            $sql_all = "
                                SELECT e.id AS emp_id, e.name AS emp_name, 
                                       a.attendance_date, a.status, a.remarks
                                FROM emp_list e
                                LEFT JOIN attendance a ON e.id = a.emp_id 
                                    AND a.attendance_date = '" . mysqli_real_escape_string($con, $today) . "'
                                ORDER BY 
                                    CASE 
                                        WHEN a.status = 'present' THEN 1
                                        WHEN a.status = 'absent' THEN 2
                                        WHEN a.status = 'leave' THEN 3
                                        ELSE 4
                                    END ASC,
                                    e.name ASC
                            ";
                            $run_all = mysqli_query($con, $sql_all);

                            if (!$run_all) {
                                echo '<tr><td colspan="6" style="text-align:center;color:#999;"><i class="fa fa-warning"></i> Error fetching employees.</td></tr>';
                            } else {
                                while ($row = mysqli_fetch_assoc($run_all)) {
                                    $i++;
                                    $emp_id   = $row['emp_id'];
                                    $emp_name = $row['emp_name'];
                                    $date     = $row['attendance_date'] ? $row['attendance_date'] : $today;
                                    $status   = $row['status'] ? ucfirst($row['status']) : 'No Record';
                                    $remarks  = !empty($row['remarks']) ? htmlspecialchars($row['remarks']) : '-';
                                    
                                    // Determine status badge color
                                    if ($row['status'] == 'present') {
                                        $badge_class = 'label-success';
                                    } elseif ($row['status'] == 'absent') {
                                        $badge_class = 'label-danger';
                                    } elseif ($row['status'] == 'leave') {
                                        $badge_class = 'label-warning';
                                    } else {
                                        $badge_class = 'label-default';
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><strong><?php echo $emp_id; ?></strong></td>
                                        <td><?php echo htmlspecialchars($emp_name); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($date)); ?></td>
                                        <td><span class="label <?php echo $badge_class; ?>"><?php echo $status; ?></span></td>
                                        <td><?php echo $remarks; ?></td>
                                    </tr>
                                    <?php
                                }

                                if ($i == 0) {
                                    echo '<tr><td colspan="6" style="text-align:center;color:#999;"><i class="fa fa-inbox"></i> No employees found.</td></tr>';
                                }
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <div style="padding: 10px 0; border-top: 1px solid #ddd; text-align: right;">
                    <a href="attendance.php?daily=1&date=<?php echo date('Y-m-d'); ?>" class="btn btn-sm btn-default">
                        Today&apos;s Attendance <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Admin Profile -->
   
</div> <!-- /row -->
