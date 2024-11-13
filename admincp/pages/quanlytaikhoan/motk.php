<?php
session_start(); // Khởi động session
include '../../connect/mysqlconnect.php'; // Kết nối cơ sở dữ liệu

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idkh = $_POST['id'];

    // Truy vấn cập nhật trạng thái tài khoản thành 'hoatdong'
    $sql = "UPDATE QuanLyTaiKhoan SET Trangthai = 'hoatdong' WHERE idkh = ?";
    
    // Chuẩn bị và thực thi truy vấn
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $idkh);
        
        if ($stmt->execute()) {
            $_SESSION['success-message'] = "Kích hoạt tài khoản thành công!";
        } else {
            $_SESSION['error-message'] = "Lỗi: Không thể kích hoạt tài khoản.";
        }
        $stmt->close();
    }

    $mysqli->close();
    header("Location: ../../index.php?action=quanlytaikhoan&query=theodoitaikhoan"); // Quay lại trang danh sách
    exit();
}