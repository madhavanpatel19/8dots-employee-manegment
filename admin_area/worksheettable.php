<?php
if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('login.php','_self')</script>";
} else {
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
?>

    <div class="row"><!-- 1 row Starts -->
        <div class="col-lg-12"><!-- col-lg-12 Starts -->
            <ol class="breadcrumb"><!-- breadcrumb Starts -->
                <li class="active">
                    <i class="fa fa-dashboard"></i> Dashboard / Worksheet Table
                </li>
            </ol><!-- breadcrumb Ends -->
        </div><!-- col-lg-12 Ends -->
    </div><!-- 1 row Ends -->

    <div class="row"><!-- 2 row Starts -->
        <div class="col-lg-12"><!-- col-lg-12 Starts -->
            <div class="panel panel-default"><!-- panel panel-default Starts -->
                <div class="panel-heading"><!-- panel-heading Starts -->
                    <h3 class="panel-title"><!-- panel-title Starts -->
                        <i class="fa fa-table fa-fw"></i> Worksheet Table
                    </h3><!-- panel-title Ends -->
                </div><!-- panel-heading Ends -->
                <div class="panel-body"><!-- panel-body Starts -->
                    <div class="table-responsive"><!-- table-responsive Starts -->
                        <table class="table table-bordered table-hover table-striped"><!-- table table-bordered table-hover table-striped Starts -->
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
                                            <td><?= date('d-m-y', strtotime($row['attendance_date'])); ?></td>
                                            <td><?= htmlspecialchars($row['check_in_time']); ?></td>
                                            <td><?= htmlspecialchars($row['check_out_time']); ?></td>
                                            <td><?= ucfirst(htmlspecialchars($row['status'])); ?></td>
                                            <td><?= htmlspecialchars($row['remarks']); ?></td>
                                            <td><?= isset($row['performance']) ? htmlspecialchars($row['performance']) : '-'; ?></td>
                                            <td><?= date('d-m-y H:i:s', strtotime($row['created_at'])); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-danger">
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
<?php } ?>
