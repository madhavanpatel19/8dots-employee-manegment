<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($con)) {
    include(__DIR__ . '/../../includes/db.php');
}

if (!isset($_SESSION['admin_email'])) {
    exit("Authentication required");
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'add') {
    file_put_contents(__DIR__ . '/debug_log.txt', date('Y-m-d H:i:s') . " POST: " . json_encode($_POST) . " FILES: " . json_encode($_FILES) . "\n", FILE_APPEND);

    $link_name = isset($_POST['link_name']) ? mysqli_real_escape_string($con, $_POST['link_name']) : '';
    $category = isset($_POST['category']) ? mysqli_real_escape_string($con, $_POST['category']) : '';
    $resource_type = isset($_POST['resource_type']) ? $_POST['resource_type'] : 'link';

    $link_url = '';

    if ($resource_type == 'document') {
        if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] == 0) {
            $base_upload_dir = realpath(__DIR__ . '/../../uploads/');
            if (!$base_upload_dir) {
                // In case admin_area/uploads doesn't exist, create it relative to this file
                $base_upload_dir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'uploads';
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
            if ($errCode == UPLOAD_ERR_INI_SIZE) {
                $errMsg = 'File exceeds upload_max_filesize in php.ini';
            } elseif ($errCode == UPLOAD_ERR_FORM_SIZE) {
                $errMsg = 'File exceeds MAX_FILE_SIZE in HTML form';
            } elseif ($errCode == UPLOAD_ERR_PARTIAL) {
                $errMsg = 'File was only partially uploaded';
            } elseif ($errCode == UPLOAD_ERR_NO_FILE) {
                $errMsg = 'No file was uploaded';
            } elseif ($errCode == UPLOAD_ERR_NO_TMP_DIR) {
                $errMsg = 'Missing a temporary folder';
            } elseif ($errCode == UPLOAD_ERR_CANT_WRITE) {
                $errMsg = 'Failed to write file to disk';
            } elseif ($errCode == UPLOAD_ERR_EXTENSION) {
                $errMsg = 'A PHP extension stopped the file upload';
            } else {
                $errMsg .= " Code: $errCode";
            }

            echo json_encode(['status' => 'error', 'message' => $errMsg]);
            exit;
        }
    } else {
        $link_url = isset($_POST['link_url']) ? mysqli_real_escape_string($con, $_POST['link_url']) : '';
    }

    if (empty($link_name) || empty($link_url) || empty($category)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill all fields']);
        exit;
    }

    $insert = "INSERT INTO company_links (link_name, link_url, category) VALUES ('$link_name', '$link_url', '$category')";
    if (mysqli_query($con, $insert)) {
        echo json_encode(['status' => 'success', 'message' => 'Resource deployed successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($con)]);
    }
} elseif ($action == 'fetch') {
    $query = "SELECT * FROM company_links ORDER BY category ASC, created_at DESC";
    $run = mysqli_query($con, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($run)) {
        $data[] = $row;
    }
    echo json_encode($data);
} elseif ($action == 'delete') {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $delete = "DELETE FROM company_links WHERE id='$id'";
    if (mysqli_query($con, $delete)) {
        echo json_encode(['status' => 'success', 'message' => 'Link removed successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($con)]);
    }
} elseif ($action == 'pin') {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $update = "UPDATE company_links SET is_pinned=1 WHERE id='$id'";
    if (mysqli_query($con, $update)) {
        echo json_encode(['status' => 'success', 'message' => 'Link pinned']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($con)]);
    }
} elseif ($action == 'unpin') {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $update = "UPDATE company_links SET is_pinned=0 WHERE id='$id'";
    if (mysqli_query($con, $update)) {
        echo json_encode(['status' => 'success', 'message' => 'Link unpinned']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($con)]);
    }
} elseif ($action == 'rename_category') {
    $old_category = mysqli_real_escape_string($con, $_POST['old_category']);
    $new_category = mysqli_real_escape_string($con, $_POST['new_category']);
    
    if (empty($old_category) || empty($new_category)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid category names']);
        exit;
    }

    $update = "UPDATE company_links SET category='$new_category' WHERE category='$old_category'";
    if (mysqli_query($con, $update)) {
        echo json_encode(['status' => 'success', 'message' => 'Category renamed successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($con)]);
    }
}
