<section class="py-5 text-light">
    <div class="container">
        <h1 class="h4 fw-bold mb-4">{{ __('common.my_orders_title') }}</h1>

        @if($orders->isEmpty())
            <div class="glass rounded-4 p-5 text-center">
                <i class="bi bi-bag-x display-1 text-secondary mb-3"></i>
                <h3 class="h5 text-secondary mb-2">{{ __('common.no_orders') }}</h3>
                <p class="text-secondary small mb-4">{{ __('common.no_orders_message') }}</p>
                <a href="{{ route('home') }}" class="btn btn-main">
                    <i class="bi bi-arrow-left"></i>
                    {{ __('common.back_to_store') }}
                </a>
            </div>
        @else
            <div class="glass rounded-4 p-4">
                <div class="table-responsive">
                    <table class="table table-dark table-sm align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('common.order_number') }}</th>
                                <th>{{ __('common.product') }}</th>
                                <th>{{ __('common.total') }}</th>
                                <th>{{ __('common.order_status') }}</th>
                                <th>{{ __('common.order_date_label') }}</th>
                                <th>{{ __('common.my_invoice') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <strong class="text-primary">#{{ $order->id }}</strong>
                                    </td>
                                    <td>
                                        @php($items = is_array($order->items ?? null) ? $order->items : [])
                                        @if(!empty($items))
                                            @php($first = $items[0])
                                            <strong>{{ $first['name'] ?? $order->product_name }}</strong>
                                            @if(count($items) > 1)
                                                <div class="text-secondary small">
                                                    + {{ count($items) - 1 }} {{ __('common.additional_items') ?? 'منتجات أخرى' }}
                                                </div>
                                            @endif
                                        @else
                                            <strong>{{ $order->product_name }}</strong>
                                        @endif
                                    </td>
                                    <td class="text-success fw-bold">${{ number_format($order->total, 2) }}</td>
                                    <td>
                                        @if($order->status === 'pending')
                                            <span class="badge bg-warning text-dark">{{ __('common.pending') }}</span>
                                        @elseif($order->status === 'confirmed')
                                            <span class="badge bg-success">{{ __('common.confirmed') }}</span>
                                        @elseif($order->status === 'cancelled')
                                            <span class="badge bg-danger">{{ __('common.cancelled') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $order->status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-secondary small">
                                        {{ $order->created_at->format('Y/m/d H:i') }}
                                    </td>
                                    <td class="d-flex flex-wrap gap-2">
                                        <a href="{{ route('store.order.invoice', $order) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-file-earmark-pdf"></i> {{ __('common.download_invoice') }}
                                        </a>
                                        @if($order->status === 'confirmed')
                                            <a href="{{ route('store.order.review', $order) }}" class="btn btn-sm btn-warning">
                                                {{ $order->review ? 'تعديل التقييم' : 'تقييم الطلبية' }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($orders->hasPages())
                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</section>
