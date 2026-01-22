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

                    <!-- إعدادات العملة Accordion -->
                    <div class="accordion-item glass rounded-4 mb-3 border-0">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed text-light" type="button" data-bs-toggle="collapse" data-bs-target="#currencyCollapse" aria-expanded="false" aria-controls="currencyCollapse" style="background: transparent; box-shadow: none;">
                                <h2 class="h6 fw-semibold mb-0">إعدادات العملة</h2>
                            </button>
                        </h2>
                        <div id="currencyCollapse" class="accordion-collapse collapse" data-bs-parent="#accountAccordion">
                            <div class="accordion-body p-4">
                                <form id="currency-form" method="POST" action="{{ route('store.currency.update') }}" onsubmit="return handleCurrencyFormSubmit(event)">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label small text-secondary mb-2">اختر العملة المفضلة</label>
                                        <select class="form-select auth-input" name="currency" id="currency-select" required>
                                            <option value="USD" {{ ($user->preferred_currency ?? 'USD') === 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                                            <option value="ILS" {{ ($user->preferred_currency ?? 'USD') === 'ILS' ? 'selected' : '' }}>شيكل إسرائيلي (ILS)</option>
                                            <option value="JOD" {{ ($user->preferred_currency ?? 'USD') === 'JOD' ? 'selected' : '' }}>دينار أردني (JOD)</option>
                                        </select>
                                        <small class="text-secondary d-block mt-2">
                                            سيتم تحديث جميع الأسعار تلقائياً حسب سعر الصرف الحالي
                                        </small>
                                    </div>
                                    <div id="exchange-rates" class="mb-3 p-3 rounded-3 bg-dark bg-opacity-25 border border-secondary-subtle">
                                        <div class="text-secondary small mb-2">أسعار الصرف الحالية:</div>
                                        <div id="rates-display" class="text-white small">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                <span class="visually-hidden">جاري التحميل...</span>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-main px-4">
                                        <i class="bi bi-currency-exchange"></i>
                                        حفظ العملة المفضلة
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- حركاتي Accordion -->
                    <div class="accordion-item glass rounded-4 mb-3 border-0">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed text-light" type="button" data-bs-toggle="collapse" data-bs-target="#activitiesCollapse" aria-expanded="false" aria-controls="activitiesCollapse" style="background: transparent; box-shadow: none;">
                                <h2 class="h6 fw-semibold mb-0">حركاتي</h2>
                            </button>
                        </h2>
                        <div id="activitiesCollapse" class="accordion-collapse collapse" data-bs-parent="#accountAccordion">
                            <div class="accordion-body p-4">
                                <div class="mb-3">
                                    <p class="text-secondary small mb-3">
                                        جميع الحركات والنشاطات التي قمت بها في حسابك
                                    </p>
                                    
                                    @php($activities = $user->activities()->paginate(20))
                                    
                                    @if($activities->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-dark table-sm align-middle">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 20%">التاريخ والوقت</th>
                                                        <th style="width: 15%">نوع الحركة</th>
                                                        <th style="width: 50%">الوصف</th>
                                                        <th style="width: 15%">التفاصيل</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($activities as $activity)
                                                        <tr>
                                                            <td class="text-secondary small">
                                                                {{ $activity->created_at->format('Y/m/d H:i') }}
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-primary small">
                                                                    {{ $activity->action }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div class="text-white small">{{ $activity->description ?? '-' }}</div>
                                                                @if($activity->metadata)
                                                                    @php($meta = $activity->metadata)
                                                                    @if(isset($meta['product_id']))
                                                                        <div class="text-secondary" style="font-size: 0.75rem;">
                                                                            منتج #{{ $meta['product_id'] }}
                                                                        </div>
                                                                    @endif
                                                                    @if(isset($meta['amount']))
                                                                        <div class="text-success" style="font-size: 0.75rem;">
                                                                            المبلغ: ${{ number_format($meta['amount'], 2) }}
                                                                        </div>
                                                                    @endif
                                                                    @if(isset($meta['points']))
                                                                        <div class="text-warning" style="font-size: 0.75rem;">
                                                                            النقاط: {{ number_format($meta['points']) }}
                                                                        </div>
                                                                    @endif
                                                                    @if(isset($meta['order_id']))
                                                                        <div class="text-info" style="font-size: 0.75rem;">
                                                                            طلب #{{ $meta['order_id'] }}
                                                                        </div>
                                                                    @endif
                                                                    @if(isset($meta['quantity']))
                                                                        <div class="text-primary" style="font-size: 0.75rem;">
                                                                            الكمية: {{ $meta['quantity'] }}
                                                                        </div>
                                                                    @endif
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($activity->metadata)
                                                                    <button class="btn btn-sm btn-outline-info" 
                                                                            type="button"
                                                                            data-bs-toggle="modal" 
                                                                            data-bs-target="#activityModal{{ $activity->id }}">
                                                                        <i class="bi bi-info-circle"></i>
                                                                    </button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        
                                                        @if($activity->metadata)
                                                            <div class="modal fade" id="activityModal{{ $activity->id }}" tabindex="-1">
                                                                <div class="modal-dialog modal-dialog-centered">
                                                                    <div class="modal-content glass border border-secondary-subtle">
                                                                        <div class="modal-header border-bottom border-secondary-subtle">
                                                                            <h5 class="modal-title text-light">تفاصيل الحركة</h5>
                                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                        </div>
                                                                        <div class="modal-body text-light">
                                                                            <div class="mb-2">
                                                                                <strong>النوع:</strong> {{ $activity->action }}
                                                                            </div>
                                                                            <div class="mb-2">
                                                                                <strong>الوصف:</strong> {{ $activity->description ?? '-' }}
                                                                            </div>
                                                                            <div class="mb-2">
                                                                                <strong>التاريخ:</strong> {{ $activity->created_at->format('Y/m/d H:i:s') }}
                                                                            </div>
                                                                            @if($activity->metadata)
                                                                                <div class="mt-3">
                                                                                    <strong>البيانات الإضافية:</strong>
                                                                                    <pre class="bg-dark p-2 rounded mt-2 small text-white" style="max-height: 200px; overflow-y: auto;">{{ json_encode($activity->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        <div class="modal-footer border-top border-secondary-subtle">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <div class="mt-3">
                                            {{ $activities->links() }}
                                        </div>
                                    @else
                                        <div class="text-center py-5">
                                            <i class="bi bi-inbox display-4 text-secondary mb-3"></i>
                                            <p class="text-secondary">لا توجد حركات مسجلة بعد</p>
                                        </div>
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
                                    @forelse($user->addresses ?? [] as $address)
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
                            <strong class="text-info" id="user-balance" data-price-usd="{{ $user->balance ?? 0 }}">{{ $currencyHelper::convertAndFormat($user->balance ?? 0, $userCurrency) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">تاريخ التسجيل:</span>
                            <strong class="text-white">
                                @if($user->created_at)
                                    {{ $user->created_at->format('Y/m/d H:i') }}
                                @else
                                    <span class="text-secondary">غير محدد</span>
                                @endif
                            </strong>
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
                                    <span class="text-secondary small ms-2">({{ $user->last_login_at->diffForHumans() }})</span>
                                @else
                                    <span class="text-secondary">لم يتم تسجيل الدخول بعد</span>
                                @endif
                            </strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">تاريخ إنشاء الحساب:</span>
                            <strong class="text-white">
                                @if($user->created_at)
                                    {{ $user->created_at->format('Y/m/d H:i') }}
                                    <span class="text-secondary small ms-2">({{ $user->created_at->diffForHumans() }})</span>
                                @else
                                    <span class="text-secondary">غير محدد</span>
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
                    
                    {{-- زر حذف الحساب --}}
                    <div class="mt-4 pt-3 border-top border-secondary-subtle">
                        <form method="POST" action="{{ route('store.account.delete') }}" id="deleteAccountForm">
                            @csrf
                            @method('DELETE')
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="confirm_delete" id="confirm_delete" value="1" required>
                                <label class="form-check-label text-danger small" for="confirm_delete">
                                    أؤكد أنني أريد حذف حسابي بشكل نهائي. هذا الإجراء لا يمكن التراجع عنه.
                                </label>
                            </div>
                            <button type="button" class="btn btn-danger w-100" id="deleteAccountBtn">
                                <i class="bi bi-trash"></i> حذف الحساب
                            </button>
                        </form>
                    </div>

                    {{-- نافذة تأكيد حذف الحساب --}}
                    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content glass border border-danger border-opacity-50 rounded-4 overflow-hidden">
                                <div class="modal-header border-0 pb-0">
                                    <div class="d-flex align-items-center gap-3 w-100">
                                        <div class="rounded-circle bg-danger bg-opacity-25 p-3 d-flex align-items-center justify-content-center">
                                            <i class="bi bi-exclamation-triangle-fill text-danger fs-2"></i>
                                        </div>
                                        <div>
                                            <h5 class="modal-title text-light mb-1" id="deleteAccountModalLabel">هل أنت متأكد من حذف حسابك؟</h5>
                                            <p class="text-secondary small mb-0">تأكد من قراءة التفاصيل أدناه قبل المتابعة</p>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                                </div>
                                <div class="modal-body pt-3 text-light">
                                    <p class="text-secondary mb-3">هذا الإجراء سيحذف بشكل نهائي:</p>
                                    <ul class="list-unstyled mb-0">
                                        <li class="d-flex align-items-center gap-2 py-2 border-bottom border-secondary border-opacity-25">
                                            <i class="bi bi-person-fill text-danger opacity-75"></i>
                                            <span>جميع بياناتك الشخصية</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2 py-2 border-bottom border-secondary border-opacity-25">
                                            <i class="bi bi-bag-fill text-danger opacity-75"></i>
                                            <span>جميع طلباتك وسجل الشراء</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2 py-2 border-bottom border-secondary border-opacity-25">
                                            <i class="bi bi-geo-alt-fill text-danger opacity-75"></i>
                                            <span>جميع عناوينك المحفوظة</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2 py-2">
                                            <i class="bi bi-currency-dollar text-danger opacity-75"></i>
                                            <span>جميع نقاطك ورصيدك</span>
                                        </li>
                                    </ul>
                                    <div class="alert alert-danger d-flex align-items-center gap-2 mt-4 mb-0 py-3" role="alert">
                                        <i class="bi bi-shield-exclamation fs-4"></i>
                                        <div>
                                            <strong>تنبيه:</strong> هذا الإجراء لا يمكن التراجع عنه. لن تتمكن من استرداد أي بيانات بعد الحذف.
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0 gap-2 flex-row-reverse">
                                    <button type="button" class="btn btn-danger px-4" id="confirmDeleteAccountBtn">
                                        <i class="bi bi-trash-fill"></i> نعم، احذف حسابي
                                    </button>
                                    <button type="button" class="btn btn-outline-light px-4" data-bs-dismiss="modal">
                                        <i class="bi bi-x-lg"></i> إلغاء
                                    </button>
                                </div>
                            </div>
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

// تحميل أسعار الصرف
async function loadExchangeRates() {
    try {
        const response = await fetch('{{ route("api.exchange-rates") }}');
        const data = await response.json();
        
        if (data.success) {
            const rates = data.rates;
            const ratesDisplay = document.getElementById('rates-display');
            if (ratesDisplay) {
                ratesDisplay.innerHTML = `
                    <div class="d-flex justify-content-between mb-1">
                        <span>1 USD =</span>
                        <strong>${rates.USD_to_ILS?.toFixed(4) || 'N/A'} ILS</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>1 USD =</span>
                        <strong>${rates.USD_to_JOD?.toFixed(4) || 'N/A'} JOD</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>1 ILS =</span>
                        <strong>${rates.ILS_to_JOD?.toFixed(4) || 'N/A'} JOD</strong>
                    </div>
                `;
            }
        }
    } catch (error) {
        console.error('خطأ في تحميل أسعار الصرف:', error);
        const ratesDisplay = document.getElementById('rates-display');
        if (ratesDisplay) {
            ratesDisplay.innerHTML = '<span class="text-danger small">فشل تحميل أسعار الصرف</span>';
        }
    }
}

// تحديث الرصيد عند تغيير العملة
async function updateBalanceDisplay(currency) {
    // تحديث الرصيد في صفحة الإعدادات
    const balanceElement = document.getElementById('user-balance');
    if (balanceElement) {
        const balanceUSD = parseFloat(balanceElement.getAttribute('data-price-usd')) || 0;
        await updateSingleBalance(balanceElement, balanceUSD, currency);
    }
    
    // تحديث الرصيد في الـ header (desktop)
    const headerBalanceDesktop = document.getElementById('header-balance-desktop');
    if (headerBalanceDesktop) {
        const balanceUSD = parseFloat(headerBalanceDesktop.getAttribute('data-price-usd')) || 0;
        await updateSingleBalance(headerBalanceDesktop, balanceUSD, currency);
    }
    
    // تحديث الرصيد في الـ header (mobile)
    const headerBalanceMobile = document.getElementById('header-balance-mobile');
    if (headerBalanceMobile) {
        const balanceUSD = parseFloat(headerBalanceMobile.getAttribute('data-price-usd')) || 0;
        await updateSingleBalance(headerBalanceMobile, balanceUSD, currency);
    }
    
    // تحديث الرصيد في صفحة الدفع
    const checkoutBalance = document.getElementById('checkout-balance');
    if (checkoutBalance) {
        const balanceUSD = parseFloat(checkoutBalance.getAttribute('data-price-usd')) || 0;
        await updateSingleBalance(checkoutBalance, balanceUSD, currency);
    }
    
    // تحديث الرصيد في صفحة دفع السلة
    const cartCheckoutBalance = document.getElementById('cart-checkout-balance');
    if (cartCheckoutBalance) {
        const balanceUSD = parseFloat(cartCheckoutBalance.getAttribute('data-price-usd')) || 0;
        await updateSingleBalance(cartCheckoutBalance, balanceUSD, currency);
    }
    
    // تحديث جميع الأسعار الأخرى في الصفحة
    if (window.currencySystem) {
        await window.currencySystem.updateAllPrices();
    }
}

// دالة مساعدة لتحديث رصيد واحد
async function updateSingleBalance(element, balanceUSD, currency) {
    // التأكد من تحميل أسعار الصرف
    if (window.currencySystem) {
        if (!window.currencySystem.exchangeRates) {
            await window.currencySystem.loadExchangeRates();
        }
        
        const convertedBalance = window.currencySystem.convertPrice(balanceUSD, currency);
        const symbol = window.currencySystem.getCurrencySymbol(currency);
        element.textContent = symbol + convertedBalance.toFixed(2);
    } else {
        // استخدام قيم افتراضية إذا لم يتم تحميل النظام بعد
        let convertedBalance = balanceUSD;
        let symbol = '$';
        
        if (currency === 'ILS') {
            convertedBalance = balanceUSD * 3.65;
            symbol = '₪';
        } else if (currency === 'JOD') {
            convertedBalance = balanceUSD * 0.71;
            symbol = 'د.أ';
        }
        
        element.textContent = symbol + convertedBalance.toFixed(2);
    }
}

// معالجة إرسال form العملة
async function handleCurrencyFormSubmit(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    
    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.ok) {
            const selectedCurrency = document.getElementById('currency-select').value;
            
            // حفظ العملة في localStorage
            localStorage.setItem('preferred_currency', selectedCurrency);
            
            // إرسال event لتحديث جميع الأسعار
            window.dispatchEvent(new CustomEvent('currencyChanged', { detail: { currency: selectedCurrency } }));
            
            // تحديث جميع الأسعار
            if (window.currencySystem) {
                await window.currencySystem.updateAllPrices();
            }
            
            // إرسال form بشكل عادي لإظهار رسالة النجاح
            form.submit();
        } else {
            alert('حدث خطأ أثناء حفظ العملة');
        }
    } catch (error) {
        console.error('Error:', error);
        // في حالة الخطأ، إرسال form بشكل عادي
        return true;
    }
    
    return false;
}

// تحديث الرصيد عند تغيير العملة في الـ select
document.addEventListener('DOMContentLoaded', async function() {
    const currencySelect = document.getElementById('currency-select');
    if (currencySelect) {
        // تحديث الرصيد عند تغيير العملة
        currencySelect.addEventListener('change', async function() {
            const selectedCurrency = this.value;
            
            // حفظ العملة في localStorage (للزوار) أو تحديثها في النظام
            if (window.currencySystem) {
                window.currencySystem.setPreferredCurrency(selectedCurrency);
            } else {
                localStorage.setItem('preferred_currency', selectedCurrency);
            }
            
            // إرسال event مخصص لتحديث جميع الأسعار في الموقع
            window.dispatchEvent(new CustomEvent('currencyChanged', { detail: { currency: selectedCurrency } }));
            
            // تحديث جميع الأرصدة والأسعار
            await updateBalanceDisplay(selectedCurrency);
            
            // تحديث جميع الأسعار في الصفحة
            if (window.currencySystem) {
                await window.currencySystem.updateAllPrices();
            }
        });
        
        // تحديث الرصيد عند تحميل الصفحة
        const currentCurrency = currencySelect.value;
        await updateBalanceDisplay(currentCurrency);
    }
    
    // تحميل أسعار الصرف وتحديث الرصيد مرة أخرى بعد التحميل
    loadExchangeRates().then(async () => {
        if (currencySelect && window.currencySystem) {
            await updateBalanceDisplay(currencySelect.value);
        }
    });
    
    // تحديث أسعار الصرف كل 5 دقائق
    setInterval(loadExchangeRates, 300000);
});

// حذف الحساب: فتح الـ modal والتأكيد
(function() {
    const deleteBtn = document.getElementById('deleteAccountBtn');
    const confirmBtn = document.getElementById('confirmDeleteAccountBtn');
    const checkbox = document.getElementById('confirm_delete');
    const form = document.getElementById('deleteAccountForm');
    const modalEl = document.getElementById('deleteAccountModal');

    if (deleteBtn && modalEl && form) {
        deleteBtn.addEventListener('click', function() {
            if (!checkbox.checked) {
                const msg = document.createElement('div');
                msg.className = 'alert alert-warning alert-dismissible fade show small mb-0';
                msg.innerHTML = '<i class="bi bi-exclamation-circle me-2"></i>يجب تأكيد حذف الحساب أولاً بتفعيل المربع أعلاه.<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                const container = form.closest('.glass');
                if (container && !container.querySelector('.alert-warning')) {
                    form.insertAdjacentElement('beforebegin', msg);
                    setTimeout(function() { msg.remove(); }, 5000);
                }
                return;
            }
            const modal = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false });
            modal.show();
        });
    }

    if (confirmBtn && form) {
        confirmBtn.addEventListener('click', function() {
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
            form.submit();
        });
    }
})();
</script>

@endif
