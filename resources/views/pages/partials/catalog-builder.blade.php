<section class="container py-4 text-light">
    <div class="glass p-4">
        <h1 class="h4 fw-bold mb-3">إدراج التصنيفات والمنتجات</h1>
        @if (session('status'))
            <div class="alert alert-success small d-flex align-items-center justify-content-between" 
                 style="background: linear-gradient(135deg, rgba(14, 255, 255, 0.15), rgba(10, 187, 187, 0.15)); 
                        border: 2px solid #0ef; 
                        border-radius: 12px; 
                        box-shadow: 0 5px 20px rgba(14, 255, 255, 0.3);
                        animation: slideInRight 0.5s ease-out;">
                <div class="d-flex align-items-center gap-3">
                    <span style="font-size: 24px;">✅</span>
                    <span style="color: #0ef; font-weight: bold;">{{ session('status') }}</span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger small" 
                 style="background: linear-gradient(135deg, rgba(255, 68, 68, 0.15), rgba(204, 0, 0, 0.15)); 
                        border: 2px solid #ff4444; 
                        border-radius: 12px; 
                        box-shadow: 0 5px 20px rgba(255, 68, 68, 0.3);
                        animation: slideInRight 0.5s ease-out;">
                <div class="d-flex align-items-start gap-3">
                    <span style="font-size: 24px;">⚠️</span>
                    <div class="flex-grow-1">
                        <strong style="color: #ff9999; font-size: 16px; display: block; margin-bottom: 10px;">حدثت أخطاء:</strong>
                        <ul class="mb-0" style="padding-right: 20px;">
                            @foreach ($errors->all() as $error)
                                <li style="color: #ffcccc; margin-bottom: 5px;">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            <style>
                @keyframes slideInRight {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                @keyframes fadeOut {
                    from {
                        opacity: 1;
                    }
                    to {
                        opacity: 0;
                    }
                }
            </style>
        @endif

        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="insert-tab" data-bs-toggle="tab" data-bs-target="#insert-pane" type="button" role="tab">
                    إدراج التصنيفات
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="manage-tab" data-bs-toggle="tab" data-bs-target="#manage-pane" type="button" role="tab">
                    إدارة التصنيفات
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="relations-tab" data-bs-toggle="tab" data-bs-target="#relations-pane" type="button" role="tab">
                    العلاقات
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="add-product-tab" data-bs-toggle="tab" data-bs-target="#add-product-pane" type="button" role="tab">
                    إضافة منتج
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="products-tab" data-bs-toggle="tab" data-bs-target="#products-pane" type="button" role="tab">
                    إدارة المنتجات
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="insert-pane" role="tabpanel" aria-labelledby="insert-tab">
                <div class="mb-3">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <span class="fw-bold text-light">الأصناف:</span>
                        <div class="btn-group flex-wrap">
                            @forelse ($categories as $cat)
                                <button type="button" class="btn btn-sm btn-outline-main cat-btn" data-cat="{{ $cat->id }}">
                                    {{ $cat->translated_name }}
                                </button>
                            @empty
                                <span class="text-secondary small">لا توجد أصناف بعد.</span>
                            @endforelse
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 align-items-center mt-2">
                        <span class="fw-bold text-light">الأنواع:</span>
                        <div class="btn-group flex-wrap" id="typeButtons"></div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 align-items-center mt-2">
                        <span class="fw-bold text-light">الشركات:</span>
                        <div class="btn-group flex-wrap">
                            @forelse ($companies as $company)
                                <button type="button" class="btn btn-sm btn-outline-main">
                                    {{ $company->name }}
                                </button>
                            @empty
                                <span class="text-secondary small">لا توجد شركات بعد.</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-lg-3">
                        <div class="glass p-3 h-100">
                            <h6 class="fw-bold">إضافة صنف رئيسي</h6>
                            <form method="POST" action="{{ route('admin.catalog.category') }}" class="d-flex flex-column gap-2" enctype="multipart/form-data">
                                @csrf
                                <input type="text" name="name" class="form-control auth-input" placeholder="اسم الصنف (Arabic)" required>
                                <input type="text" name="name_en" class="form-control auth-input" placeholder="Category Name (English)">
                                <textarea name="description" class="form-control auth-input" rows="2" placeholder="وصف بالعربية (اختياري)"></textarea>
                                <textarea name="description_en" class="form-control auth-input" rows="2" placeholder="Description in English (optional)"></textarea>
                                <label class="form-label small text-secondary mb-0">صورة الصنف (اختياري)</label>
                                <input type="file" name="image" class="form-control auth-input">
                                <button class="btn btn-main btn-sm">حفظ الصنف</button>
                            </form>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="glass p-3 h-100">
                            <h6 class="fw-bold">إضافة نوع داخل صنف</h6>
                            <form method="POST" action="{{ route('admin.catalog.type') }}" class="d-flex flex-column gap-2" enctype="multipart/form-data">
                                @csrf
                                <select name="category_id" class="form-select auth-input bg-dark text-light" required>
                                    <option value="">اختر الصنف</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->translated_name }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="name" class="form-control auth-input" placeholder="اسم النوع (Arabic)" required>
                                <input type="text" name="name_en" class="form-control auth-input" placeholder="Type Name (English)">
                                <label class="form-label small text-secondary mb-0">صورة النوع (اختياري)</label>
                                <input type="file" name="image" class="form-control auth-input">
                                <button class="btn btn-main btn-sm">حفظ النوع</button>
                            </form>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="glass p-3 h-100">
                            <h6 class="fw-bold">إضافة شركة</h6>
                            <form method="POST" action="{{ route('admin.catalog.company') }}" class="d-flex flex-column gap-2" enctype="multipart/form-data">
                                @csrf
                                <input type="text" name="name" class="form-control auth-input" placeholder="اسم الشركة" required>
                                <label class="form-label small text-secondary mb-0">صورة الشركة (اختياري)</label>
                                <input type="file" name="image" class="form-control auth-input">
                                <label class="form-label small text-secondary mb-0">خلفية الشركة (اختياري)</label>
                                <input type="file" name="background" class="form-control auth-input">
                                <textarea name="description" class="form-control auth-input" rows="2" placeholder="وصف بالعربية (اختياري)"></textarea>
                                <textarea name="description_en" class="form-control auth-input" rows="2" placeholder="Description in English (optional)"></textarea>
                                <button class="btn btn-main btn-sm">حفظ الشركة</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- تبويب مستقل لإضافة منتج بنفس النموذج --}}
            <div class="tab-pane fade" id="add-product-pane" role="tabpanel" aria-labelledby="add-product-tab">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6">
                        <div class="glass p-3 h-100">
                            <h6 class="fw-bold mb-2">إضافة منتج جديد</h6>
                            <p class="text-secondary small mb-3">اختر الصنف والنوع للشجرة الصحيحة، ثم أدخل الأسعار والنقاط.</p>
                            <form method="POST" action="{{ route('admin.catalog.product') }}" class="d-flex flex-column gap-2" enctype="multipart/form-data">
                                @csrf
                                {{-- نفس منطق الفلترة القديم: يعتمد على categorySelect / typeSelect مع data-category --}}
                                <select name="category_id" id="categorySelect" class="form-select auth-input bg-dark text-light" required>
                                    <option value="">اختر الصنف</option>
                                    @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->translated_name }}</option>
                                    @endforeach
                                </select>
                                <select name="type_id" id="typeSelect" class="form-select auth-input bg-dark text-light" required>
                                    <option value="">اختر النوع</option>
                                    @foreach ($types as $type)
                                    <option value="{{ $type->id }}" data-category="{{ $type->category_id }}">{{ $type->translated_name }}</option>
                                    @endforeach
                                </select>
                                <select name="company_id" class="form-select auth-input bg-dark text-light" required>
                                    <option value="">اختر الشركة</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>

                                {{-- اسم المنتج بالعربية والإنجليزية --}}
                                <input type="text" name="name" class="form-control auth-input" placeholder="اسم المنتج (Arabic)" required>
                                <input type="text" name="name_en" class="form-control auth-input" placeholder="Product Name (English)">

                                <input type="number" step="0.01" name="cost_price" class="form-control auth-input" placeholder="سعر التكلفة (اختياري)">
                                <input type="number" step="0.01" name="price" class="form-control auth-input" placeholder="سعر البيع الافتراضي" required>

                                <input type="number" name="stock" class="form-control auth-input" placeholder="المخزون" required>

                                <input type="number" name="points_reward" class="form-control auth-input" placeholder="عدد النقاط عند الشراء (اختياري)" min="0">

                                @php($roles = $roles ?? collect())
                                @if($roles->count() > 0)
                                    <label class="form-label small text-secondary mb-0 mt-1">أسعار خاصة حسب الدور (اختياري)</label>
                                    @foreach($roles as $role)
                                        @if(strtolower($role->key) !== 'admin')
                                            <div class="input-group input-group-sm mb-1">
                                                <span class="input-group-text bg-dark text-secondary">{{ $role->name }}</span>
                                                <input type="number"
                                                       step="0.01"
                                                       name="role_prices[{{ $role->key }}]"
                                                       class="form-control auth-input"
                                                       placeholder="سعر خاص لدور {{ $role->name }}">
                                            </div>
                                        @endif
                                    @endforeach
                                @endif

                                <textarea name="description" class="form-control auth-input" rows="2" placeholder="وصف بالعربية (اختياري)"></textarea>
                                <textarea name="description_en" class="form-control auth-input" rows="2" placeholder="Description in English (optional)"></textarea>
                                <label class="form-label small text-secondary mb-0">صورة المنتج (اختياري)</label>
                                <input type="file" name="image" class="form-control auth-input">
                                <button class="btn btn-main btn-sm mt-2">حفظ المنتج</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="manage-pane" role="tabpanel" aria-labelledby="manage-tab">
                <div class="row g-3">
                    {{-- عمود الأصناف الرئيسية --}}
                    <div class="col-lg-4">
                        <div class="glass p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">إدارة الأصناف الرئيسية</h6>
                                <a href="{{ route('admin.catalog.categories') }}" class="btn btn-sm btn-outline-main">فتح صفحة جميع الأصناف</a>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small text-secondary mb-1">بحث عن صنف رئيسي</label>
                                <input type="text" id="manage-category-search" class="form-control form-control-sm bg-dark text-light" placeholder="اكتب اسم الصنف للبحث">
                            </div>
                            <div id="manage-categories-list">
                                @forelse ($categories as $index => $cat)
                                    <div class="border rounded-3 p-3 mb-3 border-secondary-subtle manage-category-item" data-name="{{ $cat->name }}">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong class="text-success">{{ $cat->name }}</strong>
                                            <button type="button" class="btn btn-sm btn-outline-main manage-cat-btn" data-manage-cat="{{ $cat->id }}">
                                                عرض الأنواع
                                            </button>
                                        </div>
                                        <form method="POST" action="{{ route('admin.catalog.category.update', $cat) }}" class="row g-2 align-items-end" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="col-12">
                                                <label class="form-label small text-secondary mb-0">اسم الصنف</label>
                                                <input type="text" name="name" value="{{ $cat->name }}" class="form-control form-control-sm bg-dark text-light">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small text-secondary mb-0">وصف (اختياري)</label>
                                                <input type="text" name="description" value="{{ $cat->description }}" class="form-control form-control-sm bg-dark text-light">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small text-secondary mb-0">صورة (اختياري)</label>
                                                <input type="file" name="image" class="form-control form-control-sm bg-dark text-light category-image-input" data-cat-id="{{ $cat->id }}">
                                                <div class="text-secondary small mt-1 category-image-path" data-cat-id="{{ $cat->id }}" style="min-height: 20px;">
                                                    @if($cat->image)
                                                        <span class="text-success">{{ $cat->image }}</span>
                                                    @else
                                                        <span class="text-muted">لم يتم اختيار أي ملف</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-12 d-flex gap-2 mt-2">
                                                <button class="btn btn-sm btn-main" type="submit">تعديل الصنف</button>
                                            </div>
                                        </form>
                                        <form method="POST" action="{{ route('admin.catalog.category.delete', $cat) }}" onsubmit="return confirm('حذف الصنف؟ سيحذف الأنواع والمنتجات التابعة.');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger mt-1">حذف الصنف</button>
                                        </form>
                                    </div>
                                @empty
                                    <div class="text-secondary small">لا توجد أصناف لإدارتها.</div>
                                @endforelse
                            </div>
                            @if($categories->count() > 2)
                            <div class="text-center mt-3 show-all-categories-btn d-none">
                                <a href="{{ route('admin.catalog.categories') }}" class="btn btn-sm btn-outline-main d-inline-flex align-items-center gap-2">
                                    <i class="bi bi-chevron-down"></i>
                                    <span>عرض جميع الأصناف ({{ $categories->count() }})</span>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- عمود الأنواع --}}
                    <div class="col-lg-4">
                        <div class="glass p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">إدارة الأنواع</h6>
                                <a href="{{ route('admin.catalog.types') }}" class="btn btn-sm btn-outline-main">فتح صفحة جميع الأنواع</a>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small text-secondary mb-1">بحث عن نوع</label>
                                <input type="text" id="manage-type-search" class="form-control form-control-sm bg-dark text-light" placeholder="اكتب اسم النوع للبحث (ضمن الصنف المحدد)">
                            </div>
                            <div id="manage-types-placeholder" class="text-secondary small mb-2">
                                اختر صنفاً من العمود الأيمن لعرض أنواعه.
                            </div>
                            @foreach ($categories as $cat)
                                <div class="types-group d-none" data-manage-cat="{{ $cat->id }}">
                                    @if ($cat->types->count())
                                        @foreach ($cat->types as $type)
                                            <div class="border rounded-3 p-2 mb-2 border-secondary-subtle manage-type-item" data-name="{{ $type->name }}">
                                                <form method="POST" action="{{ route('admin.catalog.type.update', $type) }}" class="row g-2 align-items-end" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="col-6">
                                                        <label class="form-label small text-secondary mb-0">اسم النوع</label>
                                                        <input type="text" name="name" value="{{ $type->name }}" class="form-control form-control-sm bg-dark text-light">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small text-secondary mb-0">صورة (اختياري)</label>
                                                        <input type="file" name="image" class="form-control form-control-sm bg-dark text-light">
                                                    </div>
                                                    <div class="col-12 d-flex gap-2 mt-1">
                                                        <button class="btn btn-sm btn-main" type="submit">تعديل النوع</button>
                                                    </div>
                                                </form>
                                                <form method="POST" action="{{ route('admin.catalog.type.delete', $type) }}" onsubmit="return confirm('حذف النوع؟ سيحذف المنتجات التابعة.');" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger">حذف النوع</button>
                                                </form>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-secondary small">لا توجد أنواع لهذا الصنف.</div>
                                    @endif
                                </div>
                            @endforeach
                            <button id="manage-types-toggle" class="btn btn-sm btn-outline-main w-100 mt-2 d-none">
                                عرض جميع الأنواع
                            </button>
                        </div>
                    </div>

                    {{-- عمود الشركات --}}
                    <div class="col-lg-4">
                        <div class="glass p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">إدارة الشركات</h6>
                                <a href="{{ route('admin.catalog.companies') }}" class="btn btn-sm btn-outline-main">فتح صفحة جميع الشركات</a>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small text-secondary mb-1">بحث عن شركة</label>
                                <input type="text" id="manage-company-search" class="form-control form-control-sm bg-dark text-light" placeholder="اكتب اسم الشركة للبحث">
                            </div>
                            @forelse ($companies as $company)
                                <div class="border rounded-3 p-3 mb-2 border-secondary-subtle manage-company-item" data-name="{{ $company->name }}">
                                    <form method="POST" action="{{ route('admin.catalog.company.update', $company) }}" class="row g-2 align-items-end" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="col-md-6">
                                            <label class="form-label small text-secondary mb-0">اسم الشركة</label>
                                            <input type="text" name="name" value="{{ $company->name }}" class="form-control form-control-sm bg-dark text-light">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-secondary mb-0">صورة (اختياري)</label>
                                            <input type="file" name="image" class="form-control form-control-sm bg-dark text-light">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-secondary mb-0">خلفية (اختياري)</label>
                                            <input type="file" name="background" class="form-control form-control-sm bg-dark text-light">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small text-secondary mb-0">وصف بالعربية (اختياري)</label>
                                            <textarea name="description" class="form-control form-control-sm bg-dark text-light" rows="2">{{ $company->description }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small text-secondary mb-0">Description in English (optional)</label>
                                            <textarea name="description_en" class="form-control form-control-sm bg-dark text-light" rows="2">{{ $company->description_en }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-sm btn-main" type="submit">تعديل الشركة</button>
                                        </div>
                                    </form>
                                    <form method="POST" action="{{ route('admin.catalog.company.delete', $company) }}" onsubmit="return confirm('حذف الشركة؟ سيحذف المنتجات المرتبطة.');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger mt-1">حذف الشركة</button>
                                    </form>
                                </div>
                            @empty
                                <div class="text-secondary small">لا توجد شركات لإدارتها.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- تبويب العلاقات --}}
            <div class="tab-pane fade" id="relations-pane" role="tabpanel" aria-labelledby="relations-tab">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="glass p-3 h-100">
                            <h6 class="small fw-bold mb-3 text-success">ربط شركات بصنف رئيسي</h6>
                            <form method="POST" action="{{ route('admin.catalog.category.companies') }}" class="row g-2">
                                @csrf
                                <div class="col-12">
                                    <label class="form-label small text-secondary mb-0">اختر الصنف الرئيسي</label>
                                    <select id="relations-category-id" name="category_id" class="form-select form-select-sm bg-dark text-light" required>
                                        <option value="">اختر الصنف</option>
                                        @foreach ($categories as $category)
                                            <option
                                                value="{{ $category->id }}"
                                                data-companies="{{ $category->companies->pluck('id')->implode(',') }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}
                                            >
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small text-secondary mb-1">الشركات المرتبطة</label>
                                    <input type="text" id="search-category-companies" class="form-control form-control-sm bg-dark text-light mb-2" placeholder="بحث عن شركة...">
                                    <div class="bg-dark p-2 rounded" style="max-height:220px; overflow:auto;" id="category-companies-checkboxes">
                                        @foreach ($companies as $company)
                                            <div class="form-check text-light company-relation-item" data-name="{{ strtolower($company->name) }}">
                                                <input class="form-check-input" type="checkbox" name="companies[]" value="{{ $company->id }}" id="cat-comp-{{ $company->id }}">
                                                <label class="form-check-label small" for="cat-comp-{{ $company->id }}">{{ $company->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-12 d-flex gap-2">
                                    <button class="btn btn-sm btn-main w-100">حفظ العلاقة</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="glass p-3 h-100">
                            <h6 class="small fw-bold mb-3 text-success">ربط أصناف بشركة</h6>
                            <form method="POST" action="{{ route('admin.catalog.company.categories') }}" class="row g-2">
                                @csrf
                                <div class="col-12">
                                    <label class="form-label small text-secondary mb-0">اختر الشركة</label>
                                    <select id="relations-company-id" name="company_id" class="form-select form-select-sm bg-dark text-light" required>
                                        <option value="">اختر الشركة</option>
                                        @foreach ($companies as $company)
                                            <option
                                                value="{{ $company->id }}"
                                                data-categories="{{ $company->categories->pluck('id')->implode(',') }}"
                                                {{ old('company_id') == $company->id ? 'selected' : '' }}
                                            >
                                                {{ $company->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small text-secondary mb-1">الأصناف المرتبطة</label>
                                    <input type="text" id="search-company-categories" class="form-control form-control-sm bg-dark text-light mb-2" placeholder="بحث عن صنف...">
                                    <div class="bg-dark p-2 rounded" style="max-height:220px; overflow:auto;" id="company-categories-checkboxes">
                                        @foreach ($categories as $category)
                                            <div class="form-check text-light category-relation-item" data-name="{{ strtolower($category->name) }}">
                                                <input class="form-check-input" type="checkbox" name="categories[]" value="{{ $category->id }}" id="comp-cat-{{ $category->id }}">
                                                <label class="form-check-label small" for="comp-cat-{{ $category->id }}">{{ $category->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-12 d-flex gap-2">
                                    <button class="btn btn-sm btn-main w-100">حفظ العلاقة</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="products-pane" role="tabpanel" aria-labelledby="products-tab">
                <h6 class="fw-bold mb-3">إدارة المنتجات (آخر 20 منتج)</h6>
                <div class="table-responsive">
                    <table class="table table-dark table-sm align-middle">
                        <thead>
                            <tr>
                                <th>المنتج</th>
                                <th>الصنف</th>
                                <th>النوع</th>
                                <th>الشركة</th>
                                <th>سعر التكلفة</th>
                                <th>سعر البيع</th>
                                <th>صافي الربح</th>
                                <th>المخزون</th>
                                <th>الحالة</th>
                                <th class="text-center">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $product->translated_name }}</td>
                                    <td>{{ $product->category->name ?? '-' }}</td>
                                    <td>{{ $product->type->name ?? '-' }}</td>
                                    <td>{{ $product->company->name ?? '-' }}</td>
                                    <td>{{ $product->cost_price }}</td>
                                    <td>{{ $product->price }}</td>
                                    <td>{{ $product->price - ($product->cost_price ?? 0) }}</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>{{ ($product->is_active ?? true) ? 'مفعّل' : 'مخفي' }}</td>
                                    <td class="text-center">
                                        <form
                                            method="POST"
                                            action="{{ route('admin.catalog.product.quickUpdate', $product) }}"
                                            class="d-inline"
                                        >
                                            @csrf
                                            {{-- نرسل القيم الحالية كما هي مع عكس حالة التفعيل --}}
                                            <input type="hidden" name="cost_price" value="{{ $product->cost_price }}">
                                            <input type="hidden" name="price" value="{{ $product->price }}">
                                            <input type="hidden" name="stock" value="{{ $product->stock }}">
                                            <input type="hidden" name="is_active" value="{{ ($product->is_active ?? true) ? 0 : 1 }}">
                                            <button class="btn btn-sm {{ ($product->is_active ?? true) ? 'btn-warning' : 'btn-success' }}">
                                                {{ ($product->is_active ?? true) ? 'إخفاء المنتج' : 'إظهار المنتج' }}
                                            </button>
                                        </form>
                                        <button
                                            class="btn btn-sm btn-outline-main"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#product-details-{{ $product->id }}"
                                        >
                                            تفاصيل / تعديل
                                        </button>
                                        <form
                                            method="POST"
                                            action="{{ route('admin.catalog.product.delete', $product) }}"
                                            class="d-inline product-delete-form"
                                            onsubmit="return confirmProductDelete(event, '{{ $product->translated_name }}', {{ $product->id }});"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">حذف</button>
                                        </form>
                                    </td>
                                </tr>
                                <tr class="collapse bg-dark" id="product-details-{{ $product->id }}">
                                    <td colspan="10">
                                        <div class="p-3">
                                            <h6 class="fw-bold mb-3">تفاصيل وتعديل المنتج</h6>
                                            <div class="row g-3 mb-2">
                                                <div class="col-md-4">
                                                    <label class="form-label small text-secondary mb-0">عدد وحدات المبيع (sales_count)</label>
                                                    <input type="text" class="form-control form-control-sm bg-dark text-light" value="{{ $product->sales_count }}" readonly>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small text-secondary mb-0">نقاط المكافأة</label>
                                                    <input type="text" class="form-control form-control-sm bg-dark text-light" value="{{ $product->points_reward }}" readonly>
                                                </div>
                                            </div>

                                            <h6 class="fw-bold mb-2">تعديل أساسي</h6>
                                            <form method="POST" action="{{ route('admin.catalog.product.update', $product) }}" class="row g-2 align-items-end" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="col-md-3">
                                                    <label class="form-label small text-secondary mb-0">اسم المنتج (Arabic)</label>
                                                    <input type="text" name="name" value="{{ $product->name }}" class="form-control form-control-sm bg-dark text-light">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small text-secondary mb-0">Product Name (English)</label>
                                                    <input type="text" name="name_en" value="{{ $product->name_en }}" class="form-control form-control-sm bg-dark text-light">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small text-secondary mb-0">سعر التكلفة</label>
                                                    <input type="number" step="0.01" name="cost_price" value="{{ $product->cost_price }}" class="form-control form-control-sm bg-dark text-light">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small text-secondary mb-0">سعر البيع</label>
                                                    <input type="number" step="0.01" name="price" value="{{ $product->price }}" class="form-control form-control-sm bg-dark text-light">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small text-secondary mb-0">المخزون</label>
                                                    <input type="number" name="stock" value="{{ $product->stock }}" class="form-control form-control-sm bg-dark text-light">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small text-secondary mb-0">وصف بالعربية (اختياري)</label>
                                                    <input type="text" name="description" value="{{ $product->description }}" class="form-control form-control-sm bg-dark text-light">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small text-secondary mb-0">Description in English (optional)</label>
                                                    <input type="text" name="description_en" value="{{ $product->description_en }}" class="form-control form-control-sm bg-dark text-light">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small text-secondary mb-0">صورة (اختياري)</label>
                                                    <input type="file" name="image" class="form-control form-control-sm bg-dark text-light">
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-check mt-4">
                                                        <input class="form-check-input" type="checkbox" name="is_best_seller" value="1" id="bestSeller-{{ $product->id }}" {{ $product->is_best_seller ? 'checked' : '' }}>
                                                        <label class="form-check-label small text-secondary" for="bestSeller-{{ $product->id }}">
                                                            ضمن المنتجات الأكثر مبيعاً
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-12 d-flex gap-2 mt-1">
                                                    <button class="btn btn-sm btn-main">حفظ التعديلات</button>
                                                    <button class="btn btn-sm btn-outline-main" type="button" data-bs-toggle="collapse" data-bs-target="#product-details-{{ $product->id }}">إغلاق</button>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
    const categorySelect = document.getElementById('categorySelect');
    const typeSelect = document.getElementById('typeSelect');
    const allTypes = Array.from(typeSelect.options);
    const catButtons = document.querySelectorAll('.cat-btn');
    const typeButtonsContainer = document.getElementById('typeButtons');
    const manageCatButtons = document.querySelectorAll('.manage-cat-btn');
    const typesGroups = document.querySelectorAll('.types-group');
    const typesPlaceholder = document.getElementById('manage-types-placeholder');
    const manageCategorySearch = document.getElementById('manage-category-search');
    const manageCategoryItems = document.querySelectorAll('.manage-category-item');
    const manageTypeSearch = document.getElementById('manage-type-search');
    const manageCompanySearch = document.getElementById('manage-company-search');
    const manageCompanyItems = document.querySelectorAll('.manage-company-item');
    const manageTypesToggle = document.getElementById('manage-types-toggle');
    const typesPageBaseUrl = "{{ route('admin.catalog.types') }}";
    const showAllCategoriesBtn = document.querySelector('.show-all-categories-btn');
    let showAllCategories = false;
    function filterTypes() {
        const catId = categorySelect.value;
        typeSelect.innerHTML = '';
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = 'اختر النوع';
        typeSelect.appendChild(placeholder);
        allTypes.forEach(opt => {
            if (!opt.value) return;
            if (!catId || opt.dataset.category === catId) {
                typeSelect.appendChild(opt.cloneNode(true));
            }
        });
    }

    categorySelect?.addEventListener('change', filterTypes);

    function renderTypeButtons(catId) {
        typeButtonsContainer.innerHTML = '';
        allTypes.forEach(opt => {
            if (!opt.value) return;
            if (!catId || opt.dataset.category === catId) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-sm btn-outline-main';
                btn.textContent = opt.textContent;
                btn.onclick = () => {
                    categorySelect.value = catId;
                    filterTypes();
                    typeSelect.value = opt.value;
                };
                typeButtonsContainer.appendChild(btn);
            }
        });
        if (!typeButtonsContainer.childElementCount) {
            const span = document.createElement('span');
            span.className = 'text-secondary small';
            span.textContent = 'لا توجد أنواع';
            typeButtonsContainer.appendChild(span);
        }
    }

    catButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const catId = btn.dataset.cat;
            categorySelect.value = catId;
            filterTypes();
            renderTypeButtons(catId);
        });
    });

    // إدارة الأنواع في تبويب "إدارة التصنيفات"
    function showManageTypes(catId) {
        let anyShown = false;
        typesGroups.forEach(group => {
            if (group.dataset.manageCat === catId) {
                group.classList.remove('d-none');
                anyShown = true;
            } else {
                group.classList.add('d-none');
            }
        });
        if (typesPlaceholder) {
            typesPlaceholder.classList.toggle('d-none', anyShown);
        }
        // عند تغيير الصنف: إظهار أول 5 أنواع فقط لهذا الصنف
        if (manageTypeSearch) {
            manageTypeSearch.value = '';
        }
        limitTypesToFive();
    }

    manageCatButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const catId = btn.dataset.manageCat;
            showManageTypes(catId);
        });
    });

    // بحث وإظهار/إخفاء أصناف في تبويب "إدارة التصنيفات"
    function applyCategoryFilter() {
        const term = (manageCategorySearch?.value || '').trim().toLowerCase();
        let visibleCount = 0;
        let hiddenCount = 0;
        const totalItems = manageCategoryItems.length;

        manageCategoryItems.forEach((item, index) => {
            const name = (item.dataset.name || '').toLowerCase();
            const matches = !term || name.includes(term);

            if (!matches) {
                item.classList.add('d-none');
                return;
            }

            if (!term && !showAllCategories && visibleCount >= 2) {
                // بدون بحث وبدون "عرض الكل" نُظهر أول اثنين فقط
                item.classList.add('d-none');
                hiddenCount++;
            } else {
                item.classList.remove('d-none');
                visibleCount++;
            }
        });

        // إظهار/إخفاء زر السهم بناءً على وجود أصناف مخفية
        if (showAllCategoriesBtn) {
            // إظهار الزر إذا كان هناك أكثر من عنصرين وليس هناك بحث نشط
            if (totalItems > 2 && hiddenCount > 0 && !term) {
                showAllCategoriesBtn.classList.remove('d-none');
            } else {
                showAllCategoriesBtn.classList.add('d-none');
            }
        }
    }

    manageCategorySearch?.addEventListener('input', applyCategoryFilter);

    // تطبيق أولي: إظهار أول صنفين فقط (وإظهار كل النتائج عند البحث)
    // تأكد من أن العناصر موجودة قبل التطبيق
    if (manageCategoryItems.length > 0) {
        applyCategoryFilter();
    }

    // إظهار أول 5 أنواع فقط في المجموعة الحالية
    function limitTypesToFive() {
        const activeGroup = document.querySelector('.types-group:not(.d-none)');
        if (!activeGroup) return;

        const items = Array.from(activeGroup.querySelectorAll('.manage-type-item'));
        items.forEach((item, index) => {
            item.classList.toggle('d-none', index >= 5);
        });

        if (manageTypesToggle) {
            // نظهر الزر فقط إذا كان هناك أكثر من 5 أنواع
            manageTypesToggle.classList.toggle('d-none', items.length <= 5);
            manageTypesToggle.textContent = 'عرض جميع الأنواع';
        }
    }

    // بحث في الأنواع داخل المجموعة الظاهرة فقط (بدون حد 5 أثناء البحث)
    function applyTypeFilter() {
        const term = (manageTypeSearch?.value || '').trim().toLowerCase();
        const activeGroup = document.querySelector('.types-group:not(.d-none)');
        if (!activeGroup) return;

        const items = activeGroup.querySelectorAll('.manage-type-item');
        items.forEach(item => {
            const name = (item.dataset.name || '').toLowerCase();
            const matches = !term || name.includes(term);
            item.classList.toggle('d-none', !matches);
        });

        // أثناء البحث نخفي زر "عرض جميع الأنواع"
        if (manageTypesToggle) {
            manageTypesToggle.classList.toggle('d-none', !!term);
        }
    }

    manageTypeSearch?.addEventListener('input', applyTypeFilter);

    // عند الضغط على "عرض جميع الأنواع" ننتقل لصفحة جميع الأنواع مع تصفية على الصنف الحالي
    manageTypesToggle?.addEventListener('click', () => {
        const activeGroup = document.querySelector('.types-group:not(.d-none)');
        if (!activeGroup) return;
        const catId = activeGroup.dataset.manageCat;
        if (!catId) return;
        window.location.href = typesPageBaseUrl + '?category_id=' + encodeURIComponent(catId);
    });

    // بحث في الشركات
    function applyCompanyFilter() {
        const term = (manageCompanySearch?.value || '').trim().toLowerCase();
        manageCompanyItems.forEach(item => {
            const name = (item.dataset.name || '').toLowerCase();
            const matches = !term || name.includes(term);
            item.classList.toggle('d-none', !matches);
        });
    }

    manageCompanySearch?.addEventListener('input', applyCompanyFilter);

    // === ربط الشركات بالصنف الرئيسي: تحديث الـ checkbox مباشرة حسب البيانات الموجودة في الـ option ===
    const catRelSelect = document.getElementById('relations-category-id');
    const companyRelSelect = document.getElementById('relations-company-id');
    const catCompaniesBox = document.getElementById('category-companies-checkboxes');
    const companyCatsBox = document.getElementById('company-categories-checkboxes');

    if (catRelSelect && catCompaniesBox) {
        const companyCheckboxes = Array.from(catCompaniesBox.querySelectorAll('input[name="companies[]"]'));

        function updateCompanyCheckboxes() {
            const opt = catRelSelect.options[catRelSelect.selectedIndex];
            const ids = (opt && opt.dataset.companies ? opt.dataset.companies.split(',') : []).filter(Boolean);
            companyCheckboxes.forEach(cb => {
                cb.checked = ids.includes(cb.value);
            });
        }

        catRelSelect.addEventListener('change', updateCompanyCheckboxes);
        // إذا لم يكن هناك اختيار، جرّب اختيار أول عنصر لديه شركات مرتبطة
        if (!catRelSelect.value) {
            const firstWithCompanies = Array.from(catRelSelect.options).find(o => o.dataset.companies);
            if (firstWithCompanies && firstWithCompanies.value) {
                catRelSelect.value = firstWithCompanies.value;
            }
        }
        updateCompanyCheckboxes();
    }

    if (companyRelSelect && companyCatsBox) {
        const categoryCheckboxes = Array.from(companyCatsBox.querySelectorAll('input[name="categories[]"]'));

        function updateCategoryCheckboxes() {
            const opt = companyRelSelect.options[companyRelSelect.selectedIndex];
            const ids = (opt && opt.dataset.categories ? opt.dataset.categories.split(',') : []).filter(Boolean);
            categoryCheckboxes.forEach(cb => {
                cb.checked = ids.includes(cb.value);
            });
        }

        companyRelSelect.addEventListener('change', updateCategoryCheckboxes);
        // إذا لم يكن هناك اختيار، جرّب اختيار أول عنصر لديه أصناف مرتبطة
        if (!companyRelSelect.value) {
            const firstWithCats = Array.from(companyRelSelect.options).find(o => o.dataset.categories);
            if (firstWithCats && firstWithCats.value) {
                companyRelSelect.value = firstWithCats.value;
            }
        }
        updateCategoryCheckboxes();
    }

    // === البحث في العلاقات ===
    // البحث في الشركات المرتبطة بالصنف
    const searchCategoryCompanies = document.getElementById('search-category-companies');
    const companyRelationItems = document.querySelectorAll('.company-relation-item');
    
    if (searchCategoryCompanies) {
        searchCategoryCompanies.addEventListener('input', function() {
            const term = this.value.trim().toLowerCase();
            companyRelationItems.forEach(item => {
                const name = item.dataset.name || '';
                const matches = !term || name.includes(term);
                item.style.display = matches ? 'block' : 'none';
            });
        });
    }

    // البحث في الأصناف المرتبطة بالشركة
    const searchCompanyCategories = document.getElementById('search-company-categories');
    const categoryRelationItems = document.querySelectorAll('.category-relation-item');
    
    if (searchCompanyCategories) {
        searchCompanyCategories.addEventListener('input', function() {
            const term = this.value.trim().toLowerCase();
            categoryRelationItems.forEach(item => {
                const name = item.dataset.name || '';
                const matches = !term || name.includes(term);
                item.style.display = matches ? 'block' : 'none';
            });
        });
    }

    // تتبع إرسال نماذج الحذف
    document.addEventListener('DOMContentLoaded', function() {
        console.log('✅ تم تحميل صفحة إدارة الكتالوج');
        
        // تتبع جميع نماذج حذف المنتجات
        const deleteForms = document.querySelectorAll('.product-delete-form');
        console.log(`📋 عدد نماذج الحذف المتاحة: ${deleteForms.length}`);
        
        deleteForms.forEach((form, index) => {
            const productId = form.action.split('/').pop();
            console.log(`  - نموذج ${index + 1}: المنتج ID=${productId}`);
            
            // إضافة مستمع لحدث submit
            form.addEventListener('submit', function(e) {
                console.log(`🚀 محاولة إرسال نموذج حذف المنتج ID=${productId}`);
                console.log('   الإجراء:', form.action);
                console.log('   الطريقة:', form.method);
                
                // التحقق من وجود حقل _method
                const methodField = form.querySelector('input[name="_method"]');
                if (methodField) {
                    console.log('   _method:', methodField.value);
                } else {
                    console.warn('   ⚠️ حقل _method غير موجود!');
                }
                
                // التحقق من وجود CSRF token
                const csrfField = form.querySelector('input[name="_token"]');
                if (csrfField) {
                    console.log('   ✅ CSRF token موجود');
                } else {
                    console.error('   ❌ CSRF token غير موجود!');
                }
            });
        });
        
        // إزالة مؤشر التحميل بعد تحميل الصفحة (في حال تمت إعادة التوجيه)
        const loadingIndicator = document.getElementById('deleteLoadingIndicator');
        if (loadingIndicator) {
            setTimeout(() => {
                loadingIndicator.style.animation = 'fadeOut 0.5s ease-out';
                setTimeout(() => loadingIndicator.remove(), 500);
            }, 1000);
        }
    });

    // عرض مسار الصورة المحددة في إدارة التصنيفات
    document.querySelectorAll('.category-image-input').forEach(input => {
        input.addEventListener('change', function() {
            const catId = this.dataset.catId;
            const pathDisplay = document.querySelector(`.category-image-path[data-cat-id="${catId}"]`);
            
            if (this.files && this.files.length > 0) {
                const fileName = this.files[0].name;
                if (pathDisplay) {
                    pathDisplay.innerHTML = `<span class="text-success">${fileName}</span>`;
                }
            } else {
                if (pathDisplay) {
                    pathDisplay.innerHTML = '<span class="text-muted">لم يتم اختيار أي ملف</span>';
                }
            }
        });
    });

    // تأكيد حذف المنتج مع نافذة جميلة مخصصة
    function confirmProductDelete(event, productName, productId) {
        event.preventDefault();
        const form = event.target;
        
        // إنشاء نافذة تأكيد مخصصة جميلة
        const modal = document.createElement('div');
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            animation: fadeIn 0.2s ease-in-out;
        `;
        
        modal.innerHTML = `
            <div style="
                background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
                border: 2px solid #0ef;
                border-radius: 20px;
                padding: 30px;
                max-width: 500px;
                width: 90%;
                box-shadow: 0 20px 60px rgba(14, 255, 255, 0.3);
                animation: slideIn 0.3s ease-out;
            ">
                <div style="text-align: center; margin-bottom: 20px;">
                    <div style="
                        width: 80px;
                        height: 80px;
                        margin: 0 auto 15px;
                        background: linear-gradient(135deg, #ff4444, #cc0000);
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        box-shadow: 0 10px 30px rgba(255, 68, 68, 0.4);
                    ">
                        <svg width="40" height="40" fill="white" viewBox="0 0 16 16">
                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                        </svg>
                    </div>
                    <h3 style="color: #fff; font-size: 24px; font-weight: bold; margin-bottom: 10px;">
                        ⚠️ تأكيد الحذف
                    </h3>
                </div>
                
                <div style="
                    background: rgba(14, 255, 255, 0.1);
                    border: 1px solid rgba(14, 255, 255, 0.3);
                    border-radius: 12px;
                    padding: 20px;
                    margin-bottom: 20px;
                ">
                    <p style="color: #fff; font-size: 16px; margin-bottom: 15px; line-height: 1.6;">
                        هل أنت متأكد من حذف المنتج:
                    </p>
                    <p style="
                        color: #0ef;
                        font-size: 18px;
                        font-weight: bold;
                        margin-bottom: 20px;
                        text-align: center;
                        padding: 10px;
                        background: rgba(14, 255, 255, 0.1);
                        border-radius: 8px;
                    ">
                        "${productName}"
                    </p>
                    
                    <div style="color: #ff9999; font-size: 14px; line-height: 1.8;">
                        <p style="margin-bottom: 8px;"><strong>سيتم حذف:</strong></p>
                        <ul style="margin: 0; padding-right: 20px;">
                            <li>المنتج من قاعدة البيانات</li>
                            <li>جميع الصور المرتبطة</li>
                            <li>جميع العلاقات التابعة</li>
                        </ul>
                    </div>
                </div>
                
                <div style="
                    background: rgba(255, 68, 68, 0.1);
                    border: 1px solid rgba(255, 68, 68, 0.3);
                    border-radius: 8px;
                    padding: 12px;
                    margin-bottom: 25px;
                    text-align: center;
                ">
                    <p style="color: #ffcccc; font-size: 13px; margin: 0;">
                        ⚡ هذا الإجراء لا يمكن التراجع عنه!
                    </p>
                </div>
                
                <div style="display: flex; gap: 15px; justify-content: center;">
                    <button id="confirmDeleteBtn" style="
                        background: linear-gradient(135deg, #ff4444, #cc0000);
                        color: white;
                        border: none;
                        padding: 12px 30px;
                        border-radius: 10px;
                        font-size: 16px;
                        font-weight: bold;
                        cursor: pointer;
                        transition: all 0.3s ease;
                        box-shadow: 0 5px 15px rgba(255, 68, 68, 0.4);
                    " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(255, 68, 68, 0.6)'" 
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 15px rgba(255, 68, 68, 0.4)'">
                        🗑️ نعم، احذف المنتج
                    </button>
                    <button id="cancelDeleteBtn" style="
                        background: linear-gradient(135deg, #0ef, #0ab);
                        color: #1a1a2e;
                        border: none;
                        padding: 12px 30px;
                        border-radius: 10px;
                        font-size: 16px;
                        font-weight: bold;
                        cursor: pointer;
                        transition: all 0.3s ease;
                        box-shadow: 0 5px 15px rgba(14, 255, 255, 0.4);
                    " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(14, 255, 255, 0.6)'" 
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 15px rgba(14, 255, 255, 0.4)'">
                        ❌ إلغاء
                    </button>
                </div>
            </div>
        `;
        
        // إضافة الأنيميشن
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            @keyframes slideIn {
                from { 
                    transform: translateY(-50px);
                    opacity: 0;
                }
                to { 
                    transform: translateY(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);
        
        document.body.appendChild(modal);
        
        // معالجة الأزرار
        document.getElementById('confirmDeleteBtn').onclick = function() {
            console.log('✅ تأكيد حذف المنتج:', productId, productName);
            document.body.removeChild(modal);
            document.head.removeChild(style);
            
            // إظهار مؤشر التحميل
            const loadingDiv = document.createElement('div');
            loadingDiv.id = 'deleteLoadingIndicator';
            loadingDiv.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: linear-gradient(135deg, #0ef, #0ab);
                color: #1a1a2e;
                padding: 15px 25px;
                border-radius: 10px;
                font-weight: bold;
                z-index: 10000;
                box-shadow: 0 5px 20px rgba(14, 255, 255, 0.5);
                animation: fadeIn 0.3s ease-in-out;
            `;
            loadingDiv.innerHTML = '🔄 جاري حذف المنتج...';
            document.body.appendChild(loadingDiv);
            
            form.submit();
        };
        
        document.getElementById('cancelDeleteBtn').onclick = function() {
            console.log('❌ تم إلغاء حذف المنتج:', productId);
            document.body.removeChild(modal);
            document.head.removeChild(style);
        };
        
        // إغلاق عند النقر على الخلفية
        modal.onclick = function(e) {
            if (e.target === modal) {
                document.body.removeChild(modal);
                document.head.removeChild(style);
            }
        };
        
        return false;
    }
</script>

