<?php
// Kết nối cơ sở dữ liệu
include 'admincp/connect/mysqlconnect.php';

// Khởi tạo tham số lọc và phân trang
$tinh = $_GET['tinh'] ?? 0;
$quan = $_GET['quan'] ?? 0;
$phuong = $_GET['phuong'] ?? 0;
$page = $_GET['page'] ?? 1;
$limit = 8;
$offset = ($page - 1) * $limit;

// Xây dựng câu truy vấn có điều kiện
$query = "SELECT * FROM QuanLySan WHERE 1";
if ($tinh > 0) $query .= " AND tinh_id = $tinh";
if ($quan > 0) $query .= " AND quan_id = $quan";
if ($phuong > 0) $query .= " AND phuong_id = $phuong";
$query .= " LIMIT $limit OFFSET $offset";

$result = $mysqli->query($query);

// Truy vấn để lấy tổng số sân cho phân trang
$totalQuery = "SELECT COUNT(*) as total FROM QuanLySan WHERE 1";
if ($tinh > 0) $totalQuery .= " AND tinh_id = $tinh";
if ($quan > 0) $totalQuery .= " AND quan_id = $quan";
if ($phuong > 0) $totalQuery .= " AND phuong_id = $phuong";

$totalResult = $mysqli->query($totalQuery);
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);
?>

<div class="container_booking">
    <div class="css_select_div">
        <!-- Dropdown Lọc -->
        <select class="css_select" id="tinh" name="tinh" title="Chọn Tỉnh Thành">
            <option value="0" disabled selected style="display:none;">Tỉnh Thành</option>
            <!-- Các option sẽ được thêm qua JavaScript -->
        </select> 
        <select class="css_select" id="quan" name="quan" title="Chọn Quận Huyện">
            <option value="0" disabled selected style="display:none;">Quận Huyện</option>
        </select> 
        <select class="css_select" id="phuong" name="phuong" title="Chọn Phường Xã">
            <option value="0" disabled selected style="display:none;">Phường Xã</option>
        </select>
    </div>

    <!-- Hiển thị danh sách sân -->
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $idsan = $row['idsan']; // Lấy idsan từ bảng QuanLySan
            echo '<a href="index.php?action=chonsan&idsan=' . $idsan . '" class="container_card">'; // Truyền idsan vào URL
            echo '<div class="card">';
            echo '<h1>TÊN SÂN: ' . htmlspecialchars($row['Tensan']) . '</h1>';
            echo '<p id="time">Thời gian: ' . htmlspecialchars($row['TGmo']) . ' - ' . htmlspecialchars($row['TGdong']) . '</p>';
            echo '<p id="address">Địa chỉ: ' . htmlspecialchars($row['Diachi']) . '</p>';
            echo '<p id="info">Chi tiết: ' . htmlspecialchars($row['Chitiet']) . '</p>';
            
            $hinhanhPath = 'admincp/pages/quanlysan/uploads/' . htmlspecialchars($row['Hinhanh']);
            echo "<td><img src='" . $hinhanhPath . "' alt='Hình ảnh'></td>";
            echo '</div>';
            echo '</a>';
        }
    } else {
        echo '<p>Không có sân phù hợp với lựa chọn của bạn.</p>';
    }
    ?>
</div>

