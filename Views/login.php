<?php
session_start();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: Admin/dashboard.php");
    } else {
        header("Location: ../index.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Online Toy Shop</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">
    <div class="form-container">
        <h2>Login to Your Account</h2>

        <?php if(isset($_SESSION['error_msg'])): ?>
            <div class="alert alert-error">
                <?php 
                    echo $_SESSION['error_msg']; 
                    unset($_SESSION['error_msg']);
                ?>
            </div>
        <?php endif; ?>

        <form action="../Controllers/authControl.php" method="POST">
            
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Enter your password">
            </div>

            <button type="submit" name="login_btn" class="btn">Login</button>
        </form>

        <div style="text-align: center; margin-top: 20px;">
        <a href="forgot_password.php" style="color: #ec0808; font-size: 16px; text-decoration: none;">Forgot Password?</a>
        </div>

        <div class="form-footer">
            <p>Don't have an account? <a href="signup.php">Sign Up here</a></p>
        </div>
    </div>
</div>

</body>
</html>