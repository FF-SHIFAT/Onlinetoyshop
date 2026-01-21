<?php
session_start();
require_once '../../Models/dbConnect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT carts.*, products.name, products.price, products.image 
        FROM carts 
        JOIN products ON carts.product_id = products.id 
        WHERE carts.user_id = '$user_id'";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Cart</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .cart-container { width: 80%; margin: 30px auto; background: white; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1); border-radius: 8px; }
        .cart-table { width: 100%; border-collapse: collapse; }
        .cart-table th, .cart-table td { padding: 15px; border-bottom: 1px solid #ddd; text-align: center; vertical-align: middle; }
        .cart-table th { background: #333; color: white; }
        .btn-remove { color: red; text-decoration: none; font-weight: bold; border: 1px solid red; padding: 5px 10px; border-radius: 4px; transition: 0.3s; }
        .btn-remove:hover { background: red; color: white; }
        .btn-checkout { display: block; background: #e67e22; color: white; text-align: center; padding: 15px; text-decoration: none; margin-top: 20px; font-weight: bold; font-size: 18px; border-radius: 5px; }
        .btn-checkout:hover { background: #d35400; }
        .qty-input { width: 60px; text-align: center; padding: 5px; border: 1px solid #ccc; border-radius: 4px; font-weight: bold; }
    </style>
</head>
<body>

    <header>
        <div class="container nav-container">
            <h1><a href="../../index.php" style="color: #ff6f61; text-decoration: none;"> ToyShop</a></h1>
            <nav class="nav-links">
                <a href="../../index.php">Home</a>
                <a href="cart.php" style="color:#ff6f61;">Cart</a>
                <a href="my_orders.php">My Orders</a>
                <a href="profile.php" style="font-weight:bold; color:#ff6f61; text-decoration:none;"><?php echo $_SESSION['user_name']; ?></a>
                <a href="../../Controllers/authControl.php?logout=true" class="btn-logout">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container" style="margin-top: 30px;">
        <a href="../../index.php" class="btn-back">&larr; Continue Shopping</a>

        <div class="cart-container">
            <h2 style="margin-top: 0; padding-bottom: 15px; border-bottom: 1px solid #eee;">Your Shopping Cart</h2>
            
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grand_total = 0;
                    if (mysqli_num_rows($result) > 0):
                        while($row = mysqli_fetch_assoc($result)): 
                            $total = $row['price'] * $row['quantity'];
                            $grand_total += $total;
                    ?>
                    <tr>
                        <td><img src="../../Resources/Products/<?php echo $row['image']; ?>" width="60" style="border-radius: 5px;"></td>
                        <td style="font-weight: bold;"><?php echo $row['name']; ?></td>
                        <td>Tk. <?php echo $row['price']; ?></td>
                        <td>
                            <input type="number" class="qty-input" value="<?php echo $row['quantity']; ?>" min="1" onchange="updateQuantity(<?php echo $row['id']; ?>, this.value)">
                        </td>
                        <td style="font-weight: bold; color: #555;">Tk. <span id="item_total_<?php echo $row['id']; ?>"><?php echo $total; ?></span></td>
                        <td><a href="../../Controllers/cartControl.php?remove_id=<?php echo $row['id']; ?>" class="btn-remove" onclick="return confirm('Remove item?')">Remove</a></td>
                    </tr>
                    <?php endwhile; ?>
                    
                    <tr>
                        <td colspan="4" style="text-align: right; font-weight: bold; font-size: 18px;">Grand Total:</td>
                        <td colspan="2" style="font-weight: bold; color: #e67e22; font-size: 20px;">Tk. <span id="grand_total"><?php echo $grand_total; ?></span></td>
                    </tr>

                    <?php else: ?>
                        <tr><td colspan="6" style="padding: 30px;">Your cart is empty!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php if($grand_total > 0): ?>
                <a href="checkout.php" class="btn-checkout">Proceed to Checkout</a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function updateQuantity(cartId, newQty) {
            if(newQty < 1) { alert("Quantity cannot be less than 1"); return; }
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "../../Controllers/cartControl.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (this.readyState === 4 && this.status === 200) {
                    let response = JSON.parse(this.responseText);
                    if(response.status === "success") {
                        document.getElementById("item_total_" + cartId).innerText = response.item_total;
                        document.getElementById("grand_total").innerText = response.grand_total;
                    }
                }
            };
            xhr.send("action=update_qty_ajax&cart_id=" + cartId + "&quantity=" + newQty);
        }
    </script>

</body>
</html>