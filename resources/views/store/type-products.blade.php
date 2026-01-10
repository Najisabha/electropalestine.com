@php
    $locale = app()->getLocale();
    $typeName = $type->translated_name;
    $categoryName = $category->translated_name ?? '';
    $title = $typeName . ($categoryName ? ' - ' . $categoryName : '');
    
    // Generate SEO meta tags
    $description = $locale === 'ar'
        ? "تصفح جميع منتجات {$typeName} من ElectroPalestine - أفضل الأسعار في فلسطين"
        : "Browse all {$typeName} products from ElectroPalestine - Best prices in Palestine";
    
    $keywords = $locale === 'ar'
        ? "{$typeName}, إلكترونيات, {$categoryName}, فلسطين, متجر إلكترونيات"
        : "{$typeName}, electronics, {$categoryName}, palestine, electronics store";
    
    $baseUrl = config('app.url');
    $image = $type->image 
        ? $baseUrl . '/storage/' . $type->image
        : $baseUrl . '/images/LOGO-remove background.png';
    
    $url = $baseUrl . '/types/' . $type->slug;
    
    $seoMeta = \App\Services\SeoService::generateMetaTags([
        'title' => $title . ' - ElectroPalestine',
        'description' => $description,
        'keywords' => $keywords,
        'image' => $image,
        'url' => $url,
        'type' => 'website',
    ]);
    
    // Generate structured data
    $structuredData = [
        \App\Services\SeoService::generateOrganizationStructuredData(),
        \App\Services\SeoService::generateWebSiteStructuredData(),
    ];
    
    // Add breadcrumb
    $breadcrumbItems = [
        ['name' => __('common.home'), 'url' => route('home')],
    ];
    if ($category) {
        $breadcrumbItems[] = [
            'name' => $category->translated_name,
            'url' => route('categories.show', $category->slug),
        ];
    }
    $breadcrumbItems[] = [
        'name' => $typeName,
        'url' => route('types.show', $type->slug),
    ];
    $structuredData[] = \App\Services\SeoService::generateBreadcrumbStructuredData($breadcrumbItems);
@endphp
@include('layouts.app', [
    'seoMeta' => $seoMeta,
    'structuredData' => $structuredData,
    'slot' => view('store.partials.type-products-bootstrap', [
        'type' => $type,
        'category' => $category,
        'products' => $products,
        'companies' => $companies,
        'sort' => $sort,
        'minPrice' => $minPrice,
        'maxPrice' => $maxPrice,
        'minRating' => $minRating,
        'companyId' => $companyId,
        'inStock' => $inStock,
        'perPage' => $perPage,
    ]),
])
