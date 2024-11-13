<div class="container_quanlysan">
    <h1>Quản Lý Sân</h1>
    
    <!-- Form để thêm sân -->
    <form action="pages/quanlysan/themsan.php" method="post" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group">
                <label>Tên sân:</label>
                <input type="text" id="field-name" name="Tensan" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Thời gian mở:</label>
                <input type="time" id="opening-time" name="TGmo" required>
            </div>
            <div class="form-group">
                <label>Thời gian đóng:</label>
                <input type="time" id="closing-time" name="TGdong" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Địa chỉ:</label>
                <input type="text" id="address" name="Diachi" required>
            </div>
        </div>

        <div class="form-row detail-image-row">
            <div class="form-group image-group">
                <label>Thêm ảnh:</label>
                <div class="image-upload">
                    <img id="preview" src="" alt="Ảnh xem trước">
                    <input type="file" id="field-image" name="Hinhanh" accept="image/*" onchange="previewImage(event)" required>         
                </div>
            </div>
            <div class="form-group textarea-group">
                <label>Thông tin chi tiết:</label>
                <textarea id="details" name="Chitiet"></textarea>
            </div>
        </div>

        <div class="button-group">
            <button type="submit" class="add-button">Thêm</button>
            <a href="?action=quanlysan&query=sancon" class="addsancon-button" style="display: inline-block; padding: 10px 20px; border-radius: 4px; background-color: #007bff; color: white; text-decoration: none;">Thêm sân con</a>
        </div>  
    </form>
</div>

<?php
// Hiển thị thông báo thành công (nếu có)
if (isset($_SESSION['success-message'])) {
    echo "<div class='success-message' id=successMessage'>" . $_SESSION['success-message'] . "</div>";
    unset($_SESSION['success-message']); // Xóa thông báo sau khi hiển thị
}

// Hiển thị thông báo lỗi (nếu có)
if (isset($_SESSION['error-message'])) {
    echo "<div class='error-message' id='errorMessage'>" . $_SESSION['error-message'] . "</div>";
    unset($_SESSION['error-message']); // Xóa thông báo sau khi hiển thị
}
?>

<!-- Bảng hiển thị danh sách sân -->
<table>
    <thead>
        <tr>
            <th>ID Sân</th>
            <th>Tên Sân</th>
            <th>Ảnh</th>
            <th>Thời Gian Hoạt Động</th>
            <th>Địa Chỉ</th>
            <th>Thông Tin Chi Tiết</th>
            <th>Thao Tác</th> <!-- Thêm cột thao tác -->
        </tr>
    </thead>
    <tbody>
        <?php
        // Kết nối cơ sở dữ liệu
        include 'connect/mysqlconnect.php';

        // Truy vấn dữ liệu
        $sql = "SELECT idsan, Tensan, Hinhanh, TGmo, TGdong, Diachi, Chitiet FROM QuanLySan";
        $result = $mysqli->query($sql);

        // Kiểm tra kết quả truy vấn
        if ($result === false) {
            echo "Lỗi trong truy vấn: " . $mysqli->error;
        } else {
            // Hiển thị dữ liệu trong bảng
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['idsan']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Tensan']) . "</td>";
                    $hinhanhPath = 'pages/quanlysan/uploads/' . htmlspecialchars($row['Hinhanh']);
                    echo "<td><img src='" . $hinhanhPath . "' alt='Hình ảnh' style='width: 100px;'></td>";
                    echo "<td>" . htmlspecialchars($row['TGmo']) . " - " . htmlspecialchars($row['TGdong']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Diachi']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Chitiet']) . "</td>";

                    // Thêm cột nút sửa và xóa
                    echo "<td>
                            <form method='post' action='?action=quanlysan&query=suasan' style='display:inline-block;'>
                                <input type='hidden' name='id' value='" . htmlspecialchars($row['idsan']) . "'>
                                <button type='submit' class='edit-button' title='Sửa'>
                                    <i class='fas fa-edit'></i>
                                </button>
                            </form>
                            <form method='post' action='pages/quanlysan/xoasan.php' style='display:inline-block;' onsubmit='return confirmDelete();'>
                                <input type='hidden' name='id' value='" . htmlspecialchars($row['idsan']) . "'>
                                <button type='submit' class='delete-button' title='Xóa'>
                                    <i class='fas fa-trash-alt'></i>
                                </button>
                            </form>
                        </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Không có dữ liệu</td></tr>";
            }
        }

        // Đóng kết nối
        $mysqli->close();
        ?>
    </tbody>
</table>

<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    function confirmDelete() {
        return confirm("Điều này sẽ ảnh hưởng đến các dữ liệu của lịch đặt sân và doanh thu. Bạn có chắc chắn muốn xóa sân này không?");
    }

    document.addEventListener('DOMContentLoaded', function() {
    // Ẩn thông báo sau 3 giây nếu có
    const messages = document.querySelectorAll('.success-message, .error-message');

    messages.forEach(function(message) {
        if (message) {
            setTimeout(() => {
                message.classList.add('fade-out');
                setTimeout(() => {
                    message.style.display = 'none';
                }, 500); // Thời gian trễ để đợi hiệu ứng mờ dần
            }, 3000); // Ẩn sau 3 giây
        }
    });
    });
</script>
