@php
    // Generate SEO meta tags for company
    $seoMeta = \App\Services\SeoService::generateCompanyMetaTags($company);
    
    // Generate structured data
    $structuredData = [
        \App\Services\SeoService::generateOrganizationStructuredData(),
        \App\Services\SeoService::generateWebSiteStructuredData(),
    ];
    
    // Add breadcrumb
    $breadcrumbItems = [
        ['name' => __('common.home'), 'url' => route('home')],
        ['name' => $company->name, 'url' => route('companies.show', $company->id)],
    ];
    $structuredData[] = \App\Services\SeoService::generateBreadcrumbStructuredData($breadcrumbItems);
@endphp
@include('layouts.app', [
    'seoMeta' => $seoMeta,
    'structuredData' => $structuredData,
    'slot' => view('store.partials.company-products-bootstrap', [
        'company' => $company,
        'products' => $products,
        'types' => $types,
        'categories' => $categories,
        'sort' => $sort,
        'minPrice' => $minPrice,
        'maxPrice' => $maxPrice,
        'minRating' => $minRating,
        'typeId' => $typeId,
        'categoryId' => $categoryId,
        'inStock' => $inStock,
        'perPage' => $perPage,
    ]),
])

