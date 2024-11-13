<?php
// Kết nối cơ sở dữ liệu
include 'admincp/connect/mysqlconnect.php';

// Lấy id của sân cha từ URL
$san_id = isset($_GET['idsan']) ? (int)$_GET['idsan'] : 0; // Lấy giá trị idsan từ URL

// Lấy giá trị loại sân từ URL
$courtType = isset($_GET['courtType']) ? $_GET['courtType'] : '';

// Kiểm tra nếu id sân cha hợp lệ
if ($san_id > 0) {
    // Truy vấn thông tin sân cha
    $sanQuery = "SELECT * FROM QuanLySan WHERE idsan = $san_id";
    $sanResult = $mysqli->query($sanQuery);
    
    if ($sanResult->num_rows > 0) {
        $sanData = $sanResult->fetch_assoc();
        
        // Câu truy vấn cho sân con, nếu courtType là 'all' thì không lọc
        $courtQuery = "SELECT * FROM Sancon WHERE idsan = $san_id";
        if ($courtType && $courtType != 'all') {
            $courtQuery .= " AND Loaisan = '$courtType'"; // Lọc theo loại sân nếu có
        }

        $courtResult = $mysqli->query($courtQuery);
    } else {
        echo "Không tìm thấy sân!";
        exit;
    }
} else {
    // Nếu không có id sân cha
    echo "Không tìm thấy sân!";
    exit;
}
?>

