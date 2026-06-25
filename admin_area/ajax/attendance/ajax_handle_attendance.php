<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($con)) {
    include(__DIR__ . '/../../includes/db.php');
}

if (!isset($_SESSION['emp_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Session expired. Please login again.']);
    exit();
}

$emp_id = $_SESSION['emp_id'];
$action = isset($_POST['action']) ? $_POST['action'] : '';
$today = date('Y-m-d');
$current_time = date('H:i:s');
$now_dt = date('Y-m-d H:i:s');
$formatted_time = date('h:i A', strtotime($current_time));

if ($action == 'check_in') {
    $check_q = "SELECT * FROM attendance WHERE emp_id = '$emp_id' AND attendance_date = '$today'";
    $check_res = mysqli_query($con, $check_q);

    if (mysqli_num_rows($check_res) > 0) {
        $row = mysqli_fetch_assoc($check_res);
        if (!empty($row['check_in_time'])) {
            echo json_encode(['status' => 'info', 'message' => 'Already checked in.', 'time' => date('h:i A', strtotime($row['check_in_time']))]);
            exit();
        }
        $update_q = "UPDATE attendance SET 
                     check_in_time = '$current_time', 
                     last_resume_time = '$now_dt', 
                     is_working = 1, 
                     total_duration_secs = 0, 
                     status = 'present' 
                     WHERE id = " . $row['id'];
        if (mysqli_query($con, $update_q)) {
            echo json_encode(['status' => 'success', 'message' => 'Checked in!', 'time' => $formatted_time]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . mysqli_error($con)]);
        }
    } else {
        $insert_q = "INSERT INTO attendance (emp_id, attendance_date, check_in_time, last_resume_time, is_working, total_duration_secs, status) 
                     VALUES ('$emp_id', '$today', '$current_time', '$now_dt', 1, 0, 'present')";
        if (mysqli_query($con, $insert_q)) {
            echo json_encode(['status' => 'success', 'message' => 'Checked in!', 'time' => $formatted_time]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . mysqli_error($con)]);
        }
    }
} elseif ($action == 'pause') {
    $check_q = "SELECT * FROM attendance WHERE emp_id = '$emp_id' AND attendance_date = '$today' AND is_working = 1";
    $check_res = mysqli_query($con, $check_q);

    if (mysqli_num_rows($check_res) > 0) {
        $row = mysqli_fetch_assoc($check_res);
        $last_resume = $row['last_resume_time'];
        $diff = strtotime($now_dt) - strtotime($last_resume);

        $update_q = "UPDATE attendance SET 
                     total_duration_secs = total_duration_secs + $diff, 
                     is_working = 0 
                     WHERE id = " . $row['id'];
        if (mysqli_query($con, $update_q)) {
            echo json_encode(['status' => 'success', 'message' => 'Timer paused.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . mysqli_error($con)]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Not currently working.']);
    }
} elseif ($action == 'resume') {
    $check_q = "SELECT * FROM attendance WHERE emp_id = '$emp_id' AND attendance_date = '$today' AND is_working = 0 AND check_in_time IS NOT NULL AND check_out_time IS NULL";
    $check_res = mysqli_query($con, $check_q);

    if (mysqli_num_rows($check_res) > 0) {
        $row = mysqli_fetch_assoc($check_res);
        $update_q = "UPDATE attendance SET last_resume_time = '$now_dt', is_working = 1 WHERE id = " . $row['id'];
        if (mysqli_query($con, $update_q)) {
            echo json_encode(['status' => 'success', 'message' => 'Timer resumed.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . mysqli_error($con)]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Cannot resume. Check if you are already checked out.']);
    }
} elseif ($action == 'check_out') {
    $check_q = "SELECT * FROM attendance WHERE emp_id = '$emp_id' AND attendance_date = '$today'";
    $check_res = mysqli_query($con, $check_q);

    if (mysqli_num_rows($check_res) > 0) {
        $row = mysqli_fetch_assoc($check_res);
        if (!empty($row['check_out_time'])) {
            echo json_encode(['status' => 'info', 'message' => 'Already checked out.', 'time' => date('h:i A', strtotime($row['check_out_time']))]);
            exit();
        }

        $work_details = isset($_POST['work_details']) ? mysqli_real_escape_string($con, $_POST['work_details']) : '';
        $manual_in = isset($_POST['check_in_time']) ? $_POST['check_in_time'] : '';
        $manual_out = isset($_POST['check_out_time']) ? $_POST['check_out_time'] : '';

        // If manual times are provided, recalculate duration
        if (!empty($manual_in) && !empty($manual_out)) {
            $original_in = $row['check_in_time'];
            $last_resume = $row['last_resume_time'];

            // 1. Calculate base timer duration up to manual_out
            $base_timer = (int)$row['total_duration_secs'];
            if ($row['is_working'] == 1) {
                // Use the manual_out time as the 'now' for the current working segment
                $manual_out_dt = $today . ' ' . $manual_out;
                $segment_duration = strtotime($manual_out_dt) - strtotime($last_resume);
                $base_timer += max(0, $segment_duration);
            }

            // 2. Adjust for manual check-in change if any
            // (If user says they started later than original, subtract that diff; if earlier, add it)
            $in_diff = strtotime($today . ' ' . $manual_in) - strtotime($today . ' ' . $original_in);
            $new_duration = max(0, $base_timer - $in_diff);

            // Handle Work Photos Upload
            $uploaded_photos = [];
            if (isset($_FILES['work_photos'])) {
                $files = $_FILES['work_photos'];
                $upload_dir = '../../work_photos/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['error'][$i] == 0) {
                        $tmp_name = $files['tmp_name'][$i];
                        $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                        $new_name = $emp_id . '_' . $today . '_' . time() . '_' . $i . '.' . $ext;
                        $target = $upload_dir . $new_name;
                        if (move_uploaded_file($tmp_name, $target)) $uploaded_photos[] = 'work_photos/' . $new_name;
                    }
                }
            }
            $photos_json = !empty($uploaded_photos) ? mysqli_real_escape_string($con, json_encode($uploaded_photos)) : '';

            $update_q = "UPDATE attendance SET 
                         check_in_time = '$manual_in',
                         check_out_time = '$manual_out', 
                         is_working = 0,
                         total_duration_secs = '$new_duration',
                         remarks = '$work_details',
                         work_photos = '$photos_json'
                         WHERE id = " . $row['id'];
        } else {
            // Fallback to live timer logic
            $duration_update = "";
            if ($row['is_working'] == 1) {
                $last_resume = $row['last_resume_time'];
                $diff = strtotime($now_dt) - strtotime($last_resume);
                $duration_update = ", total_duration_secs = total_duration_secs + $diff";
            }

            // Handle Work Photos Upload (for fallback logic)
            $uploaded_photos = [];
            if (isset($_FILES['work_photos'])) {
                $files = $_FILES['work_photos'];
                $upload_dir = '../../work_photos/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['error'][$i] == 0) {
                        $tmp_name = $files['tmp_name'][$i];
                        $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                        $new_name = $emp_id . '_' . $today . '_' . time() . '_' . $i . '.' . $ext;
                        $target = $upload_dir . $new_name;
                        if (move_uploaded_file($tmp_name, $target)) $uploaded_photos[] = 'work_photos/' . $new_name;
                    }
                }
            }
            $photos_json = !empty($uploaded_photos) ? mysqli_real_escape_string($con, json_encode($uploaded_photos)) : '';

            $update_q = "UPDATE attendance SET 
                         check_out_time = '$current_time', 
                         is_working = 0,
                         remarks = '$work_details',
                         work_photos = '$photos_json'
                         $duration_update 
                         WHERE id = " . $row['id'];
        }

        if (mysqli_query($con, $update_q)) {
            echo json_encode(['status' => 'success', 'message' => 'Checked out!', 'time' => $formatted_time]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . mysqli_error($con)]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Record not found.']);
    }
}
