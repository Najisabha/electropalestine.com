@php
    $ratingAverage = $product->rating_average ?? 0;
    $ratingCount = $product->rating_count ?? 0;
    $category = $product->category;
    $types = $category?->types ?? collect();
@endphp

<section class="py-5 text-light">
    <div class="container">
        <div class="row g-4 align-items-start">
            {{-- صورة / كرت المنتج --}}
            <div class="col-lg-6">
                <div class="glass rounded-4 overflow-hidden h-100">
                    <div class="position-relative">
                        @if(!empty($product->image))
                            <img src="{{ asset('storage/'.$product->image) }}"
                                 class="w-100 bg-black"
                                 style="height: 260px; object-fit: contain;"
                                 alt="{{ $product->name }}">
                        @else
                            <div class="w-100 d-flex align-items-center justify-content-center bg-black text-secondary small"
                                 style="height: 320px;">
                                {{ __('common.no_image_product') }}
                            </div>
                        @endif
                        <span class="badge bg-success position-absolute top-0 start-0 m-3 small">
                            {{ $product->sales_count ?? 0 }} {{ __('common.sold') }}
                        </span>
                    </div>
                    <div class="p-4">
                        <p class="text-secondary small mb-1">
                            {{ __('common.product_from') }} {{ $product->company->name ?? __('common.unknown_company') }}
                        </p>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            @if($category)
                                <span class="badge bg-success text-dark">
                                    {{ $category->name }} • {{ __('common.main_category') }}
                                </span>
                            @endif
                            @if($product->type)
                                <span class="badge bg-warning text-dark">
                                    {{ $product->type->name }} • {{ __('common.type') }}
                                </span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($ratingAverage >= $i)
                                        <i class="bi bi-star-fill"></i>
                                    @elseif($ratingAverage >= $i - 0.5)
                                        <i class="bi bi-star-half"></i>
                                    @else
                                        <i class="bi bi-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="small text-light fw-semibold">
                                {{ number_format($ratingAverage, 1) }} / 5
                            </span>
                            <span class="small text-secondary">
                                ({{ $ratingCount }} {{ __('common.rating_label') }})
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- معلومات المنتج + الأزرار --}}
            <div class="col-lg-6">
                <h1 class="h3 fw-bold text-white mb-2">{{ $product->name }}</h1>

                <div class="fs-2 fw-black text-success mb-2">
                    ${{ number_format($product->price, 2) }}
                </div>
                @if(!empty($product->cost_price))
                    <p class="text-secondary small mb-1">
                        {{ __('common.approximate_cost_price') }}: ${{ number_format($product->cost_price, 2) }}
                    </p>
                @endif

                <div class="text-secondary small mb-2">
                    {{ __('common.available_stock') }}: <span class="text-success fw-semibold">{{ $product->stock }}</span>
                </div>

                <p class="text-secondary mb-3">
                    {{ $product->description ?? __('common.product_description_placeholder') }}
                </p>

                <div class="d-flex flex-wrap gap-2 mb-4">
                    <form method="POST" action="{{ route('cart.add', $product) }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-main px-4">
                            <i class="bi bi-cart-plus"></i>
                            {{ __('common.add_to_cart_button') }}
                        </button>
                    </form>
                    <a href="{{ route('store.checkout', ['product' => $product->id, 'quantity' => 1]) }}" class="btn btn-success px-4">
                        <i class="bi bi-bag-check"></i>
                        {{ __('common.buy_now') }}
                    </a>
                    <button class="btn btn-outline-main px-4">{{ __('common.contact_about_product') }}</button>
                </div>

                {{-- قسم تقييمات العملاء --}}
                <div class="glass rounded-4 p-3">
                    <h2 class="h6 fw-semibold mb-2">{{ __('common.customer_ratings') }}</h2>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="display-6 fw-bold text-warning mb-0">
                            {{ number_format($ratingAverage, 1) }}
                        </div>
                        <div>
                            <div class="text-warning mb-1">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($ratingAverage >= $i)
                                        <i class="bi bi-star-fill"></i>
                                    @elseif($ratingAverage >= $i - 0.5)
                                        <i class="bi bi-star-half"></i>
                                    @else
                                        <i class="bi bi-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <div class="small text-secondary">
                                {{ __('common.based_on_ratings') }} {{ $ratingCount }} {{ __('common.rating_label') }}
                            </div>
                        </div>
                    </div>

                    <div class="border-top border-secondary-subtle pt-2">
                        <p class="small text-secondary mb-1">
                            {{ __('common.no_detailed_comments') }}
                        </p>
                        <p class="small text-secondary mb-0">
                            {{ __('common.add_comment_system_later') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- شريط ذات صلة: منتجات من نفس الأصناف/الأنواع --}}
        <div class="mt-5">
            <h2 class="h5 fw-semibold mb-3">{{ __('common.related_products') }}</h2>

            <div class="strip-scroll mb-3">
                @forelse ($related as $item)
                    <a href="{{ route('products.show', $item) }}" class="strip-card text-decoration-none">
                        <div class="position-relative">
                            @if(!empty($item->image))
                                <img src="{{ asset('storage/'.$item->image) }}" class="strip-img" alt="{{ $item->name }}">
                            @else
                                <div class="strip-img d-flex align-items-center justify-content-center bg-black text-secondary small">
                                    {{ __('common.no_image') }}
                                </div>
                            @endif
                        </div>
                        <div class="p-3">
                            <h6 class="mb-1 text-white">{{ $item->name }}</h6>
                            <div class="text-muted small mb-1">
                                {{ $item->category->name ?? __('common.no_category') }} • {{ $item->company->name ?? __('common.unknown_company') }}
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-success fw-bold">${{ number_format($item->price, 2) }}</span>
                                <span class="badge bg-secondary small">{{ __('common.stock_label') }}: {{ $item->stock }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    {{-- لا شيء --}}
                @endforelse
            </div>
        </div>
    </div>
</section>

