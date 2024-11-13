<?php
// Đảm bảo bạn đã kết nối đến cơ sở dữ liệu
include 'connect/mysqlconnect.php'; // Thay đổi nếu bạn có tệp kết nối khác

// Kiểm tra xem kết nối có thành công hay không
if (!$mysqli) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Khởi tạo biến ngày bắt đầu, ngày kết thúc, tìm kiếm khách hàng và chọn tháng
$startDate = isset($_POST['startDate']) ? $_POST['startDate'] : '';
$endDate = isset($_POST['endDate']) ? $_POST['endDate'] : '';
$customerSearch = isset($_POST['customerSearch']) ? $_POST['customerSearch'] : '';
$selectedMonth = isset($_POST['month']) ? $_POST['month'] : '';

// Kiểm tra xem nút reset có được nhấn hay không
if (isset($_POST['reset'])) {
    // Nếu nút reset được nhấn, đặt lại ngày bắt đầu, ngày kết thúc, tìm kiếm và chọn tháng
    $startDate = '';
    $endDate = '';
    $customerSearch = '';
    $selectedMonth = '';
}

// Truy vấn doanh thu, giới hạn 20 hàng và chỉ hiển thị trạng thái 'daxacnhan' và 'choxuly'
$query = "
    SELECT 
        q.idhoadon,
        l.idlich,
        k.Tenkh,
        l.TGbatdau,
        l.TGketthuc,
        l.Ngaydat,
        q.Tongtien AS TongTienHoaDon,
        q.Ngaythanhtoan,
        l.Trangthai
    FROM 
        QuanLyDoanhThu q
    JOIN 
        LichDat l ON q.idlich = l.idlich
    JOIN 
        ThongTinKhachHang k ON l.idkh = k.idkh
    WHERE 
        l.Trangthai IN ('daxacnhan', 'choxuly')
";

// Thêm điều kiện lọc theo ngày nếu có
if ($startDate) {
    $query .= " AND l.Ngaydat >= '$startDate'";
}
if ($endDate) {
    $query .= " AND l.Ngaydat <= '$endDate'";
}

// Thêm điều kiện lọc theo tên khách hàng nếu có
if ($customerSearch) {
    $query .= " AND k.Tenkh LIKE '%" . $mysqli->real_escape_string($customerSearch) . "%'";
}

$query .= " LIMIT 20";

$result = $mysqli->query($query);

// Kiểm tra xem truy vấn có thành công hay không
if (!$result) {
    die("Lỗi truy vấn: " . $mysqli->error);
}

// Lấy kết quả
$results = $result->fetch_all(MYSQLI_ASSOC);

// Tính tổng doanh thu
$totalRevenue = 0;
foreach ($results as $item) {
    $totalRevenue += (float)$item['TongTienHoaDon'];
}

// Nếu người dùng chọn một tháng, truy vấn tổng doanh thu cho tháng đó khi nhấn nút "Thống Kê Doanh Thu"
$monthRevenue = 0;
if (isset($_POST['calculateRevenueBtn']) && $selectedMonth) {
    $monthRevenueQuery = "
        SELECT 
            SUM(q.Tongtien) AS totalRevenue
        FROM 
            QuanLyDoanhThu q
        WHERE 
            MONTH(q.Ngaythanhtoan) = '$selectedMonth' AND q.Ngaythanhtoan IS NOT NULL
    ";

    $monthRevenueResult = $mysqli->query($monthRevenueQuery);
    $monthRevenue = $monthRevenueResult->fetch_assoc()['totalRevenue'] ?: 0;
}

// Truy vấn để lấy doanh thu theo từng tháng
$monthlyRevenues = [];
for ($i = 1; $i <= 12; $i++) {
    $monthQuery = "
        SELECT SUM(Tongtien) AS totalRevenue
        FROM QuanLyDoanhThu
        WHERE MONTH(Ngaythanhtoan) = $i AND Ngaythanhtoan IS NOT NULL
    ";
    $monthResult = $mysqli->query($monthQuery);
    $monthlyRevenues[$i] = $monthResult->fetch_assoc()['totalRevenue'] ?: 0;
}
?>

