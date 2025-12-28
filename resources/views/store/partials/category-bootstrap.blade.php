<section class="py-3 py-md-5 text-light">
    <div class="container">
        {{-- هيدر الصنف --}}
        <div class="row g-4 align-items-center mb-4">
            <div class="col-12 col-lg-5">
                <div class="glass rounded-4 overflow-hidden h-100">
                    <div class="position-relative">
                        @if(!empty($category->image))
                            <img src="{{ asset('storage/'.$category->image) }}"
                                 class="w-100 bg-black"
                                 style="height: 200px; height: 260px; object-fit: contain;"
                                 alt="{{ $category->translated_name }}">
                        @else
                            <div class="w-100 d-flex align-items-center justify-content-center bg-black text-secondary small"
                                 style="height: 200px; height: 260px;">
                                لا توجد صورة لهذا الصنف
                            </div>
                        @endif
                    </div>
                    <div class="p-3 p-md-4">
                        <h1 class="h5 h4-md fw-bold mb-2 text-white">{{ $category->translated_name }}</h1>
                        <p class="text-secondary mb-0 small">
                            {{ $category->translated_description ?: 'اكتشف جميع الأنواع والشركات والمنتجات التابعة لهذا الصنف.' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-7">
                <div class="glass rounded-4 p-3 mb-3">
                    <h2 class="h6 fw-semibold mb-2">الأنواع داخل هذا الصنف</h2>
                    <div class="strip-scroll">
                        @forelse($types as $type)
                            <div class="strip-card text-center">
                                @if(!empty($type->image))
                                    <img src="{{ asset('storage/'.$type->image) }}" class="strip-img" alt="{{ $type->translated_name }}">
                                @else
                                    <div class="strip-img d-flex align-items-center justify-content-center bg-black text-secondary small">
                                        {{ $type->translated_name }}
                                    </div>
                                @endif
                                <div class="p-2 p-md-3">
                                    <h6 class="mb-1 text-white small">{{ $type->translated_name }}</h6>
                                    <span class="badge bg-warning text-dark small">نوع ضمن {{ $category->translated_name }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-secondary small mb-0">لا توجد أنواع مرتبطة بهذا الصنف حالياً.</p>
                        @endforelse
                    </div>
                </div>

                <div class="glass rounded-4 p-3">
                    <h2 class="h6 fw-semibold mb-2">الشركات المرتبطة بالصنف</h2>
                    <div class="strip-scroll">
                        @forelse($companies as $company)
                            <div class="strip-card text-center">
                                @if(!empty($company->image))
                                    <img src="{{ asset('storage/'.$company->image) }}" class="strip-img" alt="{{ $company->name }}">
                                @else
                                    <div class="strip-img d-flex align-items-center justify-content-center bg-black text-secondary small">
                                        لا توجد صورة
                                    </div>
                                @endif
                                <div class="p-2 p-md-3">
                                    <h6 class="mb-1 text-white small">{{ $company->name }}</h6>
                                    <span class="badge bg-success text-dark small">شركة مرتبطة بهذا الصنف</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-secondary small mb-0">لا توجد شركات مرتبطة بهذا الصنف حتى الآن.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- المنتجات التابعة للصنف --}}
        <div class="mt-3 mt-md-4">
            <h2 class="h5 fw-semibold mb-3">المنتجات داخل {{ $category->translated_name }}</h2>
            <div class="products-scroll">
                @forelse($products as $product)
                    <x-product-card :product="$product" :showCategory="false" />
                @empty
                    <p class="text-secondary">لا توجد منتجات ضمن هذا الصنف حالياً.</p>
                @endforelse
            </div>
        </div>
    </div>
</section>

