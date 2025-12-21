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
        footer{
            background:rgba(0,0,0,.5);
        }

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
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<!-- ===== Header ===== -->
<header class="glass mb-4">
    <div class="container py-3 d-flex justify-content-between align-items-center">

         <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none">
             <div class="brand-logo">VM</div>
             <strong class="text-white">electropalestine</strong>
        </a>

        <nav class="d-flex align-items-center gap-2 flex-wrap">
            @php($authUser = auth()->user())

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

            {{-- سلة الشراء (أيقونة فقط) --}}
            @php($cartCount = count(session()->get('cart', [])))
            <a href="{{ route('store.cart') }}" class="btn btn-sm btn-outline-main position-relative" title="{{ __('common.cart') }}">
                <i class="bi bi-cart3 fs-5"></i>
                @if($cartCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger small">
                        {{ $cartCount }}
                    </span>
                @endif
            </a>

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
<footer class="mt-5">
    <div class="container py-4 text-center small text-secondary">
         <p class="mb-1">
             electropalestine • {{ __('common.footer_tagline') }}
             <span class="text-warning">{{ __('common.green') }}</span> {{ __('common.and') }}
             <span class="text-warning">{{ __('common.black') }}</span>
         </p>
         <p class="mb-0">
             © {{ date('Y') }} electropalestine — All Rights Reserved
         </p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
