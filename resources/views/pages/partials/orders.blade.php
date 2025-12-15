<section class="container py-4 text-light">
    <div class="glass p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 fw-bold mb-0">إظهار الطلبات</h1>
            <span class="badge bg-info text-dark">{{ $orders->total() }} طلبية</span>
        </div>
        @if (session('status'))
            <div class="alert alert-success small">{{ session('status') }}</div>
        @endif
        <p class="text-secondary small mb-3">يمكنك تعديل الكمية والسعر وتأكيد أو حذف الطلبية.</p>
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>العميل</th>
                        <th>اسم المنتج</th>
                        <th>الكمية</th>
                        <th>سعر الوحدة</th>
                        <th>الإجمالي</th>
                        <th>الحالة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->user?->name ?? 'غير محدد' }}</td>
                            <td>{{ $order->product_name }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="d-flex gap-1 align-items-center">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="quantity" value="{{ $order->quantity }}" min="1" class="form-control form-control-sm bg-dark text-light" style="width:80px;">
                            </td>
                            <td>
                                    <input type="number" name="unit_price" step="0.01" value="{{ $order->unit_price }}" min="0" class="form-control form-control-sm bg-dark text-light" style="width:100px;">
                            </td>
                            <td>${{ number_format($order->total, 2) }}</td>
                            <td>
                                    <select name="status" class="form-select form-select-sm bg-dark text-light" style="width:130px;">
                                        <option value="pending" @selected($order->status === 'pending')>قيد المعالجة</option>
                                        <option value="confirmed" @selected($order->status === 'confirmed')>مؤكد</option>
                                        <option value="cancelled" @selected($order->status === 'cancelled')>ملغي</option>
                                    </select>
                            </td>
                            <td>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <button class="btn btn-sm btn-main">حفظ التعديلات</button>
                                    </div>
                                </form>
                                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger mt-1" onclick="return confirm('هل أنت متأكد من حذف هذه الطلبية؟')">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-secondary">لا توجد طلبات حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $orders->links() }}
        </div>
    </div>
</section>

