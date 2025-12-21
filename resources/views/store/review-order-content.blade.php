<section class="py-5 text-light">
    <div class="container">
        <h1 class="h4 fw-bold mb-4">تقييم الطلبية رقم #{{ $order->id }}</h1>

        <div class="mb-4">
            <div class="glass p-3 mb-3">
                <h5 class="fw-semibold mb-2">تفاصيل الطلبية</h5>
                <p class="mb-1"><strong>المستخدم:</strong> {{ $order->user->name }} ({{ $order->user->email }})</p>
                <p class="mb-1"><strong>إجمالي الطلب:</strong> ${{ number_format($order->total, 2) }}</p>
                <p class="mb-1"><strong>تاريخ الطلب:</strong> {{ $order->created_at->format('Y/m/d H:i') }}</p>
            </div>

            <div class="glass p-3">
                <h5 class="fw-semibold mb-3">المنتجات داخل الطلبية</h5>
                @php($items = is_array($order->items ?? null) ? $order->items : [])
                @if(!empty($items))
                    <ul class="list-group list-group-flush">
                        @foreach($items as $item)
                            <li class="list-group-item bg-transparent text-light d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $item['name'] ?? 'منتج' }}</strong>
                                    <span class="text-secondary small d-block">
                                        الكمية: {{ $item['quantity'] ?? 1 }} • السعر: ${{ number_format($item['unit_price'] ?? 0, 2) }}
                                    </span>
                                </div>
                                <span class="text-success fw-bold">${{ number_format($item['total'] ?? 0, 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-secondary">لا توجد عناصر مسجّلة في هذه الطلبية.</p>
                @endif
            </div>
        </div>

        <div class="glass p-4">
            <h5 class="fw-semibold mb-3">{{ $order->review ? 'تعديل التقييم' : 'إضافة تقييم للطلبية' }}</h5>

            <form method="POST" action="{{ route('store.order.review.submit', $order) }}">
                @csrf

                <div class="mb-3">
                    <label for="rating" class="form-label">التقييم (عدد النجوم)</label>
                    <select id="rating" name="rating" class="form-select bg-dark text-light" required>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ (old('rating', optional($order->review)->rating) == $i) ? 'selected' : '' }}>
                                {{ $i }} ★
                            </option>
                        @endfor
                    </select>
                    @error('rating')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="comment" class="form-label">تعليقك على الطلبية (اختياري)</label>
                    <textarea
                        id="comment"
                        name="comment"
                        class="form-control bg-dark text-light"
                        rows="4"
                        placeholder="اكتب رأيك في الخدمة أو المنتج...">{{ old('comment', optional($order->review)->comment) }}</textarea>
                    @error('comment')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-main">
                    حفظ التقييم
                </button>
                <a href="{{ route('store.my-orders') }}" class="btn btn-outline-secondary ms-2">رجوع إلى طلباتي</a>
            </form>
        </div>
    </div>
</section>

