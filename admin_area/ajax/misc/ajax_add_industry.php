<?php
if (!isset($con)) {
    include(__DIR__ . '/../../includes/db.php');
}

$response = array('status' => 'error', 'message' => 'Unknown error');

if (isset($_POST['industry_name'])) {
    $industry_name = trim(mysqli_real_escape_string($con, $_POST['industry_name']));
    
    if (!empty($industry_name)) {
        // Check if exists
        $check = mysqli_query($con, "SELECT id FROM client_industries WHERE industry_name = '$industry_name'");
        if (mysqli_num_rows($check) > 0) {
            $row = mysqli_fetch_assoc($check);
            $response = array('status' => 'error', 'message' => 'Industry already exists', 'id' => $row['id']);
        } else {
            $insert = mysqli_query($con, "INSERT INTO client_industries (industry_name) VALUES ('$industry_name')");
            if ($insert) {
                $id = mysqli_insert_id($con);
                $response = array('status' => 'success', 'id' => $id, 'name' => $industry_name);
            } else {
                $response = array('status' => 'error', 'message' => mysqli_error($con));
            }
        }
    } else {
        $response = array('status' => 'error', 'message' => 'Industry name cannot be empty');
    }
}

echo json_encode($response);
?>
