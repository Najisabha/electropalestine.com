<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0" style="background: linear-gradient(135deg, rgba(26, 29, 36, 0.95), rgba(21, 24, 32, 0.9)); border: 1px solid rgba(255, 255, 255, 0.1) !important;">
                <div class="card-body p-5">
                    <h1 class="text-center mb-4 text-white" style="color: #0db777 !important;">
                        <i class="bi bi-truck me-2"></i>
                        {{ __('common.track_order') }}
                    </h1>
                    
                    <div class="text-center mb-4">
                        <p class="text-white-50 lead">
                            {{ __('common.track_order_description') ?? 'أدخل رقم الطلب لتتبع حالة طلبك' }}
                        </p>
                    </div>

                    @auth
                        <div class="alert alert-info border-0 mb-4" style="background: rgba(13, 183, 119, 0.1); border: 1px solid rgba(13, 183, 119, 0.3) !important; color: #0db777;">
                            <i class="bi bi-info-circle me-2"></i>
                            {{ __('common.view_all_orders') ?? 'يمكنك أيضاً عرض جميع طلباتك من' }} 
                            <a href="{{ route('store.my-orders') }}" class="text-decoration-none fw-bold" style="color: #0db777;">
                                {{ __('common.my_orders') }}
                            </a>
                        </div>
                    @endauth

                    <form method="GET" action="{{ route('store.track-order') }}" class="mb-4">
                        <div class="input-group input-group-lg">
                            <input 
                                type="text" 
                                name="order_id" 
                                class="form-control form-control-lg" 
                                placeholder="{{ __('common.enter_order_number') ?? 'أدخل رقم الطلب' }}"
                                value="{{ request('order_id') }}"
                                style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: #fff;"
                            >
                            <button class="btn btn-lg" type="submit" style="background: linear-gradient(135deg, #0db777, #0aa066); color: white; border: none;">
                                <i class="bi bi-search me-2"></i>
                                {{ __('common.track') ?? 'تتبع' }}
                            </button>
                        </div>
                    </form>

                    @if(request('order_id'))
                        @php
                            $orderId = request('order_id');
                            $order = \App\Models\Order::where('id', $orderId)
                                ->when(auth()->check(), function($q) {
                                    $q->where('user_id', auth()->id());
                                })
                                ->first();
                        @endphp

                        @if($order)
                            <div class="card mt-4 border-0" style="background: rgba(13, 183, 119, 0.1); border: 1px solid rgba(13, 183, 119, 0.3) !important;">
                                <div class="card-body">
                                    <h5 class="card-title text-white mb-3">
                                        <i class="bi bi-receipt me-2" style="color: #0db777;"></i>
                                        {{ __('common.order_details') ?? 'تفاصيل الطلب' }} #{{ $order->id }}
                                    </h5>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <p class="text-white-50 mb-1">{{ __('common.order_status') ?? 'حالة الطلب' }}:</p>
                                            <span class="badge 
                                                @if($order->status === 'confirmed') bg-success
                                                @elseif($order->status === 'pending') bg-warning
                                                @elseif($order->status === 'cancelled') bg-danger
                                                @else bg-secondary
                                                @endif
                                                fs-6 px-3 py-2">
                                                {{ __('common.order_status_' . $order->status) ?? $order->status }}
                                            </span>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="text-white-50 mb-1">{{ __('common.order_date') ?? 'تاريخ الطلب' }}:</p>
                                            <p class="text-white mb-0">{{ $order->created_at->format('Y-m-d H:i') }}</p>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <p class="text-white-50 mb-1">{{ __('common.product_name') ?? 'اسم المنتج' }}:</p>
                                            <p class="text-white mb-0">{{ $order->product_name }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="text-white-50 mb-1">{{ __('common.total_amount') ?? 'المبلغ الإجمالي' }}:</p>
                                            <p class="text-white mb-0 fw-bold">{{ number_format($order->total, 2) }} ₪</p>
                                        </div>
                                    </div>

                                    @if($order->shipping_address)
                                        <div class="mb-3">
                                            <p class="text-white-50 mb-1">{{ __('common.shipping_address') ?? 'عنوان الشحن' }}:</p>
                                            <p class="text-white mb-0">{{ $order->shipping_address }}</p>
                                        </div>
                                    @endif

                                    @auth
                                        <div class="mt-3">
                                            <a href="{{ route('store.order.invoice', $order) }}" class="btn btn-sm" style="background: linear-gradient(135deg, #0db777, #0aa066); color: white; border: none;">
                                                <i class="bi bi-download me-2"></i>
                                                {{ __('common.download_invoice') ?? 'تحميل الفاتورة' }}
                                            </a>
                                        </div>
                                    @endauth
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning border-0 mt-4" style="background: rgba(245, 209, 12, 0.1); border: 1px solid rgba(245, 209, 12, 0.3) !important; color: #f5d10c;">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                {{ __('common.order_not_found') ?? 'لم يتم العثور على طلب بهذا الرقم. يرجى التحقق من الرقم والمحاولة مرة أخرى.' }}
                            </div>
                        @endif
                    @endif

                    <div class="mt-5 text-center">
                        <h5 class="text-white mb-3">{{ __('common.need_help') ?? 'تحتاج مساعدة؟' }}</h5>
                        <p class="text-white-50 mb-3">
                            {{ __('common.contact_support') ?? 'إذا كان لديك أي استفسارات حول طلبك، لا تتردد في التواصل معنا' }}
                        </p>
                        <a href="{{ route('store.contact') }}" class="btn" style="background: linear-gradient(135deg, #0db777, #0aa066); color: white; border: none;">
                            <i class="bi bi-envelope me-2"></i>
                            {{ __('common.contact_us') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
