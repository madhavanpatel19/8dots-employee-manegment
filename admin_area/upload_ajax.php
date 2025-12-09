<?php
include 'connection.php';
require_once __DIR__ . '/includes/firebase_sync.php';

$db = firebase_db();

if (isset($_POST['emp_id'])) {
    $id = intval($_POST['emp_id']);
    if (!empty($_FILES['documents']['name'][0])) {
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        foreach ($_FILES['documents']['name'] as $key => $name) {
            $filename = time() . "_" . basename($name);
            $targetFile = $targetDir . $filename;
            if (move_uploaded_file($_FILES['documents']['tmp_name'][$key], $targetFile)) {
                mysqli_query($con, "INSERT INTO employee_documents (emp_id, file_name) VALUES ('$id', '$filename')");
                $docId = mysqli_insert_id($con);
                $row = [
                    'id' => $docId,
                    'emp_id' => $id,
                    'file_name' => $filename,
                    'uploaded_at' => time(),
                ];
                firebase_sync_row($db, 'employee_documents', (string)$docId, $row);
            }
        }
        echo "success";
    } else {
        echo "no_files";
    }
}
?>
