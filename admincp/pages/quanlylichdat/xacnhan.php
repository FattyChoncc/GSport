<?php
// Kết nối đến cơ sở dữ liệu
include '../../connect/mysqlconnect.php';

// Bắt đầu phiên làm việc để sử dụng $_SESSION
session_start();

// Kiểm tra nếu có dữ liệu POST gửi lên
if (isset($_POST['idlich'])) {
    $idlich = $_POST['idlich'];

    // Câu lệnh SQL để cập nhật trạng thái
    $sql = "UPDATE LichDat SET Trangthai = 'daxacnhan' WHERE idlich = ?";

    // Chuẩn bị câu truy vấn
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $idlich);

    // Thực thi câu truy vấn
    if ($stmt->execute()) {
        // Thành công: Tạo thông báo thành công trong session
        $_SESSION['success-message'] = "Lịch đặt đã được xác nhận thành công!";
        header("Location: ../../index.php?action=quanlylichdat&query=xacnhanlich");
    } else {
        // Thất bại: Tạo thông báo lỗi trong session
        $_SESSION['error-message'] = "Lỗi khi xác nhận lịch đặt!";
        header("Location: ../../index.php?action=quanlylichdat&query=xacnhanlich&error=Lỗi khi xác nhận lịch đặt");
    }

    // Đóng câu lệnh và kết nối
    $stmt->close();
    $mysqli->close();
} else {
    // Nếu không có ID lịch, tạo thông báo lỗi trong session và chuyển hướng
    $_SESSION['error-message'] = "Không tìm thấy ID lịch!";
    header("Location: ../../index.php?action=quanlylichdat&query=xacnhanlich&error=Không tìm thấy ID lịch");
}

// Chấm dứt script
exit();