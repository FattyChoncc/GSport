<?php
session_start();
include('../../admincp/connect/mysqlconnect.php');

/*Kiểm tra người dùng có đăng nhập có đăng nhập hay chưa*/
if (!isset($_SESSION['idkh'])) {
    echo "Bạn cần đăng nhập để xem lịch sử đặt sân.";
    exit();
}

$idkh = $_SESSION['idkh'];
$dateInput = isset($_GET['dateInput']) ? $_GET['dateInput'] : '';
$startTime = isset($_GET['startTime']) ? $_GET['startTime'] : '';
$endTime = isset($_GET['endTime']) ? $_GET['endTime'] : '';
$courtType = isset($_GET['courtType-history']) ? $_GET['courtType-history'] : '';

// Prepare the SQL query with conditions
$sql = "SELECT L.idlich, L.TGbatdau, L.TGketthuc, L.Ngaydat, L.Tongtien, L.Trangthai, 
                S.LoaiSan, S.Tensan AS TenSanCon, Q.Tensan AS TenSan, Q.Diachi 
        FROM LichDat L
        INNER JOIN Sancon S ON L.id = S.id
        INNER JOIN QuanLySan Q ON S.idsan = Q.idsan
        WHERE L.idkh = ?";

$conditions = [$idkh];

// Add filter conditions if provided
if ($dateInput) {
    $sql .= " AND L.Ngaydat = ?";
    $conditions[] = $dateInput;
}

if ($startTime) {
    $sql .= " AND L.TGbatdau >= ?";
    $conditions[] = $startTime;
}

if ($endTime) {
    $sql .= " AND L.TGketthuc <= ?";
    $conditions[] = $endTime;
}

if ($courtType) {
    $sql .= " AND S.LoaiSan = ?";
    $conditions[] = $courtType;
}

// Prepare the statement
$stmt = $mysqli->prepare($sql);
if ($stmt) {
    $types = str_repeat('s', count($conditions));
    array_unshift($conditions, $types); // Add data types at the beginning

    // Bind parameters
    call_user_func_array([$stmt, 'bind_param'], refValues($conditions));

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Output the booking history
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='booking-entry'>";
            echo "<div class='booking-entry-header'>";
            echo "<p>Tên sân: " . $row['TenSan'] . "</p>";
            echo "<p id='sum'>Tổng tiền: " . number_format($row['Tongtien'], 2) . " VNĐ</p>";
            echo "</div>";
            echo "<div class='booking-details'>";
            echo "<p>Mã lịch đặt: " . $row['idlich'] . "</p>";
            echo "<p>Tên sân con: " . $row['TenSanCon'] . "</p>";
            echo "<p>Ngày đặt: " . $row['Ngaydat'] . "</p>";
            echo "<p>Thời gian bắt đầu: " . $row['TGbatdau'] . "</p>";
            echo "<p>Thời gian kết thúc: " . $row['TGketthuc'] . "</p>";
            echo "<p>Trạng thái: " . $row['Trangthai'] . "</p>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "Không có kết quả phù hợp.";
    }
} else {
    echo "Lỗi kết nối cơ sở dữ liệu.";
}

// Function to pass array by reference
function refValues($arr) {
    $refs = [];
    foreach ($arr as $key => $value) {
        $refs[$key] = &$arr[$key];
    }
    return $refs;
}
