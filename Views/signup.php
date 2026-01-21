<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - Online Toy Shop</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        #pass_msg {
            font-weight: bold;
            display: block;
            margin-top: 5px;
            font-size: 12px;
        }
    </style>
    <script>
        function checkPassword() {
            let password = document.getElementById('password').value;
            let msg = document.getElementById('pass_msg');
            let btn = document.getElementById('submit_btn');

            let strongRegex = /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,}$/;
            if (password === "") {
                msg.innerHTML = "";
                btn.disabled = false;
            } else if (strongRegex.test(password)) {
                msg.style.color = "green";
                msg.innerHTML = "Strong Password!";
                btn.disabled = false; 
            } else {
                msg.style.color = "red";
                msg.innerHTML = "Weak: Need 8+ chars, 1 number & 1 special char (@#$%)"; 
                btn.disabled = true; 
            }
        }

        function togglePassword() {
            let pass = document.getElementById("password");
            let cpass = document.getElementById("cpassword");
            
            if (pass.type === "password") {
                pass.type = "text";
                cpass.type = "text";
            } else {
                pass.type = "password";
                cpass.type = "password";
            }
        }
    </script>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h2>Create an Account</h2>

        <?php if(isset($_SESSION['error_msg'])): ?>
            <div class="alert alert-error">
                <?php echo $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
            </div>
        <?php endif; ?>

        <form action="../Controllers/authControl.php" method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" required>
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" required></textarea>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" id="password" name="password" required onkeyup="checkPassword()">
                <small id="pass_msg"></small>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" id="cpassword" name="cpassword" required>
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 8px; margin-top: -10px; margin-bottom: 15px;">
                <input type="checkbox" onclick="togglePassword()" style="width: auto; cursor: pointer;"> 
                <span style="font-size: 14px; color: #555;">Show Password</span>
            </div>

            <button type="submit" name="signup_btn" id="submit_btn" class="btn">Sign Up</button>
        </form>

        <div class="form-footer">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</div>

</body>
</html>