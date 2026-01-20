<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="css/style.css">
    <style>

        body { font-family: Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .login-container h2 { text-align: center; color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #666; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn-login { width: 100%; padding: 10px; background: #e67e22; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn-login:hover { background: #d35400; }
        .links { text-align: center; margin-top: 15px; font-size: 14px; }
        .links a { color: #1abc9c; text-decoration: none; }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Reset Password</h2>
    
    <?php if(isset($_SESSION['success'])): ?>
        <div style="color: green; text-align: center; margin-bottom: 15px; font-weight: bold; border: 1px solid green; padding: 10px; border-radius: 4px; background-color: #e8f8f5;">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>

        <script>
            setTimeout(function(){
                window.location.href = 'login.php';
            }, 3000);
        </script>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div style="color: red; text-align: center; margin-bottom: 15px;">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form action="../Controllers/authControl.php" method="POST">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="Enter your registered email" required>
        </div>
        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone" placeholder="Enter your registered phone" required>
        </div>
        <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_password" placeholder="Enter new password" required>
        </div>
        
        <button type="submit" name="reset_pass_btn" class="btn-login">Update Password</button>
    </form>

    <div class="links">
        <a href="login.php">Back to Login</a>
    </div>
</div>

</body>
</html>