<?php
$current_page = basename($_SERVER['PHP_SELF']);

// 自动检测项目根路径（假设项目在子目录，且所有入口文件在同一级）
$script_dir = dirname($_SERVER['SCRIPT_NAME']);
if ($script_dir === '/' || $script_dir === '\\') {
    $base_path = '/';
} else {
    $base_path = $script_dir . '/';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Burger Haven</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: #2c3e50;
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .header-content { 
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .logo-icon {
            margin-right: 10px;
            font-size: 1.8rem;
        }
        
        nav ul {
            display: flex;
            list-style: none;
            align-items: center;
        }
        
        nav ul li {
            margin-left: 15px;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 3px;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
        }
        
        nav ul li a:hover, nav ul li a.active {
            background-color: #34495e;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-info {
            color: #ecf0f1;
            font-size: 0.9rem;
        }
        
        .cart-count {
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.8rem;
            margin-left: 5px;
        }
        
        main {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-top: 20px;
            min-height: 500px;
        }
        
        h1, h2, h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        table tr:hover {
            background-color: #f9f9f9;
        }
        
        .btn {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .btn-secondary {
            background-color: #95a5a6;
        }
        
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        
        footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px 0;
            color: #7f8c8d;
            border-top: 1px solid #eee;
        }
        
        .subscriber-count {
            margin: 15px 0;
            font-weight: bold;
            color: #2c3e50;
        }
        
        hr {
            margin: 20px 0;
            border: 0;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <div class="logo">
                <div class="logo-icon">
                     <img src="<?php echo $base_path; ?>images/burger.png" style="width:50px; height:auto; vertical-align:middle;" alt="Burger Haven Logo">
                </div>
                <div>Burger Haven</div>
            </div>
            <nav>
                <ul>
                    <li><a href="http://47.110.70.30/513/week11/index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="http://47.110.70.30/513/week11/product.php" class="<?php echo $current_page == 'product.php' ? 'active' : ''; ?>">Products</a></li>
                    <li><a href="http://47.110.70.30/discussion/" class="<?php echo $current_page == 'discussion.php' ? 'active' : ''; ?>">Discussion</a></li>
                    <li><a href="http://47.110.70.30/513/week11/list.php" class="<?php echo $current_page == 'list.php' ? 'active' : ''; ?>">List</a></li>
                    
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                        <li><a href="http://47.110.70.30/513/week11/cart.php" class="<?php echo $current_page == 'cart.php' ? 'active' : ''; ?>">
                            🛒 Cart
                            <?php
                            // 获取购物车商品数量
                            if (isset($pdo_product)) {
                                try {
                                    $count_stmt = $pdo_product->prepare("SELECT SUM(quantity) as total FROM cart_items WHERE customer_id = ?");
                                    $count_stmt->execute([$_SESSION['customer_id']]);
                                    $cart_count = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
                                    if ($cart_count > 0) {
                                        echo '<span class="cart-count">' . $cart_count . '</span>';
                                    }
                                } catch (PDOException $e) {
                                    // 忽略错误
                                }
                            }
                            ?>
                        </a></li>
                        <li><a href="http://47.110.70.30/513/week11/order_history.php" class="<?php echo $current_page == 'order_history.php' ? 'active' : ''; ?>">Orders</a></li>
                        <li class="user-info">
                            Welcome, <?php echo $_SESSION['customer_name']; ?>
                        </li>
                        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
    <li><a href="http://47.110.70.30/513/week11/admin_panel.php" class="<?php echo $current_page == 'admin_panel.php' ? 'active' : ''; ?>">Admin Panel</a></li>
<?php endif; ?>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="http://47.110.70.30/513/week11/login.php">Login</a></li>
                        <li><a href="http://47.110.70.30/register/">Register</a></li>
                        <li><a href="http://47.110.70.30/513/week11/recruitment.php">Recruitment</a></li>
                        <li><a href="http://47.110.70.30/51-2/">Feedback</a></li>
                        <li><a href="http://47.110.70.30/513/week11/about.php">About Us</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    
    <div class="container">
        <main>