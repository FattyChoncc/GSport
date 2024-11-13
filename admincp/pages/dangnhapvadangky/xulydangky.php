<?php
// Kết nối đến cơ sở dữ liệu
include '../../connect/mysqlconnect.php';
session_start(); // Khởi động session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $tenAdmin = trim($_POST['Tenadmin']);
    $ngaySinh = $_POST['Ngaysinh'];
    $sdt = trim($_POST['SDT']);
    $gmail = trim($_POST['Gmail']);
    $tenDn = trim($_POST['Tendn']);
    $matkhau = trim($_POST['Matkhau']);

    // Kiểm tra các trường không được để trống
    if (empty($tenAdmin) || empty($ngaySinh) || empty($sdt) || empty($gmail) || empty($tenDn) || empty($matkhau)) {
        $_SESSION['error_message'] = 'Vui lòng điền đầy đủ thông tin.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // Kiểm tra định dạng email
    if (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = 'Email không hợp lệ.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // Kiểm tra số điện thoại
    if (!preg_match('/^\d{10,15}$/', $sdt)) {
        $_SESSION['error_message'] = 'Số điện thoại phải từ 10 đến 15 chữ số.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // Kiểm tra trùng lặp Gmail và SDT
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM ThongTinAdmin WHERE Gmail = ? OR SDT = ?");
    $stmt->bind_param("ss", $gmail, $sdt);
    
    if (!$stmt->execute()) {
        $_SESSION['error_message'] = 'Lỗi khi kiểm tra trùng lặp: ' . $stmt->error;
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $_SESSION['error_message'] = 'Gmail hoặc số điện thoại đã tồn tại.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // Kiểm tra trùng lặp Tendn
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM TaiKhoanAdmin WHERE Tendn = ?");
    $stmt->bind_param("s", $tenDn);
    
    if (!$stmt->execute()) {
        $_SESSION['error_message'] = 'Lỗi khi kiểm tra tên đăng nhập: ' . $stmt->error;
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $_SESSION['error_message'] = 'Tên đăng nhập đã tồn tại.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // Băm mật khẩu trước khi lưu vào cơ sở dữ liệu
    $matkhauHash = password_hash($matkhau, PASSWORD_DEFAULT);

    // Bắt đầu transaction
    $mysqli->begin_transaction();

    try {
        // Insert vào bảng ThongTinAdmin
        $stmt = $mysqli->prepare("INSERT INTO ThongTinAdmin (Tenadmin, Ngaysinh, SDT, Gmail) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $tenAdmin, $ngaySinh, $sdt, $gmail);
        
        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi chèn vào ThongTinAdmin: " . $stmt->error);
        }
        
        // Lấy idam vừa tạo
        $idam = $mysqli->insert_id;  // Lấy idam từ ThongTinAdmin

        // Insert vào bảng TaiKhoanAdmin
        $stmt = $mysqli->prepare("INSERT INTO TaiKhoanAdmin (Tendn, Matkhau, Trangthai, idam) VALUES (?, ?, 'choduyet', ?)");
        $stmt->bind_param("ssi", $tenDn, $matkhauHash, $idam);

        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi chèn vào TaiKhoanAdmin: " . $stmt->error);
        }

        // Commit transaction
        $mysqli->commit();

        // Thiết lập thông báo thành công vào session
        $_SESSION['success_message'] = 'Đăng ký thành công. Chờ phê duyệt từ quản trị viên.';
        
        // Chuyển hướng về trang đăng nhập
        header('Location: login.php');
        exit;

    } catch (Exception $e) {
        // Rollback transaction
        $mysqli->rollback();
        $_SESSION['error_message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    } finally {
        // Đóng statement và kết nối
        if (isset($stmt)) $stmt->close();
        $mysqli->close();
    }
}