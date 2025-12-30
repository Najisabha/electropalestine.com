{{-- 1. تنسيقات CSS المخصصة (داخل نفس الملف) --}}
<style>
    /* المتغيرات الأساسية */
    :root {
        --glass-bg: rgba(21, 25, 30, 0.7);
        --glass-border: rgba(255, 255, 255, 0.08);
        --primary-color: #3b82f6; /* لون أزرق عصري */
        --primary-hover: #2563eb;
    }

    /* خلفية الصفحة العامة (اختياري إذا كانت موجودة في layout) */
    body {
        background-color: #0f1115;
        color: #e2e8f0;
    }

    /* لوحات الزجاج (Glass Panels) */
    .glass-panel {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    /* Company Header Section */
    .company-header {
        position: relative;
        border-radius: 24px;
        overflow: hidden;
        margin-bottom: 2rem;
        min-height: 300px;
    }

    .company-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.3;
        z-index: 1;
    }

    .company-header-content {
        position: relative;
        z-index: 2;
        padding: 3rem 2rem;
        background: linear-gradient(180deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.8) 100%);
        display: flex;
        align-items: center;
        gap: 2rem;
    }

    .company-logo-wrapper {
        flex-shrink: 0;
        width: 120px;
        height: 120px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .company-logo-wrapper img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .company-info h1 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .verified-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        background: linear-gradient(135deg, #0db777, #0a8d5b);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .company-description {
        color: rgba(255, 255, 255, 0.8);
        font-size: 1rem;
        line-height: 1.6;
        margin-top: 0.5rem;
    }

    /* تحسين حقول الإدخال لتتناسب مع الوضع المظلم */
    .form-control, .form-select {
        background-color: rgba(0, 0, 0, 0.3);
        border: 1px solid var(--glass-border);
        color: #fff !important;
        font-size: 0.9rem;
    }
    .form-control:focus, .form-select:focus {
        background-color: rgba(0, 0, 0, 0.5);
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25);
        color: #fff !important;
    }
    .form-control input, .form-control[type="number"], .form-control[type="text"] {
        color: #fff !important;
    }
    
    /* placeholder color */
    .form-control::placeholder {
        color: #64748b;
    }
    
    /* إصلاح لون النص في حقول الإدخال */
    input[type="number"], input[type="text"] {
        color: #fff !important;
        -webkit-text-fill-color: #fff !important;
    }
    input[type="number"]:focus, input[type="text"]:focus {
        color: #fff !important;
        -webkit-text-fill-color: #fff !important;
    }
    select option {
        background-color: #1a1c20;
        color: #fff;
    }

    /* تحسين شريط السعر (Range Slider) */
    input[type=range] {
        -webkit-appearance: none;
        width: 100%;
        background: transparent;
    }
    input[type=range]::-webkit-slider-runnable-track {
        width: 100%;
        height: 6px;
        background: #334155;
        border-radius: 3px;
    }
    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none;
        height: 18px;
        width: 18px;
        border-radius: 50%;
        background: var(--primary-color);
        margin-top: -6px;
        cursor: pointer;
        transition: transform 0.1s;
    }
    input[type=range]::-webkit-slider-thumb:hover {
        transform: scale(1.1);
    }

    /* Accordion Styles for Filters */
    .filter-accordion-btn {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: none;
        border: none;
        color: #fff;
        padding: 0.75rem 0;
        font-weight: 600;
        text-decoration: none;
    }
    .filter-accordion-btn:hover {
        color: var(--primary-color);
    }

    /* Checkbox & Radio Styles */
    .form-check-input {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.2);
        cursor: pointer;
    }
    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    .form-check-label {
        cursor: pointer;
        font-size: 0.9rem;
        color: #cbd5e1;
    }

    /* Sidebar Sticky on Desktop */
    @media (min-width: 992px) {
        .sticky-sidebar {
            position: sticky;
            top: 20px;
            z-index: 10;
        }
    }

    @media (max-width: 768px) {
        .company-header-content {
            flex-direction: column;
            text-align: center;
            padding: 2rem 1rem;
        }
        .company-logo-wrapper {
            width: 100px;
            height: 100px;
        }
        .company-info h1 {
            font-size: 1.5rem;
        }
    }
