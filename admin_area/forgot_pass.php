<?php
session_start();
include 'connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$error = '';
$success = '';
$showResetForm = false;

// SEND OTP
if (isset($_POST['send_otp'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $query = mysqli_query($con, "SELECT * FROM emp_list WHERE email='$email'");
    if (mysqli_num_rows($query) > 0) {
        $otp = rand(100000, 999999);
        $expire = date("Y-m-d H:i:s", strtotime("+5 minutes"));
        mysqli_query($con, "UPDATE emp_list SET otp='$otp', otp_expire='$expire' WHERE email='$email'");
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'madhavanpatel19@gmail.com'; // your gmail
            $mail->Password = 'yawi nqpw wbhp icrx';   // gmail app password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->setFrom('madhavanpatel19@gmail.com', '8DOTS');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset OTP - 8DOTS';
            $mail->Body = "<h3>Your OTP is: <b>$otp</b></h3><p>This OTP will expire in 5 minutes.</p>";
            $mail->send();
            $_SESSION['reset_email'] = $email;
            $success = "OTP sent successfully to your email.";
            $showResetForm = true;
        } catch (Exception $e) {
            $error = "Mailer Error: " . $mail->ErrorInfo;
        }
    } else {
        $error = "Email not found.";
    }
}

// VERIFY OTP & RESET PASSWORD
if (isset($_POST['reset_password'])) {
    $email = isset($_SESSION['reset_email']) ? $_SESSION['reset_email'] : '';
    $otp = mysqli_real_escape_string($con, $_POST['otp']);
    $newpass = $_POST['newpass'];
    $confpass = $_POST['confpass'];
    if ($newpass !== $confpass) {
        $error = "Passwords do not match.";
        $showResetForm = true;
    } else if ($email == '') {
        $error = "Session expired. Please request OTP again.";
    } else {
        $current_time = date("Y-m-d H:i:s");
        $query = mysqli_query($con, "SELECT * FROM emp_list 
                                     WHERE email='$email' 
                                     AND otp='$otp' 
                                     AND otp_expire > '$current_time'");
        if (mysqli_num_rows($query) > 0) {
            mysqli_query($con, "UPDATE emp_list 
                                SET password='$newpass', 
                                    otp='', 
                                    otp_expire=NULL 
                                WHERE email='$email'");
            unset($_SESSION['reset_email']);
            header('Location: emp-login.php');
            exit();
        } else {
            $error = "Invalid or expired OTP.";
            $showResetForm = true;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password - 8DOTS</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/login.css">

</head>
<body>
    <div class="form-login">
        <h2 class="form-login-heading">Forgot Password</h2>
        <?php if($error) echo "<div class='error' style='color:red;text-align:center;margin-bottom:10px;'>$error</div>"; ?>
        <?php if($success) echo "<div class='success' style='color:green;text-align:center;margin-bottom:10px;'>$success</div>"; ?>
        <?php if(!$showResetForm && !isset($_SESSION['reset_email'])) { ?>
            <form method="POST">
                <input type="email" name="email" class="form-control" placeholder="Enter Registered Email" required>
                <button type="submit" name="send_otp" class="btn btn-primary" style="width:100%;margin-top:10px;">Send OTP</button>
            </form>
        <?php } else { ?>
            <form method="POST">
                <input type="text" name="otp" class="form-control" placeholder="Enter OTP" required>
                <input type="password" name="newpass" class="form-control" placeholder="New Password" required>
                <input type="password" name="confpass" class="form-control" placeholder="Re-enter Password" required>
                <button type="submit" name="reset_password" class="btn btn-primary" style="width:100%;margin-top:10px;">Reset Password</button>
            </form>
        <?php } ?>
    </div>
</body>
</html>