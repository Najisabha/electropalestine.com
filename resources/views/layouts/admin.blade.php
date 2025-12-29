<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'electropalestine Admin' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root{
            --primary:#0db777;
            --primary-dark:#0a8d5b;
            --bg-dark:#0b0d11;
            --accent:#f5d10c;
            --glass:rgba(255,255,255,.06);
            --border:rgba(255,255,255,.12);
        }
        body{
            font-family:'Cairo',sans-serif;
            background:radial-gradient(circle at top,var(--primary-dark),var(--bg-dark) 55%);
            color:#eaf6ef;
        }
        .glass{
            background:var(--glass);
            border:1px solid var(--border);
            backdrop-filter: blur(10px);
            border-radius:18px;
        }
        .brand-logo{
            width:46px;height:46px;
            border-radius:50%;
            display:grid;
            place-items:center;
            background:linear-gradient(135deg,var(--primary),var(--primary-dark));
            font-weight:800;
            color:#fff;
            box-shadow:0 0 25px rgba(13,183,119,.4);
        }
        .btn-main{
            background:linear-gradient(135deg,var(--primary),var(--primary-dark));
            border:none;
            color:#fff;
        }
        .btn-main:hover{
            filter:brightness(1.1);
            color:#fff;
        }
        .btn-outline-main{
            border:1px solid var(--primary);
            color:var(--primary);
        }
        .btn-outline-main:hover{
            background:var(--primary);
            color:#0b0d11;
        }
        nav a{ color:#d9f2e3; font-size:.9rem; }
        nav a:hover{ color:var(--accent); }
        
        /* ===== Dropdown Menu ===== */
        .dropdown-menu{
            background:var(--glass);
            border:1px solid var(--border);
            backdrop-filter:blur(10px);
        }
        .dropdown-item{
            color:#eaf6ef;
            transition:all 0.2s ease;
        }
        .dropdown-item:hover{
            background:rgba(13,183,119,.2);
            color:#fff;
        }
        .dropdown-item.text-danger:hover{
            background:rgba(220,53,69,.2);
        }
        .dropdown-divider{
            border-color:var(--border);
        }
        
        /* ===== Mobile Hamburger Menu ===== */
        .mobile-menu-toggle {
            display: none;
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
            width: 40px;
            height: 40px;
            border-radius: 8px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .mobile-menu-toggle:hover {
            background: var(--primary);
            color: #fff;
        }
        
        .mobile-menu-toggle span {
            display: block;
            width: 24px;
            height: 2px;
            background: currentColor;
            margin: 4px 0;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .mobile-menu-toggle.active {
            background: var(--primary);
            color: #fff;
        }
        
        .mobile-menu-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(6px, 6px);
        }
        
        .mobile-menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }
        
        .mobile-menu-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px);
        }
        
        /* Mobile Overlay */
        .mobile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.75);
            z-index: 9998;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .mobile-overlay.active {
            display: block;
            opacity: 1;
        }

        .mobile-nav-menu {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: var(--bg-dark);
            border-top: 1px solid var(--border);
            padding: 0;
            z-index: 9999;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, padding 0.4s ease;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        
        .mobile-nav-menu.active {
            display: block;
            max-height: 100vh;
            overflow-y: auto;
            padding: 1rem;
            padding-top: 6rem; /* مسافة من الأعلى لتجنب تغطية الـ header */
        }
        
        /* إخفاء المحتوى الرئيسي عند فتح القائمة */
        body.menu-open main {
            display: none !important;
        }
        
        body.menu-open header {
            z-index: 10000;
            position: relative;
        }
        
        .mobile-nav-menu::-webkit-scrollbar {
            width: 6px;
        }
        
        .mobile-nav-menu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }
        
        .mobile-nav-menu .nav-link-mobile {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #eaf6ef;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }
        
        .mobile-nav-menu .nav-link-mobile:hover,
        .mobile-nav-menu .nav-link-mobile:active {
            background: rgba(13, 183, 119, 0.15);
            border-color: var(--primary);
            color: var(--primary);
            transform: translateX(5px);
        }
        
        .mobile-nav-menu .nav-link-mobile i {
            width: 20px;
            text-align: center;
            margin-left: 8px;
        }
        
        .mobile-nav-menu .dropdown-divider {
            margin: 0.75rem 0;
            border-color: var(--border);
        }
        
        .mobile-nav-menu .dropdown-item {
            padding: 0.75rem 1rem;
            color: #eaf6ef;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Desktop Navigation - إخفاء القائمة المتنقلة على الشاشات الكبيرة */
        @media (min-width: 769px) {
            .mobile-menu-toggle {
                display: none !important;
            }
            
            .mobile-nav-menu {
                display: none !important;
            }
            
            .mobile-overlay {
                display: none !important;
            }
            
            .desktop-nav {
                display: flex !important;
            }
        }
        
        /* Mobile Styles */
        @media (max-width: 768px) {
            header {
                position: relative;
                z-index: 1;
            }
            
            header .container {
                flex-direction: row;
                gap: 0.75rem;
                position: relative;
                align-items: center;
            }
            
            .mobile-menu-toggle {
                display: flex;
                flex-direction: column;
            }
            
            .desktop-nav {
                display: none !important;
            }
            
            .mobile-nav-menu {
                display: block;
            }
            
            .header-actions {
                margin-left: auto;
                gap: 0.4rem;
            }
            
            .header-actions .btn-sm {
                font-size: 0.75rem;
                padding: 0.35rem 0.65rem;
                min-width: 40px;
            }
            
            .brand-logo {
                width: 40px;
                height: 40px;
                font-size: 0.9rem;
            }
            
            header .text-white {
                font-size: 0.9rem;
            }
            
            .mobile-nav-menu .nav-link-mobile {
                font-size: 0.9rem;
            }
            
            .mobile-nav-menu .bi {
                font-size: 1rem;
            }
            
            /* ضمان أن القائمة والـ overlay في أعلى مستوى */
            .mobile-overlay.active {
                z-index: 9998;
            }
            
            .mobile-nav-menu.active {
                z-index: 9999;
            }
        }
        
        @media (max-width: 576px) {
            .mobile-menu-toggle {
                width: 36px;
                height: 36px;
            }
            
            .mobile-menu-toggle span {
                width: 20px;
            }
            
            .brand-logo {
                width: 36px;
                height: 36px;
                font-size: 0.85rem;
            }
            
            header .text-white {
                font-size: 0.85rem;
            }
        }
    </style>
    {{-- Chart.js للرسوم البيانية في لوحة التحكم --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="d-flex flex-column min-vh-100">
@include('admin.header')

<main class="flex-grow-1">
    {!! $slot !!}
</main>

@include('admin.footer')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Admin Mobile Menu Toggle with Overlay
    document.addEventListener('DOMContentLoaded', function() {
        const adminMobileMenuToggle = document.getElementById('adminMobileMenuToggle');
        const adminMobileNavMenu = document.getElementById('adminMobileNavMenu');
        const adminMobileOverlay = document.getElementById('adminMobileOverlay');
        
        if (adminMobileMenuToggle && adminMobileNavMenu && adminMobileOverlay) {
            adminMobileMenuToggle.addEventListener('click', function() {
                const isActive = this.classList.toggle('active');
                adminMobileNavMenu.classList.toggle('active');
                adminMobileOverlay.classList.toggle('active');
                
                // إضافة/إزالة class على body لإخفاء المحتوى
                if (isActive) {
                    document.body.classList.add('menu-open');
                } else {
                    document.body.classList.remove('menu-open');
                }
            });
            
            const closeMenu = function() {
                adminMobileMenuToggle.classList.remove('active');
                adminMobileNavMenu.classList.remove('active');
                adminMobileOverlay.classList.remove('active');
                document.body.classList.remove('menu-open');
            };
            
            // إغلاق القائمة عند النقر على overlay
            adminMobileOverlay.addEventListener('click', closeMenu);
            
            // إغلاق القائمة عند النقر على رابط
            const mobileNavLinks = adminMobileNavMenu.querySelectorAll('.nav-link-mobile');
            mobileNavLinks.forEach(link => {
                link.addEventListener('click', closeMenu);
            });
            
            // إغلاق القائمة عند النقر خارجها
            document.addEventListener('click', function(event) {
                const isClickInsideMenu = adminMobileNavMenu.contains(event.target);
                const isClickOnToggle = adminMobileMenuToggle.contains(event.target);
                const isClickOnOverlay = adminMobileOverlay.contains(event.target);
                
                if (!isClickInsideMenu && !isClickOnToggle && !isClickOnOverlay && adminMobileNavMenu.classList.contains('active')) {
                    closeMenu();
                }
            });
        }
    });
</script>

</body>
</html>

