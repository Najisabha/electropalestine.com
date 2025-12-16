<section class="py-5 text-light">
    <div class="container">
        <h1 class="h4 fw-bold mb-4">إعدادات الحساب الشخصي</h1>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="glass rounded-4 p-4">
                    <h2 class="h6 fw-semibold mb-3">المعلومات الشخصية</h2>

                    @if (session('status'))
                        <div class="alert alert-success small py-2 mb-3">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="#" class="row g-3">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label small text-secondary">الاسم الأول</label>
                            <input type="text" class="form-control auth-input" value="{{ $user->first_name ?? '' }}" name="first_name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary">اسم العائلة</label>
                            <input type="text" class="form-control auth-input" value="{{ $user->last_name ?? '' }}" name="last_name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary">البريد الإلكتروني</label>
                            <input type="email" class="form-control auth-input" value="{{ $user->email ?? '' }}" name="email" readonly>
                            <small class="text-secondary">لا يمكن تغيير البريد الإلكتروني</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary">رقم الهاتف</label>
                            <input type="text" class="form-control auth-input" value="{{ $user->phone ?? '' }}" name="phone">
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-secondary">كلمة المرور الجديدة (اتركها فارغة إذا لم ترد التغيير)</label>
                            <input type="password" class="form-control auth-input" name="password" placeholder="••••••••">
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-secondary">تأكيد كلمة المرور</label>
                            <input type="password" class="form-control auth-input" name="password_confirmation" placeholder="••••••••">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-main px-4">
                                <i class="bi bi-check-circle"></i>
                                حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="glass rounded-4 p-4">
                    <h2 class="h6 fw-semibold mb-3">معلومات الحساب</h2>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">النقاط:</span>
                            <strong class="text-success">{{ number_format($user->points ?? 0) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">الرصيد:</span>
                            <strong class="text-info">${{ number_format($user->balance ?? 0, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">تاريخ التسجيل:</span>
                            <strong class="text-white">{{ $user->created_at->format('Y/m/d') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
