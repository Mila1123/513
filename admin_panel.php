<?php
session_start();

// 检查是否是管理员登录
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php'); // 统一跳转到 login.php
    exit();
}

require_once 'header.php'; // 引入头部文件
?>

<h1>Administrator Dashboard</h1>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 30px;">
    <a href="product_manager.php" class="btn">Manage Products</a>
    <a href="manage_discussions.php" class="btn">Manage Discussions</a>
    <!-- 注意链接修改为新的 manage_discussions.php -->
    <a href="manage_list.php" class="btn">View list</a>
    <a href="view_feedback.php" class="btn">View Feedback</a>
    <a href="view_recruitment.php" class="btn">View Applications</a>
</div>

<a href="logout_admin.php" class="btn btn-danger" style="margin-top: 20px;">Logout Admin</a>

<?php require_once 'footer.php'; ?>