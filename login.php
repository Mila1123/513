<?php
// customer_login.php - 客户登录页面
require_once 'config.php';
session_start();

// 👇 新增：声明变量避免 undefined 错误
$admin_error = '';
$mode = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    
    // 判断是客户登录还是管理员登录
    if (isset($_POST['login_admin'])) {
        // 👇 管理员登录逻辑
        $mode = 'admin';
        if ($email === 'admin@123.com' && $phone === '123123') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = $email;
            unset($_SESSION['logged_in']); // 清除客户会话
            header('Location: admin_panel.php');
            exit();
        } else {
            $admin_error = "Invalid admin credentials.";
        }
    } else {
        // 👇 原有客户登录逻辑
        try {
            $stmt = $pdo_customer->prepare("
                SELECT id, first_name, last_name, email, phone 
                FROM wpot_fc_subscribers 
                WHERE email = ? AND phone = ? AND status = 'subscribed'
            ");
            $stmt->execute([$email, $phone]);
            $user = $stmt->fetch();
            
            if ($user) {
                $_SESSION['customer_id'] = $user['id'];
                $_SESSION['customer_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['customer_email'] = $user['email'];
                $_SESSION['customer_phone'] = $user['phone'];
                $_SESSION['logged_in'] = true;
                unset($_SESSION['admin_logged_in']);
                header('Location: product.php');
                exit();
            } else {
                $error = "Invalid email or phone number!";
            }
        } catch (PDOException $e) {
            $error = "Login failed: " . $e->getMessage();
        }
    }
}

require_once 'header.php';
?>

<style>
    .form-container {
        max-width: 500px;
        margin: 0 auto;
        padding: 30px;
        background: #f8f9fa;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #2c3e50;
    }
    
    .form-group input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 1rem;
    }
    
    .btn-submit {
        background: #3498db;
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
        width: 100%;
    }
    
    .btn-admin {
        background: #007bff;
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
        width: 100%;
        margin-top: 10px;
    }
    
    .btn-submit:hover, .btn-admin:hover {
        opacity: 0.9;
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

<!-- 客户登录 -->
<div class="form-container">
    <h2>Customer Login</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="tel" id="phone" name="phone" required>
        </div>
        
        <button type="submit" class="btn-submit">Login as Customer</button>
    </form>
    
    <p style="text-align: center; margin-top: 20px;">
        Don't have an account? <a href="register.php">Register here</a>
    </p>
</div>

<!-- 管理员登录（修复后的正确 HTML） -->
<div class="form-container">
    <h2>Admin Login</h2>
    
    <?php if ($admin_error && $mode === 'admin'): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($admin_error); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="admin_email">Admin Email</label>
            <input type="email" id="admin_email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="admin_phone">Admin Phone</label>
            <input type="text" id="admin_phone" name="phone" required>
        </div>
        
        <button type="submit" name="login_admin" class="btn-admin">Login as Admin</button>
    </form>
</div>

<?php require_once 'footer.php'; ?>