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
    <title>Manage Orders</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        body { background-color: #f4f6f9; }
        .admin-header { background: #343a40; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .admin-container { display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #2c3e50; color: white; min-height: 100vh; }
        .sidebar a { display: block; color: #b8c7ce; padding: 15px 20px; text-decoration: none; border-bottom: 1px solid #3d566e; }
        .sidebar a:hover, .sidebar a.active { background: #1abc9c; color: white; border-left: 5px solid #16a085; }
        .main-content { flex: 1; padding: 30px; }
        
        .tabs { margin-bottom: 20px; }
        .tab-btn { padding: 10px 20px; border: none; background: #ddd; cursor: pointer; font-weight: bold; border-radius: 20px; margin-right: 5px; transition: 0.3s; }
        .tab-btn.active { background: #343a40; color: white; }
        .tab-btn:hover { background: #bbb; }

        .data-table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .data-table th, .data-table td { padding: 12px 15px; border-bottom: 1px solid #ddd; text-align: left; }
        .data-table th { background-color: #343a40; color: white; }

        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; color: white; }
        .pending { background: #f39c12; }
        .processing { background: #3498db; }
        .delivered { background: #2ecc71; }
        .cancelled { background: #e74c3c; }

        .btn-view { background: #3498db; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; margin-right: 5px; }
        .btn-update { background: #34495e; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; }

        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: white; margin: 5% auto; padding: 25px; border-radius: 8px; width: 50%; position: relative; max-height: 80vh; overflow-y: auto; }
        .close-btn { position: absolute; top: 10px; right: 20px; font-size: 25px; cursor: pointer; }
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
            <a href="manage_orders.php" class="active">Orders</a>
            <a href="manage_payments.php">Payment Gateway</a> <a href="manage_users.php">Registered Users</a>
        </div>

        <div class="main-content">
            <h2>Customer Orders</h2>
            
            <?php if(isset($_SESSION['msg'])): ?>
                <div style="background:#2ecc71; color:white; padding:10px; margin:10px 0; text-align:center; border-radius:5px;"><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
            <?php endif; ?>

            <div class="tabs">
                <button class="tab-btn active" onclick="loadOrders('new', this)">New Orders</button>
                <button class="tab-btn" onclick="loadOrders('delivered', this)">Delivered</button>
                <button class="tab-btn" onclick="loadOrders('cancelled', this)">Cancelled</button>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="orderTableBody">
                    </tbody>
            </table>
        </div>
    </div>

    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('detailsModal')">&times;</span>
            <h3>Order Details</h3>
            <div id="order_details_body">Loading...</div>
        </div>
    </div>

    <div id="statusModal" class="modal">
        <div class="modal-content" style="width: 30%;">
            <span class="close-btn" onclick="closeModal('statusModal')">&times;</span>
            <h3>Update Order Status</h3>
            <form action="../../Controllers/orderControl.php" method="POST">
                <input type="hidden" name="order_id" id="status_order_id">
                <div style="margin: 20px 0;">
                    <label>Select Status:</label>
                    <select name="order_status" id="status_select" style="width:100%; padding:10px; margin-top:5px;">
                        <option value="Pending">Pending</option>
                        <option value="Processing">Processing</option>
                        <option value="Delivered">Delivered</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <button type="submit" name="update_status" style="background:#1abc9c; color:white; border:none; padding:10px; width:100%; cursor:pointer;">Update Status</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            loadOrders('new', document.querySelector('.tab-btn.active'));
        });

        function loadOrders(status, btn) {
            let buttons = document.querySelectorAll('.tab-btn');
            for(let i=0; i<buttons.length; i++) buttons[i].classList.remove('active');
            if(btn) btn.classList.add('active');

            // AJAX Call
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "../../Controllers/orderControl.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("orderTableBody").innerHTML = this.responseText;
                }
            };
            xhr.send("action=fetch_orders&status=" + status);
        }

        function viewOrder(id) {
            document.getElementById('detailsModal').style.display = 'block';
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "../../Controllers/orderControl.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("order_details_body").innerHTML = this.responseText;
                }
            };
            xhr.send("action=get_order_details&order_id=" + id);
        }

        function updateStatus(id, currentStatus) {
            document.getElementById('status_order_id').value = id;
            document.getElementById('status_select').value = currentStatus;
            document.getElementById('statusModal').style.display = 'block';
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