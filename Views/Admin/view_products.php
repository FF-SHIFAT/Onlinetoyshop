<?php
session_start();
require_once '../../Models/dbConnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$cat_sql = "SELECT * FROM categories";
$cat_result = mysqli_query($conn, $cat_sql);


$where_clause = "WHERE 1";

if (isset($_GET['cat_id']) && $_GET['cat_id'] != 'all') {
    $cat_id = mysqli_real_escape_string($conn, $_GET['cat_id']);
    $where_clause .= " AND products.cat_id = '$cat_id'";
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where_clause .= " AND products.name LIKE '%$search%'";
}

$sql = "SELECT products.*, categories.cat_name 
        FROM products 
        LEFT JOIN categories ON products.cat_id = categories.id 
        $where_clause 
        ORDER BY products.id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>
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
        
        .controls-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            flex-wrap: wrap; gap: 10px;
        }

        .filter-group { 
            display: flex; 
            align-items: center; 
            gap: 10px; 
        }
        
        .control-input { 
            padding: 8px 12px; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            outline: none; 
        }
        .btn-go { 
            background: #343a40; 
            color: white; 
            border: none; 
            padding: 8px 15px; 
            border-radius: 5px; 
            cursor: pointer; 
        }
        
        .btn-add-new {
            background: #1abc9c; 
            color: white; 
            padding: 10px 15px; 
            border: none; 
            cursor: pointer; 
            border-radius: 5px; 
            font-weight: bold; 
            text-decoration: none; 
            display: inline-block;
        }
        .btn-add-new:hover { 
            background: #16a085; 
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
        
        .btn-action { 
            padding: 5px 10px; 
            border-radius: 4px; 
            color: white; 
            text-decoration: none; 
            font-size: 12px; 
            margin-right: 5px; 
            cursor: pointer; 
            border:none; 
        }
        .btn-view { 
            background: #3498db; 
        }
        .btn-edit { 
            background: #f39c12; 
        }
        .btn-delete { 
            background: #e74c3c; 
        }

        .modal { 
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0; 
            top: 0; 
            width: 100%; 
            height: 100%; 
            background-color: rgba(0,0,0,0.6); 
            overflow: hidden; 
        }
        .modal-content { 
            background-color: white; 
            position: absolute; 
            top: 50%; 
            left: 50%; 
            transform: translate(-50%, -50%); 
            padding: 25px; 
            border-radius: 10px; 
            width: 50%; 
            max-width: 600px; 
            max-height: 85vh; 
            overflow-y: auto; 
            box-shadow: 0 5px 25px rgba(0,0,0,0.3); 
            animation: fadeIn 0.3s; 
        }
        .close-btn { 
            position: sticky; 
            top: 0; 
            float: right; 
            font-size: 30px; 
            font-weight: bold; 
            color: #aaa; 
            cursor: pointer; 
            background: white; 
            width: 100%; 
            text-align: right; 
            z-index: 100; 
            padding-bottom: 10px; 
        }
        .desc-box { 
            background-color: #f8f9fa; 
            border: 1px solid #e9ecef; 
            padding: 15px; 
            border-radius: 6px; 
            max-height: 150px; 
            overflow-y: auto; 
            font-size: 14px; 
            color: #555; 
            line-height: 1.6; 
            margin-top: 5px; 
            white-space: pre-wrap; 
            word-wrap: break-word; 
        }
        @keyframes fadeIn { 
            from { opacity: 0; transform: translate(-50%, -60%); 
        } to { opacity: 1; transform: translate(-50%, -50%); } }
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
            <h2>Manage Products</h2>

            <form method="GET" action="view_products.php" class="controls-bar">
                <div class="filter-group">
                    <select name="cat_id" class="control-input" onchange="this.form.submit()">
                        <option value="all">All Categories</option>
                        <?php while($cat = mysqli_fetch_assoc($cat_result)): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php if(isset($_GET['cat_id']) && $_GET['cat_id'] == $cat['id']) echo 'selected'; ?>>
                                <?php echo $cat['cat_name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                    <input type="text" name="search" placeholder="Search by name..." class="control-input" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                    <button type="submit" class="btn-go">Search</button>
                    <?php if(isset($_GET['search']) || (isset($_GET['cat_id']) && $_GET['cat_id'] != 'all')): ?>
                        <a href="view_products.php" style="color: red; font-size: 14px; text-decoration: underline;">Reset</a>
                    <?php endif; ?>
                </div>

                <a href="add_product.php" class="btn-add-new">+ Add New Product</a>
            </form>

            <?php if(isset($_SESSION['msg'])): ?>
                <div style="background:#2ecc71; color:white; padding:10px; margin:10px 0; text-align:center; border-radius: 5px;">
                    <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
                </div>
            <?php endif; ?>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(mysqli_num_rows($result) > 0):
                        while($row = mysqli_fetch_assoc($result)): 
                            $p_name = htmlspecialchars($row['name'], ENT_QUOTES);
                            $p_desc = str_replace(array("\r", "\n"), ' ', htmlspecialchars($row['description'], ENT_QUOTES));
                            $p_image = $row['image'];
                            $p_cat = htmlspecialchars($row['cat_name'], ENT_QUOTES);
                    ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td>
                            <img src="../../Resources/Products/<?php echo $row['image']; ?>" width="40" style="vertical-align:middle; border-radius: 4px;">
                            <?php echo $row['name']; ?>
                        </td>
                        <td><?php echo $row['cat_name']; ?></td>
                        <td>Tk. <?php echo $row['price']; ?></td>
                        <td>
                             <?php echo ($row['stock'] > 0) ? "<span style='color:green; font-weight:bold;'>{$row['stock']}</span>" : "<span style='color:red; font-weight:bold;'>Out</span>"; ?>
                        </td>
                        <td>
                            <button class="btn-action btn-view" onclick="openModal('<?php echo $p_name; ?>', '<?php echo $p_desc; ?>', '<?php echo $row['price']; ?>', '<?php echo $row['stock']; ?>', '<?php echo $p_image; ?>', '<?php echo $p_cat; ?>')">View</button>
                            <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn-action btn-edit">Edit</a>
                            <a href="../../Controllers/productControl.php?delete_id=<?php echo $row['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; 
                    else: ?>
                        <tr><td colspan="6" style="text-align:center; padding: 20px;">No products found matching your filter!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h2 id="m_name" style="text-align:center; color:#333; margin-bottom: 15px;">Product Name</h2>
            <div class="modal-body">
                <img id="m_image" src="" alt="Product Image" style="display:block; margin: 0 auto 15px; max-width: 200px; border-radius: 8px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;">
                    <p><strong>Category:</strong> <span id="m_cat"></span></p>
                    <p><strong>Stock:</strong> <span id="m_stock"></span></p>
                    <p><strong>Price:</strong> <span style="color: #e67e22; font-weight: bold;">Tk. <span id="m_price"></span></span></p>
                </div>
                <hr style="border: 0; border-top: 1px solid #eee; margin: 10px 0;">
                <p><strong>Description:</strong></p>
                <div id="m_desc" class="desc-box"></div>
            </div>
        </div>
    </div>

    <script>
        function openModal(name, desc, price, stock, img, cat) {
            document.getElementById('m_name').innerText = name;
            document.getElementById('m_desc').innerText = desc;
            document.getElementById('m_price').innerText = price;
            document.getElementById('m_stock').innerText = stock;
            document.getElementById('m_cat').innerText = cat;
            document.getElementById('m_image').src = "../../Resources/Products/" + img;
            document.getElementById('productModal').style.display = "block";
            document.body.style.overflow = "hidden";
        }
        function closeModal() {
            document.getElementById('productModal').style.display = "none";
            document.body.style.overflow = "auto";
        }
        window.onclick = function(event) {
            if (event.target == document.getElementById('productModal')) { closeModal(); }
        }
    </script>
</body>
</html>