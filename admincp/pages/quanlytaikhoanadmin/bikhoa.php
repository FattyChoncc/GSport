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
                <button type="submit" id="searchButton">Tìm kiếm</button>
            </form>
        </div>
    </div>

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

            // Chỉ lấy các tài khoản có trạng thái 'choduyet'
            $sql = "SELECT A.idam, A.Tenadmin, A.Ngaysinh, A.SDT, A.Gmail, T.Tendn, T.Trangthai 
                    FROM ThongTinAdmin A
                    JOIN TaiKhoanAdmin T ON A.idam = T.idam
                    WHERE T.Trangthai = 'bikhoa'"; // Thay đổi câu lệnh SQL để lọc

            $result = $mysqli->query($sql);

            // Đặt giá trị mặc định cho trạng thái
            $trangThai = '';

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["idam"] . "</td>";
                    echo "<td>" . $row["Tenadmin"] . "</td>";
                    echo "<td>" . ($row["Ngaysinh"] ? date("Y-m-d", strtotime($row["Ngaysinh"])) : 'N/A') . "</td>";
                    echo "<td>" . $row["SDT"] . "</td>";
                    echo "<td>" . $row["Gmail"] . "</td>";
                    echo "<td>" . $row["Tendn"] . "</td>";

                    $trangThai = '<span class="inactive-status">Bị khóa</span>'; // Đặt trạng thái là 'Chờ Duyệt'
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
                    echo "<form action='pages/quanlyadmin/duyettk.php' method='POST' style='display:inline;' onsubmit='return confirmActivation()'>
                            <input type='hidden' name='id' value='" . $row["idam"] . "'>
                            <button type='submit' id='approve_button' style='background: none; border: none; padding: 0; color: green;'>
                                <i class='fas fa-check' title='Duyệt'></i>
                            </button>
                          </form>";
                    echo "<form action='pages/quanlyadmin/khoatk.php' method='POST' style='display:inline;' onsubmit='return confirmDeactivation()'>
                            <input type='hidden' name='id' value='" . $row["idam"] . "'>
                            <button type='submit' id='lock_button' style='background: none; border: none; padding: 0; color: red;'>
                                <i class='fas fa-ban' title='Khóa'></i>
                            </button>
                          </form>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>Không có dữ liệu để hiển thị</td></tr>";
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
</script>
