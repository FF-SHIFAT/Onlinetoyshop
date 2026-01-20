<?php
session_start();
require_once '../Models/dbConnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Views/login.php");
    exit();
}

if (isset($_POST['add_method'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $details = mysqli_real_escape_string($conn, $_POST['details']);

    $sql = "INSERT INTO payment_methods (method_name, account_number) VALUES ('$name', '$details')";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg'] = "Payment Method Added!";
    }
    header("Location: ../Views/Admin/manage_payments.php");
    exit();
}

if (isset($_POST['update_method'])) {
    $id = $_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $details = mysqli_real_escape_string($conn, $_POST['details']);

    $sql = "UPDATE payment_methods SET method_name='$name', account_number='$details' WHERE id='$id'";
    
    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg'] = "Payment Method Updated Successfully!";
    } else {
        $_SESSION['msg'] = "Update Failed!";
    }
    header("Location: ../Views/Admin/manage_payments.php");
    exit();
}

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    mysqli_query($conn, "DELETE FROM payment_methods WHERE id='$id'");
    $_SESSION['msg'] = "Method Deleted!";
    header("Location: ../Views/Admin/manage_payments.php");
    exit();
}
?>