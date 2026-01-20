<?php
session_start();
require_once '../../Models/dbConnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$sql = "SELECT orders.*, users.name as customer_name 
        FROM orders 
        JOIN users ON orders.user_id = users.id 
        ORDER BY orders.id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders</title>
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

        .badge { 
            padding: 5px 10px; 
            border-radius: 15px; 
            font-size: 12px; 
            font-weight: bold; 
            color: white; 
        }
        .pending { 
            background: #f39c12; 
        }
        .processing { 
            background: #3498db; 
        }
        .delivered { 
            background: #2ecc71; 
        }
        .cancelled { 
            background: #e74c3c; 
        }

        .btn-view { 
            background: #3498db; 
            color: white; 
            border: none; 
            padding: 5px 10px; 
            cursor: pointer; 
            border-radius: 4px; 
        }
        .btn-update { 
            background: #34495e; 
            color: white; 
            border: none; 
            padding: 5px 10px; 
            cursor: pointer; 
            border-radius: 4px; 
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
            width: 50%; 
            position: relative; 
            max-height: 80vh; 
            overflow-y: auto; 
        }
        .close-btn { 
            position: absolute; 
            top: 10px; 
            right: 20px; 
            font-size: 25px; 
            cursor: pointer; 
        }
    </style>
</head>
<body>

    <div class="admin-header">
        <h2> Admin Panel</h2>
        <a href="../../Controllers/authControl.php?logout=true" class="btn" style="background: red; padding: 5px 10px; color:white; text-decoration:none; border-radius:4px;">Logout</a>
    </div>

    <div class="admin-container">
        <div class="sidebar">
            <a href="dashboard.php">Dashboard</a>
            <a href="manage_categories.php">Manage Categories</a>
            <a href="add_product.php">Add New Product</a>
            <a href="view_products.php">Manage Products</a>
            <a href="manage_orders.php" class="active">Orders</a>
            <a href="manage_users.php">Registered Users</a>
        </div>

        <div class="main-content">
            <h2>Customer Orders</h2>

            <?php if(isset($_SESSION['msg'])): ?>
                <div style="background:#2ecc71; color:white; padding:10px; margin:10px 0; text-align:center; border-radius:5px;"><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
            <?php endif; ?>

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
                <tbody>
                    <?php 
                    if(mysqli_num_rows($result) > 0):
                        while($row = mysqli_fetch_assoc($result)): 
                            $status_class = strtolower($row['order_status']);
                    ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><?php echo $row['customer_name']; ?></td>
                        <td>Tk. <?php echo $row['total_amount']; ?></td>
                        <td><?php echo $row['payment_method']; ?></td>
                        <td><span class="badge <?php echo $status_class; ?>"><?php echo $row['order_status']; ?></span></td>
                        <td><?php echo date('d M, Y', strtotime($row['order_date'])); ?></td>
                        <td>
                            <button class="btn-view" onclick="viewOrder('<?php echo $row['id']; ?>')">Details</button>
                            <button class="btn-update" onclick="updateStatus('<?php echo $row['id']; ?>', '<?php echo $row['order_status']; ?>')">Status</button>
                        </td>
                    </tr>
                    <?php endwhile; 
                    else: ?>
                        <tr><td colspan="7" style="text-align:center; padding:20px;">No orders found!</td></tr>
                    <?php endif; ?>
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
        function viewOrder(id) {
            document.getElementById('detailsModal').style.display = 'block';
            
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../../Controllers/orderControl.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if (this.status == 200) {
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