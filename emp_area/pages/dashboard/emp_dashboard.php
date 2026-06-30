<?php
// =============================================================
// emp_area/pages/dashboard/emp_dashboard.php
// Employee dashboard – real DB data
// =============================================================
if (!isset($con) || !$con) {
    include(__DIR__ . '/../../includes/db.php');
}
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['emp_id'])) {
    echo "<script>window.open('../../pages/auth/login.php','_self')</script>";
    exit();
}

$emp_id   = (int)$_SESSION['emp_id'];
$emp_name = $_SESSION['emp_name'];
$today    = date('Y-m-d');

// ── Attendance today ──────────────────────────────────────────
$res          = mysqli_query($con, "SELECT * FROM attendance WHERE emp_id='$emp_id' AND attendance_date='$today'");
$today_record = mysqli_fetch_assoc($res);

// ── Latest Announcement ───────────────────────────────────────
$latest_announcement = "";
$has_announcement = false;
$ticker_res = mysqli_query($con, "SELECT title FROM announcements WHERE is_active=1
    AND (publish_date IS NULL OR publish_date <= NOW())
    AND (end_date IS NULL OR end_date >= NOW())
    ORDER BY publish_date DESC LIMIT 1");
if ($ticker_res && mysqli_num_rows($ticker_res) > 0) {
    $latest_announcement = htmlspecialchars(mysqli_fetch_array($ticker_res)['title']);
    $has_announcement = true;
}

// ── My Tasks (project_team_todos) ────────────────────────────
$tasks_res = mysqli_query(
    $con,
    "SELECT t.*, cp.project_name
     FROM project_team_todos t
     LEFT JOIN client_projects cp ON cp.id = t.project_id
     WHERE t.emp_id = '$emp_id' AND t.status = 0
     ORDER BY t.created_at DESC LIMIT 5"
);
$tasks = [];
$pending_task_count = 0;
if ($tasks_res) {
    while ($row = mysqli_fetch_assoc($tasks_res)) {
        $tasks[] = $row;
        if ((int)$row['status'] === 0) $pending_task_count++;
    }
}
$total_tasks = count($tasks);

// ── My Projects (assigned_employees contains emp_id) ─────────
$proj_res = mysqli_query(
    $con,
    "SELECT cp.*, cl.name AS client_name
     FROM client_projects cp
     LEFT JOIN clients cl ON cl.id = cp.client_id
     WHERE cp.status = 'Active'
       AND FIND_IN_SET('$emp_id', cp.assigned_employees) > 0
     ORDER BY cp.created_at DESC"
);
$projects = [];
if ($proj_res) {
    while ($row = mysqli_fetch_assoc($proj_res)) $projects[] = $row;
}
$active_proj_count = count($projects);

// ── This week's time log ──────────────────────────────────────
$week_start = date('Y-m-d', strtotime('monday this week'));
$week_res   = mysqli_query(
    $con,
    "SELECT attendance_date, total_duration_secs, is_working, last_resume_time
     FROM attendance WHERE emp_id='$emp_id'
       AND attendance_date BETWEEN '$week_start' AND '$today'
     ORDER BY attendance_date ASC"
);
$week_data = [];
$week_total_secs = 0;
if ($week_res) {
    while ($r = mysqli_fetch_assoc($week_res)) {
        $secs = (int)$r['total_duration_secs'];
        if ($r['attendance_date'] == $today && $r['is_working'] && $r['last_resume_time']) {
            $secs += time() - strtotime($r['last_resume_time']);
        }
        $week_data[$r['attendance_date']] = $secs;
        $week_total_secs += $secs;
    }
}
$week_days     = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
$week_secs_arr = [];
$displayed_week_mins = 0;
$base_past_mins = 0;
for ($i = 0; $i < 7; $i++) {
    $d = date('Y-m-d', strtotime("monday this week +{$i} days"));
    $s = $week_data[$d] ?? 0;
    $week_secs_arr[] = ['label' => $week_days[$i], 'secs' => $s, 'date' => $d];

    $mins = floor($s / 60);
    $displayed_week_mins += $mins;
    if ($d != $today) {
        $base_past_mins += $mins;
    }
}
$displayed_week_secs = $displayed_week_mins * 60;
$max_week = max(array_column($week_secs_arr, 'secs')) ?: 1;

// ── Today's logged duration ───────────────────────────────────
$today_secs = (int)($today_record['total_duration_secs'] ?? 0);
if ($today_record && $today_record['is_working'] && $today_record['last_resume_time']) {
    $today_secs += time() - strtotime($today_record['last_resume_time']);
}

function fmtHM($secs)
{
    $h = floor($secs / 3600);
    $m = floor(($secs % 3600) / 60);
    return "{$h}h " . str_pad($m, 2, '0', STR_PAD_LEFT) . "m";
}

function fmtHMS($secs)
{
    $h = floor($secs / 3600);
    $m = floor(($secs % 3600) / 60);
    $s = $secs % 60;
    return str_pad($h, 2, '0', STR_PAD_LEFT) . ":" . str_pad($m, 2, '0', STR_PAD_LEFT) . ":" . str_pad($s, 2, '0', STR_PAD_LEFT);
}
?>
<style>
    /* ═══════════════════════════════════════════════════════
   EMPLOYEE DASHBOARD STYLES
   ═══════════════════════════════════════════════════════ */
    .dash-wrap {
        padding: 0 24px 40px;
        font-family: 'Inter', sans-serif;
        background: #f9fafb;
        min-height: 100vh;
    }

    /* ── Announcement ── */
    .dash-announce {
        background: linear-gradient(90deg, #fff1f2, #fce7f3);
        border-left: 4px solid #e11d48;
        border-radius: 10px;
        padding: 11px 18px;
        font-size: 14px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 22px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .dash-announce i {
        color: #e11d48;
        font-size: 16px;
    }

    /* ── Stat Cards ── */
    .dash-cards {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 22px;
    }

    .dc {
        background: #fff;
        border: 1px solid #f3f4f6;
        border-radius: 14px;
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        position: relative;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        transition: box-shadow .2s, transform .2s;
        overflow: hidden;
    }

    .dc:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }

    .dc-icon {
        width: 50px;
        height: 50px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 21px;
        flex-shrink: 0;
    }

    .dc-icon.red {
        background: #ffe4e6;
        color: #e11d48;
    }

    .dc-icon.blue {
        background: #dbeafe;
        color: #2563eb;
    }

    .dc-icon.green {
        background: #d1fae5;
        color: #059669;
    }

    .dc-icon.orange {
        background: #ffedd5;
        color: #ea580c;
    }

    .dc-body h5 {
        margin: 0 0 2px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #9ca3af;
    }

    .dc-body h2 {
        margin: 0 0 2px;
        font-size: 26px;
        font-weight: 800;
        color: #111827;
        line-height: 1;
    }

    .dc-body p {
        margin: 0;
        font-size: 11px;
        color: #9ca3af;
    }

    .dc-link {
        position: absolute;
        bottom: 12px;
        right: 14px;
        font-size: 11px;
        font-weight: 700;
        color: #e11d48;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 3px;
        opacity: 0;
        transition: opacity .2s;
    }

    .dc:hover .dc-link {
        opacity: 1;
    }

    /* ── Attendance Card ── */
    .dc.att-dc {
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
        padding: 14px 16px;
    }

    .att-lbl {
        font-size: 11px;
        font-weight: 700;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .att-times {
        display: flex;
        gap: 6px;
    }

    .att-ti {
        flex: 1;
        background: #f9fafb;
        border: 1px solid #f3f4f6;
        border-radius: 8px;
        padding: 6px;
        text-align: center;
    }

    .att-ti span {
        display: block;
        font-size: 9px;
        font-weight: 700;
        color: #9ca3af;
        text-transform: uppercase;
    }

    .att-ti strong {
        font-size: 13px;
        font-weight: 700;
        color: #111827;
    }

    .att-ti.dur strong {
        color: #2563eb;
    }

    .att-btns {
        display: flex;
        gap: 7px;
    }

    .att-btn {
        flex: 1;
        border: none;
        border-radius: 9px;
        padding: 9px 0;
        font-weight: 700;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        cursor: pointer;
        color: #fff;
        transition: opacity .15s, transform .15s;
    }

    .att-btn:hover:not(:disabled) {
        opacity: .88;
        transform: translateY(-1px);
    }

    .att-btn:disabled {
        opacity: .5;
        cursor: not-allowed;
    }

    .ab-checkin {
        background: #10b981;
    }

    .ab-pause {
        background: #6b7280;
    }

    .ab-checkout {
        background: #f59e0b;
    }

    .ab-done {
        background: #e5e7eb;
        color: #6b7280;
    }

    /* ── Main grid ── */
    .dash-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 18px;
    }

    /* ── Section header ── */
    .sec-hd {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .sec-hd h3 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .sec-hd a {
        font-size: 12px;
        font-weight: 700;
        color: #e11d48;
        text-decoration: none;
        outline: none;
    }

    .sec-hd a:focus,
    .sec-hd a:active {
        outline: none !important;
        box-shadow: none !important;
    }

    /* ── Card box ── */
    .cbox {
        background: #fff;
        border: 1px solid #f3f4f6;
        border-radius: 14px;
        padding: 18px;
        margin-bottom: 18px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
    }

    /* ── Task item ── */
    .t-row {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 10px 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .t-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .t-chk {
        width: 20px;
        height: 20px;
        border: 2px solid #d1d5db;
        border-radius: 50%;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .t-chk.done {
        background: #10b981;
        border-color: #10b981;
    }

    .t-chk.done::after {
        content: '✓';
        color: #fff;
        font-size: 11px;
        font-weight: 700;
    }

    .t-info {
        flex: 1;
        min-width: 0;
    }

    .t-name {
        font-size: 13px;
        font-weight: 600;
        color: #1f2937;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        margin: 0;
    }

    .t-name.done-txt {
        text-decoration: line-through;
        color: #9ca3af;
    }

    .t-proj {
        font-size: 11px;
        color: #e11d48;
        margin: 2px 0 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .t-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: .04em;
        flex-shrink: 0;
    }

    .tb-high {
        background: #fee2e2;
        color: #dc2626;
    }

    .tb-medium {
        background: #fef3c7;
        color: #d97706;
    }

    .tb-low {
        background: #dcfce7;
        color: #16a34a;
    }

    .t-date {
        font-size: 11px;
        color: #9ca3af;
        flex-shrink: 0;
        min-width: 48px;
        text-align: right;
    }

    /* ── Project item ── */
    .p-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .p-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .p-av {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: #ffe4e6;
        color: #e11d48;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 800;
        flex-shrink: 0;
    }

    .p-info {
        flex: 1;
        min-width: 0;
    }

    .p-info h4 {
        margin: 0;
        font-size: 13px;
        font-weight: 700;
        color: #1f2937;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .p-info small {
        font-size: 11px;
        color: #9ca3af;
    }

    .p-dl {
        font-size: 11px;
        color: #6b7280;
        text-align: right;
        flex-shrink: 0;
    }

    /* ── Quick links ── */
    .ql-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px 10px;
        flex: 1;
        align-content: center;
    }

    .ql-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        color: #4b5563;
        font-size: 10px;
        font-weight: 600;
        text-align: center;
        transition: color .2s;
        outline: none;
    }

    .ql-item:focus,
    .ql-item:active {
        outline: none !important;
        box-shadow: none !important;
        text-decoration: none !important;
    }

    .ql-item:hover {
        color: #e11d48;
    }

    .ql-ic {
        width: 50px;
        height: 50px;
        background: #fff1f2;
        color: #e11d48;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        transition: transform .2s, box-shadow .2s;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    }

    .ql-item:hover .ql-ic {
        transform: scale(1.09);
        box-shadow: 0 4px 12px rgba(225, 29, 72, 0.2);
    }

    /* ── Time log chart ── */
    .tl-total {
        font-size: 28px;
        font-weight: 800;
        color: #1e293b;
        margin: 0 0 4px;
        line-height: 1.2;
    }

    .tl-sub {
        font-size: 13px;
        color: #64748b;
        margin: 0 0 20px;
        font-weight: 500;
    }

    .chart-box-premium {
        background: #ffffff;
        border: 1.5px solid #f1f5f9;
        border-radius: 16px;
        padding: 24px 16px 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
    }

    .chart-wrap {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        flex: 1;
        position: relative;
    }

    .bar-col {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        flex: 1;
        gap: 8px;
        height: 100%;
        position: relative;
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .bar-col:hover {
        transform: translateY(-3px);
    }

    .bar-val {
        font-size: 10px;
        color: #64748b;
        white-space: nowrap;
        font-weight: 700;
        position: absolute;
        top: 0;
        opacity: 0.8;
        transition: opacity 0.2s;
    }

    .bar-col:hover .bar-val {
        opacity: 1;
        color: #1e293b;
    }

    .bar-fill {
        width: 100%;
        max-width: 32px;
        border-radius: 8px;
        min-height: 4px;
        background: #fda4af;
        transition: height .6s cubic-bezier(0.4, 0, 0.2, 1), background 0.3s;
    }

    .bar-col:hover .bar-fill {
        background: #fb7185;
    }

    .bar-fill.today-bar {
        background: #e11d48;
        box-shadow: 0 4px 12px rgba(225, 29, 72, 0.25);
    }

    .bar-col:hover .bar-fill.today-bar {
        background: #be123c;
    }

    .bar-fill.empty-bar {
        background: #e2e8f0;
    }

    .bar-col:hover .bar-fill.empty-bar {
        background: #cbd5e1;
    }

    .bar-lbl {
        font-size: 11px;
        color: #94a3b8;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .bar-col.today-lbl .bar-lbl {
        color: #e11d48;
    }

    /* ── Empty state ── */
    .empty-s {
        text-align: center;
        padding: 22px 0;
        color: #9ca3af;
    }

    .empty-s i {
        font-size: 32px;
        display: block;
        margin-bottom: 8px;
        opacity: .3;
    }

    /* ── Scrollbars ── */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* ── Responsive ── */
    @media (max-width:1100px) {
        .dash-grid {
            grid-template-columns: 1fr;
        }

        .dash-cards {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width:600px) {
        .dash-cards {
            grid-template-columns: 1fr;
        }

        .dash-wrap {
            padding: 0 12px 30px;
        }

        .ql-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }
</style>

<div class="dash-wrap">

    <!-- Announcement -->
    <?php if ($has_announcement): ?>
        <div class="dash-announce">
            <i class="fa fa-bullhorn" style="flex-shrink: 0;"></i>
            <marquee scrollamount="5" scrolldelay="50" onmouseover="this.stop();" onmouseout="this.start();" style="margin: 0; padding: 0;">
                <?php echo $latest_announcement; ?>
            </marquee>
        </div>
    <?php endif; ?>

    <!-- ── Stat Cards ── -->
    <div class="dash-cards">

        <!-- Tasks -->
        <div class="dc">
            <div class="dc-icon red"><i class="fa fa-tasks"></i></div>
            <div class="dc-body">
                <h5>My Tasks</h5>
                <h2><?php echo $pending_task_count; ?></h2>
                <p><?php echo $total_tasks; ?> total assigned</p>
            </div>
            <a href="#taskSec" class="dc-link">View <i class="fa fa-arrow-right"></i></a>
        </div>

        <!-- Projects -->
        <div class="dc">
            <div class="dc-icon blue"><i class="fa fa-briefcase"></i></div>
            <div class="dc-body">
                <h5>Active Projects</h5>
                <h2><?php echo $active_proj_count; ?></h2>
                <p>Ongoing projects</p>
            </div>
            <a href="#projSec" class="dc-link">View <i class="fa fa-arrow-right"></i></a>
        </div>

        <!-- Today Work Log -->
        <!-- <div class="dc">
            <div class="dc-icon green"><i class="fa fa-clock-o"></i></div>
            <div class="dc-body">
                <h5>Today's Work Log</h5>
                <h2 id="displayDurationCard"><?php echo $today_secs > 0 ? fmtHM($today_secs) : '--:--'; ?></h2>
                <p>Logged today</p>
            </div>
            <a href="index.php?worksheet" class="dc-link">View <i class="fa fa-arrow-right"></i></a>
        </div> -->

        <!-- Attendance Widget -->
        <div class="dc att-dc">
            <div class="att-lbl"><i class="fa fa-calendar-check-o"></i> Attendance</div>
            <div class="att-times">
                <div class="att-ti">
                    <span>In</span>
                    <strong id="displayCheckIn"><?php echo ($today_record && $today_record['check_in_time']) ? date('h:i A', strtotime($today_record['check_in_time'])) : '--:--'; ?></strong>
                </div>
                <div class="att-ti">
                    <span>Out</span>
                    <strong id="displayCheckOut"><?php echo ($today_record && $today_record['check_out_time']) ? date('h:i A', strtotime($today_record['check_out_time'])) : '--:--'; ?></strong>
                </div>
                <div class="att-ti dur">
                    <span>Dur</span>
                    <strong id="displayDuration"><?php echo $today_secs > 0 ? fmtHMS($today_secs) : '00:00:00'; ?></strong>
                </div>
            </div>
            <div class="att-btns">
                <?php if (!$today_record || empty($today_record['check_in_time'])): ?>
                    <button id="btnCheckIn" class="att-btn ab-checkin"><i class="fa fa-play"></i> Check In</button>
                <?php elseif (empty($today_record['check_out_time'])): ?>
                    <?php if ($today_record['is_working']): ?>
                        <button id="btnPause" class="att-btn ab-pause"><i class="fa fa-pause"></i> Pause</button>
                    <?php else: ?>
                        <button id="btnResume" class="att-btn ab-checkin"><i class="fa fa-play"></i> Resume</button>
                    <?php endif; ?>
                    <button id="btnCheckOut" class="att-btn ab-checkout"><i class="fa fa-stop"></i> Out</button>
                <?php else: ?>
                    <button class="att-btn ab-done" disabled><i class="fa fa-check"></i> Completed</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ── Main Grid ── -->
    <div class="dash-grid">
        <!-- My Tasks -->
        <div class="cbox" style="margin-bottom: 0;">
            <div class="sec-hd" id="taskSec">
                <h3><i class="fa fa-tasks" style="color:#e11d48;"></i> My Tasks</h3>
                <a href="#">View All</a>
            </div>
            <div id="emptyTasksMsg" class="empty-s" style="<?php echo empty($tasks) ? '' : 'display:none;'; ?>">
                <i class="fa fa-check-circle"></i>You're all caught up! No pending tasks.
            </div>
            <div style="max-height: 220px; overflow-y: auto; padding-right: 5px;" class="custom-scrollbar">
                <?php foreach ($tasks as $task):
                    $done     = (int)$task['status'] === 1;
                    $priority = strtolower($task['priority'] ?? 'low');
                    $due      = !empty($task['due_date']) ? date('d M', strtotime($task['due_date'])) : '';
                    $pname    = $task['project_name'] ?: 'General';
                    $task_id  = $task['id'];
                ?>
                    <div class="t-row" id="taskRow_<?php echo $task_id; ?>" style="cursor: pointer;" onclick="completeTask(<?php echo $task_id; ?>)">
                        <div class="t-chk <?php echo $done ? 'done' : ''; ?>"></div>
                        <div class="t-info">
                            <div class="t-name <?php echo $done ? 'done-txt' : ''; ?>"><?php echo htmlspecialchars($task['task_name']); ?></div>
                            <div class="t-proj"><?php echo htmlspecialchars($pname); ?></div>
                        </div>
                        <span class="t-badge tb-<?php echo $priority; ?>"><?php echo ucfirst($priority); ?></span>
                        <?php if ($due): ?><div class="t-date"><?php echo $due; ?></div><?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="cbox" style="margin-bottom: 0;">
            <div class="sec-hd">
                <h3><i class="fa fa-th" style="color:#e11d48;"></i> Quick Links</h3>
            </div>
            <div class="ql-grid">
                <a href="#taskSec" class="ql-item">
                    <div class="ql-ic"><i class="fa fa-tasks"></i></div>My Tasks
                </a>
                <a href="index.php?worksheet" class="ql-item">
                    <div class="ql-ic"><i class="fa fa-file-text-o"></i></div>Worksheet
                </a>
                <a href="#projSec" class="ql-item">
                    <div class="ql-ic"><i class="fa fa-briefcase"></i></div>Projects
                </a>
                <a href="index.php?worksheet" class="ql-item">
                    <div class="ql-ic"><i class="fa fa-calendar-check-o"></i></div>Attendance
                </a>
                <a href="index.php?leave_application" class="ql-item">
                    <div class="ql-ic"><i class="fa fa-paper-plane-o"></i></div>Leave App
                </a>
                <a href="index.php?emp_salary_slip" class="ql-item">
                    <div class="ql-ic"><i class="fa fa-money"></i></div>Salary Slip
                </a>
                <a href="index.php?emp_profile" class="ql-item">
                    <div class="ql-ic"><i class="fa fa-user"></i></div>My Profile
                </a>
                <a href="index.php?worksheet" class="ql-item">
                    <div class="ql-ic"><i class="fa fa-clock-o"></i></div>Time Log
                </a>
            </div>
        </div>

        <!-- My Projects -->
        <div class="cbox" style="margin-bottom: 0;">
            <div class="sec-hd" id="projSec">
                <h3><i class="fa fa-briefcase" style="color:#2563eb;"></i> My Projects</h3>
                <a href="#">View All</a>
            </div>
            <?php if (empty($projects)): ?>
                <div class="empty-s"><i class="fa fa-folder-open-o"></i>No active projects assigned to you.</div>
            <?php else: ?>
                <div style="max-height: 320px; overflow-y: auto; padding-right: 5px;" class="custom-scrollbar">
                    <?php foreach ($projects as $proj):
                        $init = strtoupper(mb_substr($proj['project_name'], 0, 2));
                        $dl   = !empty($proj['deadline']) ? date('d M Y', strtotime($proj['deadline'])) : 'No deadline';
                    ?>
                        <div class="p-row">
                            <div class="p-av"><?php echo $init; ?></div>
                            <div class="p-info">
                                <h4><?php echo htmlspecialchars($proj['project_name']); ?></h4>
                                <small><?php echo htmlspecialchars($proj['client_name'] ?? 'Client'); ?></small>
                            </div>
                            <div class="p-dl"><?php echo $dl; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- This Week's Time Log -->
        <div class="cbox" style="margin-bottom: 0;">
            <div class="sec-hd">
                <h3><i class="fa fa-bar-chart" style="color:#e11d48;"></i> Time Log <span style="font-size:11px;color:#9ca3af;font-weight:400;">(This Week)</span></h3>
                <a href="index.php?worksheet">View All</a>
            </div>
            <p class="tl-total" id="weekTotalLabel"><?php echo fmtHM($displayed_week_secs); ?></p>
            <p class="tl-sub">Total logged this week</p>
            <?php if ($week_total_secs > 0): ?>
                <div class="chart-box-premium">
                    <div class="chart-wrap">
                        <?php foreach ($week_secs_arr as $day):
                            $pct     = $max_week > 0 ? ($day['secs'] / $max_week) * 95 : 0;
                            $barH    = max(4, $pct);
                            $isToday = ($day['date'] == $today);
                            $isEmpty = ($day['secs'] == 0);
                            $cls     = $isToday ? 'today-bar' : ($isEmpty ? 'empty-bar' : '');
                        ?>
                            <div class="bar-col <?php echo $isToday ? 'today-lbl' : ''; ?>">
                                <span class="bar-val" <?php echo $isToday ? 'id="todayBarVal"' : ''; ?>><?php echo fmtHM($day['secs']); ?></span>
                                <div class="bar-fill <?php echo $cls; ?>" <?php echo $isToday ? 'id="todayBarFill"' : ''; ?> style="height:<?php echo $barH; ?>px;"></div>
                                <span class="bar-lbl"><?php echo $day['label']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="empty-s"><i class="fa fa-bar-chart"></i>No time logged this week yet.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var AJAX_URL = '../admin_area/ajax/attendance/ajax_handle_attendance.php';

        var att = {
            isWorking: <?php echo ($today_record && $today_record['is_working']) ? 'true' : 'false'; ?>,
            totalSecs: <?php echo ($today_record && $today_record['total_duration_secs']) ? (int)$today_record['total_duration_secs'] : 0; ?>,
            lastResume: <?php echo ($today_record && $today_record['last_resume_time']) ? (strtotime($today_record['last_resume_time']) * 1000) : 'null'; ?>,
            checkedOut: <?php echo ($today_record && !empty($today_record['check_out_time'])) ? 'true' : 'false'; ?>,
            basePastMins: <?php echo (int)$base_past_mins; ?>,
            maxWeek: <?php echo $max_week > 0 ? $max_week : 1; ?>
        };

        function fmtDur(s) {
            var h = Math.floor(s / 3600),
                m = Math.floor((s % 3600) / 60);
            return String(h) + 'h ' + String(m).padStart(2, '0') + 'm';
        }

        function fmtHMS(s) {
            var h = Math.floor(s / 3600),
                m = Math.floor((s % 3600) / 60),
                sec = Math.floor(s % 60);
            return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(sec).padStart(2, '0');
        }

        function tick() {
            if (att.checkedOut) return;
            var cur = att.totalSecs;
            var activeSecs = 0;
            if (att.isWorking && att.lastResume) {
                activeSecs = Math.floor((Date.now() - att.lastResume) / 1000);
            }
            cur += activeSecs;

            if (cur > 0) {
                var t = fmtDur(cur);
                var tHMS = fmtHMS(cur);
                $('#displayDuration,#displayDurationCard').text(tHMS);

                var todayEl = $('#todayBarVal');
                if (todayEl.length) {
                    todayEl.text(t);
                    var pct = (cur / att.maxWeek) * 95;
                    var barH = Math.max(4, pct);
                    $('#todayBarFill').css('height', barH + 'px');
                }
                var weekEl = $('#weekTotalLabel');
                if (weekEl.length) {
                    var totalMins = att.basePastMins + Math.floor(cur / 60);
                    weekEl.text(fmtDur(totalMins * 60));
                }
            }
        }
        if (!att.checkedOut) {
            setInterval(tick, 1000);
            tick();
        } else {
            $('#displayDuration,#displayDurationCard').text(fmtHMS(att.totalSecs));
        }

        function ajaxAtt(action, $btn, resetHtml) {
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
            $.ajax({
                url: AJAX_URL,
                type: 'POST',
                data: {
                    action: action
                },
                dataType: 'json',
                success: function(r) {
                    if (r.status === 'success') location.reload();
                    else {
                        $btn.prop('disabled', false).html(resetHtml);
                        Swal.fire('Notification', r.message, 'info');
                    }
                }
            });
        }

        $(document).on('click', '#btnCheckIn', function() {
            ajaxAtt('check_in', $(this), '<i class="fa fa-play"></i> Check In');
        });
        $(document).on('click', '#btnPause', function() {
            ajaxAtt('pause', $(this), '<i class="fa fa-pause"></i> Pause');
        });
        $(document).on('click', '#btnResume', function() {
            ajaxAtt('resume', $(this), '<i class="fa fa-play"></i> Resume');
        });

        /* Check Out – open modal */
        $(document).on('click', '#btnCheckOut', function() {
            var now = new Date();
            $('#modalCheckOutTime').val(String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0'));
            var ci = $('#displayCheckIn').text().trim();
            if (ci !== '--:--') {
                var m = ci.match(/(\d+):(\d+)\s*(AM|PM)/i);
                if (m) {
                    var h = parseInt(m[1]);
                    if (m[3].toUpperCase() === 'PM' && h < 12) h += 12;
                    if (m[3].toUpperCase() === 'AM' && h === 12) h = 0;
                    $('#modalCheckInTime').val(String(h).padStart(2, '0') + ':' + m[2]);
                }
            }
            $('#checkOutModal').modal('show');
        });

        /* Confirm Check Out */
        $(document).on('click', '#confirmCheckOut', function() {
            var wd = $('#workDetails').val().trim();
            if (!wd) {
                Swal.fire('Notification', 'Please provide work details.', 'info');
                return;
            }
            if (!$('#modalCheckInTime').val() || !$('#modalCheckOutTime').val()) {
                Swal.fire('Notification', 'Check-in and check-out times required.', 'info');
                return;
            }
            var $btn = $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
            $('#btnCancelCheckOut').prop('disabled', true);
            var fd = new FormData();
            fd.append('action', 'check_out');
            fd.append('work_details', wd);
            fd.append('check_in_time', $('#modalCheckInTime').val());
            fd.append('check_out_time', $('#modalCheckOutTime').val());
            for (var i = 1; i <= 4; i++) {
                var fi = document.getElementById('work_photo_' + i);
                if (fi && fi.files[0]) fd.append('work_photos[]', fi.files[0]);
            }
            $.ajax({
                url: AJAX_URL,
                type: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(r) {
                    if (r.status === 'success') location.reload();
                    else {
                        $btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Submit Worksheet & Check Out');
                        $('#btnCancelCheckOut').prop('disabled', false);
                        Swal.fire('Notification', r.message, 'info');
                    }
                }
            });
        });

        window.previewWorkPhoto = function(input, id) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview_' + id).attr('src', e.target.result).show();
                    $('#box_' + id).find('i').hide();
                };
                reader.readAsDataURL(input.files[0]);
            }
        };
    });

    function completeTask(taskId) {
        var $row = $('#taskRow_' + taskId);
        var $chk = $row.find('.t-chk');
        var $txt = $row.find('.t-name');
        $chk.addClass('done');
        $txt.addClass('done-txt');

        $.ajax({
            url: 'ajax_toggle_todo.php',
            type: 'POST',
            data: {
                task_id: taskId
            },
            dataType: 'json',
            success: function(r) {
                if (r.success) {
                    $row.slideUp(300, function() {
                        $(this).remove();
                        if ($('.t-row').length === 0) {
                            $('#emptyTasksMsg').fadeIn(300);
                        }
                    });
                } else {
                    alert(r.message || 'Failed to complete task.');
                    $chk.removeClass('done');
                    $txt.removeClass('done-txt');
                }
            },
            error: function() {
                Swal.fire('Notification', 'Network error while completing task.', 'error');
                $chk.removeClass('done');
                $txt.removeClass('done-txt');
            }
        });
    }
</script>

<!-- Check-Out Modal -->
<div class="modal fade" id="checkOutModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:18px;overflow:hidden;border:none;box-shadow:0 25px 60px rgba(0,0,0,.2);">
            <div class="modal-header" style="background:#1f2937;color:#fff;padding:16px 22px;border:none;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.8;"><span>&times;</span></button>
                <h4 class="modal-title" style="font-weight:700;font-size:14px;letter-spacing:.04em;">
                    <i class="fa fa-pencil-square-o"></i> Submit Worksheet & Check Out
                </h4>
            </div>
            <div class="modal-body" style="padding:22px;">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label class="col-md-4 control-label" style="text-align:left;color:#64748b;font-weight:600;">Date</label>
                        <div class="col-md-8"><input type="date" class="form-control" style="border-radius:10px;border:1px solid #e2e8f0;background:#f8fafc;" value="<?php echo date('Y-m-d'); ?>" readonly></div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label" style="text-align:left;color:#64748b;font-weight:600;">Check-in <span class="text-danger">*</span></label>
                        <div class="col-md-8"><input type="time" id="modalCheckInTime" class="form-control" style="border-radius:10px;border:1px solid #e2e8f0;" required></div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label" style="text-align:left;color:#64748b;font-weight:600;">Check-out <span class="text-danger">*</span></label>
                        <div class="col-md-8"><input type="time" id="modalCheckOutTime" class="form-control" style="border-radius:10px;border:1px solid #e2e8f0;" required></div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label" style="text-align:left;color:#64748b;font-weight:600;">Work Details <span class="text-danger">*</span></label>
                        <div class="col-md-8"><textarea id="workDetails" class="form-control" style="height:90px;border-radius:10px;border:1px solid #e2e8f0;resize:none;" placeholder="What did you accomplish today?" required></textarea></div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label" style="text-align:left;color:#64748b;font-weight:600;">Work Photos</label>
                        <div class="col-md-8">
                            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                                <?php for ($id = 1; $id <= 4; $id++): ?>
                                    <div id="box_<?php echo $id; ?>" onclick="document.getElementById('work_photo_<?php echo $id; ?>').click()"
                                        style="width:70px;height:70px;border:2px dashed #cbd5e1;border-radius:12px;display:flex;align-items:center;justify-content:center;cursor:pointer;position:relative;overflow:hidden;background:#f8fafc;">
                                        <i class="fa fa-plus" style="color:#94a3b8;font-size:18px;"></i>
                                        <input type="file" id="work_photo_<?php echo $id; ?>" style="display:none;" accept="image/*" onchange="previewWorkPhoto(this,<?php echo $id; ?>)">
                                        <img id="preview_<?php echo $id; ?>" src="" style="display:none;width:100%;height:100%;object-fit:cover;position:absolute;top:0;left:0;">
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" style="margin-top:22px;margin-bottom:0;">
                        <div class="col-md-12" style="display:flex;gap:10px;justify-content:flex-end;">
                            <button type="button" id="btnCancelCheckOut" class="btn btn-default" data-dismiss="modal" style="border-radius:10px;padding:10px 18px;font-weight:600;">Cancel</button>
                            <button type="button" id="confirmCheckOut" class="btn btn-primary" style="background:#1f2937;border:none;border-radius:10px;padding:10px 20px;font-weight:700;">
                                <i class="fa fa-paper-plane"></i> Submit &amp; Check Out
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>