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
                            <div class='col-md-9'>
                                <div class='permissions-container'>
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