<?php
// session_start();
include 'connection.php';
$errorFields = [];
$successMessage = "";

// Only allow access if logged in
if (!isset($_SESSION['emp_id']) || !isset($_SESSION['emp_name'])) {
    header('Location: emp-login.php');
    exit();
}

$emp_id = $_SESSION['emp_id'];
$emp_name = $_SESSION['emp_name'];

// Partial rendering logic for dashboard integration
$is_partial = isset($_GET['partial']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $required = ['date', 'start_time', 'end_time', 'task'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $errorFields[] = $field;
        }
    }

    if (empty($errorFields)) {
        // Insert into attendance table
        $attendance_date = mysqli_real_escape_string($con, $_POST['date']);
        $today_date = date('Y-m-d');

        // Server-side validation: only allow current date
        if ($attendance_date !== $today_date) {
            $errorFields[] = 'date';
            $successMessage = "Error: You can only submit worksheet for the current date.";
        } else {
            $check_in_time = mysqli_real_escape_string($con, $_POST['start_time']);
            $check_out_time = mysqli_real_escape_string($con, $_POST['end_time']);
            $remarks = mysqli_real_escape_string($con, $_POST['task']);
        }

        // Check if record exists for this emp/date
        $check = mysqli_query($con, "SELECT id FROM attendance WHERE emp_id='$emp_id' AND attendance_date='$attendance_date'");
        if (mysqli_num_rows($check) > 0) {
            // Update
            $update = "UPDATE attendance SET check_in_time='$check_in_time', check_out_time='$check_out_time', remarks='$remarks' WHERE emp_id='$emp_id' AND attendance_date='$attendance_date'";
            if (mysqli_query($con, $update)) {
                $successMessage = "Worksheet updated successfully!";
            }
        } else {
            // Insert
            $insert = "INSERT INTO attendance (emp_id, attendance_date, check_in_time, check_out_time, status, remarks) VALUES ('$emp_id', '$attendance_date', '$check_in_time', '$check_out_time', 'present', '$remarks')";
            if (mysqli_query($con, $insert)) {
                $successMessage = "Worksheet submitted successfully!";
            }
        }
    }
}

// Fetch recent worksheet history
$history_query = "SELECT * FROM attendance WHERE emp_id = '$emp_id' ORDER BY attendance_date DESC LIMIT 10";
$history_result = mysqli_query($con, $history_query);
?>

