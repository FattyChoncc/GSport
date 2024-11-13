<div class="container_ptthanhtoan">
    <h1>Thêm Phương Thức Thanh Toán</h1>
    <!-- Form thêm phương thức thanh toán -->
    <div class="add-payment-method">
        <form action="pages/quanlyptthanhtoan/thempt.php" method="POST">
            <label for="paymentMethodName">Tên Phương Thức:</label>
            <input type="text" id="paymentMethodName" name="paymentMethodName" required>
            <button type="submit"><i class="fa-regular fa-plus"></i></button>
        </form>
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

    <!-- Bảng hiển thị phương thức thanh toán -->
    <table class="payment-method-table">
        <thead>
            <tr>
                <th>ID Phương Thức</th>
                <th>Tên Phương Thức</th>
                <th>Thao Tác</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Kết nối đến cơ sở dữ liệu
            include 'connect/mysqlconnect.php';

            // Truy vấn dữ liệu từ bảng PTThanhToan
            $sql = "SELECT * FROM PTThanhToan";
            $result = $mysqli->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["idpt"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["Tenpt"]) . "</td>";
                    echo "<td>
                            <div class='delete'>
                                <form action='pages/quanlyptthanhtoan/xoapt.php' method='POST' onsubmit='return confirm(\"Bạn có chắc chắn muốn xóa phương thức thanh toán này không?\");' style='display:inline;'>
                                    <input type='hidden' name='idpt' value='" . htmlspecialchars($row["idpt"]) . "'>
                                    <button type='submit' class='delete_credit' style='background: none; border: none; color: red; cursor: pointer;'>
                                        <i class='fas fa-trash' title='Xóa'></i>
                                    </button>
                                </form>
                            </div>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>Không có dữ liệu để hiển thị</td></tr>";
            }

            // Đóng kết nối
            $mysqli->close();
            ?>
        </tbody>
    </table>

    <script>
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
</div>