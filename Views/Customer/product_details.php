<?php
session_start();
require_once '../../Models/dbConnect.php';

if (!isset($_GET['id'])) {
    header("Location: ../../index.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$sql = "SELECT products.*, categories.cat_name 
        FROM products 
        LEFT JOIN categories ON products.cat_id = categories.id 
        WHERE products.id = '$id'";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo "Product not found!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $product['name']; ?> - Details</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .detail-container { display: flex; gap: 40px; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); }
        .left-col { flex: 1; text-align: center; }
        .left-col img { width: 100%; max-height: 400px; object-fit: contain; border-radius: 10px; border: 1px solid #f0f0f0; padding: 20px; }
        .right-col { flex: 1.2; }
        .cat-badge { background: #f1f1f1; color: #555; padding: 5px 12px; border-radius: 20px; font-size: 13px; font-weight: bold; }
        .product-title { font-size: 32px; margin: 15px 0 10px; color: #222; }
        .price-tag { font-size: 30px; color: #ff6f61; font-weight: bold; margin: 15px 0; }
        .desc { line-height: 1.8; color: #666; margin-bottom: 25px; white-space: pre-wrap; font-size: 16px; }
        
        .qty-input { padding: 10px; width: 70px; text-align: center; border: 1px solid #ddd; border-radius: 5px; font-size: 18px; font-weight: bold; margin-left: 10px;}
        
        .action-buttons { display: flex; gap: 15px; margin-top: 30px; }
        .btn-cart, .btn-buy { 
            flex: 1; 
            padding: 15px 0; 
            border: none; 
            border-radius: 6px; 
            font-size: 18px; 
            font-weight: bold; 
            cursor: pointer; 
            transition: 0.3s; 
            color: white; 
            text-align: center;
            text-decoration: none;
        }
        .btn-cart { background: #333; }
        .btn-cart:hover { background: #555; }
        .btn-buy { background: #e67e22; }
        .btn-buy:hover { background: #d35400; }
        
        .stock-status { font-weight: bold; margin-bottom: 15px; display: block; font-size: 16px; }
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
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" style="font-weight:bold; color:#ff6f61; text-decoration:none;"><?php echo $_SESSION['user_name']; ?></a>
                    <a href="../../Controllers/authControl.php?logout=true" class="btn-logout">Logout</a>
                <?php else: ?>
                    <a href="../login.php" style="font-weight:bold;">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container" style="margin-top: 30px;">
        <a href="../../index.php" class="btn-back">&larr; Back to Shop</a>

        <div class="detail-container">
            <div class="left-col">
                <img src="../../Resources/Products/<?php echo $product['image']; ?>" alt="Product Image">
            </div>

            <div class="right-col">
                <span class="cat-badge"><?php echo $product['cat_name']; ?></span>
                <h1 class="product-title"><?php echo $product['name']; ?></h1>
                <p class="price-tag">Tk. <?php echo $product['price']; ?></p>
                
                <?php if($product['stock'] > 0): ?>
                    <span class="stock-status" style="color: green;">In Stock: <?php echo $product['stock']; ?> units</span>
                    
                    <form action="../../Controllers/cartControl.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        
                        <div style="margin-bottom: 20px;">
                            <label style="font-weight: bold; font-size: 16px;">Quantity:</label>
                            <input type="number" name="quantity" class="qty-input" value="1" min="1" max="<?php echo $product['stock']; ?>">
                        </div>
                        
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <div class="action-buttons">
                                <button type="submit" name="add_to_cart" class="btn-cart">Add to Cart</button>
                                <button type="submit" name="buy_now" class="btn-buy">Buy Now</button>
                            </div>
                        <?php else: ?>
                            <div class="action-buttons">
                                <a href="../login.php" class="btn-cart" style="background:#555;">Login to Buy</a>
                            </div>
                        <?php endif; ?>
                    </form>

                <?php else: ?>
                    <span class="stock-status" style="color: red; font-size: 20px;">Out of Stock</span>
                <?php endif; ?>

                <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">
                <h3 style="margin-bottom: 10px;">Description</h3>
                <p class="desc"><?php echo $product['description']; ?></p>
            </div>
        </div>
    </div>

</body>
</html>