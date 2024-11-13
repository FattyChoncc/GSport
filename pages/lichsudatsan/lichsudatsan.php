<form method="POST" action="">
    <div class="container-history ">
       <div class="left-panel">
            <div class="date-selection">
                <div class="time-label">Chọn ngày</div>
                <input type="date" id="dateInput" name="dateInput" value="<?php echo isset($_GET['dateInput']) ? $_GET['dateInput'] : ''; ?>">
            </div>

            <div class="time-selection">
                <div>
                    <select id="startTime-history" name="startTime">
                        <option value="" disabled selected style="display:none;">Chọn giờ bắt đầu</option>
                        <?php
                        for ($hour = 8; $hour <= 20; $hour++) {
                            $time = str_pad($hour, 2, '0', STR_PAD_LEFT) . ":00";
                            echo "<option value=\"$time\" " . (isset($_GET['startTime']) && $_GET['startTime'] == $time ? 'selected' : '') . ">$time</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <select id="endTime-history" name="endTime">
                        <option value="" disabled selected style="display:none;">Chọn giờ kết thúc</option>
                        <?php
                        for ($hour = 8; $hour <= 20; $hour++) {
                            $time = str_pad($hour, 2, '0', STR_PAD_LEFT) . ":00";
                            echo "<option value=\"$time\" " . (isset($_GET['endTime']) && $_GET['endTime'] == $time ? 'selected' : '') . ">$time</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div>
                <select id="courtType-history" name="courtType-history">
                    <option value="" disabled selected style="display:none;">Loại sân</option>
                    <option value="">Tất cả</option>
                    <option value="tennis" <?php echo isset($_GET['courtType-history']) && $_GET['courtType-history'] == 'tennis' ? 'selected' : ''; ?>>Tennis</option>
                    <option value="badminton" <?php echo isset($_GET['courtType-history']) && $_GET['courtType-history'] == 'badminton' ? 'selected' : ''; ?>>Cầu lông</option>
                    <option value="football" <?php echo isset($_GET['courtType-history']) && $_GET['courtType-history'] == 'football' ? 'selected' : ''; ?>>Bóng đá</option>
                    <option value="volleyball" <?php echo isset($_GET['courtType-history']) && $_GET['courtType-history'] == 'volleyball' ? 'selected' : ''; ?>>Bóng chuyền</option>
                    <option value="basketball" <?php echo isset($_GET['courtType-history']) && $_GET['courtType-history'] == 'basketball' ? 'selected' : ''; ?>>Bóng rổ</option>
                </select>
            </div>
        </div>

        <div class="right-panel">
            <h2>LỊCH SỬ LỊCH ĐẶT</h2>
            <div class="booking-history" id="bookingHistory">
                <?php
                include('admincp/connect/mysqlconnect.php');

                // Kiểm tra xem người dùng đã đăng nhập chưa
                if (!isset($_SESSION['idkh'])) {
                    echo "<p>Bạn cần đăng nhập để xem lịch sử đặt sân.</p>";
                } else {
                    // Nếu đã đăng nhập, tiếp tục lấy lịch sử đặt sân
                    $idkh = $_SESSION['idkh'];
                    $dateInput = isset($_GET['dateInput']) ? $_GET['dateInput'] : '';
                    $startTime = isset($_GET['startTime']) ? $_GET['startTime'] : '';
                    $endTime = isset($_GET['endTime']) ? $_GET['endTime'] : '';
                    $courtType = isset($_GET['courtType-history']) ? $_GET['courtType-history'] : '';

                    // Chuẩn bị câu lệnh SQL với các điều kiện
                    $sql = "SELECT L.idlich, L.TGbatdau, L.TGketthuc, L.Ngaydat, L.Tongtien, L.Trangthai, 
                                S.LoaiSan, S.Tensan AS TenSanCon, Q.Tensan AS TenSan, Q.Diachi 
                            FROM LichDat L
                            INNER JOIN Sancon S ON L.id = S.id
                            INNER JOIN QuanLySan Q ON S.idsan = Q.idsan
                            WHERE L.idkh = ?";

                    // Khởi tạo mảng điều kiện
                    $conditions = [];
                    $params = [$idkh];

                    // Thêm điều kiện lọc nếu có
                    if ($dateInput) {
                        $sql .= " AND L.Ngaydat = ?";
                        $conditions[] = $dateInput;
                    }

                    if ($startTime) {
                        $sql .= " AND L.TGbatdau >= ?";
                        $conditions[] = $startTime;
                    }

                    if ($endTime) {
                        $sql .= " AND L.TGketthuc <= ?";
                        $conditions[] = $endTime;
                    }

                    if ($courtType) {
                        $sql .= " AND S.LoaiSan = ?";
                        $conditions[] = $courtType;
                    }

                    // Kiểm tra nếu có điều kiện lọc, sau đó tạo chuỗi kiểu dữ liệu
                    if (count($conditions) > 0) {
                        $types = str_repeat('s', count($conditions)); // Tạo chuỗi kiểu dữ liệu (ví dụ 'ssss' cho 4 tham số)
                        array_unshift($conditions, $types); // Thêm kiểu dữ liệu vào đầu mảng
                    } else {
                        $types = 's'; // Thêm tham số cho idkh mặc định là kiểu 's' cho string
                        $conditions = [$types, $idkh];
                    }

                    // Chuẩn bị câu lệnh SQL với các điều kiện
                    $stmt = $mysqli->prepare($sql);
                    if ($stmt) {
                        // Gắn tham số vào câu lệnh SQL
                        call_user_func_array([$stmt, 'bind_param'], refValues($conditions));

                        // Thực thi câu lệnh SQL
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Kiểm tra nếu có kết quả
                        if ($result->num_rows > 0) {
                            // Hiển thị lịch sử đặt sân
                            while ($row = $result->fetch_assoc()) {
                                echo "<div class='booking-entry'>";
                                echo "<div class='booking-entry-header'>";
                                echo "<p>Tên sân: " . $row['TenSan'] . "</p>"; // Tên sân từ QuanLySan
                                echo "<p id='sum'>Tổng tiền: " . number_format($row['Tongtien']) . " VNĐ</p>";
                                echo "</div>";

                                echo "<div class='booking-details'>";
                                echo "<p>Mã lịch đặt: " . $row['idlich'] . "</p>";
                                echo "<p>Tên sân con: " . $row['TenSanCon'] . "</p>"; // Tên sân con từ Sancon
                                echo "<p>Loại sân: " . $row['LoaiSan'] . "</p>";
                                
                                // Kiểm tra trạng thái và áp dụng lớp CSS màu tương ứng
                                $statusClass = '';
                                $statusText = '';
                                switch ($row['Trangthai']) {
                                    case 'daxacnhan':
                                        $statusClass = 'status-confirmed'; // Màu xanh lá
                                        $statusText = 'Đã xác nhận';
                                        break;
                                    case 'dahuy':
                                        $statusClass = 'status-canceled'; // Màu đỏ
                                        $statusText = 'Đã hủy';
                                        break;
                                    case 'choxuly':
                                        $statusClass = 'status-pending'; // Màu vàng
                                        $statusText = 'Chờ xử lý';
                                        break;
                                    default:
                                        $statusClass = 'status-unknown'; // Mặc định, nếu không có trạng thái nào khớp
                                        $statusText = 'Không xác định';
                                        break;
                                }
                                echo "<p>Trạng thái: <span class='$statusClass'>$statusText</span></p>"; 
                                
                                echo "<p>Giờ bắt đầu: " . $row['TGbatdau'] . "</p>";
                                echo "<p>Giờ kết thúc: " . $row['TGketthuc'] . "</p>";
                                echo "<p>Ngày đặt: " . $row['Ngaydat'] . "</p>";
                                echo "<p>Địa chỉ: " . $row['Diachi'] . "</p>";
                                echo "</div>";
                                echo "</div>";
                            }
                        } else {
                            echo "<p>Không có lịch đặt nào trong khoảng thời gian bạn chọn.</p>";
                        }

                        $stmt->close();
                    } else {
                        echo "Có lỗi xảy ra khi truy vấn dữ liệu.";
                    }
                }

                // Hàm giúp tham chiếu mảng tham số cho bind_param
                function refValues($arr) {
                    $refs = [];
                    foreach ($arr as $key => $value) {
                        $refs[$key] = &$arr[$key];
                    }
                    return $refs;
                }
                ?>
            </div>         
        </div>
    </div>
</form>

<script>
    function filterBookings() {
        const dateInput = document.getElementById('dateInput').value;
        const startTime = document.getElementById('startTime-history').value;
        const endTime = document.getElementById('endTime-history').value;
        const courtType = document.getElementById('courtType-history').value;

        let url = `?dateInput=${dateInput}&startTime=${startTime}&endTime=${endTime}&courtType-history=${courtType}`;

        // Send GET request
        fetch('pages/lichsudatsan/ajax_filter.php' + url)
            .then(response => response.text())
            .then(data => {
                document.getElementById('bookingHistory').innerHTML = data;
            })
            .catch(error => console.error('Error:', error));
    }

    // Attach event listeners for filtering
    document.getElementById('dateInput').addEventListener('change', filterBookings);
    document.getElementById('startTime-history').addEventListener('change', filterBookings);
    document.getElementById('endTime-history').addEventListener('change', filterBookings);
    document.getElementById('courtType-history').addEventListener('change', filterBookings);

    // JavaScript dành riêng cho popup
    document.addEventListener('DOMContentLoaded', () => {
        // Mở popup đăng nhập
        document.querySelector('.signin-btn').addEventListener('click', () => {
            openPopup('loginPopup');
        });

        // Mở popup đăng ký
        document.querySelector('.signup-btn').addEventListener('click', () => {
            openPopup('signupPopup');
        });

        // Đóng popup khi nhấn ngoài
        window.addEventListener('click', (e) => {
            const loginPopup = document.getElementById('loginPopup');
            const signupPopup = document.getElementById('signupPopup');
            
            if (e.target === loginPopup) {
                closePopup('loginPopup');
            }
            
            if (e.target === signupPopup) {
                closePopup('signupPopup');
            }
        });
    });

    function openPopup(popupId) {
        document.getElementById(popupId).style.display = 'block';
    }

    function closePopup(popupId) {
        document.getElementById(popupId).style.display = 'none';
    }
</script>
