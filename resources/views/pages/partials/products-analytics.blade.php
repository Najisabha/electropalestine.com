<section class="container py-4 text-light">
    <div class="glass p-4 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 fw-bold mb-0">تحليلات المنتجات</h1>
            <span class="badge bg-secondary text-dark small">
                إجمالي المنتجات المعروضة: {{ $products->count() }}
            </span>
        </div>
        <p class="text-secondary small mb-3">
            الأرقام تقريبية وتعتمد على سعر التكلفة الحالي وعدّاد المبيعات لكل منتج.
        </p>

        <form method="GET" action="{{ route('admin.products.analytics') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-secondary mb-1">الصنف</label>
                <select name="category_id" class="form-select auth-input bg-dark text-light">
                    <option value="">الكل</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected($filters['category_id'] == $category->id)>
                            {{ $category->translated_name ?? $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-secondary mb-1">الشركة</label>
                <select name="company_id" class="form-select auth-input bg-dark text-light">
                    <option value="">الكل</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" @selected($filters['company_id'] == $company->id)>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-secondary mb-1">سعر البيع من</label>
                <input type="number" step="0.01" name="min_price" value="{{ $filters['min_price'] }}" class="form-control auth-input">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-secondary mb-1">سعر البيع إلى</label>
                <input type="number" step="0.01" name="max_price" value="{{ $filters['max_price'] }}" class="form-control auth-input">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-secondary mb-1">هامش الربح % من</label>
                <input type="number" step="0.1" name="min_margin" value="{{ $filters['min_margin'] }}" class="form-control auth-input">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-secondary mb-1">هامش الربح % إلى</label>
                <input type="number" step="0.1" name="max_margin" value="{{ $filters['max_margin'] }}" class="form-control auth-input">
            </div>
            <div class="col-md-2 d-flex align-items-center">
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="only_negative" name="only_negative" value="1" @checked($filters['only_negative'])>
                    <label class="form-check-label small text-secondary" for="only_negative">
                        إظهار المنتجات ذات الربح السلبي فقط
                    </label>
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2 mt-3">
                <button class="btn btn-main btn-sm flex-fill">تطبيق الفلاتر</button>
                <a href="{{ route('admin.products.analytics') }}" class="btn btn-outline-secondary btn-sm flex-fill">إعادة الضبط</a>
            </div>
        </form>
    </div>

    <div class="glass p-4">
        <div class="table-responsive">
            <table class="table table-dark table-sm align-middle">
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th>الصنف</th>
                        <th>الشركة</th>
                        <th>سعر التكلفة</th>
                        <th>سعر البيع</th>
                        <th>صافي الربح للوحدة</th>
                        <th>هامش الربح %</th>
                        <th>الوحدات المباعة (تقريبية)</th>
                        <th>الربح الكلي التقريبي</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        @php
                            $profitPerUnit = $product->profit_per_unit;
                            $margin = $product->profit_margin;
                            $unitsSold = $product->units_sold;
                            $totalProfit = $product->total_profit;
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $product->translated_name }}</strong>
                                <div class="small text-secondary">
                                    المخزون: <span class="{{ $product->stock <= 0 ? 'text-danger' : ($product->stock <= config('catalog.low_stock_threshold', 5) ? 'text-warning' : '') }}">
                                        {{ $product->stock }}
                                    </span>
                                    @if(!($product->is_active ?? true))
                                        <span class="badge bg-secondary ms-1">مخفي</span>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $product->category->translated_name ?? $product->category->name ?? '-' }}</td>
                            <td>{{ $product->company->name ?? '-' }}</td>
                            <td>
                                @if(!is_null($product->cost_price))
                                    ${{ number_format($product->cost_price, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>${{ number_format($product->price, 2) }}</td>
                            <td class="{{ $profitPerUnit > 0 ? 'text-info' : ($profitPerUnit < 0 ? 'text-warning' : 'text-secondary') }} fw-semibold">
                                @if(!is_null($profitPerUnit))
                                    ${{ number_format($profitPerUnit, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="{{ $margin > 0 ? 'text-info' : ($margin < 0 ? 'text-warning' : 'text-secondary') }} fw-semibold">
                                @if(!is_null($margin))
                                    {{ number_format($margin, 1) }}%
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-info fw-semibold">
                                {{ number_format($unitsSold, 0) }}
                            </td>
                            <td class="{{ $totalProfit > 0 ? 'text-success' : ($totalProfit < 0 ? 'text-warning' : 'text-secondary') }} fw-bold">
                                @if(!is_null($totalProfit))
                                    ${{ number_format($totalProfit, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-secondary py-3">
                                لا توجد بيانات لعرضها بناءً على الفلاتر الحالية.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