<!-- Hiển thị phân trang -->
<div class="pagination">
    <?php
    // Hiển thị nút "Previous"
    if ($page > 1) {
        echo '<a href="?action=datsan&tinh=' . $tinh . '&quan=' . $quan . '&phuong=' . $phuong . '&page=' . ($page - 1) . '"><</a> ';
    }

    // Hiển thị các trang số
    for ($i = 1; $i <= $totalPages; $i++) {
        echo '<a href="?action=datsan&tinh=' . $tinh . '&quan=' . $quan . '&phuong=' . $phuong . '&page=' . $i . '"';
        if ($i == $page) echo ' class="active"'; // Trang hiện tại được đánh dấu
        echo '>' . $i . '</a> ';
    }

    // Hiển thị nút "Next"
    if ($page < $totalPages) {
        echo '<a href="?action=datsan&tinh=' . $tinh . '&quan=' . $quan . '&phuong=' . $phuong . '&page=' . ($page + 1) . '">></a>';
    }
    ?>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Lấy danh sách tỉnh thành
        fetch('https://esgoo.net/api-tinhthanh/1/0.htm')
            .then(response => response.json())
            .then(data_tinh => {
                if (data_tinh.error === 0) {
                    const tinhSelect = document.getElementById("tinh");
                    data_tinh.data.forEach(val_tinh => {
                        const option = document.createElement("option");
                        option.value = val_tinh.id;
                        option.textContent = val_tinh.full_name;
                        tinhSelect.appendChild(option);
                    });

                    // Sự kiện thay đổi của select tỉnh
                    tinhSelect.addEventListener("change", function () {
                        const idtinh = tinhSelect.value;
                        
                        // Lấy danh sách quận huyện
                        fetch(`https://esgoo.net/api-tinhthanh/2/${idtinh}.htm`)
                            .then(response => response.json())
                            .then(data_quan => {
                                if (data_quan.error === 0) {
                                    const quanSelect = document.getElementById("quan");
                                    quanSelect.innerHTML = '<option value="0">Quận Huyện</option>'; // Reset option
                                    document.getElementById("phuong").innerHTML = '<option value="0">Phường Xã</option>';

                                    data_quan.data.forEach(val_quan => {
                                        const option = document.createElement("option");
                                        option.value = val_quan.id;
                                        option.textContent = val_quan.full_name;
                                        quanSelect.appendChild(option);
                                    });

                                    // Sự kiện thay đổi của select quận
                                    quanSelect.addEventListener("change", function () {
                                        const idquan = quanSelect.value;

                                        // Lấy danh sách phường xã
                                        fetch(`https://esgoo.net/api-tinhthanh/3/${idquan}.htm`)
                                            .then(response => response.json())
                                            .then(data_phuong => {
                                                if (data_phuong.error === 0) {
                                                    const phuongSelect = document.getElementById("phuong");
                                                    phuongSelect.innerHTML = '<option value="0">Phường Xã</option>'; // Reset option

                                                    data_phuong.data.forEach(val_phuong => {
                                                        const option = document.createElement("option");
                                                        option.value = val_phuong.id;
                                                        option.textContent = val_phuong.full_name;
                                                        phuongSelect.appendChild(option);
                                                    });
                                                }
                                            });
                                    });
                                }
                            });
                    });
                }
            });
    });
    document.addEventListener('DOMContentLoaded', () => {
    // Mở popup đăng nhập
    document.querySelector('.signin-btn').addEventListener('click', (e) => {
        e.preventDefault();  // Ngừng việc gửi form và làm trang tải lại
        openPopup('loginPopup');
    });

    // Mở popup đăng ký
    document.querySelector('.signup-btn').addEventListener('click', (e) => {
        e.preventDefault();  // Ngừng việc gửi form và làm trang tải lại
        openPopup('signupPopup');
    });

    // Đóng popup khi nhấn ngoài
    window.addEventListener('click', (e) => {
        const loginPopup = document.getElementById('loginPopup');
        const signupPopup = document.getElementById('signupPopup');
        
        // Nếu người dùng click vào popup ngoài các form login và signup, đóng chúng
        if (e.target === loginPopup) {
            closePopup('loginPopup');
        }

        if (e.target === signupPopup) {
            closePopup('signupPopup');
        }
    });

    // Mở form đăng ký từ form đăng nhập và ẩn form đăng nhập trước
    document.querySelector('#loginPopup .signup-link').addEventListener('click', () => {
        closePopup('loginPopup');
        openPopup('signupPopup');
    });

    // Mở form đăng nhập từ form đăng ký và ẩn form đăng ký trước
    document.querySelector('#signupPopup .login-link').addEventListener('click', () => {
        closePopup('signupPopup');
        openPopup('loginPopup');
    });

    // Toggle password visibility for login form
    const toggleLoginPassword = document.getElementById('toggleLoginPassword');
    const loginPasswordInput = document.getElementById('loginPassword');
    toggleLoginPassword.addEventListener('click', () => {
        const type = loginPasswordInput.type === 'password' ? 'text' : 'password';
        loginPasswordInput.type = type;
        toggleLoginPassword.classList.toggle('fa-eye-slash');
    });

    // Toggle password visibility for register form
    const toggleRegisterPassword = document.getElementById('toggleRegisterPassword');
    const registerPasswordInput = document.getElementById('registerPassword');
        toggleRegisterPassword.addEventListener('click', () => {
            const type = registerPasswordInput.type === 'password' ? 'text' : 'password';
            registerPasswordInput.type = type;
            toggleRegisterPassword.classList.toggle('fa-eye-slash');
        });
    });

    function openPopup(popupId) {
        document.getElementById(popupId).style.display = 'block';
    }

    function closePopup(popupId) {
        document.getElementById(popupId).style.display = 'none';
    }

</script>