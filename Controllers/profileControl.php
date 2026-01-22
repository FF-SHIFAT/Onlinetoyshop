<?php
session_start();
require_once '../Models/dbConnect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Views/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    $image_query_part = "";
    if (!empty($_FILES['profile_image']['name'])) {
        $image_name = time() . '_' . $_FILES['profile_image']['name'];
        if (!file_exists('../Resources/Users')) {
            mkdir('../Resources/Users', 0777, true);
        }
        $target = "../Resources/Users/" . $image_name;
        
        if(move_uploaded_file($_FILES['profile_image']['tmp_name'], $target)){
            $image_query_part = ", profile_image='$image_name'";
        }
    }

    $sql = "UPDATE users SET name='$name', phone='$phone' $image_query_part WHERE id='$user_id'";
    mysqli_query($conn, $sql);
    
    $_SESSION['user_name'] = $name;

    $check_addr = mysqli_query($conn, "SELECT * FROM user_addresses WHERE user_id='$user_id'");
    if(mysqli_num_rows($check_addr) > 0){
        mysqli_query($conn, "UPDATE user_addresses SET address_line='$address' WHERE user_id='$user_id'");
    } else {
        if(!empty($address)){
            mysqli_query($conn, "INSERT INTO user_addresses (user_id, address_line, is_primary) VALUES ('$user_id', '$address', 1)");
        }
    }

    $_SESSION['msg'] = "Profile Updated Successfully!";
    $_SESSION['msg_type'] = "success";

    if($_SESSION['role'] == 'admin'){
        header("Location: ../Views/Admin/profile.php");
    } else {
        header("Location: ../Views/Customer/profile.php");
    }
    exit();
}

if (isset($_POST['change_password'])) {
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass !== $confirm_pass) {
        $_SESSION['msg'] = "New passwords do not match!";
        $_SESSION['msg_type'] = "error";
        
        if($_SESSION['role'] == 'admin'){
            header("Location: ../Views/Admin/profile.php");
        } else {
            header("Location: ../Views/Customer/profile.php");
        }
        exit();
    }

    $sql = "SELECT password FROM users WHERE id='$user_id'";
    $res = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($res);

    if (password_verify($current_pass, $row['password'])) {
        $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password='$new_hash' WHERE id='$user_id'");
        $_SESSION['msg'] = "Password Changed Successfully!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['msg'] = "Current password is wrong!";
        $_SESSION['msg_type'] = "error";
    }

    if($_SESSION['role'] == 'admin'){
        header("Location: ../Views/Admin/profile.php");
    } else {
        header("Location: ../Views/Customer/profile.php");
    }
    exit();
}
?>