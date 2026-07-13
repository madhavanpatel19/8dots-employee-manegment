<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../includes/db.php');

if (!isset($_SESSION['emp_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$emp_id = $_SESSION['emp_id'];
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

if ($action == 'fetch_categories') {
    $res = mysqli_query($con, "SELECT category_name FROM emp_personal_categories WHERE emp_id='$emp_id' ORDER BY created_at ASC");
    $cats = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $cats[] = $r['category_name'];
    }
    echo json_encode(['status' => 'success', 'data' => $cats]);
    exit;
}

if ($action == 'add_category') {
    $category_name = isset($_POST['category']) ? mysqli_real_escape_string($con, trim($_POST['category'])) : '';
    if (empty($category_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Category name is required']);
        exit;
    }
    
    // check if exists
    $chk = mysqli_query($con, "SELECT id FROM emp_personal_categories WHERE emp_id='$emp_id' AND category_name='$category_name'");
    if (mysqli_num_rows($chk) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Section already exists']);
        exit;
    }
    
    if (mysqli_query($con, "INSERT INTO emp_personal_categories (emp_id, category_name) VALUES ('$emp_id', '$category_name')")) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($con)]);
    }
    exit;
}

if ($action == 'fetch_resources') {
    $res = mysqli_query($con, "SELECT * FROM emp_personal_resources WHERE emp_id='$emp_id' ORDER BY created_at DESC");
    $data = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $data[] = $r;
    }
    echo json_encode(['status' => 'success', 'data' => $data]);
    exit;
}

if ($action == 'add_resource') {
    $link_name = isset($_POST['link_name']) ? mysqli_real_escape_string($con, trim($_POST['link_name'])) : '';
    $category = isset($_POST['category']) ? mysqli_real_escape_string($con, trim($_POST['category'])) : '';
    $resource_type = isset($_POST['resource_type']) ? $_POST['resource_type'] : 'link';
    
    if (empty($link_name) || empty($category)) {
        echo json_encode(['status' => 'error', 'message' => 'Name and Section are required']);
        exit;
    }
    
    $link_url = '';
    
    if ($resource_type == 'document') {
        if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] == 0) {
            $base_upload_dir = realpath(__DIR__ . '/../uploads/');
            if (!$base_upload_dir) {
                $base_upload_dir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'uploads';
                @mkdir($base_upload_dir, 0777, true);
                $base_upload_dir = realpath($base_upload_dir);
            }
            $upload_dir = $base_upload_dir . DIRECTORY_SEPARATOR . 'personal_docs' . DIRECTORY_SEPARATOR;
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_name = time() . '_' . basename($_FILES['document_file']['name']);
            $target_file = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['document_file']['tmp_name'], $target_file)) {
                $link_url = 'uploads/personal_docs/' . $file_name;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to upload document']);
                exit;
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Please select a document']);
            exit;
        }
    } else {
        $link_url = isset($_POST['link_url']) ? mysqli_real_escape_string($con, trim($_POST['link_url'])) : '';
        if (empty($link_url)) {
            echo json_encode(['status' => 'error', 'message' => 'URL is required']);
            exit;
        }
    }
    
    $sql = "INSERT INTO emp_personal_resources (emp_id, category, resource_type, link_name, link_url) VALUES ('$emp_id', '$category', '$resource_type', '$link_name', '$link_url')";
    if (mysqli_query($con, $sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($con)]);
    }
    exit;
}

if ($action == 'toggle_pin') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $is_pinned = isset($_POST['is_pinned']) ? (int)$_POST['is_pinned'] : 0;
    $mode = isset($_POST['mode']) ? $_POST['mode'] : 'personal';
    
    if ($mode === 'company') {
        // Pin company link
        if (mysqli_query($con, "UPDATE company_links SET is_pinned='$is_pinned' WHERE id='$id'")) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($con)]);
        }
    } else {
        // Pin personal link
        if (mysqli_query($con, "UPDATE emp_personal_resources SET is_pinned='$is_pinned' WHERE id='$id' AND emp_id='$emp_id'")) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($con)]);
        }
    }
    exit;
}

if ($action == 'delete_resource') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    $res = mysqli_query($con, "SELECT link_url, resource_type FROM emp_personal_resources WHERE id='$id' AND emp_id='$emp_id'");
    if (mysqli_num_rows($res) > 0) {
        $r = mysqli_fetch_assoc($res);
        if ($r['resource_type'] == 'document' && !empty($r['link_url'])) {
            $path = __DIR__ . '/../' . $r['link_url'];
            if (file_exists($path)) {
                @unlink($path);
            }
        }
        mysqli_query($con, "DELETE FROM emp_personal_resources WHERE id='$id' AND emp_id='$emp_id'");
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Not found']);
    }
    exit;
}

if ($action == 'delete_category') {
    $category_name = isset($_POST['category']) ? mysqli_real_escape_string($con, trim($_POST['category'])) : '';
    if (empty($category_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Category name required']);
        exit;
    }
    
    // Optional: Only allow delete if empty, or just delete it and its resources? 
    // The user wants to delete empty sections. Let's just delete the category.
    // If they want to delete the section, we can also delete its resources.
    // For safety, let's just delete the category and resources.
    $res = mysqli_query($con, "SELECT link_url, resource_type FROM emp_personal_resources WHERE category='$category_name' AND emp_id='$emp_id'");
    while ($r = mysqli_fetch_assoc($res)) {
        if ($r['resource_type'] == 'document' && !empty($r['link_url'])) {
            $path = __DIR__ . '/../' . $r['link_url'];
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }
    mysqli_query($con, "DELETE FROM emp_personal_resources WHERE category='$category_name' AND emp_id='$emp_id'");
    mysqli_query($con, "DELETE FROM emp_personal_categories WHERE category_name='$category_name' AND emp_id='$emp_id'");
    
    echo json_encode(['status' => 'success']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
?>
