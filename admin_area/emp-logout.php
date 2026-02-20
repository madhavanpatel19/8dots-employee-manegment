<?php

session_start();

session_destroy();

echo "<script>window.open('emp-login.php','_self')</script>";

?>