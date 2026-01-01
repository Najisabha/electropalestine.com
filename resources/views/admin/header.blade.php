<header class="glass mb-4">
    <div class="container py-3 d-flex justify-content-between align-items-center">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none">
            <img src="{{ asset('images/LOGO-remove background.png') }}" alt="ElectroPalestine Logo" height="46" class="d-none d-md-block">
            <img src="{{ asset('images/LOGO-remove background.png') }}" alt="ElectroPalestine Logo" height="40" class="d-md-none">
        </a>

        {{-- Desktop Navigation --}}
        <nav class="d-flex align-items-center gap-2 flex-wrap desktop-nav">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-main">لوحة التحكم</a>
            <a href="{{ route('admin.catalog') }}" class="btn btn-sm btn-outline-main">إدارة التصنيفات</a>
            <a href="{{ route('admin.campaigns') }}" class="btn btn-sm btn-outline-main">الحملات الإعلانية</a>
            <a href="{{ route('admin.roles') }}" class="btn btn-sm btn-outline-main">الأدوار و الصلاحيات</a>
            <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-main">إظهار المستخدمين</a>
            <a href="{{ route('admin.orders') }}" class="btn btn-sm btn-outline-main">إظهار الطلبات</a>
            <a href="{{ route('admin.coupons') }}" class="btn btn-sm btn-outline-main">الكوبونات</a>
            <a href="{{ route('admin.store-settings') }}" class="btn btn-sm btn-outline-main">إعدادات المتجر</a>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-main">تسجيل خروج</button>
            </form>
        </nav>

        {{-- Mobile Actions --}}
        <div class="header-actions">
            {{-- Hamburger Menu Toggle --}}
            <button class="mobile-menu-toggle" type="button" id="adminMobileMenuToggle" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>

    {{-- Mobile Overlay --}}
    <div class="mobile-overlay" id="adminMobileOverlay"></div>

    {{-- Mobile Navigation Menu --}}
    <div class="mobile-nav-menu" id="adminMobileNavMenu">
        <a href="{{ route('admin.dashboard') }}" class="nav-link-mobile">
            <i class="bi bi-speedometer2"></i>
            لوحة التحكم
        </a>
        <a href="{{ route('admin.catalog') }}" class="nav-link-mobile">
            <i class="bi bi-folder"></i>
            إدارة التصنيفات
        </a>
        <a href="{{ route('admin.campaigns') }}" class="nav-link-mobile">
            <i class="bi bi-megaphone"></i>
            الحملات الإعلانية
        </a>
        <a href="{{ route('admin.roles') }}" class="nav-link-mobile">
            <i class="bi bi-person-badge"></i>
            الأدوار و الصلاحيات
        </a>
        <a href="{{ route('admin.users') }}" class="nav-link-mobile">
            <i class="bi bi-people"></i>
            إظهار المستخدمين
        </a>
        <a href="{{ route('admin.orders') }}" class="nav-link-mobile">
            <i class="bi bi-receipt"></i>
            إظهار الطلبات
        </a>
        <a href="{{ route('admin.coupons') }}" class="nav-link-mobile">
            <i class="bi bi-ticket-perforated"></i>
            الكوبونات
        </a>
        <a href="{{ route('admin.store-settings') }}" class="nav-link-mobile">
            <i class="bi bi-gear"></i>
            إعدادات المتجر
        </a>
        <hr class="dropdown-divider">
        <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
            @csrf
            <button type="submit" class="nav-link-mobile text-danger" style="background: rgba(220,53,69,0.15); border-color: rgba(220,53,69,0.3);">
                <i class="bi bi-box-arrow-right"></i>
                تسجيل خروج
            </button>
        </form>
    </div>
</header>

