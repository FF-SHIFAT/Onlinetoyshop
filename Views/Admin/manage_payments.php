<?php
session_start();
require_once '../../Models/dbConnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); 
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM payment_methods");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Payments</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        body { background-color: #f4f6f9; }
        .admin-header { background: #343a40; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .admin-container { display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #2c3e50; color: white; min-height: 100vh; }
        .sidebar a { display: block; color: #b8c7ce; padding: 15px 20px; text-decoration: none; border-bottom: 1px solid #3d566e; }
        .sidebar a:hover, .sidebar a.active { background: #1abc9c; color: white; border-left: 5px solid #16a085; }
        .main-content { flex: 1; padding: 30px; }
        
        .form-box { background: white; padding: 25px; margin-bottom: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .form-box h3 { margin-top: 0; color: #333; }
        
        .inp-group { margin-bottom: 15px; }
        .inp-group label { display: block; font-weight: bold; margin-bottom: 5px; }
        .inp-group input { padding: 10px; width: 100%; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        
        .btn-add { padding: 10px 20px; background: #2ecc71; color: white; border: none; cursor: pointer; border-radius: 4px; font-weight: bold; }
        .btn-add:hover { background: #27ae60; }
        
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #343a40; color: white; }
        
        .btn-edit { background: #f39c12; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 13px; margin-right: 5px; cursor: pointer; border: none; }
        .btn-del { background: #e74c3c; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 13px; }
        
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: white; margin: 10% auto; padding: 25px; border-radius: 8px; width: 40%; position: relative; box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .close-btn { position: absolute; top: 10px; right: 20px; font-size: 25px; cursor: pointer; color: #aaa; }
        .close-btn:hover { color: black; }
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
            <a href="manage_payments.php" class="active">Payment Gateway</a>
            <a href="manage_users.php">Registered Users</a>
        </div>

        <div class="main-content">
            <h2>Manage Payment Gateways</h2>
            
            <?php if(isset($_SESSION['msg'])): ?>
                <div style="color: green; background: #eafaf1; padding: 10px; margin-bottom: 15px; border-left: 5px solid #2ecc71;">
                    <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
                </div>
            <?php endif; ?>

            <div class="form-box">
                <h3>Add New Payment Method</h3>
                <form action="../../Controllers/paymentControl.php" method="POST">
                    <div style="display: flex; gap: 10px;">
                        <input type="text" name="name" placeholder="Method Name (e.g. Rocket)" required style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        <input type="text" name="details" placeholder="Account Number & Type (e.g. 017... Personal)" required style="flex: 2; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        <button type="submit" name="add_method" class="btn-add">+ Add</button>
                    </div>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Method Name</th>
                        <th>Account Details</th>
                        <th style="width: 150px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(mysqli_num_rows($result) > 0):
                        while($row = mysqli_fetch_assoc($result)): 
                    ?>
                    <tr>
                        <td><strong><?php echo $row['method_name']; ?></strong></td>
                        <td><?php echo $row['account_number']; ?></td>
                        <td>
                            <button class="btn-edit" onclick="openEditModal('<?php echo $row['id']; ?>', '<?php echo $row['method_name']; ?>', '<?php echo $row['account_number']; ?>')">Edit</button>
                            
                            <a href="../../Controllers/paymentControl.php?delete_id=<?php echo $row['id']; ?>" class="btn-del" onclick="return confirm('Are you sure you want to delete this method?');">Delete</a>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                        <tr><td colspan="3" style="text-align:center; padding: 20px;">No payment methods found. Add one above!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h3 style="border-bottom: 2px solid #f39c12; padding-bottom: 10px; display: inline-block;">Edit Payment Method</h3>
            
            <form action="../../Controllers/paymentControl.php" method="POST" style="margin-top: 20px;">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="inp-group">
                    <label>Method Name</label>
                    <input type="text" name="name" id="edit_name" required>
                </div>
                
                <div class="inp-group">
                    <label>Account Details</label>
                    <input type="text" name="details" id="edit_details" required>
                </div>
                
                <button type="submit" name="update_method" style="width: 100%; padding: 10px; background: #f39c12; color: white; border: none; font-weight: bold; cursor: pointer; border-radius: 4px;">Update Method</button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, name, details) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_details').value = details;
            
            document.getElementById('editModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        window.onclick = function(event) {
            var modal = document.getElementById('editModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

</body>
</html>