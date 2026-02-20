<?php
session_start();
include 'connection.php';
$errorFields = [];

// Only allow access if logged in
if (!isset($_SESSION['emp_id']) || !isset($_SESSION['emp_name'])) {
    header('Location: emp-login.php');
    exit();
}

$emp_id = $_SESSION['emp_id'];
$emp_name = $_SESSION['emp_name'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $required = ['date', 'start_time', 'end_time', 'task'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $errorFields[] = $field;
        }
    }

    if (empty($errorFields)) {
        // Insert into attendance table
        $attendance_date = mysqli_real_escape_string($con, $_POST['date']);
        $check_in_time = mysqli_real_escape_string($con, $_POST['start_time']);
        $check_out_time = mysqli_real_escape_string($con, $_POST['end_time']);
        $remarks = mysqli_real_escape_string($con, $_POST['task']);

        // Check if record exists for this emp/date
        $check = mysqli_query($con, "SELECT id FROM attendance WHERE emp_id='$emp_id' AND attendance_date='$attendance_date'");
        if (mysqli_num_rows($check) > 0) {
            // Update
            $update = "UPDATE attendance SET check_in_time='$check_in_time', check_out_time='$check_out_time', remarks='$remarks' WHERE emp_id='$emp_id' AND attendance_date='$attendance_date'";
            $success = mysqli_query($con, $update);
        } else {
            // Insert
            $insert = "INSERT INTO attendance (emp_id, attendance_date, check_in_time, check_out_time, status, remarks) VALUES ('$emp_id', '$attendance_date', '$check_in_time', '$check_out_time', 'present', '$remarks')";
            $success = mysqli_query($con, $insert);
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>8DOTS - Worksheet</title>
    <meta charset="UTF-8">
<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', Roboto, sans-serif;
        background: linear-gradient(135deg, #ffffff, #89888b);
        min-height: 100vh;
    }

    .form-container {
        max-width: 750px;
        margin: 40px auto;
        padding: 20px;
    }

    .form-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        transition: 0.3s ease;
    }

    .form-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.15);
    }

    h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 600;
        color: #333;
    }

    .subtitle {
        color: #777;
        margin-top: 6px;
        font-size: 15px;
    }

    label {
        font-size: 15px;
        font-weight: 600;
        display: block;
        margin-bottom: 12px;
        color: #444;
    }

    .required {
        color: #e53935;
    }

    input,
    textarea {
        width: 20%;
        padding: 12px;
        font-size: 14px;
        border-radius: 6px;
        border: 1px solid #ddd;
        transition: 0.3s;
        background: #f9f9f9;
    }

    input:focus,
    textarea:focus {
        border-color: #673ab7;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(103,58,183,0.1);
        outline: none;
    }

    textarea {
        resize: none;
        height: 20px;
    }

    .button-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .btn-submit {
        background: linear-gradient(135deg, #3ab73f, #349f3a);
        color: white;
        padding: 12px 28px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-submit:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    }

    .logout-btn a {
        text-decoration: none;
        background: #e53935;
        color: white;
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        transition: 0.3s;
    }

    .logout-btn a:hover {
        background: #c62828;
    }

    .clear-link {
        color: #673ab7;
        font-size: 14px;
        text-decoration: none;
        font-weight: 500;
    }

    .clear-link:hover {
        text-decoration: underline;
    }

    .success-message {
        color: #2e7d32;
        font-weight: 600;
        text-align: center;
    }

    .input-error {
        border: 1px solid #e53935 !important;
        background: #fff3f3;
    }

    @media (max-width: 600px) {
        .button-row {
            flex-direction: column;
            align-items: stretch;
        }

        .btn-submit,
        .logout-btn a {
            width: 100%;
            text-align: center;
        }
    }
</style>
</head>

<body>



    <div class="form-container">

        <div class="form-card">
            <h1>8DOTS - Worksheet</h1>
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div class="subtitle">Daily Activity</div>
                <div class="logout-btn" style="margin-left:auto;">
                    <a href="emp-logout.php">Logout</a>
                </div>
            </div>
            <!-- <div class="required-note">* Indicates required question</div> -->
            <div style="margin-top:10px; font-size:16px; color:#333;">
                Employee: <strong><?php echo htmlspecialchars($emp_name); ?></strong> (ID: <strong><?php echo htmlspecialchars($emp_id); ?></strong>)
            </div>
        </div>

        <?php if (!empty($success)) : ?>
            <div class="form-card">
                <div class="success-message">
                    Form submitted successfully!
                </div>
            </div>
        <?php endif; ?>


        <form method="POST" onsubmit="return validateWorksheetForm();">


            <!-- Date -->
            <div class="form-card <?php echo in_array('date', $errorFields) ? 'error' : ''; ?>">
                <label>Choose Date <span class="required">*</span></label>
                <input type="date" name="date" required>
            </div>

            <!-- Start Time -->
            <div class="form-card <?php echo in_array('start_time', $errorFields) ? 'error' : ''; ?>">
                <label>Start Time (Login Time) <span class="required">*</span></label>
                <input type="time" name="start_time" required>
            </div>

            <!-- Task -->
            <div class="form-card <?php echo in_array('task', $errorFields) ? 'error' : ''; ?>">
                <label>Task Details <span class="required">*</span></label>
                <textarea name="task" placeholder="Your answer" style="width: 94%;" required><?php echo isset($_POST['task']) ? htmlspecialchars($_POST['task']) : ''; ?></textarea>
                <?php if (in_array('task', $errorFields)) : ?>
                    <div class="error-text">This is a required question</div>
                <?php endif; ?>
            </div>

            <!-- End Time -->
            <div class="form-card <?php echo in_array('end_time', $errorFields) ? 'error' : ''; ?>">
                <label>End Time (Logout Time) <span class="required">*</span></label>
                <input type="time" name="end_time" required>
            </div>

            <!-- Buttons -->
            <div class="form-card button-row">
                <button type="submit" class="btn-submit">Submit</button>
                <a href="worksheet.php" class="clear-link">Clear form</a>
            </div>

        </form>

    </div>
    <script>
        function validateWorksheetForm() {
            var date = document.querySelector('input[name="date"]');
            var start = document.querySelector('input[name="start_time"]');
            var end = document.querySelector('input[name="end_time"]');
            var task = document.querySelector('textarea[name="task"]');
            var valid = true;
            [date, start, end, task].forEach(function(field) {
                if (!field.value) {
                    field.classList.add('input-error');
                    valid = false;
                } else {
                    field.classList.remove('input-error');
                }
            });
            return valid;
        }
    </script>
    <style>
        .input-error {
            border-bottom: 2px solid #e38f8f !important;
            background-color: #fffbe6;
        }
    </style>

</body>

</html>