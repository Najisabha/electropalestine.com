{{-- CSS Styles --}}
<style>
    :root {
        --glass-bg: rgba(21, 25, 30, 0.7);
        --glass-border: rgba(255, 255, 255, 0.08);
        --primary-color: #0db777;
        --primary-hover: #0a8d5b;
    }

    .glass-panel {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .form-control, .form-select {
        background-color: rgba(0, 0, 0, 0.3);
        border: 1px solid var(--glass-border);
        color: #fff !important;
        font-size: 0.9rem;
    }
    .form-control:focus, .form-select:focus {
        background-color: rgba(0, 0, 0, 0.5);
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(13, 183, 119, 0.25);
        color: #fff !important;
    }
    .form-control::placeholder {
        color: #64748b;
    }
    input[type="number"], input[type="text"] {
        color: #fff !important;
        -webkit-text-fill-color: #fff !important;
    }
    select option {
        background-color: #1a1c20;
        color: #fff;
    }

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

    @media (min-width: 992px) {
        .sticky-sidebar {
            position: sticky;
            top: 20px;
            z-index: 10;
        }
    }
</style>

<section class="py-4 py-md-5 min-vh-100">
    <div class="container">
        
        {{-- Header Section --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <div>
                {{-- Breadcrumbs --}}
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2 small" style="--bs-breadcrumb-divider: '>';">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-secondary text-decoration-none">{{ __('common.home') }}</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">{{ __('common.all_products') }}</li>
                    </ol>
                </nav>
                
                <h1 class="h3 fw-bold text-white mb-1">{{ __('common.all_products') }}</h1>
                <p class="text-secondary small mb-0">{{ $products->total() }} {{ __('common.products_available') ?? 'منتج متوفر' }}</p>
            </div>

            {{-- Mobile Filter Button --}}
            <button class="btn btn-outline-main d-lg-none mt-3 mt-md-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                <i class="bi bi-funnel me-2"></i> {{ __('common.filter') ?? 'تصفية' }}
            </button>
        </div>

        <div class="row g-4">
            {{-- Sidebar Filters --}}
            <div class="col-lg-3">
                <div class="offcanvas-lg offcanvas-end bg-dark text-light" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
                    
                    <div class="offcanvas-header border-bottom border-secondary">
                        <h5 class="offcanvas-title" id="filterOffcanvasLabel">{{ __('common.filters') ?? 'الفلاتر' }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#filterOffcanvas" aria-label="Close"></button>
                    </div>

                    <div class="offcanvas-body p-0 sticky-sidebar">
                        <form id="filter-form" method="GET" action="{{ route('store.products') }}" class="w-100">
                            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                            @if(request('per_page')) <input type="hidden" name="per_page" value="{{ request('per_page') }}"> @endif

                            <div class="glass-panel rounded-4 p-4 d-flex flex-column gap-4">
                                
                                {{-- Search --}}
                                <div>
                                    <h6 class="fw-bold mb-3 text-white">{{ __('common.search') ?? 'البحث' }}</h6>
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('common.search_products') ?? 'بحث في المنتجات...' }}" value="{{ $search }}">
                                </div>

                                <hr class="border-secondary my-0 opacity-25">

                                {{-- Categories --}}
                                <div>
                                    <button class="filter-accordion-btn" type="button" data-bs-toggle="collapse" data-bs-target="#categoriesCollapse" aria-expanded="true">
                                        <span>{{ __('common.categories') ?? 'الأصناف' }}</span>
                                        <i class="bi bi-chevron-down small"></i>
                                    </button>
                                    <div class="collapse show" id="categoriesCollapse">
                                        <div class="d-flex flex-column gap-2 mt-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="category_id" id="all_cat" value="" 
                                                       {{ empty($categoryId) ? 'checked' : '' }} onchange="this.form.submit()">
                                                <label class="form-check-label" for="all_cat">{{ __('common.all') ?? 'الكل' }}</label>
                                            </div>
                                            @foreach($categories as $category)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="category_id" id="cat_{{ $category->id }}" 
                                                           value="{{ $category->id }}" {{ $categoryId == $category->id ? 'checked' : '' }} onchange="this.form.submit()">
                                                    <label class="form-check-label" for="cat_{{ $category->id }}">{{ $category->translated_name }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <hr class="border-secondary my-0 opacity-25">

                                {{-- Price Range --}}
                                <div>
                                    <h6 class="fw-bold mb-3 text-white">{{ __('common.price_range') ?? 'نطاق السعر' }}</h6>
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

                                {{-- Availability --}}
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="in_stock" value="1" id="stockSwitch" 
                                           {{ $inStock === '1' ? 'checked' : '' }} onchange="this.form.submit()">
                                    <label class="form-check-label" for="stockSwitch">{{ __('common.in_stock_only') ?? 'عرض المتوفر فقط' }}</label>
                                </div>

                                <hr class="border-secondary my-0 opacity-25">

                                {{-- Featured --}}
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="featured" value="1" id="featuredSwitch" 
                                           {{ $featured === '1' ? 'checked' : '' }} onchange="this.form.submit()">
                                    <label class="form-check-label" for="featuredSwitch">{{ __('common.featured_only') ?? 'المنتجات المميزة فقط' }}</label>
                                </div>

                                <hr class="border-secondary my-0 opacity-25">

                                {{-- Companies --}}
                                <div>
                                    <button class="filter-accordion-btn" type="button" data-bs-toggle="collapse" data-bs-target="#brandsCollapse" aria-expanded="true">
                                        <span>{{ __('common.companies') ?? 'الشركات' }}</span>
                                        <i class="bi bi-chevron-down small"></i>
                                    </button>
                                    <div class="collapse show" id="brandsCollapse">
                                        <div class="d-flex flex-column gap-2 mt-2" style="max-height: 200px; overflow-y: auto;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="company_id" id="all_comp" value="" 
                                                       {{ empty($companyId) ? 'checked' : '' }} onchange="this.form.submit()">
                                                <label class="form-check-label" for="all_comp">{{ __('common.all') ?? 'الكل' }}</label>
                                            </div>
                                            @foreach($companies as $company)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="company_id" id="c_{{ $company->id }}" 
                                                           value="{{ $company->id }}" {{ $companyId == $company->id ? 'checked' : '' }} onchange="this.form.submit()">
                                                    <label class="form-check-label" for="c_{{ $company->id }}">{{ $company->name }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <hr class="border-secondary my-0 opacity-25">

                                {{-- Ratings --}}
                                <div>
                                    <button class="filter-accordion-btn" type="button" data-bs-toggle="collapse" data-bs-target="#ratingCollapse" aria-expanded="true">
                                        <span>{{ __('common.rating') ?? 'التقييم' }}</span>
                                        <i class="bi bi-chevron-down small"></i>
                                    </button>
                                    <div class="collapse show" id="ratingCollapse">
                                        <div class="d-flex flex-column gap-2 mt-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="min_rating" id="r_0" 
                                                       value="0" {{ $minRating == 0 ? 'checked' : '' }} onchange="this.form.submit()">
                                                <label class="form-check-label" for="r_0">{{ __('common.all') ?? 'الكل' }}</label>
                                            </div>
                                            @foreach([4, 3, 2, 1] as $star)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="min_rating" id="r_{{ $star }}" 
                                                           value="{{ $star }}" {{ $minRating == $star ? 'checked' : '' }} onchange="this.form.submit()">
                                                    <label class="form-check-label" for="r_{{ $star }}">
                                                        <span class="text-warning">
                                                            @for($i=0; $i<$star; $i++) <i class="bi bi-star-fill"></i> @endfor
                                                            @for($i=$star; $i<5; $i++) <i class="bi bi-star"></i> @endfor
                                                        </span>
                                                        <span class="small ms-1 text-secondary">{{ __('common.or_more') ?? 'أو أكثر' }}</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="d-grid gap-2 mt-2">
                                    <button type="submit" class="btn btn-main btn-sm">{{ __('common.apply') ?? 'تطبيق' }}</button>
                                    <a href="{{ route('store.products') }}" class="btn btn-outline-main btn-sm">{{ __('common.reset') ?? 'إعادة تعيين' }}</a>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Products Grid --}}
            <div class="col-lg-9">
                
                {{-- Toolbar --}}
                <div class="glass-panel rounded-3 p-3 mb-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="d-none d-md-block text-secondary small">
                        {{ __('common.showing_results') ?? 'عرض النتائج' }} {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} {{ __('common.of') ?? 'من' }} {{ $products->total() }}
                    </div>
                    
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <label class="text-secondary small text-nowrap">{{ __('common.items_per_page') ?? 'عدد العناصر' }}:</label>
                        <select form="filter-form" name="per_page" class="form-select form-select-sm w-auto bg-transparent text-light border-secondary" onchange="document.getElementById('filter-form').submit()" style="color: #fff !important;">
                            <option value="9" {{ $perPage == 9 ? 'selected' : '' }}>9</option>
                            <option value="12" {{ $perPage == 12 ? 'selected' : '' }}>12</option>
                            <option value="24" {{ $perPage == 24 ? 'selected' : '' }}>24</option>
                            <option value="48" {{ $perPage == 48 ? 'selected' : '' }}>48</option>
                        </select>
                        
                        <label class="text-secondary small text-nowrap ms-3">{{ __('common.sort_by') ?? 'الترتيب' }}:</label>
                        <select form="filter-form" name="sort" class="form-select form-select-sm w-auto bg-transparent text-light border-secondary" onchange="document.getElementById('filter-form').submit()" style="color: #fff !important;">
                            <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>{{ __('common.newest') ?? 'الأحدث' }}</option>
                            <option value="best_selling" {{ $sort === 'best_selling' ? 'selected' : '' }}>{{ __('common.best_selling') ?? 'الأكثر مبيعاً' }}</option>
                            <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>{{ __('common.price_low_to_high') ?? 'السعر: الأقل' }}</option>
                            <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>{{ __('common.price_high_to_low') ?? 'السعر: الأعلى' }}</option>
                            <option value="highest_rated" {{ $sort === 'highest_rated' ? 'selected' : '' }}>{{ __('common.highest_rated') ?? 'الأعلى تقييماً' }}</option>
                        </select>
                    </div>
                </div>

                {{-- Products List --}}
                @if($products->count() > 0)
                    <div class="row g-3">
                        @foreach($products as $product)
                            <div class="col-6 col-md-4 col-lg-3">
                                <x-product-card :product="$product" />
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="glass-panel rounded-3 p-5 text-center">
                        <i class="bi bi-inbox fs-1 text-secondary mb-3"></i>
                        <h5 class="text-white mb-2">{{ __('common.no_products_found') ?? 'لا توجد منتجات' }}</h5>
                        <p class="text-secondary mb-4">{{ __('common.try_different_filters') ?? 'جرب تغيير الفلاتر أو البحث عن منتجات أخرى' }}</p>
                        <a href="{{ route('store.products') }}" class="btn btn-main">{{ __('common.reset_filters') ?? 'إعادة تعيين الفلاتر' }}</a>
                    </div>
                @endif

            </div>
        </div>
    </div>
</section>

<script>
    // Price range slider sync
    document.addEventListener('DOMContentLoaded', function() {
        const priceRange = document.getElementById('price-range');
        const maxPriceInput = document.getElementById('max_price');
        
        if (priceRange && maxPriceInput) {
            priceRange.addEventListener('input', function() {
                maxPriceInput.value = this.value;
            });
            
            maxPriceInput.addEventListener('input', function() {
                priceRange.value = this.value;
            });
        }
    });
</script>
