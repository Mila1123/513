<?php
// 数据库配置 - 使用宝塔中创建的用户
$host = 'localhost'; // 或服务器IP（如果远程连接）
$user = '47_110_70_30'; // ← 从宝塔复制的用户名
$pwd = 'twPhCr21zd';   // ← 从宝塔复制的密码

// 客户数据库配置
$customer_db = '47_110_70_30';
$customer_dsn = "mysql:host=$host;dbname=$customer_db;charset=utf8mb4";

// 产品数据库配置
$product_db = '47_110_70_30';
$product_dsn = "mysql:host=$host;dbname=$product_db;charset=utf8mb4";

try {
    // 连接客户数据库
    $pdo_customer = new PDO($customer_dsn, $user, $pwd);
    $pdo_customer->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Customer database connection failed: " . $e->getMessage() . "<br>";
}

try {
    // 连接产品数据库
    $pdo_product = new PDO($product_dsn, $user, $pwd);
    $pdo_product->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    try {
        // 如果产品数据库不存在，尝试创建
        $pdo_temp = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pwd);
        $pdo_temp->exec("CREATE DATABASE IF NOT EXISTS `$product_db`");
        
        // 重新连接
        $pdo_product = new PDO($product_dsn, $user, $pwd);
        $pdo_product->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e2) {
        die("Database connection failed: " . $e2->getMessage());
    }
}

// 向后兼容：设置 $pdo 指向客户数据库
$pdo = $pdo_customer;

// 设置基础路径
$base_path = '';

// 启动会话
session_start();
?>