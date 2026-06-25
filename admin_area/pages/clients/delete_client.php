<?php
if (!isset($con)) {
    if (!isset($con)) {
        include(__DIR__ . '/../../includes/db.php');
    }
}

if (isset($_GET['delete_client'])) {
    $delete_id = intval($_GET['delete_client']);
    $is_ajax = isset($_GET['ajax']);

    // Fetch client image to delete it from storage
    $get_client = "SELECT image, name FROM clients WHERE id = $delete_id";
    $run_client = mysqli_query($con, $get_client);
    $row_client = mysqli_fetch_assoc($run_client);

    if ($row_client) {
        $client_name = $row_client['name'];
        $client_image = $row_client['image'];

        // Delete client record (Cascading will handle projects and remarks)
        $delete_query = "DELETE FROM clients WHERE id = $delete_id";
        $run_delete = mysqli_query($con, $delete_query);

        if ($run_delete) {
            // Delete image file if exists
            if (!empty($client_image) && file_exists("../uploads/client_images/$client_image")) {
                unlink("../uploads/client_images/$client_image");
            }

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