<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($con)) {
    include(__DIR__ . '/../admin_area/includes/db.php');
}

if (!isset($_SESSION['emp_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Authentication required']);
    exit;
}

$link_name = isset($_POST['link_name']) ? mysqli_real_escape_string($con, $_POST['link_name']) : '';
$category = isset($_POST['category']) ? mysqli_real_escape_string($con, $_POST['category']) : '';
$resource_type = isset($_POST['resource_type']) ? $_POST['resource_type'] : 'link';

$link_url = '';

if ($resource_type == 'document') {
    if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] == 0) {
        $base_upload_dir = realpath(__DIR__ . '/../admin_area/uploads/');
        if (!$base_upload_dir) {
            $base_upload_dir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'admin_area' . DIRECTORY_SEPARATOR . 'uploads';
            @mkdir($base_upload_dir, 0777, true);
            $base_upload_dir = realpath($base_upload_dir);
        }
        $upload_dir = $base_upload_dir . DIRECTORY_SEPARATOR . 'company_links' . DIRECTORY_SEPARATOR;
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = time() . '_' . basename($_FILES['document_file']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['document_file']['tmp_name'], $target_file)) {
            $link_url = 'uploads/company_links/' . $file_name;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload document']);
            exit;
        }
    } else {
        $errCode = isset($_FILES['document_file']['error']) ? $_FILES['document_file']['error'] : 'No file received';
        $errMsg = 'Please select a document.';
        echo json_encode(['status' => 'error', 'message' => $errMsg . " Code: $errCode"]);
        exit;
    }
} else {
    $link_url = isset($_POST['link_url']) ? mysqli_real_escape_string($con, $_POST['link_url']) : '';
}

if (empty($link_name) || empty($link_url) || empty($category)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill all fields']);
    exit;
}

$emp_id = $_SESSION['emp_id'];

$insert = "INSERT INTO company_links (link_name, link_url, category, uploaded_by_type, uploaded_by_id) VALUES ('$link_name', '$link_url', '$category', 'employee', '$emp_id')";
if (mysqli_query($con, $insert)) {
    $inserted_id = mysqli_insert_id($con);
    // Fetch it to return back to UI
    $get = mysqli_query($con, "SELECT * FROM company_links WHERE id='$inserted_id'");
    $row = mysqli_fetch_assoc($get);
    echo json_encode(['status' => 'success', 'message' => 'Resource added successfully', 'data' => $row]);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($con)]);
}
?>
