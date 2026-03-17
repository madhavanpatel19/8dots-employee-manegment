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
                        <div class="col-md-9">
                            <div class="permissions-container">
                                <?php
                                $categories = [
                                    'Employee Management' => ['employee_insert', 'employee_update', 'employee_delete', 'employee_view'],
                                    'Attendance & Leaves' => ['attendance_view', 'attendance_edit', 'leave_view', 'worksheet_view'],
                                    'Finance & Salary' => ['salary_view'],
                                    'User & System' => ['user_insert', 'user_update', 'user_view', 'announcement_view']
                                ];

                                foreach ($categories as $catName => $perms):
                                ?>
                                    <div class="permission-group">
                                        <h4 class="permission-cat-title"><?php echo $catName; ?></h4>
                                        <div class="permission-grid">
                                            <?php foreach ($perms as $perm): 
                                                $is_checked = in_array($perm, $current_perms, true);
                                                $label = function_exists('getPermissionLabel') ? getPermissionLabel($perm) : ucwords(str_replace('_', ' ', $perm));
                                            ?>
                                                <div class='permission-item'>
                                                    <label class='toggle-switch'>
                                                        <input type='checkbox' name='permissions[]' value='<?php echo htmlspecialchars($perm); ?>' <?php echo $is_checked ? 'checked' : ''; ?>>
                                                        <span class='toggle-slider'></span>
                                                        <span class='toggle-label'><?php echo htmlspecialchars($label); ?></span>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <style>
                        .permissions-container { 
                            border: 1px solid #e1e8ed; 
                            border-radius: 8px; 
                            padding: 20px; 
                            background: #ffffff;
                            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                        }
                        .permission-group { margin-bottom: 25px; }
                        .permission-group:last-child { margin-bottom: 0; }
                        .permission-cat-title { 
                            font-size: 15px; 
                            font-weight: 700; 
                            color: #2c3e50; 
                            margin-bottom: 15px; 
                            padding-bottom: 8px; 
                            border-bottom: 2px solid #f1f4f6;
                            display: flex;
                            align-items: center;
                        }
                        .permission-grid {
                            display: grid;
                            grid-template-columns: repeat(2, 1fr);
                            gap: 15px;
                        }
                        .permission-item { 
                            background: #f8fafc;
                            padding: 10px 15px;
                            border-radius: 6px;
                            border: 1px solid #edf2f7;
                            transition: all 0.2s;
                        }
                        .permission-item:hover {
                            background: #f1f5f9;
                            border-color: #cbd5e0;
                        }
                        .toggle-switch { position: relative; display: flex; align-items: center; width: 100%; cursor: pointer; margin-bottom: 0; }
                        .toggle-switch input[type="checkbox"] { opacity: 0; width: 0; height: 0; position: absolute; }
                        .toggle-slider { 
                            position: relative; 
                            flex-shrink: 0;
                            width: 44px; 
                            height: 22px; 
                            background: #cbd5e0; 
                            transition: .3s; 
                            border-radius: 22px; 
                            display: inline-block;
                            margin-right: 12px;
                        }
                        .toggle-slider:before { 
                            position: absolute; 
                            content: ""; 
                            height: 16px; 
                            width: 16px; 
                            left: 3px; 
                            bottom: 3px; 
                            background: white; 
                            transition: .3s; 
                            border-radius: 50%;
                            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
                        }
                        .toggle-switch input:checked + .toggle-slider { background: #2ecc71; }
                        .toggle-switch input:checked + .toggle-slider:before { transform: translateX(22px); }
                        .toggle-label { 
                            font-size: 13px;
                            font-weight: 500; 
                            color: #4a5568;
                            line-height: 1.2;
                        }
                        @media (max-width: 768px) {
                            .permission-grid { grid-template-columns: 1fr; }
                        }
                    
                        /* border-radius: 50%;
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
                    } */
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
