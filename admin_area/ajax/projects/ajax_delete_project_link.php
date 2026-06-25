<?php
if (!isset($con)) { include(__DIR__ . '/../../includes/db.php'); }
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['link_id'])) {
    $link_id = mysqli_real_escape_string($con, $_POST['link_id']);
    
    $delete = "DELETE FROM project_links WHERE id = '$link_id'";
    if (mysqli_query($con, $delete)) {
        echo json_encode(['success' => true, 'message' => 'Link removed successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($con)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
