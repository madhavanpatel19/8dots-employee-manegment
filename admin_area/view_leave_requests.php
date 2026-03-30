<?php
if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('login.php','_self')</script>";
    exit();
}

$message = "";

// Handle Approval / Rejection
if (isset($_GET['approve']) || isset($_GET['reject'])) {
    $request_id = isset($_GET['approve']) ? (int)$_GET['approve'] : (int)$_GET['reject'];
    $new_status = isset($_GET['approve']) ? 'approved' : 'rejected';

    $update = "UPDATE leave_applications SET status = '$new_status' WHERE id = '$request_id'";
    if (mysqli_query($con, $update)) {
        if ($new_status === 'approved') {
            // Get leave details to update attendance
            $get_leave = mysqli_query($con, "SELECT * FROM leave_applications WHERE id = '$request_id'");
            $leave_row = mysqli_fetch_assoc($get_leave);
            $emp_id = $leave_row['emp_id'];
            $from = $leave_row['leave_from'];
            $to = $leave_row['leave_to'];
            $reason = $leave_row['reason'];

            // Loop through dates and update attendance
            $start_date = new DateTime($from);
            $end_date = new DateTime($to);
            $interval = new DateInterval('P1D');
            $period = new DatePeriod($start_date, $interval, $end_date->modify('+1 day'));

            foreach ($period as $date) {
                $current_date = $date->format('Y-m-d');
                // Check if record exists
                $check = mysqli_query($con, "SELECT id FROM attendance WHERE emp_id = '$emp_id' AND attendance_date = '$current_date'");
                if (mysqli_num_rows($check) > 0) {
                    mysqli_query($con, "UPDATE attendance SET status = 'leave', remarks = 'Leave: $reason' WHERE emp_id = '$emp_id' AND attendance_date = '$current_date'");
                } else {
                    mysqli_query($con, "INSERT INTO attendance (emp_id, attendance_date, status, remarks) VALUES ('$emp_id', '$current_date', 'leave', 'Leave: $reason')");
                }
            }
        }
        $message = "Leave request " . ($new_status === 'approved' ? "approved" : "rejected") . " successfully!";
    } else {
        $message = "Error: " . mysqli_error($con);
    }
}

// Fetch Pending Leave Applications
$query = "SELECT l.*, e.name as emp_name FROM leave_applications l JOIN emp_list e ON l.emp_id = e.id ORDER BY l.status = 'pending' DESC, l.created_at DESC";
$result = mysqli_query($con, $query);
?>

<div class="row"><!-- row Starts -->
    <div class="col-lg-12"><!-- col-lg-12 Starts -->
        <ol class="breadcrumb"><!-- breadcrumb Starts -->
            <li class="active">
                <i class="fa fa-dashboard"></i> Dashboard / Leave Requests
            </li>
        </ol><!-- breadcrumb Ends -->
    </div><!-- col-lg-12 Ends -->
</div><!-- row Ends -->

<div class="row"><!-- 2 row Starts -->
    <div class="col-lg-12"><!-- col-lg-12 Starts -->
        <div class="panel panel-default"><!-- panel panel-default Starts -->
            <div class="custom-page-header" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 15px;">
                <h3 class="panel-title" style="margin: 0;">
                    <i class="fa fa-money fa-fw"></i> View Leave Requests
                </h3>
            </div>

            <div class="panel-body"><!-- panel-body Starts -->
                <?php if ($message) : ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>

                <div class="table-responsive"><!-- table-responsive Starts -->
                    <table class="table table-bordered table-hover table-striped"><!-- table table-bordered table-hover table-striped Starts -->
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Employee</th>
                                <th>From Date</th>
                                <th>To Date</th>
                                <th>Reason</th>
                                <th>Applied On</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            while ($row = mysqli_fetch_array($result)) {
                                $i++;
                                $id = $row['id'];
                                $emp_name = $row['emp_name'];
                                $leave_from = $row['leave_from'];
                                $leave_to = $row['leave_to'];
                                $reason = $row['reason'];
                                $applied_on = $row['created_at'];
                                $status = $row['status'];
                            ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $emp_name; ?></td>
                                    <td><?php echo date('d-m-y', strtotime($leave_from)); ?></td>
                                    <td><?php echo date('d-m-y', strtotime($leave_to)); ?></td>
                                    <td width="200"><?php echo $reason; ?></td>
                                    <td><?php echo date('d-m-y', strtotime($applied_on)); ?></td>
                                    <td>
                                        <?php if ($status == 'pending') : ?>
                                            <span class="label label-warning">Pending</span>
                                        <?php elseif ($status == 'approved') : ?>
                                            <span class="label label-success">Approved</span>
                                        <?php else : ?>
                                            <span class="label label-danger">Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($status == 'pending') : ?>
                                            <a href="index.php?view_leave_requests&approve=<?php echo $id; ?>" class="btn btn-success btn-xs">
                                                <i class="fa fa-check"></i> Approve
                                            </a>
                                            <a href="index.php?view_leave_requests&reject=<?php echo $id; ?>" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to reject this request?')">
                                                <i class="fa fa-times"></i> Reject
                                            </a>
                                        <?php else : ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table><!-- table table-bordered table-hover table-striped Ends -->
                </div><!-- table-responsive Ends -->
            </div><!-- panel-body Ends -->
        </div><!-- panel panel-default Ends -->
    </div><!-- col-lg-12 Ends -->
</div><!-- 2 row Ends -->
