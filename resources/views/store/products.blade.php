@php
    $locale = app()->getLocale();
    $title = __('common.all_products');
    
    // Generate SEO meta tags
    $description = $locale === 'ar'
        ? 'تصفح جميع منتجات ElectroPalestine - أحدث الأجهزة الإلكترونية، الهواتف الذكية، اللابتوبات، والأجهزة الذكية. ابحث عن المنتج المثالي بأفضل الأسعار في فلسطين'
        : 'Browse all ElectroPalestine products - Latest electronic devices, smartphones, laptops, and smart devices. Find the perfect product at best prices in Palestine';
    
    $keywords = $locale === 'ar'
        ? 'جميع المنتجات, إلكترونيات فلسطين, متجر إلكترونيات, أجهزة إلكترونية, هواتف ذكية, لابتوبات, أجهزة ذكية, فلسطين, تسوق إلكتروني'
        : 'all products, electronics palestine, electronics store, electronic devices, smartphones, laptops, smart devices, palestine, online shopping';
    
    $seoMeta = \App\Services\SeoService::generateMetaTags([
        'title' => $title . ' - ElectroPalestine',
        'description' => $description,
        'keywords' => $keywords,
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
        ['name' => $title, 'url' => route('store.products')],
    ];
    $structuredData[] = \App\Services\SeoService::generateBreadcrumbStructuredData($breadcrumbItems);
@endphp
@include('layouts.app', [
    'seoMeta' => $seoMeta,
    'structuredData' => $structuredData,
    'slot' => view('store.partials.products-bootstrap', [
        'products' => $products,
        'categories' => $categories,
        'types' => $types,
        'companies' => $companies,
        'search' => $search,
        'categoryId' => $categoryId,
        'typeId' => $typeId,
        'companyId' => $companyId,
        'minPrice' => $minPrice,
        'maxPrice' => $maxPrice,
        'minRating' => $minRating,
        'inStock' => $inStock,
        'featured' => $featured,
        'sort' => $sort,
        'perPage' => $perPage,
    ]),
])
