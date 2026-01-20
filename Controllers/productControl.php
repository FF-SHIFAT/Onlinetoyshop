<?php
session_start();
require_once '../Models/dbConnect.php';

if (isset($_POST['add_product_btn'])) {
    
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];
    $image_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));

    $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($image_ext, $allowed_ext)) {
        if ($image_size < 5000000) {
            
            $new_image_name = time() . "_" . $image;
            $upload_path = "../Resources/Products/" . $new_image_name;

            if (move_uploaded_file($image_tmp, $upload_path)) {
                
                $sql = "INSERT INTO products (name, description, price, stock, image, cat_id) 
                        VALUES ('$name', '$description', '$price', '$stock', '$new_image_name', '$category_id')";

                if (mysqli_query($conn, $sql)) {
                    $_SESSION['msg'] = "Product Added Successfully!";
                    $_SESSION['msg_type'] = "success";
                    header("Location: ../Views/Admin/view_products.php");
                } else {
                    $_SESSION['msg'] = "Database Error: " . mysqli_error($conn);
                    $_SESSION['msg_type'] = "error";
                    header("Location: ../Views/Admin/add_product.php");
                }

            } else {
                $_SESSION['msg'] = "Failed to upload image!";
                $_SESSION['msg_type'] = "error";
                header("Location: ../Views/Admin/add_product.php");
            }

        } else {
            $_SESSION['msg'] = "Image size too large (Max 5MB)!";
            $_SESSION['msg_type'] = "error";
            header("Location: ../Views/Admin/add_product.php");
        }
    } else {
        $_SESSION['msg'] = "Invalid file type! Only JPG, PNG allowed.";
        $_SESSION['msg_type'] = "error";
        header("Location: ../Views/Admin/add_product.php");
    }
}

if (isset($_POST['update_product_btn'])) {

    $id = $_POST['product_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    if ($_FILES['image']['name'] != "") {
        $image = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $new_image_name = time() . "_" . $image;
        $upload_path = "../Resources/Products/" . $new_image_name;
        
        move_uploaded_file($image_tmp, $upload_path);
        
        $sql = "UPDATE products SET name='$name', cat_id='$category_id', price='$price', stock='$stock', description='$description', image='$new_image_name' WHERE id='$id'";
    } else {
        $sql = "UPDATE products SET name='$name', cat_id='$category_id', price='$price', stock='$stock', description='$description' WHERE id='$id'";
    }

    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg'] = "Product Updated Successfully!";
        header("Location: ../Views/Admin/view_products.php");
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}

if (isset($_POST['update_product'])) {
    $id = $_POST['product_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $cat_id = $_POST['category_id'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $desc = mysqli_real_escape_string($conn, $_POST['description']);

    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $target = "../Resources/Products/" . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        
        $sql = "UPDATE products SET name='$name', cat_id='$cat_id', price='$price', stock='$stock', description='$desc', image='$image' WHERE id='$id'";
    } else {
        $sql = "UPDATE products SET name='$name', cat_id='$cat_id', price='$price', stock='$stock', description='$desc' WHERE id='$id'";
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: ../Views/Admin/view_products.php");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    exit();
}

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $sql = "DELETE FROM products WHERE id='$id'";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg'] = "Product Deleted Successfully!";
        header("Location: ../Views/Admin/view_products.php");
    }
}
?>