<?php
// Kết nối đến cơ sở dữ liệu
include 'connect/mysqlconnect.php';

// Lấy ID từ POST
if (isset($_POST['id'])) {
    $idsan = $_POST['id'];

    // Truy vấn thông tin sân dựa trên ID
    $sql = "SELECT Tensan, TGmo, TGdong, Diachi, Hinhanh, Chitiet FROM QuanLySan WHERE idsan = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $idsan);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kiểm tra xem có dữ liệu trả về không
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Không tìm thấy sân với ID này.";
        exit();
    }
} else {
    echo "ID sân không hợp lệ.";
    exit();
}
?>
<div class="container_suasan">
    <h1>Chỉnh Sửa Sân</h1>
    <form action="pages/quanlysan/xulysuasan.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="idsan" value="<?php echo htmlspecialchars($idsan); ?>">

        <div class="form-group">
            <label>Tên sân:</label>
            <input type="text" name="Tensan" value="<?php echo htmlspecialchars($row['Tensan']); ?>" required>
        </div>
        
        <div class="form-group time-group">
            <div>
                <label>Thời gian mở:</label>
                <input type="time" name="TGmo" value="<?php echo htmlspecialchars($row['TGmo']); ?>" required>
            </div>
            <div>
                <label>Thời gian đóng:</label>
                <input type="time" name="TGdong" value="<?php echo htmlspecialchars($row['TGdong']); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Địa chỉ:</label>
            <input type="text" name="Diachi" value="<?php echo htmlspecialchars($row['Diachi']); ?>" required>
        </div>

        <div class="form-group image-info-group">
            <div class="image-group">
                <label>Ảnh hiện tại:</label>
                <img src="pages/quanlysan/uploads/<?php echo htmlspecialchars($row['Hinhanh']); ?>" alt="Ảnh sân" style="max-width: 150px; max-height: 100px;">
                <label>Thay đổi ảnh:</label>
                <input type="file" name="Hinhanh" accept="image/*" id="imageUpload">
                <br>
                <img id="previewImage" src="#" alt="Xem trước ảnh" style="display: none; max-width: 150px; max-height: 100px; margin-top: 10px;">
            </div>

            <div class="info-group">
                <label>Thông tin chi tiết:</label>
                <textarea name="Chitiet" rows="4"><?php echo htmlspecialchars($row['Chitiet']); ?></textarea>
            </div>
        </div>

        <div class="button-group">
            <button type="submit" class="save-button">Lưu thay đổi</button>
            <a href="?action=quanlysan&query=themsan" class="cancel-button">Hủy</a> <!-- Nút Hủy -->
        </div>
    </form>
</div>

<script>
    // Hàm xem trước ảnh
    document.getElementById('imageUpload').addEventListener('change', function(event) {
        const input = event.target;
        const previewImage = document.getElementById('previewImage');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewImage.style.display = 'block'; // Hiển thị ảnh xem trước
            }
            reader.readAsDataURL(input.files[0]); // Đọc tệp ảnh
        } else {
            previewImage.src = '#';
            previewImage.style.display = 'none'; // Ẩn ảnh nếu không có tệp
        }
    });
</script>
