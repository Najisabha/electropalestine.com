@php
    $locale = app()->getLocale();
    $title = __('common.green_electronics_store');
    
    // Generate SEO meta tags for home page
    $description = $locale === 'ar'
        ? 'متجر إلكترونيات متخصص في فلسطين - أحدث الأجهزة الإلكترونية، الهواتف الذكية، اللابتوبات، والأجهزة الذكية بأفضل الأسعار. تسوق الآن من ElectroPalestine'
        : 'Electronics store specialized in Palestine - Latest electronic devices, smartphones, laptops, and smart devices at best prices. Shop now from ElectroPalestine';
    
    $keywords = $locale === 'ar'
        ? 'إلكترونيات فلسطين, متجر إلكترونيات, أجهزة إلكترونية, هواتف ذكية, لابتوبات, أجهزة ذكية, فلسطين, تسوق إلكتروني, أفضل الأسعار, ElectroPalestine'
        : 'electronics palestine, electronics store, electronic devices, smartphones, laptops, smart devices, palestine, online shopping, best prices, ElectroPalestine';
    
    $seoMeta = \App\Services\SeoService::generateMetaTags([
        'title' => $title . ' - ' . ($locale === 'ar' ? 'متجر الإلكترونيات الرائد في فلسطين' : 'Leading Electronics Store in Palestine'),
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
    ];
    $structuredData[] = \App\Services\SeoService::generateBreadcrumbStructuredData($breadcrumbItems);
@endphp
@include('layouts.app', [
    'seoMeta' => $seoMeta,
    'structuredData' => $structuredData,
    'slot' => view('store.partials.home-bootstrap', [
        'categories' => $categories,
        'featured' => $featured,
        'bestSelling' => $bestSelling ?? collect(),
        'campaigns' => $campaigns ?? collect(),
        'allProducts' => $allProducts ?? collect(),
    ]),
])

