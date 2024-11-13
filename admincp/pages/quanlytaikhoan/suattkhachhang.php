<?php
// Kết nối đến cơ sở dữ liệu
include 'connect/mysqlconnect.php';

// Kiểm tra xem có id khách hàng được gửi từ form không
if (isset($_POST['id'])) {
    $idkh = $_POST['id'];

    // Truy vấn để lấy thông tin khách hàng
    $sql = "SELECT T.Tenkh, T.Ngaysinh, T.SDT, T.Gmail, Q.Loaikh 
            FROM ThongTinKhachHang T
            JOIN QuanLyTaiKhoan Q ON T.idkh = Q.idkh
            WHERE T.idkh = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $idkh);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kiểm tra nếu có dữ liệu
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Không tìm thấy thông tin khách hàng.";
        exit;
    }
} else {
    echo "Không có ID khách hàng.";
    exit;
}

// Đóng kết nối
$mysqli->close();
?>
    <div class="container_suattkhachhang">
    <h1>Sửa Thông Tin Khách Hàng</h1>
    <form action="pages/quanlytaikhoan/xulysuatt.php" method="POST" onsubmit="return validateForm()">
        <input type="hidden" name="idkh" value="<?php echo $idkh; ?>">

        <label for="loaikh">Loại Khách Hàng:</label>
        <select id="loaikh" name="loaikh" required>
            <option value="khachVIP" <?php if ($row['Loaikh'] == 'khachVIP') echo 'selected'; ?>>Khách VIP</option>
            <option value="Khachhangthuong" <?php if ($row['Loaikh'] == 'Khachhangthuong') echo 'selected'; ?>>Khách Hàng Thường</option>
        </select>

        <div class="row">
            <div class="column">
                <label for="tenkh">Tên Khách Hàng:</label>
                <input type="text" id="tenkh" name="tenkh" value="<?php echo htmlspecialchars($row['Tenkh']); ?>" required>
            </div>
            <div class="column">
                <label for="ngaysinh">Ngày Sinh:</label>
                <input type="date" id="ngaysinh" name="ngaysinh" value="<?php echo htmlspecialchars($row['Ngaysinh']); ?>" required>
            </div>
        </div>

        <div class="row">
            <div class="column">
                <label for="sdt">Số Điện Thoại:</label>
                <input type="text" id="sdt" name="sdt" value="<?php echo htmlspecialchars($row['SDT']); ?>" required pattern="\d*" title="Vui lòng chỉ nhập số">
            </div>
            <div class="column">
                <label for="gmail">Email:</label>
                <input type="email" id="gmail" name="gmail" value="<?php echo htmlspecialchars($row['Gmail']); ?>" required>
            </div>
        </div>
        <div class="button-container">
        <button type="submit">Cập Nhật Thông Tin</button>
        </div>
    </form>
</div>

<script>
    function validateForm() {
        var sdt = document.getElementById("sdt").value;
        var gmail = document.getElementById("gmail").value;

        // Kiểm tra số điện thoại
        if (isNaN(sdt)) {
            alert("Số điện thoại chỉ được phép nhập số.");
            return false;
        }

        // Kiểm tra định dạng Gmail
        if (!gmail.includes("@")) {
            alert("Email phải chứa ký tự '@'.");
            return false;
    }
</script>
