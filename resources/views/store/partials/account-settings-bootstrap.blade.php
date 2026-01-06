@php
    $isRegistrationComplete = $user ? $user->isRegistrationComplete() : false;
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
                                        <input type="email" class="form-control auth-input" value="{{ $user->email ?? '' }}" readonly>
                                        <small class="text-secondary">لا يمكن تغيير البريد الإلكتروني من هذه الصفحة</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-secondary">مقدمة واتساب</label>
                                        <input type="text" class="form-control auth-input" name="whatsapp_prefix" value="{{ old('whatsapp_prefix', $user->whatsapp_prefix ?? '+970') }}" {{ $isRegistrationComplete ? 'disabled' : 'required' }} placeholder="+970">
                                    </div>
                                    <div class="col-md-6">
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
                                    
                                    <div class="col-12">
                                        <label class="form-label small text-secondary">حالة رفع الهوية</label>
                                        <div class="mb-2">
                                            @if($user->id_image)
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> تم رفع الهوية
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-x-circle"></i> لم يتم رفع الهوية
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @php
                                        $status = $user->id_verified_status ?? 'unverified';
                                        $canModifyImage = $status !== 'verified'; // يمكن تعديل/حذف الصورة فقط إذا لم تكن موثقة
                                    @endphp
                                    
                                    @if($user->id_image)
                                    <div class="col-12">
                                        <label class="form-label small text-secondary">صورة الهوية</label>
                                        <div class="mb-3 d-flex align-items-start gap-3 flex-wrap">
                                            @php
                                                $idImageUrl = asset('storage/'.$user->id_image);
                                                try {
                                                    $helperUrl = \App\Helpers\ImageHelper::url($user->id_image);
                                                    if ($helperUrl) {
                                                        $idImageUrl = $helperUrl;
                                                    }
                                                } catch (\Exception $e) {
                                                    // استخدام asset كبديل
                                                }
                                            @endphp
                                            <img src="{{ $idImageUrl }}" 
                                                 alt="صورة الهوية" 
                                                 class="img-thumbnail border border-success shadow-sm" 
                                                 style="max-width: 150px; max-height: 150px; min-width: 150px; min-height: 150px; object-fit: cover; cursor: pointer;"
                                                 onclick="window.open(this.src, '_blank')"
                                                 onerror="console.error('خطأ في تحميل الصورة: ' + this.src); this.style.border='2px solid red';">
                                            
                                            @if($canModifyImage)
                                            <div class="d-flex flex-column gap-2">
                                                <form method="POST" action="{{ route('store.id-image.delete') }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف صورة الهوية؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i> حذف الصورة
                                                    </button>
                                                </form>
                                            </div>
                                            @else
                                            <div class="alert alert-info small mb-0" style="max-width: 300px;">
                                                <i class="bi bi-info-circle"></i> الصورة موثقة ولا يمكن تعديلها أو حذفها.
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                    
                                    @if($canModifyImage)
                                    <div class="col-12">
                                        <label class="form-label small text-secondary">{{ $user->id_image ? 'استبدال صورة الهوية' : 'رفع صورة الهوية' }}</label>
                                        <form method="POST" action="{{ route('store.id-image.upload') }}" enctype="multipart/form-data" class="mb-3">
                                            @csrf
                                            <input type="file" 
                                                   name="id_image" 
                                                   class="form-control auth-input" 
                                                   accept="image/*"
                                                   required>
                                            @error('id_image')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                            <button type="submit" class="btn btn-main mt-2">
                                                <i class="bi bi-upload"></i> {{ $user->id_image ? 'استبدال الصورة' : 'رفع صورة الهوية' }}
                                            </button>
                                        </form>
                                        <small class="text-secondary d-block">عند رفع صورة الهوية، ستكون الحالة تلقائياً "قيد التنفيذ" حتى يتم مراجعتها من قبل المدير.</small>
                                    </div>
                                    @endif
                                    
                                    <div class="col-12">
                                        <label class="form-label small text-secondary">حالة الحساب</label>
                                        <div class="mb-2">
                                            @if($status == 'verified')
                                                <span class="badge bg-primary">
                                                    <i class="bi bi-check-circle-fill"></i> موثق
                                                </span>
                                            @elseif($status == 'pending')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-clock-history"></i> قيد التنفيذ
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-x-circle"></i> غير موثق
                                                </span>
                                            @endif
                                        </div>
                                        <small class="text-secondary d-block mt-2">
                                            <strong>حالة الحساب:</strong><br>
                                            • <span class="text-primary">موثق</span>: الصورة محمية ولا يمكن تعديلها<br>
                                            • <span class="text-warning">قيد التنفيذ</span>: الصورة قيد المراجعة (يمكن حذفها)<br>
                                            • <span class="text-danger">غير موثق</span>: لم يتم رفع صورة أو تم رفضها
                                        </small>
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
                            </div>
                        </div>
                    </div>

                    <!-- عنواني الشخصي Accordion -->
                    <div class="accordion-item glass rounded-4 border-0">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed text-light" type="button" data-bs-toggle="collapse" data-bs-target="#addressCollapse" aria-expanded="false" aria-controls="addressCollapse" style="background: transparent; box-shadow: none;">
                                <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                    <h2 class="h6 fw-semibold mb-0">عنواني الشخصي</h2>
                                    <span class="badge bg-dark text-success small ms-2">
                                        سيتم استخدام هذا العنوان في الشحن والفواتير
                                    </span>
                                </div>
                            </button>
                        </h2>
                        <div id="addressCollapse" class="accordion-collapse collapse" data-bs-parent="#accountAccordion">
                            <div class="accordion-body p-4">
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

@endif
