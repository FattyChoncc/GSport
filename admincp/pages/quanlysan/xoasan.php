<?php
session_start(); // Khởi tạo session

// Kết nối đến cơ sở dữ liệu
include '../../connect/mysqlconnect.php';

// Kiểm tra xem có ID được gửi đến không
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Truy vấn để lấy đường dẫn hình ảnh trước khi xóa
    $sql = "SELECT Hinhanh FROM QuanLySan WHERE idsan = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hinhanhPath = 'uploads/' . $row['Hinhanh'];

        // Xóa doanh thu liên quan đến lịch đặt của các sân con chỉ cho sân cha hiện tại
        $deleteDoanhThuSql = "DELETE FROM QuanLyDoanhThu 
                              WHERE idlich IN (SELECT idlich FROM LichDat 
                              WHERE id IN (SELECT id FROM Sancon WHERE idsan = ?))";
        $deleteDoanhThuStmt = $mysqli->prepare($deleteDoanhThuSql);
        $deleteDoanhThuStmt->bind_param("s", $id);

        if ($deleteDoanhThuStmt->execute()) {
            // Xóa lịch đặt liên quan đến các sân con chỉ cho sân cha hiện tại
            $deleteLichDatSql = "DELETE FROM LichDat 
                                 WHERE id IN (SELECT id FROM Sancon WHERE idsan = ?)";
            $deleteLichDatStmt = $mysqli->prepare($deleteLichDatSql);
            $deleteLichDatStmt->bind_param("s", $id);
            
            if ($deleteLichDatStmt->execute()) {
                // Xóa các sân con liên quan
                $deleteSanConSql = "DELETE FROM Sancon WHERE idsan = ?";
                $deleteSanConStmt = $mysqli->prepare($deleteSanConSql);
                $deleteSanConStmt->bind_param("s", $id);

                if ($deleteSanConStmt->execute()) {
                    // Tiếp tục xóa dữ liệu trong cơ sở dữ liệu cho sân cha
                    $deleteSql = "DELETE FROM QuanLySan WHERE idsan = ?";
                    $deleteStmt = $mysqli->prepare($deleteSql);
                    $deleteStmt->bind_param("s", $id);

                    if ($deleteStmt->execute()) {
                        // Xóa hình ảnh trong thư mục uploads
                        if (file_exists($hinhanhPath)) {
                            unlink($hinhanhPath);
                        }
                        // Lưu thông báo thành công vào session
                        $_SESSION['success-message'] = "Sân đã được xóa thành công!";
                        header("Location:../../index.php?action=quanlysan&query=themsan");
                        exit();
                    } else {
                        // Lưu thông báo lỗi vào session
                        $_SESSION['error-message'] = "Lỗi khi xóa sân cha: " . $deleteStmt->error;
                        header("Location:../../index.php?action=quanlysan&query=themsan");
                    }

                    $deleteStmt->close();
                } else {
                    // Lưu thông báo lỗi vào session
                    $_SESSION['error-message'] = "Lỗi khi xóa sân con: " . $deleteSanConStmt->error;
                    header("Location:../../index.php?action=quanlysan&query=themsan");
                    exit();
                }

                $deleteSanConStmt->close();
            } else {
                // Lưu thông báo lỗi vào session
                $_SESSION['error-message'] = "Lỗi khi xóa lịch đặt: " . $deleteLichDatStmt->error;
                header("Location:../../index.php?action=quanlysan&query=themsan");
                exit();
            }

            $deleteLichDatStmt->close();
        } else {
            // Lưu thông báo lỗi vào session
            $_SESSION['error-message'] = "Lỗi khi xóa doanh thu: " . $deleteDoanhThuStmt->error;
            header("Location:../../index.php?action=quanlysan&query=themsan");
            exit();
        }

        $deleteDoanhThuStmt->close();
    } else {
        // Lưu thông báo lỗi vào session nếu không tìm thấy hình ảnh
        $_SESSION['error-message'] = "Không tìm thấy hình ảnh để xóa.";
        header("Location:../../index.php?action=quanlysan&query=themsan");
        exit();
    }

    $stmt->close();
}

$mysqli->close();