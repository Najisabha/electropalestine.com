<div class="container py-5 text-light">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-success small mb-1">مرحباً {{ auth()->user()->name }}</p>
            <h1 class="h4 fw-bold">لوحة التحكم الإدارية</h1>
        </div>
        <div class="badge bg-success text-dark px-3 py-2">قاعدة البيانات: الصنف الرئيسي • النوع • الشركة • المنتج</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="glass p-3 h-100">
                <p class="text-secondary small mb-1">الأصناف الرئيسية</p>
                <div class="fs-2 fw-black">{{ $metrics['categories'] }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="glass p-3 h-100">
                <p class="text-secondary small mb-1">الأنواع</p>
                <div class="fs-2 fw-black">{{ $metrics['types'] }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="glass p-3 h-100">
                <p class="text-secondary small mb-1">الشركات</p>
                <div class="fs-2 fw-black">{{ $metrics['companies'] }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="glass p-3 h-100">
                <p class="text-secondary small mb-1">المنتجات</p>
                <div class="fs-2 fw-black">{{ $metrics['products'] }}</div>
            </div>
        </div>
    </div>

    <div class="glass p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 fw-semibold mb-0">أحدث المنتجات</h2>
            <span class="text-secondary small">آخر ٥ عناصر</span>
        </div>
        <div class="table-responsive">
            <table class="table table-dark table-sm align-middle mb-0">
                <thead class="table-secondary text-dark">
                    <tr>
                        <th>المنتج</th>
                        <th>الصنف</th>
                        <th>النوع</th>
                        <th>الشركة</th>
                        <th>السعر</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($latestProducts as $product)
                        <tr>
                            <td class="fw-medium text-white">{{ $product->name }}</td>
                            <td>{{ $product->category->name ?? '-' }}</td>
                            <td>{{ $product->type->name ?? '-' }}</td>
                            <td>{{ $product->company->name ?? '-' }}</td>
                            <td class="text-success fw-semibold">${{ number_format($product->price, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary">لا توجد بيانات بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

