@php
    // قائمة مقدمات الاتصال للدول مع الأعلام، تؤخذ من ملف الإعدادات
    // واختيار اسم الدولة حسب لغة التطبيق (عربي/إنجليزي)
    $locale = app()->getLocale();
    $displayNameKey = $locale === 'ar' ? 'name_ar' : 'name_en';
    $countryDialCodes = collect(config('dial_codes.countries', []))
        ->map(function ($country) use ($displayNameKey) {
            $country['name'] = $country[$displayNameKey] ?? $country['name_en'] ?? $country['name_ar'] ?? '';
            return $country;
        })
        ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
        ->values();
@endphp

<x-auth-card title="{{ __('common.register') }}">
    {{-- تسجيل بواسطة جوجل / فيسبوك --}}
    <div class="mb-3">
        <div class="d-flex flex-column gap-2">
            <a href="{{ route('social.redirect', ['provider' => 'google']) }}" class="btn btn-outline-main w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="fab fa-google text-danger"></i>
                <span class="small fw-semibold">{{ __('إنشاء حساب بواسطة جوجل') }}</span>
            </a>
            <a href="{{ route('social.redirect', ['provider' => 'facebook']) }}" class="btn btn-outline-main w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="fab fa-facebook-f text-primary"></i>
                <span class="small fw-semibold">{{ __('إنشاء حساب بواسطة فيسبوك') }}</span>
            </a>
        </div>
        <div class="d-flex align-items-center my-3">
            <div class="flex-grow-1 border-top border-secondary opacity-50"></div>
            <span class="px-2 small text-secondary">{{ __('أو إنشاء حساب بالطريقة التقليدية') }}</span>
            <div class="flex-grow-1 border-top border-secondary opacity-50"></div>
        </div>
    </div>

    <form method="POST" action="{{ route('register.attempt') }}" class="d-flex flex-column gap-4">
        @csrf
        
        {{-- بيانات أساسية --}}
        <div class="row g-3 mb-2">
            <div class="col-md-6">
                <label class="form-label small text-secondary">{{ __('common.first_name') }}</label>
                <input type="text" name="first_name" value="{{ old('first_name') }}" required class="form-control auth-input">
                @error('first_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label small text-secondary">{{ __('common.last_name') }}</label>
                <input type="text" name="last_name" value="{{ old('last_name') }}" required class="form-control auth-input">
                @error('last_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- بيانات التواصل --}}
        <div class="row g-3 mb-2">
            <div class="col-md-4">
                <label class="form-label small text-secondary">{{ __('common.whatsapp_prefix') }}</label>
                <select name="whatsapp_prefix" required class="form-select auth-input">
                    @foreach($countryDialCodes as $country)
                        <option value="{{ $country['code'] }}" {{ old('whatsapp_prefix', '+970') === $country['code'] ? 'selected' : '' }}>
                            {{ $country['flag'] }} {{ $country['name'] }} ({{ $country['code'] }})
                        </option>
                    @endforeach
                </select>
                @error('whatsapp_prefix')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-8">
                <label class="form-label small text-secondary">{{ __('common.phone') }}</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required class="form-control auth-input">
                @error('phone')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- كلمة المرور --}}
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small text-secondary">{{ __('common.password') }}</label>
                <input type="password" name="password" required class="form-control auth-input">
                @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label small text-secondary">{{ __('common.confirm_password') }}</label>
                <input type="password" name="password_confirmation" required class="form-control auth-input">
            </div>
        </div>
        <button class="btn btn-main w-100 py-2 fw-semibold">{{ __('common.register') }}</button>
        <p class="text-center small text-secondary mb-0">
            {{ __('common.have_account') }} <a href="{{ route('login') }}" class="link-success">{{ __('common.login') }}</a>
        </p>
    </form>
</x-auth-card>

