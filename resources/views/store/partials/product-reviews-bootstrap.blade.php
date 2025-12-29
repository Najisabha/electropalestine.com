@php
    $category = $product->category;
@endphp

<section class="py-3 py-md-5 text-light">
    <div class="container">
        {{-- رأس الصفحة مع رابط العودة --}}
        <div class="mb-4 mb-md-5">
            <a href="{{ route('products.show', $product->id) }}" class="product-reviews-back-link">
                <i class="bi bi-arrow-right me-2"></i>
                {{ __('common.back_to_product') }}
            </a>
            <h1 class="product-reviews-page-title mt-3">
                {{ __('common.customer_ratings') }} - {{ $product->translated_name }}
            </h1>
        </div>

        {{-- ملخص التقييمات --}}
        <div class="product-page-reviews mb-4 mb-md-5">
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
        </div>

        {{-- جميع التعليقات --}}
        <div class="product-page-reviews">
            <h2 class="mb-4">{{ __('common.all_reviews') }}</h2>
            
            @if($reviews->isEmpty())
                <div class="product-page-review-item">
                    <p class="text-secondary mb-0 text-center py-5">
                        <i class="bi bi-chat-left-text me-2" style="font-size: 2rem; opacity: 0.3;"></i>
                        <br>
                        لا توجد تعليقات مكتوبة على هذا المنتج حتى الآن.
                    </p>
                </div>
            @else
                @foreach($reviews as $review)
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
            @endif
        </div>
    </div>
</section>
