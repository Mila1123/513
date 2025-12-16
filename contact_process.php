<?php
// contact_process.php - 联系表单处理
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    
    // 这里可以添加邮件发送或数据库存储逻辑
    // 例如：保存到数据库或发送电子邮件
    
    // 重定向回联系页面并显示成功消息
    header('Location: contact.php?status=success');
    exit();
} else {
    header('Location: contact.php');
    exit();
}
?>