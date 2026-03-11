<?php
if (!isset($_SESSION['emp_id'])) {
    echo "<script>window.open('emp-login.php','_self')</script>";
} else {
    $emp_name = $_SESSION['emp_name'];
    $header_display_name = htmlspecialchars($emp_name);
?>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="emp_index.php?dashboard">8dots (Employee)</a>
        </div>
        <ul class="nav navbar-right top-nav">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-user"></i> <?php echo $header_display_name; ?>
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="emp-logout.php">
                            <i class="fa fa-fw fa-power-off"> </i> Log Out
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav side-nav">
                <li>
                    <a href="emp_index.php?dashboard">
                        <i class="fa fa-fw fa-dashboard"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="emp_index.php?worksheet">
                        <i class="fa fa-fw fa-table"></i> Worksheet
                    </a>
                </li>
                <li>
                    <a href="emp_index.php?leave_application">
                        <i class="fa fa-fw fa-paper-plane"></i> Leave Application
                    </a>
                </li>
                    <li>
                            <a href="emp_index.php?emp_salary_slip">
                                <i class="fa fa-fw fa-money"></i> Salary Slip
                            </a>
                    </li>
                <li>
                    <a href="emp-logout.php">
                        <i class="fa fa-fw fa-power-off"></i> Log Out
                    </a>
                </li>
            </ul>
        </div>
    </nav>
<?php } ?>
