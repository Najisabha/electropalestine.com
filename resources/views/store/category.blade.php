@php
    // Generate SEO meta tags for category
    $seoMeta = \App\Services\SeoService::generateCategoryMetaTags($category);
    
    // Generate structured data
    $structuredData = [
        \App\Services\SeoService::generateOrganizationStructuredData(),
        \App\Services\SeoService::generateWebSiteStructuredData(),
    ];
    
    // Add breadcrumb
    $breadcrumbItems = [
        ['name' => __('common.home'), 'url' => route('home')],
        ['name' => $category->translated_name, 'url' => route('categories.show', $category->slug)],
    ];
    $structuredData[] = \App\Services\SeoService::generateBreadcrumbStructuredData($breadcrumbItems);
@endphp
@include('layouts.app', [
    'seoMeta' => $seoMeta,
    'structuredData' => $structuredData,
    'slot' => view('store.partials.category-bootstrap', [
        'category' => $category,
        'types' => $types,
        'companies' => $companies,
        'products' => $products,
    ]),
])

