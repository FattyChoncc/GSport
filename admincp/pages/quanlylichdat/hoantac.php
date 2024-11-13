<?php
// Kết nối đến cơ sở dữ liệu
include '../../connect/mysqlconnect.php';

// Bắt đầu phiên làm việc để sử dụng $_SESSION
session_start();

// Kiểm tra nếu có dữ liệu POST gửi lên
if (isset($_POST['idlich'])) {
    // Lấy dữ liệu từ POST
    $idlich = $_POST['idlich'];

    // Câu lệnh SQL để cập nhật trạng thái thành 'choxuly'
    $sql = "UPDATE LichDat SET Trangthai = 'choxuly' WHERE idlich = ?";

    // Chuẩn bị câu truy vấn
    if ($stmt = $mysqli->prepare($sql)) {
        // Ràng buộc tham số
        $stmt->bind_param("s", $idlich); // Giả sử idlich là varchar

        // Thực thi câu truy vấn
        if ($stmt->execute()) {
            // Thành công: Tạo thông báo thành công trong session
            $_SESSION['success-message'] = "Hoàn tác lịch thành công!";
            header("Location: ../../index.php?action=quanlylichdat&query=dahuy");
        } else {
            // Thất bại: Tạo thông báo lỗi trong session
            $_SESSION['error-message'] = "Có lỗi xảy ra khi cập nhật trạng thái lịch!";
            header("Location: ../../index.php?action=quanlylichdat&query=dahuy");
        }

        // Đóng câu lệnh
        $stmt->close();
    } else {
        // Nếu không thể chuẩn bị câu truy vấn, tạo thông báo lỗi trong session
        $_SESSION['error-message'] = "Không thể chuẩn bị câu truy vấn.";
        header("Location: ../../index.php?action=quanlylichdat&query=dahuy");
    }

    // Đóng kết nối
    $mysqli->close();
} else {
    // Nếu không có ID lịch, tạo thông báo lỗi trong session
    $_SESSION['error-message'] = "ID lịch không hợp lệ.";
    header("Location: ../../index.php?action=quanlylichdat&query=dahuy");
}

// Chấm dứt script
exit();
