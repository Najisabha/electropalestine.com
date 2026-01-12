@props([
    'src' => null,
    'alt' => 'Image',
    'lazy' => true,
    'class' => '',
    'width' => null,
    'height' => null,
    'placeholder' => 'images/placeholder.png'
])

@php
    // استخدام ImageHelper للحصول على URL مع دعم CDN
    $imageUrl = $src ? \App\Helpers\ImageHelper::url($src) : asset($placeholder);
    
    $attributes = [];
    $attributes['alt'] = $alt;
    $attributes['class'] = $class;
    
    // Lazy loading
    if ($lazy) {
        $attributes['loading'] = 'lazy';
        $attributes['decoding'] = 'async';
    }
    
    // Width & Height لتجنب layout shift
    if ($width) {
        $attributes['width'] = $width;
    }
    if ($height) {
        $attributes['height'] = $height;
    }
    
    // Image optimization attributes
    $attributes['fetchpriority'] = $lazy ? 'auto' : 'high';
@endphp

<img 
    src="{{ $imageUrl }}" 
    @foreach($attributes as $key => $value)
        {{ $key }}="{{ htmlspecialchars($value) }}"
    @endforeach
/>
