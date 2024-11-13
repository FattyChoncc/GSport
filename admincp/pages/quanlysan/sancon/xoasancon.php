<?php
session_start(); // Khởi tạo session

// Kết nối đến cơ sở dữ liệu
include '../../../connect/mysqlconnect.php';

// Kiểm tra xem có ID sân con được gửi không
if (isset($_POST['id'])) {
    // Lấy ID sân con
    $id = $_POST['id'];

    // Truy vấn để lấy tên tệp hình ảnh trước khi xóa
    $sql = "SELECT Hinhanh FROM Sancon WHERE id = ?";
    $stmt = $mysqli->prepare($sql);

    if ($stmt) {
        // Liên kết tham số
        $stmt->bind_param('i', $id);
        
        // Thực hiện câu lệnh
        $stmt->execute();
        $stmt->bind_result($hinhanh);
        $stmt->fetch();
        
        // Đóng câu lệnh
        $stmt->close();

        // Xóa tệp hình ảnh nếu tồn tại
        if ($hinhanh) {
            $file_path = '../../../pages/quanlysan/sancon/uploads/' . $hinhanh;
            if (file_exists($file_path)) {
                unlink($file_path); // Xóa tệp hình ảnh
            }
        }

        // Xóa doanh thu liên quan đến lịch đặt của sân con này
        $deleteDoanhThuSql = "DELETE FROM QuanLyDoanhThu WHERE idlich IN (SELECT idlich FROM LichDat WHERE id = ?)";
        $deleteDoanhThuStmt = $mysqli->prepare($deleteDoanhThuSql);
        if ($deleteDoanhThuStmt) {
            $deleteDoanhThuStmt->bind_param('i', $id);
            $deleteDoanhThuStmt->execute();
            $deleteDoanhThuStmt->close();
        }

        // Xóa lịch đặt liên quan đến sân con này
        $deleteLichDatSql = "DELETE FROM LichDat WHERE id = ?";
        $deleteLichDatStmt = $mysqli->prepare($deleteLichDatSql);
        if ($deleteLichDatStmt) {
            $deleteLichDatStmt->bind_param('i', $id);
            $deleteLichDatStmt->execute();
            $deleteLichDatStmt->close();
        }

        // Xóa sân con
        $deleteSanConSql = "DELETE FROM Sancon WHERE id = ?";
        $deleteSanConStmt = $mysqli->prepare($deleteSanConSql);
        
        // Kiểm tra xem câu lệnh có được chuẩn bị thành công không
        if ($deleteSanConStmt) {
            $deleteSanConStmt->bind_param('i', $id);
            
            // Thực hiện câu lệnh
            if ($deleteSanConStmt->execute()) {
                // Lưu thông báo thành công vào session
                $_SESSION['success-message'] = "Sân con đã được xóa thành công.";
            } else {
                // Lưu thông báo lỗi vào session
                $_SESSION['error-message'] = "Lỗi: Không thể xóa sân con.";
            }

            $deleteSanConStmt->close();
        } else {
            $_SESSION['error-message'] = "Lỗi: Không thể chuẩn bị câu lệnh xóa sân con.";
        }
    } else {
        $_SESSION['error-message'] = "Lỗi: Không thể chuẩn bị câu lệnh.";
    }
} else {
    // Nếu không có ID, lưu thông báo lỗi vào session
    $_SESSION['error-message'] = "ID sân con không hợp lệ.";
}

// Đóng kết nối
$mysqli->close();

// Quay lại trang quản lý sân con
header("Location:../../../index.php?action=quanlysan&query=sancon");
exit();