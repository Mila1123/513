<?php
// register.php - 客户注册页面
require_once 'config.php';
require_once 'header.php';

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $source = trim($_POST['source'] ?? '');              // ← 改为 source
    $address_line_1 = trim($_POST['address_line_1'] ?? '');

    $error = '';

    if (empty($first_name) || empty($last_name) || empty($email)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    }

    if (empty($error)) {
        try {
            $check_stmt = $pdo_customer->prepare("SELECT id FROM wp_fc_subscribers WHERE email = ?");
            $check_stmt->execute([$email]);

            if ($check_stmt->rowCount() > 0) {
                $error = "Email already exists!";
            } else {
                $hash = md5(uniqid($email . time(), true));
                $insert_stmt = $pdo_customer->prepare("
                    INSERT INTO wp_fc_subscribers 
                    (first_name, last_name, email, phone, city, country, source, address_line_1, 
                     hash, contact_type, status, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'lead', 'subscribed', NOW(), NOW())
                ");

                $insert_stmt->execute([$first_name, $last_name, $email, $phone, $city, $country, $source, $address_line_1,$hash]); // ← 使用 $source

                $success = "Registration successful! You can now log in.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
            error_log("Register Error: " . $e->getMessage());
        }
    }
}
?>

<style>
    /* 保留你的样式 */
    .form-container {
        max-width: 500px;
        margin: 0 auto;
        padding: 30px;
        background: #f8f9fa;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #2c3e50; }
    .form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
    .btn-submit {
        background: #3498db; color: white; padding: 12px 30px; border: none;
        border-radius: 5px; cursor: pointer; font-size: 1rem; width: 100%;
    }
    .btn-submit:hover { background: #2980b9; }
    .alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>

<div class="form-container">
    <h2>Customer Registration</h2>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="first_name">First Name *</label>
            <input type="text" id="first_name" name="first_name" required>
        </div>

        <div class="form-group">
            <label for="last_name">Last Name *</label>
            <input type="text" id="last_name" name="last_name" required>
        </div>

        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="tel" id="phone" name="phone">
        </div>

        <div class="form-group">
            <label for="city">City</label>
            <input type="text" id="city" name="city">
        </div>

        <div class="form-group">
            <label for="country">Country</label>
            <input type="text" id="country" name="country">
        </div>

        <!-- 替换 postal_code 为 source -->
        <div class="form-group">
            <label for="source">Source</label>
            <input type="text" id="source" name="source" maxlength="50"> <!-- 限制长度符合 varchar(50) -->
        </div>

        <div class="form-group">
            <label for="address_line_1">Address Line 1</label>
            <input type="text" id="address_line_1" name="address_line_1">
        </div>

        <button type="submit" class="btn-submit">Subscribe</button>
    </form>

    <p style="text-align: center; margin-top: 20px;">
        Already have an account? <a href="customer_login.php">Login here</a>
    </p>
</div>

<?php require_once 'footer.php'; ?>