<?php
// session_start();
include 'connection.php';
$errorFields = [];
$successMessage = "";

// Only allow access if logged in as employee
if (!isset($_SESSION['emp_id']) || !isset($_SESSION['emp_name'])) {
    header('Location: emp-login.php');
    exit();
}

$emp_id = $_SESSION['emp_id'];
$emp_name = $_SESSION['emp_name'];

$is_partial = isset($_GET['partial']);

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['apply_leave'])) {
    $leave_from = mysqli_real_escape_string($con, $_POST['leave_from']);
    $leave_to = mysqli_real_escape_string($con, $_POST['leave_to']);
    $reason = mysqli_real_escape_string($con, $_POST['reason']);

    if (empty($leave_from)) $errorFields[] = 'leave_from';
    if (empty($leave_to)) $errorFields[] = 'leave_to';
    if (empty($reason)) $errorFields[] = 'reason';

    if (empty($errorFields)) {
        $insert = "INSERT INTO leave_applications (emp_id, leave_from, leave_to, reason, status) VALUES ('$emp_id', '$leave_from', '$leave_to', '$reason', 'pending')";
        if (mysqli_query($con, $insert)) {
            $successMessage = "Leave application submitted successfully!";
        } else {
            $successMessage = "Error: " . mysqli_error($con);
        }
    }
}

// Fetch Previous Leave Applications
$query = "SELECT * FROM leave_applications WHERE emp_id = '$emp_id' ORDER BY created_at DESC";
$result = mysqli_query($con, $query);
?>

<?php if (!$is_partial) : ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>8DOTS - Leave Application</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        body { background: #f4f7f6; padding-top: 20px; }
        .page-header { border-bottom: 1px solid #eee; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
<?php endif; ?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <i class="fa fa-calendar-check-o"></i> Leave Application
            <small>Request time off and track status</small>
        </h1>
        
        <ol class="breadcrumb">
            <li class="active">
                <i class="fa fa-dashboard"></i> Dashboard / Leave Application
            </li>
        </ol>
    </div>
</div>

<?php if ($successMessage) : ?>
<div class="row">
    <div class="col-lg-12">
        <div class="alert <?php echo strpos($successMessage, 'Error') === false ? 'alert-success' : 'alert-danger'; ?> alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa fa-info-circle"></i> <?php echo htmlspecialchars($successMessage); ?>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-paper-plane fa-fw"></i> Apply New Leave</h3>
            </div>
            <div class="panel-body">
                <div style="margin-bottom: 20px; font-weight: bold; color: #555;">
                    Employee: <?php echo htmlspecialchars($emp_name); ?> (ID: <?php echo htmlspecialchars($emp_id); ?>)
                </div>
                
                <form class="form-horizontal" method="POST">
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">From Date <span class="text-danger">*</span></label>
                        <div class="col-md-6">
                            <input type="date" name="leave_from" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">To Date <span class="text-danger">*</span></label>
                        <div class="col-md-6">
                            <input type="date" name="leave_to" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Reason for Leave <span class="text-danger">*</span></label>
                        <div class="col-md-6">
                            <textarea name="reason" class="form-control" rows="5" placeholder="Enter reason for leave..." required></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-6">
                            <button type="submit" name="apply_leave" class="btn btn-primary">Submit Application</button>
                            <a href="leave_application.php" class="btn btn-default" style="margin-left:10px;">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list fa-fw"></i> Previous Leave Requests</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Applied On</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Reason</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0) : ?>
                                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                    <tr>
                                        <td style="white-space: nowrap;"><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                                        <td style="white-space: nowrap;"><?php echo date('d M Y', strtotime($row['leave_from'])); ?></td>
                                        <td style="white-space: nowrap;"><?php echo date('d M Y', strtotime($row['leave_to'])); ?></td>
                                        <td><?php echo htmlspecialchars($row['reason']); ?></td>
                                        <td>
                                            <?php if($row['status'] == 'pending'): ?>
                                                <span class="label label-warning">Pending</span>
                                            <?php elseif($row['status'] == 'approved'): ?>
                                                <span class="label label-success">Approved</span>
                                            <?php else: ?>
                                                <span class="label label-danger">Rejected</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr><td colspan="5" class="text-center">No previous leave requests found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!$is_partial) : ?>
    </div>
</body>
</html>
<?php endif; ?>
