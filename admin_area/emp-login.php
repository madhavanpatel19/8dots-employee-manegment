<?php
include 'connection.php';

$error = "";

if (isset($_POST['login'])) {

    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($con, "SELECT * FROM emp_list WHERE email='$email' AND password='$password'");

    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['emp_id'] = $user['id'];
        $_SESSION['emp_name'] = $user['name'];
        header("Location: emp_index.php?dashboard");
        exit();
    } else {
        $error = "Invalid Email or Password!";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login - 8DOTS</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/login.css">
</head>

<body>

    <div class="form-login">
        <h2 class="form-login-heading">8DOTS Login</h2>
        <?php if ($error) echo "<div class='error' style='color:red;text-align:center;margin-bottom:10px;'>$error</div>"; ?>
        <form method="POST">
            <input type="email" name="email" class="form-control" placeholder="Enter Email" required>
            <input type="password" name="password" class="form-control" placeholder="Enter Password" required>
            <button type="submit" name="login" class="btn btn-primary" style="width:100%;margin-top:10px;">Login</button>
        </form>
        <div style="margin-top:15px;text-align:right;">
            <a href="forgot_pass.php" style="color:#337ab7;text-decoration:underline;font-size:14px;">Forgot Password?</a>
        </div>
    </div>

</body>

</html>