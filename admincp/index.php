<?php
session_start(); // Ensure session is started at the top of the file

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: pages/dangnhapvadangky/login.php"); // Redirect to login if not authenticated
    exit();
}

// Your existing index.php code continues here
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Control Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="cssadmin/styles.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header>
        <?php
            include("modules/header.php");
        ?>
    </header>
    
    <div class="main-content">
        <?php
            include("modules/sidebar.php");
        ?>
        <main class="content">
            <?php
            // Kiểm tra action trong URL
            if(isset($_GET['action']) && isset($_GET['query'])) {
                $option = $_GET['action'];
                $query = $_GET['query'];
            } else {
                $option = '';
                $query = '';
            }

            // Điều hướng theo action
            //Sân------------------------------------------------
            if($option == 'quanlysan' && $query == 'themsan') {
                include("pages/quanlysan/quanlysan.php");
            } 
            else if($option == 'quanlysan' && $query == 'suasan') {
                include("pages/quanlysan/suasan.php");
            }
            else if($option == 'quanlysan' && $query == 'suasancon') {
                include("pages/quanlysan/sancon/suasancon.php");
            }
            else if($option == 'quanlysan' && $query == 'sancon') {
                include("pages/quanlysan/sancon/sancon.php");
            }
            //Lịch------------------------------------------------
            else if($option == 'quanlylichdat' && $query == 'xacnhanlich') {
                include("pages/quanlylichdat/quanlylichdat.php");
            } 
            else if($option == 'quanlylichdat' && $query == 'dahuy'){
                include("pages/quanlylichdat/dahuy.php");
            }
            else if($option == 'quanlylichdat' && $query == 'daxacnhan'){
                include("pages/quanlylichdat/daxacnhan.php");
            }
            //Khách------------------------------------------------
            else if($option == 'quanlytaikhoan' && $query == 'theodoitaikhoan') {
                include("pages/quanlytaikhoan/quanlytaikhoan.php");
            }
            else if($option == 'quanlytaikhoan' && $query == 'suattkhachhang') {
                include("pages/quanlytaikhoan/suattkhachhang.php");
            }
            else if($option == 'quanlytaikhoan' && $query == 'tkhoatdong') {
                include("pages/quanlytaikhoan/tkhoatdong.php");
            } 
            else if($option == 'quanlytaikhoan' && $query == 'tkbikhoa') {
                include("pages/quanlytaikhoan/tkbikhoa.php");
            }
            //Admin------------------------------------------------
            else if($option == 'quanlytaikhoanadmin' && $query == 'theodoitaikhoanadmin') {
                include("pages/quanlytaikhoanadmin/tkadmin.php");
            }
            else if($option == 'quanlytaikhoanadmin' && $query == 'suaadmin') {
                include("pages/quanlytaikhoanadmin/suaadmin.php");
            }
            else if($option == 'quanlytaikhoanadmin' && $query == 'hoatdong') {
                include("pages/quanlytaikhoanadmin/hoatdong.php");
            }
            else if($option == 'quanlytaikhoanadmin' && $query == 'choduyet') {
                include("pages/quanlytaikhoanadmin/choduyet.php");
            }
            else if($option == 'quanlytaikhoanadmin' && $query == 'bikhoa') {
                include("pages/quanlytaikhoanadmin/bikhoa.php");
            }
            //Doanh thu------------------------------------------------
            else if($option == 'quanlydoanhthu') {
                include("pages/quanlydoanhthu/quanlydoanhthu.php");
            } 
            //Thanh toán-----------------------------------------------        
            else if($option == 'quanlyptthanhtoan' && $query == 'thempt') {
                include("pages/quanlyptthanhtoan/quanlyptthanhtoan.php");
            } 
            else {
                // Mặc định hiển thị quanlysan nếu không có action
                include('pages/quanlysan/quanlysan.php');
            }
            ?>
        </main>
    </div>

    <script>
        const sidebar = document.querySelector('.sidebar');
        const toggleBtn = document.querySelector('.toggle-sidebar');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            if (sidebar.classList.contains('collapsed')) {
                toggleBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
            } else {
                toggleBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
            }
        });
    </script>
</body>
</html>
