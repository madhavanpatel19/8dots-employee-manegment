<?php
if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('pages/auth/login.php','_self')</script>";
    exit();
}

if (!isset($con)) {
    include(__DIR__ . '/../../includes/db.php');
}

if (isset($_GET['edit_nda'])) {
    $edit_id = mysqli_real_escape_string($con, $_GET['edit_nda']);
    $get_edit = "SELECT * FROM nda_forms WHERE id='$edit_id'";
    $run_edit = mysqli_query($con, $get_edit);
    $row_edit = mysqli_fetch_array($run_edit);

    if (!$row_edit) {
        echo "<script>Swal.fire({title: 'Notification', text: 'NDA record not found.', icon: 'error'}).then(() => { window.open('index.php?view_nda','_self'); });</script>";
        exit();
    }

    $id = $row_edit['id'];
    $name = $row_edit['name'];
    $employee_id = $row_edit['employee_id'];
    $department = $row_edit['department'];
    $designation = $row_edit['designation'];
    $start_date = $row_edit['start_date'];
}

if (isset($_POST['update_nda'])) {
    $update_id = mysqli_real_escape_string($con, $_POST['id']);
    $u_name = mysqli_real_escape_string($con, $_POST['name']);
    $u_employee_id = mysqli_real_escape_string($con, $_POST['employee_id']);
    $u_department = mysqli_real_escape_string($con, $_POST['department']);
    $u_designation = mysqli_real_escape_string($con, $_POST['designation']);
    $u_start_date = mysqli_real_escape_string($con, $_POST['start_date']);

    $update_query = "UPDATE nda_forms SET 
        name='$u_name', 
        employee_id='$u_employee_id', 
        department='$u_department', 
        designation='$u_designation', 
        start_date='$u_start_date' 
        WHERE id='$update_id'";

    $run_update = mysqli_query($con, $update_query);

    if ($run_update) {
        echo "<script>Swal.fire({title: 'Notification', text: 'NDA has been updated successfully', icon: 'success'});</script>";
        echo "<script>window.open('index.php?view_nda','_self')</script>";
    } else {
        echo "<script>Swal.fire({title: 'Notification', text: 'Error: Could not update NDA.', icon: 'error'});</script>";
    }
}
?>

<div class="page-wrapper premium-ui-enabled">
    <form method="post" action="">
        <div class="premium-card" style="margin: 0 30px 30px 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); background: #fff;">
            <div style="padding: 25px 30px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 15px;">
                <div style="width: 32px; height: 32px; background: var(--p-bg-color); color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">
                    1
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #1e293b;">Party Information</h3>
                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">Basic details about the NDA record</p>
                </div>
            </div>

            <div style="padding: 30px;">
                <input type="hidden" name="id" value="<?php echo $id; ?>">

                <div class="row">
                    <div class="col-md-6" style="margin-bottom: 20px;">
                        <label style="font-weight: 700; color: #475569; display: block; margin-bottom: 12px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Employee Name</label>
                        <input type="text" name="name" class="p-input-premium" value="<?php echo htmlspecialchars($name); ?>" required style="height: 48px; width: 100%; border-radius: 8px; border: 1px solid #e2e8f0; padding: 0 15px; transition: all 0.3s;" onfocus="this.style.borderColor='var(--p-bg-color)'; this.style.boxShadow='0 4px 10px rgba(166, 166, 167, 0.2)';" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';">
                    </div>
                    <div class="col-md-6" style="margin-bottom: 20px;">
                        <label style="font-weight: 700; color: #475569; display: block; margin-bottom: 12px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Employee ID</label>
                        <input type="text" name="employee_id" class="p-input-premium" value="<?php echo htmlspecialchars($employee_id); ?>" required style="height: 48px; width: 100%; border-radius: 8px; border: 1px solid #e2e8f0; padding: 0 15px; transition: all 0.3s;" onfocus="this.style.borderColor='var(--p-bg-color)'; this.style.boxShadow='0 4px 10px rgba(166, 166, 167, 0.2)';" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6" style="margin-bottom: 20px;">
                        <label style="font-weight: 700; color: #475569; display: block; margin-bottom: 12px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Department</label>
                        <input type="text" name="department" class="p-input-premium" value="<?php echo htmlspecialchars($department); ?>" required style="height: 48px; width: 100%; border-radius: 8px; border: 1px solid #e2e8f0; padding: 0 15px; transition: all 0.3s;" onfocus="this.style.borderColor='var(--p-bg-color)'; this.style.boxShadow='0 4px 10px rgba(166, 166, 167, 0.2)';" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';">
                    </div>
                    <div class="col-md-6" style="margin-bottom: 20px;">
                        <label style="font-weight: 700; color: #475569; display: block; margin-bottom: 12px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Designation</label>
                        <input type="text" name="designation" class="p-input-premium" value="<?php echo htmlspecialchars($designation); ?>" required style="height: 48px; width: 100%; border-radius: 8px; border: 1px solid #e2e8f0; padding: 0 15px; transition: all 0.3s;" onfocus="this.style.borderColor='var(--p-bg-color)'; this.style.boxShadow='0 4px 10px rgba(166, 166, 167, 0.2)';" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6" style="margin-bottom: 20px;">
                        <label style="font-weight: 700; color: #475569; display: block; margin-bottom: 12px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Joining Date</label>
                        <input type="date" name="start_date" class="p-input-premium" value="<?php echo $start_date; ?>" required style="height: 48px; width: 100%; border-radius: 8px; border: 1px solid #e2e8f0; padding: 0 15px; transition: all 0.3s;" onfocus="this.style.borderColor='var(--p-bg-color)'; this.style.boxShadow='0 4px 10px rgba(166, 166, 167, 0.2)';" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';">
                    </div>
                </div>

                <div style="margin-top: 30px; display: flex; justify-content: flex-end; gap: 15px;">
                    <a href="index.php?view_nda" class="btn-premium-cancel">Cancel</a>
                    <button type="submit" name="update_nda" class="btn-premium-add">Save Changes</button>
                </div>
            </div>
        </div>
    </form>
</div>