<div class="container_doanhthu">
<h1>Bảng Doanh Thu</h1>

<!-- Form để lọc theo ngày đặt và tìm kiếm khách hàng -->
<form method="POST" style="margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
        <div style="flex: 1; margin-right: 10px;">
            <label class="label" for="startDate">Từ Ngày:</label>
            <input type="date" id="startDate" name="startDate" value="<?= htmlspecialchars($startDate) ?>">
        </div>
        <div style="flex: 1;">
            <label class="label" for="endDate">Đến Ngày:</label>
            <input type="date" id="endDate" name="endDate" value="<?= htmlspecialchars($endDate) ?>">
        </div>
    </div>

    <div style="margin-bottom: 10px;">
        <label class="label" for="customerSearch">Tìm Kiếm Khách Hàng:</label>
        <input type="text" id="customerSearch" name="customerSearch" value="<?= htmlspecialchars($customerSearch) ?>">
    </div>

    <div style="display: flex; justify-content: space-between;">
        <div style="display: flex; justify-content: flex-start;">
            <button type="submit" id="search-btn" name="search" title="Tìm Kiếm">
            <i class="fas fa-search"></i> <!-- Biểu tượng tìm kiếm -->
            </button>
            <button type="submit" id="reset-btn" name="reset" title="Reset">
                <i class="fas fa-redo"></i> <!-- Biểu tượng reset -->
            </button>
        </div>

        <div style="display: flex; justify-content: flex-end; align-items: center;">
            <div style="margin-right: 10px;">
                <select id="month" name="month">
                    <option value="">--Chọn Tháng--</option>
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?= $i ?>" <?= ($selectedMonth == $i) ? 'selected' : '' ?>>Tháng <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <button type="submit" id="calculateRevenueBtn" name="calculateRevenueBtn">Thống Kê Doanh Thu</button>
        </div>
    </div>
</form>

<!-- Kết quả thống kê doanh thu -->
<div id="totalRevenueDisplay" style="margin-top: 20px; font-weight: bold;">
    Tổng doanh thu tháng <?= $selectedMonth ?>: <span><?= number_format($monthRevenue, 0, '.', ',') ?> VNĐ</span>
</div>

    <!-- Bảng doanh thu -->
<table id="revenueTable">
    <thead>
        <tr>
            <th>ID Hóa Đơn</th>
            <th>ID Lịch Đặt</th>
            <th>Tên Khách Hàng</th>
            <th>Thời Gian Bắt Đầu</th>
            <th>Thời Gian Kết Thúc</th>
            <th>Ngày Đặt</th>
            <th>Tổng Tiền Hóa Đơn</th>
            <th>Ngày Thanh Toán</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($results)): ?>
            <tr>
                <td colspan="8" style="text-align: center;">Không có khách hàng thích hợp</td>
            </tr>
        <?php else: ?>
            <?php foreach ($results as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['idhoadon']) ?></td>
                    <td><?= htmlspecialchars($item['idlich']) ?></td>
                    <td><?= htmlspecialchars($item['Tenkh']) ?></td>
                    <td><?= htmlspecialchars($item['TGbatdau']) ?></td>
                    <td><?= htmlspecialchars($item['TGketthuc']) ?></td>
                    <td><?= htmlspecialchars($item['Ngaydat']) ?></td>
                    <td><?= number_format((float)$item['TongTienHoaDon'], 0, '.', ',') ?></td>
                    <td><?= htmlspecialchars($item['Ngaythanhtoan']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

    <!-- Biểu đồ doanh thu theo tháng -->
    <canvas id="revenueChart" width="400" height="200"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Vẽ biểu đồ doanh thu theo tháng
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
            datasets: [{
                label: 'Doanh Thu Theo Tháng (VNĐ)',
                data: <?= json_encode(array_values($monthlyRevenues)) ?>, // Dữ liệu doanh thu theo tháng
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
