<?php
if (!isset($con)) {
    include(__DIR__ . '/../../includes/db.php');
}

if (isset($_GET['delete_client'])) {
    $delete_id = intval($_GET['delete_client']);
    $is_ajax   = isset($_GET['ajax']);

    // Fetch client to confirm it exists and isn't already soft-deleted
    $get_client = "SELECT image, name FROM clients WHERE id = $delete_id AND deleted_at IS NULL";
    $run_client = mysqli_query($con, $get_client);
    $row_client = mysqli_fetch_assoc($run_client);

    if ($row_client) {
        $client_name  = $row_client['name'];
        $client_image = $row_client['image'];
        $now          = date('Y-m-d H:i:s');

        // Soft delete the client (projects/remarks are also soft-deleted)
        $delete_query = "UPDATE clients SET deleted_at = '$now' WHERE id = $delete_id AND deleted_at IS NULL";
        $run_delete   = mysqli_query($con, $delete_query);

        // Also soft delete child projects
        mysqli_query($con, "UPDATE client_projects SET deleted_at = '$now' WHERE client_id = $delete_id AND deleted_at IS NULL");

        if ($run_delete) {
            // We keep the image file on disk (soft delete = no data loss)
            if ($is_ajax) {
                echo json_encode(['success' => true, 'message' => "Client $client_name deleted successfully."]);
                exit();
            }
            $success = true;
        } else {
            $error = mysqli_error($con);
            if ($is_ajax) {
                echo json_encode(['success' => false, 'error' => $error]);
                exit();
            }
        }
    } else {
        if ($is_ajax) {
            echo json_encode(['success' => false, 'error' => 'Client not found.']);
            exit();
        }
        echo "<script>window.location.href='index.php?client_directory';</script>";
        exit();
    }
}

if (isset($success) && $success) {
    echo "<script>window.location.href='index.php?client_directory';</script>";
    exit();
}
?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger">Error deleting client: <?php echo $error; ?></div>
<?php endif; ?>