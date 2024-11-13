<?php
session_start(); // Bắt đầu session

// Hủy tất cả các session
session_unset(); 

// Chuyển hướng về trang đăng nhập hoặc trang chủ
$_SESSION['success-message'] = 'Đăng xuất thành công';
header('Location:../index.php?action=trangchu');
exit();