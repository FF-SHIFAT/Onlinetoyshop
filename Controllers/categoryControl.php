<?php
session_start();
require_once '../Models/dbConnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Views/login.php");
    exit();
}

if (isset($_POST['add_category'])) {
    $cat_name = mysqli_real_escape_string($conn, $_POST['cat_name']);
    
    if(!empty($cat_name)){
        $check = mysqli_query($conn, "SELECT * FROM categories WHERE cat_name = '$cat_name'");
        if(mysqli_num_rows($check) > 0){
            $_SESSION['msg'] = "Category already exists!";
        } else {
            $sql = "INSERT INTO categories (cat_name) VALUES ('$cat_name')";
            if(mysqli_query($conn, $sql)){
                $_SESSION['msg'] = "Category Added Successfully!";
            } else {
                $_SESSION['msg'] = "Error adding category!";
            }
        }
    }
    header("Location: ../Views/Admin/manage_categories.php");
    exit();
}

if (isset($_POST['update_category'])) {
    $cat_id = $_POST['cat_id'];
    $cat_name = mysqli_real_escape_string($conn, $_POST['cat_name']);
    
    $sql = "UPDATE categories SET cat_name='$cat_name' WHERE id='$cat_id'";
    if(mysqli_query($conn, $sql)){
        $_SESSION['msg'] = "Category Updated Successfully!";
    } else {
        $_SESSION['msg'] = "Error updating category!";
    }
    header("Location: ../Views/Admin/manage_categories.php");
    exit();
}

if (isset($_GET['delete_id'])) {
    $cat_id = $_GET['delete_id'];
    $sql = "DELETE FROM categories WHERE id='$cat_id'";
    if(mysqli_query($conn, $sql)){
        $_SESSION['msg'] = "Category Deleted Successfully!";
    } else {
        $_SESSION['msg'] = "Error deleting category!";
    }
    header("Location: ../Views/Admin/manage_categories.php");
    exit();
}
?>