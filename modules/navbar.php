<div class="container">
    <a href="index.php" class="logo">
        <img src="logoweb/logoweb-removebg-preview.jpg" alt="Logo" class="logo-img">
    </a>

    <div class="menu-toggle">
        <i class="fas fa-bars"></i>
    </div>

    <?php
        // Kiểm tra URL để xác định trang hiện tại
        $current_page = isset($_GET['action']) ? $_GET['action'] : 'trangchu';
    ?>
    <ul class="nav-menu">
        <li><a href="index.php?action=trangchu" class="<?= ($current_page == 'trangchu') ? 'active' : '' ?>">Trang chủ</a></li>
        <li><a href="index.php?action=datsan" class="<?= ($current_page == 'datsan') ? 'active' : '' ?>">Đặt sân</a></li>
        <li><a href="index.php?action=lichsudatsan" class="<?= ($current_page == 'lichsudatsan') ? 'active' : '' ?>">Lịch sử đặt</a></li>
        <li><a href="index.php?action=lienhe" class="<?= ($current_page == 'lienhe') ? 'active' : '' ?>">Liên hệ</a></li>
    </ul>

    <div class="nav-buttons">
        <button class="theme-toggle">
            <i class="fas fa-sun"></i>
        </button>
        <div class="profile-dropdown">
            <button class="profile-toggle">
                <i class="fas fa-user"></i>
            </button>
            <ul class="dropdown-menu">
                <li><a href="#" class="pro-btn">Hồ sơ</a></li>
                <?php if (isset($_SESSION['idkh'])): ?>
                    <li><a href="doimatkhau.php" class="change-password-btn">Đổi mật khẩu</a></li>
                    <li><a href="xulydangnhap/xulydangxuat.php" class="logout-btn" onclick="return confirmLogout()">Đăng Xuất</a></li>
                <?php else: ?>
                    <li><a href="#" class="signin-btn">Đăng nhập</a></li>
                    <li><a href="#" class="signup-btn">Đăng ký</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
