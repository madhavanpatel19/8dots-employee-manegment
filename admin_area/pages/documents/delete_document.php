<?php
if (!isset($con)) { include(__DIR__ . '/../../includes/db.php'); }

header('Content-Type: application/json');

if (isset($_POST['doc_id'])) {
    $doc_id = intval($_POST['doc_id']);
    $res = mysqli_query($con, "SELECT file_name FROM employee_documents WHERE id='$doc_id'");
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $file = "../../uploads/" . $row['file_name'];
        if (file_exists($file)) unlink($file);
        $deleted = mysqli_query($con, "DELETE FROM employee_documents WHERE id='$doc_id'");
        echo json_encode([
            "status" => $deleted ? "success" : "error"
        ]);
    } else {
        echo json_encode([
            "status" => "error"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error"
    ]);
}
?>
