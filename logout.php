<?php
// logout.php - 用户登出
session_start();
session_destroy();
header('Location: index.php');
exit();
session_start();
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_email']);
session_destroy();
header('Location: index.php');
exit();
?>