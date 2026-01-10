@php
    $locale = app()->getLocale();
    $title = __('common.contact_us');
    
    $description = $locale === 'ar'
        ? 'تواصل معنا في ElectroPalestine - نحن هنا لمساعدتك في جميع استفساراتك حول المنتجات والطلبات. اتصل بنا الآن'
        : 'Contact us at ElectroPalestine - We are here to help you with all your inquiries about products and orders. Contact us now';
    
    $keywords = $locale === 'ar'
        ? 'اتصل بنا, ElectroPalestine, دعم العملاء, خدمة العملاء, فلسطين'
        : 'contact us, ElectroPalestine, customer support, customer service, palestine';
    
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
            ['name' => $title, 'url' => route('store.contact')],
        ]),
    ];
@endphp
@include('layouts.app', [
    'seoMeta' => $seoMeta,
    'structuredData' => $structuredData,
    'slot' => view('store.partials.contact'),
])


