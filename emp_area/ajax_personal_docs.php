<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['emp_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$emp_id = (int)$_SESSION['emp_id'];
$action = $_POST['action'] ?? '';

if ($action === 'upload') {
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status' => 'error', 'message' => 'Upload failed.']);
        exit();
    }
    
    $file = $_FILES['file'];
    $doc_name = mysqli_real_escape_string($con, pathinfo($file['name'], PATHINFO_FILENAME));
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = time() . '_' . rand(1000, 9999) . '.' . $ext;
    $target_dir = 'uploads/personal_docs/';
    $target_file = $target_dir . $filename;
    
    // Check if directory exists, if not create it
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        $sql = "INSERT INTO emp_personal_documents (emp_id, doc_name, file_path) VALUES ('$emp_id', '$doc_name', '$target_file')";
        if (mysqli_query($con, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Document uploaded successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file.']);
    }
} elseif ($action === 'fetch') {
    $sql = "SELECT * FROM emp_personal_documents WHERE emp_id = '$emp_id' ORDER BY created_at DESC";
    $res = mysqli_query($con, $sql);
    $docs = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $docs[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $docs]);
} elseif ($action === 'delete') {
    $doc_id = (int)($_POST['doc_id'] ?? 0);
    $sql = "SELECT file_path FROM emp_personal_documents WHERE id = '$doc_id' AND emp_id = '$emp_id'";
    $res = mysqli_query($con, $sql);
    if ($row = mysqli_fetch_assoc($res)) {
        if (file_exists($row['file_path'])) {
            unlink($row['file_path']);
        }
        mysqli_query($con, "DELETE FROM emp_personal_documents WHERE id = '$doc_id' AND emp_id = '$emp_id'");
        echo json_encode(['status' => 'success', 'message' => 'Deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Document not found or access denied']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}
?>
