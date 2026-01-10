@php
    // Generate SEO meta tags for product
    $seoMeta = \App\Services\SeoService::generateProductMetaTags($product);
    
    // Generate structured data with reviews
    $structuredData = [
        \App\Services\SeoService::generateProductStructuredData($product, $reviews ?? collect()),
        \App\Services\SeoService::generateOrganizationStructuredData(),
        \App\Services\SeoService::generateWebSiteStructuredData(),
    ];
    
    // Generate breadcrumb structured data
    $breadcrumbItems = [
        ['name' => __('common.home'), 'url' => route('home')],
    ];
    
    if ($product->category) {
        $breadcrumbItems[] = [
            'name' => $product->category->translated_name,
            'url' => route('categories.show', $product->category->slug),
        ];
    }
    
    if ($product->type) {
        $breadcrumbItems[] = [
            'name' => $product->type->translated_name,
            'url' => route('types.show', $product->type->slug),
        ];
    }
    
    $breadcrumbItems[] = [
        'name' => $product->translated_name,
        'url' => route('products.show', $product->slug),
    ];
    
    $structuredData[] = \App\Services\SeoService::generateBreadcrumbStructuredData($breadcrumbItems);
@endphp
@include('layouts.app', [
    'seoMeta' => $seoMeta,
    'structuredData' => $structuredData,
    'slot' => view('store.partials.product-bootstrap', [
        'product' => $product,
        'related' => $related,
        'reviews' => $reviews ?? collect(),
    ]),
])

