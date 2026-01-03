@php
    $ratingAverage = $product->rating_average ?? 0;
    $ratingCount = $product->rating_count ?? 0;
    $category = $product->category;
    $types = $category?->types ?? collect();
@endphp

<section class="py-3 py-md-5 text-light">
    <div class="container">
        {{-- Breadcrumbs --}}
        <nav aria-label="breadcrumb" class="mb-3 product-page-breadcrumb">
            <ol class="breadcrumb mb-0 product-breadcrumb-list" style="--bs-breadcrumb-divider: '<';">
                <li class="breadcrumb-item">
                    <a href="/" class="breadcrumb-link">الرئيسية</a>
                </li>
                @if($category)
                    <li class="breadcrumb-item">
                        <a href="{{ route('categories.show', $category->slug) }}" class="breadcrumb-link">
                            {{ $category->translated_name }}
                        </a>
                    </li>
                @endif
                @if($product->type)
                    <li class="breadcrumb-item">
                        <a href="{{ route('types.show', $product->type->slug) }}" class="breadcrumb-link">
                            {{ $product->type->translated_name }}
                        </a>
                    </li>
                @endif
                <li class="breadcrumb-item active breadcrumb-product-name" aria-current="page">
                    {{ $product->translated_name }}
                </li>
            </ol>
        </nav>

        <div class="row g-4 align-items-start">
            {{-- صورة / كرت المنتج --}}
            <div class="col-12 col-lg-6">
                <div class="product-page-card h-100">
                    <div class="product-page-image-container">
                        @if(!empty($product->image))
                            <img src="{{ asset('storage/'.$product->image) }}"
                                 alt="{{ $product->translated_name }}">
                        @else
                            <div class="w-100 d-flex align-items-center justify-content-center text-secondary">
                                <div class="text-center">
                                    <i class="bi bi-image" style="font-size: 4rem; opacity: 0.3;"></i>
                                    <p class="mt-2 small">{{ __('common.no_image_product') }}</p>
                                </div>
                            </div>
                        @endif
                        <span class="product-page-sales-badge">
                            <i class="bi bi-cart-check me-1"></i>
                            {{ $product->sales_count ?? 0 }} {{ __('common.sold') }}
                        </span>
                    </div>
                    <div class="product-page-info">
                        <p class="text-secondary small mb-2">
                            {{ __('common.product_from') }} <strong class="text-primary">{{ $product->company->name ?? __('common.unknown_company') }}</strong>
                        </p>
                        <div class="product-page-badges">
                            @if($category)
                                <span class="product-page-badge product-page-badge-success">
                                    {{ $category->name }} • {{ __('common.main_category') }}
                                </span>
                            @endif
                            @if($product->type)
                                <span class="product-page-badge product-page-badge-warning">
                                    {{ $product->type->name }} • {{ __('common.type') }}
                                </span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-3">
                            <div class="product-page-rating-stars">
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
                            <span class="text-light fw-semibold">
                                {{ number_format($ratingAverage, 1) }} / 5
                            </span>
                            <span class="text-secondary small">
                                ({{ $ratingCount }} {{ __('common.rating_label') }})
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- معلومات المنتج + الأزرار --}}
            <div class="col-12 col-lg-6">
                <h1 class="product-page-title">{{ $product->translated_name }}</h1>

                <div class="product-page-price mb-3">
                    ${{ number_format($product->price, 2) }}
                </div>

                <div class="product-page-stock mb-4">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ __('common.available_stock') }}: <strong>{{ $product->stock }}</strong></span>
                </div>

                <p class="product-page-description">
                    {{ $product->translated_description ?? __('common.product_description_placeholder') }}
                </p>

                <div class="product-page-actions">
                    <form method="POST" action="{{ route('cart.add', $product) }}" class="d-inline flex-grow-1">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-main w-100">
                            <i class="bi bi-cart-plus me-2"></i>
                            {{ __('common.add_to_cart_button') }}
                        </button>
                    </form>
                    <a href="{{ route('store.checkout', ['product' => $product->id, 'quantity' => 1]) }}" class="btn btn-success flex-grow-1">
                        <i class="bi bi-bag-check me-2"></i>
                        {{ __('common.buy_now') }}
                    </a>
                    <button class="btn btn-outline-main flex-grow-1">
                        <i class="bi bi-envelope me-2"></i>
                        {{ __('common.contact_about_product') }}
                    </button>
                </div>

                {{-- قسم تقييمات العملاء --}}
                <div class="product-page-reviews">
                    <h2>{{ __('common.customer_ratings') }}</h2>
                    <div class="product-page-rating-summary">
                        <div class="product-page-rating-number">
                            {{ number_format($ratingAverage, 1) }}
                        </div>
                        <div>
                            <div class="product-page-rating-stars mb-2">
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
                            <div class="text-secondary">
                                {{ __('common.based_on_ratings') }} <strong class="text-light">{{ $ratingCount }}</strong> {{ __('common.rating_label') }}
                            </div>
                        </div>
                    </div>

                    {{-- التعليقات التفصيلية على هذه الطلبات لهذا المنتج --}}
                    @php($reviewsList = $reviews ?? collect())
                    @if($reviewsList->isEmpty())
                        <div class="product-page-review-item">
                            <p class="text-secondary mb-0 text-center py-3">
                                <i class="bi bi-chat-left-text me-2"></i>
                                لا توجد تعليقات مكتوبة على هذا المنتج حتى الآن.
                            </p>
                        </div>
                    @else
                        @foreach($reviewsList->take(3) as $review)
                            <div class="product-page-review-item">
                                <div class="product-page-review-header">
                                    <div>
                                        <div class="product-page-review-author">
                                            {{ $review->user->name ?? 'مستخدم' }}
                                        </div>
                                        <div class="product-page-review-email">
                                            {{ $review->user->email ?? 'بلا بريد' }}
                                        </div>
                                    </div>
                                    <div class="product-page-rating-stars" style="font-size: 1rem;">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="bi bi-star-fill"></i>
                                            @else
                                                <i class="bi bi-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                @if($review->comment)
                                    <p class="product-page-review-comment">{{ $review->comment }}</p>
                                @endif
                                <div class="product-page-review-date">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $review->created_at?->format('Y/m/d H:i') }}
                                </div>
                            </div>
                        @endforeach
                        
                        {{-- رابط عرض جميع التعليقات --}}
                        @if($reviewsList->count() > 3)
                            <div class="product-page-view-all-reviews">
                                <a href="{{ route('products.reviews', $product->id) }}" class="product-page-view-all-link">
                                    <span>{{ __('common.view_all_reviews') }} ({{ $reviewsList->count() }})</span>
                                    <i class="bi bi-arrow-left"></i>
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- شريط ذات صلة: منتجات من نفس الأصناف/الأنواع --}}
        <div class="product-page-related">
            <h2>{{ __('common.related_products') }}</h2>

            <div class="products-scroll mb-3">
                @forelse ($related as $item)
                    <x-product-card :product="$item" />
                @empty
                    {{-- لا شيء --}}
                @endforelse
            </div>
        </div>
    </div>
</section>

