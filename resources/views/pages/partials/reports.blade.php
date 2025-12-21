@php($activeTab = request('tab', 'date'))

<section class="container py-4 text-light">
    <div class="glass p-4">
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab === 'date' ? 'active' : '' }}" id="sales-date-tab" data-bs-toggle="tab" data-bs-target="#sales-date-pane" type="button" role="tab">
                    المبيعات حسب التاريخ
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab === 'category' ? 'active' : '' }}" id="sales-category-tab" data-bs-toggle="tab" data-bs-target="#sales-category-pane" type="button" role="tab">
                    المبيعات حسب الصنف / الشركة
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab === 'profit' ? 'active' : '' }}" id="profit-period-tab" data-bs-toggle="tab" data-bs-target="#profit-period-pane" type="button" role="tab">
                    الأرباح حسب الفترة
                </button>
            </li>
        </ul>

        <div class="tab-content">
            {{-- تبويب 1: المبيعات حسب التاريخ --}}
            <div class="tab-pane fade {{ $activeTab === 'date' ? 'show active' : '' }}" id="sales-date-pane" role="tabpanel" aria-labelledby="sales-date-tab">
                <form method="GET" action="{{ route('admin.reports') }}" class="row g-3 align-items-end mb-3">
                    <div class="col-md-4">
                        <label class="form-label small text-secondary mb-1">من تاريخ</label>
                        <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="form-control auth-input">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-secondary mb-1">إلى تاريخ</label>
                        <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="form-control auth-input">
                    </div>
                    <div class="col-md-4 d-flex gap-2 mt-3">
                        <button class="btn btn-main btn-sm flex-fill">تحديث التقرير</button>
                        <a href="{{ route('admin.reports') }}" class="btn btn-outline-secondary btn-sm flex-fill">اليوم / هذا الشهر</a>
                    </div>
                </form>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-dark border border-secondary-subtle h-100">
                            <p class="small text-secondary mb-1">عدد الطلبات</p>
                            <div class="fs-4 fw-bold text-info">{{ $salesByDate['orders_count'] }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-dark border border-secondary-subtle h-100">
                            <p class="small text-secondary mb-1">إجمالي المبيعات</p>
                            <div class="fs-4 fw-bold text-success">
                                ${{ number_format($salesByDate['sales_total'], 2) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-dark border border-secondary-subtle h-100">
                            <p class="small text-secondary mb-1">إجمالي الربح التقريبي</p>
                            <div class="fs-4 fw-bold {{ $salesByDate['profit_total'] >= 0 ? 'text-info' : 'text-warning' }}">
                                ${{ number_format($salesByDate['profit_total'], 2) }}
                            </div>
                            <div class="small text-secondary mt-1">
                                يعتمد على تكلفة المنتج الحالية وليس تكلفة تاريخية.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.reports.sales-by-date.excel', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}"
                       class="btn btn-sm btn-success">
                        تصدير إلى Excel
                    </a>
                    <a href="{{ route('admin.reports.sales-by-date.pdf', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}"
                       class="btn btn-sm btn-outline-light">
                        تصدير إلى PDF
                    </a>
                </div>
            </div>

            {{-- تبويب 2: المبيعات حسب الصنف / الشركة --}}
            <div class="tab-pane fade {{ $activeTab === 'category' ? 'show active' : '' }}" id="sales-category-pane" role="tabpanel" aria-labelledby="sales-category-tab">
                <form method="GET" action="{{ route('admin.reports') }}" class="row g-3 align-items-end mb-3">
                    <input type="hidden" name="tab" value="category">
                    <div class="col-md-4">
                        <label class="form-label small text-secondary mb-1">الصنف</label>
                        <select name="category_id" class="form-select auth-input bg-dark text-light">
                            <option value="">كل الأصناف</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected($categoryId == $category->id)>
                                    {{ $category->translated_name ?? $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-secondary mb-1">الشركة</label>
                        <select name="company_id" class="form-select auth-input bg-dark text-light">
                            <option value="">كل الشركات</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" @selected($companyId == $company->id)>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex gap-2 mt-3">
                        <button class="btn btn-main btn-sm flex-fill">تحديث التقرير</button>
                        <a href="{{ route('admin.reports', ['tab' => 'category']) }}" class="btn btn-outline-secondary btn-sm flex-fill">
                            إعادة الضبط
                        </a>
                    </div>
                </form>

                <div class="table-responsive mb-3">
                    <table class="table table-dark table-sm align-middle">
                        <thead>
                            <tr>
                                <th>الصنف</th>
                                <th>الشركة</th>
                                <th>عدد الطلبات</th>
                                <th>إجمالي المبيعات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($categorySales && $categorySales->count())
                                @foreach($categorySales as $row)
                                    <tr>
                                        <td>{{ $row->category_name }}</td>
                                        <td>{{ $row->company_name }}</td>
                                        <td class="text-info fw-semibold">{{ $row->orders_count }}</td>
                                        <td class="text-success fw-semibold">
                                            ${{ number_format($row->sales_total, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center text-secondary py-3">
                                        لا توجد بيانات لعرضها. اختر صنفاً أو شركة ثم اضغط "تحديث التقرير".
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <a href="{{ route('admin.reports.sales-by-category.excel', ['category_id' => $categoryId, 'company_id' => $companyId]) }}"
                   class="btn btn-sm btn-success">
                    تصدير إلى Excel
                </a>
            </div>

            {{-- تبويب 3: الأرباح حسب الفترة (يومياً ضمن النطاق) --}}
            <div class="tab-pane fade {{ $activeTab === 'profit' ? 'show active' : '' }}" id="profit-period-pane" role="tabpanel" aria-labelledby="profit-period-tab">
                <form method="GET" action="{{ route('admin.reports') }}" class="row g-3 align-items-end mb-3">
                    <input type="hidden" name="tab" value="profit">
                    <div class="col-md-4">
                        <label class="form-label small text-secondary mb-1">من تاريخ</label>
                        <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="form-control auth-input">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-secondary mb-1">إلى تاريخ</label>
                        <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="form-control auth-input">
                    </div>
                    <div class="col-md-4 d-flex gap-2 mt-3">
                        <button class="btn btn-main btn-sm flex-fill">تحديث التقرير</button>
                        <a href="{{ route('admin.reports', ['tab' => 'profit']) }}" class="btn btn-outline-secondary btn-sm flex-fill">
                            إعادة الضبط
                        </a>
                    </div>
                </form>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-dark border border-secondary-subtle h-100">
                            <p class="small text-secondary mb-1">إجمالي الربح التقريبي</p>
                            <div class="fs-4 fw-bold {{ ($profitSummary['total_profit'] ?? 0) >= 0 ? 'text-info' : 'text-warning' }}">
                                ${{ number_format($profitSummary['total_profit'] ?? 0, 2) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-dark border border-secondary-subtle h-100">
                            <p class="small text-secondary mb-1">أفضل فترة</p>
                            @if(!empty($profitSummary['best_period']))
                                <div class="fw-semibold">
                                    {{ $profitSummary['best_period']['label'] }}
                                </div>
                                <div class="small text-success">
                                    ربح: ${{ number_format($profitSummary['best_period']['profit_total'], 2) }}
                                </div>
                            @else
                                <div class="small text-secondary">لا توجد بيانات كافية.</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-dark border border-secondary-subtle h-100">
                            <p class="small text-secondary mb-1">أضعف فترة</p>
                            @if(!empty($profitSummary['worst_period']))
                                <div class="fw-semibold">
                                    {{ $profitSummary['worst_period']['label'] }}
                                </div>
                                <div class="small text-danger">
                                    ربح: ${{ number_format($profitSummary['worst_period']['profit_total'], 2) }}
                                </div>
                            @else
                                <div class="small text-secondary">لا توجد بيانات كافية.</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="table-responsive mb-3">
                    <table class="table table-dark table-sm align-middle">
                        <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>عدد الطلبات</th>
                            <th>إجمالي المبيعات</th>
                            <th>الربح التقريبي</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($profitPeriods as $row)
                            <tr>
                                <td>{{ $row['label'] }}</td>
                                <td class="text-info fw-semibold">{{ $row['orders_count'] }}</td>
                                <td class="text-success fw-semibold">
                                    ${{ number_format($row['sales_total'], 2) }}
                                </td>
                                <td class="{{ $row['profit_total'] >= 0 ? 'text-info' : 'text-warning' }} fw-semibold">
                                    ${{ number_format($row['profit_total'], 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-secondary py-3">
                                    لا توجد بيانات لعرضها في هذه الفترة.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.reports.profit-by-period.excel', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}"
                       class="btn btn-sm btn-success">
                        تصدير إلى Excel
                    </a>
                    <a href="{{ route('admin.reports.profit-by-period.pdf', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}"
                       class="btn btn-sm btn-outline-light">
                        تصدير إلى PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

