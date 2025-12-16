<?php
// cart.php - 购物车页面
require_once 'config.php';
require_once 'header.php';

// 检查用户是否登录
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: customer_login.php');
    exit();
}

$customer_id = $_SESSION['customer_id'];

// 处理购物车操作（保持不变）
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ... 原有的购物车操作代码保持不变
}

// 获取购物车商品 - 修复SQL查询
try {
    $cart_stmt = $pdo_product->prepare("
        SELECT ci.* 
        FROM cart_items ci 
        WHERE ci.customer_id = ?
        ORDER BY ci.added_at DESC
    ");
    $cart_stmt->execute([$customer_id]);
    $cart_items = $cart_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 计算总价
    $total_amount = 0;
    foreach ($cart_items as $item) {
        $total_amount += $item['product_price'] * $item['quantity'];
    }
} catch (PDOException $e) {
    $error = "Error loading cart: " . $e->getMessage();
    $cart_items = [];
    $total_amount = 0;
}
?>

<!-- 样式保持不变 -->

<div class="cart-container">
    <div class="cart-header">
        <h1>Your Shopping Cart</h1>
        <p>Review your items and proceed to checkout</p>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (empty($cart_items)): ?>
        <div class="empty-cart">
            <div class="empty-cart-icon">🛒</div>
            <h2>Your cart is empty</h2>
            <p>Browse our delicious products and add some items to your cart!</p>
            <a href="product.php" class="btn" style="margin-top: 20px;">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-items">
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <!-- 移除图片部分或使用默认图标 -->
                    <div class="item-image">
                        <span>🍔</span> <!-- 使用默认汉堡图标 -->
                    </div>
                    
                    <div class="item-details">
                        <div class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                        <div class="item-price">$<?php echo number_format($item['product_price'], 2); ?></div>
                    </div>
                    
                    <div class="item-controls">
                        <form method="POST" action="" class="quantity-control">
                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="0" max="99" class="quantity-input">
                            <button type="submit" name="update_quantity" class="btn-update">Update</button>
                        </form>
                        
                        <form method="POST" action="">
                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                            <button type="submit" name="remove_item" class="btn-remove">Remove</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="cart-summary">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>$<?php echo number_format($total_amount, 2); ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping:</span>
                <span>$0.00</span>
            </div>
            <div class="summary-row">
                <span>Tax:</span>
                <span>$0.00</span>
            </div>
            <div class="summary-row total-row">
                <span>Total:</span>
                <span>$<?php echo number_format($total_amount, 2); ?></span>
            </div>
            
            <div class="cart-actions">
                <a href="product.php" class="btn-continue">Continue Shopping</a>
                <a href="checkout.php" class="btn-checkout">Proceed to Checkout</a>
            </div>
            
            <form method="POST" action="">
                <button type="submit" name="clear_cart" class="btn-clear" onclick="return confirm('Are you sure you want to clear your cart?')">Clear Cart</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>