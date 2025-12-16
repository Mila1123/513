<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

require_once 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newProducts = [];
    
    // 遍历所有提交的产品数据
    foreach ($_POST['name'] as $i => $name) {
        // 跳过标记为删除的项
        if (isset($_POST['delete'][$i])) {
            continue;
        }
        
        // 跳过完全空白的新行（防止意外保存空产品）
        if (empty(trim($name)) && empty(trim($_POST['category'][$i]))) {
            continue;
        }

        if (!empty(trim($name))) {
            $newProducts[] = [
                'name' => trim($name),
                'category' => trim($_POST['category'][$i]),
                'price' => '$' . number_format(floatval($_POST['price'][$i]), 2),
                'stock' => trim($_POST['stock'][$i]) . ' available',
                'description' => trim($_POST['description'][$i]),
                'image' => trim($_POST['image'][$i])
            ];
        }
    }

    file_put_contents('products.json', json_encode($newProducts, JSON_PRETTY_PRINT));
    $message = "Products updated successfully!";
    $products = $newProducts; // 刷新显示
} else {
    // 初始加载
    $products = json_decode(file_get_contents('products.json'), true);
    if (!is_array($products)) $products = [];
}

// 👇 到这里才引入 header.php
require_once 'header.php';
?>

<h2>Manage Products</h2>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<form method="POST">
    <table>
        <thead>
            <tr>
                <th>Delete?</th> <!-- 新增列 -->
                <th>Name</th>
                <th>Category</th>
                <th>Price (Number)</th>
                <th>Stock Text</th>
                <th>Description</th>
                <th>Image Path</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $index => $p): ?>
            <tr>
                <td><input type="checkbox" name="delete[<?php echo $index; ?>]" value="1"></td>
                <td><input name="name[]" value="<?= htmlspecialchars($p['name']) ?>" required></td>
                <td><input name="category[]" value="<?= htmlspecialchars($p['category']) ?>" required></td>
                <td><input type="number" step="0.01" name="price[]" value="<?= htmlspecialchars(trim($p['price'], '$')) ?>" required></td>
                <td><input name="stock[]" value="<?= htmlspecialchars(explode(' ', $p['stock'])[0]) ?>" required></td>
                <td><input name="description[]" value="<?= htmlspecialchars($p['description']) ?>" required></td>
                <td><input name="image[]" value="<?= htmlspecialchars($p['image']) ?>" required></td>
            </tr>
            <?php endforeach; ?>
            
            <!-- 新增空行用于添加新产品 -->
            <tr style="background-color: #f9f9f9;">
                <td></td>
                <td><input name="name[]" placeholder="New product"></td>
                <td><input name="category[]" placeholder="Category"></td>
                <td><input type="number" step="0.01" name="price[]" placeholder="0.00"></td>
                <td><input name="stock[]" placeholder="Stock qty"></td>
                <td><input name="description[]" placeholder="Description"></td>
                <td><input name="image[]" placeholder="image.jpg"></td>
            </tr>
        </tbody>
    </table>
    
    <button type="submit" class="btn" style="margin-top: 20px;">Save All Changes</button>
</form>

<a href="admin_panel.php" class="btn btn-secondary">← Back to Admin Panel</a>

<?php require_once 'footer.php'; ?>