<?php
require_once __DIR__ . '/../settings/permissions/permissions.php';
if (!isset($con)) {
    include(__DIR__ . '/includes/db.php');
}
if (!function_exists('isSuperAdmin')) {
    include(__DIR__ . '/includes/admin_permissions.php');
}
if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('login.php','_self')</script>";
} else {
?>
    <div class='row'><!-- 1  row Starts -->
        <div class='col-lg-12'><!-- col-lg-12 Starts -->
            <ol class='breadcrumb'><!-- breadcrumb Starts -->
                <li class='active'>
                    <i class='fa fa-dashboard'></i> Dashboard / Insert User
                </li>
            </ol><!-- breadcrumb Ends -->
        </div><!-- col-lg-12 Ends -->
    </div><!-- 1  row Ends -->
    <div class='row'><!-- 2 row Starts -->
        <div class='col-lg-12'><!-- col-lg-12 Starts -->
            <div class='panel panel-default'><!-- panel panel-default Starts -->
                <div class='panel-heading'><!-- panel-heading Starts -->
                    <h3 class='panel-title'>
                        <i class='fa fa-money fa-fw'></i> Insert User
                    </h3>
                </div><!-- panel-heading Ends -->
                <div class='panel-body'><!-- panel-body Starts -->
                    <form class='form-horizontal' method='post' enctype='multipart/form-data'><!-- form-horizontal Starts -->
                        <div class='form-group'><!-- form-group Starts -->
                            <label class='col-md-3 control-label'>User Name: </label>
                            <div class='col-md-6'><!-- col-md-6 Starts -->
                                <input type='text' name='admin_name' class='form-control' required>
                            </div><!-- col-md-6 Ends -->
                        </div><!-- form-group Ends -->
                        <div class='form-group'><!-- form-group Starts -->
                            <label class='col-md-3 control-label'>User Email: </label>
                            <div class='col-md-6'><!-- col-md-6 Starts -->
                                <input type='text' name='admin_email' class='form-control' required>
                            </div><!-- col-md-6 Ends -->
                        </div><!-- form-group Ends -->
                        <div class='form-group'><!-- form-group Starts -->
                            <label class='col-md-3 control-label'>User Password: </label>
                            <div class='col-md-6'><!-- col-md-6 Starts -->
                                <input type='password' name='admin_pass' class='form-control' required>
                            </div><!-- col-md-6 Ends -->
                        </div><!-- form-group Ends -->
                        <div class='form-group'><!-- form-group Starts -->
                            <label class='col-md-3 control-label'>User Country: </label>
                            <div class='col-md-6'><!-- col-md-6 Starts -->
                                <input type='text' name='admin_country' class='form-control' required>
                            </div><!-- col-md-6 Ends -->
                        </div><!-- form-group Ends -->
                        <div class='form-group'><!-- form-group Starts -->
                            <label class='col-md-3 control-label'>User Job: </label>
                            <div class='col-md-6'><!-- col-md-6 Starts -->
                                <input type='text' name='admin_job' class='form-control' required>
                            </div><!-- col-md-6 Ends -->
                        </div><!-- form-group Ends -->
                        <div class='form-group'><!-- form-group Starts -->
                            <label class='col-md-3 control-label'>User Contact: </label>
                            <div class='col-md-6'><!-- col-md-6 Starts -->
                                <input type='text' name='admin_contact' class='form-control' required>
                            </div><!-- col-md-6 Ends -->
                        </div><!-- form-group Ends -->
                        <div class='form-group'><!-- form-group Starts -->
                            <label class='col-md-3 control-label'>User About: </label>
                            <div class='col-md-6'><!-- col-md-6 Starts -->
                                <textarea name='admin_about' class='form-control' rows='3'> </textarea>
                            </div><!-- col-md-6 Ends -->
                        </div><!-- form-group Ends -->
                        <div class='form-group'><!-- form-group Starts -->
                            <label class='col-md-3 control-label'>User Image: </label>
                            <div class='col-md-6'><!-- col-md-6 Starts -->
                                <input type='file' name='admin_image' class='form-control' required>
                            </div><!-- col-md-6 Ends -->
                        </div><!-- form-group Ends -->
                        <?php if (function_exists('isSuperAdmin') && isSuperAdmin()): ?>
                            <div class='form-group'>
                                <label class='col-md-3 control-label'>Super Admin:</label>
                                <div class='col-md-6'>
                                    <label class='toggle-switch'>
                                        <input type='checkbox' name='is_super_admin' value='1'>
                                        <span class='toggle-slider'></span>
                                        <span class='toggle-label'>Full access ( see and edit all )</span>
                                    </label>
                                </div>
                            </div>
                        <?php endif;
                        ?>
                        <div class='form-group'>
                            <label class='col-md-3 control-label'>Permissions:</label>
                            <div class='col-md-6'>
                                <div class='permissions-container'>
                                    <?php
                                    $permList = function_exists('getUsedAdminPermissions') ? getUsedAdminPermissions() : (function_exists('getAllPermissions') ? getAllPermissions() : []);
                                    foreach ($permList as $perm):
                                        $label = function_exists('getPermissionLabel') ? getPermissionLabel($perm) : ucwords(str_replace('_', ' ', $perm));
                                    ?>
                                        <div class='permission-item'>
                                            <label class='toggle-switch'>
                                                <input type='checkbox' name='permissions[]' value='<?php echo htmlspecialchars($perm); ?>'>
                                                <span class='toggle-slider'></span>
                                                <span class='toggle-label'><?php echo htmlspecialchars($label); ?></span>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <style>
                            .permissions-container { border: 1px solid #ddd; border-radius: 4px; padding: 15px; background: #f9f9f9; }
                            .permission-item { margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #e0e0e0; }
                            .permission-item:last-child { margin-bottom: 0; padding-bottom: 0; border-bottom: none; }
                            .toggle-switch { position: relative; display: inline-block; width: 100%; cursor: pointer; }
                            .toggle-switch input[type="checkbox"] { opacity: 0; width: 0; height: 0; }
                            .toggle-slider { position: absolute; left: 0; top: 0; width: 50px; height: 24px; background: #ccc; transition: .4s; border-radius: 34px; display: inline-block; vertical-align: middle; margin-right: 10px; }
                            .toggle-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background: white; transition: .4s; border-radius: 50%; }
                            .toggle-switch input:checked + .toggle-slider { background: #5cb85c; }
                            .toggle-switch input:checked + .toggle-slider:before { transform: translateX(26px); }
                            .toggle-label { margin-left: 60px; font-weight: normal; display: inline-block; vertical-align: middle; line-height: 24px; }
                        </style>

                        <div class='form-group'><!-- form-group Starts -->
                            <label class='col-md-3 control-label'></label>
                            <div class='col-md-6'><!-- col-md-6 Starts -->
                                <input type='submit' name='submit' value='Insert User' class='btn btn-primary form-control'>
                            </div><!-- col-md-6 Ends -->
                        </div><!-- form-group Ends -->
                    </form><!-- form-horizontal Ends -->
                </div><!-- panel-body Ends -->
            </div><!-- panel panel-default Ends -->
        </div><!-- col-lg-12 Ends -->
    </div><!-- 2 row Ends -->
    <?php
    if (isset($_POST['submit'])) {
        $admin_name = $_POST['admin_name'];
        $admin_email = $_POST['admin_email'];
        $admin_pass = $_POST['admin_pass'];
        $admin_country = $_POST['admin_country'];
        $admin_job = $_POST['admin_job'];
        $admin_contact = $_POST['admin_contact'];
        $admin_about = $_POST['admin_about'];
        $admin_image = $_FILES['admin_image']['name'];
        $temp_admin_image = $_FILES['admin_image']['tmp_name'];
        move_uploaded_file($temp_admin_image, "admin_images/$admin_image");
        $permissions = '';
        if (!empty($_POST['permissions'])) {
            $permissions = implode(',', array_map(
                function ($p) use ($con) {
                    return mysqli_real_escape_string($con, $p);
                },
                $_POST['permissions']
            ));
        }
        $is_super = (isset($_POST['is_super_admin']) && $_POST['is_super_admin'] == '1') ? 1 : 0;
        $perm_esc = mysqli_real_escape_string($con, $permissions);
        $insert_admin = "INSERT INTO admins (admin_name,admin_email,admin_pass,admin_image,admin_contact,admin_country,admin_job,admin_about,permissions,is_super_admin) VALUES ('" . mysqli_real_escape_string($con, $admin_name) . "','" . mysqli_real_escape_string($con, $admin_email) . "','" . mysqli_real_escape_string($con, $admin_pass) . "','" . mysqli_real_escape_string($con, $admin_image) . "','" . mysqli_real_escape_string($con, $admin_contact) . "','" . mysqli_real_escape_string($con, $admin_country) . "','" . mysqli_real_escape_string($con, $admin_job) . "','" . mysqli_real_escape_string($con, $admin_about) . "','$perm_esc','$is_super')";
        $run_admin = mysqli_query($con, $insert_admin);
        if ($run_admin) {
            echo "<script>alert('One User Has Been Inserted successfully')</script>";
            echo "<script>window.open('index.php?view_users','_self')</script>";
        }
    }
    ?>
<?php }
?>