</style>

<section class="py-4 py-md-5 min-vh-100">
    <div class="container">
        
        {{-- Company Header Section with Background --}}
        <div class="company-header">
            @if($company->background)
                <img src="{{ asset('storage/' . $company->background) }}" alt="{{ $company->name }}" class="company-background">
            @else
                <div class="company-background" style="background: linear-gradient(135deg, #0db777 0%, #0a8d5b 100%);"></div>
            @endif
            
            <div class="company-header-content">
                <div class="company-logo-wrapper">
                    @if($company->image)
                        <img src="{{ asset('storage/' . $company->image) }}" alt="{{ $company->name }}">
                    @else
                        <i class="bi bi-building" style="font-size: 3rem; color: rgba(255,255,255,0.5);"></i>
                    @endif
                </div>
                
                <div class="company-info flex-grow-1">
                    <h1>
                        {{ $company->name }}
                        <span class="verified-badge">
                            <i class="bi bi-check-circle-fill"></i>
                            موثق
                        </span>
                    </h1>
                    @if($company->description || $company->description_en)
                        <div class="company-description">
                            {{ app()->getLocale() === 'ar' ? ($company->description ?? $company->description_en) : ($company->description_en ?? $company->description) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Header Section --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <div>
                {{-- Breadcrumbs --}}
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2 small" style="--bs-breadcrumb-divider: '>';">
                        <li class="breadcrumb-item"><a href="/" class="text-secondary text-decoration-none">الرئيسية</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">{{ $company->name }}</li>
                    </ol>
                </nav>
                
                <h2 class="h4 fw-bold text-white mb-1">جميع منتجات {{ $company->name }}</h2>
                <p class="text-secondary small mb-0">{{ $products->total() }} منتج متوفر</p>
            </div>

            {{-- Mobile Filter Button --}}
            <button class="btn btn-outline-light d-lg-none mt-3 mt-md-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                <i class="bi bi-funnel me-2"></i> تصفية النتائج
            </button>
        </div>

        <div class="row g-4">
            {{-- 2. Sidebar Filters (Desktop & Mobile Wrapper) --}}
            <div class="col-lg-3">
                <div class="offcanvas-lg offcanvas-end bg-dark text-light" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
                    
                    {{-- Mobile Header --}}
                    <div class="offcanvas-header border-bottom border-secondary">
                        <h5 class="offcanvas-title" id="filterOffcanvasLabel">الفلاتر</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#filterOffcanvas" aria-label="Close"></button>
                    </div>

                    <div class="offcanvas-body p-0 sticky-sidebar">
                        <form id="filter-form" method="GET" action="{{ route('companies.show', $company) }}" class="w-100">
                            {{-- Hidden Inputs --}}
                            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                            @if(request('per_page')) <input type="hidden" name="per_page" value="{{ request('per_page') }}"> @endif

                            <div class="glass-panel rounded-4 p-4 d-flex flex-column gap-4">
                                
                                {{-- 1. Price Range --}}
                                <div>
                                    <h6 class="fw-bold mb-3 text-white">نطاق السعر</h6>
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <input type="number" name="min_price" id="min_price" class="form-control form-control-sm text-center" 
                                               placeholder="0" value="{{ $minPrice }}" min="0" max="10000" style="color: #fff !important;">
                                        <span class="text-secondary">-</span>
                                        <input type="number" name="max_price" id="max_price" class="form-control form-control-sm text-center" 
                                               placeholder="10000" value="{{ $maxPrice }}" min="0" max="10000" style="color: #fff !important;">
                                    </div>
                                    <input type="range" id="price-range" min="0" max="10000" step="50" value="{{ $maxPrice }}">
                                </div>

                                <hr class="border-secondary my-0 opacity-25">

                                {{-- 2. Availability Switch --}}
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="in_stock" value="1" id="stockSwitch" 
                                           {{ $inStock === '1' ? 'checked' : '' }} onchange="this.form.submit()">
                                    <label class="form-check-label" for="stockSwitch">عرض المتوفر فقط</label>
                                </div>

                                <hr class="border-secondary my-0 opacity-25">

                                {{-- 3. Categories (Expandable) --}}
                                @if($categories->count() > 0)
                                <div>
                                    <button class="filter-accordion-btn" type="button" data-bs-toggle="collapse" data-bs-target="#categoriesCollapse" aria-expanded="true">
                                        <span>الأصناف</span>
                                        <i class="bi bi-chevron-down small"></i>
                                    </button>
                                    <div class="collapse show" id="categoriesCollapse">
                                        <div class="d-flex flex-column gap-2 mt-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="category_id" id="all_cat" value="" 
                                                       {{ empty($categoryId) ? 'checked' : '' }} onchange="this.form.submit()">
                                                <label class="form-check-label" for="all_cat">الكل</label>
                                            </div>
                                            @foreach($categories as $category)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="category_id" id="cat_{{ $category->id }}" 
                                                           value="{{ $category->id }}" {{ $categoryId == $category->id ? 'checked' : '' }} onchange="this.form.submit()">
                                                    <label class="form-check-label" for="cat_{{ $category->id }}">{{ $category->translated_name ?? $category->name }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <hr class="border-secondary my-0 opacity-25">
                                @endif

                                {{-- 4. Types (Expandable) --}}
                                @if($types->count() > 0)
                                <div>
                                    <button class="filter-accordion-btn" type="button" data-bs-toggle="collapse" data-bs-target="#typesCollapse" aria-expanded="true">
                                        <span>الأنواع</span>
                                        <i class="bi bi-chevron-down small"></i>
                                    </button>
                                    <div class="collapse show" id="typesCollapse">
                                        <div class="d-flex flex-column gap-2 mt-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="type_id" id="all_type" value="" 
                                                       {{ empty($typeId) ? 'checked' : '' }} onchange="this.form.submit()">
                                                <label class="form-check-label" for="all_type">الكل</label>
                                            </div>
                                            @foreach($types as $type)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="type_id" id="t_{{ $type->id }}" 
                                                           value="{{ $type->id }}" {{ $typeId == $type->id ? 'checked' : '' }} onchange="this.form.submit()">
                                                    <label class="form-check-label" for="t_{{ $type->id }}">{{ $type->translated_name ?? $type->name }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <hr class="border-secondary my-0 opacity-25">
                                @endif

                                {{-- 5. Ratings --}}
                                <div>
                                    <button class="filter-accordion-btn" type="button" data-bs-toggle="collapse" data-bs-target="#ratingCollapse" aria-expanded="true">
                                        <span>التقييم</span>
                                        <i class="bi bi-chevron-down small"></i>
                                    </button>
                                    <div class="collapse show" id="ratingCollapse">
                                        <div class="d-flex flex-column gap-2 mt-2">
                                            @foreach([4, 3, 2, 1] as $star)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="min_rating" id="r_{{ $star }}" 
                                                           value="{{ $star }}" {{ $minRating == $star ? 'checked' : '' }} onchange="this.form.submit()">
                                                    <label class="form-check-label" for="r_{{ $star }}">
                                                        <span class="text-warning">
                                                            @for($i=0; $i<$star; $i++) <i class="bi bi-star-fill"></i> @endfor
                                                            @for($i=$star; $i<5; $i++) <i class="bi bi-star"></i> @endfor
                                                        </span>
                                                        <span class="small ms-1 text-secondary">أو أكثر</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="d-grid gap-2 mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm">تطبيق</button>
                                    <a href="{{ route('companies.show', $company) }}" class="btn btn-outline-light btn-sm">إعادة تعيين</a>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- 3. Products Grid --}}
            <div class="col-lg-9">
                
                {{-- Toolbar --}}
                <div class="glass-panel rounded-3 p-3 mb-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="d-none d-md-block text-secondary small">
                        عرض النتائج {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}
                    </div>
                    
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <label class="text-secondary small text-nowrap">عدد العناصر:</label>
                        <select form="filter-form" name="per_page" class="form-select form-select-sm w-auto bg-transparent text-light border-secondary" onchange="document.getElementById('filter-form').submit()" style="color: #fff !important;">
                            <option value="9" {{ ($perPage ?? 9) == 9 ? 'selected' : '' }}>9</option>
                            <option value="15" {{ ($perPage ?? 9) == 15 ? 'selected' : '' }}>15</option>
                            <option value="30" {{ ($perPage ?? 9) == 30 ? 'selected' : '' }}>30</option>
                        </select>
                        
                        <label class="text-secondary small text-nowrap ms-3">الترتيب:</label>
                        <select form="filter-form" name="sort" class="form-select form-select-sm w-auto bg-transparent text-light border-secondary" onchange="document.getElementById('filter-form').submit()" style="color: #fff !important;">
                            <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>الأحدث</option>
                            <option value="best_selling" {{ $sort === 'best_selling' ? 'selected' : '' }}>الأكثر مبيعاً</option>
                            <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>السعر: الأقل</option>
                            <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>السعر: الأعلى</option>
                        </select>
                    </div>
                </div>

                {{-- Products List --}}
                <div class="row g-3">
                    @forelse($products as $product)
                        <div class="col-6 col-md-4">
                            <x-product-card :product="$product" :showCategory="false" />
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="glass-panel rounded-4 p-5 text-center">
                                <i class="bi bi-box-seam display-1 text-secondary opacity-25 mb-3"></i>
                                <h4 class="h5 text-white">لا توجد منتجات</h4>
                                <p class="text-secondary">لم نعثر على منتجات تطابق خيارات البحث الحالية.</p>
                                <a href="{{ route('companies.show', $company) }}" class="btn btn-primary px-4 mt-2">مسح الفلاتر</a>
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                <div class="mt-5 d-flex justify-content-center">
                    {{ $products->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 4. JavaScript المنطق البرمجي --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const priceRange = document.getElementById('price-range');
    const maxPriceInput = document.getElementById('max_price');
    const minPriceInput = document.getElementById('min_price');

    // دالة لتحديث Slider عند تغيير Input
    function updateRange() {
        let val = parseFloat(maxPriceInput.value) || 0;
        if(val > 10000) val = 10000;
        if(val < 0) val = 0;
        priceRange.value = val;
        maxPriceInput.value = val;
    }

    function updateMinPrice() {
        let val = parseFloat(minPriceInput.value) || 0;
        if(val > 10000) val = 10000;
        if(val < 0) val = 0;
        minPriceInput.value = val;
    }

    if(priceRange && maxPriceInput && minPriceInput) {
        // عند تحريك المؤشر
        priceRange.addEventListener('input', function() {
            let val = parseFloat(this.value);
            if(val > 10000) val = 10000;
            if(val < 0) val = 0;
            maxPriceInput.value = val;
        });

        // عند الكتابة في حقل الحد الأعلى
        maxPriceInput.addEventListener('input', function() {
            let val = parseFloat(this.value) || 0;
            if(val > 10000) {
                val = 10000;
                this.value = 10000;
            }
            if(val < 0) {
                val = 0;
                this.value = 0;
            }
            priceRange.value = val;
        });

        maxPriceInput.addEventListener('change', updateRange);
        
        // عند الكتابة في حقل الحد الأدنى
        minPriceInput.addEventListener('input', function() {
            let val = parseFloat(this.value) || 0;
            if(val > 10000) {
                val = 10000;
                this.value = 10000;
            }
            if(val < 0) {
                val = 0;
                this.value = 0;
            }
            // التحقق من أن الحد الأدنى لا يتجاوز الحد الأعلى
            if(val > parseFloat(maxPriceInput.value)) {
                maxPriceInput.value = val;
                priceRange.value = val;
            }
        });

        minPriceInput.addEventListener('change', function() {
            updateMinPrice();
            if(parseFloat(this.value) > parseFloat(maxPriceInput.value)) {
                maxPriceInput.value = this.value;
                priceRange.value = this.value;
            }
        });
    }
});
</script>

