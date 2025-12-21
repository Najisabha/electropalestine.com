<section class="py-5 text-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 fw-bold mb-0">{{ __('common.shopping_cart') }}</h1>
            @if(!empty($cartItems))
                <form method="POST" action="{{ route('cart.clear') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('common.clear_cart_confirm') }}');">
                        <i class="bi bi-trash"></i>
                        {{ __('common.clear_cart') }}
                    </button>
                </form>
            @endif
        </div>

        @if (session('status'))
            <div class="alert alert-success small py-2 mb-3">{{ session('status') }}</div>
        @endif

        @if(empty($cartItems))
            <div class="glass rounded-4 p-5 text-center">
                <i class="bi bi-cart-x display-1 text-secondary mb-3"></i>
                <h3 class="h5 text-secondary mb-2">{{ __('common.empty_cart') }}</h3>
                <p class="text-secondary small mb-4">{{ __('common.empty_cart_message') }}</p>
                <a href="{{ route('home') }}" class="btn btn-main">
                    <i class="bi bi-arrow-left"></i>
                    {{ __('common.back_to_store') }}
                </a>
            </div>
        @else
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="glass rounded-4 p-4">
                        <h2 class="h6 fw-semibold mb-3">{{ __('common.products_in_cart') }}</h2>
                        <div class="table-responsive">
                            <table class="table table-dark table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 15%">{{ __('common.image') }}</th>
                                        <th style="width: 35%">{{ __('common.product') }}</th>
                                        <th style="width: 15%">{{ __('common.price') }}</th>
                                        <th style="width: 20%">{{ __('common.quantity') }}</th>
                                        <th style="width: 10%">{{ __('common.total') }}</th>
                                        <th style="width: 5%">{{ __('common.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cartItems as $productId => $item)
                                        @php($product = $item['product'])
                                        <tr>
                                            <td>
                                                @if(!empty($product->image))
                                                    <img src="{{ asset('storage/'.$product->image) }}"
                                                         class="rounded"
                                                         style="width: 60px; height: 60px; object-fit: cover;"
                                                         alt="{{ $product->name }}">
                                                @else
                                                    <div class="bg-black rounded d-flex align-items-center justify-content-center"
                                                         style="width: 60px; height: 60px;">
                                                        <span class="text-secondary small">{{ __('common.no_image') }}</span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-white">
                                                    <strong>{{ $product->name }}</strong>
                                                </a>
                                                <div class="text-secondary small">
                                                    {{ $product->category->name ?? __('common.no_category') }} • {{ $product->company->name ?? __('common.unknown_company') }}
                                                </div>
                                            </td>
                                            <td class="text-success fw-semibold">
                                                ${{ number_format($product->price, 2) }}
                                            </td>
                                            <td>
                                                <form method="POST" action="{{ route('cart.update', $product) }}" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="number"
                                                           name="quantity"
                                                           value="{{ $item['quantity'] }}"
                                                           min="1"
                                                           class="form-control form-control-sm bg-dark text-light"
                                                           style="width: 80px; display: inline-block;"
                                                           onchange="this.form.submit()">
                                                </form>
                                            </td>
                                            <td class="text-success fw-bold">
                                                ${{ number_format($item['subtotal'], 2) }}
                                            </td>
                                            <td>
                                                <form method="POST" action="{{ route('cart.remove', $product) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('common.delete_product') }}');">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="glass rounded-4 p-4 sticky-top" style="top: 100px;">
                        <h2 class="h6 fw-semibold mb-3">{{ __('common.order_summary') }}</h2>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">{{ __('common.product_count') }}:</span>
                            <strong class="text-white">{{ count($cartItems) }}</strong>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">{{ __('common.total_quantity') }}:</span>
                            <strong class="text-white">{{ array_sum(array_column($cartItems, 'quantity')) }}</strong>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 mt-3 border-top border-secondary-subtle mb-3">
                            <span class="h6 mb-0">{{ __('common.grand_total') }}:</span>
                            <span class="h5 fw-bold text-success mb-0">${{ number_format($total, 2) }}</span>
                        </div>

                        <a href="{{ route('store.cart.checkout') }}" class="btn btn-main w-100 mb-2">
                            <i class="bi bi-credit-card"></i>
                            {{ __('common.confirm_order') }}
                        </a>

                        <a href="{{ route('home') }}" class="btn btn-outline-main w-100">
                            <i class="bi bi-arrow-left"></i>
                            {{ __('common.continue_shopping') }}
                        </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
