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
            $_SESSION['leave_success'] = "Leave application submitted successfully!";
        } else {
            $_SESSION['leave_error'] = "Error: " . mysqli_error($con);
        }
    } else {
        $_SESSION['leave_error'] = "Please fill in all required fields.";
    }

    // Redirect to prevent form resubmission using JavaScript since HTML might already be sent
    $redirect_url = $_SERVER['PHP_SELF'];
    if (isset($_GET['leave_application'])) {
        $redirect_url = "emp_index.php?leave_application";
    }
    echo "<script>window.open('$redirect_url','_self');</script>";
    exit();
}

// Retrieve messages from session if they exist
if (isset($_SESSION['leave_success'])) {
    $successMessage = $_SESSION['leave_success'];
    unset($_SESSION['leave_success']);
}
if (isset($_SESSION['leave_error'])) {
    $successMessage = $_SESSION['leave_error']; // Reusing the same variable for display logic below
    unset($_SESSION['leave_error']);
}

$is_partial = isset($_GET['partial']);

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
            body {
                background: #f4f7f6;
                padding-top: 20px;
            }

            .page-header {
                border-bottom: 1px solid #eee;
                margin-bottom: 20px;
            }
        </style>
    </head>

    <body>
        <div class="container">
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    <i class="fa fa-calendar-check-o"></i> Leave Application
                    <div style="text-align: right; margin-top: 20px; margin-bottom: 20px;">
                        <button class="btn btn-success" data-toggle="modal" data-target="#applyLeaveModal">
                            <i class="fa fa-plus"></i> Apply Leave
                        </button>
                    </div>
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
                <!-- <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-paper-plane fa-fw"></i> Apply New Leave</h3>
            </div>
            <div class="panel-body">
                <div style="margin-bottom: 20px; font-weight: bold; color: #555;">
                    Employee: <?php echo htmlspecialchars($emp_name); ?> (ID: <?php echo htmlspecialchars($emp_id); ?>)
                </div>
                
                 Modal trigger only, form moved to modal below
            </div>
        </div> -->

                <!-- Apply Leave Modal -->
                <div class="modal fade" id="applyLeaveModal" tabindex="-1" role="dialog" aria-labelledby="applyLeaveModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="applyLeaveModalLabel">Apply Leave</h4>
                            </div>
                            <div class="modal-body">
                                <form class="form-horizontal" method="POST" onsubmit="return validateLeaveForm();">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">From Date <span class="text-danger">*</span></label>
                                        <div class="col-md-8">
                                            <input type="date" name="leave_from" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">To Date <span class="text-danger">*</span></label>
                                        <div class="col-md-8">
                                            <input type="date" name="leave_to" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Reason for Leave <span class="text-danger">*</span></label>
                                        <div class="col-md-8">
                                            <textarea name="reason" class="form-control" rows="4" placeholder="Enter reason for leave..." required></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-offset-3 col-md-8">
                                            <button type="button" class="btn btn-default" data-dismiss="modal" style="margin-left:10px;">Cancel</button>
                                            <button type="submit" name="apply_leave" class="btn btn-primary">Submit Application</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function validateLeaveForm() {
                        var from = document.querySelector('#applyLeaveModal input[name="leave_from"]');
                        var to = document.querySelector('#applyLeaveModal input[name="leave_to"]');
                        var reason = document.querySelector('#applyLeaveModal textarea[name="reason"]');
                        var valid = true;
                        [from, to, reason].forEach(function(field) {
                            if (!field.value) {
                                field.parentElement.classList.add('has-error');
                                valid = false;
                            } else {
                                // Specific check for weekend dates
                                if (field.name === 'leave_from' || field.name === 'leave_to') {
                                    var date = new Date(field.value);
                                    var day = date.getDay(); // 0 is Sun, 6 is Sat
                                    if (day === 0 || day === 6) {
                                        alert("Selected date is a " + (day === 0 ? "Sunday" : "Saturday") + ", which is already a holiday. Please select a working day.");
                                        field.value = ""; // Reset the field
                                        valid = false;
                                    }
                                }
                                field.parentElement.classList.remove('has-error');
                            }
                        });
                        return valid;
                    }
                </script>
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
                                                    <?php if ($row['status'] == 'pending'): ?>
                                                        <span class="label label-warning">Pending</span>
                                                    <?php elseif ($row['status'] == 'approved'): ?>
                                                        <span class="label label-success">Approved</span>
                                                    <?php else: ?>
                                                        <span class="label label-danger">Rejected</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No previous leave requests found.</td>
                                        </tr>
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