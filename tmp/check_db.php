<?php
$con = mysqli_connect('localhost', 'root', '', '8dots');
if (!$con) {
    echo "Connection failed: " . mysqli_connect_error();
    exit;
}
$result = mysqli_query($con, "DESCRIBE emp_list");
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . "\n";
}
?>
