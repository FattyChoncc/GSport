<?php
session_start();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập / Đăng ký</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../cssadmin/styleslogin.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="container">
        <div class="form-container" id="formContainer">
            <!-- Thông báo lỗi (nếu có) -->
            <?php
            // Hiển thị thông báo lỗi
            if (isset($_SESSION['error_message']) && !empty($_SESSION['error_message'])) {
                echo '
                <div class="error-message" id="errorMessage">
                    ' . $_SESSION['error_message'] . '
                </div>';
                unset($_SESSION['error_message']); // Xóa thông báo sau khi hiển thị
            }

            // Hiển thị thông báo thành công
            if (isset($_SESSION['success_message']) && !empty($_SESSION['success_message'])) {
                echo '
                <div class="success-message" id="successMessage">
                    ' . $_SESSION['success_message'] . '
                </div>';
                unset($_SESSION['success_message']); // Xóa thông báo sau khi hiển thị
            }
            ?>
            
            <div class="form-side" id="loginSide">
                <h2>Đăng nhập</h2>
                <form action="xulydangnhap.php" id="loginForm" method="POST">
                    <input type="text" placeholder="Tên đăng nhập" name="Tendn" required>
                    <div class="password-container">
                        <input type="password" placeholder="Mật khẩu" name="Matkhau" id="loginPassword" required>
                        <i class="fa fa-eye" id="toggleLoginPassword"></i>
                    </div>
                    <button type="submit">Đăng nhập</button>
                </form>
                <p>Chưa có tài khoản? <a href="#" id="showRegister">Đăng ký</a></p>
            </div>
            <div class="form-side" id="registerSide">
                <h2>Đăng ký</h2>
                <form action="xulydangky.php" id="registerForm" method="POST">
                    <input type="text" placeholder="Tên Admin" name="Tenadmin" required>
                    <input type="date" placeholder="Ngày sinh" name="Ngaysinh" required>
                    <input type="tel" placeholder="Số điện thoại" name="SDT" required pattern="\d{10,15}" title="Số điện thoại phải từ 10 đến 15 chữ số">
                    <input type="email" placeholder="Gmail" name="Gmail" required pattern=".+@.+\..+" title="Vui lòng nhập địa chỉ Gmail hợp lệ">
                    <input type="text" placeholder="Tên đăng nhập" name="Tendn" required>
                    <div class="password-container">
                        <input type="password" placeholder="Mật khẩu" name="Matkhau" id="registerPassword" required>
                        <i class="fa fa-eye" id="toggleRegisterPassword"></i>
                    </div>
                    <button type="submit">Đăng ký</button>
                </form>
                <p>Đã có tài khoản? <a href="#" id="showLogin"><span>Đăng nhập</span></a></p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formContainer = document.getElementById('formContainer');
            const showRegister = document.getElementById('showRegister');
            const showLogin = document.getElementById('showLogin');

            // Hiển thị hoặc ẩn form Đăng ký
            showRegister.addEventListener('click', function(e) {
                e.preventDefault();
                formContainer.classList.add('flip');
            });

            showLogin.addEventListener('click', function(e) {
                e.preventDefault();
                formContainer.classList.remove('flip');
            });

            // Chức năng hiển thị/ẩn mật khẩu cho form đăng nhập
            const loginPassword = document.getElementById('loginPassword');
            const toggleLoginPassword = document.getElementById('toggleLoginPassword');
            toggleLoginPassword.addEventListener('click', function () {
                const type = loginPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                loginPassword.setAttribute('type', type);
                this.classList.toggle('fa-eye-slash');
            });

            // Chức năng hiển thị/ẩn mật khẩu cho form đăng ký
            const registerPassword = document.getElementById('registerPassword');
            const toggleRegisterPassword = document.getElementById('toggleRegisterPassword');
            toggleRegisterPassword.addEventListener('click', function () {
                const type = registerPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                registerPassword.setAttribute('type', type);
                this.classList.toggle('fa-eye-slash');
            });

            // Ẩn thông báo sau 3 giây nếu có
            const errorMessage = document.getElementById('errorMessage');
            const successMessage = document.getElementById('successMessage');

            if (errorMessage ) {
                setTimeout(() => {
                    errorMessage.classList.add('fade-out');
                    setTimeout(() => {
                        errorMessage.style.display = 'none';
                    }, 500); // Thời gian trễ để đợi hiệu ứng mờ dần
                }, 3000); // Ẩn sau 3 giây
            }
            if (successMessage ) {
                setTimeout(() => {
                    successMessage.classList.add('fade-out');
                    setTimeout(() => {
                        successMessage.style.display = 'none';
                    }, 500); // Thời gian trễ để đợi hiệu ứng mờ dần
                }, 3000); // Ẩn sau 3 giây
            }
        });
    </script>
</body>
</html>

