<?php
session_start(); // Bắt đầu session

// Kết nối cơ sở dữ liệu
include('../admincp/connect/mysqlconnect.php'); 

// Kiểm tra nếu form được submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy thông tin từ form đăng nhập
    $tendn = $_POST['Tendn'];
    $matkhau = $_POST['Matkhau'];
    
    // Truy vấn kiểm tra tài khoản và mật khẩu
    $sql = "SELECT * FROM TaiKhoanKhachHang WHERE Tendn = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $tendn);  // Ràng buộc tham số
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Kiểm tra nếu tài khoản tồn tại
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Kiểm tra mật khẩu
        if (password_verify($matkhau, $user['Matkhau'])) {
            // Lưu thông tin người dùng vào session
            $_SESSION['idkh'] = $user['idkh']; // ID khách hàng
            $_SESSION['Tenkh'] = $user['Tenkh']; // Tên khách hàng
            $_SESSION['email'] = $user['Gmail']; // Gmail khách hàng
            
            // Đăng nhập thành công và thiết lập thông báo chào mừng
            $_SESSION['success-message'] = 'Đăng nhập thành công! Chào mừng bạn';
            
            // Chuyển hướng về trang chủ hoặc trang người dùng
            header('Location: ../index.php?action=datsan');
            exit();
        } else {
            // Mật khẩu không đúng
            $_SESSION['error-message'] = 'Mật khẩu không đúng';
            header('Location: ../index.php?action=datsan');
            exit();
        }
    } else {
        // Tài khoản không tồn tại
        $_SESSION['error-message'] = 'Tài khoản không tồn tại';
        header('Location: ../index.php?action=datsan');
        exit();
    }
}