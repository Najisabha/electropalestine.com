<section class="py-5 text-light">
    <div class="container">
        <h1 class="h4 fw-bold mb-4">إعدادات الحساب الشخصي</h1>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="glass rounded-4 p-4 mb-4">
                    <h2 class="h6 fw-semibold mb-3">المعلومات الشخصية</h2>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-secondary">الاسم الأول</label>
                            <input type="text" class="form-control auth-input" value="{{ $user->first_name ?? '' }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary">اسم العائلة</label>
                            <input type="text" class="form-control auth-input" value="{{ $user->last_name ?? '' }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary">البريد الإلكتروني</label>
                            <input type="email" class="form-control auth-input" value="{{ $user->email ?? '' }}" readonly>
                            <small class="text-secondary">لا يمكن تغيير البريد الإلكتروني من هذه الصفحة</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary">رقم الهاتف الحالي</label>
                            <input type="text" class="form-control auth-input" value="{{ $user->phone ?? '' }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="glass rounded-4 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h6 fw-semibold mb-0">عنواني الشخصي</h2>
                        <span class="badge bg-dark text-success small">
                            سيتم استخدام هذا العنوان في الشحن والفواتير
                        </span>
                    </div>

                    <form method="POST" action="{{ route('store.address.update') }}" class="row g-3">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label small text-secondary">الاسم الأول</label>
                            <input type="text" class="form-control auth-input" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary">اسم العائلة</label>
                            <input type="text" class="form-control auth-input" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small text-secondary">المدينة</label>
                            <input type="text" class="form-control auth-input" name="city" value="{{ old('city', $user->city) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary">البلدة / الحي</label>
                            <input type="text" class="form-control auth-input" name="district" value="{{ old('district', $user->district) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small text-secondary">المحافظة</label>
                            <input type="text" class="form-control auth-input" name="governorate" value="{{ old('governorate', $user->governorate) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary">الرمز البريدي ZIP</label>
                            <input type="text" class="form-control auth-input" name="zip_code" value="{{ old('zip_code', $user->zip_code) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small text-secondary">مقدمة البلد</label>
                            <input type="text" class="form-control auth-input" name="country_code" value="{{ old('country_code', $user->country_code ?? '+970') }}" placeholder="+970">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small text-secondary">رقم الهاتف</label>
                            <input type="text" class="form-control auth-input" name="phone" value="{{ old('phone', $user->phone) }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label small text-secondary">العنوان الكامل</label>
                            <input type="text" class="form-control auth-input" name="address" value="{{ old('address', $user->address) }}" required placeholder="اسم الشارع، رقم العمارة، أقرب علامة مميزة...">
                        </div>

                        <div class="col-12">
                            <label class="form-label small text-secondary">عنوان احتياطي (اختياري)</label>
                            <input type="text" class="form-control auth-input" name="secondary_address" value="{{ old('secondary_address', $user->secondary_address) }}" placeholder="عنوان بديل للتسليم عند الحاجة">
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-main px-4">
                                <i class="bi bi-geo-alt-fill"></i>
                                حفظ عنواني الشخصي
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
