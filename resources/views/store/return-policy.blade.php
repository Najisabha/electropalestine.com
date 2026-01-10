@php
    $locale = app()->getLocale();
    $title = __('common.return_policy');
    
    $description = $locale === 'ar'
        ? 'سياسة الإرجاع والاستبدال في ElectroPalestine - تعرف على شروط إرجاع المنتجات واستبدالها. ضمان رضا العملاء'
        : 'Return and Exchange Policy at ElectroPalestine - Learn about product return and exchange terms. Customer satisfaction guarantee';
    
    $keywords = $locale === 'ar'
        ? 'سياسة الإرجاع, سياسة الاستبدال, ElectroPalestine, ضمان, رضا العملاء'
        : 'return policy, exchange policy, ElectroPalestine, guarantee, customer satisfaction';
    
    $seoMeta = \App\Services\SeoService::generateMetaTags([
        'title' => $title . ' - ElectroPalestine',
        'description' => $description,
        'keywords' => $keywords,
        'type' => 'website',
    ]);
    
    $structuredData = [
        \App\Services\SeoService::generateOrganizationStructuredData(),
        \App\Services\SeoService::generateBreadcrumbStructuredData([
            ['name' => __('common.home'), 'url' => route('home')],
            ['name' => $title, 'url' => route('store.return-policy')],
        ]),
    ];
@endphp
@include('layouts.app', [
    'seoMeta' => $seoMeta,
    'structuredData' => $structuredData,
    'slot' => view('store.partials.return-policy'),
])
