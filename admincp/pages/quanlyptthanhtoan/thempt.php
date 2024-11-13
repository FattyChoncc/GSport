<?php
// Kết nối đến cơ sở dữ liệu
include '../../connect/mysqlconnect.php';

// Bắt đầu phiên làm việc
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy tên phương thức thanh toán từ form
    $paymentMethodName = $_POST['paymentMethodName'];

    // Chuẩn bị truy vấn để thêm phương thức thanh toán
    $sql = "INSERT INTO PTThanhToan (Tenpt) VALUES (?)";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $paymentMethodName);

        // Thực thi truy vấn
        if ($stmt->execute()) {
            // Thêm thông báo thành công vào session
            $_SESSION['success-message'] = "Thêm phương thức thanh toán thành công!";
            header("Location: ../../index.php?action=quanlyptthanhtoan&query=thempt");
        } else {
            // Thêm thông báo lỗi vào session
            $_SESSION['error-message'] = "Có lỗi xảy ra. Vui lòng thử lại.";
            header("Location: ../../index.php?action=quanlyptthanhtoan&query=thempt");
        }
        $stmt->close();
    }

    // Đóng kết nối
    $mysqli->close();
}