<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name', 'VoltMart') }}</title>

    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

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
        footer{
            background:rgba(0,0,0,.5);
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
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<!-- ===== Header ===== -->
<header class="glass mb-4">
    <div class="container py-3 d-flex justify-content-between align-items-center">

         <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none">
             <div class="brand-logo">VM</div>
             <strong class="text-white">VoltMart</strong>
        </a>

        <nav class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-main">الرئيسية</a>

            @auth
                <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-main">لوحة تحكم الإدارة</a>
                <a href="{{ route('admin.catalog') }}" class="btn btn-sm btn-outline-main">إدارة التصنيفات</a>
                <a href="{{ route('admin.add-campaign') }}" class="btn btn-sm btn-outline-main">إضافة حملة إعلانية</a>
                <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-main">إظهار المستخدمين</a>
                <a href="{{ route('admin.orders') }}" class="btn btn-sm btn-outline-main">إظهار الطلبات</a>
                <a href="{{ route('admin.coupons') }}" class="btn btn-sm btn-outline-main">الكوبونات</a>
                <a href="{{ route('admin.store-settings') }}" class="btn btn-sm btn-outline-main">إعدادات المتجر</a>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-outline-main">تسجيل خروج</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-main">تسجيل دخول</a>
                <a href="{{ route('register') }}" class="btn btn-sm btn-main">إنشاء حساب</a>
            @endauth
        </nav>
    </div>
</header>

 <!-- ===== Main ===== -->
 <main class="flex-grow-1">
     {!! $slot !!}
 </main>

<!-- ===== Footer ===== -->
<footer class="mt-5">
    <div class="container py-4 text-center small text-secondary">
         <p class="mb-1">
             VoltMart • متجر إلكترونيات عصري بألوان الطاقة ⚡
             <span class="text-warning">أخضر</span> و
             <span class="text-warning">أسود</span>
         </p>
         <p class="mb-0">
             © {{ date('Y') }} VoltMart — All Rights Reserved
         </p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
