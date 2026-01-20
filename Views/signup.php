<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - Online Toy Shop</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">
    <div class="form-container">
        <h2>Create an Account</h2>

        <?php if(isset($_SESSION['error_msg'])): ?>
            <div class="alert alert-error">
                <?php 
                    echo $_SESSION['error_msg']; 
                    unset($_SESSION['error_msg']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['success_msg'])): ?>
    <div class="alert alert-success" style="display: flex; justify-content: space-between; align-items: center;">
        <span><?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?></span>
        <a href="login.php" style="background: white; color: green; padding: 5px 10px; text-decoration: none; font-weight: bold; border-radius: 4px; font-size: 12px;">Login Now</a>
    </div>
<?php endif; ?>


        <form action="../Controllers/authControl.php" method="POST" onsubmit="return validateSignup()">
            
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" id="name" placeholder="Enter your name">
                <span id="err_name" class="error"></span>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" id="email" placeholder="Enter your email">
                <span id="err_email" class="error"></span>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" id="phone" placeholder="017xxxxxxxx">
                <span id="err_phone" class="error"></span>
            </div>

            <div class="form-group">
                <label>Address (Primary)</label>
                <textarea name="address" id="address" rows="2" placeholder="House, Road, Area, City"></textarea>
                <span id="err_address" class="error"></span>
                </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" id="password" placeholder="Enter password">
                <span id="err_password" class="error"></span>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="cpassword" id="cpassword" placeholder="Confirm password">
                <span id="err_cpassword" class="error"></span>
            </div>

            <button type="submit" name="signup_btn" class="btn">Sign Up</button>
        </form>

        <div class="form-footer">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</div>

<script src="js/script.js"></script>

</body>
</html>