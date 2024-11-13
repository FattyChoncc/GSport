<nav class="sidebar">
    <button class="toggle-sidebar">
        <i class="fas fa-chevron-left"></i>
    </button>
    <ul>
        <li>
            <a href="index.php?action=quanlysan&query=themsan"><i class="fas fa-futbol"></i> <span class="menu-text">Quản lý sân</span></a>
        </li>
        <li>
            <a href="index.php?action=quanlylichdat&query=xacnhanlich"><i class="fas fa-calendar-alt"></i> <span class="menu-text">Quản lý lịch đặt</span></a>
        </li>
        <li>
            <a href="index.php?action=quanlydoanhthu&query=thongkedoanhthu"><i class="fas fa-chart-line"></i> <span class="menu-text">Quản lý doanh thu</span></a>
        </li>
        <li>
            <a href="index.php?action=quanlytaikhoan&query=theodoitaikhoan"><i class="fas fa-user"></i> <span class="menu-text">Quản lý tài khoản</span></a>
        </li>
        <li>
            <a href="index.php?action=quanlytaikhoanadmin&query=theodoitaikhoanadmin"><i class="fas fa-user-shield"></i> <span class="menu-text">Quản lý tài khoản admin</span></a>
        </li>
        <li>
            <a href="index.php?action=quanlyptthanhtoan&query=thempt"><i class="fas fa-credit-card"></i> <span class="menu-text">Quản lý PT thanh toán</span></a>
        </li>
    </ul>
    <div class="admin-info">
        <?php if (isset($_SESSION['ten_admin'])): ?>
            <p class="welcome-message">Chào mừng <?php echo htmlspecialchars($_SESSION['ten_admin']); ?> đã trở lại!</p>
        <?php else: ?>
            <p>Chưa đăng nhập</p>
        <?php endif; ?>
    </div>
    
    <div class="settings-container">
        <button class="settings-btn">
            <i class="fas fa-cog"></i>
            <span class="menu-text">Cài đặt</span>
        </button>
        <div class="settings-menu">
            <a href="#" class="settings-item" id="changePasswordBtn">
                <i class="fas fa-key"></i>
                <span>Đổi mật khẩu</span>
            </a>
            <a href="#" class="settings-item" id="logoutBtn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Đăng xuất</span>
            </a>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const logoutBtn = document.getElementById('logoutBtn');
    const changePasswordBtn = document.getElementById('changePasswordBtn');

    // Function to handle sidebar collapse based on window width
    function handleSidebarCollapse() {
        const windowWidth = window.innerWidth;
        if (windowWidth <= 768) { // Kích thước ngưỡng để thu gọn sidebar
            sidebar.classList.add('collapsed');
        } else {
            sidebar.classList.remove('collapsed');
        }
    }

    // Xử lý khi trang được tải và khi thay đổi kích thước cửa sổ
    handleSidebarCollapse();
    window.addEventListener('resize', handleSidebarCollapse);

    // Xử lý sự kiện click cho nút đăng xuất
    logoutBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (confirm('Bạn có chắc chắn muốn đăng xuất?')) {
            window.location.href = 'modules/dangxuat.php'; // Thực hiện hành động đăng xuất
        }
    });

    // Xử lý sự kiện click cho nút đổi mật khẩu
    changePasswordBtn.addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = 'modules/doimatkhau.php'; // Đường dẫn tới trang đổi mật khẩu
    });
});
</script>
