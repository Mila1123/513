<?php
session_start();
if (!($_SESSION['admin_logged_in'] ?? false)) {
    http_response_code(403);
    exit('Forbidden');
}

require_once 'config.php';
$id = intval($_POST['id'] ?? 0);
if ($id > 0) {
    $pdo_customer->prepare("DELETE FROM discussion WHERE id = ?")->execute([$id]);
}
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
?>