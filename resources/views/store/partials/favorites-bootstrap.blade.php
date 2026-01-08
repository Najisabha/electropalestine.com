<section class="py-5 text-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 fw-bold mb-0">
                <i class="bi bi-heart-fill text-danger me-2"></i>
                قائمة الرغبات
            </h1>
            <a href="{{ route('store.products') }}" class="btn btn-outline-main">
                <i class="bi bi-arrow-left me-2"></i>
                العودة للمنتجات
            </a>
        </div>

        @if (session('status'))
            <div class="alert alert-success small py-2 mb-3">{{ session('status') }}</div>
        @endif

        @if($favoriteProducts->count() > 0)
            <div class="row g-4">
                @foreach($favoriteProducts as $product)
                    <div class="col-6 col-md-4 col-lg-3">
                        <x-product-card :product="$product" />
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $favoriteProducts->links() }}
            </div>
        @else
            <div class="glass rounded-4 p-5 text-center">
                <i class="bi bi-heart display-1 text-secondary mb-3"></i>
                <h3 class="h5 text-secondary mb-2">قائمة الرغبات فارغة</h3>
                <p class="text-secondary small mb-4">لم تقم بإضافة أي منتجات إلى قائمة الرغبات بعد</p>
                <a href="{{ route('store.products') }}" class="btn btn-main">
                    <i class="bi bi-arrow-left me-2"></i>
                    تصفح المنتجات
                </a>
            </div>
        @endif
    </div>
</section>
