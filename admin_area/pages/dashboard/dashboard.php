<?php
// ---- Basic safety: DB + session + admin check ----

// Include database connection if not already included
if (!isset($con) || !$con) {
    if (!isset($con)) {
        include(__DIR__ . '/../../includes/db.php');
    }
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

// Check admin login
if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('../../pages/auth/login.php','_self')</script>";
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



<div class="dashboard-content">
    <!-- Greeting Section
    <div class="greeting-section">
        <div class="date-badge">
            <i class="fa fa-calendar-o"></i>
            <span><?php echo date('M d, Y (D)'); ?></span>
        </div>
    </div> -->
    <?php
    // ---- Fetch Stats for Dashboard Cards ----

    // Total Projects
    $total_projects = 0;
    $q_tp = "SELECT COUNT(*) AS total FROM client_projects";
    $r_tp = mysqli_query($con, $q_tp);
    if ($r_tp && $row_tp = mysqli_fetch_assoc($r_tp)) {
        $total_projects = (int)$row_tp['total'];
    }

    // Active Projects
    $active_projects = 0;
    $q_ap = "SELECT COUNT(*) AS total FROM client_projects WHERE LOWER(status) LIKE '%active%' OR LOWER(status) LIKE '%progress%'";
    $r_ap = mysqli_query($con, $q_ap);
    if ($r_ap && $row_ap = mysqli_fetch_assoc($r_ap)) {
        $active_projects = (int)$row_ap['total'];
    }

    // Pending Leaves (already available from sidebar as $pending_leave_count, but fetch fresh just in case)
    $pending_leaves = 0;
    $q_pl = "SELECT COUNT(*) AS total FROM leave_applications WHERE status='pending'";
    $r_pl = mysqli_query($con, $q_pl);
    if ($r_pl && $row_pl = mysqli_fetch_assoc($r_pl)) {
        $pending_leaves = (int)$row_pl['total'];
    }

    // Last month comparisons
    $last_month_start = date('Y-m-01', strtotime('-1 month'));
    $last_month_end = date('Y-m-t', strtotime('-1 month'));
    $this_month_start = date('Y-m-01');

    // Projects this month vs last month
    $proj_this_month = 0;
    $q_ptm = "SELECT COUNT(*) AS total FROM client_projects WHERE created_at >= '$this_month_start'";
    $r_ptm = mysqli_query($con, $q_ptm);
    if ($r_ptm && $row_ptm = mysqli_fetch_assoc($r_ptm)) {
        $proj_this_month = (int)$row_ptm['total'];
    }

    $proj_last_month = 0;
    $q_plm = "SELECT COUNT(*) AS total FROM client_projects WHERE created_at >= '$last_month_start' AND created_at <= '$last_month_end'";
    $r_plm = mysqli_query($con, $q_plm);
    if ($r_plm && $row_plm = mysqli_fetch_assoc($r_plm)) {
        $proj_last_month = (int)$row_plm['total'];
    }
    $proj_pct_change = $proj_last_month > 0 ? round((($proj_this_month - $proj_last_month) / $proj_last_month) * 100) : ($proj_this_month > 0 ? 100 : 0);

    // Employees this month vs last month
    $emp_this_month = 0;
    $q_etm = "SELECT COUNT(*) AS total FROM emp_list WHERE join_date >= '$this_month_start'";
    $r_etm = mysqli_query($con, $q_etm);
    if ($r_etm && $row_etm = mysqli_fetch_assoc($r_etm)) {
        $emp_this_month = (int)$row_etm['total'];
    }

    $emp_last_month = 0;
    $q_elm = "SELECT COUNT(*) AS total FROM emp_list WHERE join_date >= '$last_month_start' AND join_date <= '$last_month_end'";
    $r_elm = mysqli_query($con, $q_elm);
    if ($r_elm && $row_elm = mysqli_fetch_assoc($r_elm)) {
        $emp_last_month = (int)$row_elm['total'];
    }
    $emp_pct_change = $emp_last_month > 0 ? round((($emp_this_month - $emp_last_month) / $emp_last_month) * 100) : ($emp_this_month > 0 ? 100 : 0);

    // Active projects change
    $active_this_month = 0;
    $q_atm = "SELECT COUNT(*) AS total FROM client_projects WHERE (LOWER(status) LIKE '%active%' OR LOWER(status) LIKE '%progress%') AND created_at >= '$this_month_start'";
    $r_atm = mysqli_query($con, $q_atm);
    if ($r_atm && $row_atm = mysqli_fetch_assoc($r_atm)) {
        $active_this_month = (int)$row_atm['total'];
    }

    $active_last_month = 0;
    $q_alm = "SELECT COUNT(*) AS total FROM client_projects WHERE (LOWER(status) LIKE '%active%' OR LOWER(status) LIKE '%progress%') AND created_at >= '$last_month_start' AND created_at <= '$last_month_end'";
    $r_alm = mysqli_query($con, $q_alm);
    if ($r_alm && $row_alm = mysqli_fetch_assoc($r_alm)) {
        $active_last_month = (int)$row_alm['total'];
    }
    $active_pct_change = $active_last_month > 0 ? round((($active_this_month - $active_last_month) / $active_last_month) * 100) : ($active_this_month > 0 ? 100 : 0);
    ?>

    <!-- Dashboard Stat Cards -->
    <div class="stat-cards-row">
        <!-- Total Projects -->
        <div class="stat-card">
            <div class="stat-card-icon sc-purple">
                <i class="fa fa-briefcase"></i>
            </div>
            <div class="stat-card-body">
                <div class="stat-card-title">Total Projects</div>
                <div class="stat-card-value"><?php echo $total_projects; ?></div>
            </div>
        </div>

        <!-- Active Projects -->
        <div class="stat-card">
            <div class="stat-card-icon sc-green">
                <i class="fa fa-users"></i>
            </div>
            <div class="stat-card-body">
                <div class="stat-card-title">Active Projects</div>
                <div class="stat-card-value"><?php echo $active_projects; ?></div>
            </div>
        </div>

        <!-- Total Employees -->
        <div class="stat-card">
            <div class="stat-card-icon sc-orange">
                <i class="fa fa-users"></i>
            </div>
            <div class="stat-card-body">
                <div class="stat-card-title">Total Employees</div>
                <div class="stat-card-value"><?php echo $count_employees; ?></div>
            </div>
        </div>

        <!-- Pending Leaves -->
        <div class="stat-card">
            <div class="stat-card-icon sc-blue">
                <i class="fa fa-calendar"></i>
            </div>
            <div class="stat-card-body">
                <div class="stat-card-title">Pending Leaves</div>
                <div class="stat-card-value"><?php echo $pending_leaves; ?></div>
                <!-- <a href="index.php?view_leave_requests" class="stat-card-link">View Requests &rarr;</a> -->
            </div>
        </div>
    </div>
</div>

<!-- Middle Grid: Chart & Recent Projects -->
<div class="middle-grid">
    <div class="chart-panel">
        <div class="panel-title-row">
            <h3>Recent Projects</h3>
            <a href="index.php?projects" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> View All</a>
        </div>
        <table class="recent-leads-table" style="width: 100%;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-light);">
                    <th style="text-align:left; padding:12px 10px; font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">Project & Client</th>
                    <th style="text-align:center; padding:12px 10px; font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">Date</th>
                    <th style="text-align:center; padding:12px 10px; font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $q_recent_proj = "SELECT cp.*, c.name as client_name FROM client_projects cp JOIN clients c ON cp.client_id = c.id ORDER BY cp.id DESC LIMIT 4";
                $run_recent_proj = mysqli_query($con, $q_recent_proj);

                if ($run_recent_proj && mysqli_num_rows($run_recent_proj) > 0) {
                    while ($proj_row = mysqli_fetch_assoc($run_recent_proj)) {
                        $client_name  = htmlspecialchars($proj_row['client_name'] ?? 'N/A');
                        $project_name = htmlspecialchars($proj_row['project_name'] ?? '-');
                        $project_date = $proj_row['project_date'] ?? '';
                        $status = ucfirst(strtolower($proj_row['status'] ?? 'Active'));

                        // Status Badge styling
                        $badge_bg = '#f8fafc';
                        $badge_color = '#64748b';
                        if ($status === 'Active') {
                            $badge_bg = '#dcfce7';
                            $badge_color = '#16a34a';
                        } elseif ($status === 'Completed') {
                            $badge_bg = '#dbeafe';
                            $badge_color = '#2563eb';
                        } elseif ($status === 'Pending') {
                            $badge_bg = '#fef3c7';
                            $badge_color = '#d97706';
                        }

                        $date_formatted = !empty($project_date) ? date('d M Y', strtotime($project_date)) : '-';
                ?>
                        <tr style="transition: all 0.2s ease;">
                            <td style="padding: 12px 10px;">
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div style="width:40px; height:40px; border-radius:12px; background:#ffeaeb; display:flex; align-items:center; justify-content:center; font-size:16px; color:#dd2127; flex-shrink:0;">
                                        <i class="fa fa-folder-open"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight:800; color:var(--text-main); font-size:14px; margin-bottom: 3px;"><?php echo $project_name; ?></div>
                                        <div style="font-size:12px; color:var(--text-muted); font-weight: 600;">
                                            <i class="fa fa-user" style="font-size:10px; margin-right:4px;"></i><?php echo $client_name; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align:center; padding: 12px 10px;">
                                <span style="font-size:13px; font-weight:600; color:var(--text-muted);"><i class="fa fa-calendar-o" style="margin-right:4px; opacity: 0.7;"></i><?php echo $date_formatted; ?></span>
                            </td>
                            <td style="text-align:center; padding: 12px 10px;">
                                <span class="status-badge" style="background:<?php echo $badge_bg; ?>; color:<?php echo $badge_color; ?>; padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: 800; letter-spacing: 0.3px;">
                                    <?php echo $status; ?>
                                </span>
                            </td>
                        </tr>
                    <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="3" style="text-align:center; padding:40px; color:var(--text-muted);">
                            <div style="width: 60px; height: 60px; background: #f8fafc; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto;">
                                <i class="fa fa-folder-open-o" style="font-size:24px; color: #cbd5e1;"></i>
                            </div>
                            <div style="font-size: 14px; font-weight: 700; color: #64748b;">No Recent Projects</div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>


    <div class="projects-panel">
        <div class="panel-title-row">
            <h3>Recent Leads</h3>
            <a href="index.php?leads" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> View All</a>
        </div>
        <table class="recent-leads-table">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-light);">
                    <th style="text-align:left; padding:10px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">Client</th>
                    <th style="text-align:left; padding:10px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">Project</th>
                    <th style="text-align:center; padding:10px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">Follow-up</th>
                    <th style="text-align:center; padding:10px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $q_recent_leads = "SELECT * FROM leads ORDER BY id DESC LIMIT 3";
                $run_recent_leads = mysqli_query($con, $q_recent_leads);

                if ($run_recent_leads && mysqli_num_rows($run_recent_leads) > 0) {
                    while ($lead_row = mysqli_fetch_assoc($run_recent_leads)) {
                        $client_name  = htmlspecialchars($lead_row['client_name'] ?? '');
                        $company_name = htmlspecialchars($lead_row['company_name'] ?? '');
                        $project_name = htmlspecialchars($lead_row['project_name'] ?? '');
                        $followup_date = $lead_row['followup_date'] ?? '';
                        $status = strtolower($lead_row['status'] ?? '');

                        // Badge colors for lead statuses: active, future, expired
                        $badge_bg = '#f3f4f6';
                        $badge_color = '#6b7280';
                        if ($status === 'active') {
                            $badge_bg = '#dcfce7';
                            $badge_color = '#15803d';
                        } elseif ($status === 'future') {
                            $badge_bg = '#dbeafe';
                            $badge_color = '#2563eb';
                        } elseif ($status === 'expired') {
                            $badge_bg = '#fee2e2';
                            $badge_color = '#dc2626';
                        }

                        $followup_formatted = !empty($followup_date) ? date('d M Y', strtotime($followup_date)) : '-';
                ?>
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div style="width:34px; height:34px; border-radius:50%; background:#f1f5f9; display:flex; align-items:center; justify-content:center; font-size:13px; color:#64748b; font-weight:700; flex-shrink:0;">
                                        <?php echo strtoupper(substr($client_name, 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div style="font-weight:600; color:var(--text-main); font-size:13px;"><?php echo !empty($client_name) ? $client_name : 'N/A'; ?></div>
                                        <div style="font-size:11px; color:var(--text-muted);"><?php echo !empty($company_name) ? $company_name : ''; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-size:13px; font-weight:500; color:var(--text-main);"><?php echo !empty($project_name) ? $project_name : '-'; ?></span>
                            </td>
                            <td style="text-align:center;">
                                <span style="font-size:12px; font-weight:600; color:var(--text-muted);"><?php echo $followup_formatted; ?></span>
                            </td>
                            <td style="text-align:center;">
                                <span class="status-badge" style="background:<?php echo $badge_bg; ?>; color:<?php echo $badge_color; ?>;">
                                    <?php echo ucfirst($status); ?>
                                </span>
                            </td>
                        </tr>
                    <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding:30px; color:var(--text-muted);">
                            <i class="fa fa-inbox" style="font-size:24px; display:block; margin-bottom:8px; opacity:0.4;"></i>
                            No Recent Leads Found
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bottom Panel: Employee Work Log -->
<div class="bottom-panel">
    <div class="panel-title-row">
        <div style="display:flex; align-items:center; gap:10px;">
            <div class="panel-header-icon" style="background: #ffeaeb; color: #dd2127; width:32px; height:32px; font-size:14px; display:flex; align-items:center; justify-content:center; border-radius:8px;"><i class="fa fa-user"></i></div>
            <h3 style="font-size:18px;">Employee Work Log (Today)</h3>
        </div>
        <a href="index.php?attendance&daily=1&date=<?php echo $today; ?>" class="btn btn-info btn-sm" style="font-size: 13px;"><i class="fa fa-eye"></i> View Full Timesheet</a>
    </div>
    <table class="worklog-table">
        <thead>
            <tr>
                <th>Employee</th>
                <th>Role</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Total Work</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // We already have $today defined in dashboard.php around line 81
            $q_worklog = "SELECT a.*, e.name, e.employee_image FROM attendance a JOIN emp_list e ON a.emp_id = e.id WHERE a.attendance_date = '$today' ORDER BY a.check_in_time DESC LIMIT 5";
            $run_worklog = mysqli_query($con, $q_worklog);
            if ($run_worklog && mysqli_num_rows($run_worklog) > 0) {
                while ($att_row = mysqli_fetch_assoc($run_worklog)) {
                    $e_name = htmlspecialchars($att_row['name']);
                    $e_img = !empty($att_row['employee_image']) ? 'uploads/' . $att_row['employee_image'] : 'https://ui-avatars.com/api/?name=' . urlencode($e_name) . '&background=3b82f6&color=fff';
                    $check_in = !empty($att_row['check_in_time']) ? date('h:i A', strtotime($att_row['check_in_time'])) : '-';
                    $check_out = !empty($att_row['check_out_time']) ? date('h:i A', strtotime($att_row['check_out_time'])) : '-';
                    $status = htmlspecialchars($att_row['status']);

                    $total_secs = isset($att_row['total_duration_secs']) ? (int)$att_row['total_duration_secs'] : 0;
                    $is_working_val = isset($att_row['is_working']) ? (int)$att_row['is_working'] : 0;

                    // If total_secs is 0 but check_in and check_out exist, compute diff
                    if ($total_secs == 0 && !empty($att_row['check_out_time']) && !empty($att_row['check_in_time'])) {
                        $diff = strtotime($att_row['check_out_time']) - strtotime($att_row['check_in_time']);
                        if ($diff > 0) $total_secs = $diff;
                    }

                    // Currently working = present + no check-out yet (or is_working is 1)
                    $currently_working = ($status === 'present' && ($is_working_val == 1 || (empty($att_row['check_out_time']) && !empty($att_row['check_in_time']))));
                    $dot_color = $currently_working ? '#10b981' : '#ef4444';
                    $total_work_txt = '00h 00m';
                    if ($total_secs > 0) {
                        $h = floor($total_secs / 3600);
                        $m = floor(($total_secs % 3600) / 60);
                        $total_work_txt = sprintf('%02dh %02dm', $h, $m);
                    } elseif (empty($att_row['check_out_time']) && !empty($att_row['check_in_time'])) {
                        // Basic fallback calculation if total_secs is not maintained perfectly and still working
                        $in_time = strtotime($att_row['check_in_time']);
                        $now = time();
                        $diff = $now - $in_time;
                        if ($diff > 0) {
                            $h = floor($diff / 3600);
                            $m = floor(($diff % 3600) / 60);
                            $total_work_txt = sprintf('%02dh %02dm', $h, $m);
                        }
                    }

                    $badge_bg = 'var(--border-light)';
                    $badge_color = 'var(--text-muted)';
                    $badge_text = ucfirst($status);

                    if ($status == 'present') {
                        if ($currently_working) {
                            $badge_bg = 'var(--blue-light)';
                            $badge_color = 'var(--blue)';
                            $badge_text = 'Working';
                        } elseif (!empty($att_row['check_out_time'])) {
                            $badge_bg = 'var(--green-light)';
                            $badge_color = 'var(--green)';
                            $badge_text = 'Completed';
                        } else {
                            $badge_bg = 'var(--yellow-light)';
                            $badge_color = 'var(--yellow)';
                            $badge_text = 'Present';
                        }
                    } elseif ($status == 'absent') {
                        $badge_bg = 'var(--red-light)';
                        $badge_color = 'var(--red)';
                    } elseif ($status == 'leave') {
                        $badge_bg = 'var(--yellow-light)';
                        $badge_color = 'var(--yellow)';
                    }
            ?>
                    <tr>
                        <td style="text-align: center; vertical-align: middle;">
                            <div class="emp-info" style="justify-content: center;">
                                <img src="<?php echo $e_img; ?>" alt="<?php echo $e_name; ?>">
                                <span><?php echo $e_name; ?></span>
                            </div>
                        </td>
                        <td style="vertical-align: middle; text-align: center;"><span class="role-text">Employee</span></td>
                        <td style="vertical-align: middle; text-align: center;"><span class="check-in" style="color: var(--green); font-weight: 600;"><?php echo $check_in; ?></span></td>
                        <td style="vertical-align: middle; text-align: center;"><span class="check-out" style="color: <?php echo $check_out == '-' ? 'var(--text-muted)' : 'var(--red)'; ?>; font-weight: <?php echo $check_out == '-' ? '400' : '600'; ?>"><?php echo $check_out; ?></span></td>
                        <td style="vertical-align: middle; text-align: center;">
                            <div style="display:flex; align-items:center; justify-content: center; gap:7px;">
                                <?php if ($currently_working): ?>
                                    <span class="live-dot" title="Currently Working"></span>
                                <?php endif; ?>
                                <span class="total-work"><?php echo $total_work_txt; ?></span>
                            </div>
                        </td>
                        <td style="vertical-align: middle; text-align: center;"><span class="status-badge" style="background: <?php echo $badge_bg; ?>; color: <?php echo $badge_color; ?>; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:600;"><?php echo $badge_text; ?></span></td>
                    </tr>
            <?php
                }
            } else {
                echo '<tr><td colspan="6" style="text-align:center; padding:20px; color:var(--text-muted);">No attendance records for today</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>
</div>

<script>
    $(document).ready(function() {
        function formatDuration(seconds) {
            var h = Math.floor(seconds / 3600);
            var m = Math.floor((seconds % 3600) / 60);
            var s = seconds % 60;

            if (h > 0) {
                return h + "h " + m + "m " + s + "s";
            } else if (m > 0) {
                return m + "m " + s + "s";
            } else {
                return s + "s";
            }
        }

        function updateLiveTimers() {
            $('.duration-cell[data-is-working="1"]').each(function() {
                var $cell = $(this);
                var totalSecs = parseInt($cell.attr('data-total-secs')) || 0;
                var lastResumeStr = $cell.attr('data-last-resume');

                if (lastResumeStr) {
                    var lastResumeTime = new Date(lastResumeStr).getTime();
                    var now = new Date().getTime();
                    var elapsedSinceResume = Math.floor((now - lastResumeTime) / 1000);

                    if (elapsedSinceResume < 0) elapsedSinceResume = 0;

                    var currentTotal = totalSecs + elapsedSinceResume;
                    $cell.find('.duration-text').text(formatDuration(currentTotal));
                }
            });
        }

        function syncLiveStatus() {
            $.ajax({
                url: 'ajax/misc/ajax_get_live_status.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('.duration-cell').each(function() {
                        var $cell = $(this);
                        var empId = $cell.attr('data-emp-id');

                        if (data[empId]) {
                            var info = data[empId];

                            // Update data attributes
                            $cell.attr('data-is-working', info.is_working);
                            $cell.attr('data-total-secs', info.total_secs);
                            $cell.attr('data-last-resume', info.last_resume);

                            // Update Live Indicator
                            var hasIndicator = $cell.find('.live-indicator-wrapper').length > 0;
                            if (info.is_working == 1 && !hasIndicator) {
                                $cell.append('<span class="live-indicator-wrapper" title="Currently Working"><span class="live-indicator-circle"></span></span>');
                            } else if (info.is_working == 0 && hasIndicator) {
                                $cell.find('.live-indicator-wrapper').remove();
                            }

                            // Update Status Badge if needed
                            var $row = $cell.closest('tr');
                            var $statusBadge = $row.find('.status-pill');
                            if (info.status) {
                                var statusUpper = info.status.charAt(0).toUpperCase() + info.status.slice(1);
                                if ($statusBadge.text() != statusUpper) {
                                    $statusBadge.text(statusUpper);
                                    $statusBadge.attr('class', 'status-pill status-' + info.status);
                                }
                            }

                            // If not working, update duration text immediately
                            if (info.is_working == 0) {
                                $cell.find('.duration-text').text(formatDuration(info.total_secs));
                            }
                        }
                    });
                }
            });
        }

        // Update duration every second
        setInterval(updateLiveTimers, 1000);

        // Sync status from server every 10 seconds
        setInterval(syncLiveStatus, 10000);

        updateLiveTimers();
    });
</script>