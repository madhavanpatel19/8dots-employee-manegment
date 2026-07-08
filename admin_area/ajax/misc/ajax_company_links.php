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

    $admin_id = $_SESSION['admin_email']; // using email as ID for admin

    $insert = "INSERT INTO company_links (link_name, link_url, category, uploaded_by_type, uploaded_by_id) VALUES ('$link_name', '$link_url', '$category', 'admin', NULL)";
    if (mysqli_query($con, $insert)) {
        echo json_encode(['status' => 'success', 'message' => 'Resource deployed successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($con)]);
    }
} elseif ($action == 'fetch') {
    $query = "SELECT c.*, 
              e.name as emp_name, 
              e.employee_image as emp_photo 
              FROM company_links c 
              LEFT JOIN emp_list e ON c.uploaded_by_id = e.id 
              ORDER BY c.category ASC, c.created_at DESC";
    $run = mysqli_query($con, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($run)) {
        if ($row['uploaded_by_type'] == 'employee' && !empty($row['emp_name'])) {
            $row['uploader_name'] = $row['emp_name'];
            $row['uploader_photo'] = $row['emp_photo'];
        } else {
            $row['uploader_name'] = 'Admin';
            $row['uploader_photo'] = ''; // Will fall back to default
        }
        $data[] = $row;
    }
    echo json_encode($data);
} elseif ($action == 'delete') {
    $id  = intval($_POST['id']);
    $now = date('Y-m-d H:i:s');
    $update = "UPDATE company_links SET deleted_at = '$now' WHERE id = $id AND deleted_at IS NULL";
    if (mysqli_query($con, $update)) {
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
} elseif ($action == 'get_assignments') {
    $category = mysqli_real_escape_string($con, $_GET['category']);
    $query = "SELECT emp_id FROM company_links_assignments WHERE category='$category'";
    $run = mysqli_query($con, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($run)) {
        $data[] = $row['emp_id'];
    }
    echo json_encode($data);
} elseif ($action == 'save_assignments') {
    $category = mysqli_real_escape_string($con, $_POST['category']);
    $emp_ids = isset($_POST['emp_ids']) && is_array($_POST['emp_ids']) ? $_POST['emp_ids'] : [];

    // First delete existing assignments for this category
    mysqli_query($con, "DELETE FROM company_links_assignments WHERE category='$category'");

    // Then insert new ones
    if (!empty($emp_ids)) {
        $values = [];
        foreach ($emp_ids as $eid) {
            $e_safe = mysqli_real_escape_string($con, $eid);
            $values[] = "('$category', '$e_safe')";
        }
        $insert = "INSERT INTO company_links_assignments (category, emp_id) VALUES " . implode(',', $values);
        if (mysqli_query($con, $insert)) {
            echo json_encode(['status' => 'success', 'message' => 'Assignments updated']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($con)]);
        }
    } else {
        echo json_encode(['status' => 'success', 'message' => 'Assignments cleared']);
    }
}
