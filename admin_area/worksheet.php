<?php
include 'connection.php';
$errorFields = [];

// Fetch employees for dropdown
$employees = [];
$empRes = mysqli_query($con, "SELECT id, name FROM emp_list ORDER BY name ASC");
if ($empRes && mysqli_num_rows($empRes) > 0) {
    while ($row = mysqli_fetch_assoc($empRes)) {
        $employees[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $required = ['who', 'date', 'start_time', 'end_time', 'task'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $errorFields[] = $field;
        }
    }

    if (empty($errorFields)) {
        // Insert into attendance table
        $emp_id = (int)$_POST['who'];
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
            font-family: 'Roboto', Arial, sans-serif;
            background-color: #ede7f6;
        }

        .top-bar {
            background-color: #673ab7;
            height: 19px;
        }

        .form-container {
            max-width: 720px;
            margin: -11px auto 50px;
        }

        .form-card {
            background: #fff;
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
        }

        h1 {
            margin: 0;
            font-size: 30px;
            font-weight: 500;
        }

        .subtitle {
            color: #5f6368;
            margin-top: 6px;
            font-size: 15px;
        }

        .required-note {
            color: #e38f8f;
            font-size: 13px;
            margin-top: 10px;
        }

        label {
            font-size: 16px;
            font-weight: 500;
            display: block;
            margin-bottom: 15px;
        }

        .required {
            color: #f55050;
        }

        input,
        select,
        textarea {
            width: 20%;
            border: none;
            border-bottom: 1px solid #dadce0;
            padding: 8px 0;
            font-size: 15px;
            background: transparent;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-bottom: 2px solid #673ab7;
        }

        textarea {
            resize: none;
            height: 20px;
        }

        .error input,
        .error select,
        .error textarea {
            border-bottom: 2px solid #eeeeee !important;
        }

        .error-text {
            color: #eeeeee;
            font-size: 13px;
            margin-top: 8px;
        }

        .button-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-submit {
            background-color: #673ab7;
            color: white;
            padding: 10px 28px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-submit:hover {
            background-color: #5e35b1;
        }

        .clear-link {
            color: #673ab7;
            font-size: 14px;
            text-decoration: none;
        }

        .clear-link:hover {
            text-decoration: underline;
        }

        .success-message {
            color: green;
            font-size: 15px;
        }

        .select-wrapper {
            position: relative;
            width: 250px;
            /* same compact width like Google Form */
        }

        .select-wrapper select {
            width: 100%;
            padding: 10px 35px 10px 12px;
            font-size: 14px;
            border: 1px solid #dadce0;
            border-radius: 4px;
            background-color: #f8f9fa;
            appearance: none;
            cursor: pointer;
        }

        /* Custom arrow */
        .select-wrapper::after {
            content: "▾";
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            color: #5f6368;
            pointer-events: none;
        }

        /* Focus style */
        .select-wrapper select:focus {
            outline: none;
            border: 2px solid #673ab7;
            background-color: #fff;
        }

        /* Error style */
        .error .select-wrapper select {
            border: 2px solid #eeeeee;
            background-color: #fff;
        }

        .input-error {
            border-bottom: 2px solid #e38f8f !important;
            background-color: #fffbe6;
        }
    </style>
</head>

<body>

    <div class="top-bar"></div>

    <div class="form-container">

        <div class="form-card">
            <h1>8DOTS - Worksheet</h1>
            <div class="subtitle">Daily Activity</div>
            <div class="required-note">* Indicates required question</div>
        </div>

        <?php if (!empty($success)) : ?>
            <div class="form-card">
                <div class="success-message">
                    Form submitted successfully!
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" onsubmit="return validateWorksheetForm();">

            <!-- Who -->
            <div class="form-card <?php echo in_array('who', $errorFields) ? 'error' : ''; ?>">
                <label>Who are you? <span class="required">*</span></label>
                <div class="select-wrapper">
                    <select name="who" required>
                        <option value="">Choose Employee</option>
                        <?php foreach ($employees as $emp): ?>
                            <option value="<?php echo $emp['id']; ?>" <?php if (isset($_POST['who']) && $_POST['who'] == $emp['id']) echo 'selected'; ?>><?php echo htmlspecialchars($emp['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if (in_array('who', $errorFields)) : ?>
                    <div class="error-text">This is a required question</div>
                <?php endif; ?>
            </div>


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
                <textarea name="task" placeholder="Your answer" style="width: 100%;" required><?php echo isset($_POST['task']) ? htmlspecialchars($_POST['task']) : ''; ?></textarea>
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
        var who = document.querySelector('select[name="who"]');
        var date = document.querySelector('input[name="date"]');
        var start = document.querySelector('input[name="start_time"]');
        var end = document.querySelector('input[name="end_time"]');
        var task = document.querySelector('textarea[name="task"]');
        var valid = true;
        [who, date, start, end, task].forEach(function(field) {
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