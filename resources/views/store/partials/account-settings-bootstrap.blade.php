@php
    $isRegistrationComplete = $user ? $user->isRegistrationComplete() : false;

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

@if(!$user)
    <div class="container py-5 text-light">
        <div class="alert alert-danger">المستخدم غير موجود</div>
    </div>
@else
<style>
    /* تحسين التباين في حقول الإدخال */
    .auth-input {
        background: rgba(0, 0, 0, 0.6) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        color: #fff !important;
        padding: .65rem .75rem;
        border-radius: 12px;
    }
    
    .auth-input:disabled {
        background: rgba(0, 0, 0, 0.7) !important;
        border-color: rgba(255, 255, 255, 0.15) !important;
        color: #e0e0e0 !important;
        opacity: 1 !important;
        -webkit-text-fill-color: #e0e0e0 !important;
    }
    
    .auth-input[readonly] {
        background: rgba(0, 0, 0, 0.75) !important;
        border-color: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
        -webkit-text-fill-color: #ffffff !important;
    }
    
    .auth-input:focus {
        background: rgba(0, 0, 0, 0.8) !important;
        color: #fff !important;
        border-color: var(--primary, #0db777) !important;
        box-shadow: 0 0 0 .15rem rgba(13, 183, 119, .25) !important;
        -webkit-text-fill-color: #fff !important;
    }
    
    .auth-input::placeholder {
        color: rgba(255, 255, 255, 0.5) !important;
        opacity: 1;
    }
    
    /* التأكد من أن النص في الحقول واضح */
    input.auth-input,
    textarea.auth-input {
        -webkit-text-fill-color: inherit !important;
    }
</style>

<section class="py-5 text-light">
    <div class="container">
        <h1 class="h4 fw-bold mb-4">إعدادات الحساب الشخصي</h1>

        {{-- رسائل النجاح / الفشل --}}
        @if (session('status'))
            <div class="alert alert-success glass small">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger glass small">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="accordion" id="accountAccordion">
                    <!-- المعلومات الشخصية Accordion -->
                    <div class="accordion-item glass rounded-4 mb-3 border-0">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed text-light" type="button" data-bs-toggle="collapse" data-bs-target="#personalInfoCollapse" aria-expanded="false" aria-controls="personalInfoCollapse" style="background: transparent; box-shadow: none;">
                                <h2 class="h6 fw-semibold mb-0">المعلومات الشخصية</h2>
                            </button>
                        </h2>
                        <div id="personalInfoCollapse" class="accordion-collapse collapse" data-bs-parent="#accountAccordion">
                            <div class="accordion-body p-4">
                                @if($isRegistrationComplete)
                                    <div class="alert alert-info mb-3 small">
                                        <i class="bi bi-info-circle"></i> تم إكمال التسجيل. المعلومات الشخصية غير قابلة للتعديل.
                                    </div>
                                @endif
                                
                                <form method="POST" action="{{ route('store.address.update') }}" class="row g-3">
                                    @csrf
                                    <div class="col-md-6">
                                        <label class="form-label small text-secondary">الاسم الأول</label>
                                        <input type="text" class="form-control auth-input" name="first_name" value="{{ old('first_name', $user->first_name ?? '') }}" {{ $isRegistrationComplete ? 'disabled' : 'required' }}>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-secondary">اسم العائلة</label>
                                        <input type="text" class="form-control auth-input" name="last_name" value="{{ old('last_name', $user->last_name ?? '') }}" {{ $isRegistrationComplete ? 'disabled' : 'required' }}>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-secondary">البريد الإلكتروني</label>
                                        <input type="email" class="form-control auth-input" name="email" value="{{ old('email', $user->email ?? '') }}">
                                        <small class="text-secondary">يمكنك إضافة أو تحديث بريدك الإلكتروني هنا</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-secondary">مقدمة واتساب</label>
                                        <select class="form-select auth-input" name="whatsapp_prefix" {{ $isRegistrationComplete ? 'disabled' : 'required' }}>
                                            @foreach($countryDialCodes as $country)
                                                <option value="{{ $country['code'] }}" {{ old('whatsapp_prefix', $user->whatsapp_prefix ?? '+970') === $country['code'] ? 'selected' : '' }}>
                                                    {{ $country['flag'] }} {{ $country['name'] }} ({{ $country['code'] }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label small text-secondary">رقم الهاتف</label>
                                        <input type="text" class="form-control auth-input" name="phone" value="{{ old('phone', $user->phone ?? '') }}" {{ $isRegistrationComplete ? 'disabled' : 'required' }}>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small text-secondary">تاريخ الميلاد</label>
                                        <div class="row g-2">
                                            <div class="col-4">
                                                <input type="number" class="form-control auth-input" name="birth_year" value="{{ old('birth_year', $user->birth_year ?? '') }}" {{ $isRegistrationComplete ? 'disabled' : '' }} placeholder="السنة" min="1900" max="{{ date('Y') }}">
                                            </div>
                                            <div class="col-4">
                                                <input type="number" class="form-control auth-input" name="birth_month" value="{{ old('birth_month', $user->birth_month ?? '') }}" {{ $isRegistrationComplete ? 'disabled' : '' }} placeholder="الشهر" min="1" max="12">
                                            </div>
                                            <div class="col-4">
                                                <input type="number" class="form-control auth-input" name="birth_day" value="{{ old('birth_day', $user->birth_day ?? '') }}" {{ $isRegistrationComplete ? 'disabled' : '' }} placeholder="اليوم" min="1" max="31">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    @if(!$isRegistrationComplete)
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-main px-4">
                                            <i class="bi bi-save"></i>
                                            حفظ المعلومات الشخصية
                                        </button>
                                    </div>
                                    @endif
                                </form>

                                {{-- قسم الهوية منفصل عن فورم المعلومات --}}
                                @php
                                    $status = $user->id_verified_status ?? 'unverified';
                                    $canModifyImage = $status !== 'verified'; // يمكن تعديل/حذف الصورة فقط إذا لم تكن موثقة
                                    $idImageUrl = null;
                                    if ($user->id_image) {
                                        $idImageUrl = asset('storage/'.$user->id_image);
                                        try {
                                            $helperUrl = \App\Helpers\ImageHelper::url($user->id_image);
                                            if ($helperUrl) {
                                                $idImageUrl = $helperUrl;
                                            }
                                        } catch (\Exception $e) {
                                            // استخدام asset كبديل
                                        }
                                    }
                                @endphp

                                <div class="mt-4 p-3 rounded-3 bg-dark bg-opacity-25 border border-secondary-subtle">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                        <div>
                                            <label class="form-label small text-secondary mb-1">حالة رفع الهوية</label>
                                            @if($user->id_image)
                                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> تم رفع الهوية</span>
                                            @else
                                                <span class="badge bg-danger"><i class="bi bi-x-circle"></i> لم يتم رفع الهوية</span>
                                            @endif
                                        </div>
                                        <div>
                                            <label class="form-label small text-secondary mb-1">حالة الحساب</label>
                                            @if($status == 'verified')
                                                <span class="badge bg-primary"><i class="bi bi-check-circle-fill"></i> موثق</span>
                                            @elseif($status == 'pending')
                                                <span class="badge bg-warning text-dark"><i class="bi bi-clock-history"></i> قيد التنفيذ</span>
                                            @else
                                                <span class="badge bg-danger"><i class="bi bi-x-circle"></i> غير موثق</span>
                                            @endif
                                        </div>
                                    </div>

                                    @if($user->id_image)
                                        <div class="mb-3 d-flex align-items-start gap-3 flex-wrap">
                                            <div class="text-secondary small w-100">معاينة صورة الهوية</div>
                                            <img src="{{ $idImageUrl }}" 
                                                 alt="صورة الهوية" 
                                                 class="img-thumbnail border border-success shadow-sm" 
                                                 style="max-width: 150px; max-height: 150px; min-width: 150px; min-height: 150px; object-fit: cover; cursor: pointer;"
                                                 onclick="window.open(this.src, '_blank')"
                                                 onerror="console.error('خطأ في تحميل الصورة: ' + this.src); this.style.border='2px solid red';">
                                            @if($canModifyImage)
                                                <form method="POST" action="{{ route('store.id-image.delete') }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف صورة الهوية؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i> حذف الصورة
                                                    </button>
                                                </form>
                                            @else
                                                <div class="alert alert-info small mb-0" style="max-width: 300px;">
                                                    <i class="bi bi-info-circle"></i> الصورة موثقة ولا يمكن تعديلها أو حذفها.
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    @if($canModifyImage)
                                        <form method="POST" action="{{ route('store.id-image.upload') }}" enctype="multipart/form-data" class="row g-2">
                                            @csrf
                                            <div class="col-12">
                                                <label class="form-label small text-secondary">{{ $user->id_image ? 'استبدال صورة الهوية' : 'رفع صورة الهوية' }}</label>
                                                <input type="file" name="id_image" class="form-control auth-input" accept="image/*" required>
                                                @error('id_image')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12 d-flex gap-2 align-items-center">
                                                <button type="submit" class="btn btn-main">
                                                    <i class="bi bi-upload"></i> {{ $user->id_image ? 'استبدال الصورة' : 'رفع صورة الهوية' }}
                                                </button>
                                                <small class="text-secondary">عند رفع صورة الهوية، ستكون الحالة تلقائياً "قيد التنفيذ" حتى تتم المراجعة.</small>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- عنواني الشخصي Accordion (إدارة عناوين متعددة) -->
                    <div class="accordion-item glass rounded-4 border-0">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed text-light" type="button" data-bs-toggle="collapse" data-bs-target="#addressCollapse" aria-expanded="false" aria-controls="addressCollapse" style="background: transparent; box-shadow: none;">
                                <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                    <h2 class="h6 fw-semibold mb-0">{{ __('addresses.section_title') }}</h2>
                                    <span class="badge bg-dark text-success small ms-2">
                                        {{ __('addresses.hint_shipping') }}
                                    </span>
                                </div>
                            </button>
                        </h2>
                        <div id="addressCollapse" class="accordion-collapse collapse" data-bs-parent="#accountAccordion">
                            <div class="accordion-body p-4">
                                {{-- بطاقات العناوين المحفوظة --}}
                                <div class="mb-3 d-flex flex-wrap gap-3">
                                    @forelse($user->addresses as $address)
                                        <div class="glass p-3 rounded-3" style="min-width:260px;max-width:320px;">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <strong class="text-truncate">{{ $address->city ?? '-' }}</strong>
                                                @if($address->is_default)
                                                    <span class="badge bg-success text-dark">{{ __('addresses.default') }}</span>
                                                @endif
                                            </div>
                                            <div class="small text-secondary text-start">
                                                {{ $address->governorate }}<br>
                                                {{ $address->country_code }} {{ $address->phone }}<br>
                                                {{ $address->street }}
                                            </div>
                                            <div class="mt-2 d-flex flex-wrap gap-2">
                                                @unless($address->is_default)
                                                <form method="POST" action="{{ route('store.addresses.default', $address) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                                        {{ __('addresses.set_default') }}
                                                    </button>
                                                </form>
                                                @endunless
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-info"
                                                        onclick='fillAddressForm(@json($address))'>
                                                    {{ __('addresses.edit_address') }}
                                                </button>
                                                <form method="POST"
                                                      action="{{ route('store.addresses.destroy', $address) }}"
                                                      onsubmit="return confirm(@json(__('addresses.confirm_delete')));">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        {{ __('addresses.delete') ?? __('common.delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-secondary small mb-0">{{ __('addresses.no_addresses') }}</p>
                                    @endforelse
                                </div>

                                <button type="button" class="btn btn-main mb-3" onclick="openNewAddressForm()">
                                    {{ __('addresses.add_new') }}
                                </button>

                                {{-- نموذج حفظ / تعديل العنوان (مخفي افتراضياً ليشبه تجربة علي إكسبريس) --}}
                                <div id="addressFormContainer" class="mt-3 d-none">
                                    <form method="POST" action="{{ route('store.addresses.save') }}" class="row g-3" id="addressForm">
                                        @csrf
                                        <input type="hidden" name="address_id" id="address_id">
                                        <div class="col-md-6">
                                            <label class="form-label small text-secondary">{{ __('addresses.first_name') }}</label>
                                            <input type="text" class="form-control auth-input" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-secondary">{{ __('addresses.last_name') }}</label>
                                            <input type="text" class="form-control auth-input" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label small text-secondary">{{ __('addresses.city') }}</label>
                                            <input type="text" class="form-control auth-input" name="city" value="{{ old('city', $user->city) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-secondary">{{ __('addresses.governorate') }}</label>
                                            <input type="text" class="form-control auth-input" name="governorate" value="{{ old('governorate', $user->governorate) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-secondary">{{ __('addresses.zip_code') }}</label>
                                            <input type="text" class="form-control auth-input" name="zip_code" value="{{ old('zip_code', $user->zip_code) }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label small text-secondary">{{ __('addresses.country_code') }}</label>
                                            <input type="text" class="form-control auth-input" name="country_code" value="{{ old('country_code', $user->country_code ?? '+970') }}" placeholder="+970">
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label small text-secondary">{{ __('addresses.phone') }}</label>
                                            <input type="text" class="form-control auth-input" name="phone" value="{{ old('phone', $user->phone) }}" required>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label small text-secondary">{{ __('addresses.street') }}</label>
                                            <input type="text" class="form-control auth-input" name="street" value="{{ old('street', $user->address) }}" placeholder="{{ __('addresses.street_placeholder') }}">
                                        </div>

                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1" id="is_default" name="is_default">
                                                <label class="form-check-label text-secondary small" for="is_default">
                                                    {{ __('addresses.set_default') }}
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-12 d-flex gap-2">
                                            <button type="submit" class="btn btn-main px-4">
                                                <i class="bi bi-geo-alt-fill"></i>
                                                {{ __('addresses.save') }}
                                            </button>
                                            <button type="button" class="btn btn-outline-light px-4" onclick="closeAddressForm()">
                                                {{ __('addresses.cancel') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="glass rounded-4 p-4 mb-4">
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
                            <strong class="text-white">{{ $user->created_at ? $user->created_at->format('Y/m/d') : 'غير محدد' }}</strong>
                        </div>
                    </div>
                </div>

                <div class="glass rounded-4 p-4">
                    <h2 class="h6 fw-semibold mb-3">بيانات تسجيل الدخول</h2>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">آخر تسجيل دخول:</span>
                            <strong class="text-white">
                                @if($user->last_login_at)
                                    {{ $user->last_login_at->format('Y/m/d H:i') }}
                                @else
                                    <span class="text-secondary">لم يتم تسجيل الدخول بعد</span>
                                @endif
                            </strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">البريد الإلكتروني:</span>
                            <strong class="text-white small">{{ $user->email }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">مقدمة واتساب:</span>
                            <strong class="text-white">{{ $user->whatsapp_prefix ?? 'غير محدد' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">رقم الهاتف:</span>
                            <strong class="text-white">{{ $user->phone ?? 'غير محدد' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function resetAddressForm() {
    const form = document.getElementById('addressForm');
    if (!form) return;
    form.reset();
    document.getElementById('address_id').value = '';
    const isDefault = document.getElementById('is_default');
    if (isDefault) {
        isDefault.checked = false;
    }
}

function openNewAddressForm() {
    resetAddressForm();
    const container = document.getElementById('addressFormContainer');
    if (container) {
        container.classList.remove('d-none');
        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function fillAddressForm(address) {
    const form = document.getElementById('addressForm');
    if (!form) return;
    document.getElementById('address_id').value = address.id;
    form.first_name.value = address.first_name || '';
    form.last_name.value = address.last_name || '';
    form.city.value = address.city || '';
    form.governorate.value = address.governorate || '';
    form.zip_code.value = address.zip_code || '';
    form.country_code.value = address.country_code || '';
    form.phone.value = address.phone || '';
    form.street.value = address.street || '';
    document.getElementById('is_default').checked = !!address.is_default;

    const container = document.getElementById('addressFormContainer');
    if (container) {
        container.classList.remove('d-none');
        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // فتح الأكورديون إذا كان مغلقاً
    const collapse = document.getElementById('addressCollapse');
    if (collapse && !collapse.classList.contains('show')) {
        const bsCollapse = bootstrap.Collapse.getOrCreateInstance(collapse);
        bsCollapse.show();
    }
}

function closeAddressForm() {
    const container = document.getElementById('addressFormContainer');
    if (container) {
        container.classList.add('d-none');
    }
}
</script>

@endif
