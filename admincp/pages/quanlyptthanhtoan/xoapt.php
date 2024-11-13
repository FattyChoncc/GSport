<?php
// Kết nối đến cơ sở dữ liệu
include '../../connect/mysqlconnect.php';

// Bắt đầu phiên làm việc
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy ID phương thức thanh toán từ form
    $idpt = $_POST['idpt'];

    // Chuẩn bị truy vấn để xóa phương thức thanh toán
    $sql = "DELETE FROM PTThanhToan WHERE idpt = ?";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $idpt);

        // Thực thi truy vấn
        if ($stmt->execute()) {
            // Thêm thông báo thành công vào session
            $_SESSION['success-message'] = "Xóa phương thức thanh toán thành công!";
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