@php($user = auth()->user())
<section class="container py-4 text-light">
    <div class="glass p-4">
        <h1 class="h4 fw-bold mb-3">إعدادات الحساب</h1>
        @if(session('status'))
            <div class="alert alert-success small py-2 mb-3">{{ session('status') }}</div>
        @endif
        @if($user)
            <p class="text-secondary small mb-3">
                يمكنك هنا تعديل بياناتك الأساسية (سيتم ربط النموذج فعلياً لاحقاً).
            </p>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small text-secondary">الاسم الأول</label>
                    <input type="text" class="form-control form-control-sm auth-input" value="{{ $user->first_name }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-secondary">الاسم الأخير</label>
                    <input type="text" class="form-control form-control-sm auth-input" value="{{ $user->last_name }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-secondary">البريد الإلكتروني</label>
                    <input type="email" class="form-control form-control-sm auth-input" value="{{ $user->email }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-secondary">نقاطك الحالية</label>
                    <input type="text" class="form-control form-control-sm auth-input" value="{{ number_format($user->points ?? 0) }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-secondary">رصيدك الحالي</label>
                    <input type="text" class="form-control form-control-sm auth-input" value="{{ number_format($user->balance ?? 0, 2) }} $" disabled>
                </div>
            </div>
        @else
            <p class="text-secondary small mb-0">يجب تسجيل الدخول لعرض إعدادات حسابك.</p>
        @endif
    </div>
</section>


