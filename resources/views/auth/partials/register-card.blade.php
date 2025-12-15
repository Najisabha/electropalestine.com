<x-auth-card title="إنشاء حساب جديد">
    <form method="POST" action="{{ route('register.attempt') }}" class="d-flex flex-column gap-4" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small text-secondary">الاسم الأول</label>
                <input type="text" name="first_name" value="{{ old('first_name') }}" required class="form-control auth-input">
                @error('first_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label small text-secondary">الاسم الأخير</label>
                <input type="text" name="last_name" value="{{ old('last_name') }}" required class="form-control auth-input">
                @error('last_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label small text-secondary">البريد الإلكتروني</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="form-control auth-input">
                @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label small text-secondary">مقدمة واتساب</label>
                <input type="text" name="whatsapp_prefix" value="{{ old('whatsapp_prefix', '+970') }}" required class="form-control auth-input">
                @error('whatsapp_prefix')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label small text-secondary">رقم الجوال</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required class="form-control auth-input">
                @error('phone')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label small text-secondary">تاريخ الميلاد</label>
                <div class="row g-2">
                    <div class="col-4">
                        <input type="number" name="birth_year" value="{{ old('birth_year') }}" required class="form-control auth-input" placeholder="السنة">
                        @error('birth_year')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-4">
                        <input type="number" name="birth_month" value="{{ old('birth_month') }}" required class="form-control auth-input" placeholder="الشهر">
                        @error('birth_month')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-4">
                        <input type="number" name="birth_day" value="{{ old('birth_day') }}" required class="form-control auth-input" placeholder="اليوم">
                        @error('birth_day')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label small text-secondary">كلمة المرور</label>
                <input type="password" name="password" required class="form-control auth-input">
                @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label small text-secondary">تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation" required class="form-control auth-input">
            </div>
            <div class="col-12">
                <label class="form-label small text-secondary">صورة الهوية (اختياري)</label>
                <input type="file" name="id_image" class="form-control auth-input">
                @error('id_image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
        </div>
        <button class="btn btn-main w-100 py-2 fw-semibold">إنشاء الحساب</button>
        <p class="text-center small text-secondary mb-0">
            لديك حساب بالفعل؟ <a href="{{ route('login') }}" class="link-success">تسجيل دخول</a>
        </p>
    </form>
</x-auth-card>

