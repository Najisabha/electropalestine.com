@props(['product', 'showCategory' => true])

@php
    $ratingAverage = $product->rating_average ?? 0;
    $salesCount = $product->sales_count ?? 0;
@endphp

<a href="{{ route('products.show', $product) }}" class="product-card-new text-decoration-none">
    {{-- الصورة مع الشارات --}}
    <div class="product-card-image-wrapper">
        {{-- شارة التقييم (أعلى يسار) --}}
        <span class="product-badge-rating">
            {{ number_format($ratingAverage, 1) }} <i class="bi bi-star-fill"></i>
        </span>
        
        {{-- شارة المبيعات (أعلى يمين) --}}
        <span class="product-badge-sold">
            {{ $salesCount }} {{ __('common.sold') }}
        </span>
        
        {{-- صورة المنتج --}}
        <div class="product-card-image-container">
            @if(!empty($product->image))
                <img src="{{ asset('storage/'.$product->image) }}" 
                     class="product-card-image" 
                     alt="{{ $product->translated_name }}">
            @else
                <div class="product-card-no-image">
                    <i class="bi bi-image text-secondary fs-1"></i>
                </div>
            @endif
        </div>
    </div>
    
    {{-- معلومات المنتج --}}
    <div class="product-card-info">
        <h6 class="product-card-title">{{ $product->translated_name }}</h6>
        
        @if($showCategory)
            <p class="product-card-category">
                {{ $product->category->translated_name ?? __('common.no_category') }}
            </p>
        @endif
        
        <div class="product-card-footer">
            <span class="product-card-price">${{ number_format($product->price, 2) }}</span>
            <span class="product-card-stock">{{ __('common.stock_label') }}: {{ $product->stock }}</span>
        </div>
    </div>
</a>

