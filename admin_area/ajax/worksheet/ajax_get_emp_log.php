<?php
// =============================================================
// admin_area/ajax/worksheet/ajax_get_emp_log.php
// Returns attendance log details + per-segment timeline
// =============================================================
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($con)) {
    include(__DIR__ . '/../../includes/db.php');
}

header('Content-Type: application/json');

if (!isset($_SESSION['admin_email'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$att_id = isset($_GET['att_id']) ? intval($_GET['att_id']) : 0;
if ($att_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid record ID']);
    exit();
}

// Ensure attendance_logs table exists
@mysqli_query($con, "CREATE TABLE IF NOT EXISTS attendance_logs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    att_id      INT NOT NULL,
    emp_id      INT NOT NULL,
    action      VARCHAR(20) NOT NULL,
    action_time DATETIME NOT NULL,
    ip_address  VARCHAR(50) DEFAULT NULL,
    location    VARCHAR(255) DEFAULT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (att_id)
)");

// Fetch attendance record
$sql = "SELECT a.*, e.name AS emp_name, e.employee_image 
        FROM attendance a 
        LEFT JOIN emp_list e ON a.emp_id = e.id 
        WHERE a.id = $att_id LIMIT 1";
$result = mysqli_query($con, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo json_encode(['success' => false, 'message' => 'Record not found']);
    exit();
}

$row = mysqli_query($con, $sql) ? null : null;
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);

// Calculate live total duration
$duration_secs = (int)$row['total_duration_secs'];
$is_today = ($row['attendance_date'] == date('Y-m-d'));
$is_live  = false;
if ($is_today && $row['is_working'] == 1 && !empty($row['last_resume_time'])) {
    $duration_secs += time() - strtotime($row['last_resume_time']);
    $is_live = true;
}

// Format helper
function fmtDur($secs) {
    $h = floor($secs / 3600);
    $m = floor(($secs % 3600) / 60);
    $s = $secs % 60;
    return sprintf('%02dh %02dm %02ds', $h, $m, $s);
}

$duration_fmt = fmtDur($duration_secs);

// Fetch per-event logs
$logs_res = mysqli_query($con, "SELECT * FROM attendance_logs WHERE att_id = $att_id ORDER BY action_time ASC");
$events = [];
if ($logs_res) {
    while ($lr = mysqli_fetch_assoc($logs_res)) {
        $events[] = $lr;
    }
}

// Build segments: pair start events (check_in / resume) with end events (pause / check_out)
$segments = [];
$current_start = null;

foreach ($events as $ev) {
    if (in_array($ev['action'], ['check_in', 'resume'])) {
        $current_start = $ev;
    } elseif (in_array($ev['action'], ['pause', 'check_out']) && $current_start) {
        $start_ts = strtotime($current_start['action_time']);
        $end_ts   = strtotime($ev['action_time']);
        $seg_secs = max(0, $end_ts - $start_ts);
        $segments[] = [
            'seg_num'    => count($segments) + 1,
            'start_time' => date('h:i A', $start_ts),
            'end_time'   => date('h:i A', $end_ts),
            'end_action' => $ev['action'],
            'duration'   => fmtDur($seg_secs),
            'duration_secs' => $seg_secs,
            'start_ip'   => $current_start['ip_address'] ?? '-',
            'start_loc'  => $current_start['location'] ?? '-',
            'end_ip'     => $ev['ip_address'] ?? '-',
            'end_loc'    => $ev['location'] ?? '-',
        ];
        $current_start = null;
    }
}

// If timer is currently running (no matching end event yet), add a live "in-progress" segment
if ($current_start && $is_live) {
    $start_ts = strtotime($current_start['action_time']);
    $live_secs = time() - $start_ts;
    $segments[] = [
        'seg_num'    => count($segments) + 1,
        'start_time' => date('h:i A', $start_ts),
        'end_time'   => null,
        'end_action' => 'live',
        'duration'   => fmtDur($live_secs),
        'duration_secs' => $live_secs,
        'start_ip'   => $current_start['ip_address'] ?? '-',
        'start_loc'  => $current_start['location'] ?? '-',
        'end_ip'     => '-',
        'end_loc'    => '-',
    ];
}

echo json_encode([
    'success'         => true,
    'emp_name'        => $row['emp_name'],
    'attendance_date' => $row['attendance_date'],
    'check_in_time'   => $row['check_in_time']  ? date('h:i A', strtotime($row['check_in_time']))  : null,
    'check_out_time'  => $row['check_out_time'] ? date('h:i A', strtotime($row['check_out_time'])) : null,
    'duration_secs'   => $duration_secs,
    'duration_fmt'    => $duration_fmt,
    'is_working'      => (bool)$row['is_working'],
    'is_live'         => $is_live,
    'status'          => $row['status'],
    'ip_address'      => $row['ip_address'] ?? null,
    'location'        => $row['location'] ?? null,
    'segments'        => $segments,
    'has_logs'        => count($events) > 0,
]);
