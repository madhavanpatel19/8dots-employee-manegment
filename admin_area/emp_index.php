<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION['emp_id'])) {
    echo "<script>window.open('emp-login.php','_self')</script>";
} else {
    $emp_id = $_SESSION['emp_id'];
    $emp_name = $_SESSION['emp_name'];
?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>8dots - Employee Dashboard</title>
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
        <link href="css/dashboard.css" rel="stylesheet">
        <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            /* Smooth transitions for dashboard panels */
            .panel { transition: all 0.3s ease; }
            .panel:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        </style>
    </head>
    <body>
        <div id="wrapper">
            <?php include("includes/emp_sidebar.php"); ?>
            <div id="page-wrapper">
                <div class="container-fluid">
                    <?php
                    if (isset($_GET['dashboard'])) {
                        include("emp_dashboard.php");
                    } elseif (isset($_GET['worksheet'])) {
                        $_GET['partial'] = true; // Flag for worksheet.php
                        include("worksheet.php");
                    } elseif (isset($_GET['leave_application'])) {
                        $_GET['partial'] = true; // Flag for leave_application.php
                        include("leave_application.php");
                    } elseif (isset($_GET['emp_salary_slip'])) {
                        $_GET['partial'] = true; // Flag for emp_salary_slip.php
                        include("emp_salary_slip.php");
                    } elseif (isset($_GET['view_announcement'])) {
                        include("view_announcement.php");
                    } else {
                        include("emp_dashboard.php");
                    }
                    ?>
                </div>
            </div>
        </div>
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
    </body>
    </html>
<?php } ?>
