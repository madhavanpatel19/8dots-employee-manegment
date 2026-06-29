<?php
if (!isset($con)) {
    if (!isset($con)) {
        include(__DIR__ . '/../../includes/db.php');
    }
}

require_once __DIR__ . '/../../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// File Upload Function
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

if (isset($_POST['submit'])) {
    echo '
    <div id="php_server_loader" style="width: 100%; min-height: 80vh; background: transparent; display: flex; flex-direction: column; align-items: center; justify-content: center; font-family: sans-serif;">
        <div style="width: 50px; height: 50px; border: 4px solid #f1f5f9; border-top: 4px solid #DF2127; border-radius: 50%; animation: spin 1s linear infinite;"></div>
        <h3 style="margin-top: 20px; color: #1e293b;">Saving Employee...</h3>
        <p style="color: #64748b; margin-top: 5px;">Please wait while we upload documents and send the login email.</p>
        <style>@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }</style>
    </div>
    ';
    @ob_flush();
    @flush();

    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $contact = preg_replace('/\D+/', '', $_POST['number']);

    if (strlen($contact) != 10) {
        echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body style='background:#f1f5f9;'>";
        echo "<script>
            if(document.getElementById('php_server_loader')) document.getElementById('php_server_loader').style.display = 'none';
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Phone must be 10 digits!',
                    confirmButtonColor: '#DF2127'
                }).then((result) => {
                    window.history.back();
                });
            });
        </script></body></html>";
        exit();
    }

    $address = mysqli_real_escape_string($con, $_POST['address']);
    $blood = mysqli_real_escape_string($con, $_POST['blood']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $joinDate = mysqli_real_escape_string($con, $_POST['joinDate']);

    $basic = $_POST['basic_salary'] ?? 0;
    $hra = $_POST['hra'] ?? 0;
    $allowance = $_POST['allowance'] ?? 0;
    $deductions = $_POST['deductions'] ?? 0;
    $salary = $_POST['salary'] ?? 0;

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

    $offer_letter = handleFileUpload($_FILES['offer_latter']);
    $NDA = handleFileUpload($_FILES['NDA']);
    $Aadhar_card = handleFileUpload($_FILES['Aadhar_card']);
    $Pan_card = handleFileUpload($_FILES['Pan_card']);
    $Passportsize_photo = handleFileUpload($_FILES['Passportsize_photo']);
    $old_company_slary_slip = handleFileUpload($_FILES['old_company_slary_slip']);

    $employee_image = '';
    if (isset($_FILES['employee_image']) && $_FILES['employee_image']['error'] == 0) {
        $employee_image = handleFileUpload($_FILES['employee_image']);
    }

    $plainPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

    $query = "INSERT INTO emp_list 
    (name, phone_number, address, email, blood_group, gender, join_date, basic_salary, hra, allowance, deductions, salary, password, 
    age, dob, work_experience, marital_status, num_dependents, emergency_name, emergency_relationship, emergency_address, emergency_phone, 
    education_json, employment_json, account_name, bank_branch, account_number, account_type_ifsc, employee_image, offer_latter, NDA, Aadhar_card, Pan_card, Passportsize_photo, old_company_slary_slip)
              VALUES 
    ('$name', '$contact', '$address', '$email', '$blood', '$gender', '$joinDate', '$basic', '$hra', '$allowance', '$deductions', '$salary', '$plainPassword', 
    '$age', '$dob', '$work_exp', '$marital', '$dependents', '$e_name', '$e_rel', '$e_addr', '$e_phone', 
    '$edu_json', '$emp_json', '$acc_name', '$bank_br', '$acc_num', '$acc_ifsc', '$employee_image', '$offer_letter', '$NDA', '$Aadhar_card', '$Pan_card', '$Passportsize_photo', '$old_company_slary_slip')";

    $run = mysqli_query($con, $query);

    if ($run) {
        $emp_id = mysqli_insert_id($con);
        if (!empty($_FILES['documents']['name'][0])) {
            foreach ($_FILES['documents']['name'] as $key => $name) {
                if ($_FILES['documents']['error'][$key] == 0) {
                    $tmp_name = $_FILES['documents']['tmp_name'][$key];
                    $ext = pathinfo($name, PATHINFO_EXTENSION);
                    $new_name = time() . '_extra_' . rand(1000, 9999) . '.' . $ext;
                    if (move_uploaded_file($tmp_name, "../../uploads/" . $new_name)) {
                        mysqli_query($con, "INSERT INTO employee_documents (emp_id, file_name) VALUES ('$emp_id', '$new_name')");
                    }
                }
            }
        }

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'madhavanpatel19@gmail.com';
            $mail->Password = 'yawi nqpw wbhp icrx';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->setFrom('madhavanpatel19@gmail.com', '8DOTS');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Employee Login Password';
            $mail->Body = "<h3>Welcome to 8DOTS</h3><p>Your login password is: <b>$plainPassword</b></p><p>Please login from Employee Portal.</p>";
            $mail->send();
        } catch (Exception $e) {
        }

?>
        <link rel="stylesheet" href="css/success_notification.css">
        <style>
            #php_server_loader {
                display: none !important;
            }
        </style>
        <div class="success-modal-overlay" id="successModal">
            <div class="success-modal-content">
                <div class="success-icon-wrapper">
                    <i class="fa fa-check"></i>
                </div>
                <h2 class="success-title">Success!</h2>
                <p class="success-message">Employee <strong><?php echo $name; ?></strong> has been registered successfully. The login password has been sent to their email.</p>
                <div class="success-actions">
                    <a href="index.php?emp_directory" class="btn-success-go">
                        <i class="fa fa-users"></i> Go to Directory
                    </a>
                </div>
                <div class="success-timer-bar" style="animation-duration: 4s;"></div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('successModal');
                setTimeout(() => {
                    modal.classList.add('active');
                }, 100);

                // Auto redirect after 4 seconds
                setTimeout(() => {
                    window.location.href = 'index.php?emp_directory';
                }, 4000);
            });
        </script>
