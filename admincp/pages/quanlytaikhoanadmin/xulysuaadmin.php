<?php
session_start(); // Khởi động session
include '../../connect/mysqlconnect.php'; // Kết nối với cơ sở dữ liệu

// Kiểm tra xem có dữ liệu từ POST request hay không
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ biểu mẫu
    $id = $_POST['id']; // ID admin
    $tenAdmin = $_POST['admin-name']; // Tên admin
    $ngaySinh = $_POST['birthdate']; // Ngày sinh
    $sdt = $_POST['phone']; // Số điện thoại
    $gmail = $_POST['email']; // Gmail

    // Kiểm tra xem các trường có rỗng hay không
    if (empty($tenAdmin) || empty($ngaySinh) || empty($sdt) || empty($gmail)) {
        $_SESSION['error-message'] = "Vui lòng điền tất cả các trường.";
        header("Location: ../../index.php?action=quanlytaikhoanadmin&query=theodoitaikhoanadmin");
        exit;
    }

    // Câu truy vấn để cập nhật thông tin admin
    $sql = "UPDATE ThongTinAdmin SET 
                Tenadmin = ?, 
                Ngaysinh = ?, 
                SDT = ?, 
                Gmail = ? 
            WHERE idam = ?";

    // Chuẩn bị câu lệnh
    $stmt = $mysqli->prepare($sql);
    if ($stmt) {
        // Ràng buộc các tham số
        $stmt->bind_param("sssss", $tenAdmin, $ngaySinh, $sdt, $gmail, $id);

        // Thực hiện câu lệnh
        if ($stmt->execute()) {
            $_SESSION['success-message'] = "Cập nhật thông tin admin thành công.";
            header("Location: ../../index.php?action=quanlytaikhoanadmin&query=theodoitaikhoanadmin");
            exit;
        } else {
            $_SESSION['error-message'] = "Có lỗi xảy ra trong quá trình cập nhật: " . $mysqli->error;
            header("Location: ../../index.php?action=quanlytaikhoanadmin&query=theodoitaikhoanadmin");
        }
    } else {
        $_SESSION['error-message'] = "Có lỗi trong quá trình chuẩn bị câu lệnh: " . $mysqli->error;
        header("Location: ../../index.php?action=quanlytaikhoanadmin&query=theodoitaikhoanadmin");
        exit;
    }

    // Đóng câu lệnh
    $stmt->close();
}

// Đóng kết nối
$mysqli->close();
