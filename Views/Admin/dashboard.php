<?php
session_start();
require_once '../../Models/dbConnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$total_users = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE role='customer'"));
$total_products = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM products"));
$total_orders = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM orders"));

$product_sql = "SELECT * FROM products ORDER BY id DESC";
$product_res = mysqli_query($conn, $product_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        body { background-color: #f4f6f9; margin: 0; font-family: sans-serif; }
        .admin-header { background: #343a40; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .admin-container { display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #2c3e50; color: white; min-height: 100vh; }
        .sidebar a { display: block; color: #b8c7ce; padding: 15px 20px; text-decoration: none; border-bottom: 1px solid #3d566e; }
        .sidebar a:hover, .sidebar a.active { background: #1abc9c; color: white; border-left: 5px solid #16a085; }
        .main-content { flex: 1; padding: 30px; }
        
        .dashboard-cards { display: flex; gap: 20px; margin-bottom: 40px; }
        .card { flex: 1; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: center; }
        .card h3 { font-size: 30px; color: #ff6f61; margin: 10px 0; }
        .card p { color: #555; font-weight: bold; }
        
        .btn-add-new { display: inline-block; background: #3498db; color: white; padding: 12px 25px; font-size: 16px; font-weight: bold; text-decoration: none; border-radius: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: 0.3s; }
        .btn-add-new:hover { background: #2980b9; transform: translateY(-2px); }

        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; margin-top: 20px; }
        .product-card { background: white; border: 1px solid #eee; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-align: center; transition: 0.3s; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .product-img { width: 100%; height: 180px; object-fit: contain; padding: 15px; background: #f9f9f9; border-bottom: 1px solid #eee; }
        .p-info { padding: 15px; }
        .p-info h4 { margin: 10px 0; color: #333; font-size: 16px; }
        .price { color: #ff6f61; font-weight: bold; font-size: 18px; display: block; margin-bottom: 10px; }
        .stock-badge { font-size: 12px; padding: 3px 8px; border-radius: 10px; color: white; background: #2ecc71; display: inline-block; margin-bottom: 10px; }
        .out-stock { background: #e74c3c; }
        .admin-actions { display: flex; justify-content: center; gap: 10px; margin-top: 10px; }
        .btn-sm { padding: 5px 10px; font-size: 12px; text-decoration: none; color: white; border-radius: 3px; }
        .btn-edit { background: #f39c12; }
        .btn-del { background: #e74c3c; }
    </style>
</head>
<body>

    <div class="admin-header" style="background: #343a40; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center;">
    <h2>Admin Panel</h2>
    <div style="display: flex; align-items: center; gap: 20px;">
        <a href="profile.php" style="color: white; font-weight: bold; text-decoration: none;">
            Welcome, <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin'; ?>
        </a>
        <a href="../../Controllers/authControl.php?logout=true" style="background: red; padding: 8px 15px; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; transition: 0.3s;">Logout</a>
    </div>
</div>

    <div class="admin-container">
        <div class="sidebar">
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="manage_categories.php">Manage Categories</a>
            <a href="add_product.php">Add New Product</a>
            <a href="view_products.php">Manage Products</a>
            <a href="manage_orders.php">Orders</a>
            <a href="manage_payments.php">Payment Gateway</a>
            <a href="manage_users.php">Registered Users</a>
        </div>

        <div class="main-content">
            <h2>Dashboard Overview</h2>

            <div class="dashboard-cards">
                <div class="card">
                    <h3><?php echo $total_users; ?></h3>
                    <p>Total Customers</p>
                </div>
                <div class="card">
                    <h3><?php echo $total_products; ?></h3>
                    <p>Total Products</p>
                </div>
                <div class="card">
                    <h3><?php echo $total_orders; ?></h3>
                    <p>Total Orders</p>
                </div>
            </div>

            <div style="margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 20px;">
                <h3 style="color: #333; margin-bottom: 15px;">Quick Actions</h3>
                <a href="add_product.php" class="btn-add-new">+ Add a New Toy Product</a>
            </div>

            <div>
                <h3 style="color: #333;">Current Product Inventory</h3>
                <div class="product-grid">
                    <?php 
                    if(mysqli_num_rows($product_res) > 0):
                        while($row = mysqli_fetch_assoc($product_res)): 
                    ?>
                        <div class="product-card">
                            <img src="../../Resources/Products/<?php echo $row['image']; ?>" class="product-img" alt="Toy">
                            <div class="p-info">
                                <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                                <span class="price">Tk. <?php echo $row['price']; ?></span>
                                
                                <?php if($row['stock'] > 0): ?>
                                    <span class="stock-badge">Stock: <?php echo $row['stock']; ?></span>
                                <?php else: ?>
                                    <span class="stock-badge out-stock">Out of Stock</span>
                                <?php endif; ?>

                                <div class="admin-actions">
                                    <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn-sm btn-edit">Edit</a>
                                    <a href="../../Controllers/productControl.php?delete_id=<?php echo $row['id']; ?>" class="btn-sm btn-del" onclick="return confirm('Delete this product?')">Delete</a>
                                </div>
                            </div>
                        </div>
                    <?php 
                        endwhile; 
                    else: 
                    ?>
                        <p style="color: #888;">No products found.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

</body>
</html>