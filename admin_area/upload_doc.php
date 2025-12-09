<?php 
session_start();
include 'connection.php';
require_once __DIR__ . '/includes/firebase_sync.php';

$db = firebase_db();

if (!isset($_GET['id'])) {
  die("Invalid request");
}
$id = mysqli_real_escape_string($con, $_GET['id']);

if (isset($_POST['upload'])) {
  if (!empty($_FILES['documents']['name'][0])) {
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

    $files = $_FILES['documents'];
    $uploaded = 0;

    for ($i = 0; $i < count($files['name']); $i++) {
      $filename = time() . "_" . basename($files['name'][$i]);
      $targetFile = $targetDir . $filename;

      if (move_uploaded_file($files['tmp_name'][$i], $targetFile)) {
        mysqli_query($con, "INSERT INTO employee_documents (emp_id, file_name) VALUES ('$id', '$filename')");
        $docId = mysqli_insert_id($con);
        $row = [
          'id' => $docId,
          'emp_id' => (int)$id,
          'file_name' => $filename,
          'uploaded_at' => time(),
        ];
        firebase_sync_row($db, 'employee_documents', (string)$docId, $row);
        $uploaded++;
      }
    }

    echo "<script>alert('$uploaded file(s) uploaded successfully!');window.location.href='emp_directory.php';</script>";
  } else {
    echo "<script>alert('Please select at least one file.');</script>";
  }
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Upload Employee Documents</title>
<style>
  body {
    font-family: Calibri, sans-serif;
    background: #f7f7f7;
  }
  .container {
    max-width: 500px;
    margin: 60px auto;
    background: #fff;
    padding: 30px 40px;
    border-radius: 10px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
  }
  h2 {
    text-align: center;
    color: #2f4f6f;
  }
  input[type='file'] {
    width: 100%;
    margin-top: 20px;
    margin-bottom: 20px;
  }
  button {
    background: #2f4f6f;
    color: #fff;
    border: none;
    padding: 10px 18px;
    border-radius: 8px;
    cursor: pointer;
  }
  button:hover {
    background: #3b5f82;
  }
  a {
    display: inline-block;
    margin-top: 20px;
    text-decoration: none;
    color: #2f4f6f;
  }
</style>
</head>
<body>
  <div class="container">
    <h2>Upload Multiple Documents</h2>
    <form method="POST" enctype="multipart/form-data">
      <label for="documents">Select one or more files:</label>
      <input type="file" name="documents[]" id="documents" multiple required />
      <button type="submit" name="upload">Upload</button>
    </form>
    <div style="text-align:center;">
      <a href="emp_directory.php">← Back to Employee List</a>
    </div>
  </div>
</body>
</html>
