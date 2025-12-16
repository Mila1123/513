<?php
session_start();

// 检查管理员登录状态
if (!($_SESSION['admin_logged_in'] ?? false)) {
    header('Location: login.php');
    exit();
}

require_once 'config.php';

// 处理删除操作
$id = intval($_POST['id'] ?? 0);
if ($id > 0) {
    $pdo_customer->prepare("DELETE FROM discussion_posts WHERE id = ?")->execute([$id]);
    header('Location: manage_discussions.php');
    exit();
}

// 获取所有讨论（按最新排序）
$stmt = $pdo_customer->query("SELECT * FROM discussion_posts ORDER BY id DESC");
$discussions = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once 'header.php';
?>

<h2>Manage Discussions</h2>

<?php if (!empty($discussions)): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Title</th>
                <th>Content</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($discussions as $d): ?>
                <tr>
                    <td><?= htmlspecialchars($d['id'] ?? '') ?></td>
                    <td><?= htmlspecialchars($d['customer_name'] ?? '') ?></td>
                
                    <td><?= htmlspecialchars($d['title'] ?? '') ?></td>
                    <td><?= htmlspecialchars($d['content'] ?? '') ?></td>
                    <td><?= htmlspecialchars($d['created_at'] ?? '') ?></td>
                    <td>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this discussion?')">
                            <input type="hidden" name="id" value="<?= (int)($d['id'] ?? 0) ?>">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No discussions found.</p>
<?php endif; ?>

<a href="admin_panel.php" class="btn">← Back to Admin Panel</a>

<?php
require_once 'footer.php';
?>