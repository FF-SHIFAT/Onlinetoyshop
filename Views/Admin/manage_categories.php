<?php
session_start();
require_once '../../Models/dbConnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$sql = "SELECT * FROM categories ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        body { 
            background-color: #f4f6f9; 
        }
        .admin-header { 
            background: #343a40; 
            color: white; 
            padding: 15px 30px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .admin-container { 
            display: flex; 
            min-height: 100vh; 
        }
        .sidebar { 
            width: 260px; 
            background: #2c3e50; 
            color: white; 
            min-height: 100vh; 
        }
        .sidebar a { 
            display: block; 
            color: #b8c7ce; 
            padding: 15px 20px; 
            text-decoration: none; 
            border-bottom: 1px solid #3d566e; 
        }
        .sidebar a:hover, .sidebar a.active { 
            background: #1abc9c; 
            color: white; 
            border-left: 5px solid #16a085; 
        }
        .main-content { 
            flex: 1; 
            padding: 30px; 
        }
        
        .data-table { 
            width: 100%; 
            border-collapse: collapse; 
            background: white; 
            margin-top: 20px; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.05); 
        }
        .data-table th, .data-table td { 
            padding: 12px 15px; 
            border-bottom: 1px solid #ddd; 
            text-align: left; 
        }
        .data-table th { 
            background-color: #343a40; 
            color: white; 
        }
        
        .modal { 
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0; 
            top: 0; 
            width: 100%; 
            height: 100%; 
            background-color: rgba(0,0,0,0.5); 
        }
        .modal-content { 
            background-color: white; 
            margin: 10% auto; 
            padding: 25px; 
            border-radius: 8px; 
            width: 40%; 
            position: relative; 
        }
        .close-btn { 
            position: absolute; 
            top: 10px; 
            right: 20px; 
            font-size: 25px; 
            cursor: pointer; 
            color: #aaa; 
        }
        .close-btn:hover { 
            color: red; 
        }
        .form-group { 
            margin-bottom: 15px; 
        }
        .form-group input { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
        }
        .btn-submit { 
            background: #1abc9c; 
            color: white; 
            padding: 10px 20px; 
            border: none; 
            cursor: pointer; 
            width: 100%; 
        }
        
        .btn-edit { 
            background: #f39c12; 
            color: white; 
            border: none; 
            padding: 6px 12px; 
            border-radius: 4px; 
            cursor: pointer; 
        }
        .btn-delete { 
            background: #e74c3c; 
            color: white; 
            padding: 6px 12px; 
            border-radius: 4px; 
            text-decoration: none; 
            display: inline-block; 
        }
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
    <a href="add_product.php">Add New Product</a>
    <a href="view_products.php">Manage Products</a>
    <a href="manage_orders.php">Orders</a>
    <a href="manage_payments.php">Payment Gateway</a> <a href="manage_users.php">Registered Users</a>
    </div>

        <div class="main-content">
            <div style="display:flex; justify-content:space-between;">
                <h2>Product Categories</h2>
                <button onclick="openAddModal()" class="btn" style="background: #1abc9c; color: white; padding: 10px; cursor: pointer; border:none; border-radius:4px;">+ Add Category</button>
            </div>

            <?php if(isset($_SESSION['msg'])): ?>
                <div style="background:#2ecc71; color:white; padding:10px; margin:10px 0; text-align:center; border-radius:5px;"><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
            <?php endif; ?>

            <table class="data-table">
                <thead>
                    <tr>
                        <th width="10%">ID</th>
                        <th>Category Name</th>
                        <th width="20%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['cat_name']; ?></td>
                        <td>
                            <button onclick="openEditModal('<?php echo $row['id']; ?>', '<?php echo $row['cat_name']; ?>')" class="btn-edit">Edit</button>
                            <a href="../../Controllers/categoryControl.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')" class="btn-delete">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('addModal')">&times;</span>
            <h3>Add New Category</h3>
            <form action="../../Controllers/categoryControl.php" method="POST">
                <div class="form-group">
                    <label>Category Name</label>
                    <input type="text" name="cat_name" placeholder="Ex: Puzzle Games" required>
                </div>
                <button type="submit" name="add_category" class="btn-submit">Add Category</button>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('editModal')">&times;</span>
            <h3>Edit Category</h3>
            <form action="../../Controllers/categoryControl.php" method="POST">
                <input type="hidden" name="cat_id" id="edit_cat_id">
                <div class="form-group">
                    <label>Category Name</label>
                    <input type="text" name="cat_name" id="edit_cat_name" required>
                </div>
                <button type="submit" name="update_category" class="btn-submit" style="background: #f39c12;">Update Category</button>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addModal').style.display = 'block';
        }

        function openEditModal(id, name) {
            document.getElementById('edit_cat_id').value = id;
            document.getElementById('edit_cat_name').value = name;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = "none";
            }
        }
    </script>
</body>
</html>