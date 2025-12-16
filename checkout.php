<?php
// checkout.php - 结算页面
require_once 'config.php';
require_once 'header.php';

// 检查用户是否登录
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo "<script>window.location.href = 'customer_login.php';</script>";
    exit();
}

$customer_id = $_SESSION['customer_id'];
$customer_name = $_SESSION['customer_name'];
$customer_email = $_SESSION['customer_email'];
$customer_phone = $_SESSION['customer_phone'];

// 获取购物车商品
try {
    $cart_stmt = $pdo_product->prepare("
        SELECT ci.* 
        FROM cart_items ci 
        WHERE ci.customer_id = ?
        ORDER BY ci.added_at DESC
    ");
    $cart_stmt->execute([$customer_id]);
    $cart_items = $cart_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($cart_items)) {
        echo "<script>window.location.href = 'cart.php';</script>";
        exit();
    }
    
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

// 处理订单提交
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    try {
        // 准备订单项目数据
        $order_items = [];
        foreach ($cart_items as $item) {
            $order_items[] = [
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'price' => $item['product_price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['product_price'] * $item['quantity']
            ];
        }
        
        $items_json = json_encode($order_items);
        
        // 插入订单到数据库
        $insert_stmt = $pdo_product->prepare("
            INSERT INTO orders (customer_id, customer_name, customer_email, customer_phone, total_amount, items) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $insert_stmt->execute([
            $customer_id,
            $customer_name,
            $customer_email,
            $customer_phone,
            $total_amount,
            $items_json
        ]);
        
        $order_id = $pdo_product->lastInsertId();
        
        // 清空购物车
        $clear_stmt = $pdo_product->prepare("DELETE FROM cart_items WHERE customer_id = ?");
        $clear_stmt->execute([$customer_id]);
        
        // 存储订单ID到session
        $_SESSION['last_order_id'] = $order_id;
        
        // 使用JavaScript重定向到感谢页面
        echo "<script>window.location.href = 'thankyou.php';</script>";
        exit();
        
    } catch (PDOException $e) {
        $error = "Error placing order: " . $e->getMessage();
    }
}
?>


<style>
    .checkout-container {
        max-width: 1000px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 30px;
    }
    
    .checkout-header {
        grid-column: 1 / -1;
        text-align: center;
        margin-bottom: 30px;
    }
    
    .customer-info {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .info-row {
        display: flex;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .info-label {
        font-weight: bold;
        color: #2c3e50;
        width: 150px;
    }
    
    .info-value {
        flex: 1;
        color: #7f8c8d;
    }
    
    .order-summary {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        position: sticky;
        top: 20px;
    }
    
    .summary-title {
        font-size: 1.3rem;
        font-weight: bold;
        margin-bottom: 20px;
        color: #2c3e50;
        text-align: center;
    }
    
    .order-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .item-name {
        flex: 1;
    }
    
    .item-quantity {
        color: #7f8c8d;
        margin: 0 10px;
    }
    
    .item-price {
        font-weight: bold;
        color: #e74c3c;
    }
    
    .total-row {
        border-top: 2px solid #eee;
        padding-top: 15px;
        margin-top: 15px;
        font-weight: bold;
        font-size: 1.2rem;
        color: #e74c3c;
    }
    
    .btn-place-order {
        background: #27ae60;
        color: white;
        padding: 15px 30px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1.1rem;
        width: 100%;
        margin-top: 20px;
    }
    
    .btn-place-order:hover {
        background: #219a52;
    }
    
    .alert {
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    
    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>

<div class="checkout-container">
    <div class="checkout-header">
        <h1>Checkout</h1>
        <p>Review your order and complete your purchase</p>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="checkout-left">
        <div class="customer-info">
            <h3>Customer Information</h3>
            <div class="info-row">
                <div class="info-label">Name:</div>
                <div class="info-value"><?php echo htmlspecialchars($customer_name); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value"><?php echo htmlspecialchars($customer_email); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value"><?php echo htmlspecialchars($customer_phone); ?></div>
            </div>
        </div>
    </div>
    
    <div class="order-summary">
        <div class="summary-title">Order Summary</div>
        
        <?php foreach ($cart_items as $item): ?>
            <div class="order-item">
                <div class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                <div class="item-details">
                    <span class="item-quantity">Qty: <?php echo $item['quantity']; ?></span>
                    <span class="item-price">$<?php echo number_format($item['product_price'], 2); ?></span>
                </div>
            </div>
        <?php endforeach; ?>
        
        <div class="order-item total-row">
            <div>Total Amount:</div>
            <div>$<?php echo number_format($total_amount, 2); ?></div>
        </div>
        
        <form method="POST" action="">
            <button type="submit" name="place_order" class="btn-place-order">Place Order</button>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>