<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

// Disable automatic exception throwing for connection attempts to handle errors gracefully
mysqli_report(MYSQLI_REPORT_OFF);

$con = false;
$max_retries = 3;
$retry_delay = 200000; // 200ms

for ($i = 0; $i < $max_retries; $i++) {
    $con = @mysqli_connect("localhost", "root", "", "8dots_crm", 3306);
    if ($con) {
        break;
    }
    usleep($retry_delay);
}

if (!$con) {
    // Attempt connecting without specifying port if host socket requires fallback
    $con = @mysqli_connect("localhost", "root", "", "8dots_crm");
}

if (!$con) {
    $error_msg = mysqli_connect_error() ?: "Operation not permitted / Connection limit exceeded";
    die("<div style='padding:20px; font-family:sans-serif; text-align:center;'>
            <h3 style='color:#e11d48;'>Database Connection Error</h3>
            <p style='color:#475569;'>The database server is temporarily busy or rejecting connections: <strong>" . htmlspecialchars($error_msg) . "</strong></p>
            <p><a href='' onclick='window.location.reload(); return false;' style='display:inline-block; padding:10px 20px; background:#2563eb; color:#fff; text-decoration:none; border-radius:6px;'>Click here to retry</a></p>
         </div>");
}

mysqli_query($con, "SET time_zone = '+05:30'");


