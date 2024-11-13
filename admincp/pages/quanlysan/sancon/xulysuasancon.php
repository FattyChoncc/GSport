<?php
session_start(); // Khởi tạo session

include '../../../connect/mysqlconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ biểu mẫu
    $idsancon = $_POST['id']; // Đổi tên biến để đồng nhất
    $tensan = $_POST['Tensan'];
    $loaisan = $_POST['Loaisan'];
    $gia = $_POST['Gia'];

    // Khởi tạo biến cho hình ảnh
    $hinhanh = null;

    // Xử lý hình ảnh
    if (isset($_FILES['Hinhanh']) && $_FILES['Hinhanh']['error'] == 0) {
        // Lấy phần mở rộng của hình ảnh mới
        $imageFileType = strtolower(pathinfo($_FILES['Hinhanh']['name'], PATHINFO_EXTENSION));

        // Tạo tên hình ảnh mới
        $hinhanh = time() . '_' . basename($_FILES['Hinhanh']['name']); // Ví dụ: 1234567890_tenfile.jpg
        $target = "uploads/" . $hinhanh; // Đường dẫn lưu tệp với tên mới

        // Lấy tên hình ảnh cũ từ cơ sở dữ liệu
        $sql_select = "SELECT Hinhanh FROM Sancon WHERE id = ?";
        $stmt_select = $mysqli->prepare($sql_select);
        $stmt_select->bind_param("i", $idsancon);
        $stmt_select->execute();
        $stmt_select->bind_result($hinhanh_cu);
        $stmt_select->fetch();
        $stmt_select->close();

        // Xóa hình ảnh cũ nếu có
        if ($hinhanh_cu && file_exists("uploads/" . $hinhanh_cu)) {
            unlink("uploads/" . $hinhanh_cu); // Xóa hình ảnh cũ
        }

        // Di chuyển hình ảnh mới vào thư mục
        if (!move_uploaded_file($_FILES['Hinhanh']['tmp_name'], $target)) {
            $_SESSION['error-message'] = "Có lỗi khi tải hình ảnh lên.";
            header("Location:../../../index.php?action=quanlysan&query=sancon");
            exit();
        }
    }

    // Cập nhật thông tin sân mà không thay đổi trạng thái
    $sql = "UPDATE Sancon SET Tensan = ?, Loaisan = ?, Gia = ?" . ($hinhanh ? ", Hinhanh = ?" : "") . " WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    
    if ($hinhanh) {
        $stmt->bind_param("ssssi", $tensan, $loaisan, $gia, $hinhanh, $idsancon);
    } else {
        $stmt->bind_param("sssi", $tensan, $loaisan, $gia, $idsancon);
    }

    if ($stmt->execute()) {
        $_SESSION['success-message'] = "Cập nhật thông tin sân thành công.";
        header("Location:../../../index.php?action=quanlysan&query=sancon"); // Chuyển hướng về trang danh sách
        exit(); // Ngăn chặn thực thi thêm
    } else {
        $_SESSION['error-message'] = "Lỗi: " . $stmt->error;
        header("Location:../../../index.php?action=quanlysan&query=sancon"); // Quay lại trang danh sách
    }

    $stmt->close();
}

$mysqli->close();

