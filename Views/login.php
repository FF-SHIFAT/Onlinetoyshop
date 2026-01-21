<?php
session_start();
require_once '../Models/dbConnect.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: Admin/dashboard.php");
    } else {
        header("Location: ../index.php");
    }
    exit();
}

$saved_email = "";
if (isset($_COOKIE['user_email'])) {
    $saved_email = $_COOKIE['user_email'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - <?php echo $GLOBALS['site_config']['app_name']; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .remember-me-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #555;
            cursor: pointer;
        }
        .remember-me-group input {
            width: auto;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h2>Login to <?php echo $GLOBALS['site_config']['app_name']; ?></h2>

        <?php if(isset($_SESSION['error_msg'])): ?>
            <div class="alert alert-error">
                <?php 
                    echo $_SESSION['error_msg']; 
                    unset($_SESSION['error_msg']);
                ?>
            </div>
        <?php if(isset($_SESSION['success'])): ?>
             <div class="alert alert-success" style="background:#d4edda; color:#155724; padding:10px; border-radius:5px; margin-bottom:15px;">
                <?php 
                    echo $_SESSION['success']; 
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        <?php endif; ?>

        <form action="../Controllers/authControl.php" method="POST">
            
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?php echo $saved_email; ?>" required placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Enter your password">
            </div>

            <label class="remember-me-group">
                <input type="checkbox" name="remember_me" <?php if(!empty($saved_email)) echo "checked"; ?>>
                Remember Me
            </label>

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