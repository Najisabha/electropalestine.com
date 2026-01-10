@php
    $locale = app()->getLocale();
    $title = __('common.faq');
    
    $description = $locale === 'ar'
        ? 'الأسئلة الشائعة حول ElectroPalestine - إجابات على جميع استفساراتك حول المنتجات، الطلبات، الشحن، والدفع'
        : 'Frequently Asked Questions about ElectroPalestine - Answers to all your questions about products, orders, shipping, and payment';
    
    $keywords = $locale === 'ar'
        ? 'أسئلة شائعة, FAQ, ElectroPalestine, استفسارات, مساعدة'
        : 'frequently asked questions, FAQ, ElectroPalestine, inquiries, help';
    
    $seoMeta = \App\Services\SeoService::generateMetaTags([
        'title' => $title . ' - ElectroPalestine',
        'description' => $description,
        'keywords' => $keywords,
        'type' => 'website',
    ]);
    
    // Generate FAQ structured data
    $faqs = [
        [
            'question' => __('common.faq_question_1') ?? 'كيف يمكنني الطلب من الموقع؟',
            'answer' => __('common.faq_answer_1') ?? 'يمكنك تصفح المنتجات وإضافتها إلى السلة، ثم إتمام عملية الشراء. يجب أن تكون مسجلاً في الموقع لإتمام الطلب.',
        ],
        [
            'question' => __('common.faq_question_2') ?? 'ما هي طرق الدفع المتاحة؟',
            'answer' => __('common.faq_answer_2') ?? 'نقبل الدفع النقدي عند الاستلام، والدفع بالرصيد/النقاط للمستخدمين المسجلين.',
        ],
        [
            'question' => __('common.faq_question_3') ?? 'كم تستغرق عملية التوصيل؟',
            'answer' => __('common.faq_answer_3') ?? 'مدة التوصيل تتراوح بين 3-7 أيام عمل حسب الموقع. سيتم التواصل معك لتحديد موعد التوصيل.',
        ],
        [
            'question' => __('common.faq_question_4') ?? 'هل يمكنني تتبع طلبي؟',
            'answer' => __('common.faq_answer_4') ?? 'نعم، يمكنك تتبع طلبك من خلال صفحة "تتبع الطلب" باستخدام رقم الطلب، أو من خلال صفحة "طلباتي" إذا كنت مسجلاً.',
        ],
        [
            'question' => __('common.faq_question_5') ?? 'ما هي سياسة الاسترجاع؟',
            'answer' => __('common.faq_answer_5') ?? 'يمكنك استرجاع المنتج خلال 14 يوم من تاريخ الاستلام بشرط أن يكون في حالته الأصلية. راجع صفحة "سياسة الاسترجاع" للتفاصيل الكاملة.',
        ],
        [
            'question' => __('common.faq_question_6') ?? 'هل المنتجات مضمونة؟',
            'answer' => __('common.faq_answer_6') ?? 'نعم، جميع المنتجات أصلية ومضمونة. نقدم ضمان على جميع المنتجات حسب نوع المنتج.',
        ],
        [
            'question' => __('common.faq_question_7') ?? 'كيف يمكنني التواصل مع خدمة العملاء؟',
            'answer' => __('common.faq_answer_7') ?? 'يمكنك التواصل معنا عبر صفحة "تواصل معنا" أو عبر البريد الإلكتروني: info@electropalestine.com أو الهاتف: +970598134332',
        ],
    ];
    
    $structuredData = [
        \App\Services\SeoService::generateOrganizationStructuredData(),
        \App\Services\SeoService::generateFAQStructuredData($faqs),
        \App\Services\SeoService::generateBreadcrumbStructuredData([
            ['name' => __('common.home'), 'url' => route('home')],
            ['name' => $title, 'url' => route('store.faq')],
        ]),
    ];
@endphp
@include('layouts.app', [
    'seoMeta' => $seoMeta,
    'structuredData' => $structuredData,
    'slot' => view('store.partials.faq'),
])
