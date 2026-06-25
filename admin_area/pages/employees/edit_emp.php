<?php
if (!isset($con)) {
    if (!isset($con)) {
        include(__DIR__ . '/../../includes/db.php');
    }
}

$employee = null;
if (isset($_GET['edit_emp'])) {
    $id = mysqli_real_escape_string($con, $_GET['edit_emp']);
    $query = "SELECT * FROM emp_list WHERE id = '$id'";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $employee = mysqli_fetch_assoc($result);
    }
}

if (!$employee) {
    echo "<script>alert('Employee not found'); window.location='index.php?emp_directory';</script>";
    exit;
}

// File Upload Handler
if (!function_exists('handleFileUpload')) {
    function handleFileUpload($fileArray, $targetDir = __DIR__ . "/../../uploads/")
    {
        if (isset($fileArray) && $fileArray['error'] == 0) {
            $file_name = $fileArray['name'];
            $tmp_name = $fileArray['tmp_name'];
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $new_name = time() . '_' . rand(1000, 9999) . '.' . $ext;
            $target_path = $targetDir . $new_name;
            if (move_uploaded_file($tmp_name, $target_path)) {
                // PDF Unlock Logic
                if ($ext === "pdf") {
                    $qpdf = "C:/Program Files/qpdf/qpdf 12.3.2/bin/qpdf.exe";
                    $unlocked_file = $targetDir . "unlock_" . $new_name;
                    $command = "\"$qpdf\" --decrypt \"$target_path\" \"$unlocked_file\" 2>&1";
                    exec($command, $output, $return_var);
                    if ($return_var === 0 && file_exists($unlocked_file)) {
                        unlink($target_path);
                        rename($unlocked_file, $target_path);
                    }
                }
                return $new_name;
            }
        }
        return '';
    }
}

