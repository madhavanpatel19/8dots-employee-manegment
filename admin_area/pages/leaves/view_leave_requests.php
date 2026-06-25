<?php
if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('../../pages/auth/login.php','_self')</script>";
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

// Fetch Filtered Leave Applications
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
$f_status = isset($_GET['status']) ? mysqli_real_escape_string($con, $_GET['status']) : '';
$f_type = isset($_GET['type']) ? mysqli_real_escape_string($con, $_GET['type']) : '';

$where_clauses = [];
if (!empty($search)) {
    $where_clauses[] = "e.name LIKE '%$search%'";
}
if (!empty($f_status)) {
    $where_clauses[] = "l.status = '$f_status'";
}
if (!empty($f_type)) {
    $where_clauses[] = "lt.leave_name = '$f_type'";
}

$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(" AND ", $where_clauses) : "";

$query = "SELECT l.*, e.id as emp_list_id, e.name as emp_name, e.employee_image, lt.leave_name FROM leave_applications l 
          JOIN emp_list e ON l.emp_id = e.id 
          LEFT JOIN leave_types lt ON l.leave_type_id = lt.id
          $where_sql
          ORDER BY l.status = 'pending' DESC, l.created_at DESC";
$result = mysqli_query($con, $query);

if (!$result) {
    $message = "Database synchronization required. Please import the latest 8dots.sql file. (Error: " . mysqli_error($con) . ")";
}

// Calculate Stats
$stat_total_requests = 0;
$stat_pending = 0;
$stat_approved = 0;
$stat_rejected = 0;

$q_stats = "SELECT status, COUNT(*) as count FROM leave_applications GROUP BY status";
$run_stats = mysqli_query($con, $q_stats);
if ($run_stats) {
    while ($row = mysqli_fetch_assoc($run_stats)) {
        $status = strtolower($row['status']);
        $count = (int)$row['count'];
        $stat_total_requests += $count;
        if ($status == 'pending') $stat_pending += $count;
        elseif ($status == 'approved') $stat_approved += $count;
        elseif ($status == 'rejected') $stat_rejected += $count;
    }
}
?>

