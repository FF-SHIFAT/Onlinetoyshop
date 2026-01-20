<?php
session_start();
require_once '../Models/dbConnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized Access']);
    exit();
}

if (isset($_POST['action']) && $_POST['action'] == 'fetch_users') {
    $role = isset($_POST['role_filter']) ? $_POST['role_filter'] : 'customer';
    $search = isset($_POST['search_query']) ? mysqli_real_escape_string($conn, $_POST['search_query']) : "";

    $sql = "SELECT users.*, user_addresses.address_line 
            FROM users 
            LEFT JOIN user_addresses ON users.id = user_addresses.user_id AND user_addresses.is_primary = 1
            WHERE users.role = '$role' 
            AND (users.name LIKE '%$search%' OR users.email LIKE '%$search%' OR users.phone LIKE '%$search%')
            ORDER BY users.id DESC";

    $result = mysqli_query($conn, $sql);
    $output = "";

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $addr = $row['address_line'] ?? 'Not set';
            $output .= "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['phone']}</td>
                            <td>{$addr}</td>
                            <td>
                                <button class='btn-view' onclick='viewUser({$row['id']})'>View</button>
                                <button class='btn-edit' onclick='editUser({$row['id']})'>Edit</button>
                                <button class='btn-delete' onclick='deleteUser({$row['id']})'>Delete</button>
                            </td>
                        </tr>";
        }
    } else {
        $output .= "<tr><td colspan='6' style='text-align:center;'>No records found!</td></tr>";
    }
    echo $output;
    exit();
}

if (isset($_POST['action']) && $_POST['action'] == 'get_user_details') {
    $id = $_POST['user_id'];
    $sql = "SELECT users.*, user_addresses.address_line FROM users 
            LEFT JOIN user_addresses ON users.id = user_addresses.user_id AND user_addresses.is_primary = 1
            WHERE users.id = '$id'";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    echo json_encode($data);
    exit();
}

if (isset($_POST['add_user_btn'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if(mysqli_num_rows($check) > 0){
        $_SESSION['msg'] = "Email already exists!";
    } else {
        $sql = "INSERT INTO users (name, email, password, phone, role) VALUES ('$name', '$email', '$password', '$phone', '$role')";
        if(mysqli_query($conn, $sql)){
            $uid = mysqli_insert_id($conn);
            mysqli_query($conn, "INSERT INTO user_addresses (user_id, address_line, is_primary) VALUES ('$uid', '$address', 1)");
            $_SESSION['msg'] = "User Added Successfully!";
        } else {
            $_SESSION['msg'] = "Error adding user!";
        }
    }
    header("Location: ../Views/Admin/manage_users.php");
    exit();
}

if (isset($_POST['update_user_btn'])) {
    $id = $_POST['user_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $role = $_POST['role'];
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $pass_query = "";
    if(!empty($_POST['password'])){
        $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $pass_query = ", password='$hashed'";
    }

    $sql = "UPDATE users SET name='$name', email='$email', phone='$phone', role='$role' $pass_query WHERE id='$id'";
    
    if(mysqli_query($conn, $sql)){
        $chk_addr = mysqli_query($conn, "SELECT * FROM user_addresses WHERE user_id='$id' AND is_primary=1");
        if(mysqli_num_rows($chk_addr) > 0){
            mysqli_query($conn, "UPDATE user_addresses SET address_line='$address' WHERE user_id='$id' AND is_primary=1");
        } else {
            mysqli_query($conn, "INSERT INTO user_addresses (user_id, address_line, is_primary) VALUES ('$id', '$address', 1)");
        }
        $_SESSION['msg'] = "User Updated Successfully!";
    } else {
        $_SESSION['msg'] = "Error updating user!";
    }
    header("Location: ../Views/Admin/manage_users.php");
    exit();
}

if (isset($_POST['action']) && $_POST['action'] == 'delete_user') {
    $user_id = $_POST['user_id'];
    if(mysqli_query($conn, "DELETE FROM users WHERE id = '$user_id'")){
        echo "success";
    } else {
        echo "error";
    }
    exit();
}
?>