// Update employee data
if (isset($_POST['update'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $contact = preg_replace('/\D+/', '', $_POST['number']);

    if (strlen($contact) != 10) {
        echo "<script>alert('Contact must be 10 digits'); window.history.back();</script>";
        exit;
    }

    $address = mysqli_real_escape_string($con, $_POST['address']);
    $blood = mysqli_real_escape_string($con, $_POST['blood']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $joinDate = mysqli_real_escape_string($con, $_POST['joinDate']);

    $age = mysqli_real_escape_string($con, $_POST['age'] ?? '');
    $dob = mysqli_real_escape_string($con, $_POST['dob'] ?? '');
    $work_exp = mysqli_real_escape_string($con, $_POST['work_experience'] ?? '');
    $marital = mysqli_real_escape_string($con, $_POST['marital_status'] ?? '');
    $dependents = mysqli_real_escape_string($con, $_POST['num_dependents'] ?? 0);

    $e_name = mysqli_real_escape_string($con, $_POST['emergency_name'] ?? '');
    $e_rel = mysqli_real_escape_string($con, $_POST['emergency_relationship'] ?? '');
    $e_addr = mysqli_real_escape_string($con, $_POST['emergency_address'] ?? '');
    $e_phone = mysqli_real_escape_string($con, $_POST['emergency_phone'] ?? '');

    $edu_json = mysqli_real_escape_string($con, $_POST['education_json'] ?? '[]');
    $emp_json = mysqli_real_escape_string($con, $_POST['employment_json'] ?? '[]');

    $acc_name = mysqli_real_escape_string($con, $_POST['account_name'] ?? '');
    $bank_br = mysqli_real_escape_string($con, $_POST['bank_branch'] ?? '');
    $acc_num = mysqli_real_escape_string($con, $_POST['account_number'] ?? '');
    $acc_ifsc = mysqli_real_escape_string($con, $_POST['account_type_ifsc'] ?? '');

    $basic = $_POST['basic_salary'] ?? 0;
    $hra = $_POST['hra'] ?? 0;
    $allowance = $_POST['allowance'] ?? 0;
    $deductions = $_POST['deductions'] ?? 0;
    $salary = $_POST['salary'] ?? 0;
    $status = isset($_POST['status']) ? 'Active' : 'Inactive';

    // Handle File Updates
    $q_extra = "";
    $docs_to_check = [
        'employee_image' => 'employee_image',
        'offer_latter' => 'offer_latter',
        'NDA' => 'NDA',
        'Aadhar_card' => 'Aadhar_card',
        'Pan_card' => 'Pan_card',
        'Passportsize_photo' => 'Passportsize_photo',
        'old_company_slary_slip' => 'old_company_slary_slip'
    ];

    foreach ($docs_to_check as $postKey => $dbCol) {
        if (!empty($_FILES[$postKey]['name'])) {
            $newFile = handleFileUpload($_FILES[$postKey]);
            if ($newFile) {
                // Delete old file if exists
                if (!empty($employee[$dbCol]) && file_exists("../../uploads/" . $employee[$dbCol])) {
                    @unlink("../../uploads/" . $employee[$dbCol]);
                }
                $q_extra .= ", $dbCol = '$newFile'";
            }
        }
    }

    $query = "UPDATE emp_list SET 
              name = '$name', phone_number = '$contact', address = '$address', email = '$email', blood_group = '$blood', gender = '$gender', join_date = '$joinDate',
              age = '$age', dob = '$dob', work_experience = '$work_exp', marital_status = '$marital', num_dependents = '$dependents',
              emergency_name = '$e_name', emergency_relationship = '$e_rel', emergency_address = '$e_addr', emergency_phone = '$e_phone',
              education_json = '$edu_json', employment_json = '$emp_json', 
              account_name = '$acc_name', bank_branch = '$bank_br', account_number = '$acc_num', account_type_ifsc = '$acc_ifsc',
              basic_salary = '$basic', hra = '$hra', allowance = '$allowance', deductions = '$deductions', salary = '$salary', status = '$status'
              $q_extra
              WHERE id = '$id'";

    $result = mysqli_query($con, $query);
    if ($result) {
        // Handle extra documents
        if (!empty($_FILES['documents']['name'][0])) {
            foreach ($_FILES['documents']['name'] as $key => $name) {
                if ($_FILES['documents']['error'][$key] == 0) {
                    $new_name = handleFileUpload(['name' => $name, 'tmp_name' => $_FILES['documents']['tmp_name'][$key], 'error' => 0]);
                    if ($new_name) {
                        mysqli_query($con, "INSERT INTO employee_documents (emp_id, file_name) VALUES ('$id', '$new_name')");
                    }
                }
            }
        }
        echo "<script>alert('Profile Updated Successfully'); window.location.href = 'index.php?emp_directory';</script>";
    } else {
        echo "<script>alert('Error updating: " . mysqli_error($con) . "');</script>";
    }
}
?>

<div class="page-wrapper premium-ui-enabled">
    <div class="page-header-premium">
        <h1></h1>
        <div class="header-actions-premium">
            <a href="index.php?emp_directory" class="btn-premium-add" style="background: #f1f5f9 !important; color: #475569 !important; border: 1.5px solid #e2e8f0 !important; box-shadow: none !important;">
                <i class="fa fa-arrow-left"></i> Back to Directory
            </a>
        </div>
    </div>

    <form method="POST" id="edit_employee_form" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $employee['id']; ?>">

        <!-- 1. Personal Information -->
        <div class="premium-card" style="margin: 0 30px 30px 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); background: #fff;">
            <div style="padding: 25px 30px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 15px;">
                <div style="width: 32px; height: 32px; background: #DF2127; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">1</div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #1e293b;">Personal Information</h3>
                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">Basic details and identity</p>
                </div>
            </div>
            <div style="padding: 30px;">

                <!-- Identity & Photo Section -->
                <div class="row align-items-center" style="margin-bottom: 25px;">
                    <div class="col-md-3">
                        <div class="form-group text-center" style="margin-bottom: 0;">
                            <div style="position: relative; display: inline-block;">
                                <img id="edit_preview" src="uploads/<?php echo !empty($employee['employee_image']) ? $employee['employee_image'] : '../admin_images/default.png'; ?>" style="width: 140px; height: 140px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                                <label for="employee_image" style="position: absolute; bottom: 5px; right: 5px; background: #333; color: #fff; width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 2px solid #fff; transition: 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                                    <i class="fa fa-pencil" style="font-size: 16px;"></i>
                                </label>
                                <input type="file" name="employee_image" id="employee_image" style="display: none;" accept="image/*" onchange="handleImagePreview(this, 'edit_preview')">
                            </div>
                            <small style="color: #64748b; margin-top: 12px; display: block; font-weight: 600;">Edit Profile Photo</small>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Full Name *</label>
                                    <input type="text" name="name" class="p-input-premium" value="<?php echo htmlspecialchars($employee['name']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Gender *</label>
                                    <select name="gender" class="p-input-premium" required>
                                        <option <?php if ($employee['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                                        <option <?php if ($employee['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                                        <option <?php if ($employee['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Blood Group *</label>
                                    <select name="blood" class="p-input-premium" required>
                                        <?php $bgs = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                                        foreach ($bgs as $bg) echo "<option " . ($employee['blood_group'] == $bg ? 'selected' : '') . ">$bg</option>"; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 12px;">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Date of Birth *</label>
                                    <input type="date" id="edit_dob" name="dob" class="p-input-premium" value="<?php echo $employee['dob']; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Age (Auto)</label>
                                    <input type="number" name="age" id="edit_age" class="p-input-premium" value="<?php echo $employee['age']; ?>" readonly style="background: #f8fafc; cursor: not-allowed;">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Marital Status</label>
                                    <div class="p-radio-group" style="height: 48px; align-items: center;">
                                        <label class="p-radio-item" style="margin-bottom: 0;">
                                            <input type="radio" name="marital_status" value="Single" <?php if ($employee['marital_status'] == 'Single') echo 'checked'; ?>> Single
                                        </label>
                                        <label class="p-radio-item" style="margin-bottom: 0;">
                                            <input type="radio" name="marital_status" value="Married" <?php if ($employee['marital_status'] == 'Married') echo 'checked'; ?>> Married
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Dependent(s)</label>
                                    <input type="number" name="num_dependents" class="p-input-premium" value="<?php echo $employee['num_dependents']; ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- 2. Contact Information -->
        <div class="premium-card" style="margin: 0 30px 30px 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); background: #fff;">
            <div style="padding: 25px 30px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 15px;">
                <div style="width: 32px; height: 32px; background: #DF2127; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">2</div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #1e293b;">Contact Information</h3>
                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">How to reach the employee</p>
                </div>
            </div>
            <div style="padding: 30px;">
                <div class="row" style="margin-bottom: 10px;">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Contact Number *</label>
                            <div style="position: relative;">
                                <i class="fa fa-phone" style="position: absolute; left: 15px; top: 16px; color: #64748b; font-size: 14px;"></i>
                                <input type="tel" name="number" class="p-input-premium" value="<?php echo htmlspecialchars($employee['phone_number']); ?>" maxlength="10" required style="padding-left: 40px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Email Address *</label>
                            <div style="position: relative;">
                                <i class="fa fa-envelope" style="position: absolute; left: 15px; top: 16px; color: #64748b; font-size: 14px;"></i>
                                <input type="email" name="email" class="p-input-premium" value="<?php echo htmlspecialchars($employee['email']); ?>" required style="padding-left: 40px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Residential Address *</label>
                            <div style="position: relative;">
                                <i class="fa fa-map-marker" style="position: absolute; left: 15px; top: 16px; color: #64748b; font-size: 14px;"></i>
                                <input type="text" name="address" class="p-input-premium" value="<?php echo htmlspecialchars($employee['address']); ?>" required style="padding-left: 40px;">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- 3. Employee Documents -->
        <div class="premium-card" style="margin: 0 30px 30px 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); background: #fff;">
            <div style="padding: 25px 30px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 15px;">
                <div style="width: 32px; height: 32px; background: #DF2127; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">3</div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #1e293b;">Employee Documents</h3>
                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">Upload important files</p>
                </div>
            </div>
            <div style="padding: 30px;">

                <div class="row">
                    <?php
                    $docs = [
                        'Offer Letter' => 'offer_latter',
                        'Aadhar Card' => 'Aadhar_card',
                        'PAN Card' => 'Pan_card',
                        'NDA Document' => 'NDA',
                        'Passport Photo' => 'Passportsize_photo',
                        'Salary Slip' => 'old_company_slary_slip'
                    ];
                    foreach ($docs as $lbl => $fld): ?>
                        <div class="col-md-4" style="margin-bottom: 15px;">
                            <div class="form-group">
                                <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;"><?php echo $lbl; ?></label>
                                <input type="file" name="<?php echo $fld; ?>" class="p-input-premium">
                                <?php if (!empty($employee[$fld])): ?>
                                    <div style="margin-top: 8px; display: flex; align-items: center; gap: 8px; background: #f1f5f9; padding: 6px 12px; border-radius: 8px; width: fit-content;">
                                        <i class="fa fa-check-circle" style="color: #059669;"></i>
                                        <a href="uploads/<?php echo $employee[$fld]; ?>" target="_blank" style="font-size: 12px; color: #333; font-weight: 600; text-decoration: none;">View Current</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>

        <!-- 4. Emergency Contact Details -->
        <div class="premium-card" style="margin: 0 30px 30px 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); background: #fff;">
            <div style="padding: 25px 30px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 15px;">
                <div style="width: 32px; height: 32px; background: #DF2127; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">4</div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #1e293b;">Emergency Contact Details</h3>
                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">Who to call in an emergency</p>
                </div>
            </div>
            <div style="padding: 30px;">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Full Name</label>
                            <input type="text" name="emergency_name" class="p-input-premium" value="<?php echo htmlspecialchars($employee['emergency_name']); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Relationship</label>
                            <input type="text" name="emergency_relationship" class="p-input-premium" value="<?php echo htmlspecialchars($employee['emergency_relationship']); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Contact Phone</label>
                            <input type="tel" name="emergency_phone" class="p-input-premium" value="<?php echo htmlspecialchars($employee['emergency_phone']); ?>" maxlength="10">
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top: 15px;">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Address</label>
                            <input type="text" name="emergency_address" class="p-input-premium" value="<?php echo htmlspecialchars($employee['emergency_address']); ?>">
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- 5. Educational Background -->
        <div class="premium-card" style="margin: 0 30px 30px 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); background: #fff;">
            <div style="padding: 25px 30px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 15px;">
                <div style="width: 32px; height: 32px; background: #DF2127; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">5</div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #1e293b;">Educational Background</h3>
                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">Academic history</p>
                </div>
            </div>
            <div style="padding: 30px;">

                <div class="table-premium" style="overflow-x: auto; border: 1.5px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px;">
                    <table class="table" id="edu_table" style="margin-bottom: 0; min-width: 800px;">
                        <thead>
                            <tr style="background: #f8fafc;">
                                <th style="border: none;">Degree/Course</th>
                                <th style="border: none;">University/Institute</th>
                                <th style="border: none;">Year</th>
                                <th style="border: none;">Grade</th>
                                <th style="border: none;">City</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $edu_data = json_decode($employee['education_json'], true) ?: [];
                            for ($i = 0; $i < max(2, count($edu_data)); $i++):
                                $row = $edu_data[$i] ?? []; ?>
                                <tr>
                                    <td style="padding: 10px;"><input type="text" class="p-input-premium edu-degree" value="<?php echo htmlspecialchars($row['degree'] ?? ''); ?>" style="height: 38px; font-size: 13px;"></td>
                                    <td style="padding: 10px;"><input type="text" class="p-input-premium edu-univ" value="<?php echo htmlspecialchars($row['univ'] ?? ''); ?>" style="height: 38px; font-size: 13px;"></td>
                                    <td style="padding: 10px;"><input type="text" class="p-input-premium edu-year" value="<?php echo htmlspecialchars($row['year'] ?? ''); ?>" style="height: 38px; font-size: 13px;"></td>
                                    <td style="padding: 10px;"><input type="text" class="p-input-premium edu-grade" value="<?php echo htmlspecialchars($row['grade'] ?? ''); ?>" style="height: 38px; font-size: 13px;"></td>
                                    <td style="padding: 10px;"><input type="text" class="p-input-premium edu-city" value="<?php echo htmlspecialchars($row['city'] ?? ''); ?>" style="height: 38px; font-size: 13px;"></td>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
                <input type="hidden" name="education_json" id="education_json">

            </div>
        </div>

        <!-- 6. Employment History -->
        <div class="premium-card" style="margin: 0 30px 30px 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); background: #fff;">
            <div style="padding: 25px 30px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="width: 32px; height: 32px; background: #DF2127; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">6</div>
                    <div>
                        <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #1e293b;">Employment History</h3>
                        <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">Previous work experience</p>
                    </div>
                </div>
                <button type="button" id="edit_add_employment_row" class="btn-premium-add" style="background: #f1f5f9 !important; color: #475569 !important; border: 1.5px solid #e2e8f0 !important; box-shadow: none !important; padding: 10px 22px !important; font-size: 14px !important;">
                    <i class="fa fa-plus"></i> Add Row
                </button>
            </div>
            <div style="padding: 30px;">

                <div class="table-premium" style="overflow-x: auto; border: 1.5px solid #e2e8f0; border-radius: 12px; margin-bottom: 10px;">
                    <table class="table" id="emp_hist_table" style="margin-bottom: 0; min-width: 800px;">
                        <thead>
                            <tr style="background: #f8fafc;">
                                <th style="border: none;">Company Name</th>
                                <th style="border: none;">Position</th>
                                <th style="border: none;">Duration/Year</th>
                                <th style="border: none;">Reason for Leaving</th>
                                <th style="border: none; width: 56px; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="employment_body">
                            <?php
                            $hist_data = json_decode($employee['employment_json'], true) ?: [];
                            $hist_rows = max(2, count($hist_data));
                            for ($i = 0; $i < $hist_rows; $i++):
                                $row = $hist_data[$i] ?? [];
                                $pos_val = $row['pos'] ?? $row['position'] ?? '';
                            ?>
                                <tr>
                                    <td style="padding: 10px;"><input type="text" class="p-input-premium hist-company" value="<?php echo htmlspecialchars($row['company'] ?? ''); ?>" style="height: 38px; font-size: 13px;"></td>
                                    <td style="padding: 10px;"><input type="text" class="p-input-premium hist-pos" value="<?php echo htmlspecialchars($pos_val); ?>" style="height: 38px; font-size: 13px;"></td>
                                    <td style="padding: 10px;"><input type="text" class="p-input-premium hist-year" value="<?php echo htmlspecialchars($row['year'] ?? ''); ?>" style="height: 38px; font-size: 13px;"></td>
                                    <td style="padding: 10px;"><input type="text" class="p-input-premium hist-reason" value="<?php echo htmlspecialchars($row['reason'] ?? ''); ?>" style="height: 38px; font-size: 13px;"></td>
                                    <td style="text-align: center; vertical-align: middle; padding: 10px;">
                                        <button type="button" class="employment-remove-row btn btn-danger btn-sm" title="Remove row" style="min-width: 36px; border-radius: 8px;">×</button>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>

                <input type="hidden" name="employment_json" id="employment_json">

            </div>
        </div>

        <!-- 7. Bank Account Details -->
        <div class="premium-card" style="margin: 0 30px 30px 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); background: #fff;">
            <div style="padding: 25px 30px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 15px;">
                <div style="width: 32px; height: 32px; background: #DF2127; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">7</div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #1e293b;">Bank Account Details</h3>
                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">Financial information</p>
                </div>
            </div>
            <div style="padding: 30px;">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Account Holder Name</label>
                            <input type="text" name="account_name" class="p-input-premium" value="<?php echo htmlspecialchars($employee['account_name']); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Bank & Branch</label>
                            <input type="text" name="bank_branch" class="p-input-premium" value="<?php echo htmlspecialchars($employee['bank_branch']); ?>">
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top: 15px;">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Account Number</label>
                            <input type="text" name="account_number" class="p-input-premium" value="<?php echo htmlspecialchars($employee['account_number']); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">IFSC Code / Account Type</label>
                            <input type="text" name="account_type_ifsc" class="p-input-premium" value="<?php echo htmlspecialchars($employee['account_type_ifsc']); ?>">
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- 8. Professional & Salary Details -->
        <div class="premium-card" style="margin: 0 30px 30px 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); background: #fff;">
            <div style="padding: 25px 30px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 15px;">
                <div style="width: 32px; height: 32px; background: #DF2127; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">8</div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #1e293b;">Professional & Salary Details</h3>
                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">Compensation structure</p>
                </div>
            </div>
            <div style="padding: 30px;">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Joining Date *</label>
                            <input type="date" name="joinDate" class="p-input-premium" value="<?php echo $employee['join_date']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Basic Pay *</label>
                            <input type="number" id="edit_basic_salary" name="basic_salary" class="p-input-premium" value="<?php echo $employee['basic_salary']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">HRA</label>
                            <input type="number" id="edit_hra" name="hra" class="p-input-premium" value="<?php echo $employee['hra']; ?>">
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top: 15px;">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Other Allowance</label>
                            <input type="number" id="edit_allowance" name="allowance" class="p-input-premium" value="<?php echo $employee['allowance']; ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Monthly Deductions</label>
                            <input type="number" id="edit_deductions" name="deductions" class="p-input-premium" value="<?php echo $employee['deductions']; ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Net Monthly Salary</label>
                            <input type="text" id="edit_salary" name="salary" class="p-input-premium" value="<?php echo $employee['salary']; ?>" readonly style="background: #f0fdf4; font-weight: 800; color: #059669; font-size: 18px; border-color: #bbf7d0;">
                        </div>
                    </div>
                </div>

                <style>
                    .status-switch {
                        position: relative;
                        display: inline-block;
                        width: 48px;
                        height: 26px;
                        margin-bottom: 0;
                    }

                    .status-switch input {
                        opacity: 0;
                        width: 0;
                        height: 0;
                    }

                    .status-slider {
                        position: absolute;
                        cursor: pointer;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        background-color: #cbd5e1;
                        transition: .4s;
                        border-radius: 34px;
                    }

                    .status-slider:before {
                        position: absolute;
                        content: "";
                        height: 20px;
                        width: 20px;
                        left: 3px;
                        bottom: 3px;
                        background-color: white;
                        transition: .4s;
                        border-radius: 50%;
                    }

                    .status-switch input:checked+.status-slider {
                        background-color: #2563eb;
                    }

                    .status-switch input:checked+.status-slider:before {
                        transform: translateX(22px);
                    }
                </style>
                <?php $is_active = (!isset($employee['status']) || $employee['status'] !== 'Inactive'); ?>
                <div style="margin-top: 30px; padding: 20px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 20px;">
                    <div style="flex-shrink: 0;">
                        <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Status <span style="color: red;">*</span></label>
                        <label class="status-switch">
                            <input type="checkbox" name="status" id="emp_status_toggle" value="Active" <?php echo $is_active ? 'checked' : ''; ?>>
                            <span class="status-slider"></span>
                        </label>
                    </div>
                    <div style="margin-top: 25px;">
                        <div style="font-weight: 700; color: #1e293b; font-size: 15px;"><span id="status_label_text"><?php echo $is_active ? 'Active' : 'Inactive'; ?></span> <span style="color: #94a3b8; font-weight: 400; margin-left: 8px;">| Inactive employees will not be able to access the system.</span></div>
                    </div>
                </div>
                <script>
                    document.getElementById('emp_status_toggle').addEventListener('change', function() {
                        document.getElementById('status_label_text').innerText = this.checked ? 'Active' : 'Inactive';
                        document.getElementById('status_label_text').style.color = this.checked ? '#1e293b' : '#64748b';
                    });
                </script>

            </div>
        </div>

        <div style="margin: 0 30px 40px 30px; text-align: right;">
            <button type="submit" name="update" class="btn-premium-add" style="padding: 14px 40px !important; font-size: 16px !important; background: #DF2127 !important; border: none; box-shadow: 0 4px 6px -1px rgba(223, 33, 39, 0.3);" onclick="serializeTables()">
                <i class="fa fa-save"></i> Update Employee Profile
            </button>
        </div>

    </form>
</div>

<script>
    function handleImagePreview(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function autoCalculateAge(dobValue, ageInputId) {
        if (!dobValue || dobValue === '0000-00-00') return;
        const birthDate = new Date(dobValue);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) age--;
        document.getElementById(ageInputId).value = age > 0 ? age : 0;
    }

    function serializeTables() {
        const edu = [];
        document.querySelectorAll('#edu_table tbody tr').forEach(tr => {
            const row = {
                degree: tr.querySelector('.edu-degree').value,
                univ: tr.querySelector('.edu-univ').value,
                year: tr.querySelector('.edu-year').value,
                grade: tr.querySelector('.edu-grade').value,
                city: tr.querySelector('.edu-city').value
            };
            if (row.degree || row.univ) edu.push(row);
        });
        document.getElementById('education_json').value = JSON.stringify(edu);

        const hist = [];
        document.querySelectorAll('#emp_hist_table tbody tr').forEach(tr => {
            const company = (tr.querySelector('.hist-company')?.value || '').trim();
            const pos = (tr.querySelector('.hist-pos')?.value || '').trim();
            const year = (tr.querySelector('.hist-year')?.value || '').trim();
            const reason = (tr.querySelector('.hist-reason')?.value || '').trim();
            if (company || pos || year || reason) {
                hist.push({
                    company,
                    pos,
                    year,
                    reason
                });
            }
        });
        document.getElementById('employment_json').value = JSON.stringify(hist);
    }

    function employmentHistoryRowHtml() {
        return '<td style="padding: 10px;"><input type="text" class="p-input-premium hist-company" style="height: 38px; font-size: 13px;"></td>' +
            '<td style="padding: 10px;"><input type="text" class="p-input-premium hist-pos" style="height: 38px; font-size: 13px;"></td>' +
            '<td style="padding: 10px;"><input type="text" class="p-input-premium hist-year" style="height: 38px; font-size: 13px;"></td>' +
            '<td style="padding: 10px;"><input type="text" class="p-input-premium hist-reason" style="height: 38px; font-size: 13px;"></td>' +
            '<td style="text-align: center; vertical-align: middle; padding: 10px;">' +
            '<button type="button" class="employment-remove-row btn btn-danger btn-sm" title="Remove row" style="min-width: 36px; border-radius: 8px;">×</button></td>';
    }

    function addEmploymentRowEdit() {
        const tbody = document.getElementById('employment_body');
        if (!tbody) return;
        const tr = document.createElement('tr');
        tr.innerHTML = employmentHistoryRowHtml();
        tbody.appendChild(tr);
    }

    document.getElementById('edit_add_employment_row')?.addEventListener('click', addEmploymentRowEdit);

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.employment-remove-row');
        if (!btn || !document.getElementById('employment_body')?.contains(btn)) return;
        const tr = btn.closest('tr');
        if (tr) tr.remove();
    });

    document.getElementById('edit_employee_form')?.addEventListener('submit', function() {
        serializeTables();
    });

    document.getElementById('edit_dob').addEventListener('change', function() {
        autoCalculateAge(this.value, 'edit_age');
    });

    function calculateTotalEdit() {
        let basic = parseFloat(document.getElementById('edit_basic_salary').value) || 0;
        let hra = parseFloat(document.getElementById('edit_hra').value) || 0;
        let allowance = parseFloat(document.getElementById('edit_allowance').value) || 0;
        let deduction = parseFloat(document.getElementById('edit_deductions').value) || 0;
        document.getElementById('edit_salary').value = (basic + hra + allowance - deduction).toFixed(2);
    }

    ['edit_basic_salary', 'edit_hra', 'edit_allowance', 'edit_deductions'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', calculateTotalEdit);
    });
</script>