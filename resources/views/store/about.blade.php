@php
    $locale = app()->getLocale();
    $title = __('common.about_us');
    
    $description = $locale === 'ar'
        ? 'تعرف على ElectroPalestine - متجر الإلكترونيات الرائد في فلسطين. اكتشف قصتنا ورسالتنا في تقديم أفضل الأجهزة الإلكترونية للعملاء'
        : 'Learn about ElectroPalestine - Leading electronics store in Palestine. Discover our story and mission in providing the best electronic devices to customers';
    
    $keywords = $locale === 'ar'
        ? 'من نحن, ElectroPalestine, متجر إلكترونيات, فلسطين, قصة المتجر'
        : 'about us, ElectroPalestine, electronics store, palestine, store story';
    
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
            ['name' => $title, 'url' => route('store.about')],
        ]),
    ];
@endphp
@include('layouts.app', [
    'seoMeta' => $seoMeta,
    'structuredData' => $structuredData,
    'slot' => view('store.partials.about'),
])


