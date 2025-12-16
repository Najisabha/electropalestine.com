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
                                <th>{{ __('common.quantity') }}</th>
                                <th>{{ __('common.price') }}</th>
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
                                        <strong>{{ $order->product_name }}</strong>
                                    </td>
                                    <td>{{ $order->quantity }}</td>
                                    <td class="text-success">${{ number_format($order->unit_price, 2) }}</td>
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
                                    <td>
                                        <a href="{{ route('store.order.invoice', $order) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-file-earmark-pdf"></i> {{ __('common.download_invoice') }}
                                        </a>
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
