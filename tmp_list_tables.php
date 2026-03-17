<?php
include 'c:/xampp/htdocs/8dots/admin_area/includes/db.php';
$res = mysqli_query($con, 'SHOW TABLES');
while ($row = mysqli_fetch_row($res)) {
    echo $row[0] . PHP_EOL;
}
?>
