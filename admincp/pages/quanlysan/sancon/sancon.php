<div class="container_sancon">
    <h1>Quản Lý Sân Con</h1>
    <form action="pages/quanlysan/sancon/themsancon.php" method="post" enctype="multipart/form-data">
        <!-- Chọn sân -->
        <div class="form-row">
            <div class="form-group">
                <label>Chọn sân:</label>
                <select name="idsan" required>
                    <?php
                    include 'connect/mysqlconnect.php';

                    // Lấy danh sách tên sân từ bảng QuanLySan
                    $sql = "SELECT idsan, Tensan FROM QuanLySan";
                    $result = $mysqli->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($row['idsan']) . "'>" . htmlspecialchars($row['Tensan']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>Không có sân tồn tại</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <!-- Tên sân và Loại sân -->
        <div class="form-row">
            <div class="form-group">
                <label>Tên sân con:</label>
                <input type="text" name="Tensan" required>
            </div>
            <div class="form-group">
                <label>Loại sân:</label>
                <input type="text" name="Loaisan" required>
            </div>
        </div>

        <!-- Chọn ảnh và Giá -->
        <div class="form-row">
            <div class="form-group">
                <label>Chọn ảnh:</label>
                <input type="file" name="Hinhanh" accept="image/*" onchange="previewImage(event)" required>
            </div>
            <div class="form-group">
                <label>Giá:</label>
                <input type="number" name="Gia" step="0.01" required>
            </div>
        </div>

        <!-- Ảnh xem trước và Trạng thái -->
        <div class="form-row">
            <div class="form-group">
                <label>Ảnh xem trước:</label>
                <img id="preview" src="" alt="Ảnh xem trước" class="preview-image">
            </div>
            <div class="form-group">
                <label>Trạng thái:</label>
                <select name="Trangthai" required>
                    <option value="hoatdong">Hoạt động</option>
                    <option value="khonghoatdong">Không hoạt động</option>
                </select>
            </div>
        </div>

        <div class="button-group">
            <button type="submit" class="add-button">Thêm Sân Con</button>
        </div>
    </form>
</div>

<!-- Form lọc sân -->
<form method="get" action="">
    <div class="form-row filter-container-sancon">
        <label>Lọc theo sân:</label>
        <select name="filter_san">
            <option value="">Tất cả</option>
            <?php
            // Hiển thị danh sách sân
            $sqlSan = "SELECT idsan, Tensan FROM QuanLySan";
            $resultSan = $mysqli->query($sqlSan);

            if ($resultSan && $resultSan->num_rows > 0) {
                while ($san = $resultSan->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($san['Tensan']) . "'>" . htmlspecialchars($san['Tensan']) . "</option>";
                }
            }
            ?>
        </select>
    </div>
</form>

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

<!-- Bảng hiển thị danh sách sân con -->
<table>
    <thead>
        <tr>
            <th>ID Sân Con</th>
            <th>Tên Sân</th> 
            <th>Loại Sân</th>
            <th>Tên Sân Con</th>
            <th>Hình Ảnh</th>
            <th>Giá</th>
            <th>Trạng Thái</th>
            <th>Thao Tác</th>
            <th>Cập Nhật Trạng Thái</th>
        </tr>
    </thead>
    <tbody id="sancon-table">
        <?php
        // Truy vấn lấy dữ liệu từ bảng Sancon và QuanLySan
        $sql = "
            SELECT s.id, s.Tensan AS TenSanCon, s.Loaisan, s.Hinhanh, s.Gia, s.Trangthai, q.Tensan AS TenSanQuanLy 
            FROM Sancon s 
            JOIN QuanLySan q ON s.idsan = q.idsan
        ";
        $result = $mysqli->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr data-ten-san='" . htmlspecialchars($row['TenSanQuanLy']) . "'>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['TenSanQuanLy']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Loaisan']) . "</td>";
                echo "<td>" . htmlspecialchars($row['TenSanCon']) . "</td>";

                $hinhanhPath = 'pages/quanlysan/sancon/uploads/' . htmlspecialchars($row['Hinhanh']);
                echo "<td><img src='" . $hinhanhPath . "' alt='Hình ảnh' style='width: 100px;'></td>";

                // Cập nhật định dạng hiển thị giá
                echo "<td>" . number_format($row['Gia'], 0, ',', '.') . " VND</td>"; // Định dạng giá với hàng nghìn
                
                echo "<td>" . ($row['Trangthai'] === 'hoatdong' ? '<span class="active-status">Hoạt Động</span>' : '<span class="inactive-status">Tạm dừng</span>') . "</td>";

                // Cột Thao Tác: Chứa các biểu tượng Sửa và Xóa
                echo "<td>
                        <form method='post' action='?action=quanlysan&query=suasancon' style='display:inline-block;'>
                            <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                            <button type='submit' class='edit-button' style='background: none; border: none; cursor: pointer; color: #007bff;'>
                                <i class='fas fa-edit'></i>
                            </button>
                        </form>
                        <form method='post' action='pages/quanlysan/sancon/xoasancon.php' style='display:inline-block;' onsubmit='return confirmDelete();'>
                            <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                            <button type='submit' class='delete-button' style='background: none; border: none; cursor: pointer; color: red;'>
                                <i class='fas fa-trash-alt'></i>
                            </button>
                        </form>
                    </td>";

                // Cột Tùy Chỉnh Trạng Thái
                echo "<td>";
                if ($row['Trangthai'] === 'khonghoatdong') {
                    // Nút mở khóa sẽ chỉ hiển thị khi trạng thái là "không hoạt động"
                    echo "<form method='post' action='pages/quanlysan/sancon/xulymo.php' style='display:inline-block;' onsubmit='return confirmUnlock();'>
                            <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                            <button type='submit' class='unlock-button' style='background: none; border: none; cursor: pointer; color: green;'>
                                <i class='fas fa-unlock'></i>
                            </button>
                        </form>";

                    // Vô hiệu hóa nút khóa khi trạng thái là "không hoạt động"
                    echo "<button type='button' class='lock-button' style='background: none; border: none; cursor: not-allowed; color: grey;' disabled>
                            <i class='fas fa-lock'></i>
                        </button>";
                } else {
                    // Nút khóa sẽ chỉ hiển thị khi trạng thái là "hoạt động"
                    echo "<button type='button' class='unlock-button' style='background: none; border: none; cursor: not-allowed; color: grey;' disabled>
                            <i class='fas fa-unlock'></i>
                        </button>";
                        
                    echo "<form method='post' action='pages/quanlysan/sancon/xulykhoa.php' style='display:inline-block;' onsubmit='return confirmLock();'>
                            <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                            <button type='submit' class='lock-button' style='background: none; border: none; cursor: pointer; color: orange;'>
                                <i class='fas fa-lock'></i>
                            </button>
                        </form>";
                }
                echo "</td>";
                echo "</tr>"; // Đóng thẻ <tr>
            }
        } else {
            echo "<tr><td colspan='9'>Không có dữ liệu</td></tr>";
        }
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
        return confirm("Điều này sẽ ảnh hưởng đến các dữ liệu của lịch đặt sân và doanh thu. Bạn có chắc chắn muốn xóa sân con này không?");
    }

    function confirmLock() {
        return confirm("Bạn có chắc chắn muốn khóa sân con này không?");
    }

    function confirmUnlock() {
        return confirm("Bạn có chắc chắn muốn mở sân con này không?");
    }

    document.addEventListener('DOMContentLoaded', function() {
        const filterSelect = document.querySelector('select[name="filter_san"]');
        const tableRows = document.querySelectorAll('#sancon-table tr');

        filterSelect.addEventListener('change', function() {
            const selectedSan = filterSelect.value;

            tableRows.forEach(row => {
                const tenSan = row.getAttribute('data-ten-san');

                // Hiển thị tất cả các hàng nếu "Tất cả" được chọn hoặc lọc theo tên sân
                if (selectedSan === "" || tenSan === selectedSan) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });

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
