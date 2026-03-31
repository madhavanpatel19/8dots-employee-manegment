<?php
$con = mysqli_connect('localhost', 'root', '', '8dots');
if (!$con) {
    echo "Connection failed: " . mysqli_connect_error();
    exit;
}
$check = mysqli_query($con, "SHOW COLUMNS FROM emp_list LIKE 'employee_image'");
if (mysqli_num_rows($check) == 0) {
    $res = mysqli_query($con, "ALTER TABLE emp_list ADD COLUMN employee_image VARCHAR(255) AFTER id");
    if ($res) {
        echo "Column employee_image added successfully.\n";
    } else {
        echo "Error: " . mysqli_error($con) . "\n";
    }
} else {
    echo "Column employee_image already exists.\n";
}
?>
