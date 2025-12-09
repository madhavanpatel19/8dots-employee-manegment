<?php
include 'connection.php';
require_once __DIR__ . '/includes/firebase_sync.php';

$db = firebase_db();  

$employee = null;
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($con, $_GET['id']);
    $query = "SELECT * FROM emp_list WHERE id = '$id'";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $employee = mysqli_fetch_assoc($result);
    }
}

// Update employee data
if (isset($_POST['update'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    // Normalize and validate contact number: digits only and must be 10 digits
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

    $query = "UPDATE emp_list SET 
              name = '$name',
              phone_number = '$contact',
              address = '$address',
              email = '$email',
              blood_group = '$blood',
              gender = '$gender',
              join_date = '$joinDate',
              basic_salary = '$basic_salary',
              hra = '$hra',
              allowance = '$allowance',
              deductions = '$deductions',
              salary = '$salary'
              WHERE id = '$id'";
              
    $result = mysqli_query($con, $query);
    if ($result) {
        $rowRes = mysqli_query($con, "SELECT * FROM emp_list WHERE id = '$id' LIMIT 1");
        if ($rowRes && $row = mysqli_fetch_assoc($rowRes)) {
            try {
                firebase_sync_row($db, 'emp_list', (string)$id, $row);
            } catch (Throwable $e) {
                // ignore Firebase sync failure to not block UI
            }
        }
        echo "<script>
          alert('Employee updated successfully');
          window.location.href = 'index.php?emp_directory';
        </script>";
    } else {
        echo "<script>alert('Error updating employee');</script>";
    }
}
?>

  <div class="row">
    <div class="col-lg-12">
      <h1 class="page-header">Edit Employee</h1>
      <ol class="breadcrumb">
        <li>
          <i class="fa fa-users"></i> Employees
        </li>
        <li class="active">Edit Employee</li>
      </ol>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h4 class="panel-title"><i class="fa fa-edit"></i> Edit Employee</h4>
        </div>
        <div class="panel-body">
          <?php if ($employee): ?>
          <form id="employeeForm" method="POST" action="" class="form-horizontal">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($employee['id']); ?>" />

            <div class="form-group">
              <label class="col-sm-3 control-label" for="name">Employee Name</label>
              <div class="col-sm-9">
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($employee['name']); ?>" class="form-control" required />
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="number">Phone Number</label>
              <div class="col-sm-9">
                <input type="tel" id="number" name="number" value="<?php echo htmlspecialchars($employee['phone_number']); ?>" class="form-control" required maxlength="10" pattern="\d{10}" inputmode="numeric" />
                
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="address">Address</label>
              <div class="col-sm-9">
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($employee['address']); ?>" class="form-control" required />
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="email">Email</label>
              <div class="col-sm-9">
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($employee['email']); ?>" class="form-control" required />
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="blood">Blood Group</label>
              <div class="col-sm-9">
                <select id="blood" name="blood" class="form-control" required>
                  <?php
                  $blood_groups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                  foreach ($blood_groups as $bg) {
                      $selected = ($employee['blood_group'] == $bg) ? 'selected' : '';
                      echo "<option value='$bg' $selected>$bg</option>";
                  }
                  ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="gender">Gender</label>
              <div class="col-sm-9">
                <select id="gender" name="gender" class="form-control" required>
                  <?php
                  $genders = ['Male', 'Female', 'Other'];
                  foreach ($genders as $g) {
                      $selected = ($employee['gender'] == $g) ? 'selected' : '';
                      echo "<option value='$g' $selected>$g</option>";
                  }
                  ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="joinDate">Join Date</label>
              <div class="col-sm-9">
                <input type="date" id="joinDate" name="joinDate" value="<?php echo htmlspecialchars($employee['join_date']); ?>" class="form-control" required max="<?php echo date('Y-m-d'); ?>" />
              </div>
            </div>

            <hr>
            <h4 style="margin-top: 20px; margin-bottom: 15px;">Salary Structure</h4>
            <hr>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="basic_salary">Basic Pay</label>
              <div class="col-sm-9">
                <input type="text" id="basic_salary" name="basic_salary" value="<?php echo htmlspecialchars($employee['basic_salary'] ?? ''); ?>" class="form-control" placeholder="Enter basic pay" required />
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="hra">HRA</label>
              <div class="col-sm-9">
                <input type="text" id="hra" name="hra" value="<?php echo htmlspecialchars($employee['hra'] ?? ''); ?>" class="form-control" placeholder="Enter HRA" />
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="allowance">Other Allowances</label>
              <div class="col-sm-9">
                <input type="text" id="allowance" name="allowance" value="<?php echo htmlspecialchars($employee['allowance'] ?? ''); ?>" class="form-control" placeholder="Enter allowances" />
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="deductions">Deductions</label>
              <div class="col-sm-9">
                <input type="number" id="deductions" name="deductions" value="<?php echo htmlspecialchars($employee['deductions'] ?? ''); ?>" class="form-control" placeholder="Enter deductions" />
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="salary">Total Salary</label>
              <div class="col-sm-9">
                <input type="text" id="salary" name="salary" value="<?php echo htmlspecialchars($employee['salary']); ?>" class="form-control" readonly />
              </div>
            </div>

            <div class="form-group">
              <div class="col-sm-offset-3 col-sm-9">
                <button type="submit" name="update" class="btn btn-primary">
                  <i class="fa fa-save"></i> Update Employee
                </button>
                <a href="index.php?emp_directory" class="btn btn-default">
                  <i class="fa fa-arrow-left"></i> Back to List
                </a>
              </div>
            </div>
          </form>
          <?php else: ?>
            <p>Employee not found.</p>
          <?php endif; ?>
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
      if (el) el.addEventListener('input', calculateTotal);
    });

    // Prevent future join date on client-side as well
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
      });
    }
  </script>
    
