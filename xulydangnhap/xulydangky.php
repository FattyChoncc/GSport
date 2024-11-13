<?php
// Kết nối đến cơ sở dữ liệu
include '../admincp/connect/mysqlconnect.php';
session_start(); // Khởi động session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $tenkh = trim($_POST['Tenkh']);
    $ngaySinh = $_POST['Ngaysinh'];
    $sdt = trim($_POST['SDT']);
    $gmail = trim($_POST['Gmail']);
    $tendn = trim($_POST['Tendn']);
    $matkhau = trim($_POST['Matkhau']);

    // Kiểm tra các trường không được để trống
    if (empty($tenkh) || empty($ngaySinh) || empty($sdt) || empty($gmail) || empty($tendn) || empty($matkhau)) {
        $_SESSION['error-message'] = 'Vui lòng điền đầy đủ thông tin.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // Kiểm tra định dạng email
    if (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error-message'] = 'Email không hợp lệ.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // Kiểm tra số điện thoại
    if (!preg_match('/^\d{10,15}$/', $sdt)) {
        $_SESSION['error-message'] = 'Số điện thoại phải từ 10 đến 15 chữ số.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // Kiểm tra trùng lặp Gmail và SDT
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM ThongTinKhachHang WHERE Gmail = ? OR SDT = ?");
    $stmt->bind_param("ss", $gmail, $sdt);
    
    if (!$stmt->execute()) {
        $_SESSION['error-message'] = 'Lỗi khi kiểm tra trùng lặp: ' . $stmt->error;
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $_SESSION['error-message'] = 'Gmail hoặc số điện thoại đã tồn tại.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // Kiểm tra trùng lặp tên đăng nhập
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM TaiKhoanKhachHang WHERE Tendn = ?");
    $stmt->bind_param("s", $tendn);
    
    if (!$stmt->execute()) {
        $_SESSION['error-message'] = 'Lỗi khi kiểm tra tên đăng nhập: ' . $stmt->error;
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $_SESSION['error-message'] = 'Tên đăng nhập đã tồn tại.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // Băm mật khẩu trước khi lưu vào cơ sở dữ liệu
    $matkhauHash = password_hash($matkhau, PASSWORD_DEFAULT);

    // Bắt đầu transaction
    $mysqli->begin_transaction();

    try {
        // Insert vào bảng ThongTinKhachHang
        $stmt = $mysqli->prepare("INSERT INTO ThongTinKhachHang (Tenkh, Ngaysinh, SDT, Gmail) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $tenkh, $ngaySinh, $sdt, $gmail);
        
        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi chèn vào ThongTinKhachHang: " . $stmt->error);
        }
        
        // Lấy idkh vừa tạo
        $idkh = $mysqli->insert_id;  // Lấy idkh từ ThongTinKhachHang

        // Insert vào bảng TaiKhoanKhachHang
        $stmt = $mysqli->prepare("INSERT INTO TaiKhoanKhachHang (Tendn, Matkhau, idkh) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $tendn, $matkhauHash, $idkh);

        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi chèn vào TaiKhoanKhachHang: " . $stmt->error);
        }

        // Insert vào bảng QuanLyTaiKhoan
        $current_date = date('Y-m-d'); // Lấy ngày hiện tại
        $stmt = $mysqli->prepare("INSERT INTO QuanLyTaiKhoan (Trangthai, Ngaythamgia, Loaikh, idkh) VALUES ('hoatdong', ?, 'Khachhangthuong', ?)");
        $stmt->bind_param("si", $current_date, $idkh);

        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi chèn vào QuanLyTaiKhoan: " . $stmt->error);
        }

        // Commit transaction
        $mysqli->commit();

        // Thiết lập thông báo thành công vào session
        $_SESSION['success-message'] = 'Đăng ký thành công!';
        
        // Chuyển hướng về trang đăng nhập
        header('Location:../index.php?action=trangchu');
        exit;

    } catch (Exception $e) {
        // Rollback transaction
        $mysqli->rollback();
        $_SESSION['error-message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    } finally {
        // Đóng statement và kết nối
        if (isset($stmt)) $stmt->close();
        $mysqli->close();
    }
}