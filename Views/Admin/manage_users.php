<?php
session_start();
require_once '../../Models/dbConnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>

        body { background-color: #f4f6f9; }
        .admin-header { background: #343a40; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .admin-container { display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #2c3e50; color: white; min-height: 100vh; }
        .sidebar a { display: block; color: #b8c7ce; padding: 15px 20px; text-decoration: none; border-bottom: 1px solid #3d566e; }
        .sidebar a:hover, .sidebar a.active { background: #1abc9c; color: white; border-left: 5px solid #16a085; }
        .main-content { flex: 1; padding: 30px; }

        .user-controls { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background: #fff; padding: 10px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .tabs button { padding: 8px 20px; cursor: pointer; border: none; background: #f1f1f1; font-weight: bold; border-radius: 20px; transition: 0.3s; margin-right: 5px; }
        .tabs button.active { background: #343a40; color: white; }
        .tabs button:hover { background: #ddd; }
        .search-box input { padding: 8px 15px; width: 250px; border: 1px solid #ddd; border-radius: 20px; outline: none; }

        .btn-view { background: #3498db; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; }
        .btn-edit { background: #f39c12; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; }
        .btn-delete { background: #e74c3c; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; }

        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); overflow-y: auto; }
        .modal-content { background-color: white; margin: 5% auto; padding: 25px; border-radius: 8px; width: 50%; position: relative; }
        .close-btn { position: absolute; top: 10px; right: 20px; font-size: 25px; cursor: pointer; color: #aaa; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .btn-submit { background: #1abc9c; color: white; padding: 10px; width: 100%; border: none; cursor: pointer; }
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
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Manage Users</h2>
                <button onclick="openAddModal()" style="background: #1abc9c; color: white; padding: 10px 15px; border: none; cursor: pointer; border-radius: 5px; font-weight: bold;">+ Add New User</button>
            </div>

            <?php if(isset($_SESSION['msg'])): ?>
                <div style="background:#2ecc71; color:white; padding:10px; margin:10px 0; text-align:center; border-radius: 5px;"><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
            <?php endif; ?>

            <div class="user-controls">
                <div class="tabs">
                    <button class="tab-btn active" onclick="setRole('customer', this)">Customers</button>
                    <button class="tab-btn" onclick="setRole('admin', this)">Admins</button>
                </div>

                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search by name, email..." onkeyup="loadUsers()">
                </div>
            </div>

            <table class="data-table" style="width:100%; border-collapse:collapse; background:white; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                <thead style="background:#343a40; color:white;">
                    <tr>
                        <th style="padding:12px;">ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    </tbody>
            </table>
        </div>
    </div>

    <div id="viewModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('viewModal')">&times;</span>
            <h3 style="border-bottom: 2px solid #1abc9c; display: inline-block; padding-bottom: 5px;">User Details</h3>
            <div id="view_body" style="margin-top: 15px; line-height: 1.8;">Loading...</div>
        </div>
    </div>

    <div id="userModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('userModal')">&times;</span>
            <h3 id="modalTitle">Add New User</h3>
            <form action="../../Controllers/userControl.php" method="POST" id="userForm">
                <input type="hidden" name="user_id" id="u_id">
                
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" id="u_name" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="u_email" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" id="u_phone" required>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" id="u_role">
                        <option value="customer">Customer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" id="u_address" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Password <small style="color:red;">(Leave blank to keep current)</small></label>
                    <input type="password" name="password" id="u_pass" placeholder="Enter new password">
                </div>

                <button type="submit" name="add_user_btn" id="btnAdd" class="btn-submit">Create User</button>
                <button type="submit" name="update_user_btn" id="btnUpdate" class="btn-submit" style="display:none; background:#f39c12;">Update User</button>
            </form>
        </div>
    </div>

    <script>
        let currentRole = 'customer';

        document.addEventListener("DOMContentLoaded", function() {
            loadUsers();
        });

        function setRole(role, btn) {
            currentRole = role;
            let buttons = document.querySelectorAll('.tab-btn');
            for (let i = 0; i < buttons.length; i++) {
                buttons[i].classList.remove('active');
            }
            btn.classList.add('active');
            loadUsers();
        }

        function loadUsers() {
            let search = document.getElementById('searchInput').value;
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "../../Controllers/userControl.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            
            xhr.onreadystatechange = function() {
                if (this.readyState === 4 && this.status === 200) {
                    document.getElementById("userTableBody").innerHTML = this.responseText;
                }
            };
            xhr.send("action=fetch_users&role_filter=" + currentRole + "&search_query=" + search);
        }

        function viewUser(id) {
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "../../Controllers/userControl.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            
            xhr.onreadystatechange = function() {
                if (this.readyState === 4 && this.status === 200) {
                    let data = JSON.parse(this.responseText);
                    
                    let html = "";
                    html += "<p><strong>Name:</strong> " + data.name + "</p>";
                    html += "<p><strong>Email:</strong> " + data.email + "</p>";
                    html += "<p><strong>Phone:</strong> " + data.phone + "</p>";
                    html += "<p><strong>Role:</strong> <span style='background:#333; color:white; padding:2px 6px; border-radius:4px; font-size:12px;'>" + data.role.toUpperCase() + "</span></p>";
                    
                    let address = data.address_line ? data.address_line : 'N/A';
                    html += "<p><strong>Address:</strong> " + address + "</p>";
                    html += "<p><strong>Joined Date:</strong> " + data.created_at + "</p>";
                    
                    document.getElementById('view_body').innerHTML = html;
                    document.getElementById('viewModal').style.display = 'block';
                }
            };
            xhr.send("action=get_user_details&user_id=" + id);
        }

        function openAddModal() {
            document.getElementById('userForm').reset();
            document.getElementById('modalTitle').innerText = "Add New User";
            document.getElementById('btnAdd').style.display = 'block';
            document.getElementById('btnUpdate').style.display = 'none';
            document.getElementById('userModal').style.display = 'block';
        }

        function editUser(id) {
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "../../Controllers/userControl.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            
            xhr.onreadystatechange = function() {
                if (this.readyState === 4 && this.status === 200) {
                    let data = JSON.parse(this.responseText);
                    
                    document.getElementById('u_id').value = data.id;
                    document.getElementById('u_name').value = data.name;
                    document.getElementById('u_email').value = data.email;
                    document.getElementById('u_phone').value = data.phone;
                    document.getElementById('u_role').value = data.role;
                    document.getElementById('u_address').value = data.address_line;
                    
                    document.getElementById('modalTitle').innerText = "Edit User Details";
                    document.getElementById('btnAdd').style.display = 'none';
                    document.getElementById('btnUpdate').style.display = 'block';
                    document.getElementById('userModal').style.display = 'block';
                }
            };
            xhr.send("action=get_user_details&user_id=" + id);
        }

        function deleteUser(id) {
            if(confirm("Are you sure? This action cannot be undone!")) {
                let xhr = new XMLHttpRequest();
                xhr.open("POST", "../../Controllers/userControl.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                
                xhr.onreadystatechange = function() {
                    if (this.readyState === 4 && this.status === 200) {
                        if (this.responseText.trim() == "success") {
                            loadUsers();
                        } else {
                            alert("Failed to delete!");
                        }
                    }
                };
                xhr.send("action=delete_user&user_id=" + id);
            }
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = "none";
            }
        }
    </script>

</body>
</html>