<?php
require_once '../Models/dbConnect.php';

if (isset($_POST['input'])) {
    
    $input = mysqli_real_escape_string($conn, $_POST['input']);
    
    $sql = "SELECT * FROM products WHERE name LIKE '%{$input}%' OR description LIKE '%{$input}%'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <div class="product-card">
                <a href="Views/Customer/product_details.php?id=<?php echo $row['id']; ?>" class="card-link">
                    <img src="Resources/Products/<?php echo $row['image']; ?>" class="product-img" alt="Toy">
                    <div class="p-info">
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p class="price">Tk. <?php echo $row['price']; ?></p>
                        <?php if($row['stock'] > 0): ?>
                            <span style="color: green; font-size: 13px; font-weight:bold;">In Stock</span>
                        <?php else: ?>
                            <span style="color: red; font-size: 13px; font-weight:bold;">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                </a>

                <?php if($row['stock'] > 0): ?>
                <div class="btn-group">
                    <form action="Controllers/cartControl.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" name="add_to_cart" class="btn-action btn-cart">Cart</button>
                    </form>
                    
                    <form action="Controllers/cartControl.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" name="buy_now" class="btn-action btn-buy">Buy</button>
                    </form>
                </div>
                <?php else: ?>
                    <div style="padding:15px; text-align:center; background:#f9f9f9; color:#999; font-weight:bold;">Sold Out</div>
                <?php endif; ?>
            </div>
            <?php
        }
    } else {
        echo "<p style='color:red; font-weight:bold; width:100%; text-align:center;'>No Product Found Matching: " . htmlspecialchars($input) . "</p>";
    }
}
?>