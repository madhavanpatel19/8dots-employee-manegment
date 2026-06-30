<?php
// =============================================================
// emp_area/pages/worksheets/worksheet.php
// Employee worksheet – supports partial render (via index.php)
// Moved from: admin_area/pages/worksheets/worksheet.php
// Paths updated:
//   - db.php include → emp_area/includes/db.php
//   - auth redirect → emp_area/pages/auth/login.php
//   - CSS/assets   → admin_area (up 3 levels from here)
// =============================================================
if (!isset($con)) {
    include(__DIR__ . '/../../includes/db.php');
}
$errorFields    = [];
$successMessage = "";

if (!isset($_SESSION['emp_id']) || !isset($_SESSION['emp_name'])) {
    header('Location: ../../pages/auth/login.php');
    exit();
}

$emp_id   = $_SESSION['emp_id'];
$emp_name = $_SESSION['emp_name'];

$is_partial = isset($_GET['partial']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $required = ['date', 'start_time', 'end_time', 'task'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $errorFields[] = $field;
        }
    }

    if (empty($errorFields)) {
        $attendance_date = mysqli_real_escape_string($con, $_POST['date']);
        $today_date      = date('Y-m-d');

        if ($attendance_date !== $today_date) {
            $errorFields[]  = 'date';
            $successMessage = "Error: You can only submit worksheet for the current date.";
        } else {
            $check_in_time  = mysqli_real_escape_string($con, $_POST['start_time']);
            $check_out_time = mysqli_real_escape_string($con, $_POST['end_time']);
            $remarks        = mysqli_real_escape_string($con, $_POST['task']);
        }

        $check = mysqli_query($con, "SELECT id FROM attendance WHERE emp_id='$emp_id' AND attendance_date='$attendance_date'");
        if (mysqli_num_rows($check) > 0) {
            $update = "UPDATE attendance SET check_in_time='$check_in_time', check_out_time='$check_out_time', remarks='$remarks' WHERE emp_id='$emp_id' AND attendance_date='$attendance_date'";
            if (mysqli_query($con, $update)) {
                $successMessage = "Worksheet updated successfully!";
            }
        } else {
            $insert = "INSERT INTO attendance (emp_id, attendance_date, check_in_time, check_out_time, status, remarks) VALUES ('$emp_id', '$attendance_date', '$check_in_time', '$check_out_time', 'present', '$remarks')";
            if (mysqli_query($con, $insert)) {
                $successMessage = "Worksheet submitted successfully!";
            }
        }
    }
}

$history_query  = "SELECT * FROM attendance WHERE emp_id = '$emp_id' ORDER BY attendance_date DESC LIMIT 10";
$history_result = mysqli_query($con, $history_query);

$today_date  = date('Y-m-d');
$today_q     = "SELECT check_in_time, check_out_time FROM attendance WHERE emp_id = '$emp_id' AND attendance_date = '$today_date'";
$today_res   = mysqli_query($con, $today_q);
$today_att   = mysqli_fetch_assoc($today_res);
$prefill_in  = ($today_att && $today_att['check_in_time'])  ? date('H:i', strtotime($today_att['check_in_time']))  : '';
$prefill_out = ($today_att && $today_att['check_out_time']) ? date('H:i', strtotime($today_att['check_out_time'])) : date('H:i');
?>

