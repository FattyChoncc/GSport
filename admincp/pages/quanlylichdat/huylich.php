<?php
// Kết nối đến cơ sở dữ liệu
include '../../connect/mysqlconnect.php';

// Bắt đầu phiên làm việc để sử dụng $_SESSION
session_start();

// Kiểm tra nếu có dữ liệu POST gửi lên
if (isset($_POST['idlich'])) {
    // Lấy dữ liệu từ POST
    $idlich = $_POST['idlich'];

    // Truy vấn để lấy thông tin khách hàng (giả sử có cột `idkh` trong bảng `LichDat`)
    $sqlGetCustomer = "SELECT idkh FROM LichDat WHERE idlich = ?";
    if ($stmt = $mysqli->prepare($sqlGetCustomer)) {
        $stmt->bind_param("s", $idlich);
        $stmt->execute();
        $stmt->bind_result($idkh);
        $stmt->fetch();
        $stmt->close();
        
        // Truy vấn lấy tên khách hàng từ bảng `ThongTinKhachHang`
        $sqlGetCustomerName = "SELECT Tenkhachhang FROM ThongTinKhachHang WHERE idkh = ?";
        if ($stmt = $mysqli->prepare($sqlGetCustomerName)) {
            $stmt->bind_param("s", $idkh);
            $stmt->execute();
            $stmt->bind_result($customerName);
            $stmt->fetch();
            $stmt->close();
        }
    }

    // Câu lệnh SQL để cập nhật trạng thái thành 'dahuy'
    $sql = "UPDATE LichDat SET Trangthai = 'dahuy' WHERE idlich = ?";

    // Chuẩn bị câu truy vấn
    if ($stmt = $mysqli->prepare($sql)) {
        // Ràng buộc tham số
        $stmt->bind_param("s", $idlich); // Giả sử idlich là varchar

        // Thực thi câu truy vấn
        if ($stmt->execute()) {
            // Thành công: Tạo thông báo thành công và chuyển hướng
            $_SESSION['success-message'] = "Cập nhật trạng thái lịch thành công của khách hàng: " . htmlspecialchars($customerName);
            header("Location: ../../index.php?action=quanlylichdat&query=xacnhanlich");
        } else {
            // Thất bại: Tạo thông báo lỗi và chuyển hướng
            $_SESSION['error-message'] = "Có lỗi xảy ra khi cập nhật trạng thái lịch.";
            header("Location: ../../index.php?action=quanlylichdat&query=xacnhanlich");
        }

        // Đóng câu lệnh
        $stmt->close();
    } else {
        // Nếu không thể chuẩn bị câu truy vấn, tạo thông báo lỗi và chuyển hướng
        $_SESSION['error-message'] = "Không thể chuẩn bị câu truy vấn.";
        header("Location: ../../index.php?action=quanlylichdat&query=xacnhanlich");
    }

    // Đóng kết nối
    $mysqli->close();
} else {
    // Nếu không có ID lịch, tạo thông báo lỗi và chuyển hướng
    $_SESSION['error-message'] = "ID lịch không hợp lệ.";
    header("Location: ../../index.php?action=quanlylichdat&query=xacnhanlich");
}

// Chấm dứt script
exit();