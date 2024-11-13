<?php
    session_start(); // Khởi tạo session
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Navbar</title>
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Vollkorn:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Vùng chứa thông báo -->
    <div class="notification-container">       
        <?php
        if (isset($_SESSION['success-message'])) {
            echo "<div class='success-message' id='successMessage'>" . $_SESSION['success-message'] . "</div>";
            unset($_SESSION['success-message']);
        }
        if (isset($_SESSION['error-message'])) {
            echo "<div class='error-message' id='errorMessage'>" . $_SESSION['error-message'] . "</div>";
            unset($_SESSION['error-message']);
        }
        ?>
    </div>

    <nav class="navbar">
        <?php
        include('modules/navbar.php');
        ?>
    </nav>

    <!-- Thông báo -->
    <?php
    if (isset($_SESSION['success-message'])) {
        echo "<div class='success-message' id='successMessage'>" . $_SESSION['success-message'] . "</div>";
        unset($_SESSION['success-message']);
    }
    if (isset($_SESSION['error-message'])) {
        echo "<div class='error-message' id='errorMessage'>" . $_SESSION['error-message'] . "</div>";
        unset($_SESSION['error-message']);
    }
    ?>

    <main>
        <?php
            // Kiểm tra action trong URL
            if(isset($_GET['action'])){
                $option = $_GET['action'];
            } else {
                $option = '';
            }

            // Điều hướng theo action
            if($option == 'trangchu'){
                include("pages/trangchu.php");
            } 
            else if($option == 'datsan') {
                include("pages/datsan/datsan.php");
            }
            else if($option == 'chonsan'){
                include("pages/datsan/chonsan.php");
            }
            else if($option == 'lichsudatsan') {
                include("pages/lichsudatsan/lichsudatsan.php");
            }
            else if($option == 'lienhe') {
                include("pages/lienhe/lienhe.php");
            }          
            else {
                // Mặc định hiển thị quanlysan nếu không có action
                include('pages/trangchu.php');
            }
        ?>
    </main>

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

    <!-- Popup Form Đăng Nhập -->
    <div class="popup-overlay" id="loginPopup">
        <div class="popup-content">
            <span class="close-btn" onclick="closePopup('loginPopup')">&times;</span>
            <h2>Đăng Nhập</h2>
            <form method="POST" action="xulydangnhap/xulydangnhap.php" id="loginForm">
                <div class="input-container">
                    <input type="text" name="Tendn" placeholder="Tên đăng nhập" required>
                    <i class="icon fas fa-user"></i>
                </div>
                <div class="input-container password-container">
                    <input type="password" name="Matkhau" placeholder="Mật khẩu" id="loginPassword" required>
                    <i class="icon fas fa-lock"></i>
                    <i class="toggle-password fas fa-eye" id="toggleLoginPassword"></i>
                    <a href="javascript:void(0);" onclick="openPopup('forgotPopup')" class="forgot-password">Quên mật khẩu?</a>
                </div>
                <button type="submit">Đăng Nhập</button>
                <p class="signup-link">Chưa có tài khoản? <a href="javascript:void(0);" onclick="openPopup('signupPopup')">Đăng ký</a></p>
            </form>
        </div>
    </div>

    <!-- Popup Form Đăng Ký -->
    <div class="popup-overlay" id="signupPopup">
        <div class="popup-content">
            <span class="close-btn" onclick="closePopup('signupPopup')">&times;</span>
            <h2>Đăng Ký</h2>
            <form action="xulydangnhap/xulydangky.php" id="registerForm" method="POST">
                <div class="input-container">
                    <input type="text" placeholder="Tên khách hàng" name="Tenkh" required>
                    <i class="icon fas fa-user"></i>
                </div>
                <div class="input-container">
                    <input type="date" placeholder="Ngày sinh" name="Ngaysinh" required>
                    <i class="icon fas fa-calendar-alt"></i>
                </div>
                <div class="input-container">
                    <input type="tel" placeholder="Số điện thoại" name="SDT" required pattern="\d{10,15}" title="Số điện thoại phải từ 10 đến 15 chữ số">
                    <i class="icon fas fa-phone"></i>
                </div>
                <div class="input-container">
                    <input type="email" placeholder="Gmail" name="Gmail" required pattern=".+@.+\..+" title="Vui lòng nhập địa chỉ Gmail hợp lệ">
                    <i class="icon fas fa-envelope"></i>
                </div>
                <div class="input-container">
                    <input type="text" placeholder="Tên đăng nhập" name="Tendn" required>
                    <i class="icon fas fa-user"></i>
                </div>
                <div class="input-container password-container">
                    <input type="password" placeholder="Mật khẩu" name="Matkhau" id="registerPassword" required>
                    <i class="icon fas fa-lock"></i>
                    <i class="toggle-password fas fa-eye" id="toggleRegisterPassword"></i>
                </div>
                <button type="submit">Đăng ký</button>
            </form>
            <p class="login-link">Đã có tài khoản? <a href="javascript:void(0);" onclick="openPopup('loginPopup')"><span>Đăng nhập</span></a></p>
        </div>
    </div>

    <!-- Popup Form Quên Mật Khẩu -->
    <div class="popup-overlay" id="forgotPopup">
        <div class="popup-content">
            <span class="close-btn" onclick="closePopup('forgotPopup')">&times;</span>
            <h2>Quên Mật Khẩu</h2>
            <form method="POST" action="xulymatkhaumoi.php">
                <div class="input-container">
                    <input type="email" name="Gmail" placeholder="Email của bạn" required>
                    <i class="icon fas fa-envelope"></i>
                </div>
                <button type="submit">Gửi liên kết</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menuToggle = document.querySelector('.menu-toggle');
            const navMenu = document.querySelector('.nav-menu');
            const profileToggle = document.querySelector('.profile-toggle');
            const dropdownMenu = document.querySelector('.dropdown-menu');
            const themeToggle = document.querySelector('.theme-toggle');
            const slider = document.querySelector('.slider');
            const prevBtn = document.querySelector('.prev');
            const nextBtn = document.querySelector('.next');

            let currentSlide = 0;
            const totalSlides = document.querySelectorAll('.slider img').length;

            // Toggle mobile menu
            menuToggle.addEventListener('click', () => {
                navMenu.classList.toggle('show');
            });

            // Toggle profile dropdown
            profileToggle.addEventListener('click', () => {
                dropdownMenu.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!profileToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    dropdownMenu.classList.remove('show');
                }
            });

            // Theme toggle
            function setTheme(themeName) {
                localStorage.setItem('theme', themeName);
                document.documentElement.className = themeName;
            }

            function toggleTheme() {
                if (localStorage.getItem('theme') === 'theme-dark') {
                    setTheme('theme-light');
                } else {
                    setTheme('theme-dark');
                }
            }

            (function () {
                if (localStorage.getItem('theme') === 'theme-dark') {
                    setTheme('theme-dark');
                    document.querySelector('.theme-toggle i').classList.replace('fa-sun', 'fa-moon');
                } else {
                    setTheme('theme-light');
                    document.querySelector('.theme-toggle i').classList.replace('fa-moon', 'fa-sun');
                }
            })();

            themeToggle.addEventListener('click', () => {
                toggleTheme();
                const icon = themeToggle.querySelector('i');
                icon.classList.toggle('fa-sun');
                icon.classList.toggle('fa-moon');
            });

            // Slider functionality
            function showSlide(index) {
                if (index < 0) {
                    currentSlide = totalSlides - 1;
                } else if (index >= totalSlides) {
                    currentSlide = 0;
                } else {
                    currentSlide = index;
                }
                slider.style.transform = `translateX(-${currentSlide * 100}%)`;
            }

            prevBtn.addEventListener('click', () => showSlide(currentSlide - 1));
            nextBtn.addEventListener('click', () => showSlide(currentSlide + 1));

            // Auto-slide
            setInterval(() => showSlide(currentSlide + 1), 5000);

            // Smooth scrolling for navigation links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });

            /* Popup đăng nhập - đăng ký */
            const signinBtn = document.querySelector('.signin-btn');
            const signupBtn = document.querySelector('.signup-btn');

            // Đóng login
            signinBtn.addEventListener('click', () => {
                openPopup('loginPopup');
            });

            // Đóng signup
            signupBtn.addEventListener('click', () => {
                openPopup('signupPopup');
            });

            // Đóng popup khi bấm ngoài khu vực form
            window.addEventListener('click', (e) => {
                const loginPopup = document.getElementById('loginPopup');
                const signupPopup = document.getElementById('signupPopup');
                
                // Đóng popup login khi bấm ngoài
                if (e.target === loginPopup) {
                    closePopup('loginPopup');
                }
                
                // Đóng popup signup khi bấm ngoài
                if (e.target === signupPopup) {
                    closePopup('signupPopup');
                }
            });

            // Mở form đăng nhập từ form đăng ký và ẩn form đăng ký trước đó
            document.querySelector('#signupPopup .login-link').addEventListener('click', () => {
                closePopup('signupPopup');  // Ẩn form đăng ký trước
                openPopup('loginPopup');    // Mở form đăng nhập
            });

            // Mở form đăng ký từ form đăng nhập và ẩn form đăng nhập trước đó
            document.querySelector('#loginPopup .signup-link').addEventListener('click', () => {
                closePopup('loginPopup');  // Ẩn form đăng nhập trước
                openPopup('signupPopup');  // Mở form đăng ký
            });
        });

      
        document.addEventListener('DOMContentLoaded', () => {
            const toggleLoginPassword = document.getElementById('toggleLoginPassword');
            const toggleRegisterPassword = document.getElementById('toggleRegisterPassword');
            const loginPasswordInput = document.getElementById('loginPassword');
            const registerPasswordInput = document.getElementById('registerPassword');

            // Toggle password visibility for login form
            toggleLoginPassword.addEventListener('click', () => {
                const type = loginPasswordInput.type === 'password' ? 'text' : 'password';
                loginPasswordInput.type = type;
                toggleLoginPassword.classList.toggle('fa-eye-slash');
            });

            // Toggle password visibility for register form
            toggleRegisterPassword.addEventListener('click', () => {
                const type = registerPasswordInput.type === 'password' ? 'text' : 'password';
                registerPasswordInput.type = type;
                toggleRegisterPassword.classList.toggle('fa-eye-slash');
            });
        });

        // Open and close popups
        function openPopup(popupId) {
            document.getElementById(popupId).style.display = 'flex';
        }

        function closePopup(popupId) {
            document.getElementById(popupId).style.display = 'none';
        }

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

        // Xác nhận đăng xuất
        function confirmLogout() {
            return confirm("Bạn có chắc chắn muốn đăng xuất?");
        }
    </script>
</body>
</html>