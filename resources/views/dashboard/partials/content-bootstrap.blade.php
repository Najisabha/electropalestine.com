<div class="container py-5 text-light">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-success small mb-1">مرحباً {{ auth()->user()->name }}</p>
            <h1 class="h4 fw-bold">لوحة التحكم الإدارية</h1>
        </div>
        <div class="badge bg-success text-dark px-3 py-2">قاعدة البيانات: الصنف الرئيسي • النوع • الشركة • المنتج</div>
    </div>

    {{-- تنبيه عام في أعلى لوحة التحكم عند وجود منتجات منخفضة المخزون --}}
    @php
        $lowStockCount = $lowStockProducts->count();
        $lowStockThreshold = (int) config('catalog.low_stock_threshold', 5);
    @endphp

    @if ($lowStockCount > 0)
        <div class="alert alert-warning border border-warning-subtle bg-warning-subtle text-dark d-flex justify-content-between align-items-center mb-4" role="alert">
            <div>
                <strong>تنبيه مخزون منخفض:</strong>
                هناك {{ $lowStockCount }} منتج/منتجات وصل مخزونها إلى {{ $lowStockThreshold }} أو أقل.
                <span class="text-muted small">الرجاء إعادة الطلب أو تحديث الكميات لتجنّب نفاذ المخزون.</span>
            </div>
            <a href="#low-stock-section" class="btn btn-sm btn-outline-dark">
                عرض التفاصيل
            </a>
        </div>
    @endif

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

    {{-- قمع المبيعات حسب الفترة + مخطط حالات الطلبات --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="glass p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2 class="h6 fw-semibold mb-0">قمع المبيعات حسب الفترة</h2>
                    <span class="text-secondary small">هرمي (اليوم ← الأسبوع ← الشهر)</span>
                </div>
                <div class="d-flex align-items-center justify-content-center" style="min-height: 220px;">
                    <canvas id="salesFunnelChart" style="max-height: 220px;"></canvas>
                </div>
            </div>
        </div>

        {{-- مخطط دائري لحالات الطلبات --}}
        <div class="col-lg-4">
            <div class="glass p-3 h-100 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2 class="h6 fw-semibold mb-0">توزيع حالات الطلبات</h2>
                    <span class="text-secondary small">مخطط دائري</span>
                </div>
                <div class="flex-grow-1 d-flex align-items-center justify-content-center">
                    <canvas id="ordersStatusPie" style="max-height: 220px;"></canvas>
                </div>
                <div class="mt-2 small text-secondary d-flex flex-wrap gap-2">
                    <span class="badge bg-secondary text-dark">معلقة: {{ $salesStats['status_counts']['pending'] ?? 0 }}</span>
                    <span class="badge bg-success text-dark">مؤكدة: {{ $salesStats['status_counts']['confirmed'] ?? 0 }}</span>
                    <span class="badge bg-danger">ملغاة: {{ $salesStats['status_counts']['cancelled'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ملخص المبيعات والأرباح (بعد قسم أحدث المنتجات) --}}
    <div class="glass p-4 mb-4">
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
                        <th>سعر التكلفة</th>
                        <th>سعر البيع</th>
                        <th>صافي الربح للوحدة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($latestProducts as $product)
                        <tr>
                            @php
                                $cost = $product->cost_price;
                                $sale = $product->price;
                                $profit = $product->profit_per_unit;
                            @endphp
                            <td class="fw-medium text-white">{{ $product->name }}</td>
                            <td>{{ $product->category->translated_name ?? '-' }}</td>
                            <td>{{ $product->type->translated_name ?? '-' }}</td>
                            <td>{{ $product->company->name ?? '-' }}</td>
                            <td class="text-secondary">
                                @if(!is_null($cost))
                                    ${{ number_format($cost, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-success">
                                ${{ number_format($sale, 2) }}
                            </td>
                            <td class="{{ !is_null($profit) && $profit > 0 ? 'text-info' : (!is_null($profit) && $profit < 0 ? 'text-warning' : 'text-secondary') }} fw-semibold">
                                @if(!is_null($profit))
                                    ${{ number_format($profit, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary">لا توجد بيانات بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="row g-3 mt-3">
            <div class="col-lg-8">
                <div class="glass p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h6 fw-semibold mb-0">ملخص المبيعات والأرباح</h2>
                        <span class="text-secondary small">الأرقام تقريبية بناءً على تكلفة المنتج الحالية</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 bg-dark border border-secondary-subtle h-100">
                                <p class="small text-secondary mb-1">اليوم</p>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="small text-secondary">المبيعات</div>
                                        <div class="fs-5 fw-bold text-success">
                                            ${{ number_format($salesStats['today']['sales'] ?? 0, 2) }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="small text-secondary">الربح التقريبي</div>
                                        <div class="fs-5 fw-bold {{ ($salesStats['today']['profit'] ?? 0) >= 0 ? 'text-info' : 'text-warning' }}">
                                            ${{ number_format($salesStats['today']['profit'] ?? 0, 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 bg-dark border border-secondary-subtle h-100">
                                <p class="small text-secondary mb-1">هذا الأسبوع</p>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="small text-secondary">المبيعات</div>
                                        <div class="fs-5 fw-bold text-success">
                                            ${{ number_format($salesStats['week']['sales'] ?? 0, 2) }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="small text-secondary">الربح التقريبي</div>
                                        <div class="fs-5 fw-bold {{ ($salesStats['week']['profit'] ?? 0) >= 0 ? 'text-info' : 'text-warning' }}">
                                            ${{ number_format($salesStats['week']['profit'] ?? 0, 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 bg-dark border border-secondary-subtle h-100">
                                <p class="small text-secondary mb-1">هذا الشهر</p>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="small text-secondary">المبيعات</div>
                                        <div class="fs-5 fw-bold text-success">
                                            ${{ number_format($salesStats['month']['sales'] ?? 0, 2) }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="small text-secondary">الربح التقريبي</div>
                                        <div class="fs-5 fw-bold {{ ($salesStats['month']['profit'] ?? 0) >= 0 ? 'text-info' : 'text-warning' }}">
                                            ${{ number_format($salesStats['month']['profit'] ?? 0, 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- أداء المنتجات وتنبيهات المخزون --}}
    <div class="row g-3 mt-4">
        <div class="col-lg-6">
            <div class="glass p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2 class="h6 fw-semibold mb-0">أكثر المنتجات مبيعاً</h2>
                    <span class="text-secondary small">Top {{ $topSellingProducts->count() }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-dark table-sm align-middle mb-0">
                        <thead class="table-secondary text-dark">
                            <tr>
                                <th>المنتج</th>
                                <th>الشركة</th>
                                <th>المبيعات</th>
                                <th>المخزون</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topSellingProducts as $product)
                                <tr>
                                    <td>{{ $product->translated_name }}</td>
                                    <td>{{ $product->company->name ?? '-' }}</td>
                                    <td class="text-info fw-semibold">{{ $product->sales_count ?? 0 }}</td>
                                    <td>{{ $product->stock }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-secondary">لا توجد بيانات بعد.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <hr class="border-secondary my-3">

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2 class="h6 fw-semibold mb-0">المنتجات الأقل حركة</h2>
                    <span class="text-secondary small">Bottom {{ $lowMovementProducts->count() }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-dark table-sm align-middle mb-0">
                        <thead class="table-secondary text-dark">
                            <tr>
                                <th>المنتج</th>
                                <th>الشركة</th>
                                <th>المبيعات</th>
                                <th>المخزون</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lowMovementProducts as $product)
                                <tr>
                                    <td>{{ $product->translated_name }}</td>
                                    <td>{{ $product->company->name ?? '-' }}</td>
                                    <td class="text-warning fw-semibold">{{ $product->sales_count ?? 0 }}</td>
                                    <td>{{ $product->stock }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-secondary">لا توجد بيانات بعد.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-3" id="low-stock-section">
            <div class="glass p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2 class="h6 fw-semibold mb-0">تنبيهات المخزون</h2>
                    <span class="badge bg-warning text-dark small">قربت تخلص</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-dark table-sm align-middle mb-0">
                        <thead class="table-secondary text-dark">
                            <tr>
                                <th>المنتج</th>
                                <th>المخزون</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lowStockProducts as $product)
                                <tr>
                                    <td class="small">
                                        {{ $product->translated_name }}
                                        <div class="small text-secondary">
                                            {{ $product->company->name ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="fw-bold {{ $product->stock <= 0 ? 'text-danger' : 'text-warning' }}">
                                        {{ $product->stock }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-secondary small">لا توجد منتجات منخفضة المخزون.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="glass p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2 class="h6 fw-semibold mb-0">أفضل العملاء</h2>
                    <span class="text-secondary small">حسب إجمالي الإنفاق</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-dark table-sm align-middle mb-0">
                        <thead class="table-secondary text-dark">
                            <tr>
                                <th>العميل</th>
                                <th>الطلبات</th>
                                <th>إجمالي الدفع</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topCustomers as $customer)
                                <tr>
                                    <td class="small">
                                        {{ $customer->name }}
                                        <div class="small text-secondary">{{ $customer->phone ?? $customer->email }}</div>
                                    </td>
                                    <td class="text-info fw-semibold">{{ $customer->orders_count }}</td>
                                    <td class="text-success fw-semibold">
                                        ${{ number_format($customer->total_spent, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-secondary small">لا توجد بيانات عملاء بعد.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- سكربت الرسوم البيانية للوحة التحكم --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Chart === 'undefined') {
            return;
        }

        // بيانات حالات الطلبات (مخطط دائري)
        const ordersStatusData = {
            labels: ['معلقة', 'مؤكدة', 'ملغاة'],
            datasets: [{
                data: [
                    {{ $salesStats['status_counts']['pending'] ?? 0 }},
                    {{ $salesStats['status_counts']['confirmed'] ?? 0 }},
                    {{ $salesStats['status_counts']['cancelled'] ?? 0 }},
                ],
                backgroundColor: ['#6c757d', '#0db777', '#dc3545'],
                borderColor: '#0b0d11',
                borderWidth: 1,
            }]
        };

        const pieCtx = document.getElementById('ordersStatusPie');
        if (pieCtx) {
            new Chart(pieCtx, {
                type: 'doughnut',
                data: ordersStatusData,
                options: {
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                color: '#eaf6ef',
                                font: { family: 'Cairo' },
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    return label + ': ' + value;
                                }
                            }
                        }
                    },
                    cutout: '55%',
                }
            });
        }

        // مخطط هرمي / قمع المبيعات حسب الفترة
        const salesFunnelData = {
            labels: ['هذا الشهر', 'هذا الأسبوع', 'اليوم'],
            datasets: [{
                data: [
                    {{ $salesStats['month']['sales'] ?? 0 }},
                    {{ $salesStats['week']['sales'] ?? 0 }},
                    {{ $salesStats['today']['sales'] ?? 0 }},
                ],
                backgroundColor: ['rgba(13, 183, 119, 0.25)', 'rgba(13, 183, 119, 0.55)', 'rgba(13, 183, 119, 0.95)'],
                borderColor: 'rgba(13, 183, 119, 1)',
                borderWidth: 1,
                borderRadius: 8,
                barPercentage: 0.8,
                categoryPercentage: 0.7,
            }]
        };

        const funnelCtx = document.getElementById('salesFunnelChart');
        if (funnelCtx) {
            new Chart(funnelCtx, {
                type: 'bar',
                data: salesFunnelData,
                options: {
                    indexAxis: 'y', // أفقي ليعطي إحساس هرمي
                    scales: {
                        x: {
                            ticks: {
                                color: '#eaf6ef'
                            },
                            grid: {
                                color: 'rgba(255,255,255,0.08)'
                            }
                        },
                        y: {
                            ticks: {
                                color: '#eaf6ef'
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const value = context.raw || 0;
                                    return 'المبيعات: $' + Number(value).toFixed(2);
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>

