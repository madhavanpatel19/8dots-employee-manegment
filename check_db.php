<?php
include 'admin_area/includes/db.php';
$res = mysqli_query($con, 'DESCRIBE project_team_todos');
while ($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . "\n";
}
