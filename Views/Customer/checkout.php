<?php
session_start();
require_once '../../Models/dbConnect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

$sql = "SELECT carts.*, products.name, products.price FROM carts JOIN products ON carts.product_id = products.id WHERE carts.user_id = '$user_id'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 0) { header("Location: index.php"); exit(); }

$u_res = mysqli_query($conn, "SELECT address_line FROM user_addresses WHERE user_id='$user_id' LIMIT 1");
$user_address = ($row = mysqli_fetch_assoc($u_res)) ? $row['address_line'] : "";

$pay_res = mysqli_query($conn, "SELECT * FROM payment_methods");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .checkout-container { display: flex; gap: 30px; width: 80%; margin: 30px auto; }
        .shipping-form { flex: 1.5; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 8px; color: #333; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; box-sizing: border-box; }
        
        .order-summary { flex: 1; background: #f9f9f9; padding: 30px; border-radius: 8px; border: 1px solid #eee; height: fit-content; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px; color: #555; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .total-row { display: flex; justify-content: space-between; font-weight: bold; font-size: 18px; margin-top: 20px; color: #333; border-top: 2px solid #ddd; padding-top: 15px; }
        
        .btn-place-order { width: 100%; padding: 15px; background: #2ecc71; color: white; border: none; font-size: 18px; font-weight: bold; cursor: pointer; border-radius: 5px; margin-top: 20px; transition: 0.3s; }
        .btn-place-order:hover { background: #27ae60; }

        .payment-info-box { display: none; background: #eafaf1; border: 1px solid #2ecc71; padding: 15px; border-radius: 5px; margin-top: 10px; }
    </style>
</head>
<body>

    <header>
        <div class="container nav-container">
            <h1><a href="../../index.php" style="color: #ff6f61; text-decoration: none;"> ToyShop</a></h1>
            <nav class="nav-links">
                <a href="../../index.php">Home</a>
                <a href="cart.php">Cart</a>
                <a href="my_orders.php">My Orders</a>
                <a href="profile.php" style="font-weight:bold; color:#ff6f61; text-decoration:none;"><?php echo $_SESSION['user_name']; ?></a>
                <a href="../../Controllers/authControl.php?logout=true" class="btn-logout">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container" style="margin-top: 30px;">
        <a href="cart.php" class="btn-back">&larr; Back to Cart</a>

        <div class="checkout-container">
            <div class="shipping-form">
                <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">Shipping Details</h3>
                
                <form action="../../Controllers/orderControl.php" method="POST">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" value="<?php echo $_SESSION['user_name']; ?>" readonly style="background:#f0f0f0;">
                    </div>

                    <div class="form-group">
                        <label>Delivery Address</label>
                        <textarea name="address" rows="3" required placeholder="Enter your full address here..."><?php echo $user_address; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" required placeholder="e.g. 017xxxxxxxx">
                    </div>

                    <div class="form-group">
                        <label>Payment Method</label>
                        <select name="payment_method" id="payment_method" onchange="togglePaymentInfo()">
                            <option value="Cash on Delivery" data-details="">Cash on Delivery</option>
                            <?php while($method = mysqli_fetch_assoc($pay_res)): ?>
                                <option value="<?php echo $method['method_name']; ?>" data-details="<?php echo $method['account_number']; ?>">
                                    <?php echo $method['method_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div id="payment_section" class="payment-info-box">
                        <p style="margin: 0 0 10px 0; font-weight: bold; color: #2ecc71;">
                            Please Send Money to <span id="method_name"></span>:
                            <br>
                            <span id="method_details" style="font-size: 18px; color: #333;"></span>
                        </p>
                        <label style="font-size: 13px; font-weight: bold;">Transaction ID (TrxID):</label>
                        <input type="text" name="transaction_id" id="trx_id" placeholder="e.g. 8N7A6D5...">
                    </div>

                    <input type="hidden" name="place_order" value="1">
            </div>

            <div class="order-summary">
                <h3 style="margin-bottom: 20px;">Your Order</h3>
                
                <?php 
                $grand_total = 0;
                mysqli_data_seek($result, 0); 
                while($row = mysqli_fetch_assoc($result)): 
                    $total = $row['price'] * $row['quantity'];
                    $grand_total += $total;
                ?>
                    <div class="summary-item">
                        <span><?php echo $row['name']; ?> (x<?php echo $row['quantity']; ?>)</span>
                        <span>Tk. <?php echo $total; ?></span>
                    </div>
                <?php endwhile; ?>

                <div class="total-row">
                    <span>Total Amount</span>
                    <span style="color: #e67e22;">Tk. <?php echo $grand_total; ?></span>
                </div>
                
                <input type="hidden" name="total_amount" value="<?php echo $grand_total; ?>">
                <button type="submit" class="btn-place-order">Place Order</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePaymentInfo() {
            var selectBox = document.getElementById("payment_method");
            var section = document.getElementById("payment_section");
            var methodName = document.getElementById("method_name");
            var methodDetails = document.getElementById("method_details");
            var trxInput = document.getElementById("trx_id");
            var selectedOption = selectBox.options[selectBox.selectedIndex];
            var value = selectedOption.value;
            var details = selectedOption.getAttribute("data-details");

            if (value !== "Cash on Delivery") {
                section.style.display = "block";
                methodName.innerText = value;
                methodDetails.innerText = details;
                trxInput.setAttribute("required", "true");
            } else {
                section.style.display = "none";
                trxInput.removeAttribute("required");
                trxInput.value = "";
            }
        }
    </script>

</body>
</html>