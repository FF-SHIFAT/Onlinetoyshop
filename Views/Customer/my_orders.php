<?php
session_start();
require_once '../../Models/dbConnect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$order_sql = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY id DESC";
$order_res = mysqli_query($conn, $order_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .order-container { max-width: 900px; margin: 30px auto; }
        
        .order-card { 
            background: white; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            margin-bottom: 25px; 
            overflow: hidden; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.05); 
        }
        
        .order-header { 
            background: #f8f9fa; 
            padding: 15px 20px; 
            border-bottom: 1px solid #eee; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        
        .order-header h3 { margin: 0; font-size: 18px; color: #333; }
        .order-meta { color: #666; font-size: 14px; margin-top: 5px; display: block; }
        
        .order-body { padding: 20px; }
        
        .status-badge { 
            padding: 5px 12px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: bold; 
            color: white; 
            display: inline-block; 
            text-transform: uppercase;
        }
        .pending { background: #f39c12; }
        .processing { background: #3498db; }
        .delivered { background: #2ecc71; }
        .cancelled { background: #e74c3c; }
        
        .item-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .item-table th, .item-table td { padding: 12px; text-align: left; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        .item-table th { color: #555; background: #fff; font-weight: 600; }
        .item-img { width: 50px; height: 50px; object-fit: contain; border: 1px solid #eee; border-radius: 4px; vertical-align: middle; }
        
        .grand-total { 
            text-align: right; 
            padding-top: 15px; 
            font-size: 18px; 
            font-weight: bold; 
            color: #333; 
            border-top: 2px solid #f0f0f0; 
            margin-top: 10px;
        }
        
        .info-section {
            background: #fafafa;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 13px;
            color: #555;
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
        }

        .empty-state {
            text-align: center;
            padding: 60px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border: 2px dashed #eee;
        }
        .empty-icon {
            font-size: 50px;
            color: #ddd;
            margin-bottom: 15px;
            display: block;
        }
    </style>
</head>
<body>

    <header>
        <div class="container nav-container">
            <h1><a href="../../index.php" style="color: #ff6f61; text-decoration: none;"> ToyShop</a></h1>
            <nav class="nav-links">
                <a href="../../index.php">Home</a>
                <a href="cart.php">Cart</a>
                <a href="my_orders.php" style="color:#ff6f61;">My Orders</a>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" style="font-weight:bold; color:#ff6f61; text-decoration:none;"><?php echo $_SESSION['user_name']; ?></a>
                    <a href="../../Controllers/authControl.php?logout=true" class="btn-logout">Logout</a>
                <?php else: ?>
                    <a href="../login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container order-container">
        <a href="../../index.php" class="btn-back">&larr; Back to Shop</a>
        
        <h2 style="margin-bottom: 25px; color: #333; border-left: 5px solid #ff6f61; padding-left: 10px;">My Order History</h2>

        <?php if(mysqli_num_rows($order_res) > 0): ?>
            <?php while($order = mysqli_fetch_assoc($order_res)): 
                $status_class = strtolower($order['order_status']);
                $o_id = $order['id'];
                
                $item_sql = "SELECT order_details.*, products.name, products.image 
                             FROM order_details 
                             JOIN products ON order_details.product_id = products.id 
                             WHERE order_details.order_id = '$o_id'";
                $item_res = mysqli_query($conn, $item_sql);
                
                $addr_id = $order['address_id'];
                $addr_res = mysqli_query($conn, "SELECT address_line FROM user_addresses WHERE id='$addr_id'");
                $addr_row = mysqli_fetch_assoc($addr_res);
                $address = $addr_row ? $addr_row['address_line'] : "Address not found";
            ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h3>Order ID: #<?php echo $order['id']; ?></h3>
                            <span class="order-meta">
                                 Date: <?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?>
                            </span>
                        </div>
                        <div>
                            <span class="status-badge <?php echo $status_class; ?>">
                                <?php echo $order['order_status']; ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="order-body">
                        <table class="item-table">
                            <thead>
                                <tr>
                                    <th width="10%">Image</th>
                                    <th>Product Name</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th style="text-align:right;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($item = mysqli_fetch_assoc($item_res)): 
                                    $subtotal = $item['price'] * $item['quantity'];
                                ?>
                                <tr>
                                    <td><img src="../../Resources/Products/<?php echo $item['image']; ?>" class="item-img" alt="Toy"></td>
                                    <td><?php echo $item['name']; ?></td>
                                    <td>Tk. <?php echo $item['price']; ?></td>
                                    <td>x<?php echo $item['quantity']; ?></td>
                                    <td style="text-align:right;">Tk. <?php echo $subtotal; ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        
                        <div class="grand-total">
                            Total Amount: <span style="color: #e67e22;">Tk. <?php echo $order['total_amount']; ?></span>
                        </div>
                        
                        <div class="info-section">
                            <div>
                                <strong> Shipping Address:</strong><br>
                                <?php echo $address; ?>
                            </div>
                            <div style="text-align: right;">
                                <strong> Payment Method:</strong> <?php echo $order['payment_method']; ?>
                                <?php if(!empty($order['transaction_id'])): ?>
                                    <br><strong>TrxID:</strong> <span style="font-family: monospace;"><?php echo $order['transaction_id']; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

        <?php else: ?>
            <div class="empty-state">
                <span class="empty-icon"></span>
                <h3 style="color: #666; margin-bottom: 10px;">No orders found!</h3>
                <p style="color: #999; margin-bottom: 20px;">You haven't placed any orders yet.</p>
                <a href="../../index.php" style="display: inline-block; padding: 12px 25px; background: #ff6f61; color: white; border-radius: 5px; text-decoration: none; font-weight: bold; transition:0.3s;">Start Shopping</a>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>