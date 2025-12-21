<section class="container py-4 text-light">
    <div class="glass p-4 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 fw-bold mb-0">العملاء</h1>
            <span class="badge bg-secondary text-dark small">
                عدد العملاء المعروضين: {{ $customers->count() }}
            </span>
        </div>
        <p class="text-secondary small mb-3">
            يعرض هذا الجدول عدد الطلبات، إجمالي ما دفعه العميل، وآخر طلب تم تسجيله له.
        </p>

        <form method="GET" action="{{ route('admin.customers') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-secondary mb-1">نوع الفلتر</label>
                <select name="filter" class="form-select auth-input bg-dark text-light">
                    <option value="">الأحدث نشاطاً</option>
                    <option value="top_spenders" @selected($filter === 'top_spenders')>أكثر العملاء إنفاقاً</option>
                    <option value="top_orders" @selected($filter === 'top_orders')>أكثر العملاء طلباً</option>
                    <option value="inactive" @selected($filter === 'inactive')>العملاء غير النشطين</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-secondary mb-1">العدد (Top N)</label>
                <input type="number" name="limit" value="{{ $limit }}" min="1" max="200" class="form-control auth-input">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-secondary mb-1">عدد الأيام لاعتبار العميل غير نشط</label>
                <input type="number" name="days" value="{{ $days }}" min="1" max="365" class="form-control auth-input">
            </div>
            <div class="col-md-4 d-flex gap-2 mt-3">
                <button class="btn btn-main btn-sm flex-fill">تطبيق الفلاتر</button>
                <a href="{{ route('admin.customers') }}" class="btn btn-outline-secondary btn-sm flex-fill">إعادة الضبط</a>
            </div>
        </form>
    </div>

    <div class="glass p-4">
        <div class="table-responsive">
            <table class="table table-dark table-sm align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم الكامل</th>
                        <th>البريد الإلكتروني</th>
                        <th>رقم الجوال</th>
                        <th>عدد الطلبات</th>
                        <th>إجمالي ما دفع</th>
                        <th>آخر طلب</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>{{ $customer->id }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td><span dir="ltr">{{ $customer->phone }}</span></td>
                            <td class="text-info fw-semibold">{{ $customer->orders_count }}</td>
                            <td class="text-success fw-semibold">
                                ${{ number_format($customer->total_spent, 2) }}
                            </td>
                            <td class="small">
                                @if($customer->last_order_at)
                                    {{ \Illuminate\Support\Carbon::parse($customer->last_order_at)->format('Y-m-d') }}
                                @else
                                    <span class="text-secondary">لا يوجد</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.orders', ['user_id' => $customer->id]) }}" class="btn btn-sm btn-outline-main">
                                    عرض طلباته
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-secondary py-3">
                                لا توجد بيانات عملاء وفق الفلاتر الحالية.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

