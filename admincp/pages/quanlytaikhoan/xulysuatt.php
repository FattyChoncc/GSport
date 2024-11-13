<?php
session_start(); // Khởi động session
include '../../connect/mysqlconnect.php'; // Kết nối cơ sở dữ liệu

// Kiểm tra xem có id khách hàng được gửi từ form không
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $idkh = $_POST['idkh'];
    $tenkh = $_POST['tenkh'];
    $ngaysinh = $_POST['ngaysinh'];
    $sdt = $_POST['sdt'];
    $gmail = $_POST['gmail'];
    $loaikh = $_POST['loaikh'];

    // Kiểm tra dữ liệu hợp lệ (có thể thêm các kiểm tra khác nếu cần)
    if (empty($tenkh) || empty($ngaysinh) || empty($sdt) || empty($gmail) || empty($loaikh)) {
        $_SESSION['error-message'] = "Vui lòng điền đầy đủ thông tin.";
        header("Location: ../../index.php?action=quanlytaikhoan&query=theodoitaikhoan");
        exit;
    }

    // Cập nhật thông tin trong bảng ThongTinKhachHang
    $sql1 = "UPDATE ThongTinKhachHang SET Tenkh = ?, Ngaysinh = ?, SDT = ?, Gmail = ? WHERE idkh = ?";
    $stmt1 = $mysqli->prepare($sql1);
    $stmt1->bind_param("sssss", $tenkh, $ngaysinh, $sdt, $gmail, $idkh);

    // Cập nhật thông tin trong bảng QuanLyTaiKhoan
    $sql2 = "UPDATE QuanLyTaiKhoan SET Loaikh = ? WHERE idkh = ?";
    $stmt2 = $mysqli->prepare($sql2);
    $stmt2->bind_param("ss", $loaikh, $idkh);

    // Thực hiện các truy vấn
    if ($stmt1->execute() && $stmt2->execute()) {
        $_SESSION['success-message'] = "Cập nhật thông tin khách hàng thành công.";
    } else {
        $_SESSION['error-message'] = "Có lỗi xảy ra khi cập nhật thông tin: " . $mysqli->error;
    }

    // Đóng các statement
    $stmt1->close();
    $stmt2->close();
    $mysqli->close();

    // Chuyển hướng về trang đích
    header("Location: ../../index.php?action=quanlytaikhoan&query=theodoitaikhoan");
    exit();
} else {
    $_SESSION['error-message'] = "Yêu cầu không hợp lệ.";
    header("Location: ../../index.php?action=quanlytaikhoan&query=theodoitaikhoan");
    exit();
}