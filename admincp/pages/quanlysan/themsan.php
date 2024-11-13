<?php
// Bắt đầu session
session_start();

// Kết nối đến cơ sở dữ liệu
include '../../connect/mysqlconnect.php';

// Kiểm tra xem có dữ liệu được gửi đến không
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ biểu mẫu
    $tensan = $_POST['Tensan'];
    $tgmo = $_POST['TGmo'];
    $tgdong = $_POST['TGdong'];
    $diachi = $_POST['Diachi'];
    $chitiet = $_POST['Chitiet'];

    // Kiểm tra xem tên sân đã tồn tại chưa
    $checkSql = "SELECT COUNT(*) FROM QuanLySan WHERE Tensan = ?";
    $stmt_check = $mysqli->prepare($checkSql);
    $stmt_check->bind_param("s", $tensan);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($count > 0) {
        // Nếu tên sân đã tồn tại, lưu thông báo vào session
        $_SESSION['error-message'] = 'Tên sân đã tồn tại. Vui lòng chọn tên khác.';
        header('Location: ' . $_SERVER['HTTP_REFERER']); // Quay về trang trước
        exit();
    } else {
        // Xử lý hình ảnh
        if (isset($_FILES['Hinhanh']) && $_FILES['Hinhanh']['error'] == 0) {
            $hinhanhTen = time() . '_' . basename($_FILES['Hinhanh']['name']);
            $hinhanh = 'uploads/' . $hinhanhTen;

            // Di chuyển tệp hình ảnh vào thư mục uploads
            if (move_uploaded_file($_FILES['Hinhanh']['tmp_name'], $hinhanh)) {
                // Chuẩn bị truy vấn SQL
                $sql = "INSERT INTO QuanLySan (Tensan, Hinhanh, TGmo, TGdong, Diachi, Chitiet) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                
                // Tạo statement
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("ssssss", $tensan, $hinhanhTen, $tgmo, $tgdong, $diachi, $chitiet);

                // Thực hiện truy vấn
                if ($stmt->execute()) {
                    $_SESSION['success-message'] = 'Thêm sân thành công!';
                    header('Location: ../../index.php?action=quanlysan&query=themsan'); // Chuyển hướng về trang quản lý sân
                    exit();
                } else {
                    $_SESSION['error-message'] = "Lỗi: " . $stmt->error;
                }

                $stmt->close();
            } else {
                $_SESSION['error-message'] = 'Có lỗi khi tải hình ảnh lên.';
            }
        } else {
            $_SESSION['error-message'] = 'Hình ảnh không hợp lệ hoặc không được chọn.';
        }
    }
}

$mysqli->close();
