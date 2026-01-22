<?php
session_start();
require_once '../Models/dbConnect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Views/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['add_to_cart']) || isset($_POST['buy_now'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $check_sql = "SELECT * FROM carts WHERE user_id='$user_id' AND product_id='$product_id'";
    $check_res = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_res) > 0) {
        $update_sql = "UPDATE carts SET quantity = quantity + $quantity WHERE user_id='$user_id' AND product_id='$product_id'";
        mysqli_query($conn, $update_sql);
    } else {
        $insert_sql = "INSERT INTO carts (user_id, product_id, quantity) VALUES ('$user_id', '$product_id', '$quantity')";
        mysqli_query($conn, $insert_sql);
    }

    if (isset($_POST['buy_now'])) {
        header("Location: ../Views/Customer/checkout.php"); 
    } else {
        header("Location: ../Views/Customer/cart.php"); 
    }
    exit();
}

if (isset($_GET['remove_id'])) {
    $cart_id = $_GET['remove_id'];
    $sql = "DELETE FROM carts WHERE id='$cart_id'";
    mysqli_query($conn, $sql);
    header("Location: ../Views/Customer/cart.php");
    exit();
}

if (isset($_POST['action']) && $_POST['action'] == 'update_qty_ajax') {
    $cart_id = $_POST['cart_id'];
    $qty = $_POST['quantity'];
    
    if($qty > 0){
        $sql = "UPDATE carts SET quantity='$qty' WHERE id='$cart_id'";
        mysqli_query($conn, $sql);

        $p_sql = "SELECT price FROM products 
                  JOIN carts ON products.id = carts.product_id 
                  WHERE carts.id = '$cart_id'";
        $p_res = mysqli_query($conn, $p_sql);
        $p_row = mysqli_fetch_assoc($p_res);
        
        $item_total = $p_row['price'] * $qty;

        $g_sql = "SELECT SUM(products.price * carts.quantity) as grand_total 
                  FROM carts 
                  JOIN products ON carts.product_id = products.id 
                  WHERE carts.user_id = '$user_id'";
        $g_res = mysqli_query($conn, $g_sql);
        $g_row = mysqli_fetch_assoc($g_res);
        
        echo json_encode([
            "status" => "success",
            "item_total" => $item_total,
            "grand_total" => $g_row['grand_total']
        ]);
    }
    exit();
}
?>