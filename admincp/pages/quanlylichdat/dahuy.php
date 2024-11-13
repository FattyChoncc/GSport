<div class="container_lichdat">
    <h1>Lịch Đặt Đã Xác Nhận</h1>

    <!-- Container cho Dropdown và thanh tìm kiếm -->
    <div class="filter-container">
        <div class="status-dropdown">
            <label for="statusFilter">Chọn Trạng Thái:</label>
            <select id="statusFilter" onchange="filterByStatus()">
                <option value="" disabled selected style="display:none;">Trạng thái</option>
                <option value="xacnhanlich">Lịch Chờ Xác Nhận</option>
                <option value="dahuy">Lịch Đã Hủy</option>          
                <option value="daxacnhan">Lịch Đã Xác Nhận</option>
            </select>
        </div>

        <!-- Thanh tìm kiếm khách hàng -->
        <div class="search-container">
            <input type="text" id="customerSearch" placeholder="Tìm kiếm khách hàng...">
            <button onclick="searchCustomer()"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>
    </div>

    <!-- Bảng hiển thị lịch đặt -->
    <table class="lich-dat-table">
        <thead>
            <tr>
                <th>ID Lịch</th>
                <th>Tên Sân Quản Lý</th>
                <th>Tên Sân Con</th>
                <th>Loại Sân</th>
                <th>Tên Khách Hàng</th>
                <th>Ngày Đặt</th>
                <th>Bắt Đầu</th>
                <th>Kết Thúc</th>
                <th>Tổng Tiền</th>
                <th>Phương Thức Thanh Toán</th> <!-- Thêm cột Phương Thức Thanh Toán -->
                <th>Thao Tác</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <?php
            // Kết nối đến cơ sở dữ liệu
            include 'connect/mysqlconnect.php';

            // Xác định trạng thái mặc định
            $trangthai = isset($_GET['status']) ? $_GET['status'] : 'dahuy';

            // Truy vấn dữ liệu từ bảng LichDat, SanCon, QuanLySan, ThongTinKhachHang và PTThanhToan
            $sql = "
                SELECT 
                    L.idlich, 
                    QS.Tensan AS TenSanQuanLy, 
                    SC.Tensan AS TenSanCon, 
                    SC.Loaisan AS LoaiSan, 
                    KH.Tenkh AS TenKhachHang, 
                    L.Ngaydat, 
                    L.TGbatdau, 
                    L.TGketthuc, 
                    L.Tongtien, 
                    PT.Tenpt AS PhuongThucThanhToan  -- Thêm phương thức thanh toán từ bảng PTThanhToan
                FROM 
                    LichDat L
                JOIN 
                    SanCon SC ON L.id = SC.id
                JOIN 
                    QuanLySan QS ON SC.idsan = QS.idsan
                JOIN 
                    ThongTinKhachHang KH ON L.idkh = KH.idkh
                JOIN 
                    PTThanhToan PT ON L.idpt = PT.idpt  -- Liên kết với bảng PTThanhToan
                WHERE 
                    L.Trangthai = ?
            ";

            // Chuẩn bị và thực thi truy vấn
            if ($stmt = $mysqli->prepare($sql)) {
                $stmt->bind_param("s", $trangthai);
                $stmt->execute();
                $result = $stmt->get_result();

                // Kiểm tra và hiển thị dữ liệu
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["idlich"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["TenSanQuanLy"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["TenSanCon"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["LoaiSan"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["TenKhachHang"]) . "</td>";
                        echo "<td>" . date("Y-m-d", strtotime($row["Ngaydat"])) . "</td>";
                        echo "<td>" . date("H:i", strtotime($row["TGbatdau"])) . "</td>";
                        echo "<td>" . date("H:i", strtotime($row["TGketthuc"])) . "</td>";
                        echo "<td>" . number_format($row["Tongtien"], 0, ',', '.') . " VND</td>";
                        echo "<td>" . htmlspecialchars($row["PhuongThucThanhToan"]) . "</td>";  // Hiển thị phương thức thanh toán
                        echo "<td>
                                <div class='action-buttons'>
                                    <form action='pages/quanlylichdat/huylich.php' method='POST' onsubmit='return confirmAction(\"Điều này sẽ chuyển lịch vào danh sách đã hủy. Bạn có chắc chắn không?\");'>
                                        <input type='hidden' name='idlich' value='" . htmlspecialchars($row["idlich"]) . "'>
                                        <button type='submit' class='cancel-btn' style='background: none; border: none; padding: 0;'>
                                            <i class='fas fa-ban' title='Hủy' style='color: red; font-size: 18px;'></i>
                                        </button>
                                    </form>
                                </div>
                            </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11'>Không có dữ liệu để hiển thị</td></tr>";
                }
                $stmt->close();
            }

            // Đóng kết nối
            $mysqli->close();
            ?>
        </tbody>
    </table>
</div>

<script>
    function confirmAction(message) {
        return confirm(message);
    }

    function filterByStatus() {
        const filter = document.getElementById('statusFilter').value;
        window.location.href = window.location.pathname + "?action=quanlylichdat&query=" + filter;
    }

    function searchCustomer() {
        const input = document.getElementById('customerSearch').value.toLowerCase();
        const rows = document.querySelectorAll('#tableBody tr');

        rows.forEach(row => {
            const customerName = row.cells[4].textContent.toLowerCase(); // Cột tên khách hàng
            if (customerName.includes(input)) {
                row.style.display = ''; // Hiện hàng
            } else {
                row.style.display = 'none'; // Ẩn hàng
            }
        });
    }
</script>
