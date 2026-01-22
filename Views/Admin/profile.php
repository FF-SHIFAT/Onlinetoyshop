<?php
session_start();
require_once '../../Models/dbConnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_sql = "SELECT * FROM users WHERE id='$user_id'";
$user_res = mysqli_query($conn, $user_sql);
$user = mysqli_fetch_assoc($user_res);

$addr_res = mysqli_query($conn, "SELECT address_line FROM user_addresses WHERE user_id='$user_id' LIMIT 1");
$address = ($row = mysqli_fetch_assoc($addr_res)) ? $row['address_line'] : "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Profile</title>
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

        .profile-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); max-width: 800px; margin: 0 auto; }
        .profile-header-sec { display: flex; align-items: center; gap: 20px; margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 20px; }
        .profile-img-display { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #343a40; }
        .default-avatar { width: 100px; height: 100px; border-radius: 50%; background: #343a40; color: white; display: flex; align-items: center; justify-content: center; font-size: 40px; font-weight: bold; border: 3px solid #eee; }
        .form-section { margin-bottom: 30px; }
        .form-section h3 { color: #333; margin-bottom: 15px; border-left: 4px solid #343a40; padding-left: 10px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-width { grid-column: 1 / -1; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 8px; color: #555; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .btn-update { background: #3498db; color: white; border: none; padding: 12px 20px; border-radius: 5px; font-weight: bold; cursor: pointer; width: 100%; margin-top: 10px; }
        .btn-update:hover { background: #2980b9; }
        .btn-pass { background: #e74c3c; } 
        .btn-pass:hover { background: #c0392b; }
        .alert-msg { padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
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
            <div class="profile-box">
                <h2 style="margin-top: 0; margin-bottom: 20px;">My Profile</h2>

                <?php if(isset($_SESSION['msg'])): ?>
                    <div class="alert-msg alert-<?php echo $_SESSION['msg_type']; ?>">
                        <?php echo $_SESSION['msg']; unset($_SESSION['msg']); unset($_SESSION['msg_type']); ?>
                    </div>
                <?php endif; ?>

                <div class="profile-header-sec">
                    <?php if (!empty($user['profile_image']) && file_exists("../../Resources/Users/" . $user['profile_image'])): ?>
                        <img src="../../Resources/Users/<?php echo $user['profile_image']; ?>" alt="Profile" class="profile-img-display">
                    <?php else: ?>
                        <div class="default-avatar">
                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>

                    <div>
                        <h2><?php echo $user['name']; ?></h2>
                        <p style="color:#777;">Administrator</p>
                        <p><?php echo $user['email']; ?></p>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Edit Information</h3>
                    <form action="../../Controllers/profileControl.php" method="POST" enctype="multipart/form-data">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="name" value="<?php echo $user['name']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" name="phone" value="<?php echo $user['phone']; ?>" required>
                            </div>
                            <div class="form-group full-width">
                                <label>Address</label>
                                <textarea name="address" rows="2"><?php echo $address; ?></textarea>
                            </div>
                            <div class="form-group full-width">
                                <label>Change Photo</label>
                                <input type="file" name="profile_image" accept="image/*">
                            </div>
                        </div>
                        <button type="submit" name="update_profile" class="btn-update">Update Information</button>
                    </form>
                </div>

                <hr style="border:0; border-top:1px solid #eee; margin: 30px 0;">

                <div class="form-section">
                    <h3>Change Password</h3>
                    <form action="../../Controllers/profileControl.php" method="POST">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" required>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" name="new_password" required>
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" name="confirm_password" required>
                            </div>
                        </div>
                        <button type="submit" name="change_password" class="btn-update btn-pass">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>