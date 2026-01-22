<?php
session_start();
require_once '../Models/dbConnect.php';

if (isset($_POST['place_order'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../Views/login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $payment = $_POST['payment_method'];
    
    $trx_id = isset($_POST['transaction_id']) && !empty($_POST['transaction_id']) ? mysqli_real_escape_string($conn, $_POST['transaction_id']) : NULL;
    
    $total = $_POST['total_amount'];
    $date = date('Y-m-d H:i:s');

    $addr_check = mysqli_query($conn, "SELECT id FROM user_addresses WHERE user_id='$user_id'");
    if(mysqli_num_rows($addr_check) > 0){
        mysqli_query($conn, "UPDATE user_addresses SET address_line='$address' WHERE user_id='$user_id'");
        $row = mysqli_fetch_assoc($addr_check);
        $address_id = $row['id'];
    } else {
        mysqli_query($conn, "INSERT INTO user_addresses (user_id, address_line, is_primary) VALUES ('$user_id', '$address', 1)");
        $address_id = mysqli_insert_id($conn);
    }

    $order_sql = "INSERT INTO orders (user_id, address_id, total_amount, payment_method, transaction_id, order_status, order_date) 
                  VALUES ('$user_id', '$address_id', '$total', '$payment', '$trx_id', 'Pending', '$date')";
    
    if (mysqli_query($conn, $order_sql)) {
        $order_id = mysqli_insert_id($conn);
        $cart_res = mysqli_query($conn, "SELECT * FROM carts WHERE user_id='$user_id'");
        while ($item = mysqli_fetch_assoc($cart_res)) {
            $p_id = $item['product_id'];
            $qty = $item['quantity'];
            
            $price_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT price FROM products WHERE id='$p_id'"));
            $price = $price_row['price'];

            mysqli_query($conn, "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES ('$order_id', '$p_id', '$qty', '$price')");
            mysqli_query($conn, "UPDATE products SET stock = stock - $qty WHERE id='$p_id'");
        }

        mysqli_query($conn, "DELETE FROM carts WHERE user_id='$user_id'");

        header("Location: ../Views/Customer/order_success.php?oid=" . $order_id);
        exit();
    }
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    if(!isset($_POST['place_order'])){
        echo "Unauthorized"; 
        exit();
    }
}

if (isset($_POST['action']) && $_POST['action'] == 'get_order_details') {
    $order_id = $_POST['order_id'];

    $order_sql = "SELECT orders.*, users.name, users.phone, user_addresses.address_line 
                  FROM orders 
                  JOIN users ON orders.user_id = users.id 
                  JOIN user_addresses ON orders.address_id = user_addresses.id 
                  WHERE orders.id = '$order_id'";
    $order_res = mysqli_query($conn, $order_sql);
    $order = mysqli_fetch_assoc($order_res);

    $items_sql = "SELECT order_details.*, products.name, products.image 
                  FROM order_details 
                  JOIN products ON order_details.product_id = products.id 
                  WHERE order_details.order_id = '$order_id'";
    $items_res = mysqli_query($conn, $items_sql);

    $output = "<p><strong>Customer:</strong> " . $order['name'] . " (" . $order['phone'] . ")</p>";
    $output .= "<p><strong>Address:</strong> " . $order['address_line'] . "</p>";
    
    $output .= "<p><strong>Payment Method:</strong> " . $order['payment_method'] . "</p>";
    if(!empty($order['transaction_id'])) {
        $output .= "<p style='color:red; font-size:16px;'><strong>Transaction ID:</strong> " . $order['transaction_id'] . "</p>";
    }

    $output .= "<p><strong>Date:</strong> " . $order['order_date'] . "</p>";
    $output .= "<hr><h4>Ordered Items:</h4>";
    $output .= "<table style='width:100%; border-collapse:collapse; margin-top:10px;'>";
    $output .= "<tr style='background:#f1f1f1;'><th style='padding:5px;'>Image</th><th>Product</th><th>Qty</th><th>Price</th></tr>";

    while($item = mysqli_fetch_assoc($items_res)){
        $output .= "<tr>";
        $output .= "<td style='padding:5px; text-align:center;'><img src='../../Resources/Products/" . $item['image'] . "' width='30'></td>";
        $output .= "<td style='padding:5px;'>" . $item['name'] . "</td>";
        $output .= "<td style='padding:5px; text-align:center;'>" . $item['quantity'] . "</td>";
        $output .= "<td style='padding:5px;'>Tk. " . $item['price'] . "</td>";
        $output .= "</tr>";
    }
    $output .= "</table>";
    $output .= "<h4 style='text-align:right; margin-top:10px;'>Total: Tk. " . $order['total_amount'] . "</h4>";

    echo $output;
    exit();
}

if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['order_status'];

    $sql = "UPDATE orders SET order_status = '$status' WHERE id = '$order_id'";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg'] = "Order Status Updated!";
    }
    header("Location: ../Views/Admin/manage_orders.php");
    exit();
}

if (isset($_POST['action']) && $_POST['action'] == 'fetch_orders') {
    $status = $_POST['status'];
    
    $sql = "SELECT orders.*, users.name as customer_name 
            FROM orders 
            JOIN users ON orders.user_id = users.id";

    if ($status == 'new') {
        $sql .= " WHERE order_status IN ('Pending', 'Processing')";
    } elseif ($status == 'delivered') {
        $sql .= " WHERE order_status = 'Delivered'";
    } elseif ($status == 'cancelled') {
        $sql .= " WHERE order_status = 'Cancelled'";
    }

    $sql .= " ORDER BY orders.id DESC";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $st_class = strtolower($row['order_status']); // CSS ক্লাসের জন্য
            
            echo "<tr>
                    <td>#{$row['id']}</td>
                    <td>{$row['customer_name']}</td>
                    <td>Tk. {$row['total_amount']}</td>
                    <td>{$row['payment_method']}</td>
                    <td><span class='badge {$st_class}'>{$row['order_status']}</span></td>
                    <td>" . date('d M, Y', strtotime($row['order_date'])) . "</td>
                    <td>
                        <button class='btn-view' onclick=\"viewOrder('{$row['id']}')\">Details</button>
                        <button class='btn-update' onclick=\"updateStatus('{$row['id']}', '{$row['order_status']}')\">Status</button>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='7' style='text-align:center; padding:20px; color:#888;'>No orders found in this category!</td></tr>";
    }
    exit();
}
?>