<?php
include('c:/xampp/htdocs/8dots/admin_area/includes/db.php');
$res = mysqli_query($con, 'DESCRIBE project_team_todos');
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
?>
