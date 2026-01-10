@php
    $locale = app()->getLocale();
    $title = __('common.our_story');
    
    $description = $locale === 'ar'
        ? 'اكتشف قصة ElectroPalestine - رحلتنا في بناء متجر إلكترونيات موثوق في فلسطين. تعرف على رؤيتنا وقيمنا'
        : 'Discover the story of ElectroPalestine - Our journey in building a trusted electronics store in Palestine. Learn about our vision and values';
    
    $keywords = $locale === 'ar'
        ? 'قصتنا, ElectroPalestine, رؤية, قيم, فلسطين'
        : 'our story, ElectroPalestine, vision, values, palestine';
    
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
            ['name' => $title, 'url' => route('store.story')],
        ]),
    ];
@endphp
@include('layouts.app', [
    'seoMeta' => $seoMeta,
    'structuredData' => $structuredData,
    'slot' => view('store.partials.story'),
])


