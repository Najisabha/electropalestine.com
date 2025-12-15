<section class="py-5 text-light">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <p class="text-success fw-semibold text-uppercase small mb-2">VoltMart</p>
                <h1 class="fw-black display-5 mb-3">VoltMart متجر إلكتروني احترافي بالأخضر والأسود <span class="accent">ولمسة أصفر</span>.</h1>
                <p class="text-secondary mb-4">كل ما تحتاجه من منتجات إلكترونية مع تصنيفات واضحة: الصنف الرئيسي، النوع، الشركة والمنتج.</p>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="#featured" class="btn btn-ep px-4">استكشف المنتجات</a>
                    <a href="#categories" class="btn btn-ep-outline px-4">الأصناف والأنواع</a>
                </div>
                <div class="d-flex gap-4 mt-4 text-secondary small">
                    <span class="d-flex align-items-center gap-2"><span class="badge rounded-circle bg-success p-2"></span>دعم فوري</span>
                    <span class="d-flex align-items-center gap-2"><span class="badge rounded-circle bg-warning p-2"></span>تسليم سريع</span>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="glass p-4">
                    <div class="row g-3">
                        @foreach ($featured->take(4) as $product)
                            <div class="col-6">
                                <div class="card h-100 bg-dark border-0 text-light">
                                    <div class="card-body text-center">
                                        <div class="bg-black rounded-3 py-3 mb-3 text-success small">{{ $product->name }}</div>
                                        <h6 class="mb-1 text-white">{{ $product->name }}</h6>
                                        <div class="text-muted small">{{ $product->company->name ?? 'شركة' }}</div>
                                        <div class="text-success fw-bold mt-1">${{ number_format($product->price, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="featured" class="py-5 bg-black text-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <p class="text-success small mb-1">منتجات مختارة</p>
                <h2 class="h4 fw-bold mb-0">الأحدث في المتجر</h2>
            </div>
            <a href="#categories" class="text-secondary small">عرض حسب التصنيف</a>
        </div>
        <div class="row g-3">
            @forelse ($featured as $product)
                <div class="col-12 col-sm-6 col-lg-3">
                    <a href="{{ route('products.show', $product) }}" class="card h-100 bg-dark border-0 text-light">
                        <div class="card-body">
                            <div class="bg-black rounded-3 py-3 mb-3 text-success small text-center">{{ $product->name }}</div>
                            <h6 class="mb-1 text-white">{{ $product->name }}</h6>
                            <div class="text-muted small">{{ $product->category->name ?? '' }} • {{ $product->company->name ?? '' }}</div>
                            <div class="text-success fw-bold mt-1">${{ number_format($product->price, 2) }}</div>
                        </div>
                    </a>
                </div>
            @empty
                <p class="text-secondary">لا توجد منتجات بعد.</p>
            @endforelse
        </div>
    </div>
</section>

<section id="categories" class="py-5 text-light">
    <div class="container">
        <div class="mb-4">
            <p class="text-success small mb-1">هيكلة البيانات</p>
            <h2 class="h4 fw-bold mb-0">الصنف الرئيسي، النوع، الشركة، المنتج</h2>
        </div>
        <div class="row g-3">
            @foreach ($categories as $category)
                <div class="col-12 col-lg-4">
                    <div class="glass p-3 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="fw-semibold">{{ $category->name }}</div>
                            <span class="badge bg-success text-dark">الصنف الرئيسي</span>
                        </div>
                        <p class="text-secondary small">{{ $category->description ?? 'تفاصيل الصنف الرئيسي.' }}</p>
                        <div class="d-flex flex-column gap-2">
                            @foreach ($category->types as $type)
                                <div class="bg-dark rounded-3 p-3">
                                    <div class="d-flex justify-content-between text-light small">
                                        <span>{{ $type->name }}</span>
                                        <span class="text-secondary">النوع</span>
                                    </div>
                                    <div class="mt-2 d-flex flex-wrap gap-2">
                                        @foreach ($type->products as $product)
                                            <span class="badge bg-secondary text-light">{{ $product->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

