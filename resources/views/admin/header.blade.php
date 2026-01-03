<header class="glass mb-4">
    <div class="container py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none">
            <div class="brand-logo">VM</div>
            <strong class="text-white">electropalestine Admin</strong>
        </a>
        <nav class="d-flex align-items-center gap-2 flex-wrap">
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
                <button class="btn btn-sm btn-main">تسجيل خروج</button>
            </form>
        </nav>
    </div>
</header>

