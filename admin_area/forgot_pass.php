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

// Determine current state based on session
$currentState = 'email'; // default
if (isset($_SESSION['reset_step'])) {
    $currentState = $_SESSION['reset_step'];
}

// 1. SEND OTP ACTION
if (isset($_POST['send_otp'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $query = mysqli_query($con, "SELECT * FROM emp_list WHERE email='$email'");
    
    if (mysqli_num_rows($query) > 0) {
        $otp = rand(100000, 999999);
        $expire = date("Y-m-d H:i:s", strtotime("+5 minutes"));
        
        // Update DB with OTP
        mysqli_query($con, "UPDATE emp_list SET otp='$otp', otp_expire='$expire' WHERE email='$email'");
        
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'madhavanpatel19@gmail.com'; // your gmail
            $mail->Password   = 'yawi nqpw wbhp icrx';       // gmail app password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('madhavanpatel19@gmail.com', '8DOTS Support');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset OTP - 8DOTS';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                    <h2 style='color: #2c3e50; text-align: center;'>Password Reset Request</h2>
                    <p>Hello,</p>
                    <p>We received a request to reset your password for your 8DOTS account. Use the OTP below to proceed:</p>
                    <div style='background: #f4f7f6; padding: 15px; text-align: center; border-radius: 5px; margin: 20px 0;'>
                        <span style='font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #3498db;'>$otp</span>
                    </div>
                    <p style='color: #e74c3c; font-weight: bold;'>This OTP will expire in 5 minutes.</p>
                    <p>If you did not request this, please ignore this email.</p>
                    <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #7f8c8d; text-align: center;'>&copy; " . date('Y') . " 8DOTS. All rights reserved.</p>
                </div>
            ";

            $mail->send();
            
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_step'] = 'otp';
            $currentState = 'otp';
            $success = "A 6-digit OTP has been sent to your registered email.";
        } catch (Exception $e) {
            $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $error = "The provided email address is not registered in our system.";
    }
}

// 2. VERIFY OTP & RESET PASSWORD ACTION
if (isset($_POST['reset_password'])) {
    $email = isset($_SESSION['reset_email']) ? $_SESSION['reset_email'] : '';
    $otp = mysqli_real_escape_string($con, $_POST['otp']);
    $newpass = $_POST['newpass'];
    $confpass = $_POST['confpass'];

    if ($email == '') {
        $error = "Session expired. Please start over.";
        unset($_SESSION['reset_step']);
        $currentState = 'email';
    } elseif ($newpass !== $confpass) {
        $error = "Passwords do not match. Please try again.";
        $currentState = 'otp';
    } else {
        $current_time = date("Y-m-d H:i:s");
        $query = mysqli_query($con, "SELECT * FROM emp_list WHERE email='$email' AND otp='$otp' AND otp_expire > '$current_time'");
        
        if (mysqli_num_rows($query) > 0) {
            // Note: Using plain text as requested by user
            mysqli_query($con, "UPDATE emp_list SET password='$newpass', otp='', otp_expire=NULL WHERE email='$email'");
            
            // Clean up session
            unset($_SESSION['reset_email']);
            unset($_SESSION['reset_step']);
            
            echo "<script>alert('Password Reset Successful! Please login with your new password.'); window.location.href='emp-login.php';</script>";
            exit();
        } else {
            $error = "Invalid or expired OTP. Please check and try again.";
            $currentState = 'otp';
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
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>
<body>
    <div class="login-wrap">
        <div class="container">
            <?php if ($currentState === 'email') { ?>
                <form class="form-login" method="POST">
                    <h2 class="form-login-heading">Forgot Password</h2>
                    
                    <?php if($error) echo "<div class='alert alert-danger'><i class='fa fa-exclamation-circle'></i> $error</div>"; ?>
                    <?php if($success) echo "<div class='alert alert-success'><i class='fa fa-check-circle'></i> $success</div>"; ?>

                    <input type="email" name="email" class="form-control" placeholder="Enter Registered Email" required>
                    <button type="submit" name="send_otp" class="btn-primary">Send OTP</button>
                    <div class="back-to-login">
                        <a href="emp-login.php"><i class="fa fa-arrow-left"></i> Back to Login</a>
                    </div>
                </form>
            <?php } else { ?>
                <form class="form-login" method="POST">
                    <h2 class="form-login-heading">Reset Password</h2>
                    
                    <?php if($error) echo "<div class='alert alert-danger'><i class='fa fa-exclamation-circle'></i> $error</div>"; ?>
                    <?php if($success) echo "<div class='alert alert-success'><i class='fa fa-check-circle'></i> $success</div>"; ?>

                    <div style="text-align: center; margin-bottom: 15px;">
                        <span class="otp-badge"><i class="fa fa-envelope-o"></i> OTP Sent to <?php echo htmlspecialchars($_SESSION['reset_email']); ?></span>
                    </div>
                    <input type="text" name="otp" class="form-control" placeholder="Enter OTP" required style="text-align: center; letter-spacing: 5px; font-weight: bold;">
                    <input type="password" name="newpass" class="form-control" placeholder="New Password" required>
                    <input type="password" name="confpass" class="form-control" placeholder="Confirm Password" required>
                    <button type="submit" name="reset_password" class="btn-primary">Reset Password</button>
                    <div style="text-align: center; margin-top: 15px;">
                        <a href="?clear=true" style="font-size: 13px; color: #718096; text-decoration: underline;">Didn't receive code? Try again</a>
                    </div>
                    <div class="back-to-login">
                        <a href="emp-login.php"><i class="fa fa-arrow-left"></i> Back to Login</a>
                    </div>
                </form>
            <?php } ?>
        </div>
    </div>
</body>
</html>