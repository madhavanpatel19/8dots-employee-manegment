<?php
require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/includes/firebase_sync.php';

$db = firebase_db();
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Add New Employee
        </h1>
        <ol class="breadcrumb">
            <li>
                <i class="fa fa-users"></i> Employees
            </li>
            <li class="active">Add New Employee</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <i class="fa fa-user-plus"></i> Employee Information
                </h4>
            </div>
            <div class="panel-body">
                <form id="employeeForm" method="POST" action="" class="form-horizontal">
                    <div class="form-group">
                        <label for="name" class="col-sm-3 control-label">Employee Name <span style="color: red;">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" id="name" name="name" class="form-control" placeholder="Enter full name" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="number" class="col-sm-3 control-label">Phone Number <span style="color: red;">*</span></label>
                        <div class="col-sm-9">
                            <input type="tel" id="number" name="number" class="form-control" placeholder="Enter 10-digit phone number" required maxlength="10" pattern="\d{10}" title="Enter 10 digit phone number" inputmode="numeric" />
                            
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email" class="col-sm-3 control-label">Email <span style="color: red;">*</span></label>
                        <div class="col-sm-9">
                            <input type="email" id="email" name="email" class="form-control" placeholder="Enter email address" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address" class="col-sm-3 control-label">Address <span style="color: red;">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" id="address" name="address" class="form-control" placeholder="Enter address" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="blood" class="col-sm-3 control-label">Blood Group <span style="color: red;">*</span></label>
                        <div class="col-sm-9">
                            <select id="blood" name="blood" class="form-control" required>
                                <option value="">Select Blood Group</option>
                                <option>A+</option>
                                <option>A-</option>
                                <option>B+</option>
                                <option>B-</option>
                                <option>AB+</option>
                                <option>AB-</option>
                                <option>O+</option>
                                <option>O-</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="gender" class="col-sm-3 control-label">Gender <span style="color: red;">*</span></label>
                        <div class="col-sm-9">
                            <select id="gender" name="gender" class="form-control" required>
                                <option value="">Select Gender</option>
                                <option>Male</option>
                                <option>Female</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="joinDate" class="col-sm-3 control-label">Join Date <span style="color: red;">*</span></label>
                        <div class="col-sm-9">
                            <input type="date" id="joinDate" name="joinDate" class="form-control" required max="<?php echo date('Y-m-d'); ?>" />
                        </div>
                    </div>

                    <hr>
                    <h4 style="margin-top: 20px; margin-bottom: 15px;">Salary Structure</h4>
                    <hr>

                    <div class="form-group">
                        <label for="basic_salary" class="col-sm-3 control-label">Basic Pay <span style="color: red;">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" id="basic_salary" name="basic_salary" class="form-control" placeholder="Enter basic pay" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="hra" class="col-sm-3 control-label">HRA</label>
                        <div class="col-sm-9">
                            <input type="text" id="hra" name="hra" class="form-control" placeholder="Enter HRA amount" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="allowance" class="col-sm-3 control-label">Other Allowances</label>
                        <div class="col-sm-9">
                            <input type="text" id="allowance" name="allowance" class="form-control" placeholder="Enter other allowances" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="deductions" class="col-sm-3 control-label">Deductions</label>
                        <div class="col-sm-9">
                            <input type="text" id="deductions" name="deductions" class="form-control" placeholder="Enter deductions" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="salary" class="col-sm-3 control-label">Total Salary</label>
                        <div class="col-sm-9">
                            <input type="text" id="salary" name="salary" class="form-control" placeholder="Auto calculated" readonly />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                             <a href="index.php?emp_directory" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Back 
                            </a>
                            <button type="submit" name="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Add 
                            </button>
                           
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    // Auto calculate salary
    const basic = document.getElementById('basic_salary');
    const hra = document.getElementById('hra');
    const allowance = document.getElementById('allowance');
    const deductions = document.getElementById('deductions');
    const total = document.getElementById('salary');

    function calculateTotal() {
        const b = parseFloat(basic.value) || 0;
        const h = parseFloat(hra.value) || 0;
        const a = parseFloat(allowance.value) || 0;
        const d = parseFloat(deductions.value) || 0;
        total.value = (b + h + a - d).toFixed(2);
    }

    [basic, hra, allowance, deductions].forEach(el => {
        el.addEventListener('input', calculateTotal);
    });

        // Prevent future join date on client-side
        const joinDateEl = document.getElementById('joinDate');
        const form = document.getElementById('employeeForm');
        if (form && joinDateEl) {
            form.addEventListener('submit', function(e) {
                const today = new Date().toISOString().slice(0,10);
                if (joinDateEl.value && joinDateEl.value > today) {
                    e.preventDefault();
                    alert('Join date cannot be in the future. Please select a valid date.');
                    joinDateEl.focus();
                    return false;
                }
                // Validate phone number (10 digits)
                const phoneEl = document.getElementById('number');
                if (phoneEl && !/^[0-9]{10}$/.test(phoneEl.value)) {
                    e.preventDefault();
                    alert('Please enter a valid 10 digit contact number (digits only).');
                    phoneEl.focus();
                    return false;
                }
            });
        }
</script>

<?php        
if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    // Normalize and validate contact number: allow digits only and must be 10 digits
    $raw_contact = isset($_POST['number']) ? $_POST['number'] : '';
    $clean_contact = preg_replace('/\D+/', '', $raw_contact);
    if (strlen($clean_contact) != 10) {
        echo "<script>alert('Contact number must be 10 digits.'); window.history.back();</script>";
        exit();
    }
    $contact = mysqli_real_escape_string($con, $clean_contact);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $blood = mysqli_real_escape_string($con, $_POST['blood']);  
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $joinDate = mysqli_real_escape_string($con, $_POST['joinDate']);    
    $basic_salary = mysqli_real_escape_string($con, $_POST['basic_salary']);
    $hra = mysqli_real_escape_string($con, $_POST['hra']);
    $allowance = mysqli_real_escape_string($con, $_POST['allowance']);
    $deductions = mysqli_real_escape_string($con, $_POST['deductions']);
    $salary = mysqli_real_escape_string($con, $_POST['salary']);

    // Server-side validation: join date must not be in the future
    if (strtotime($joinDate) > strtotime(date('Y-m-d'))) {
        echo "<script>alert('Join date cannot be in the future. Please select a valid date.'); window.history.back();</script>";
        exit();
    }

    $query = "INSERT INTO emp_list 
              (name, phone_number, address, email, blood_group, gender, join_date, basic_salary, hra, allowance, deductions, salary) 
              VALUES 
              ('$name', '$contact', '$address', '$email', '$blood', '$gender', '$joinDate', '$basic_salary', '$hra', '$allowance', '$deductions', '$salary')";

    $result = mysqli_query($con, $query);
    if ($result) {
        $last_id = mysqli_insert_id($con);
        $rowRes = mysqli_query($con, "SELECT * FROM emp_list WHERE id = '$last_id' LIMIT 1");
        if ($rowRes && $row = mysqli_fetch_assoc($rowRes)) {
            try {
                firebase_sync_row($db, 'emp_list', (string)$last_id, $row);
            } catch (Throwable $e) {
                // continue even if Firebase sync fails
            }
        }
        echo "<script>alert('Employee added successfully'); window.location.href='index.php?emp_directory';</script>";
    } else {
        echo "<script>alert('Error adding employee: " . addslashes(mysqli_error($con)) . "');</script>";
    }
}
?>
