<?php
session_start();
require_once 'Models/dbConnect.php';

$search_query = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $search_query = "WHERE name LIKE '%$search%'";
}

$sql = "SELECT * FROM products $search_query ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Toy Shop</title>
    <link rel="stylesheet" href="Views/css/style.css">
    <style>
        .hero { background: #343a40; color: white; padding: 40px 0; text-align: center; margin-bottom: 30px; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 25px; }
        .product-card { background: white; border: 1px solid #eee; border-radius: 10px; overflow: hidden; transition: 0.3s; box-shadow: 0 5px 15px rgba(0,0,0,0.05); display: flex; flex-direction: column; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
        .card-link { text-decoration: none; color: inherit; flex-grow: 1; display: flex; flex-direction: column; }
        .product-img { width: 100%; height: 220px; object-fit: contain; padding: 20px; background: #fdfdfd; border-bottom: 1px solid #f0f0f0; }
        .p-info { padding: 15px; text-align: center; flex-grow: 1; }
        .price { color: #ff6f61; font-weight: bold; font-size: 20px; margin: 5px 0; }
        .btn-group { display: flex; padding: 15px; gap: 10px; background: #fff; border-top: 1px solid #f0f0f0; }
        .btn-group form { flex: 1; display: flex; }
        .btn-action { width: 100%; padding: 12px 0; border: none; border-radius: 6px; color: white; cursor: pointer; font-weight: 600; font-size: 16px; transition: 0.3s; }
        .btn-cart { background: #333; }
        .btn-buy { background: #e67e22; }
    </style>
</head>
<body>

    <header>
        <div class="container nav-container">
            <h1><a href="index.php" style="color: #ff6f61; text-decoration: none;"> ToyShop</a></h1>
            
            <form class="search-form" action="index.php" method="GET" style="display:flex;">
                <input type="text" name="search" placeholder="Search toys..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" style="padding:8px; border:1px solid #ddd; border-radius:4px 0 0 4px;">
                <button type="submit" style="padding:8px 15px; background:#ff6f61; color:white; border:none; border-radius:0 4px 4px 0; cursor:pointer;">Search</button>
            </form>

            <nav class="nav-links">
                <a href="index.php">Home</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php if($_SESSION['role'] == 'admin'): ?>
                        <a href="Views/Admin/dashboard.php">Dashboard</a>
                    <?php else: ?>
                        <a href="Views/Customer/cart.php">Cart</a>
                        <a href="Views/Customer/my_orders.php">My Orders</a>
                        <a href="Views/Customer/profile.php" style="font-weight:bold; color:#ff6f61; text-decoration:none;"><?php echo $_SESSION['user_name']; ?></a>
                    <?php endif; ?>
                    <a href="Controllers/authControl.php?logout=true" class="btn-logout">Logout</a>
                <?php else: ?>
                    <a href="Views/login.php">Login</a>
                    <a href="Views/signup.php">Sign Up</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="hero">
        <h2>Find the Best Toys for Your Kids!</h2>
        <p>Exclusive collection available now.</p>
    </div>

    <div class="container">
        <h2 style="margin-bottom: 20px; border-bottom: 2px solid #ff6f61; display: inline-block;">Latest Collection</h2>
        
        <div class="product-grid">
            <?php 
            if(mysqli_num_rows($result) > 0):
                while($row = mysqli_fetch_assoc($result)): 
            ?>
                <div class="product-card">
                    <a href="Views/Customer/product_details.php?id=<?php echo $row['id']; ?>" class="card-link">
                        <img src="Resources/Products/<?php echo $row['image']; ?>" class="product-img" alt="Toy">
                        <div class="p-info">
                            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                            <p class="price">Tk. <?php echo $row['price']; ?></p>
                            <?php if($row['stock'] > 0): ?>
                                <span style="color: green; font-size: 13px; font-weight:bold;">In Stock</span>
                            <?php else: ?>
                                <span style="color: red; font-size: 13px; font-weight:bold;">Out of Stock</span>
                            <?php endif; ?>
                        </div>
                    </a>

                    <?php if($row['stock'] > 0): ?>
                    <div class="btn-group">
                        <form action="Controllers/cartControl.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" name="add_to_cart" class="btn-action btn-cart">Cart</button>
                        </form>
                        
                        <form action="Controllers/cartControl.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" name="buy_now" class="btn-action btn-buy">Buy</button>
                        </form>
                    </div>
                    <?php else: ?>
                        <div style="padding:15px; text-align:center; background:#f9f9f9; color:#999; font-weight:bold;">Sold Out</div>
                    <?php endif; ?>
                </div>
            <?php 
                endwhile; 
            else: 
            ?>
                <p>No products found!</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>