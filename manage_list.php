<?php
// 所有逻辑必须在任何输出前完成
session_start();

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit();
}

require_once 'config.php';

// 处理删除请求
$id = intval($_POST['id'] ?? 0);
if ($id > 0) {
    $pdo_customer->prepare("DELETE FROM wp_fc_subscribers WHERE id = ?")->execute([$id]);
    header('Location: manage_list.php');
    exit();
}

$stmt = $pdo_customer->query("SELECT * FROM wp_fc_subscribers ORDER BY created_at DESC");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once 'header.php';
?>

<h2>Manage Subscribers</h2>

<?php if (!empty($items)): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Subscribed At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['id'] ?? '') ?></td>
                <td><?= htmlspecialchars($item['first_name'] ?? '') ?></td>
                <td><?= htmlspecialchars($item['last_name'] ?? '') ?></td>
                <td><?= htmlspecialchars($item['email'] ?? '') ?></td>
                <td><?= htmlspecialchars($item['phone'] ?? '') ?></td>
                <td><?= htmlspecialchars($item['created_at'] ?? '') ?></td>
                <td>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this subscriber?')">
                        <input type="hidden" name="id" value="<?= (int)($item['id'] ?? 0) ?>">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No subscribers found.</p>
<?php endif; ?>

<a href="admin_panel.php" class="btn">← Back to Admin Panel</a>

<?php
require_once 'footer.php';
?>