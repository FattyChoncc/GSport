<div class="container_contact">
    <div class="map-container">
        <div id="map"></div>
    </div>

    <div class="contact-methods">
        <h4 class="contact-title">LIÊN HỆ VỚI CHÚNG TÔI</h4>

        <div class="contact-methods-wrapper">
            <div class="contact-method">
                <i class="fas fa-envelope fa-3x"></i>
                <label>Email</label>
                <a href="">123@gmail.com</a>
            </div>

            <div class="contact-method">
                <i class="fas fa-phone fa-3x"></i>
                <label>Điện thoại</label>
                <a href="">0123456789</a>
            </div>

            <div class="contact-method">
                <i class="fas fa-globe fa-3x"></i>
                <label>Mạng xã hội</label>
                <a href="">fbsieucap.com</a>
            </div>
        </div>
    </div>
</div>


<!-- Leaflet CSS and JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>

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
    // Khởi tạo bản đồ
    var map = L.map('map').setView([21.0285, 105.8542], 15); // Hà Nội, Việt Nam

    // Thêm tile map từ OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Thêm marker
    L.marker([21.0285, 105.8542]).addTo(map)
        .bindPopup("Văn phòng của chúng tôi")
        .openPopup();
</script>