<div class="container_picking">
    <div class="left-section">
        <!-- Hiển thị thông tin sân cha -->
        <h1 class="court-title">TÊN SÂN: <?php echo htmlspecialchars($sanData['Tensan']); ?></h1>
        <div class="details">
            <p>Thời gian mở: <?php echo htmlspecialchars($sanData['TGmo']); ?> - <?php echo htmlspecialchars($sanData['TGdong']); ?></p>
            <p id="address">Địa chỉ: <?php echo htmlspecialchars($sanData['Diachi']); ?></p>
            <p>Chi tiết: <?php echo htmlspecialchars($sanData['Chitiet']); ?></p>
        </div>
    </div>

    <div class="right-section">
        <p>Chọn khung giờ:</p>
        <div class="time-selection">       
            <select id="startTime">
                <option value="" disabled selected style="display:none;">Thời gian bắt đầu</option>
            </select>
            <select id="endTime">
                <option value="" disabled selected style="display:none;">Thời gian kết thúc</option>
            </select>
        </div>
        <hr class="divider">
        
        <!-- Lọc theo loại sân -->
        <div class="court-type-selection">
            <select id="courtType" onchange="filterCourts()">
                <option value="" disabled selected style="display:none;">Loại sân</option>
                <option value="all">Tất cả</option> <!-- Thêm option "Tất cả" -->
                <option value="tennis">Tennis</option>
                <option value="Bóng đá">Bóng đá</option>
                <option value="Bóng chuyền">Bóng chuyền</option>
                <option value="Bóng rổ">Bóng rổ</option>
                <option value="Cầu lông">Cầu lông</option>
            </select>
        </div>

        <div class="courts-grid">
            <?php
            // Hiển thị các sân con dưới dạng các button
            if ($courtResult->num_rows > 0) {
                while ($court = $courtResult->fetch_assoc()) {
                    $courtId = $court['id'];
                    $courtStatus = $court['Trangthaidatsan']; // Lấy trạng thái đặt sân

                    // Kiểm tra trạng thái và xác định màu sắc button
                    if ($courtStatus == 'dadat') {
                        $statusClass = 'booked'; // Màu đỏ
                        $disabled = 'disabled'; // Không thể tương tác
                    } else {
                        $statusClass = 'available'; // Màu xanh lục
                        $disabled = ''; // Có thể tương tác
                    }
                    
                    // Hiển thị button cho sân con
                    echo '<button class="court-btn ' . $statusClass . '" ' . $disabled . ' onclick="showCourtConfirmation(' . $court['id'] . ')">' . htmlspecialchars($court['Tensan']) . '</button>';
                }
            } else {
                echo '<p>Không có sân.</p>';
            }
            ?>
        </div>


        <!-- Đây là nơi sẽ hiển thị xác nhận đặt sân khi bấm vào button -->
        <div id="courtConfirmation" class="court-confirmation" style="display:none;">
            <!-- Nội dung xác nhận sẽ được hiển thị ở đây -->
        </div>

        <button class="payment-btn">Thanh toán</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startTimeSelect = document.getElementById('startTime');
        const endTimeSelect = document.getElementById('endTime');
        
        //Tạo thời gian từ 7h đến 20h
        for (let hour = 7; hour <= 20; hour++) {
            const timeStr = `${hour.toString().padStart(2, '0')}:00`;
            
            const startOption = document.createElement('option');
            startOption.value = timeStr;
            startOption.textContent = timeStr;
            startTimeSelect.appendChild(startOption);
            
            const endOption = document.createElement('option');
            endOption.value = timeStr;
            endOption.textContent = timeStr;
            endTimeSelect.appendChild(endOption);
        }
    });

    function filterCourts() {
    const courtType = document.getElementById('courtType').value;
    window.location.href = `?action=chonsan&idsan=<?php echo $san_id; ?>&courtType=${courtType}`; // Refresh page with courtType filter
    }

    // Hiển thị xác nhận đặt sân khi người dùng bấm vào button
    function showCourtConfirmation(courtId) {
    // Lấy giá trị thời gian từ dropdowns
    const startTime = document.getElementById('startTime').value;
    const endTime = document.getElementById('endTime').value;

    // Kiểm tra nếu chưa chọn thời gian bắt đầu hoặc kết thúc
    if (!startTime || !endTime) {
        alert("Vui lòng chọn thời gian bắt đầu và kết thúc!");
        return; // Dừng hàm nếu chưa chọn thời gian
    }

    // Chuyển thời gian bắt đầu và kết thúc thành đối tượng Date để so sánh
    const startDate = new Date(`1970-01-01T${startTime}:00`);
    const endDate = new Date(`1970-01-01T${endTime}:00`);

    // Kiểm tra nếu thời gian kết thúc cách thời gian bắt đầu ít nhất 1 giờ
    const timeDifference = (endDate - startDate) / (1000 * 60 * 60); // Chuyển đổi thành giờ

    if (timeDifference < 1) {
        alert("Thời gian kết thúc phải cách thời gian bắt đầu ít nhất 1 tiếng!");
        return; // Dừng hàm nếu không đủ 1 giờ
    }

    // Lấy thông tin sân từ server bằng AJAX
    fetch(`pages/datsan/get_court_info.php?courtId=${courtId}`)
        .then(response => response.json())
        .then(data => {
            if (data) {
                const confirmationDiv = document.getElementById('courtConfirmation');
                confirmationDiv.style.display = 'block'; // Hiển thị thông tin xác nhận

                const price = data.Gia; // Lấy giá từ dữ liệu trả về
                const formattedPrice = new Intl.NumberFormat('vi-VN').format(price); // Định dạng giá theo chuẩn Việt Nam

                confirmationDiv.innerHTML = `
                    <h3>Thông tin xác nhận đặt sân</h3>
                    <div class="court-info">
                        <div class="info-row">
                            <p><strong>Tên sân:</strong> ${data.Tensan}</p>
                            <p><strong>Loại sân:</strong> ${data.Loaisan}</p>
                        </div>
                        <div class="info-row">
                            <p><strong>Thời gian bắt đầu:</strong> ${startTime}</p>
                            <p><strong>Thời gian kết thúc:</strong> ${endTime}</p>
                        </div>
                        <div class="info-row">
                            <p><strong>Giá:</strong> ${formattedPrice} VND</p>
                        </div>
                        <div class="info-row">
                            <p><strong>Hình ảnh:</strong><br><img src="${data.Hinhanh}" alt="Hình ảnh sân" style="width: 200px; height: auto; margin-top: 10px;"></p>
                        </div>
                    </div>
                `;
            }
        })
        .catch(error => console.error('Error fetching court info:', error));
    }

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
