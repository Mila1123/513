<?php
require_once 'config.php';
require_once 'header.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit();
}

// 处理删除请求（新增）
$id = intval($_POST['id'] ?? 0);
if ($id > 0) {
    $pdo_customer->prepare("DELETE FROM feedback WHERE id = ?")->execute([$id]);
    // 重定向刷新页面，避免重复提交
    header('Location: view_recruitment.php');
    exit();
}

$stmt = $pdo_customer->query("SELECT * FROM feedback WHERE position IS NOT NULL ORDER BY created_at DESC");
$applications = $stmt->fetchAll();
?>

<h2>Job Applications</h2>
<table>
    <thead><tr><th>Name</th><th>Email</th><th>Position</th><th>Message</th><th>Date</th><th>Action</th></tr></thead>
    <tbody>
        <?php foreach ($applications as $a): ?>
        <tr>
            <td><?= htmlspecialchars($a['name']) ?></td>
            <td><?= htmlspecialchars($a['email']) ?></td>
            <td><?= htmlspecialchars($a['position']) ?></td>
            <td><?= nl2br(htmlspecialchars($a['message'])) ?></td>
            <td><?= htmlspecialchars($a['created_at']) ?></td>
            <td>
                <!-- 删除按钮（新增） -->
                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this application?')">
                    <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
                <!-- 如需“修改”功能，通常需跳转到编辑页，但你未提供，暂不加 -->
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- 修改文字（仅改这里） -->
<a href="admin_panel.php" class="btn">← Back to Admin Panel</a>

<?php require_once 'footer.php'; ?>