<?php
        exit();
    } else {
        $dbError = addslashes(mysqli_error($con));
        echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body style='background:#f1f5f9;'>";
        echo "<script>
            if(document.getElementById('php_server_loader')) document.getElementById('php_server_loader').style.display = 'none';
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Database Error',
                    text: '{$dbError}',
                    confirmButtonColor: '#DF2127'
                }).then((result) => {
                    window.history.back();
                });
            });
        </script></body></html>";
        exit();
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
    <form method="POST" id="add_employee_form" enctype="multipart/form-data">

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
                                <img id="add_preview" src="admin_images/default.png" style="width: 140px; height: 140px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                                <label for="employee_image" style="position: absolute; bottom: 5px; right: 5px; background: #333; color: #fff; width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 2px solid #fff; transition: 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                                    <i class="fa fa-camera" style="font-size: 16px;"></i>
                                </label>
                                <input type="file" name="employee_image" id="employee_image" style="display: none;" accept="image/*" onchange="handleImagePreview(this, 'add_preview')">
                            </div>
                            <small style="color: #64748b; margin-top: 12px; display: block; font-weight: 600;">Profile Photo *</small>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Full Name *</label>
                                    <input type="text" name="name" class="p-input-premium" placeholder="Enter Full Name" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Gender *</label>
                                    <select name="gender" class="p-input-premium" required>
                                        <option value="">Select</option>
                                        <option>Male</option>
                                        <option>Female</option>
                                        <option>Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Blood Group *</label>
                                    <select name="blood" class="p-input-premium" required>
                                        <option value="">Select</option>
                                        <option>A+</option>
                                        <option>B+</option>
                                        <option>O+</option>
                                        <option>AB+</option>
                                        <option>A-</option>
                                        <option>B-</option>
                                        <option>O-</option>
                                        <option>AB-</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 12px;">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Date of Birth *</label>
                                    <input type="date" id="add_dob" name="dob" class="p-input-premium" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Age (Auto)</label>
                                    <input type="number" name="age" id="add_age" class="p-input-premium" readonly style="background: #f8fafc; cursor: not-allowed;" placeholder="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Marital Status</label>
                                    <div class="p-radio-group" style="height: 48px; align-items: center;">
                                        <label class="p-radio-item" style="margin-bottom: 0;">
                                            <input type="radio" name="marital_status" value="Single"> Single
                                        </label>
                                        <label class="p-radio-item" style="margin-bottom: 0;">
                                            <input type="radio" name="marital_status" value="Married"> Married
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Dependent(s)</label>
                                    <input type="number" name="num_dependents" class="p-input-premium" value="0">
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
                                <input type="tel" name="number" class="p-input-premium" maxlength="10" placeholder="10-digit mobile" required style="padding-left: 40px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Email Address *</label>
                            <div style="position: relative;">
                                <i class="fa fa-envelope" style="position: absolute; left: 15px; top: 16px; color: #64748b; font-size: 14px;"></i>
                                <input type="email" name="email" class="p-input-premium" placeholder="email@example.com" required style="padding-left: 40px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Residential Address *</label>
                            <div style="position: relative;">
                                <i class="fa fa-map-marker" style="position: absolute; left: 15px; top: 16px; color: #64748b; font-size: 14px;"></i>
                                <input type="text" name="address" class="p-input-premium" placeholder="House No, Street, City, State" required style="padding-left: 40px;">
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
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Offer Letter</label>
                            <input type="file" name="offer_latter" class="p-input-premium">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Aadhar Card</label>
                            <input type="file" name="Aadhar_card" class="p-input-premium">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">PAN Card</label>
                            <input type="file" name="Pan_card" class="p-input-premium">
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top: 15px;">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">NDA Document</label>
                            <input type="file" name="NDA" class="p-input-premium">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Passport Photo</label>
                            <input type="file" name="Passportsize_photo" class="p-input-premium">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Previous Salary Slip</label>
                            <input type="file" name="old_company_slary_slip" class="p-input-premium">
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top: 15px;">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Additional Documents (Multiple)</label>
                            <input type="file" name="documents[]" class="p-input-premium" multiple style="height: auto;">
                            <small class="text-muted"><i class="fa fa-info-circle"></i> You can upload multiple files like certificates, residence proof, etc.</small>
                        </div>
                    </div>
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
                            <input type="text" name="emergency_name" class="p-input-premium" placeholder="Contact Name">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Relationship</label>
                            <input type="text" name="emergency_relationship" class="p-input-premium" placeholder="e.g. Father, Spouse">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Contact Phone</label>
                            <input type="tel" name="emergency_phone" class="p-input-premium" maxlength="10" placeholder="10-digit mobile">
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top: 15px;">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Address</label>
                            <input type="text" name="emergency_address" class="p-input-premium" placeholder="Full Emergency Contact Address">
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
                            <?php for ($i = 0; $i < 2; $i++): ?>
                                <tr>
                                    <td style="padding: 10px;"><input type="text" class="p-input-premium edu-degree" style="height: 38px; font-size: 13px;"></td>
                                    <td style="padding: 10px;"><input type="text" class="p-input-premium edu-univ" style="height: 38px; font-size: 13px;"></td>
                                    <td style="padding: 10px;"><input type="text" class="p-input-premium edu-year" style="height: 38px; font-size: 13px;"></td>
                                    <td style="padding: 10px;"><input type="text" class="p-input-premium edu-grade" style="height: 38px; font-size: 13px;"></td>
                                    <td style="padding: 10px;"><input type="text" class="p-input-premium edu-city" style="height: 38px; font-size: 13px;"></td>
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
                <button type="button" id="add_employment_row_btn" class="btn-premium-add" style="background: #f1f5f9 !important; color: #475569 !important; border: 1.5px solid #e2e8f0 !important; box-shadow: none !important; padding: 10px 22px !important; font-size: 14px !important;">
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
                            <?php for ($i = 0; $i < 2; $i++): ?>
                                <tr>
                                    <td style="padding: 10px;"><input type="text" class="p-input-premium hist-company" style="height: 38px; font-size: 13px;"></td>
                                    <td style="padding: 10px;"><input type="text" class="p-input-premium hist-pos" style="height: 38px; font-size: 13px;"></td>
                                    <td style="padding: 10px;"><input type="text" class="p-input-premium hist-year" style="height: 38px; font-size: 13px;"></td>
                                    <td style="padding: 10px;"><input type="text" class="p-input-premium hist-reason" style="height: 38px; font-size: 13px;"></td>
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
                            <input type="text" name="account_name" class="p-input-premium" placeholder="Name as per Passbook">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Bank & Branch</label>
                            <input type="text" name="bank_branch" class="p-input-premium" placeholder="Bank Name, Branch Name">
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top: 15px;">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Account Number</label>
                            <input type="text" name="account_number" class="p-input-premium" placeholder="Enter Full Account Number">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">IFSC Code / Account Type</label>
                            <input type="text" name="account_type_ifsc" class="p-input-premium" placeholder="Enter IFSC & Account Type">
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
                            <input type="date" name="joinDate" class="p-input-premium" max="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Basic Pay *</label>
                            <input type="number" id="add_basic_salary" name="basic_salary" class="p-input-premium" placeholder="Monthly Basic" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">HRA</label>
                            <input type="number" id="add_hra" name="hra" class="p-input-premium" placeholder="Allowance">
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top: 15px;">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Other Allowance</label>
                            <input type="number" id="add_allowance" name="allowance" class="p-input-premium" placeholder="Additional">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Monthly Deductions</label>
                            <input type="number" id="add_deductions" name="deductions" class="p-input-premium" placeholder="Tax, PF, etc.">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="font-weight: 600; color: #475569; margin-bottom: 8px; display: block;">Net Monthly Salary</label>
                            <input type="text" id="add_salary" name="salary" class="p-input-premium" readonly style="background: #f0fdf4; font-weight: 800; color: #059669; font-size: 18px; border-color: #bbf7d0;">
                        </div>
                    </div>
                </div>


            </div>
        </div>

        <div style="margin: 0 30px 40px 30px; text-align: right;">
            <button type="submit" name="submit" class="btn-premium-add" style="padding: 14px 40px !important; font-size: 16px !important; background: #DF2127 !important; border: none; box-shadow: 0 4px 6px -1px rgba(223, 33, 39, 0.3);">
                <i class="fa fa-user-plus"></i> Register New Employee
            </button>
        </div>

    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    function employmentHistoryRowHtmlAdd() {
        return '<td style="padding: 10px;"><input type="text" class="p-input-premium hist-company" style="height: 38px; font-size: 13px;"></td>' +
            '<td style="padding: 10px;"><input type="text" class="p-input-premium hist-pos" style="height: 38px; font-size: 13px;"></td>' +
            '<td style="padding: 10px;"><input type="text" class="p-input-premium hist-year" style="height: 38px; font-size: 13px;"></td>' +
            '<td style="padding: 10px;"><input type="text" class="p-input-premium hist-reason" style="height: 38px; font-size: 13px;"></td>' +
            '<td style="text-align: center; vertical-align: middle; padding: 10px;">' +
            '<button type="button" class="employment-remove-row btn btn-danger btn-sm" title="Remove row" style="min-width: 36px; border-radius: 8px;">×</button></td>';
    }

    function addEmploymentRow() {
        const tbody = document.getElementById('employment_body');
        if (!tbody) return;
        const tr = document.createElement('tr');
        tr.innerHTML = employmentHistoryRowHtmlAdd();
        tbody.appendChild(tr);
    }

    document.getElementById('add_employment_row_btn')?.addEventListener('click', addEmploymentRow);

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.employment-remove-row');
        if (!btn || !document.getElementById('employment_body')?.contains(btn)) return;
        const tr = btn.closest('tr');
        if (tr) tr.remove();
    });

    document.getElementById('add_employee_form')?.addEventListener('submit', function() {
        serializeTables();

        // Ensure SweetAlert is loaded, or fallback to native if not available
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Saving Employee...',
                text: 'Please wait while we upload documents and send the login email.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
    });

    document.getElementById('add_dob').addEventListener('change', function() {
        autoCalculateAge(this.value, 'add_age');
    });

    function calculateTotalAdd() {
        let basic = parseFloat(document.getElementById('add_basic_salary').value) || 0;
        let hra = parseFloat(document.getElementById('add_hra').value) || 0;
        let allowance = parseFloat(document.getElementById('add_allowance').value) || 0;
        let deduction = parseFloat(document.getElementById('add_deductions').value) || 0;
        document.getElementById('add_salary').value = (basic + hra + allowance - deduction).toFixed(2);
    }

    ['add_basic_salary', 'add_hra', 'add_allowance', 'add_deductions'].forEach(id => {
        document.getElementById(id).addEventListener('input', calculateTotalAdd);
    });
</script>