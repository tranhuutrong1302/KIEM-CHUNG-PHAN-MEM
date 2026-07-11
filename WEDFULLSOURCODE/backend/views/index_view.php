<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Royal Bid - Đẳng Cấp Thượng Lưu</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Manrope:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* [GIỮ NGUYÊN TOÀN BỘ CSS CỦA BẠN] */
        body { background-color: #121212; font-family: 'Manrope', sans-serif; color: #e0e0e0; overflow-x: hidden; }
        .navbar { background: rgba(20, 20, 20, 0.95); border-bottom: 1px solid #333; transition: 0.3s; }
        .navbar-brand { font-family: 'Playfair Display', serif; color: #d4af37 !important; font-weight: bold; letter-spacing: 1px; }
        .user-greeting { color: #fff; font-size: 0.9rem; border: 1px solid #333; padding: 5px 15px; border-radius: 20px; background: #1a1a1a; }
        .user-greeting span { color: #d4af37; font-weight: bold; font-family: 'Playfair Display', serif; }
        .carousel-item { height: 600px; overflow: hidden; position: relative; }
        @keyframes zoomMove { 0% { transform: scale(1); } 100% { transform: scale(1.15); } }
        .carousel-item img { height: 100%; width: 100%; object-fit: cover; filter: brightness(0.55); animation: zoomMove 12s linear infinite alternate; }
        .carousel-caption { bottom: 35%; text-align: center; z-index: 10; }
        .carousel-caption h1 { font-family: 'Playfair Display', serif; font-size: 4rem; color: #d4af37; text-shadow: 0 5px 15px rgba(0,0,0,0.8); margin-bottom: 15px; text-transform: uppercase; letter-spacing: 3px; animation: fadeInUp 1s ease; }
        .carousel-caption p { font-size: 1.2rem; color: #fff; letter-spacing: 1px; font-weight: 300; animation: fadeInUp 1.5s ease; }
        @keyframes fadeInUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .section-header { text-align: center; margin: 70px 0 40px 0; }
        .section-title { font-family: 'Playfair Display', serif; font-size: 2.5rem; color: #e0e0e0; text-transform: uppercase; letter-spacing: 1px; position: relative; display: inline-block; padding-bottom: 15px; }
        .section-title::after { content: ''; position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 80px; height: 3px; background-color: #c5a059; }
        .filter-container { display: flex; justify-content: center; gap: 12px; margin-bottom: 50px; flex-wrap: wrap; }
        .filter-btn { background: transparent; border: 1px solid #444; color: #888; padding: 8px 25px; border-radius: 30px; font-size: 0.9rem; font-weight: 600; transition: all 0.3s; cursor: pointer; }
        .filter-btn:hover, .filter-btn.active { border-color: #c5a059; color: #c5a059; background: rgba(197, 160, 89, 0.1); }
        .dark-card { background-color: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 4px; overflow: hidden; transition: transform 0.3s ease, border-color 0.3s; height: 100%; }
        .dark-card:hover { transform: translateY(-7px); box-shadow: 0 15px 40px rgba(0,0,0,0.6); border-color: #444; }
        .card-img-wrapper { position: relative; height: 240px; overflow: hidden; }
        .card-img-top { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s; }
        .dark-card:hover .card-img-top { transform: scale(1.1); }
        .cat-badge { position: absolute; top: 12px; left: 12px; background-color: rgba(0,0,0,0.8); color: #d4af37; border: 1px solid #d4af37; padding: 4px 10px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; z-index: 2; letter-spacing: 1px; }
        .card-body { padding: 25px; }
        .card-title { font-family: 'Playfair Display', serif; font-size: 1.15rem; color: #fff; margin-bottom: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .card-price { color: #ff4d4d; font-weight: 700; font-size: 1.3rem; margin-bottom: 20px; display: block; }
        .meta-row { display: flex; justify-content: space-between; font-size: 0.8rem; color: #999; margin-bottom: 20px; font-weight: 500; border-top: 1px solid #333; padding-top: 15px; }
        .btn-view-auction { width: 100%; background-color: #0e141d; color: #fff; border: 1px solid #333; padding: 12px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; }
        .btn-view-auction:hover { background-color: #c5a059; color: #000; border-color: #c5a059; }
        .royal-footer { background-color: #050505; color: #a0a0a0; padding-top: 80px; margin-top: 80px; border-top: 3px solid #1a1a1a; }
        .footer-title { font-family: 'Playfair Display', serif; color: #c5a059; font-size: 1.2rem; margin-bottom: 25px; font-weight: bold; letter-spacing: 1px; }
        .footer-text { font-size: 0.9rem; line-height: 1.8; color: #888; }
        .footer-links { list-style: none; padding: 0; }
        .footer-links li { margin-bottom: 12px; }
        .footer-links a { color: #a0a0a0; text-decoration: none; font-size: 0.9rem; transition: 0.3s; display: flex; align-items: center; }
        .footer-links a:hover { color: #c5a059; transform: translateX(5px); }
        .footer-links a i { font-size: 0.7rem; margin-right: 10px; color: #444; }
        .contact-item { display: flex; gap: 15px; margin-bottom: 20px; }
        .contact-icon { width: 40px; height: 40px; background: #111; border: 1px solid #333; display: flex; align-items: center; justify-content: center; color: #c5a059; border-radius: 50%; }
        .footer-bottom { background: #000; padding: 20px 0; margin-top: 60px; border-top: 1px solid #111; font-size: 0.8rem; text-align: center; color: #666; }
        .modal-content { background-color: #1a1a1a; border: 1px solid #333; color: #eee; }
        .modal-header { border-bottom: 1px solid #333; }
        .modal-footer { border-top: 1px solid #333; }
        .form-control { background-color: #222; border: 1px solid #444; color: #fff; }
        .form-control:focus { background-color: #2a2a2a; border-color: #c5a059; color: #fff; box-shadow: none; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fa-solid fa-crown me-2"></i>ROYAL BID</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
                <div class="d-flex gap-3 align-items-center mt-3 mt-lg-0">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="user-greeting">
                            Xin chào, <span><?php echo $_SESSION['full_name']; ?></span>
                        </div>
                        <a href="my_bids.php" class="btn btn-outline-warning btn-sm">Đơn của tôi</a>
                        <?php if(isset($_SESSION['role']) && $_SESSION['role']=='admin'): ?>
                            <a href="admin.php" class="btn btn-outline-danger btn-sm">Admin</a>
                        <?php endif; ?>
                        <a href="logout.php" class="btn btn-sm text-secondary">Thoát</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-light btn-sm px-4">Đăng nhập</a>
                        <a href="register.php" class="btn btn-warning btn-sm text-dark fw-bold px-4">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://static-images.vnncdn.net/files/publish/2023/9/7/sieu-xe-ferrari-laferrari-mui-tran-duoc-rao-ban-65-trieu-euro-1288.jpg?width=0&s=K8jt9qsSq3w4NJgj2FYpkQ" alt="Luxury Car">
                <div class="carousel-caption">
                    <h1>SIÊU XE THƯỢNG LƯU</h1>
                    <p>Bộ sưu tập Rolls-Royce, Bentley, Ferrari giới hạn.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1613490493576-7fde63acd811?q=80&w=1600" alt="Luxury Villa">
                <div class="carousel-caption">
                    <h1>BẤT ĐỘNG SẢN HẠNG SANG</h1>
                    <p>Biệt thự nghỉ dưỡng & Penthouse đẳng cấp quốc tế.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://file.hstatic.net/200000567741/article/kim-cuong-loai-da-quy-hiem-duoc-su-dung-trong-da-dang-linh-vuc_a2437b50c14242889292a534750f980d.jpg" alt="Diamond">
                <div class="carousel-caption">
                    <h1>TRANG SỨC KIM CƯƠNG</h1>
                    <p>Vẻ đẹp vĩnh cửu, tinh hoa chế tác đỉnh cao.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1523170335258-f5ed11844a49?q=80&w=1600" alt="Rolex Watch">
                <div class="carousel-caption">
                    <h1>ĐỒNG HỒ ROLEX</h1>
                    <p>Biểu tượng của sự thành công và quyền lực.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <div class="section-header">
            <h2 class="section-title">TIN ĐẤU GIÁ NỔI BẬT</h2>
        </div>

        <div class="filter-container">
            <button class="filter-btn active" onclick="filterSelection('all')">Tất cả</button>
            <button class="filter-btn" onclick="filterSelection('Xe sang')">Xe sang</button>
            <button class="filter-btn" onclick="filterSelection('Bất động sản')">Bất động sản</button>
            <button class="filter-btn" onclick="filterSelection('Trang sức')">Trang sức</button>
            <button class="filter-btn" onclick="filterSelection('Đồng hồ')">Đồng hồ</button>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($products_data as $row): ?>
                <div class="col filter-item <?php echo str_replace(' ', '-', $row['category_name']); ?>">
                    <div class="dark-card">
                        <div class="card-img-wrapper">
                            <div class="cat-badge"><?php echo $row['category_name']; ?></div>
                            <img src="<?php echo normalizeImageUrl($row['image']); ?>" class="card-img-top">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title" title="<?php echo $row['name']; ?>"><?php echo $row['name']; ?></h5>
                            <div class="card-price"><?php echo number_format($row['price']); ?>đ</div>
                            <div class="meta-row">
                                <span class="timer" data-time="<?php echo $row['end_time_js']; ?>">
                                    <i class="fa-regular fa-hourglass-half"></i> Đang tính...
                                </span>
                                <span><i class="fa-solid fa-fire text-warning"></i> <?php echo $row['bid_count']; ?> lượt</span>
                            </div>
                            
                            <?php if(!$row['is_expired']): ?>
                                <?php if($isLoggedIn): ?>
                                <button class="btn-view-auction" onclick="openBidModal('<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['name']); ?>', <?php echo $row['next_min']; ?>)">XEM CHI TIẾT & ĐẤU GIÁ</button>
                                <?php else: ?>
                                <a href="login.php" class="btn-view-auction text-decoration-none d-flex align-items-center justify-content-center">ĐĂNG NHẬP ĐỂ ĐẤU GIÁ</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <button class="btn-view-auction" style="opacity:0.5; cursor:not-allowed">ĐÃ KẾT THÚC</button>
                            <?php endif; ?>
                            
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer class="royal-footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-md-6 mb-5">
                    <h4 class="footer-title">ROYAL BID</h4>
                    <p class="footer-text">
                        Sàn đấu giá trực tuyến uy tín hàng đầu Việt Nam. Chúng tôi cam kết mang đến những tài sản giá trị thực, pháp lý minh bạch và trải nghiệm đấu giá đẳng cấp quốc tế cho giới thượng lưu.
                    </p>
                    <div class="mt-4">
                        <a href="#" class="text-light me-3"><i class="fa-brands fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-light me-3"><i class="fa-brands fa-youtube fa-lg"></i></a>
                        <a href="#" class="text-light"><i class="fa-brands fa-tiktok fa-lg"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-5">
                    <h4 class="footer-title">DANH MỤC</h4>
                    <ul class="footer-links">
                        <li><a href="#"><i class="fa-solid fa-caret-right"></i> Xe sang</a></li>
                        <li><a href="#"><i class="fa-solid fa-caret-right"></i> Bất động sản</a></li>
                        <li><a href="#"><i class="fa-solid fa-caret-right"></i> Đồng hồ hiệu</a></li>
                        <li><a href="#"><i class="fa-solid fa-caret-right"></i> Trang sức</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-12 mb-5">
                    <h4 class="footer-title">LIÊN HỆ</h4>
                    <div class="contact-item">
                        <div class="contact-icon"><i class="fa-solid fa-location-dot"></i></div>
                        <div>
                            <strong class="text-light d-block">Trụ sở chính</strong>
                            <span class="footer-text small">Vincom Center, Quận 1, TP.Hồ Chí Minh</span>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon"><i class="fa-solid fa-phone"></i></div>
                        <div>
                            <strong class="text-light d-block">Hotline 24/7</strong>
                            <span class="footer-text small" style="color:#c5a059">1900 9999</span>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon"><i class="fa-solid fa-envelope"></i></div>
                        <div>
                            <strong class="text-light d-block">Email hỗ trợ</strong>
                            <span class="footer-text small">vip@royalbid.vn</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-start text-center mb-2 mb-md-0">
                        © 2025 ROYAL BID AUCTION. All rights reserved.
                    </div>
                    <div class="col-md-6 text-md-end text-center">
                        <span class="me-3">Điều khoản sử dụng</span>
                        <span>Chính sách bảo mật</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <div class="modal fade" id="bidModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-warning">Đặt giá</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <p class="text-secondary small">Bạn đang đặt giá cho: <strong id="mTitle" class="text-white"></strong></p>
                        <input type="hidden" name="product_id" id="mId">
                        <label class="form-label text-light">Giá đề nghị (VNĐ):</label>
                        
                        <input type="text" name="bid_amount" id="mAmount" class="form-control text-warning fw-bold" 
                               onkeyup="formatCurrency(this)" required>
                        
                        <div class="mt-2 text-secondary small">Tối thiểu: <span id="mMinShow"></span></div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" name="submit_bid" class="btn btn-warning fw-bold text-dark">XÁC NHẬN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterSelection(c) {
            var x = document.getElementsByClassName("filter-item");
            if (c == "all") c = "";
            var btns = document.getElementsByClassName("filter-btn");
            for (var i = 0; i < btns.length; i++) {
                btns[i].classList.remove("active");
                if (btns[i].innerText.includes(c) || (c=="" && btns[i].innerText=="Tất cả")) btns[i].classList.add("active");
            }
            c = c.replace(/ /g, "-");
            for (var i = 0; i < x.length; i++) {
                x[i].style.display = "none";
                if (x[i].className.indexOf(c) > -1) x[i].style.display = "block";
            }
        }

        setInterval(() => {
            document.querySelectorAll('.timer').forEach(el => {
                const dest = parseInt(el.dataset.time);
                const now = new Date().getTime();
                const diff = dest - now;
                if(diff <= 0) { el.innerHTML = "Kết thúc"; el.style.color="#888"; }
                else {
                    const d = Math.floor(diff / (1000*60*60*24));
                    const h = Math.floor((diff % (1000*60*60*24)) / (1000*60*60));
                    const m = Math.floor((diff % (1000*60*60)) / (1000*60));
                    const s = Math.floor((diff % (1000*60)) / 1000);
                    el.innerHTML = `<i class="fa-regular fa-hourglass-half"></i> ${d}d ${h}h ${m}m ${s}s`;
                }
            });
        }, 1000);

        function openBidModal(id, name, minPrice) {
            <?php if(!$isLoggedIn): ?>
            alert('Vui lòng đăng nhập để đấu giá!');
            window.location = 'login.php';
            return;
            <?php endif; ?>
            document.getElementById('mId').value = id;
            document.getElementById('mTitle').innerText = name;
            let minPriceFmt = new Intl.NumberFormat('de-DE').format(minPrice);
            document.getElementById('mAmount').value = minPriceFmt;
            document.getElementById('mMinShow').innerText = minPriceFmt + ' VNĐ';
            new bootstrap.Modal(document.getElementById('bidModal')).show();
        }

        function formatCurrency(input) {
            let value = input.value.replace(/[^0-9]/g, '');
            if (value) { input.value = new Intl.NumberFormat('de-DE').format(value); } 
            else { input.value = ''; }
        }
    </script>
</body>
</html>