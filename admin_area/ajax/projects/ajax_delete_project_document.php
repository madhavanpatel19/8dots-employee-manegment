<?php
if (!isset($con)) { include(__DIR__ . '/../../includes/db.php'); }
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['doc_id'])) {
    $doc_id = mysqli_real_escape_string($con, $_POST['doc_id']);
    
    // Get file path first to delete the physical file
    $get_path = "SELECT file_path FROM project_documents WHERE id = '$doc_id'";
    $run_path = mysqli_query($con, $get_path);
    if ($row = mysqli_fetch_assoc($run_path)) {
        $path = $row['file_path'];
        
        $delete = "DELETE FROM project_documents WHERE id = '$doc_id'";
        if (mysqli_query($con, $delete)) {
            if (file_exists($path)) {
                unlink($path);
            }
            echo json_encode(['success' => true, 'message' => 'Document deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($con)]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Document not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
