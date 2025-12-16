[file name]: order_history.php
[file content begin]
<?php
// order_history.php - 订单历史页面
require_once 'config.php';
require_once 'header.php';

// 检查用户是否登录
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: customer_login.php');
    exit();
}

$customer_id = $_SESSION['customer_id'];

try {
    // 获取用户订单历史
    $orders_stmt = $pdo_product->prepare("
        SELECT * FROM orders 
        WHERE customer_id = ? 
        ORDER BY order_date DESC
    ");
    $orders_stmt->execute([$customer_id]);
    $orders = $orders_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error loading order history: " . $e->getMessage();
    $orders = [];
}
?>

<style>
    .order-history-container {
        max-width: 1000px;
        margin: 0 auto;
    }
    
    .page-header {
        text-align: center;
        margin-bottom: 40px;
    }
    
    .order-card {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        border-left: 5px solid #3498db;
    }
    
    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .order-id {
        font-weight: bold;
        font-size: 1.2rem;
        color: #2c3e50;
    }
    
    .order-date {
        color: #7f8c8d;
    }
    
    .order-status {
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 0.9rem;
    }
    
    .status-pending { background: #fff3cd; color: #856404; }
    .status-processing { background: #cce7ff; color: #004085; }
    .status-shipped { background: #d1ecf1; color: #0c5460; }
    .status-delivered { background: #d4edda; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #721c24; }
    
    .order-details {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
    }
    
    .order-items {
        margin-bottom: 15px;
    }
    
    .order-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        padding: 8px;
        background: #f8f9fa;
        border-radius: 5px;
    }
    
    .order-summary {
        text-align: right;
    }
    
    .total-amount {
        font-size: 1.3rem;
        font-weight: bold;
        color: #e74c3c;
        margin: 10px 0;
    }
    
    .no-orders {
        text-align: center;
        padding: 60px 20px;
        color: #7f8c8d;
    }
    
    .no-orders-icon {
        font-size: 4rem;
        margin-bottom: 20px;
    }
</style>

<div class="order-history-container">
    <div class="page-header">
        <h1>Order History</h1>
        <p>View your past orders and their status</p>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (empty($orders)): ?>
        <div class="no-orders">
            <div class="no-orders-icon">📦</div>
            <h2>No Orders Yet</h2>
            <p>You haven't placed any orders yet. Start shopping to see your order history here!</p>
            <a href="product.php" class="btn" style="margin-top: 20px;">Start Shopping</a>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): 
            $items = json_decode($order['items'], true);
        ?>
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <div class="order-id">Order #<?php echo $order['id']; ?></div>
                        <div class="order-date">Placed on <?php echo date('F j, Y g:i A', strtotime($order['order_date'])); ?></div>
                    </div>
                    <div class="order-status status-<?php echo $order['order_status']; ?>">
                        <?php echo ucfirst($order['order_status']); ?>
                    </div>
                </div>
                
                <div class="order-details">
                    <div class="order-items">
                        <h4>Items Ordered:</h4>
                        <?php foreach ($items as $item): ?>
                            <div class="order-item">
                                <div class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                <div class="item-details">
                                    Qty: <?php echo $item['quantity']; ?> × 
                                    $<?php echo number_format($item['price'], 2); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="order-summary">
                        <div><strong>Total Amount:</strong></div>
                        <div class="total-amount">$<?php echo number_format($order['total_amount'], 2); ?></div>
                        <div><strong>Shipping Address:</strong></div>
                        <div><?php echo htmlspecialchars($order['customer_name']); ?></div>
                        <div><?php echo htmlspecialchars($order['customer_email']); ?></div>
                        <div><?php echo htmlspecialchars($order['customer_phone']); ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
[file content end]