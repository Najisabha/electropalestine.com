@props(['product', 'showCategory' => true])

@php
    $ratingAverage = $product->rating_average ?? 0;
    $salesCount = $product->sales_count ?? 0;
    $hasStock = ($product->stock ?? 0) > 0;
@endphp

<a href="{{ route('products.show', $product) }}" class="product-card-new text-decoration-none">
    {{-- الصورة مع الشارات --}}
    <div class="product-card-image-wrapper">
        {{-- شارة التقييم (أعلى يسار) --}}
        @if($ratingAverage > 0)
            <span class="product-badge-rating">
                <i class="bi bi-star-fill"></i>
                <span>{{ number_format($ratingAverage, 1) }}</span>
            </span>
        @endif
        
        {{-- شارة المبيعات (أعلى يمين) --}}
        @if($salesCount > 0)
            <span class="product-badge-sold">
                <i class="bi bi-check-circle"></i>
                <span>{{ $salesCount }} {{ __('common.sold') }}</span>
            </span>
        @endif
        
        {{-- شارة المخزون --}}
        @if(!$hasStock)
            <span class="product-badge-out-of-stock">
                <i class="bi bi-x-circle"></i>
                <span>{{ __('common.out_of_stock') }}</span>
            </span>
        @endif
        
        {{-- صورة المنتج --}}
        <div class="product-card-image-container">
            @if(!empty($product->image))
                <img src="{{ asset('storage/'.$product->image) }}" 
                     class="product-card-image" 
                     alt="{{ $product->translated_name }}"
                     loading="lazy">
            @else
                <div class="product-card-no-image">
                    <i class="bi bi-image"></i>
                </div>
            @endif
        </div>
        
        {{-- Overlay عند hover --}}
        <div class="product-card-overlay">
            <div class="product-card-overlay-content">
                <i class="bi bi-eye"></i>
                <span>{{ __('common.view_details') }}</span>
            </div>
        </div>
    </div>
    
    {{-- معلومات المنتج --}}
    <div class="product-card-info">
        <div class="product-card-header">
            <h6 class="product-card-title">{{ $product->translated_name }}</h6>
            @if($showCategory && $product->category)
                <span class="product-card-category-badge">
                    {{ $product->category->translated_name }}
                </span>
            @endif
        </div>
        
        <div class="product-card-footer">
            <div class="product-card-price-section">
                <span class="product-card-price">${{ number_format($product->price, 2) }}</span>
                @if($hasStock)
                    <span class="product-card-stock-badge">
                        <i class="bi bi-check2-circle"></i>
                        <span>{{ $product->stock }} {{ __('common.in_stock') }}</span>
                    </span>
                @else
                    <span class="product-card-stock-badge out-of-stock">
                        <i class="bi bi-x-circle"></i>
                        <span>{{ __('common.out_of_stock') }}</span>
                    </span>
                @endif
            </div>
        </div>
    </div>
</a>

