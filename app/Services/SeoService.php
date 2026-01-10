<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\URL;

class SeoService
{
    /**
     * Generate meta tags for a page
     */
    public static function generateMetaTags(array $data = []): array
    {
        $locale = app()->getLocale();
        $baseUrl = config('app.url');
        $siteName = 'ElectroPalestine';
        $defaultDescription = $locale === 'ar' 
            ? 'متجر إلكترونيات متخصص في فلسطين - أحدث الأجهزة الإلكترونية بأفضل الأسعار'
            : 'Electronics store specialized in Palestine - Latest electronic devices at best prices';
        
        $defaultKeywords = $locale === 'ar'
            ? 'إلكترونيات فلسطين, متجر إلكترونيات, أجهزة إلكترونية, هواتف, لابتوبات, أجهزة ذكية, فلسطين'
            : 'electronics palestine, electronics store, electronic devices, phones, laptops, smart devices, palestine';

        // Default values
        $title = $data['title'] ?? $siteName;
        $description = $data['description'] ?? $defaultDescription;
        $keywords = $data['keywords'] ?? $defaultKeywords;
        $image = $data['image'] ?? $baseUrl . '/images/LOGO-remove background.png';
        $url = $data['url'] ?? URL::current();
        $type = $data['type'] ?? 'website';
        $canonical = $data['canonical'] ?? $url;
        
        // Ensure full URL for image
        if ($image && !str_starts_with($image, 'http')) {
            $image = $baseUrl . '/' . ltrim($image, '/');
        }
        
        // Ensure full URL
        if (!str_starts_with($url, 'http')) {
            $url = $baseUrl . '/' . ltrim($url, '/');
        }
        
        if (!str_starts_with($canonical, 'http')) {
            $canonical = $baseUrl . '/' . ltrim($canonical, '/');
        }

        // Generate hreflang URLs
        $hreflang = self::generateHreflang($url, $data['hreflang_urls'] ?? []);

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'image' => $image,
            'url' => $url,
            'canonical' => $canonical,
            'type' => $type,
            'site_name' => $siteName,
            'locale' => $locale,
            'hreflang' => $hreflang,
            'twitter_card' => $data['twitter_card'] ?? 'summary_large_image',
        ];
    }

    /**
     * Generate meta tags for a product page
     */
    public static function generateProductMetaTags(Product $product): array
    {
        $locale = app()->getLocale();
        $baseUrl = config('app.url');
        
        $name = $product->translated_name;
        $description = $product->translated_description 
            ? strip_tags($product->translated_description)
            : ($locale === 'ar' 
                ? "اشتري {$name} من ElectroPalestine - أفضل الأسعار في فلسطين"
                : "Buy {$name} from ElectroPalestine - Best prices in Palestine");
        
        // Limit description length
        if (mb_strlen($description) > 160) {
            $description = mb_substr($description, 0, 157) . '...';
        }
        
        $price = number_format($product->price, 2);
        $currency = 'ILS'; // Israeli Shekel
        
        $keywords = $locale === 'ar'
            ? "{$name}, إلكترونيات, {$product->category?->name}, {$product->company?->name}, فلسطين, متجر إلكترونيات"
            : "{$name}, electronics, {$product->category?->name_en}, {$product->company?->name}, palestine, electronics store";
        
        $image = $product->image 
            ? $baseUrl . '/storage/' . $product->image
            : $baseUrl . '/images/LOGO-remove background.png';
        
        $url = $baseUrl . '/products/' . $product->slug;
        
        // Generate hreflang URLs
        $hreflangUrls = [
            'ar' => $url,
            'en' => $url, // Same URL, different language via session
        ];
        
        return self::generateMetaTags([
            'title' => "{$name} - ElectroPalestine",
            'description' => $description,
            'keywords' => $keywords,
            'image' => $image,
            'url' => $url,
            'type' => 'product',
            'hreflang_urls' => $hreflangUrls,
            'price' => $price,
            'currency' => $currency,
            'availability' => $product->stock > 0 ? 'in_stock' : 'out_of_stock',
        ]);
    }

    /**
     * Generate meta tags for a category page
     */
    public static function generateCategoryMetaTags(Category $category): array
    {
        $locale = app()->getLocale();
        $baseUrl = config('app.url');
        
        $name = $locale === 'en' && $category->name_en ? $category->name_en : $category->name;
        
        $description = $locale === 'ar'
            ? "تصفح جميع منتجات {$name} من ElectroPalestine - أفضل الأسعار في فلسطين"
            : "Browse all {$name} products from ElectroPalestine - Best prices in Palestine";
        
        $keywords = $locale === 'ar'
            ? "{$name}, إلكترونيات, منتجات, فلسطين, متجر إلكترونيات"
            : "{$name}, electronics, products, palestine, electronics store";
        
        $image = $category->image 
            ? $baseUrl . '/storage/' . $category->image
            : $baseUrl . '/images/LOGO-remove background.png';
        
        $url = $baseUrl . '/categories/' . $category->slug;
        
        return self::generateMetaTags([
            'title' => "{$name} - ElectroPalestine",
            'description' => $description,
            'keywords' => $keywords,
            'image' => $image,
            'url' => $url,
            'type' => 'website',
        ]);
    }

    /**
     * Generate meta tags for a company page
     */
    public static function generateCompanyMetaTags($company): array
    {
        $locale = app()->getLocale();
        $baseUrl = config('app.url');
        
        $name = $company->name;
        
        $description = $locale === 'ar'
            ? "تصفح جميع منتجات {$name} من ElectroPalestine - أفضل الأسعار في فلسطين"
            : "Browse all {$name} products from ElectroPalestine - Best prices in Palestine";
        
        $keywords = $locale === 'ar'
            ? "{$name}, إلكترونيات, منتجات, فلسطين, متجر إلكترونيات"
            : "{$name}, electronics, products, palestine, electronics store";
        
        $image = $company->image 
            ? $baseUrl . '/storage/' . $company->image
            : ($company->background 
                ? $baseUrl . '/storage/' . $company->background
                : $baseUrl . '/images/LOGO-remove background.png');
        
        $url = $baseUrl . '/companies/' . $company->id;
        
        return self::generateMetaTags([
            'title' => "{$name} - ElectroPalestine",
            'description' => $description,
            'keywords' => $keywords,
            'image' => $image,
            'url' => $url,
            'type' => 'website',
        ]);
    }

    /**
     * Generate hreflang tags
     */
    private static function generateHreflang(string $currentUrl, array $urls = []): array
    {
        $baseUrl = config('app.url');
        $locale = app()->getLocale();
        
        // If no specific URLs provided, use current URL for both languages
        if (empty($urls)) {
            return [
                'ar' => $currentUrl,
                'en' => $currentUrl,
                'x-default' => $currentUrl,
            ];
        }
        
        return [
            'ar' => $urls['ar'] ?? $currentUrl,
            'en' => $urls['en'] ?? $currentUrl,
            'x-default' => $urls['ar'] ?? $currentUrl,
        ];
    }

    /**
     * Generate JSON-LD structured data for a product
     */
    public static function generateProductStructuredData(Product $product, $reviews = null): array
    {
        $locale = app()->getLocale();
        $baseUrl = config('app.url');
        
        $name = $product->translated_name;
        $description = $product->translated_description 
            ? strip_tags($product->translated_description)
            : '';
        
        $image = $product->image 
            ? $baseUrl . '/storage/' . $product->image
            : $baseUrl . '/images/LOGO-remove background.png';
        
        $url = $baseUrl . '/products/' . $product->slug;
        
        $availability = $product->stock > 0 
            ? 'https://schema.org/InStock' 
            : 'https://schema.org/OutOfStock';
        
        $ratingValue = $product->rating_average ?? 0;
        $reviewCount = $product->rating_count ?? 0;
        
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $name,
            'description' => $description,
            'image' => $image,
            'url' => $url,
            'sku' => (string) $product->id,
            'mpn' => (string) $product->id,
            'brand' => [
                '@type' => 'Brand',
                'name' => $product->company?->name ?? 'ElectroPalestine',
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => $url,
                'priceCurrency' => 'ILS',
                'price' => (string) $product->price,
                'priceValidUntil' => date('Y-m-d', strtotime('+1 year')),
                'itemCondition' => 'https://schema.org/NewCondition',
                'availability' => $availability,
                'seller' => [
                    '@type' => 'Organization',
                    'name' => 'ElectroPalestine',
                    'url' => $baseUrl,
                ],
            ],
        ];
        
        // Add category
        if ($product->category) {
            $structuredData['category'] = $locale === 'en' && $product->category->name_en 
                ? $product->category->name_en 
                : $product->category->name;
        }
        
        // Add aggregate rating if available
        if ($reviewCount > 0 && $ratingValue > 0) {
            $structuredData['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => (string) $ratingValue,
                'reviewCount' => (string) $reviewCount,
                'bestRating' => '5',
                'worstRating' => '1',
            ];
        }
        
        // Add individual reviews if provided
        if ($reviews && $reviews->isNotEmpty()) {
            $structuredData['review'] = [];
            foreach ($reviews->take(10) as $review) {
                $reviewData = [
                    '@type' => 'Review',
                    'author' => [
                        '@type' => 'Person',
                        'name' => $review->user->name ?? 'مستخدم',
                    ],
                    'datePublished' => $review->created_at?->format('Y-m-d') ?? date('Y-m-d'),
                    'reviewBody' => $review->comment ?? '',
                    'reviewRating' => [
                        '@type' => 'Rating',
                        'ratingValue' => (string) $review->rating,
                        'bestRating' => '5',
                        'worstRating' => '1',
                    ],
                ];
                $structuredData['review'][] = $reviewData;
            }
        }
        
        return $structuredData;
    }

    /**
     * Generate JSON-LD structured data for the organization/store
     */
    public static function generateOrganizationStructuredData(): array
    {
        $baseUrl = config('app.url');
        $locale = app()->getLocale();
        
        $name = 'ElectroPalestine';
        $description = $locale === 'ar'
            ? 'متجر إلكترونيات متخصص في فلسطين - أحدث الأجهزة الإلكترونية بأفضل الأسعار'
            : 'Electronics store specialized in Palestine - Latest electronic devices at best prices';
        
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $name,
            'url' => $baseUrl,
            'logo' => $baseUrl . '/images/LOGO-remove background.png',
            'description' => $description,
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => 'PS', // Palestine ISO code
                'addressRegion' => 'Palestine',
            ],
            'sameAs' => [
                'https://www.facebook.com/share/14V9hQGcAbE/',
                'https://www.instagram.com/electro_palestine',
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'contactType' => 'customer service',
                'areaServed' => 'PS',
                'availableLanguage' => ['ar', 'en'],
            ],
        ];
    }

    /**
     * Generate JSON-LD structured data for breadcrumbs
     */
    public static function generateBreadcrumbStructuredData(array $items): array
    {
        $baseUrl = config('app.url');
        
        $breadcrumbList = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [],
        ];
        
        $position = 1;
        foreach ($items as $item) {
            $url = $item['url'] ?? '';
            if (!str_starts_with($url, 'http')) {
                $url = $baseUrl . '/' . ltrim($url, '/');
            }
            
            $breadcrumbList['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $item['name'],
                'item' => $url,
            ];
            
            $position++;
        }
        
        return $breadcrumbList;
    }

    /**
     * Generate JSON-LD structured data for a website
     */
    public static function generateWebSiteStructuredData(): array
    {
        $baseUrl = config('app.url');
        $locale = app()->getLocale();
        
        $name = 'ElectroPalestine';
        $description = $locale === 'ar'
            ? 'متجر إلكترونيات متخصص في فلسطين - أحدث الأجهزة الإلكترونية بأفضل الأسعار'
            : 'Electronics store specialized in Palestine - Latest electronic devices at best prices';
        
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $name,
            'url' => $baseUrl,
            'description' => $description,
            'inLanguage' => $locale === 'ar' ? 'ar' : 'en',
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => $baseUrl . '/products?search={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $name,
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $baseUrl . '/images/LOGO-remove background.png',
                ],
            ],
        ];
    }

    /**
     * Generate FAQ structured data
     */
    public static function generateFAQStructuredData(array $faqs): array
    {
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [],
        ];
        
        foreach ($faqs as $faq) {
            $structuredData['mainEntity'][] = [
                '@type' => 'Question',
                'name' => $faq['question'] ?? '',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'] ?? '',
                ],
            ];
        }
        
        return $structuredData;
    }
}