<?php if (!$is_partial) : ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <title>8DOTS - Worksheet</title>
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

                    <i class="fa fa-pencil-square-o"></i> Daily Worksheet
                    <div style="text-align: right; margin-top: 20px; margin-bottom: 20px;">
                        <button class="btn btn-success" data-toggle="modal" data-target="#addWorksheetModal">
                            <i class="fa fa-plus"></i> Add Worksheet
                        </button>
                    </div>
                </h1>

                <ol class="breadcrumb">
                    <li class="active">
                        <i class="fa fa-dashboard"></i> Dashboard / Worksheet
                    </li>
                </ol>
            </div>
        </div>

        <?php if ($successMessage) : ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert <?php echo strpos($successMessage, 'Error') !== false ? 'alert-danger' : 'alert-success'; ?> alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="fa <?php echo strpos($successMessage, 'Error') !== false ? 'fa-exclamation-triangle' : 'fa-check'; ?>"></i> <?php echo htmlspecialchars($successMessage); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- <div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-edit fa-fw"></i> Fill Worksheet</h3>
            </div>
            <div class="panel-body">
                <div style="margin-bottom: 20px; font-weight: bold; color: #555;">
                    Employee: <?php echo htmlspecialchars($emp_name); ?> (ID: <?php echo htmlspecialchars($emp_id); ?>)
                </div>
                
                <form class="form-horizontal" method="POST" onsubmit="return validateWorksheetForm();">
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Date <span class="text-danger">*</span></label>
                        <div class="col-md-6">
                            <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Check-in Time <span class="text-danger">*</span></label>
                        <div class="col-md-6">
                            <input type="time" name="start_time" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Check-out Time <span class="text-danger">*</span></label>
                        <div class="col-md-6">
                            <input type="time" name="end_time" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Work/Task Details <span class="text-danger">*</span></label>
                        <div class="col-md-6">
                            <textarea name="task" class="form-control" rows="5" placeholder="Describe what you worked on today..." required><?php echo isset($_POST['task']) ? htmlspecialchars($_POST['task']) : ''; ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">Submit Worksheet</button>
                            <a href="worksheet.php" class="btn btn-default" style="margin-left:10px;">Clear Form</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> -->

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-history fa-fw"></i> Recent Submissions</h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Check-in</th>
                                        <th>Check-out</th>
                                        <th>Task Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($history_result) > 0) : ?>
                                        <?php while ($row = mysqli_fetch_assoc($history_result)) : ?>
                                            <tr>
                                                <td style="white-space: nowrap;"><?php echo date('d-m-y', strtotime($row['attendance_date'])); ?></td>
                                                <td><?php echo $row['check_in_time']; ?></td>
                                                <td><?php echo $row['check_out_time']; ?></td>
                                                <td><?php echo nl2br(htmlspecialchars($row['remarks'])); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No recent records found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- <div style="text-align: right; margin-top: 10px;">
                            <button class="btn btn-success" data-toggle="modal" data-target="#addWorksheetModal">
                                <i class="fa fa-plus"></i> Add Worksheet
                            </button>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Worksheet Modal -->
        <div class="modal fade" id="addWorksheetModal" tabindex="-1" role="dialog" aria-labelledby="addWorksheetModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="addWorksheetModalLabel">Add Worksheet</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" method="POST" onsubmit="return validateWorksheetForm();">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Date <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" readonly>
                                    <small class="text-muted">Worksheet can only be filled for today.</small>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Check-in Time <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="time" name="start_time" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Check-out Time <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="time" name="end_time" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Task Details <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <textarea name="task" class="form-control" rows="4" placeholder="Describe your work..." required></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-offset-3 col-md-8">
                                    <button type="button" class="btn btn-default" data-dismiss="modal" style="margin-left:10px;">Cancel</button>
                                     <button type="submit" class="btn btn-primary">Submit Worksheet</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function validateWorksheetForm() {
                var date = document.querySelector('input[name="date"]');
                var start = document.querySelector('input[name="start_time"]');
                var end = document.querySelector('input[name="end_time"]');
                var task = document.querySelector('textarea[name="task"]');
                var valid = true;
                [date, start, end, task].forEach(function(field) {
                    if (!field.value) {
                        field.parentElement.classList.add('has-error');
                        valid = false;
                    } else {
                        // Weekend blocking for Worksheet
                        if (field.name === 'date') {
                            var dateVal = new Date(field.value);
                            var day = dateVal.getDay();
                            if (day === 0 || day === 6) {
                                alert("Selected date is a " + (day === 0 ? "Sunday" : "Saturday") + ", which is already a holiday. Please select a working day.");
                                field.value = "";
                                valid = false;
                            }
                        }
                        field.parentElement.classList.remove('has-error');
                    }
                });
                return valid;
            }

            // Direct listener for date input
            document.addEventListener('DOMContentLoaded', function() {
                var worksheetDate = document.querySelector('input[name="date"]');
                if (worksheetDate) {
                    worksheetDate.addEventListener('change', function() {
                        if (this.value) {
                            var dateVal = new Date(this.value);
                            var day = dateVal.getDay();
                            if (day === 0 || day === 6) {
                                alert("Selected date is a " + (day === 0 ? "Sunday" : "Saturday") + ", which is already a holiday. Please select a working day.");
                                this.value = "";
                            }
                        }
                    });
                }
            });
        </script>

        <?php if (!$is_partial) : ?>
        </div>
    </body>

    </html>
<?php endif; ?>