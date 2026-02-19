<?php
if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('login.php','_self')</script>";
    exit;
}
include 'connection.php';
if (!function_exists('isSuperAdmin')) {
    include __DIR__ . '/includes/admin_permissions.php';
}
if (!function_exists('getUsedAdminPermissions')) {
    require_once __DIR__ . '/../settings/permissions/permissions.php';
}
if (!isset($_GET['edit_user'])) {
    header('Location: view_users.php');
    exit;
}
$edit_id = (int)$_GET['edit_user'];
$run_admin = mysqli_query($con, "SELECT * FROM admins WHERE admin_id=" . $edit_id . " LIMIT 1");
if (!$run_admin || mysqli_num_rows($run_admin) == 0) {
    echo "<div class='alert alert-danger'>User not found.</div>";
    exit;
}
$row_admin = mysqli_fetch_assoc($run_admin);
$admin_id = $row_admin['admin_id'];
$admin_name = $row_admin['admin_name'];
$admin_email = $row_admin['admin_email'];
$admin_pass = $row_admin['admin_pass'];
$admin_image = $row_admin['admin_image'];
$new_admin_image = $row_admin['admin_image'];
$admin_country = $row_admin['admin_country'];
$admin_job = $row_admin['admin_job'];
$admin_contact = $row_admin['admin_contact'];
$admin_about = $row_admin['admin_about'];
$current_super = isset($row_admin['is_super_admin']) ? (int)$row_admin['is_super_admin'] : 0;
$perms_raw = isset($row_admin['permissions']) ? trim((string)$row_admin['permissions']) : '';
$current_perms = [];
if ($perms_raw !== '') {
    $current_perms = array_values(array_filter(array_map('trim', explode(',', $perms_raw)), function($p) { return $p !== ''; }));
}
if (isset($_POST['update'])) {
    $admin_name = $_POST['admin_name'];
    $admin_email = $_POST['admin_email'];
    $admin_pass = $_POST['admin_pass'];
    $admin_country = $_POST['admin_country'];
    $admin_job = $_POST['admin_job'];
    $admin_contact = $_POST['admin_contact'];
    $admin_about = $_POST['admin_about'];
    $admin_image = $_FILES['admin_image']['name'];
    $temp_admin_image = $_FILES['admin_image']['tmp_name'];
    if (!empty($admin_image)) {
        move_uploaded_file($temp_admin_image, "admin_images/$admin_image");
    } else {
        $admin_image = $new_admin_image;
    }
    $permissions = '';
    if (!empty($_POST['permissions']) && is_array($_POST['permissions'])) {
        $permissions = implode(',', array_map(function($p) use ($con) { return mysqli_real_escape_string($con, $p); }, $_POST['permissions']));
    }
    $is_super = (isset($_POST['is_super_admin']) && $_POST['is_super_admin'] == '1') ? 1 : 0;
    $perm_esc = mysqli_real_escape_string($con, $permissions);
    $name_esc = mysqli_real_escape_string($con, $admin_name);
    $email_esc = mysqli_real_escape_string($con, $admin_email);
    $pass_esc = mysqli_real_escape_string($con, $admin_pass);
    $img_esc = mysqli_real_escape_string($con, $admin_image);
    $contact_esc = mysqli_real_escape_string($con, $admin_contact);
    $country_esc = mysqli_real_escape_string($con, $admin_country);
    $job_esc = mysqli_real_escape_string($con, $admin_job);
    $about_esc = mysqli_real_escape_string($con, $admin_about);
    $update_admin = "UPDATE admins SET admin_name='$name_esc', admin_email='$email_esc', admin_pass='$pass_esc', admin_image='$img_esc', admin_contact='$contact_esc', admin_country='$country_esc', admin_job='$job_esc', admin_about='$about_esc', permissions='$perm_esc', is_super_admin='$is_super' WHERE admin_id='$admin_id'";
    $run_admin = mysqli_query($con, $update_admin);
    if ($run_admin) {
        echo "<script>alert('User Has Been Updated successfully')</script>";
        // Optionally redirect or refresh
        echo "<script>window.open('index.php?view_users','_self')</script>";
        exit;
    } else {
        echo "<div class='alert alert-danger'>Update failed.</div>";
    }
}
?>
<div class="row">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li class="active">
                <i class="fa fa-dashboard"></i> Dashboard / Edit User
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-money fa-fw"></i> Edit User
                </h3>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="col-md-3 control-label">User Name: </label>
                        <div class="col-md-6">
                            <input type="text" name="admin_name" class="form-control" required value="<?php echo htmlspecialchars($admin_name); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">User Email: </label>
                        <div class="col-md-6">
                            <input type="text" name="admin_email" class="form-control" required value="<?php echo htmlspecialchars($admin_email); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">User Password: </label>
                        <div class="col-md-6">
                            <input type="text" name="admin_pass" class="form-control" required value="<?php echo htmlspecialchars($admin_pass); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">User Country: </label>
                        <div class="col-md-6">
                            <input type="text" name="admin_country" class="form-control" required value="<?php echo htmlspecialchars($admin_country); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">User Job: </label>
                        <div class="col-md-6">
                            <input type="text" name="admin_job" class="form-control" required value="<?php echo htmlspecialchars($admin_job); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">User Contact: </label>
                        <div class="col-md-6">
                            <input type="text" name="admin_contact" class="form-control" required value="<?php echo htmlspecialchars($admin_contact); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">User About: </label>
                        <div class="col-md-6">
                            <textarea name="admin_about" class="form-control" rows="3"><?php echo htmlspecialchars($admin_about); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">User Image: </label>
                        <div class="col-md-6">
                            <input type="file" name="admin_image" class="form-control">
                            <br>
                            <img src="admin_images/<?php echo htmlspecialchars($admin_image); ?>" width="70" height="70">
                        </div>
                    </div>
                    <?php if (function_exists('isSuperAdmin') && isSuperAdmin()): ?>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Super Admin: </label>
                        <div class="col-md-6">
                            <label class="toggle-switch">
                                <input type="checkbox" name="is_super_admin" value="1" <?php echo $current_super ? ' checked="checked"' : ''; ?>>
                                <span class="toggle-slider"></span>
                                <span class="toggle-label">Full access (see and edit all)</span>
                            </label>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Permissions: </label>
                        <div class="col-md-6">
                            <div class="permissions-container">
                                <?php foreach (function_exists('getUsedAdminPermissions') ? getUsedAdminPermissions() : getAllPermissions() as $perm): ?>
                                <?php $is_checked = in_array($perm, $current_perms, true); ?>
                                <div class="permission-item">
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="permissions[]" value="<?php echo htmlspecialchars($perm); ?>"<?php echo $is_checked ? ' checked="checked"' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                        <span class="toggle-label"><?php echo htmlspecialchars(function_exists('getPermissionLabel') ? getPermissionLabel($perm) : ucwords(str_replace('_', ' ', $perm))); ?></span>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <style>
                    .permissions-container {
                        border: 1px solid #ddd;
                        border-radius: 4px;
                        padding: 15px;
                        background-color: #f9f9f9;
                    }
                    .permission-item {
                        margin-bottom: 12px;
                        padding-bottom: 12px;
                        border-bottom: 1px solid #e0e0e0;
                    }
                    .permission-item:last-child {
                        margin-bottom: 0;
                        padding-bottom: 0;
                        border-bottom: none;
                    }
                    .toggle-switch {
                        position: relative;
                        display: inline-block;
                        width: 100%;
                        cursor: pointer;
                    }
                    .toggle-switch input[type="checkbox"] {
                        opacity: 0;
                        width: 0;
                        height: 0;
                    }
                    .toggle-slider {
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        background-color: #ccc;
                        transition: .4s;
                        border-radius: 34px;
                        width: 50px;
                        height: 24px;
                        display: inline-block;
                        vertical-align: middle;
                        margin-right: 10px;
                    }
                    .toggle-slider:before {
                        position: absolute;
                        content: "";
                        height: 18px;
                        width: 18px;
                        left: 3px;
                        bottom: 3px;
                        background-color: white;
                        transition: .4s;
                        border-radius: 50%;
                    }
                    .toggle-switch input:checked + .toggle-slider {
                        background-color: #5cb85c;
                    }
                    .toggle-switch input:checked + .toggle-slider:before {
                        transform: translateX(26px);
                    }
                    .toggle-label {
                        margin-left: 60px;
                        font-weight: normal;
                        display: inline-block;
                        vertical-align: middle;
                        line-height: 24px;
                    }
                    </style>
                    <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-6">
                            <input type="submit" name="update" value="Update User" class="btn btn-primary form-control">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
