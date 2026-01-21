<?php
session_start();
if (!isset($_GET['oid'])) {
    header("Location: ../../index.php");
    exit();
}
$order_id = $_GET['oid'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Success</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .success-box {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 50%;
            margin: 100px auto;
            border: 1px solid #ddd;
        }
        .btn-home {
            background: #333; 
            color: white; 
            padding: 10px 20px; 
            text-decoration: none; 
            border-radius: 4px; 
            margin-top: 20px; 
            display: inline-block;
            font-weight: bold;
        }
        .btn-home:hover { background: #555; }
    </style>
</head>
<body style="background: #f4f6f9;">

    <div class="success-box">
        <h2 style="color: green; margin-bottom: 15px; border-bottom: 2px solid green; display: inline-block; padding-bottom: 5px;">
            Order Placed Successfully!
        </h2>
        
        <p style="color: #555; margin-top: 10px;">Thank you for shopping with us.</p>
        
        <div style="background: #f9f9f9; padding: 15px; margin: 20px 0; border: 1px solid #eee;">
            <p style="margin: 0; font-size: 18px; color: #333;">
                Your Order ID is: <strong>#<?php echo $order_id; ?></strong>
            </p>
        </div>
        
        <a href="../../index.php" class="btn-home">Continue Shopping</a>
    </div>

</body>
</html>