<?php
// Kết nối cơ sở dữ liệu
include '../../admincp/connect/mysqlconnect.php';

if (isset($_GET['courtId'])) {
    $courtId = (int)$_GET['courtId'];

    // Truy vấn lấy thông tin sân con và đường dẫn hình ảnh
    $query = "SELECT * FROM Sancon WHERE id = $courtId";
    $result = $mysqli->query($query);

    if ($result->num_rows > 0) {
        $court = $result->fetch_assoc();

        // Đảm bảo đường dẫn hình ảnh đúng
        $court['Hinhanh'] = 'admincp/pages/quanlysan/sancon/uploads/' . $court['Hinhanh'];  // Thêm thư mục images vào đường dẫn

        // Gửi dữ liệu dưới dạng JSON
        echo json_encode($court);
    } else {
        echo json_encode(null);
    }
} else {
    echo json_encode(null);
}
?>
