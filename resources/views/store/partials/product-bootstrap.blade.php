<section class="py-5 text-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="bg-dark border border-secondary-subtle rounded-4 p-4 text-center h-100">
                    <div class="bg-black rounded-3 py-5 text-success fw-bold fs-4">{{ $product->name }}</div>
                </div>
            </div>
            <div class="col-lg-6">
                <p class="text-secondary small">منتج من {{ $product->company->name ?? 'شركة' }}</p>
                <h1 class="h3 fw-bold text-white">{{ $product->name }}</h1>
                <div class="d-flex gap-2 my-3">
                    <span class="badge bg-success text-dark">{{ $product->category->name ?? 'صنف رئيسي' }}</span>
                    <span class="badge bg-warning text-dark">{{ $product->type->name ?? 'نوع' }}</span>
                </div>
                <div class="fs-3 fw-black text-success mb-3">${{ number_format($product->price, 2) }}</div>
                <p class="text-secondary">{{ $product->description ?? 'وصف المنتج سيظهر هنا.' }}</p>
                <div class="text-secondary small mb-3">المخزون المتاح: {{ $product->stock }}</div>
                <div class="d-flex gap-2">
                    <button class="btn btn-ep px-4">أضف للسلة</button>
                    <button class="btn btn-ep-outline px-4">تواصل معنا</button>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <h2 class="h5 fw-semibold mb-3">منتجات ذات صلة</h2>
            <div class="row g-3">
                @forelse ($related as $item)
                    <div class="col-12 col-sm-6 col-lg-3">
                        <a href="{{ route('products.show', $item) }}" class="card h-100 bg-dark border-0 text-light">
                            <div class="card-body">
                                <div class="bg-black rounded-3 py-3 mb-3 text-success small text-center">{{ $item->name }}</div>
                                <h6 class="mb-1 text-white">{{ $item->name }}</h6>
                                <div class="text-muted small">{{ $item->company->name ?? '' }}</div>
                                <div class="text-success fw-bold mt-1">${{ number_format($item->price, 2) }}</div>
                            </div>
                        </a>
                    </div>
                @empty
                    <p class="text-secondary">لا يوجد منتجات مشابهة حالياً.</p>
                @endforelse
            </div>
        </div>
    </div>
</section>

