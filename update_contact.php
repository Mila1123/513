<?php
// update_contact.php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.php');
    exit;
}

$id = $_POST['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die('Invalid ID');
}

// 获取所有提交的字段
$fields = [];
foreach ($_POST as $key => $value) {
    if ($key === 'id') continue; // 跳过 id

    // 只允许非空字符串、数字等有效值
    if (empty($value)) {
        // 对于整数字段（如 user_id, phone 等），如果为空，可以设为 NULL 或跳过
        // 我们选择：跳过空值字段（不更新）
        continue;
    }

    // 特殊处理：如果是数字字段，确保是整数或浮点数
    if (is_numeric($value)) {
        $fields[$key] = (int)$value; // 强制转为整数（适用于 INT 类型）
    } else {
        $fields[$key] = $value; // 字符串直接保留
    }
}

if (empty($fields)) {
    die('No valid data to update.');
}

// 构建 SET 子句
$setParts = [];
$params = [];

foreach ($fields as $field => $value) {
    $setParts[] = "`$field` = ?";
    $params[] = $value;
}
$params[] = $id;

$sql = "UPDATE wp_fc_subscribers SET " . implode(', ', $setParts) . " WHERE id = ?";

try {
    $stmt = $pdo_customer->prepare($sql);
    $stmt->execute($params);

    header('Location: contact.php?updated=1');
    exit;
} catch (PDOException $e) {
    error_log("Update failed: " . $e->getMessage());
    die('Update failed: ' . htmlspecialchars($e->getMessage()));
}
?>