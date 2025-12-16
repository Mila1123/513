<a href="admin_panel.php" class="btn">Back</a>
<?php
// 必须在任何输出前启动 session
session_start();

// 检查管理员登录
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit();
}

require_once 'config.php';

// 处理删除请求（必须在 header.php 引入前）
$id = intval($_POST['id'] ?? 0);
if ($id > 0) {
    // 表名含连字符，必须用反引号包裹
    $pdo_customer->prepare("DELETE FROM `feedback-form` WHERE id = ?")->execute([$id]);
    header('Location: view_feedback.php');
    exit();
}

// 查询数据
$stmt = $pdo_customer->query("SELECT * FROM `feedback-form` ORDER BY id DESC");
$feedbacks = $stmt->fetchAll();

// 到这里才安全引入 header（开始 HTML 输出）
require_once 'header.php';
?>

<h2>User Feedback</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Message</th>
            <th>Action</th> <!-- 新增操作列 -->
        </tr>
    </thead>
    <tbody>
        <?php foreach ($feedbacks as $f): ?>
        <tr>
            <td><?= (int)$f['id'] ?></td>
            <td><?= htmlspecialchars($f['name']) ?></td>
            <td><?= htmlspecialchars($f['email']) ?></td>
            <td><?= nl2br(htmlspecialchars($f['message'])) ?></td>
            <td>
                <!-- 删除表单 -->
                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this feedback?')">
                    <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- 文字微调（可选） -->
<a href="admin_panel.php" class="btn">← Back to Admin Panel</a>

<?php require_once 'footer.php'; ?>