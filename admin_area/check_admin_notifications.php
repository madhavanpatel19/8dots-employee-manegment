<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION['admin_email'])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit();
}

// Get the latest leave application ID from the database
$query = "SELECT l.id, l.reason, e.name as emp_name FROM leave_applications l JOIN emp_list e ON l.emp_id = e.id ORDER BY l.id DESC LIMIT 1";
$run = mysqli_query($con, $query);

if (mysqli_num_rows($run) > 0) {
    $row = mysqli_fetch_array($run);
    $latest_id = $row['id'];
    $emp_name = $row['emp_name'];
    $reason = $row['reason'];

    // If the session variable is not set, initialize it
    if (!isset($_SESSION['last_admin_leave_id'])) {
        $_SESSION['last_admin_leave_id'] = $latest_id;
        echo json_encode(["status" => "none"]);
        exit();
    }

    // Check if there's a new leave application
    if ($latest_id > $_SESSION['last_admin_leave_id']) {
        $_SESSION['last_admin_leave_id'] = $latest_id;
        echo json_encode([
            "status" => "new",
            "title" => "New Leave Request: " . $emp_name,
            "message" => "Reason: " . substr($reason, 0, 50) . "..."
        ]);
    } else {
        echo json_encode(["status" => "none"]);
    }
} else {
    echo json_encode(["status" => "none"]);
}
?>
