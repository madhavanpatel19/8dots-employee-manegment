<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION['emp_id'])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit();
}

$emp_id = $_SESSION['emp_id'];

// Get the latest announcement ID from the database
$query = "SELECT id, title, message FROM announcements ORDER BY id DESC LIMIT 1";
$run = mysqli_query($con, $query);

if (mysqli_num_rows($run) > 0) {
    $row = mysqli_fetch_array($run);
    $latest_id = $row['id'];
    $title = $row['title'];
    $message = $row['message'];

    // If the session variable is not set, initialize it
    if (!isset($_SESSION['last_emp_announcement_id'])) {
        $_SESSION['last_emp_announcement_id'] = $latest_id;
        echo json_encode(["status" => "none"]);
        exit();
    }

    // Check if there's a new announcement
    if ($latest_id > $_SESSION['last_emp_announcement_id']) {
        $_SESSION['last_emp_announcement_id'] = $latest_id;
        echo json_encode([
            "status" => "new",
            "title" => $title,
            "message" => substr($message, 0, 100) . "..."
        ]);
    } else {
        echo json_encode(["status" => "none"]);
    }
} else {
    echo json_encode(["status" => "none"]);
}
?>