<?php if (!$is_partial) : ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <title>8DOTS - Worksheet</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../../../admin_area/css/bootstrap.min.css">
        <link href="../../../admin_area/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <link href="../../../admin_area/css/style.css" rel="stylesheet">
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

        <div class="premium-ui-enabled">
            <div class="page-header-premium">
                <h1></h1>
                <div class="header-actions">
                    <button class="btn-premium-add" data-toggle="modal" data-target="#addWorksheetModal">
                        <i class="fa fa-plus"></i> Add Worksheet
                    </button>
                </div>
            </div>

            <!-- <div class="row">
            <div class="col-lg-12">
                <ol class="breadcrumb" style="background: #f1f5f9; border-radius: 12px; padding: 12px 20px; margin-bottom: 25px;">
                    <li><a href="../../index.php?dashboard" style="color: #64748b; text-decoration: none;"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                    <li class="active" style="color: #1e293b; font-weight: 600;">Worksheet</li>
                </ol>
            </div>
        </div> -->

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

            <div class="row">
                <div class="col-lg-12">
                    <div class="premium-card">
                        <div class="card-hdr">
                            <i class="fa fa-history"></i>
                            <h3>Recent Submissions</h3>
                        </div>
                        <div style="overflow-x: auto;">
                            <table class="table-premium">
                                <thead>
                                    <tr>
                                        <th style="width: 60px; text-align: center;">#</th>
                                        <th style="text-align: center;">Date</th>
                                        <th style="text-align: center;">Check-in</th>
                                        <th style="text-align: center;">Check-out</th>
                                        <th style="text-align: center;">Duration</th>
                                        <th style="text-align: center;">Status</th>
                                        <th class="p-cell-wrap">Task Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($history_result) > 0) : $i = 1; ?>
                                        <?php while ($row = mysqli_fetch_assoc($history_result)) :
                                            $st = $row['status'] ?: 'present';
                                            $badge_class = 'p-badge-secondary';
                                            if ($st == 'present') $badge_class = 'p-badge-success';
                                            elseif ($st == 'absent') $badge_class = 'p-badge-danger';
                                            elseif ($st == 'leave')  $badge_class = 'p-badge-primary';
                                        ?>
                                            <tr>
                                                <td style="text-align: center; font-weight: 700; color: #64748b;"><?php echo $i++; ?></td>
                                                <td style="font-weight: 600; color: #1e293b; text-align: center;"><?php echo date('d M Y', strtotime($row['attendance_date'])); ?></td>
                                                <td style="text-align: center; color: #64748b; font-size: 13px;"><?php echo $row['check_in_time'] ?: '--:--'; ?></td>
                                                <td style="text-align: center; color: #64748b; font-size: 13px;"><?php echo $row['check_out_time'] ?: '--:--'; ?></td>
                                                <td style="text-align: center; color: #dd2127; font-weight: 700;">
                                                    <?php
                                                    if ($row['total_duration_secs'] > 0) {
                                                        $h = floor($row['total_duration_secs'] / 3600);
                                                        $m = floor(($row['total_duration_secs'] % 3600) / 60);
                                                        echo "{$h}h {$m}m";
                                                    } else {
                                                        echo "-";
                                                    }
                                                    ?>
                                                </td>
                                                <td style="text-align: center;">
                                                    <span class="p-badge <?php echo $badge_class; ?>" style="display: inline-flex; justify-content: center; min-width: 80px;">
                                                        <?php echo ucfirst($st); ?>
                                                    </span>
                                                </td>
                                                <td class="p-cell-wrap">
                                                    <div style="font-size: 13px; line-height: 1.6;">
                                                        <?php echo nl2br(htmlspecialchars($row['remarks'])); ?>

                                                        <?php
                                                        if (!empty($row['work_photos'])) {
                                                            $photos = json_decode($row['work_photos'], true);
                                                            if (!empty($photos)) {
                                                                echo '<div style="display: flex; gap: 6px; margin-top: 10px; flex-wrap: wrap;">';
                                                                foreach ($photos as $p) {
                                                                    $img_url = '../admin_area/' . $p;
                                                                    echo '<div class="work-photo-item" onclick="window.open(\'' . htmlspecialchars($img_url) . '\')" title="Click to view full image">
                                                                        <img src="' . htmlspecialchars($img_url) . '">
                                                                        <div class="work-photo-overlay"><i class="fa fa-search-plus"></i></div>
                                                                      </div>';
                                                                }
                                                                echo '</div>';
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">
                                                <i class="fa fa-folder-open-o" style="font-size: 32px; display: block; margin-bottom: 10px;"></i>
                                                No recent worksheet records found.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Worksheet Modal -->
            <div class="modal fade" id="addWorksheetModal" tabindex="-1" role="dialog" aria-labelledby="addWorksheetModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content" style="border-radius: 20px; overflow: hidden; border: none; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
                        <div class="modal-header" style="background: #ffeaeb; color: black; padding: 20px 25px;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 0.8;"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="addWorksheetModalLabel" style="font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; font-size: 15px;">
                                <i class="fa fa-plus-circle"></i> Add New Worksheet
                            </h4>
                        </div>
                        <div class="modal-body" style="padding: 25px;">
                            <form class="form-horizontal" method="POST" onsubmit="return validateWorksheetForm();">
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left; color: #64748b; font-weight: 600;">Date</label>
                                    <div class="col-md-8">
                                        <input type="date" name="date" class="p-input-premium" style="background: #f8fafc;" value="<?php echo date('Y-m-d'); ?>" readonly>
                                        <small style="color: #94a3b8; font-size: 11px; margin-top: 5px; display: block;">Worksheet can only be filled for today.</small>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left; color: #64748b; font-weight: 600;">Check-in Time <span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="time" name="start_time" id="ws_start_time" class="p-input-premium" style="background: #f8fafc;" value="<?php echo $prefill_in; ?>" readonly required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left; color: #64748b; font-weight: 600;">Check-out Time <span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="time" name="end_time" id="ws_end_time" class="p-input-premium" style="background: #f8fafc;" value="<?php echo $prefill_out; ?>" readonly required>
                                        <small style="color: #dd2127; font-size: 11px; margin-top: 5px; display: block;"><i class="fa fa-info-circle"></i> Times are automatically fetched from your real-time Check-In/Out.</small>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left; color: #64748b; font-weight: 600;">Work Details <span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <textarea name="task" class="p-input-premium" style="height: 120px; resize: none;" placeholder="What did you accomplish today?" required></textarea>
                                    </div>
                                </div>
                                <div class="form-group" style="margin-top: 30px; margin-bottom: 0;">
                                    <div class="col-md-12" style="display: flex; gap: 10px; justify-content: flex-end;">
                                        <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 10px; padding: 10px 20px; font-weight: 600;">Cancel</button>
                                        <button type="submit" class="btn-premium-add" style="border: none;">
                                            <i class="fa fa-paper-plane"></i> Submit Worksheet
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- Closing premium-ui-enabled div -->

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
                        if (field.name === 'date') {
                            var dateVal = new Date(field.value);
                            var day = dateVal.getDay();
                            if (day === 0 || day === 6) {
                                Swal.fire('Notification', "Selected date is a " + (day === 0 ? "Sunday" : "Saturday", 'info') + ", which is a holiday.");
                                field.value = "";
                                valid = false;
                            }
                        }
                        field.parentElement.classList.remove('has-error');
                    }
                });
                return valid;
            }

            document.addEventListener('DOMContentLoaded', function() {
                $('.btn-premium-add').on('click', function() {
                    var endTimeField = $('#ws_end_time');
                    if (!endTimeField.val()) {
                        var now = new Date();
                        var h = String(now.getHours()).padStart(2, '0');
                        var m = String(now.getMinutes()).padStart(2, '0');
                        endTimeField.val(h + ":" + m);
                    }
                });
            });
        </script>

        <?php if (!$is_partial) : ?>
        </div>
    </body>

    </html>
<?php endif; ?>