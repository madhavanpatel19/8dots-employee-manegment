<?php
// =============================================================
// emp_area/pages/auth/logout.php
// Employee logout.
// Moved from: admin_area/pages/auth/emp-logout.php
// =============================================================
if (session_status() == PHP_SESSION_NONE) { session_start(); }

session_destroy();

echo "<script>window.open('../../pages/auth/login.php','_self')</script>";
?>
