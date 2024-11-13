<?php
session_start(); // Khởi tạo session

include '../../connect/mysqlconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ biểu mẫu
    $idsan = $_POST['idsan'];
    $tensan = $_POST['Tensan'];
    $tgmo = $_POST['TGmo'];
    $tgdong = $_POST['TGdong'];
    $diachi = $_POST['Diachi'];
    $chitiet = $_POST['Chitiet'];

    // Xử lý hình ảnh
    $hinhanh = null;
    if (isset($_FILES['Hinhanh']) && $_FILES['Hinhanh']['error'] == 0) {
        // Lấy phần mở rộng của hình ảnh mới
        $imageFileType = strtolower(pathinfo($_FILES['Hinhanh']['name'], PATHINFO_EXTENSION));

        // Tạo tên hình ảnh mới
        $hinhanh = time() . '_' . basename($_FILES['Hinhanh']['name']); // Ví dụ: 1234567890_tenfile.jpg
        $target = "uploads/" . $hinhanh; // Đường dẫn lưu tệp với tên mới

        // Kiểm tra nếu tồn tại hình ảnh cũ để xóa
        $sql_select = "SELECT Hinhanh FROM QuanLySan WHERE idsan = ?";
        $stmt_select = $mysqli->prepare($sql_select);
        $stmt_select->bind_param("i", $idsan);
        $stmt_select->execute();
        $stmt_select->bind_result($hinhanh_cu);
        $stmt_select->fetch();
        $stmt_select->close();

        // Xóa hình ảnh cũ nếu có
        if ($hinhanh_cu && file_exists("uploads/" . $hinhanh_cu)) {
            unlink("uploads/" . $hinhanh_cu);
        }

        // Di chuyển hình ảnh mới vào thư mục
        if (!move_uploaded_file($_FILES['Hinhanh']['tmp_name'], $target)) {
            $_SESSION['error_message'] = "Có lỗi khi tải hình ảnh lên.";
            header('Location: ../../index.php?action=quanlysan&query=suasan&id=' . $idsan);
            exit();
        }
    }

    // Cập nhật thông tin sân
    $sql = "UPDATE QuanLySan SET Tensan = ?, TGmo = ?, TGdong = ?, Diachi = ?, Chitiet = ?" . ($hinhanh ? ", Hinhanh = ?" : "") . " WHERE idsan = ?";
    $stmt = $mysqli->prepare($sql);
    
    if ($hinhanh) {
        $stmt->bind_param("ssssssi", $tensan, $tgmo, $tgdong, $diachi, $chitiet, $hinhanh, $idsan);
    } else {
        $stmt->bind_param("sssssi", $tensan, $tgmo, $tgdong, $diachi, $chitiet, $idsan);
    }

    if ($stmt->execute()) {
        $_SESSION['success-message'] = "Cập nhật sân thành công!";
        header('Location: ../../index.php?action=quanlysan&query=themsan');
        exit();
        
    } else {
        $_SESSION['error_message'] = "Có lỗi khi cập nhật sân. Vui lòng thử lại!";
        header('Location: ../../index.php?action=quanlysan&query=suasan&id=' . $idsan);
    }
    

    $stmt->close();
}

$mysqli->close();