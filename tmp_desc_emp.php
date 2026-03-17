<?php
include 'c:/xampp/htdocs/8dots/admin_area/includes/db.php';
function desc($table) {
    global $con;
    echo "--- $table ---" . PHP_EOL;
    $res = mysqli_query($con, "DESCRIBE $table");
    while ($row = mysqli_fetch_assoc($res)) {
        echo $row['Field'] . " (" . $row['Type'] . ")" . PHP_EOL;
    }
}
desc('emp_list');
?>
