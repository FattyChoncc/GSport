<?php
session_start(); // Khởi tạo session

// Kết nối đến cơ sở dữ liệu
include '../../../connect/mysqlconnect.php';

// Kiểm tra nếu ID sân con được gửi qua POST
if (isset($_POST['id'])) {
    $idSanCon = $_POST['id'];

    // Truy vấn để cập nhật trạng thái của sân con thành 'hoatdong'
    $sql = "UPDATE Sancon SET Trangthai = 'hoatdong' WHERE id = ?";
    $stmt = $mysqli->prepare($sql);

    // Kiểm tra xem câu lệnh chuẩn bị có thành công không
    if ($stmt === false) {
        $_SESSION['error-message'] = 'Lỗi prepare: ' . $mysqli->error;
        header("Location:../../../index.php?action=quanlysan&query=sancon");
        exit();
    }

    $stmt->bind_param("i", $idSanCon);
    
    // Thực thi câu lệnh và kiểm tra nếu thành công
    if ($stmt->execute()) {
        $_SESSION['success-message'] = "Đã mở sân.";
    } else {
        $_SESSION['error-message'] = "Lỗi khi cập nhật trạng thái sân: " . $stmt->error;
    }

    $stmt->close();
} else {
    $_SESSION['error-message'] = "ID sân con không hợp lệ.";
}

$mysqli->close();

// Quay lại trang quản lý sân con
header("Location:../../../index.php?action=quanlysan&query=sancon");
exit();
