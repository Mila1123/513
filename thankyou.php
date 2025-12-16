<?php
// thankyou.php - 订单确认页面
require_once 'config.php';
require_once 'header.php';

// 检查用户是否登录
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: customer_login.php');
    exit();
}

// 获取最后订单信息
$order_id = $_SESSION['last_order_id'] ?? null;

if (!$order_id) {
    header('Location: product.php');
    exit();
}

try {
    $order_stmt = $pdo_product->prepare("SELECT * FROM orders WHERE id = ? AND customer_id = ?");
    $order_stmt->execute([$order_id, $_SESSION['customer_id']]);
    $order = $order_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        header('Location: product.php');
        exit();
    }
    
    $items = json_decode($order['items'], true);
} catch (PDOException $e) {
    $error = "Error loading order details: " . $e->getMessage();
}
?>

<style>
    .thankyou-container {
        max-width: 800px;
        margin: 0 auto;
        text-align: center;
        padding: 40px 20px;
    }
    
    .success-icon {
        font-size: 5rem;
        color: #27ae60;
        margin-bottom: 20px;
    }
    
    .order-details {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin: 30px 0;
        text-align: left;
    }
    
    .detail-row {
        display: flex;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .detail-label {
        font-weight: bold;
        color: #2c3e50;
        width: 200px;
    }
    
    .detail-value {
        flex: 1;
        color: #7f8c8d;
    }
    
    .order-items {
        margin-top: 25px;
    }
    
    .order-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 5px;
    }
    
    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 30px;
    }
    
    .btn-primary {
        background: #3498db;
        color: white;
        padding: 12px 25px;
        text-decoration: none;
        border-radius: 5px;
        display: inline-block;
    }
    
    .btn-secondary {
        background: #95a5a6;
        color: white;
        padding: 12px 25px;
        text-decoration: none;
        border-radius: 5px;
        display: inline-block;
    }
</style>

<div class="thankyou-container">
    <div class="success-icon">✅</div>
    <h1>Thank You for Your Order!</h1>
    <p>Your order has been placed successfully and is being processed.</p>
    
    <?php if (isset($order)): ?>
        <div class="order-details">
            <h2>Order Details</h2>
            
            <div class="detail-row">
                <div class="detail-label">Order ID:</div>
                <div class="detail-value">#<?php echo $order['id']; ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Order Date:</div>
                <div class="detail-value"><?php echo date('F j, Y g:i A', strtotime($order['order_date'])); ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Customer Name:</div>
                <div class="detail-value"><?php echo htmlspecialchars($order['customer_name']); ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Email:</div>
                <div class="detail-value"><?php echo htmlspecialchars($order['customer_email']); ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Phone:</div>
                <div class="detail-value"><?php echo htmlspecialchars($order['customer_phone']); ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Order Status:</div>
                <div class="detail-value">
                    <span style="color: #f39c12; font-weight: bold;"><?php echo ucfirst($order['order_status']); ?></span>
                </div>
            </div>
            
            <div class="order-items">
                <h3>Order Items</h3>
                <?php foreach ($items as $item): ?>
                    <div class="order-item">
                        <div class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                        <div class="item-details">
                            Qty: <?php echo $item['quantity']; ?> × 
                            $<?php echo number_format($item['price'], 2); ?> = 
                            $<?php echo number_format($item['subtotal'], 2); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="order-item" style="background: #e8f5e8; font-weight: bold; margin-top: 15px;">
                    <div>Total Amount:</div>
                    <div>$<?php echo number_format($order['total_amount'], 2); ?></div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="action-buttons">
        <a href="product.php" class="btn-primary">Continue Shopping</a>
        <a href="order_history.php" class="btn-secondary">View Order History</a>
    </div>
</div>

<?php 
// 清除最后订单ID
unset($_SESSION['last_order_id']);
require_once 'footer.php'; 
?>
