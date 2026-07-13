<?php

if (session_status() == PHP_SESSION_NONE) { session_start(); }

session_destroy();

echo "<script>window.open('../../pages/auth/login.php','_self')</script>";

?>