<div class="page-wrapper premium-ui-enabled">
    <div class="page-header-premium">
        <h1></h1>
        <div class="header-actions" style="display: flex; gap: 10px;">
            <button style="background:#DF2127 !important;" class="btn-premium-add" onclick="openManageLeaves()">
                <i class="fa fa-cog"></i> Manage Leave Types
            </button>
        </div>
    </div>

    <!-- Manage Leave Types Modal -->
    <div id="manageLeavesModal" class="modal fade" role="dialog" style="z-index: 99999;">
        <div class="modal-dialog" style="margin-top: 80px; max-width: 550px;">
            <div class="modal-content premium-modal-content" style="border: none; border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3); overflow: hidden;">
                <div class="modal-header" style="background: #ffeaeb; color: #000; padding: 25px; border: none; position: relative;">
                    <button type="button" class="close" data-dismiss="modal" style="color: #000000ff; opacity: 0.5;">&times;</button>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 45px; height: 45px; background:#df2127; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; color: #fff;">
                            <i class="fa fa-calendar-o"></i>
                        </div>
                        <div>
                            <h4 class="modal-title" style="font-weight: 800; font-size: 18px; margin: 0;">Annual Leave Policy</h4>
                            <p style="margin: 4px 0 0 0; font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Define Yearly Allowances</p>
                        </div>
                    </div>
                </div>
                <div class="modal-body" style="padding: 30px; background: #fff;">
                    <!-- Add New Leave Type Form -->
                    <div style="background: #f8fafc; padding: 20px; border-radius: 16px; margin-bottom: 25px; border: 1px solid #f1f5f9;">
                        <div style="display: flex; gap: 12px;">
                            <div style="flex: 1;">
                                <label style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; display: block;">Leave Name</label>
                                <input type="text" id="new_leave_name" class="p-input-premium" placeholder="e.g. Sick Leave" style="height: 42px;">
                            </div>
                            <div style="width: 100px;">
                                <label style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; display: block;">Count</label>
                                <input type="number" id="new_leave_count" class="p-input-premium" placeholder="12" style="height: 42px;">
                            </div>
                            <div style="align-self: flex-end;">
                                <button class="btn-premium-add" onclick="addLeaveType()" style="height: 42px; border: none;">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Leave Types List -->
                    <div id="leave-types-list-container">
                        <!-- Loaded via AJAX -->
                        <div class="spinner-premium" style="margin: 20px auto;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- <ol class="breadcrumb">
        <li><i class="fa fa-calendar-check-o"></i> Leave Requests</li>
    </ol> -->

    <style>
        .table-premium th {
            text-align: center !important;
        }

        .table-premium th:nth-child(2),
        .table-premium td:nth-child(2) {
            text-align: left !important;
        }

        .table-premium th:nth-child(5),
        .table-premium td:nth-child(5) {
            text-align: left !important;
        }

        .p-badge-warning {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .p-badge-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .p-badge-default {
            background: rgba(148, 163, 184, 0.1);
            color: #64748b;
        }

        /* New UI Styles */

        .filter-bar {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 25px;
            display: flex;
            gap: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-input-wrap {
            flex: 1;
            min-width: 200px;
            position: relative;
        }

        .filter-input-wrap i {
            position: absolute;
            left: 15px;
            top: 14px;
            color: #94a3b8;
        }

        .filter-input {
            width: 100%;
            height: 42px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0 15px 0 40px;
            font-size: 14px;
            color: #475569;
            outline: none;
            transition: 0.2s;
        }

        .filter-input:focus {
            border-color: #DF2127;
            box-shadow: 0 0 0 3px rgba(223, 33, 39, 0.1);
        }

        .filter-select {
            height: 42px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0 15px;
            font-size: 14px;
            color: #475569;
            outline: none;
            background: #fff;
            cursor: pointer;
            min-width: 160px;
        }

        .btn-filter {
            background: #DF2127;
            color: #fff;
            border: none;
            height: 42px;
            padding: 0 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(223, 33, 39, 0.3);
        }

        .btn-icon-premium {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            width: 38px;
            height: 38px;
            transition: 0.3s;
            cursor: pointer;
            color: #64748b;
            text-decoration: none !important;
        }

        .btn-icon-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .btn-icon-approve {
            color: #10b981 !important;
            background: #ecfdf5 !important;
            border-color: #d1fae5 !important;
        }

        .btn-icon-approve:hover {
            background: #d1fae5 !important;
            border-color: #a7f3d0 !important;
            color: #059669 !important;
        }

        .btn-icon-reject {
            color: #ef4444 !important;
            background: #fef2f2 !important;
            border-color: #fee2e2 !important;
        }

        .btn-icon-reject:hover {
            background: #fee2e2 !important;
            border-color: #fecaca !important;
            color: #b91c1c !important;
        }

        .table-premium th,
        .table-premium td {
            vertical-align: middle !important;
        }
    </style>

    <div class="stat-cards-row">
        <!-- Total Requests -->
        <div class="stat-card" style="cursor: pointer; transition: 0.3s;" onclick="window.location.href='index.php?view_leave_requests'">
            <div class="stat-card-icon" style="background: #fee2e2; color: #ef4444;">
                <i class="fa fa-copy"></i>
            </div>
            <div class="stat-card-body" style="text-align: left;">
                <div class="stat-card-value"><?php echo sprintf('%02d', $stat_total_requests); ?></div>
                <div class="stat-card-title">TOTAL REQUESTS</div>
            </div>
        </div>
        <!-- Pending Requests -->
        <div class="stat-card" style="cursor: pointer; transition: 0.3s;" onclick="window.location.href='index.php?view_leave_requests&status=Pending'">
            <div class="stat-card-icon sc-orange">
                <i class="fa fa-users"></i>
            </div>
            <div class="stat-card-body" style="text-align: left;">
                <div class="stat-card-value"><?php echo sprintf('%02d', $stat_pending); ?></div>
                <div class="stat-card-title">PENDING</div>
            </div>
        </div>
        <!-- Approved Requests -->
        <div class="stat-card" style="cursor: pointer; transition: 0.3s;" onclick="window.location.href='index.php?view_leave_requests&status=Approved'">
            <div class="stat-card-icon sc-green">
                <i class="fa fa-check-circle"></i>
            </div>
            <div class="stat-card-body" style="text-align: left;">
                <div class="stat-card-value"><?php echo sprintf('%02d', $stat_approved); ?></div>
                <div class="stat-card-title">APPROVED</div>
            </div>
        </div>
        <!-- Rejected Requests -->
        <div class="stat-card" style="cursor: pointer; transition: 0.3s;" onclick="window.location.href='index.php?view_leave_requests&status=Rejected'">
            <div class="stat-card-icon sc-purple">
                <i class="fa fa-commenting"></i>
            </div>
            <div class="stat-card-body" style="text-align: left;">
                <div class="stat-card-value"><?php echo sprintf('%02d', $stat_rejected); ?></div>
                <div class="stat-card-title">REJECTED</div>
            </div>
        </div>
    </div>
    <div class="premium-card" style="background: #fff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); overflow: hidden;">
        <div class="card-hdr" style="padding: 20px 24px; background:var(--p-bg-header);color:white; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; gap: 15px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fa fa-list"></i>
                <h3 style="margin: 0; font-size: 16px; font-weight: 700;">Leave Requests List</h3>
            </div>
        </div>

        <?php if ($message) : ?>
            <div style="padding: 15px 25px;">
                <div class="alert alert-info" style="border-radius: 10px; margin: 0; font-weight: 600;">
                    <i class="fa fa-info-circle"></i> <?php echo $message; ?>
                </div>
            </div>
        <?php endif; ?>

        <div style="overflow-x: auto;">
            <table class="table-premium">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th style="width: 200px;">Employee</th>
                        <th>Leave Type</th>
                        <th style="width: 110px;">From Date</th>
                        <th style="width: 110px;">To Date</th>
                        <th style="width: 100px;">Duration</th>
                        <th>Reason</th>
                        <th style="width: 110px;">Applied On</th>
                        <th style="width: 120px;">Status</th>
                        <th style="width: 160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_array($result)) {
                            $i++;
                            $id = $row['id'];
                            $emp_name = $row['emp_name'];
                            $status = strtolower($row['status']);

                            $status_class = 'p-badge-default';
                            if ($status === 'approved') $status_class = 'p-badge-success';
                            elseif ($status === 'rejected') $status_class = 'p-badge-danger';
                            elseif ($status === 'pending') $status_class = 'p-badge-warning';

                            $from_date = new DateTime($row['leave_from']);
                            $to_date = new DateTime($row['leave_to']);
                            $duration_days = $from_date->diff($to_date)->days + 1;
                            $duration_str = $duration_days . ' Day' . ($duration_days > 1 ? 's' : '');

                            $emp_img = !empty($row['employee_image']) ? "uploads/" . $row['employee_image'] : "../admin_images/default.png";
                            $emp_id_formatted = "EMP" . str_pad($row['emp_list_id'], 3, "0", STR_PAD_LEFT);
                    ?>
                            <tr>
                                <td class="text-center" style="font-weight: 700; color: #64748b;"><?php echo $i; ?></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <img src="<?php echo htmlspecialchars($emp_img); ?>" alt="avatar" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;">
                                        <div>
                                            <div style="font-weight: 700; color: #1e293b; font-size: 13px;"><?php echo htmlspecialchars($emp_name); ?></div>
                                            <div style="font-size: 11px; color: #64748b; font-weight: 600;"><?php echo $emp_id_formatted; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="p-badge p-badge-secondary" style="background: #e0e7ff; color: #4338ca; border: none; font-weight: 600; padding: 4px 10px;"><?php echo htmlspecialchars($row['leave_name'] ?: 'N/A'); ?></span>
                                </td>
                                <td class="text-center" style="font-weight: 600; color: #475569; font-size: 13px;">
                                    <?php echo date('d-m-Y', strtotime($row['leave_from'])); ?>
                                </td>
                                <td class="text-center" style="font-weight: 600; color: #475569; font-size: 13px;">
                                    <?php echo date('d-m-Y', strtotime($row['leave_to'])); ?>
                                </td>
                                <td class="text-center" style="font-weight: 600; color: #1e293b; font-size: 13px;">
                                    <?php echo $duration_str; ?>
                                </td>
                                <td class="p-cell-wrap" style="font-size: 13px; color: #475569; font-weight: 500;"><?php echo htmlspecialchars($row['reason']); ?></td>
                                <td class="text-center" style="font-size: 11px; color: #94a3b8;">
                                    <?php echo date('d-m-Y', strtotime($row['created_at'])); ?>
                                </td>
                                <td class="text-center">
                                    <span class="p-badge <?php echo $status_class; ?>"><?php echo ucfirst($status); ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($status == 'pending') : ?>
                                        <div style="display: flex; gap: 8px; justify-content: center;">
                                            <a href="index.php?view_leave_requests&approve=<?php echo $id; ?>" class="btn-icon-premium btn-icon-approve" title="Approve">
                                                <i class="fa fa-check"></i>
                                            </a>
                                            <a href="index.php?view_leave_requests&reject=<?php echo $id; ?>" class="btn-icon-premium btn-icon-reject" title="Reject" onclick="return confirm('Reject this request?')">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </div>
                                    <?php else : ?>
                                        <span style="color: #cbd5e1;">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php }
                    } elseif ($result && mysqli_num_rows($result) == 0) { ?>
                        <tr>
                            <td colspan="10" style="padding: 80px 0; text-align: center;">
                                <div style="background: #f8fafc; padding: 35px; border-radius: 16px; border: 1.5px dashed #cbd5e1; display: inline-block; max-width: 450px;">
                                    <i class="fa fa-inbox" style="color: #94a3b8; font-size: 48px; margin-bottom: 20px;"></i>
                                    <h4 style="color: #334155; font-weight: 800; font-size: 18px; margin-bottom: 8px;">No Leave Requests Found</h4>
                                    <p style="color: #64748b; font-size: 14px; font-weight: 500; margin: 0;">There are no leave requests matching the current filters.</p>
                                </div>
                            </td>
                        </tr>
                    <?php } else { ?>
                        <tr>
                            <td colspan="10" style="padding: 80px 0; text-align: center;">
                                <div style="background: #fef2f2; padding: 25px; border-radius: 16px; border: 1.5px dashed #fecaca; display: inline-block; max-width: 400px;">
                                    <i class="fa fa-database" style="color: #ef4444; font-size: 40px; margin-bottom: 15px;"></i>
                                    <h4 style="color: #991b1b; font-weight: 800;">Schema Out of Sync</h4>
                                    <p style="color: #b91c1c; font-size: 13px; font-weight: 600;">The leave management tables are missing from your database.</p>
                                    <p style="color: #7f1d1d; font-size: 12px; margin-top: 10px; opacity: 0.8;">Please import <b>8dots.sql</b> to fix this.</p>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    window.openManageLeaves = function() {
        $('#manageLeavesModal').modal('show');
        loadLeaveTypes();
    }

    function loadLeaveTypes() {
        const container = $('#leave-types-list-container');
        container.html('<div class="spinner-premium" style="margin: 20px auto;"></div>');

        $.ajax({
            url: 'ajax/leaves/ajax_get_leave_types.php',
            method: 'GET',
            success: function(response) {
                container.html(response);
            }
        });
    }

    window.addLeaveType = function() {
        const name = $('#new_leave_name').val().trim();
        const count = $('#new_leave_count').val().trim();

        if (!name || !count) {
            alert("Please fill all fields");
            return;
        }

        $.ajax({
            url: 'ajax/leaves/ajax_add_leave_type.php',
            method: 'POST',
            data: {
                leave_name: name,
                num_of_leave: count
            },
            success: function(response) {
                try {
                    const res = typeof response === 'string' ? JSON.parse(response) : response;
                    if (res.success) {
                        $('#new_leave_name').val('');
                        $('#new_leave_count').val('');
                        loadLeaveTypes();
                    } else {
                        alert(res.message || "Error adding leave type");
                    }
                } catch (e) {
                    console.error("Response parse error:", e);
                    loadLeaveTypes(); // Refresh anyway
                }
            }
        });
    }

    window.deleteLeaveType = function(id) {
        if (!confirm("Are you sure you want to delete this leave type?")) return;

        $.ajax({
            url: 'ajax/leaves/ajax_delete_leave_type.php',
            method: 'POST',
            data: {
                id: id
            },
            success: function(response) {
                try {
                    const res = typeof response === 'string' ? JSON.parse(response) : response;
                    if (res.success) {
                        loadLeaveTypes();
                    } else {
                        alert(res.message || "Error deleting leave type");
                    }
                } catch (e) {
                    loadLeaveTypes();
                }
            }
        });
    }
</script>

<style>
    .spinner-premium {
        width: 30px;
        height: 30px;
        border: 3px solid #f1f5f9;
        border-top: 3px solid #6366f1;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .leave-type-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 20px;
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        margin-bottom: 10px;
        transition: 0.3s;
    }

    .leave-type-card:hover {
        border-color: #e2e8f0;
        transform: translateX(4px);
        background: #fcfdfe;
    }
</style>