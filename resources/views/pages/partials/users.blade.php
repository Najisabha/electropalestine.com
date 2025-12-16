@php use Illuminate\Support\Facades\Storage; @endphp

<section class="container py-4 text-light">
    {{-- الهيدر والإحصائيات العلوية --}}
    <div class="glass p-4 rounded-3 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 fw-bold mb-0">إظهار المستخدمين</h1>
            <span class="badge bg-success text-dark">{{ $users->count() }} مستخدم</span>
        </div>
        <p class="text-secondary small mb-3">عرض البيانات الكاملة للمستخدمين وإدارة أدوارهم.</p>
        
        @if (session('status'))
            <div class="alert alert-success small py-2 mb-3">{{ session('status') }}</div>
        @endif
        
        @if ($errors->any())
            <div class="alert alert-danger small py-2 mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- جدول المستخدمين --}}
        <div class="table-responsive">
            <table class="table table-dark table-striped align-middle table-hover rounded-3 overflow-hidden">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم المستخدم</th>
                        <th>البريد الإلكتروني</th>
                        <th>رقم الجوال</th>
                        <th>الدور</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td><strong>{{ $user->id }}</strong></td>
                            <td>
                                <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td><span dir="ltr">{{ $user->whatsapp_prefix }}{{ $user->phone }}</span></td>
                            <td>
                                <span class="badge {{ strtolower($user->role) === 'admin' ? 'bg-danger' : 'bg-info text-dark' }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#userDetailsModal{{ $user->id }}">
                                        <i class="bi bi-eye"></i> التفاصيل
                                    </button>
                                    @if(auth()->check() && strtolower(auth()->user()->role) === 'admin')
                                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                            <i class="bi bi-pencil"></i> تعديل
                                        </button>
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف المستخدم {{ $user->first_name }} {{ $user->last_name }}؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i> حذف
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-4">لا يوجد مستخدمون حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- قسم المودال (Modals) --}}
    @foreach ($users as $user)
    
        {{-- 1. Modal التفاصيل الكاملة --}}
        <div class="modal fade" id="userDetailsModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content glass border border-secondary-subtle">
                    <div class="modal-header border-bottom border-secondary-subtle">
                        <h5 class="modal-title text-light">
                            <i class="bi bi-person-circle me-2"></i> تفاصيل: {{ $user->first_name }} {{ $user->last_name }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body text-light custom-scrollbar">
                        {{-- المحتوى: معلومات شخصية --}}
                        <div class="row g-4">
                            {{-- القسم الأيمن: البيانات --}}
                            <div class="col-lg-6">
                                <div class="p-3 rounded-3 bg-dark bg-opacity-50 h-100">
                                    <h6 class="text-success fw-bold mb-3 border-bottom border-secondary pb-2">المعلومات الشخصية</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><span class="text-secondary">الاسم الكامل:</span> {{ $user->first_name }} {{ $user->last_name }}</li>
                                        <li class="mb-2"><span class="text-secondary">البريد:</span> {{ $user->email }}</li>
                                        <li class="mb-2"><span class="text-secondary">الجوال:</span> <span dir="ltr">{{ $user->whatsapp_prefix }}{{ $user->phone }}</span></li>
                                        <li class="mb-2"><span class="text-secondary">الميلاد:</span> {{ $user->birth_year }}-{{ $user->birth_month }}-{{ $user->birth_day }}</li>
                                    </ul>
                                </div>
                            </div>
                            
                            {{-- القسم الأيسر: المالية والإحصائيات --}}
                            <div class="col-lg-6">
                                <div class="p-3 rounded-3 bg-dark bg-opacity-50 h-100">
                                    <h6 class="text-info fw-bold mb-3 border-bottom border-secondary pb-2">الإحصائيات والمالية</h6>
                                    <div class="row text-center g-2">
                                        <div class="col-6">
                                            <div class="p-2 border border-secondary rounded">
                                                <small class="text-secondary d-block">النقاط</small>
                                                <span class="fw-bold text-warning">{{ number_format($user->points ?? 0) }}</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-2 border border-secondary rounded">
                                                <small class="text-secondary d-block">الرصيد</small>
                                                <span class="fw-bold text-success">${{ number_format($user->balance ?? 0, 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="col-12 mt-3">
                                            <small class="text-secondary">تاريخ التسجيل: {{ $user->created_at->format('Y/m/d H:i') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- قسم الطلبات --}}
                            <div class="col-12">
                                <div class="p-3 rounded-3 bg-dark bg-opacity-50">
                                    <h6 class="text-primary fw-bold mb-3 border-bottom border-secondary pb-2">آخر الطلبات</h6>
                                    @if($user->orders && $user->orders->isNotEmpty())
                                        <div class="table-responsive">
                                            <table class="table table-sm table-dark mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>المنتج</th>
                                                        <th>السعر</th>
                                                        <th>الحالة</th>
                                                        <th>التاريخ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($user->orders->take(5) as $order)
                                                        <tr>
                                                            <td>#{{ $order->id }}</td>
                                                            <td>{{ $order->product_name }}</td>
                                                            <td>${{ $order->total }}</td>
                                                            <td><span class="badge bg-secondary">{{ $order->status }}</span></td>
                                                            <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-secondary text-center">لا توجد طلبات مسجلة.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer border-top border-secondary-subtle">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Modal تعديل المستخدم --}}
        @if(auth()->check() && strtolower(auth()->user()->role) === 'admin')
        <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content glass border border-secondary-subtle">
                    <div class="modal-header border-bottom border-secondary-subtle">
                        <h5 class="modal-title text-light">
                            <i class="bi bi-pencil-square me-2"></i> تعديل: {{ $user->first_name }} {{ $user->last_name }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body text-light custom-scrollbar">
                        <form method="POST" action="{{ route('admin.users.update', $user) }}" id="editForm{{ $user->id }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="row g-4">
                                {{-- القسم الأول: البيانات الأساسية --}}
                                <div class="col-lg-6">
                                    <h6 class="text-warning mb-3">البيانات الشخصية</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label text-secondary small">الاسم الأول</label>
                                            <input type="text" class="form-control auth-input" name="first_name" value="{{ $user->first_name }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-secondary small">اسم العائلة</label>
                                            <input type="text" class="form-control auth-input" name="last_name" value="{{ $user->last_name }}" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label text-secondary small">البريد الإلكتروني</label>
                                            <input type="email" class="form-control auth-input" name="email" value="{{ $user->email }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label text-secondary small">مقدمة واتساب</label>
                                            <input type="text" class="form-control auth-input" name="whatsapp_prefix" value="{{ $user->whatsapp_prefix }}" required>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label text-secondary small">رقم الجوال</label>
                                            <input type="text" class="form-control auth-input" name="phone" value="{{ $user->phone }}" required>
                                        </div>
                                        
                                        {{-- تاريخ الميلاد --}}
                                        <div class="col-4">
                                            <label class="form-label text-secondary small">اليوم</label>
                                            <input type="number" class="form-control auth-input" name="birth_day" value="{{ $user->birth_day }}" min="1" max="31">
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label text-secondary small">الشهر</label>
                                            <input type="number" class="form-control auth-input" name="birth_month" value="{{ $user->birth_month }}" min="1" max="12">
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label text-secondary small">السنة</label>
                                            <input type="number" class="form-control auth-input" name="birth_year" value="{{ $user->birth_year }}" min="1900">
                                        </div>
                                    </div>
                                </div>

                                {{-- القسم الثاني: الإعدادات والمالية --}}
                                <div class="col-lg-6">
                                    <h6 class="text-warning mb-3">الإعدادات والمالية</h6>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label text-secondary small">الدور (Role)</label>
                                            <select name="role" class="form-select auth-input bg-dark text-light border-secondary">
                                                {{-- افترضنا وجود متغير Roles أو يمكن استخدام قيم ثابتة --}}
                                                @foreach($roles ?? [] as $role)
                                                    <option value="{{ $role->key ?? $role->name }}" {{ $user->role == ($role->key ?? $role->name) ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                                {{-- خيارات احتياطية في حال لم يتم تمرير الأدوار --}}
                                                @if(!isset($roles))
                                                    <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>مستخدم (User)</option>
                                                    <option value="admin" {{ strtolower($user->role) == 'admin' ? 'selected' : '' }}>مدير (Admin)</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-secondary small">النقاط</label>
                                            <input type="number" class="form-control auth-input" name="points" value="{{ $user->points ?? 0 }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-secondary small">الرصيد</label>
                                            <input type="number" step="0.01" class="form-control auth-input" name="balance" value="{{ $user->balance ?? 0 }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer border-top border-secondary-subtle">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" form="editForm{{ $user->id }}" class="btn btn-primary px-4" id="submitBtn{{ $user->id }}">حفظ التغييرات</button>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
    @endforeach
</section>

<style>
    /* CSS مخصص لإصلاح المظهر */
    
    /* جعل الحقول واضحة في الوضع الليلي */
    .auth-input {
        background-color: rgba(0, 0, 0, 0.3) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        color: #fff !important;
        padding: 0.6rem 0.8rem;
    }
    
    .auth-input:focus {
        background-color: rgba(0, 0, 0, 0.5) !important;
        border-color: var(--bs-primary) !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }
    
    /* تحسين شكل المودال الزجاجي */
    .glass {
        background: rgba(33, 37, 41, 0.95); /* لون داكن شبه شفاف */
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }
    
    /* تحسين شريط التمرير داخل المودال */
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    /* حجم الأزرار الصغيرة */
    .btn-sm {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // معالجة إرسال نموذج التعديل
    document.querySelectorAll('form[id^="editForm"]').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            // التأكد من أن جميع الحقول المطلوبة مملوءة
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            let firstError = null;
            
            requiredFields.forEach(function(field) {
                // إزالة أي رسائل خطأ سابقة
                field.classList.remove('is-invalid');
                const errorDiv = field.parentElement.querySelector('.invalid-feedback');
                if (errorDiv) {
                    errorDiv.remove();
                }
                
                // التحقق من الحقل
                if (!field.value || !field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                    
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.textContent = 'هذا الحقل مطلوب';
                    field.parentElement.appendChild(errorDiv);
                    
                    if (!firstError) {
                        firstError = field;
                    }
                }
            });
            
            // التحقق من صحة البريد الإلكتروني
            const emailField = form.querySelector('input[type="email"]');
            if (emailField && emailField.value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailField.value)) {
                    isValid = false;
                    emailField.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.textContent = 'البريد الإلكتروني غير صحيح';
                    emailField.parentElement.appendChild(errorDiv);
                    if (!firstError) {
                        firstError = emailField;
                    }
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                e.stopPropagation();
                
                // التمرير إلى أول حقل به خطأ
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
                
                return false;
            }
            
            // إظهار رسالة تحميل
            const submitBtn = document.querySelector('[form="' + form.id + '"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>جاري الحفظ...';
            }
            
            // السماح بإرسال الـ form
            return true;
        });
    });
    
    // إعادة تفعيل الأزرار بعد إغلاق الـ modal
    document.querySelectorAll('.modal').forEach(function(modal) {
        modal.addEventListener('hidden.bs.modal', function() {
            const forms = this.querySelectorAll('form');
            forms.forEach(function(form) {
                const submitBtn = document.querySelector('[form="' + form.id + '"]');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'حفظ التغييرات';
                }
            });
        });
    });
});
</script>