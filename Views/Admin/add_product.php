<?php
session_start();
require_once '../../Models/dbConnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$cat_result = mysqli_query($conn, "SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Product</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        body { background-color: #f4f6f9; margin: 0; }
        .admin-header { background: #343a40; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .admin-container { display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #2c3e50; color: white; min-height: 100vh; }
        .sidebar a { display: block; color: #b8c7ce; padding: 15px 20px; text-decoration: none; border-bottom: 1px solid #3d566e; }
        .sidebar a:hover, .sidebar a.active { background: #1abc9c; color: white; border-left: 5px solid #16a085; }
        .main-content { flex: 1; padding: 30px; }
        
        .form-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); max-width: 800px; margin: 0 auto; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 8px; color: #333; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; box-sizing: border-box; }
        .btn-submit { background: #2ecc71; color: white; padding: 12px 20px; border: none; cursor: pointer; border-radius: 5px; font-size: 16px; font-weight: bold; width: 100%; }
        .btn-submit:hover { background: #27ae60; }
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
            <a href="dashboard.php">Dashboard</a>
            <a href="manage_categories.php">Manage Categories</a>
            <a href="add_product.php" class="active">Add New Product</a>
            <a href="view_products.php">Manage Products</a>
            <a href="manage_orders.php">Orders</a>
            <a href="manage_payments.php">Payment Gateway</a>
            <a href="manage_users.php">Registered Users</a>
        </div>

        <div class="main-content">
            <div class="form-container">
                <h2 style="margin-top:0; color:#333; border-bottom:2px solid #eee; padding-bottom:10px;">Add New Toy Product</h2>

                <form action="../../Controllers/productControl.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" name="name" placeholder="Ex: Remote Control Car" required>
                    </div>

                    <div style="display: flex; gap: 20px;">
                        <div class="form-group" style="flex: 1;">
                            <label>Category</label>
                            <select name="category_id" required>
                                <option value="">Select Category</option>
                                <?php while($row = mysqli_fetch_assoc($cat_result)): ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['cat_name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group" style="flex: 1;">
                            <label>Stock Quantity</label>
                            <input type="number" name="stock" placeholder="Ex: 50" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Price (Tk)</label>
                        <input type="number" name="price" placeholder="Ex: 1200" required>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="5" placeholder="Enter detailed description..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Product Image</label>
                        <input type="file" name="image" required>
                    </div>

                    <button type="submit" name="add_product" class="btn-submit">Add Product</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>