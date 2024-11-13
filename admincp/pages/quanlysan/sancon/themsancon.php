<?php
// Khởi tạo session
session_start();

// Kết nối đến cơ sở dữ liệu
include '../../../connect/mysqlconnect.php';

// Kiểm tra nếu form đã được submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $idSan = $_POST['idsan']; // Thêm ID sân
    $loaiSan = $_POST['Loaisan'];
    $tenSan = $_POST['Tensan'];

    // Xử lý giá, loại bỏ các ký tự không hợp lệ và định dạng lại
    $gia = str_replace('.', '', $_POST['Gia']); // Xóa dấu chấm
    $gia = str_replace(',', '', $gia); // Xóa dấu phẩy
    $gia = floatval($gia); // Chuyển đổi thành số thực

    // Kiểm tra xem giá có hợp lệ không
    if ($gia <= 0) {
        $_SESSION['error-message'] = "Giá không hợp lệ. Vui lòng nhập giá lớn hơn 0.";
        header("Location:../../../index.php?action=quanlysan&query=sancon");
        exit();
    }

    $trangThai = $_POST['Trangthai'];

    // Xử lý upload ảnh
    $target_dir = "uploads/";  // Thư mục lưu ảnh
    $imageFileType = strtolower(pathinfo($_FILES["Hinhanh"]["name"], PATHINFO_EXTENSION));

    // Tạo tên tệp hình ảnh duy nhất theo quy tắc (dựa theo mã đầu tiên)
    $hinhanh = time() . '_' . basename($_FILES['Hinhanh']['name']); // Tạo tên ảnh
    $target_file = $target_dir . $hinhanh; // Đường dẫn lưu tệp

    $uploadOk = 1;

    // Kiểm tra xem file có thực sự là ảnh không
    $check = getimagesize($_FILES["Hinhanh"]["tmp_name"]);
    if ($check === false) {
        $_SESSION['error-message'] = "File không phải là ảnh.";
        $uploadOk = 0;
    }

    // Kiểm tra kích thước file (giới hạn 5MB)
    if ($_FILES["Hinhanh"]["size"] > 5000000) {
        $_SESSION['error-message'] = "Dung lượng file quá lớn.";
        $uploadOk = 0;
    }

    // Chỉ cho phép các định dạng JPG, JPEG, PNG
    if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png") {
        $_SESSION['error-message'] = "Chỉ chấp nhận file JPG, JPEG, PNG.";
        $uploadOk = 0;
    }

    // Kiểm tra nếu file hợp lệ và thực hiện upload
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["Hinhanh"]["tmp_name"], $target_file)) {
            // Thêm dữ liệu vào cơ sở dữ liệu
            // Dùng tên tệp đã tạo
            $sql = "INSERT INTO Sancon (idsan, Loaisan, Tensan, Hinhanh, Gia, Trangthai) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            // Tạo statement
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("isssss", $idSan, $loaiSan, $tenSan, $hinhanh, $gia, $trangThai);

            if ($stmt->execute()) {
                $_SESSION['success-message'] = "Thêm sân con thành công.";
                header("Location:../../../index.php?action=quanlysan&query=sancon");
                exit();
            } else {
                $_SESSION['error-message'] = "Lỗi: " . $stmt->error;
            }
        } else {
            $_SESSION['error-message'] = "Có lỗi xảy ra khi tải lên hình ảnh.";
        }
    }

    // Đóng statement
    $stmt->close();
}

// Đóng kết nối cơ sở dữ liệu
$mysqli->close();
