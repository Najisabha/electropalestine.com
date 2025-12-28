@php
    $locale = app()->getLocale();
    $isRTL = $locale === 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRTL ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name', 'electropalestine') }}</title>

    <!-- Bootstrap -->
    @if($isRTL)
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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

        /* ===== Glass Effect ===== */
        .glass{
            background:var(--glass);
            border:1px solid var(--border);
            backdrop-filter: blur(10px);
            border-radius:18px;
        }
        .font {
            font-family:'Cairo',sans-serif;
            color:#ffffff;
        }
        /* ===== Brand ===== */
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

        /* ===== Buttons ===== */
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

        /* ===== Header ===== */
        header{
            position:sticky;
            top:0;
            z-index:1000;
        }

        nav a{
            color:#d9f2e3;
            font-size:.9rem;
        }
        nav a:hover{
            color:var(--accent);
        }

        /* ===== Footer ===== */
        .hover-link { transition: all 0.3s ease; }
        .hover-link:hover { color: #fff !important; padding-right: 5px; }
        .social-icon { 
            background-color: rgba(255,255,255,0.1) !important; 
            transition: all 0.3s ease; 
            display: inline-flex !important;
        }
        .social-icon:hover { 
            background-color: #0db777 !important; 
            color: white !important; 
            transform: translateY(-3px); 
            box-shadow: 0 4px 8px rgba(13, 183, 119, 0.3);
        }
        .btn-subscribe { background-color: #0db777; border-color: #0db777; color: white; transition: all 0.3s; }
        .btn-subscribe:hover { background-color: #0a8d5b; border-color: #0a8d5b; color: white; }
        footer { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; } /* أو خطك العربي المفضل */

        /* ===== Store product cards ===== */
        .product-card-img{
            height:180px;
            object-fit:cover;
            border-top-left-radius:18px;
            border-top-right-radius:18px;
        }
        .product-card{
            border-radius:18px;
            overflow:hidden;
        }

        /* ===== Horizontal strips ===== */
        .strip-scroll{
            display:flex;
            gap:1rem;
            overflow-x:auto;
            padding-bottom:.5rem;
            scrollbar-width:thin;
            -webkit-overflow-scrolling:touch; /* تحسين التمرير على iOS */
        }
        .strip-scroll::-webkit-scrollbar{
            height:6px;
        }
        .strip-scroll::-webkit-scrollbar-thumb{
            background:rgba(255,255,255,.2);
            border-radius:999px;
        }
        .strip-card{
            min-width:260px;
            background:var(--glass);
            border:1px solid var(--border);
            border-radius:16px;
            overflow:hidden;
            color:#eaf6ef;
            text-decoration:none;
            flex-shrink:0;
        }
        .strip-img{
            height:160px;
            width:100%;
            object-fit:contain;             /* تجنب قص الصورة */
            background:rgba(0,0,0,.0);     /* خلفية شفافة داكنة للصور */
            padding:8px;                    /* مسافة بسيطة حول الصورة */
        }

        .bg-black{
            background-color:rgba(0,0,0,.0) !important;
        }

         /* ===== Professional Product Card Design ===== */
        .product-card-new{
            display:block;
            width:280px;
            min-width:280px;
            background:linear-gradient(135deg, #1a1d24 0%, #151820 100%);
            border-radius:20px;
            overflow:hidden;
            transition:all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border:1px solid rgba(255,255,255,0.1);
            flex-shrink:0;
            box-shadow:0 4px 20px rgba(0,0,0,0.3);
            position:relative;
            height:100%;
        }
        .product-card-new::before{
            content:'';
            position:absolute;
            top:0;
            left:0;
            right:0;
            height:4px;
            background:linear-gradient(90deg, var(--primary), var(--primary-dark));
            opacity:0;
            transition:opacity 0.3s ease;
        }
        .product-card-new:hover{
            transform:translateY(-8px);
            box-shadow:0 16px 40px rgba(13,183,119,0.2);
            border-color:rgba(13,183,119,0.4);
        }
        .product-card-new:hover::before{
            opacity:1;
        }
        @media (hover: none) {
            .product-card-new:hover{
                transform:none;
            }
        }
        
        .product-card-image-wrapper{
            position:relative;
            padding:20px;
            background:linear-gradient(135deg, #0a0c10 0%, #0f1115 100%);
            display:flex;
            align-items:center;
            justify-content:center;
            min-height:220px;
            overflow:hidden;
        }
        
        .product-badge-rating{
            position:absolute;
            top:12px;
            left:12px;
            background:linear-gradient(135deg, #f5d10c 0%, #f0c908 100%);
            color:#000;
            font-size:0.7rem;
            font-weight:700;
            padding:6px 12px;
            border-radius:20px;
            z-index:3;
            display:flex;
            align-items:center;
            gap:4px;
            box-shadow:0 2px 8px rgba(245,209,12,0.3);
            backdrop-filter:blur(10px);
        }
        .product-badge-rating i{
            font-size:0.7rem;
            color:#000;
        }
        
        .product-badge-sold{
            position:absolute;
            top:12px;
            right:12px;
            background:linear-gradient(135deg, #0db777 0%, #0a8d5b 100%);
            color:#fff;
            font-size:0.65rem;
            font-weight:700;
            padding:6px 12px;
            border-radius:20px;
            z-index:3;
            display:flex;
            align-items:center;
            gap:4px;
            box-shadow:0 2px 8px rgba(13,183,119,0.3);
            backdrop-filter:blur(10px);
        }
        .product-badge-sold i{
            font-size:0.7rem;
        }
        
        .product-badge-out-of-stock{
            position:absolute;
            top:12px;
            right:12px;
            background:linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color:#fff;
            font-size:0.65rem;
            font-weight:700;
            padding:6px 12px;
            border-radius:20px;
            z-index:3;
            display:flex;
            align-items:center;
            gap:4px;
            box-shadow:0 2px 8px rgba(220,53,69,0.3);
        }
        .product-badge-out-of-stock i{
            font-size:0.7rem;
        }
        
        .product-card-image-container{
            width:160px;
            height:160px;
            display:flex;
            align-items:center;
            justify-content:center;
            border-radius:16px;
            overflow:hidden;
            background:linear-gradient(135deg, #0d0f13 0%, #080a0d 100%);
            box-shadow:0 8px 32px rgba(0,0,0,0.5), inset 0 0 40px rgba(13,183,119,0.05);
            transition:transform 0.3s ease;
            position:relative;
        }
        .product-card-new:hover .product-card-image-container{
            transform:scale(1.05);
        }
        
        .product-card-image{
            width:100%;
            height:100%;
            object-fit:contain;
            border-radius:16px;
            transition:transform 0.3s ease;
        }
        .product-card-new:hover .product-card-image{
            transform:scale(1.1);
        }
        
        .product-card-no-image{
            width:100%;
            height:100%;
            display:flex;
            align-items:center;
            justify-content:center;
            background:linear-gradient(135deg, #0d0f13 0%, #080a0d 100%);
            border-radius:16px;
            color:#5a5e66;
        }
        .product-card-no-image i{
            font-size:3rem;
        }
        
        .product-card-overlay{
            position:absolute;
            top:0;
            left:0;
            right:0;
            bottom:0;
            background:linear-gradient(to bottom, rgba(13,183,119,0.9), rgba(10,141,91,0.9));
            display:flex;
            align-items:center;
            justify-content:center;
            opacity:0;
            transition:opacity 0.3s ease;
            z-index:2;
        }
        .product-card-new:hover .product-card-overlay{
            opacity:1;
        }
        
        .product-card-overlay-content{
            color:#fff;
            text-align:center;
            font-weight:600;
            font-size:0.9rem;
            display:flex;
            flex-direction:column;
            align-items:center;
            gap:8px;
        }
        .product-card-overlay-content i{
            font-size:2rem;
        }
        
        .product-card-info{
            padding:18px 20px 20px;
            background:linear-gradient(135deg, #1a1d24 0%, #151820 100%);
        }
        
        .product-card-header{
            margin-bottom:12px;
        }
        
        .product-card-title{
            color:#fff;
            font-size:0.95rem;
            font-weight:600;
            margin-bottom:6px;
            line-height:1.5;
            display:-webkit-box;
            -webkit-line-clamp:2;
            -webkit-box-orient:vertical;
            overflow:hidden;
            text-overflow:ellipsis;
            min-height:2.85rem;
        }
        
        .product-card-category-badge{
            display:inline-block;
            background:rgba(13,183,119,0.15);
            color:var(--primary);
            font-size:0.7rem;
            font-weight:500;
            padding:4px 10px;
            border-radius:12px;
            border:1px solid rgba(13,183,119,0.3);
        }
        
        .product-card-footer{
            margin-top:12px;
            padding-top:12px;
            border-top:1px solid rgba(255,255,255,0.08);
        }
        
        .product-card-price-section{
            display:flex;
            flex-direction:column;
            gap:8px;
        }
        
        .product-card-price{
            color:var(--primary);
            font-size:1.3rem;
            font-weight:700;
            line-height:1;
            background:linear-gradient(135deg, var(--primary), var(--primary-dark));
            -webkit-background-clip:text;
            -webkit-text-fill-color:transparent;
            background-clip:text;
        }
        
        .product-card-stock-badge{
            display:inline-flex;
            align-items:center;
            gap:6px;
            color:#5a5e66;
            font-size:0.75rem;
            font-weight:500;
            background:rgba(90,94,102,0.15);
            padding:6px 12px;
            border-radius:12px;
            border:1px solid rgba(90,94,102,0.2);
        }
        .product-card-stock-badge i{
            font-size:0.8rem;
            color:#0db777;
        }
        .product-card-stock-badge.out-of-stock{
            background:rgba(220,53,69,0.15);
            border-color:rgba(220,53,69,0.2);
            color:#dc3545;
        }
        .product-card-stock-badge.out-of-stock i{
            color:#dc3545;
        }
        
        /* Horizontal scroll for product cards - Desktop */
        .products-scroll{
            display:flex;
            gap:16px;
            overflow-x:auto;
            padding-bottom:10px;
            scrollbar-width:thin;
            -webkit-overflow-scrolling:touch; /* تحسين التمرير على iOS */
        }
        .products-scroll::-webkit-scrollbar{
            height:6px;
        }
        .products-scroll::-webkit-scrollbar-thumb{
            background:rgba(255,255,255,.15);
            border-radius:999px;
        }
        .products-scroll::-webkit-scrollbar-thumb:hover{
            background:rgba(255,255,255,.25);
        }

         /* ===== Auth pages ===== */
         .auth-card{
             width:100%;
             max-width:440px;
             border-radius:16px;
         }
         .auth-logo{
             width:64px;
             height:64px;
             margin-inline:auto;
             border-radius:50%;
             display:grid;
             place-items:center;
             font-weight:800;
             color:#fff;
             background:linear-gradient(135deg,var(--primary),var(--primary-dark));
             box-shadow:0 0 30px rgba(13,183,119,.45);
         }
         .auth-input{
             background:rgba(0,0,0,.55);
             border:1px solid rgba(255,255,255,.15);
             color:#fff;
             padding:.65rem .75rem;
             border-radius:12px;
         }
         .auth-input:focus{
             background:rgba(0,0,0,.7);
             color:#fff;
             border-color:var(--primary);
             box-shadow:0 0 0 .15rem rgba(13,183,119,.25);
         }

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
         
         .mobile-nav-menu {
             display: none;
             position: absolute;
             top: 100%;
             left: 0;
             right: 0;
             background: var(--bg-dark);
             border-top: 1px solid var(--border);
             padding: 0;
             z-index: 999;
             max-height: 0;
             overflow: hidden;
             transition: max-height 0.4s ease, padding 0.4s ease;
             box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
         }
         
         .mobile-nav-menu.active {
             display: block;
             max-height: 80vh;
             overflow-y: auto;
             padding: 1rem;
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
             
             .desktop-nav {
                 display: flex !important;
             }
         }

         /* ===== Responsive Design for Mobile & Tablet ===== */
         @media (max-width: 768px) {
             /* تحسين Header على الشاشات الصغيرة */
             header {
                 position: relative;
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
             
             /* تحسين عرض السلة والحساب على الشاشات الصغيرة */
             .header-actions .position-relative {
                 min-width: auto;
             }
             
             .header-actions .badge {
                 font-size: 0.6rem;
                 padding: 0.2rem 0.4rem;
             }
             
             /* تحسين الأيقونات */
             .header-actions .bi {
                 font-size: 1.1rem;
             }
             
             /* تحسين القائمة المتنقلة */
             .mobile-nav-menu .nav-link-mobile {
                 font-size: 0.9rem;
             }
             
             .mobile-nav-menu .bi {
                 font-size: 1rem;
             }
             
             /* تحسين الـ logo على الشاشات الصغيرة */
             .brand-logo {
                 width: 40px;
                 height: 40px;
                 font-size: 0.9rem;
             }
             
             header .text-white {
                 font-size: 0.9rem;
             }
             
             /* تحويل strip-scroll إلى Grid لعرض 3 بطاقات */
             .strip-scroll {
                 display: grid !important;
                 grid-template-columns: repeat(3, 1fr);
                 gap: 8px;
                 overflow-x: visible;
                 padding-bottom: 0;
             }
             
             /* تحسين Category Cards على الشاشات الصغيرة - 3 بطاقات */
             .strip-card {
                 width: 100% !important;
                 min-width: 0 !important;
                 max-width: 100% !important;
                 height: auto;
             }
             
             .strip-img {
                 height: 120px;
             }
             
             /* تحويل products-scroll إلى Grid لعرض 3 بطاقات */
             .products-scroll {
                 display: grid !important;
                 grid-template-columns: repeat(3, 1fr);
                 gap: 8px;
                 overflow-x: visible;
                 padding-bottom: 0;
             }
             
             /* تحسين Product Cards على الشاشات الصغيرة - 3 بطاقات */
             .product-card-new {
                 width: 100% !important;
                 min-width: 0 !important;
                 max-width: 100% !important;
                 height: auto;
             }
             
             .product-card-image-container {
                 width: 90px;
                 height: 90px;
             }
             
             .product-card-image-wrapper {
                 min-height: 130px;
                 padding: 14px;
             }
             
             .product-card-info {
                 padding: 12px 14px 14px;
             }
             
             .product-card-title {
                 font-size: 0.75rem;
                 min-height: 2.25rem;
                 line-height: 1.3;
                 -webkit-line-clamp: 2;
             }
             
             .product-card-price {
                 font-size: 0.9rem;
             }
             
             .product-card-stock-badge {
                 font-size: 0.6rem;
                 padding: 4px 8px;
                 gap: 4px;
             }
             
             .product-card-stock-badge i {
                 font-size: 0.65rem;
             }
             
             .product-card-category-badge {
                 font-size: 0.55rem;
                 padding: 2px 6px;
             }
             
             .product-badge-rating,
             .product-badge-sold,
             .product-badge-out-of-stock {
                 font-size: 0.55rem;
                 padding: 4px 8px;
                 top: 8px;
             }
             
             .product-badge-rating {
                 left: 8px;
             }
             
             .product-badge-sold,
             .product-badge-out-of-stock {
                 right: 8px;
             }
             
             .product-badge-rating i,
             .product-badge-sold i,
             .product-badge-out-of-stock i {
                 font-size: 0.6rem;
             }
             
             .product-card-overlay-content {
                 font-size: 0.7rem;
             }
             
             .product-card-overlay-content i {
                 font-size: 1.5rem;
             }
             
             /* تحسين المسافات */
             .strip-scroll {
                 gap: 0.75rem;
                 padding-bottom: 0.75rem;
             }
         }
             
             /* تحسين العناوين */
             .h5 {
                 font-size: 1.1rem;
             }
             
             .h6 {
                 font-size: 0.95rem;
             }
             
             /* تحسين الأزرار */
             .btn-main,
             .btn-outline-main {
                 font-size: 0.8rem;
                 padding: 0.4rem 0.8rem;
             }
             
             /* تحسين الأزرار الكبيرة */
             .btn {
                 font-size: 0.85rem;
             }
             
             /* تحسين المسافات الرأسية */
             section {
                 padding-top: 1rem !important;
                 padding-bottom: 1rem !important;
             }
             
             /* تحسين الـ glass cards */
             .glass {
                 border-radius: 14px;
             }
         }
         
         @media (max-width: 576px) {
             /* تحسينات إضافية للهواتف الصغيرة جداً - 4 بطاقات */
             .strip-card {
                 min-width: 90vw;
                 max-width: 90vw;
             }
             
             .strip-img {
                 height: 120px;
             }
             
             /* Grid Layout للهواتف الصغيرة - 3 بطاقات */
             .strip-scroll {
                 grid-template-columns: repeat(3, 1fr);
                 gap: 6px;
             }
             
             .products-scroll {
                 grid-template-columns: repeat(3, 1fr);
                 gap: 6px;
             }
             
             .strip-card {
                 width: 100% !important;
                 min-width: 0 !important;
                 max-width: 100% !important;
             }
             
             .strip-img {
                 height: 100px;
             }
             
             .product-card-new {
                 width: 100% !important;
                 min-width: 0 !important;
                 max-width: 100% !important;
                 height: auto;
             }
             
             .product-card-image-container {
                 width: 75px;
                 height: 75px;
             }
             
             .product-card-image-wrapper {
                 min-height: 115px;
                 padding: 12px;
             }
             
             .product-card-info {
                 padding: 10px 12px 12px;
             }
             
             .product-card-title {
                 font-size: 0.7rem;
                 min-height: 2.1rem;
                 line-height: 1.25;
             }
             
             .product-card-price {
                 font-size: 0.85rem;
             }
             
             .product-card-stock-badge {
                 font-size: 0.55rem;
                 padding: 3px 6px;
             }
             
             .product-card-category-badge {
                 font-size: 0.5rem;
                 padding: 2px 5px;
             }
             
             .product-badge-rating,
             .product-badge-sold,
             .product-badge-out-of-stock {
                 font-size: 0.5rem;
                 padding: 3px 6px;
                 top: 6px;
             }
             
             .product-badge-rating {
                 left: 6px;
             }
             
             .product-badge-sold,
             .product-badge-out-of-stock {
                 right: 6px;
             }
             
             .product-card-new {
                 border-radius: 12px;
             }
             
             .product-card-image-container {
                 border-radius: 10px;
             }
             
             .header-actions {
                 gap: 0.3rem;
             }
             
             .header-actions .btn-sm {
                 font-size: 0.7rem;
                 padding: 0.3rem 0.5rem;
                 min-width: 36px;
             }
             
             .mobile-menu-toggle {
                 width: 36px;
                 height: 36px;
             }
             
             .mobile-menu-toggle span {
                 width: 20px;
             }
             
             .container {
                 padding-left: 1rem;
                 padding-right: 1rem;
             }
             
             .brand-logo {
                 width: 36px;
                 height: 36px;
                 font-size: 0.85rem;
             }
             
             header .text-white {
                 font-size: 0.85rem;
             }
             
             .mobile-nav-menu {
                 max-height: 75vh;
             }
             
             /* تحسين العناوين على الشاشات الصغيرة جداً */
             .h3 {
                 font-size: 1.3rem;
             }
             
             .h4 {
                 font-size: 1.15rem;
             }
             
             .h5 {
                 font-size: 1rem;
             }
             
             .h6 {
                 font-size: 0.9rem;
             }
             
             /* تحسين الأزرار */
             .btn {
                 font-size: 0.8rem;
                 padding: 0.4rem 0.7rem;
             }
             
             /* تحسين الشارات */
             .badge {
                 font-size: 0.65rem;
                 padding: 0.25rem 0.5rem;
             }
             
             /* تحسين المسافات */
             .mb-4 {
                 margin-bottom: 1rem !important;
             }
             
             .mb-3 {
                 margin-bottom: 0.75rem !important;
             }
             
             .mt-4 {
                 margin-top: 1rem !important;
             }
             
             .mt-5 {
                 margin-top: 1.25rem !important;
             }
         }
         
         @media (min-width: 769px) and (max-width: 1024px) {
             /* تحسينات للأجهزة اللوحية */
             .strip-card {
                 min-width: 300px;
             }
             
             .strip-scroll {
                 gap: 1.25rem;
             }
             
             .products-scroll {
                 gap: 18px;
             }
         }
         
         /* على الشاشات الكبيرة - التصميم الأصلي مع التمرير الأفقي */
         @media (min-width: 1025px) {
             .strip-scroll {
                 display: flex !important;
                 overflow-x: auto;
             }
             
             .strip-card {
                 min-width: 260px;
                 width: auto;
                 max-width: none;
             }
             
             .products-scroll {
                 display: flex !important;
                 overflow-x: auto;
             }
             
             .product-card-new {
                 width: 280px;
                 min-width: 280px;
                 max-width: 280px;
             }
         }
         
         /* تحسين التمرير على الأجهزة المحمولة */
         @media (max-width: 1024px) {
             .strip-scroll,
             .products-scroll {
                 scroll-snap-type: x mandatory;
                 -webkit-overflow-scrolling: touch;
             }
             
             .strip-card,
             .product-card-new {
                 scroll-snap-align: start;
             }
         }
         
         /* إخفاء scrollbar على بعض المتصفحات المحمولة */
         @media (max-width: 768px) {
             .strip-scroll::-webkit-scrollbar,
             .products-scroll::-webkit-scrollbar {
                 height: 4px;
             }
         }

    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<!-- ===== Header ===== -->
<header class="glass mb-4">
    <div class="container py-3 d-flex justify-content-between align-items-center">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none">
            <div class="brand-logo">VM</div>
            <strong class="text-white">electropalestine</strong>
        </a>

        @php($authUser = auth()->user())
        @php($cartCount = count(session()->get('cart', [])))

        {{-- Desktop Navigation --}}
        <nav class="d-flex align-items-center gap-2 flex-wrap desktop-nav">
            {{-- Language Switcher --}}
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-main dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-translate"></i>
                    {{ $locale === 'ar' ? __('common.arabic') : __('common.english') }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end glass border border-secondary-subtle" aria-labelledby="languageDropdown">
                    <li><a class="dropdown-item text-light" href="{{ route('language.switch', 'ar') }}">العربية</a></li>
                    <li><a class="dropdown-item text-light" href="{{ route('language.switch', 'en') }}">English</a></li>
                </ul>
            </div>

            {{-- روابط عامة للمستخدم / الزائر --}}
            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-main">{{ __('common.home') }}</a>
            <a href="{{ route('store.about') }}" class="btn btn-sm btn-outline-main">{{ __('common.about') }}</a>
            <a href="{{ route('store.story') }}" class="btn btn-sm btn-outline-main">{{ __('common.story') }}</a>
            <a href="{{ route('home') }}#products" class="btn btn-sm btn-outline-main">{{ __('common.products') }}</a>
            <a href="{{ route('store.contact') }}" class="btn btn-sm btn-outline-main">{{ __('common.contact') }}</a>

            {{-- شريط خاص بالأدمن فقط --}}
            @if($authUser && strtolower($authUser->role) === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-main font">{{ __('common.admin_dashboard') }}</a>
                <a href="{{ route('admin.catalog') }}" class="btn btn-sm btn-outline-main font">{{ __('common.manage_categories') }}</a>
                <a href="{{ route('admin.campaigns') }}" class="btn btn-sm btn-outline-main font">{{ __('common.advertising_campaigns') }}</a>
                <a href="{{ route('admin.roles') }}" class="btn btn-sm btn-outline-main font">{{ __('common.roles_permissions') }}</a>
                <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-main font">{{ __('common.show_users') }}</a>
                <a href="{{ route('admin.orders') }}" class="btn btn-sm btn-outline-main font">{{ __('common.show_orders') }}</a>
                <a href="{{ route('admin.coupons') }}" class="btn btn-sm btn-outline-main font">{{ __('common.coupons') }}</a>
                <a href="{{ route('admin.store-settings') }}" class="btn btn-sm btn-outline-main font">{{ __('common.store_settings') }}</a>
            @endif

            {{-- جزء المستخدم المسجّل (غير الأدمن): صورة + نقاط + رصيد + إعدادات (dropdown) --}}
            @if($authUser)
                <div class="d-flex align-items-center gap-2 ms-2">
                    <div class="rounded-circle bg-success d-flex align-items-center justify-content-center"
                         style="width:34px;height:34px;font-size:0.8rem;">
                        {{ mb_substr($authUser->first_name,0,1) }}{{ mb_substr($authUser->last_name,0,1) }}
                    </div>
                    <span class="badge bg-dark border border-success small">
                        {{ __('common.your_points') }}: <strong class="text-success">{{ number_format($authUser->points ?? 0) }}</strong>
                    </span>
                    <span class="badge bg-dark border border-info small">
                        {{ __('common.your_balance') }}: <strong class="text-info">${{ number_format($authUser->balance ?? 0, 2) }}</strong>
                    </span>
                </div>

                {{-- قائمة الإعدادات المنسدلة --}}
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-main dropdown-toggle" type="button" id="settingsDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('common.settings') }}">
                        <i class="bi bi-gear fs-5"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end glass border border-secondary-subtle" aria-labelledby="settingsDropdown" style="min-width: 200px;">
                        <li>
                            <a class="dropdown-item text-light" href="{{ route('store.account-settings') }}">
                                <i class="bi bi-person-circle me-2"></i>
                                {{ __('common.account_settings') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-light" href="{{ route('store.my-orders') }}">
                                <i class="bi bi-bag-check me-2"></i>
                                {{ __('common.my_orders') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-light" href="{{ route('store.my-comments') }}">
                                <i class="bi bi-chat-left-text me-2"></i>
                                {{ __('common.my_comments') }}
                            </a>
                        </li>
                        <li><hr class="dropdown-divider border-secondary-subtle"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                                @csrf
                                <button type="submit" class="dropdown-item text-light text-danger" onclick="return confirm('{{ __('common.logout_confirm') }}');">
                                    <i class="bi bi-box-arrow-right me-2"></i>
                                    {{ __('common.logout') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-main">{{ __('common.login') }}</a>
                <a href="{{ route('register') }}" class="btn btn-sm btn-main">{{ __('common.register') }}</a>
            @endif
        </nav>

        {{-- Mobile Actions (Cart + Account) --}}
        <div class="header-actions">
            {{-- سلة الشراء --}}
            <a href="{{ route('store.cart') }}" class="btn btn-sm btn-outline-main position-relative" title="{{ __('common.cart') }}">
                <i class="bi bi-cart3 fs-5"></i>
                @if($cartCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger small">
                        {{ $cartCount }}
                    </span>
                @endif
            </a>

            {{-- حساب المستخدم (على الشاشات الصغيرة) --}}
            @if($authUser)
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-main dropdown-toggle d-flex align-items-center" type="button" id="mobileUserDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('common.account') }}">
                        <div class="rounded-circle bg-success d-flex align-items-center justify-content-center"
                             style="width:28px;height:28px;font-size:0.7rem;">
                            {{ mb_substr($authUser->first_name,0,1) }}{{ mb_substr($authUser->last_name,0,1) }}
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end glass border border-secondary-subtle" aria-labelledby="mobileUserDropdown" style="min-width: 200px;">
                        <li class="px-3 py-2">
                            <div class="small text-light mb-1">
                                <strong>{{ $authUser->first_name }} {{ $authUser->last_name }}</strong>
                            </div>
                            <div class="small text-secondary">
                                {{ __('common.your_points') }}: <strong class="text-success">{{ number_format($authUser->points ?? 0) }}</strong>
                            </div>
                            <div class="small text-secondary">
                                {{ __('common.your_balance') }}: <strong class="text-info">${{ number_format($authUser->balance ?? 0, 2) }}</strong>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider border-secondary-subtle"></li>
                        <li>
                            <a class="dropdown-item text-light" href="{{ route('store.account-settings') }}">
                                <i class="bi bi-person-circle me-2"></i>
                                {{ __('common.account_settings') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-light" href="{{ route('store.my-orders') }}">
                                <i class="bi bi-bag-check me-2"></i>
                                {{ __('common.my_orders') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-light" href="{{ route('store.my-comments') }}">
                                <i class="bi bi-chat-left-text me-2"></i>
                                {{ __('common.my_comments') }}
                            </a>
                        </li>
                        @if(strtolower($authUser->role) === 'admin')
                            <li><hr class="dropdown-divider border-secondary-subtle"></li>
                            <li>
                                <a class="dropdown-item text-light" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-speedometer2 me-2"></i>
                                    {{ __('common.admin_dashboard') }}
                                </a>
                            </li>
                        @endif
                        <li><hr class="dropdown-divider border-secondary-subtle"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                                @csrf
                                <button type="submit" class="dropdown-item text-light text-danger" onclick="return confirm('{{ __('common.logout_confirm') }}');">
                                    <i class="bi bi-box-arrow-right me-2"></i>
                                    {{ __('common.logout') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-main d-none d-md-inline-block">{{ __('common.login') }}</a>
                <a href="{{ route('register') }}" class="btn btn-sm btn-main d-none d-md-inline-block">{{ __('common.register') }}</a>
            @endif

            {{-- Hamburger Menu Toggle --}}
            <button class="mobile-menu-toggle" type="button" id="mobileMenuToggle" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>

    {{-- Mobile Navigation Menu --}}
    <div class="mobile-nav-menu" id="mobileNavMenu">
        {{-- Language Switcher --}}
        <div class="mb-3">
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-main w-100 dropdown-toggle" type="button" id="mobileLanguageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-translate"></i>
                    {{ $locale === 'ar' ? __('common.arabic') : __('common.english') }}
                </button>
                <ul class="dropdown-menu w-100 glass border border-secondary-subtle" aria-labelledby="mobileLanguageDropdown">
                    <li><a class="dropdown-item text-light" href="{{ route('language.switch', 'ar') }}">العربية</a></li>
                    <li><a class="dropdown-item text-light" href="{{ route('language.switch', 'en') }}">English</a></li>
                </ul>
            </div>
        </div>

        {{-- روابط عامة --}}
        <a href="{{ route('home') }}" class="nav-link-mobile">
            <i class="bi bi-house me-2"></i>
            {{ __('common.home') }}
        </a>
        <a href="{{ route('store.about') }}" class="nav-link-mobile">
            <i class="bi bi-info-circle me-2"></i>
            {{ __('common.about') }}
        </a>
        <a href="{{ route('store.story') }}" class="nav-link-mobile">
            <i class="bi bi-book me-2"></i>
            {{ __('common.story') }}
        </a>
        <a href="{{ route('home') }}#products" class="nav-link-mobile">
            <i class="bi bi-box-seam me-2"></i>
            {{ __('common.products') }}
        </a>
        <a href="{{ route('store.contact') }}" class="nav-link-mobile">
            <i class="bi bi-envelope me-2"></i>
            {{ __('common.contact') }}
        </a>

        {{-- شريط خاص بالأدمن فقط --}}
        @if($authUser && strtolower($authUser->role) === 'admin')
            <hr class="dropdown-divider">
            <div class="small text-success px-3 py-2 mb-2">
                <i class="bi bi-shield-check me-2"></i>
                <strong>{{ __('common.admin_panel') }}</strong>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link-mobile">
                <i class="bi bi-speedometer2 me-2"></i>
                {{ __('common.admin_dashboard') }}
            </a>
            <a href="{{ route('admin.catalog') }}" class="nav-link-mobile">
                <i class="bi bi-folder me-2"></i>
                {{ __('common.manage_categories') }}
            </a>
            <a href="{{ route('admin.campaigns') }}" class="nav-link-mobile">
                <i class="bi bi-megaphone me-2"></i>
                {{ __('common.advertising_campaigns') }}
            </a>
            <a href="{{ route('admin.roles') }}" class="nav-link-mobile">
                <i class="bi bi-person-badge me-2"></i>
                {{ __('common.roles_permissions') }}
            </a>
            <a href="{{ route('admin.users') }}" class="nav-link-mobile">
                <i class="bi bi-people me-2"></i>
                {{ __('common.show_users') }}
            </a>
            <a href="{{ route('admin.orders') }}" class="nav-link-mobile">
                <i class="bi bi-receipt me-2"></i>
                {{ __('common.show_orders') }}
            </a>
            <a href="{{ route('admin.coupons') }}" class="nav-link-mobile">
                <i class="bi bi-ticket-perforated me-2"></i>
                {{ __('common.coupons') }}
            </a>
            <a href="{{ route('admin.store-settings') }}" class="nav-link-mobile">
                <i class="bi bi-gear me-2"></i>
                {{ __('common.store_settings') }}
            </a>
        @endif

        {{-- روابط تسجيل الدخول/التسجيل للزوار --}}
        @if(!$authUser)
            <hr class="dropdown-divider">
            <a href="{{ route('login') }}" class="nav-link-mobile">
                <i class="bi bi-box-arrow-in-right me-2"></i>
                {{ __('common.login') }}
            </a>
            <a href="{{ route('register') }}" class="nav-link-mobile" style="background: var(--primary); color: #fff; border-color: var(--primary);">
                <i class="bi bi-person-plus me-2"></i>
                {{ __('common.register') }}
            </a>
        @endif
    </div>
</header>

<!-- ===== Main ===== -->
<main class="flex-grow-1">
    {{-- رسائل الحالة / الأخطاء بشكل تنبيه Bootstrap جميل --}}
    @if (session('status') || $errors->has('error'))
        <div class="container mt-3">
            <div class="alert alert-{{ $errors->has('error') ? 'danger' : 'success' }} alert-dismissible fade show glass" role="alert">
                <i class="bi {{ $errors->has('error') ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill' }} me-2"></i>
                <span class="font">
                    {{ $errors->has('error') ? $errors->first('error') : session('status') }}
                </span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    {!! $slot !!}
</main>

<!-- ===== Footer ===== -->
<footer class="bg-dark text-white pt-5 pb-4" dir="{{ $isRTL ? 'rtl' : 'ltr' }}" style="background-color: #1a1c20 !important;">
    <div class="container text-md-start text-center">
        
        <div class="row align-items-center mb-5 pb-4 border-bottom border-secondary">
            <div class="col-md-6 mb-3 mb-md-0">
                <h4 class="fw-bold">{{ __('common.newsletter_subscribe') }}</h4>
                <p class="text-white-50 small mb-0">{{ __('common.newsletter_description') }}</p>
            </div>
            <div class="col-md-6">
                <form action="#" class="d-flex gap-2 justify-content-center justify-content-md-end">
                    <input type="email" class="form-control bg-dark text-white border-secondary" placeholder="{{ __('common.email_placeholder') }}" style="max-width: 300px;">
                    <button class="btn btn-subscribe px-4" type="button">{{ __('common.subscribe_button') }}</button>
                </form>
            </div>
        </div>

        <div class="row mt-3">
            
            <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
                <h5 class="text-uppercase fw-bold mb-4">
                    <i class="fas fa-bolt me-2" style="color: #0db777;"></i> ElectroPalestine
                </h5>
                <p class="text-white-50 text-justify">
                    {{ __('common.company_description') }}
                </p>
                <div class="mt-3 d-flex gap-2">
                    <a href="https://www.facebook.com/electropalestine" target="_blank" rel="noopener noreferrer" class="social-icon d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%; background-color: rgba(255,255,255,0.1); color: white; text-decoration: none; font-size: 18px;" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/electropalestine" target="_blank" rel="noopener noreferrer" class="social-icon d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%; background-color: rgba(255,255,255,0.1); color: white; text-decoration: none; font-size: 18px;" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.instagram.com/electropalestine" target="_blank" rel="noopener noreferrer" class="social-icon d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%; background-color: rgba(255,255,255,0.1); color: white; text-decoration: none; font-size: 18px;" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://wa.me/970591234567" target="_blank" rel="noopener noreferrer" class="social-icon d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%; background-color: rgba(255,255,255,0.1); color: white; text-decoration: none; font-size: 18px;" title="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
                <h6 class="text-uppercase fw-bold mb-4 d-inline-block pb-1" style="border-bottom: 2px solid #0db777;">{{ __('common.quick_links') }}</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ url('/') }}" class="text-white-50 text-decoration-none hover-link">{{ __('common.home') }}</a></li>
                    <li class="mb-2"><a href="/login" class="text-white-50 text-decoration-none hover-link">{{ __('common.login') }}</a></li>
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-link">{{ __('common.all_products') }}</a></li>
                    <li class="mb-2"><a href="/contact" class="text-white-50 text-decoration-none hover-link">{{ __('common.contact_us') }}</a></li>
                </ul>
            </div>

            <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
                <h6 class="text-uppercase fw-bold mb-4 d-inline-block pb-1" style="border-bottom: 2px solid #0db777;">{{ __('common.customer_service') }}</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-link">{{ __('common.my_account') }}</a></li>
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-link">{{ __('common.track_order') }}</a></li>
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-link">{{ __('common.return_policy') }}</a></li>
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-link">{{ __('common.faq') }}</a></li>
                </ul>
            </div>

            <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
                <h6 class="text-uppercase fw-bold mb-4 d-inline-block pb-1" style="border-bottom: 2px solid #0db777;">{{ __('common.contact_us') }}</h6>
                <p class="text-white-50"><i class="fas fa-home me-3 text-secondary ms-2"></i> {{ __('common.footer_address') }}</p>
                <p class="text-white-50"><i class="fas fa-envelope me-3 text-secondary ms-2"></i> info@electropalestine.com</p>
                <p class="text-white-50" dir="ltr"><i class="fas fa-phone me-3 text-secondary ms-2"></i> +970 59 123 4567</p>
                <p class="text-white-50" dir="ltr"><i class="fas fa-print me-3 text-secondary ms-2"></i> +970 2 298 7654</p>
            </div>
        </div>
    </div>

    <div class="text-center p-4 mt-3" style="background-color: rgba(0, 0, 0, 0.2); border-top: 1px solid #2c2e33;">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="mb-2 mb-md-0">
                © {{ date('Y') }} {{ __('common.copyright_text') }}:
                <a class="text-white fw-bold text-decoration-none" href="https://electropalestine.com/">ElectroPalestine</a>
            </div>
            <div>
                <i class="fab fa-cc-visa fa-lg text-white mx-1"></i>
                <i class="fab fa-cc-mastercard fa-lg text-white mx-1"></i>
                <i class="fab fa-cc-paypal fa-lg text-white mx-1"></i>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Mobile Menu Toggle
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mobileNavMenu = document.getElementById('mobileNavMenu');
        
        if (mobileMenuToggle && mobileNavMenu) {
            mobileMenuToggle.addEventListener('click', function() {
                this.classList.toggle('active');
                mobileNavMenu.classList.toggle('active');
            });
            
            // إغلاق القائمة عند النقر خارجها
            document.addEventListener('click', function(event) {
                const isClickInsideMenu = mobileNavMenu.contains(event.target);
                const isClickOnToggle = mobileMenuToggle.contains(event.target);
                
                if (!isClickInsideMenu && !isClickOnToggle && mobileNavMenu.classList.contains('active')) {
                    mobileMenuToggle.classList.remove('active');
                    mobileNavMenu.classList.remove('active');
                }
            });
            
            // إغلاق القائمة عند النقر على رابط
            const mobileNavLinks = mobileNavMenu.querySelectorAll('.nav-link-mobile');
            mobileNavLinks.forEach(link => {
                link.addEventListener('click', function() {
                    mobileMenuToggle.classList.remove('active');
                    mobileNavMenu.classList.remove('active');
                });
            });
        }
    });
</script>

</body>
</html>
