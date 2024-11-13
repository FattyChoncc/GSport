<?php
session_start();
include '../../connect/mysqlconnect.php'; // Kết nối đến cơ sở dữ liệu

// Xử lý đăng nhập
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['Tendn']) && isset($_POST['Matkhau'])) {
        $tendn = $_POST['Tendn'];
        $matkhau = $_POST['Matkhau'];

        // Kiểm tra tên đăng nhập và trạng thái tài khoản
        $stmt = $mysqli->prepare("SELECT A.idam, A.Tenadmin, T.Matkhau FROM ThongTinAdmin A JOIN TaiKhoanAdmin T ON A.idam = T.idam WHERE T.Tendn = ? AND T.Trangthai = 'hoatdong'");
        
        if (!$stmt) {
            die("Chuẩn bị truy vấn thất bại: " . $mysqli->error);
        }
        
        $stmt->bind_param("s", $tendn);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            // Kiểm tra mật khẩu
            if (password_verify($matkhau, $row['Matkhau'])) {
                $_SESSION['userid'] = $row['idam']; // Lưu ID admin vào session
                $_SESSION['ten_admin'] = $row['Tenadmin']; // Lưu tên admin vào session
                // Thiết lập thông báo thành công
                $_SESSION['success_message'] = "Đăng nhập thành công.";
                header("Location: ../../index.php?action=quanlysan&query=themsan");
            } else {
                // Lưu thông báo lỗi vào session
                $_SESSION['error_message'] = "Mật khẩu không chính xác.";
                header("Location: login.php");
            }
        } else {
            // Lưu thông báo lỗi vào session
            $_SESSION['error_message'] = "Tên đăng nhập không tồn tại hoặc tài khoản chưa được duyệt.";
            header("Location: login.php");
        }
        // Đóng statement sau khi xử lý xong
        $stmt->close();
    } else {
        // Lưu thông báo lỗi vào session
        $_SESSION['error_message'] = "Vui lòng nhập đầy đủ thông tin.";
        header("Location: login.php");
    }
} else {
    header("Location: login.php");
}

$mysqli->close();