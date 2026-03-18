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
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Login | 8DOTS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-wrap">
        <div class="container">
            <form class="form-login" method="POST">
                <h2 class="form-login-heading">Employee Login</h2>
                
                <?php if ($error) echo "<div class='alert alert-danger'><i class='fa fa-exclamation-circle'></i> $error</div>"; ?>

                <input type="email" name="email" class="form-control" placeholder="Email Address" required autocomplete="email">
                <input type="password" name="password" class="form-control" placeholder="Password" required autocomplete="current-password">
                
                <button type="submit" name="login" class="btn-primary">
                    Log in
                </button>
                <div class="forgot-link">
                    <a href="forgot_pass.php">Forgot Password?</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>