<?php
session_start(); // Bắt đầu phiên
session_unset(); // Xóa tất cả các biến phiên
session_destroy(); // Hủy phiên

header("Location: ../pages/dangnhapvadangky/login.php"); // Chuyển hướng về trang đăng nhập
exit();
