<?php
include 'connect/mysqlconnect.php'; // Kết nối với cơ sở dữ liệu

// Kiểm tra xem có ID admin được gửi từ form không
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Truy vấn để lấy thông tin admin
    $sql = "SELECT * FROM ThongTinAdmin WHERE idam = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $id); // Ràng buộc ID như kiểu string
    $stmt->execute();
    $result = $stmt->get_result();

    // Kiểm tra nếu có dữ liệu
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Không tìm thấy thông tin admin.";
        exit;
    }
} else {
    echo "Không có ID admin.";
    exit;
}

// Đóng kết nối
$mysqli->close();
?>

<div class="container_sua_admin">
    <h1>Sửa Thông Tin Admin</h1>
    <form action="pages/quanlytaikhoanadmin/xulysuaadmin.php" method="POST" onsubmit="return validateForm()">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['idam']); ?>"> <!-- Lưu ID admin -->

        <div class="form-row">
            <div class="form-group">
                <label for="admin-name">Tên Admin:</label>
                <input type="text" id="admin-name" name="admin-name" value="<?php echo htmlspecialchars($row['Tenadmin']); ?>" required>
            </div>
            <div class="form-group">
                <label for="birthdate">Ngày Sinh:</label>
                <input type="date" id="birthdate" name="birthdate" value="<?php echo htmlspecialchars(date('Y-m-d', strtotime($row['Ngaysinh']))); ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="phone">SĐT:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($row['SDT']); ?>" required pattern="\d*" title="Vui lòng chỉ nhập số">
            </div>
            <div class="form-group">
                <label for="email">Gmail:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['Gmail']); ?>" required>
            </div>
        </div>

        <div class="button-group">
            <button type="submit" class="save-button">Lưu Thay Đổi</button>
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
