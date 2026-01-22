<?php
session_start();
require_once '../../Models/dbConnect.php';

if (!isset($_GET['id'])) {
    header("Location: view_products.php");
}

$id = $_GET['id'];
$sql = "SELECT * FROM products WHERE id = '$id'";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

$cat_sql = "SELECT * FROM categories";
$cat_result = mysqli_query($conn, $cat_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        body { background-color: #f4f6f9; margin: 0; overflow: hidden; }
        
        .admin-header { 
            background: #343a40; 
            color: white; 
            padding: 0 30px; 
            height: 70px;
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            box-sizing: border-box;
        }

        .admin-container { display: flex; }
        
        .sidebar { 
            width: 260px; 
            background: #2c3e50; 
            color: white; 
            position: fixed; 
            top: 70px; 
            left: 0; 
            bottom: 0;
            overflow-y: auto;
        }

        .sidebar a { display: block; color: #b8c7ce; padding: 15px 20px; text-decoration: none; border-bottom: 1px solid #3d566e; }
        .sidebar a:hover, .sidebar a.active { background: #1abc9c; color: white; border-left: 5px solid #16a085; }
        
        .main-content { 
            flex: 1; 
            padding: 30px; 
            margin-top: 70px; 
            margin-left: 260px; 
            height: calc(100vh - 70px); 
            overflow-y: auto; 
            box-sizing: border-box;
        }

        .form-container { 
            background: white; 
            padding: 30px; 
            border-radius: 8px; 
            max-width: 600px; 
            margin: 0 auto; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); 
        }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, textarea, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn-submit { background: #f39c12; color: white; padding: 10px 20px; border: none; cursor: pointer; width: 100%; font-weight: bold; }
    </style>
</head>
<body>

    <div class="admin-header">
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
            <a href="add_product.php">Add New Product</a>
            <a href="view_products.php">Manage Products</a>
            <a href="manage_orders.php">Orders</a>
            <a href="manage_payments.php">Payment Gateway</a>
            <a href="manage_users.php">Registered Users</a>
        </div>

        <div class="main-content">
            <div class="form-container">
                <h2 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:10px;">Edit Product</h2>
                <form action="../../Controllers/productControl.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" value="<?php echo $product['name']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id">
                            <?php while($cat = mysqli_fetch_assoc($cat_result)): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php if($cat['id'] == $product['cat_id']) echo 'selected'; ?>>
                                    <?php echo $cat['cat_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" name="price" value="<?php echo $product['price']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="4"><?php echo $product['description']; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Change Image (Optional)</label>
                        <input type="file" name="image">
                        <img src="../../Resources/Products/<?php echo $product['image']; ?>" width="50" style="margin-top:5px;">
                    </div>

                    <button type="submit" name="update_product_btn" class="btn-submit">Update Product</button>
                    <a href="view_products.php" style="display:block; text-align:center; margin-top:10px; color: #555; text-decoration:none;">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>