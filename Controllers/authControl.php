<?php
session_start();
require_once '../Models/dbConnect.php'; 

if (isset($_POST['signup_btn'])) {
    $name = mysqli_real_escape_string($conn, htmlspecialchars($_POST['name']));
    $email = mysqli_real_escape_string($conn, htmlspecialchars($_POST['email']));
    $phone = mysqli_real_escape_string($conn, htmlspecialchars($_POST['phone']));
    $address = mysqli_real_escape_string($conn, htmlspecialchars($_POST['address']));
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    if (strlen($password) < 8 || !preg_match("/[0-9]/", $password) || !preg_match("/[!@#$%^&*]/", $password)) {
        $_SESSION['error_msg'] = "Password must be 8+ chars long and include at least one number and one special character (@#$%^&*)!";
        header("Location: ../Views/signup.php");
        exit();
    }

    if ($password !== $cpassword) {
        $_SESSION['error_msg'] = "Passwords do not match!";
        header("Location: ../Views/signup.php");
        exit();
    }

    $checkEmail = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $checkEmail);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['error_msg'] = "Email already registered!";
        header("Location: ../Views/signup.php");
        exit();
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql_user = "INSERT INTO users (name, email, password, phone, role) VALUES ('$name', '$email', '$hashed_password', '$phone', 'customer')";
        
        if (mysqli_query($conn, $sql_user)) {
            $user_id = mysqli_insert_id($conn);

            $sql_address = "INSERT INTO user_addresses (user_id, address_line, is_primary) VALUES ('$user_id', '$address', 1)";
            mysqli_query($conn, $sql_address);

            $_SESSION['success'] = "Registration Successful! Please Login.";
            header("Location: ../Views/login.php");
            exit();
        } else {
            $_SESSION['error_msg'] = "Database Error: " . mysqli_error($conn);
            header("Location: ../Views/signup.php");
            exit();
        }
    }
}

if (isset($_POST['login_btn'])) {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $_SESSION['error_msg'] = "Please fill in all fields";
        header("Location: ../Views/login.php");
        exit();
    }

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {
            
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['role'] = $row['role'];

            if (isset($_POST['remember_me'])) {
                setcookie("user_email", $email, time() + (86400 * 30), "/");
            } else {
                if(isset($_COOKIE['user_email'])){
                    setcookie("user_email", "", time() - 3600, "/");
                }
            }

            if ($row['role'] == 'admin') {
                header("Location: ../Views/Admin/dashboard.php");
            } else {
                header("Location: ../index.php");
            }
            exit();

        } else {
            $_SESSION['error_msg'] = "Incorrect Password!";
            header("Location: ../Views/login.php");
            exit();
        }
    } else {
        $_SESSION['error_msg'] = "Email not found!";
        header("Location: ../Views/login.php");
        exit();
    }
}

if (isset($_POST['reset_pass_btn'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $new_pass = mysqli_real_escape_string($conn, $_POST['new_password']);

    $check_sql = "SELECT * FROM users WHERE email='$email' AND phone='$phone'";
    $result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($result) == 1) {
        $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);

        $update_sql = "UPDATE users SET password='$hashed_password' WHERE email='$email'";
        
        if (mysqli_query($conn, $update_sql)) {
            $_SESSION['success'] = "Password updated successfully. Redirecting to Login page...";
            header("Location: ../Views/forgot_password.php"); 
        } else {
            $_SESSION['error'] = "Something went wrong! Please try again.";
            header("Location: ../Views/forgot_password.php");
        }
    } else {
        $_SESSION['error'] = "Email and Phone number do not match!";
        header("Location: ../Views/forgot_password.php");
    }
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['user_id']);
    unset($_SESSION['user_name']);
    unset($_SESSION['role']);
    header("Location: ../Views/login.php");
    exit();
}
?>