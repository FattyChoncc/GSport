<?php
session_start(); // Khởi động session
include '../../connect/mysqlconnect.php'; // Kết nối đến cơ sở dữ liệu

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    if (!empty($id)) {
        // Bắt đầu transaction
        $mysqli->begin_transaction();

        try {
            // Cập nhật trạng thái tài khoản thành "hoạt động"
            $stmt = $mysqli->prepare("UPDATE TaiKhoanAdmin SET Trangthai = 'hoatdong' WHERE idam = ?");
            $stmt->bind_param("i", $id);

            if (!$stmt->execute()) {
                throw new Exception("Lỗi khi cập nhật trạng thái tài khoản: " . $stmt->error);
            }

            // Commit transaction
            $mysqli->commit();

            // Lưu thông báo vào session
            $_SESSION['success-message'] = "Duyệt tài khoản thành công.";
            header("Location: ../../index.php?action=quanlytaikhoanadmin&query=theodoitaikhoanadmin");
            exit;
        } catch (Exception $e) {
            // Rollback transaction
            $mysqli->rollback();
            $_SESSION['error-message'] = "Có lỗi xảy ra: " . $e->getMessage();
            header("Location: ../../index.php?action=quanlytaikhoanadmin&query=theodoitaikhoanadmin");
            exit;
        } finally {
            // Đóng statement và kết nối
            $stmt->close();
            $mysqli->close();
        }
    } else {
        $_SESSION['error-message'] = "ID không hợp lệ.";
        header("Location: ../../index.php?action=quanlytaikhoanadmin&query=theodoitaikhoanadmin");
        exit;
    }
}