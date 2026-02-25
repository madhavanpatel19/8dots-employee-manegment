<?php
// session_start();
include 'includes/db.php';

// Only allow access if admin is logged in
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit();
}

/* ==============================
   FETCH EMPLOYEE LIST
============================== */
$empList = [];
$empQ = mysqli_query($con, "SELECT id, name FROM emp_list ORDER BY name ASC");
while ($erow = mysqli_fetch_assoc($empQ)) {
    $empList[] = $erow;
}

/* ==============================
   HANDLE FILTERS SAFELY
============================== */
$filter_emp    = isset($_GET['emp_id']) ? intval($_GET['emp_id']) : '';
$filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';
$filter_from   = isset($_GET['from']) ? trim($_GET['from']) : '';
$filter_to     = isset($_GET['to']) ? trim($_GET['to']) : '';

$where = [];

// Employee filter
if (!empty($filter_emp)) {
    $where[] = "a.emp_id = $filter_emp";
}

// Status filter
if (!empty($filter_status) && in_array($filter_status, ['present', 'absent', 'leave'])) {
    $filter_status = mysqli_real_escape_string($con, $filter_status);
    $where[] = "a.status = '$filter_status'";
}

// Date From filter
if (!empty($filter_from)) {
    $filter_from = mysqli_real_escape_string($con, $filter_from);
    $where[] = "DATE(a.attendance_date) >= '$filter_from'";
}

// Date To filter
if (!empty($filter_to)) {
    $filter_to = mysqli_real_escape_string($con, $filter_to);
    $where[] = "DATE(a.attendance_date) <= '$filter_to'";
}

$whereSql = "";
if (!empty($where)) {
    $whereSql = "WHERE " . implode(" AND ", $where);
}

/* ==============================
   MAIN QUERY
============================== */
$sql = "SELECT a.*, e.name AS emp_name 
        FROM attendance a 
        LEFT JOIN emp_list e ON a.emp_id = e.id 
        $whereSql
        ORDER BY a.attendance_date DESC, a.id DESC";

$result = mysqli_query($con, $sql);

include 'includes/header.php';
?>

<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="container" style="margin-top:40px;">
        <div class="row">
            <div class="col-lg-12">

                <h2 class="page-header">Worksheet Table</h2>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-filter"></i> Filter
                    </div>

                    <div class="panel-body">



                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Employee</th>
                                        <th>Date</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                        <th>Performance</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php if ($result && mysqli_num_rows($result) > 0): $i = 1; ?>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td><?= htmlspecialchars($row['emp_name']); ?></td>
                                                <td><?= htmlspecialchars($row['attendance_date']); ?></td>
                                                <td><?= htmlspecialchars($row['check_in_time']); ?></td>
                                                <td><?= htmlspecialchars($row['check_out_time']); ?></td>
                                                <td><?= ucfirst(htmlspecialchars($row['status'])); ?></td>
                                                <td><?= htmlspecialchars($row['remarks']); ?></td>
                                                <td><?= isset($row['performance']) ? htmlspecialchars($row['performance']) : '-'; ?></td>
                                                <td><?= htmlspecialchars($row['created_at']); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-danger">
                                                No worksheet data found.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>