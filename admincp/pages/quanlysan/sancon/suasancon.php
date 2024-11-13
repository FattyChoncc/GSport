<?php
// Kết nối đến cơ sở dữ liệu
include 'connect/mysqlconnect.php';

// Lấy ID sân con từ POST
if (isset($_POST['id'])) {
    $idSanCon = $_POST['id'];

    // Truy vấn thông tin sân con dựa trên ID
    $sql = "SELECT Tensan, Loaisan, Gia, Trangthai, Hinhanh, idsan FROM Sancon WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    
    // Kiểm tra xem prepare có thành công không
    if ($stmt === false) {
        die('Lỗi prepare: ' . $mysqli->error);
    }
    
    $stmt->bind_param("i", $idSanCon);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kiểm tra xem có dữ liệu trả về không
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Không tìm thấy sân con với ID này.";
        exit();
    }
} else {
    echo "ID sân con không hợp lệ.";
    exit();
}

// Lấy danh sách các sân từ bảng QuanLySan
$sqlQuanLySan = "SELECT idsan, Tensan FROM QuanLySan";
$resultQuanLySan = $mysqli->query($sqlQuanLySan);

if (!$resultQuanLySan) {
    die('Lỗi truy vấn danh sách sân: ' . $mysqli->error);
}
?>

<div class="container_suasancon">
    <h1>Chỉnh Sửa Sân Con</h1>
    <form action="pages/quanlysan/sancon/xulysuasancon.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($idSanCon); ?>">

        <!-- Chọn sân -->
        <div class="form-group">
            <label>Chọn sân:</label>
            <select name="idsan" required>
                <?php
                while ($san = $resultQuanLySan->fetch_assoc()) {
                    $selected = ($san['idsan'] == $row['idsan']) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($san['idsan']) . "' $selected>" . htmlspecialchars($san['Tensan']) . "</option>";
                }
                ?>
            </select>
        </div>
        
        <!-- Tên sân con và Loại sân -->
        <div class="two-column">
            <div class="form-group">
                <label>Tên sân con:</label>
                <input type="text" name="Tensan" value="<?php echo htmlspecialchars($row['Tensan']); ?>" required>
            </div>
            <div class="form-group">
                <label>Loại sân:</label>
                <input type="text" name="Loaisan" value="<?php echo htmlspecialchars($row['Loaisan']); ?>" required>
            </div>
        </div>

        <!-- Ảnh hiện tại và Giá -->
        <div class="two-column">
            <div class="form-group">
                <label>Ảnh hiện tại:</label>
                <img src="pages/quanlysan/sancon/uploads/<?php echo htmlspecialchars($row['Hinhanh']); ?>" alt="Ảnh hiện tại" class="preview-image" style="max-width: 150px; max-height: 100px;">
            </div>
            <div class="form-group">
                <label>Giá:</label>
                <input type="text" name="Gia" value="<?php echo number_format($row['Gia'], 0, ',', '.'); ?>" required>
            </div>
        </div>

        <!-- Ảnh mới (chọn ảnh) -->
        <div class="form-group image-info-group">
            <label>Thay đổi ảnh:</label>
            <input type="file" name="Hinhanh" accept="image/*" id="imageUpload">
            <br>
            <img id="previewImage" src="#" alt="Xem trước ảnh" style="display: none; max-width: 150px; max-height: 100px; margin-top: 10px;">
        </div>

        <div class="button-group">
            <button type="submit" class="save-button">Lưu thay đổi</button>
            <a href="?action=quanlysan&query=sancon" class="cancel-button">Hủy</a>
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