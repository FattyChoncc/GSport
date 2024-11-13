<div class="container_admin">
    <h1>Danh Sách Tài Khoản Admin</h1>

    <div class="filter-search-wrapper">
        <!-- Dropdown để chọn trạng thái tài khoản -->
        <div class="filter-container">
            <select id="statusFilter" onchange="filterByStatus()">
                <option value="" disabled selected style="display:none;">Trạng thái</option>
                <option value="theodoitaikhoanadmin">Tất cả</option>
                <option value="hoatdong">Hoạt Động</option>
                <option value="choduyet">Chờ Duyệt</option>
                <option value="bikhoa">Bị khóa</option>
            </select>
        </div>

        <!-- Search form bên góc phải -->
        <div class="search-container">
            <form action="" method="GET">
                <input type="hidden" name="action" value="quanlytaikhoanadmin">
                <input type="hidden" name="query" value="theodoitaikhoanadmin">
                <input type="text" name="search" placeholder="Tìm kiếm theo tên..." id="searchInput">
                <button type="submit" id="searchButton"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>
    </div>

    <?php
    // Hiển thị thông báo thành công (nếu có)
    if (isset($_SESSION['success-message'])) {
        echo "<div class='success-message' id='successMessage'>" . $_SESSION['success-message'] . "</div>";
        unset($_SESSION['success-message']); // Xóa thông báo sau khi hiển thị
    }

    // Hiển thị thông báo lỗi (nếu có)
    if (isset($_SESSION['error-message'])) {
        echo "<div class='error-message' id='errorMessage'>" . $_SESSION['error-message'] . "</div>";
        unset($_SESSION['error-message']); // Xóa thông báo sau khi hiển thị
    }
    ?>

    <table>
        <thead>
            <tr>
                <th>ID Admin</th>
                <th>Tên Admin</th>
                <th>Ngày Sinh</th>
                <th>Số Điện Thoại</th>
                <th>Email</th>
                <th>Tên Đăng Nhập</th>
                <th>Trạng Thái Tài Khoản</th>
                <th>Thao Tác</th>
                <th>Duyệt - Khóa Tài Khoản</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include 'connect/mysqlconnect.php';

            $trangthai = isset($_GET['status']) ? $_GET['status'] : '';
            $search = isset($_GET['search']) ? $_GET['search'] : '';

            // SQL query with optional search filter
            $sql = "SELECT A.idam, A.Tenadmin, A.Ngaysinh, A.SDT, A.Gmail, T.Tendn, T.Trangthai 
                    FROM ThongTinAdmin A
                    JOIN TaiKhoanAdmin T ON A.idam = T.idam";

            if ($trangthai || $search) {
                $sql .= " WHERE 1=1";
                if ($trangthai) {
                    $sql .= " AND T.Trangthai = '$trangthai'";
                }
                if ($search) {
                    $sql .= " AND A.Tenadmin LIKE '%$search%'";
                }
            }

            $result = $mysqli->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["idam"] . "</td>";
                    echo "<td>" . $row["Tenadmin"] . "</td>";
                    echo "<td>" . ($row["Ngaysinh"] ? date("Y-m-d", strtotime($row["Ngaysinh"])) : 'N/A') . "</td>";
                    echo "<td>" . $row["SDT"] . "</td>";
                    echo "<td>" . $row["Gmail"] . "</td>";
                    echo "<td>" . $row["Tendn"] . "</td>";
                     
                    // Các trạng thái hiển thị trong bảng
                    $trangThai = '';

                    if ($row["Trangthai"] === 'hoatdong') {
                        $trangThai = '<span class="active-status">Hoạt Động</span>';
                    } elseif ($row["Trangthai"] === 'choduyet') {
                        $trangThai = '<span class="pending-status">Chờ Duyệt</span>';
                    } elseif ($row["Trangthai"] === 'bikhoa') {
                        $trangThai = '<span class="inactive-status">Bị Khóa</span>';
                    }
                    echo "<td>" . $trangThai . "</td>";

                    // Cột Thao Tác với nút Sửa
                    echo "<td>
                            <form action='?action=quanlytaikhoanadmin&query=suaadmin' method='POST' style='display:inline;'>
                                <input type='hidden' name='id' value='" . $row["idam"] . "'>
                                <button type='submit' id='edit_button' style='background: none; border: none; padding: 0;'>
                                    <i class='fas fa-edit' title='Sửa'></i>
                                </button>
                            </form>
                          </td>";

                    // Cột Duyệt/Khóa Tài Khoản với các nút điều khiển
                    echo "<td>";
                    
                    if ($row["Trangthai"] === 'hoatdong') {
                        echo "<button type='button' id='approve_button' style='background: none; border: none; padding: 0; color: grey;' disabled>
                                <i class='fas fa-check' title='Duyệt'></i>
                              </button>";
                        echo "<form action='pages/quanlytaikhoanadmin/khoatk.php' method='POST' style='display:inline;' onsubmit='return confirmDeactivation()'>
                                <input type='hidden' name='id' value='" . $row["idam"] . "'>
                                <button type='submit' id='lock_button' style='background: none; border: none; padding: 0; color: red;'>
                                    <i class='fas fa-ban' title='Khóa'></i>
                                </button>
                              </form>";
                    } else {
                        echo "<form action='pages/quanlytaikhoanadmin/duyettk.php' method='POST' style='display:inline;' onsubmit='return confirmActivation()'>
                                <input type='hidden' name='id' value='" . $row["idam"] . "'>
                                <button type='submit' id='approve_button' style='background: none; border: none; padding: 0; color: green;'>
                                    <i class='fas fa-check' title='Duyệt'></i>
                                </button>
                              </form>";
                        echo "<button type='button' id='lock_button' style='background: none; border: none; padding: 0; color: grey;' disabled>
                                <i class='fas fa-ban' title='Khóa'></i>
                              </button>";
                    }

                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>Không có tài khoản admin nào phù hợp.</td></tr>";
            }

            $mysqli->close();
            ?>
        </tbody>
    </table>
</div>

<script>
    function confirmActivation() {
        return confirm("Bạn có chắc chắn muốn duyệt tài khoản này không?");
    }

    function confirmDeactivation() {
        return confirm("Bạn có chắc chắn muốn khóa tài khoản này không?");
    }

    function filterByStatus() {
        const filter = document.getElementById('statusFilter').value;
        window.location.href = window.location.pathname + "?action=quanlytaikhoanadmin&query=" + filter;
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
