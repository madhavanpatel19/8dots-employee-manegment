<?php
if (!isset($con)) { include(__DIR__ . '/../../includes/db.php'); }
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $project_id = mysqli_real_escape_string($con, $_POST['project_id']);
    $link_name = mysqli_real_escape_string($con, $_POST['link_name']);
    $link_url = mysqli_real_escape_string($con, $_POST['link_url']);
    
    if (!empty($link_name) && !empty($link_url)) {
        $insert = "INSERT INTO project_links (project_id, link_name, link_url) VALUES ('$project_id', '$link_name', '$link_url')";
        if (mysqli_query($con, $insert)) {
            echo json_encode(['success' => true, 'message' => 'Link saved successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($con)]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Required fields are missing']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
