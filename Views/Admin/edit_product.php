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
        body { background-color: #f4f6f9; }
        .form-container { 
            background: white; 
            padding: 30px; 
            border-radius: 8px; 
            max-width: 600px; 
            margin: 50px auto; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); 
        }
        .form-group { 
            margin-bottom: 15px; 
        }
        .form-group label { 
            display: block; 
            margin-bottom: 5px; 
            font-weight: bold; 
        }
        .form-group input, textarea, select { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
        }
        .btn-submit { 
            background: #f39c12; 
            color: white; 
            padding: 10px 20px; 
            border: none; 
            cursor: pointer; 
            width: 100%; 
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Edit Product</h2>
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
            <a href="view_products.php" style="display:block; text-align:center; margin-top:10px;">Cancel</a>
        </form>
    </div>
</body>
</html>