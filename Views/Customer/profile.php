<?php
session_start();
require_once '../../Models/dbConnect.php';

if (!isset($_SESSION['user_id'])) {
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
    <title>My Profile</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .profile-container { max-width: 800px; margin: 30px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        
        .profile-header-sec { display: flex; align-items: center; gap: 20px; margin-bottom: 30px; border-bottom: 2px solid #f0f0f0; padding-bottom: 20px; }
        
        .profile-img-display { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #ff6f61; }
        
        .default-avatar {
            width: 100px; height: 100px; border-radius: 50%; 
            background: #ff6f61; color: white; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 40px; font-weight: bold; border: 3px solid #eee;
        }

        .profile-header-sec h2 { margin: 0; color: #333; }
        .profile-header-sec p { margin: 5px 0 0; color: #666; }

        .form-section { margin-bottom: 40px; }
        .form-section h3 { color: #555; margin-bottom: 20px; border-left: 4px solid #ff6f61; padding-left: 10px; }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-width { grid-column: 1 / -1; }
        
        .btn-update { background: #ff6f61; color: white; border: none; padding: 12px 25px; border-radius: 5px; font-weight: bold; cursor: pointer; transition: 0.3s; margin-top: 10px; width: 100%; }
        .btn-update:hover { background: #e65b50; }
        
        .btn-pass { background: #333; width: auto; } 
        .btn-pass:hover { background: #555; }

        .alert-msg { padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

    <header>
        <div class="container nav-container">
            <h1><a href="../../index.php" style="color: #ff6f61; text-decoration: none;">ToyShop</a></h1>
            <nav class="nav-links">
                <a href="../../index.php">Home</a>
                <a href="cart.php">Cart</a>
                <a href="my_orders.php">My Orders</a>
                <a href="profile.php" style="font-weight:bold; color:#ff6f61;"><?php echo $_SESSION['user_name']; ?></a>
                <a href="../../Controllers/authControl.php?logout=true" class="btn-logout">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container" style="margin-top: 30px;">
        <a href="../../index.php" class="btn-back">&larr; Back to Shop</a>

        <div class="profile-container">
            
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
                    <p><?php echo $user['email']; ?></p>
                </div>
            </div>

            <div class="form-section">
                <h3>Edit Personal Information</h3>
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
                            <label>Email Address (Cannot be changed)</label>
                            <input type="email" value="<?php echo $user['email']; ?>" readonly style="background: #f9f9f9; cursor: not-allowed;">
                        </div>
                        <div class="form-group full-width">
                            <label>Shipping Address</label>
                            <textarea name="address" rows="3" placeholder="Enter your address..."><?php echo $address; ?></textarea>
                        </div>
                        
                        <div class="form-group full-width">
                            <label>Change Profile Photo</label>
                            <input type="file" name="profile_image" accept="image/*" style="padding: 10px; border: 1px dashed #ccc; background: #fafafa;">
                        </div>
                    </div>
                    <button type="submit" name="update_profile" class="btn-update">Update Profile Information</button>
                </form>
            </div>

            <hr style="border:0; border-top:1px solid #eee; margin: 30px 0;">

            <div class="form-section">
                <h3>Change Password</h3>
                <form action="../../Controllers/profileControl.php" method="POST">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" required placeholder="Enter current password">
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" required placeholder="Enter new password">
                        </div>
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" required placeholder="Re-enter new password">
                        </div>
                    </div>
                    <button type="submit" name="change_password" class="btn-update btn-pass" style="width: 100%;">Change Password</button>
                </form>
            </div>

        </div>
    </div>

</body>
</html>