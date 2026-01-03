<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Type;
use App\Models\Company;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;

class SitemapController extends Controller
{
    /**
     * Cache duration in minutes (24 hours)
     */
    private const CACHE_DURATION = 1440;
    
    /**
     * Maximum URLs per sitemap (Google limit is 50,000, but we use 10,000 for better performance)
     */
    private const MAX_URLS_PER_SITEMAP = 10000;
    
    /**
     * Generate sitemap.xml dynamically with caching
     */
    public function index(): Response
    {
        $cacheKey = 'sitemap.xml';
        
        // Try to get from cache first
        $xml = Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            return $this->generateSitemap();
        });
        
        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=86400'); // Cache for 24 hours
    }
    
    /**
     * Generate sitemap index if content is too large
     */
    public function indexFile(): Response
    {
        $cacheKey = 'sitemap.index.xml';
        
        $xml = Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            return $this->generateSitemapIndex();
        });
        
        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=86400');
    }
    
    /**
     * Generate sitemap for products (if split is needed)
     */
    public function products(int $page = 1): Response
    {
        $cacheKey = "sitemap.products.{$page}.xml";
        
        $xml = Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($page) {
            return $this->generateProductsSitemap($page);
        });
        
        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=86400');
    }
    
    /**
     * Generate images sitemap for products
     */
    public function images(): Response
    {
        $cacheKey = 'sitemap.images.xml';
        
        $xml = Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            return $this->generateImagesSitemap();
        });
        
        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=86400');
    }
    
    /**
     * Generate the main sitemap
     */
    private function generateSitemap(): string
    {
        $baseUrl = config('app.url');
        $supportedLocales = ['ar', 'en'];
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
        
        // Static pages with hreflang
        $staticPages = [
            ['path' => '', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['path' => '/products', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['path' => '/about', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['path' => '/story', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['path' => '/contact', 'priority' => '0.7', 'changefreq' => 'monthly'],
        ];
        
        // Add static pages with hreflang
        // Note: Since language is handled via session, we use the same URL for both languages
        // but indicate language variants via hreflang
        foreach ($staticPages as $page) {
            $urls = [];
            $basePath = $baseUrl . $page['path'];
            foreach ($supportedLocales as $locale) {
                // Use same URL for both languages (language is set via session)
                // But we can add a language indicator in hreflang
                $urls[$locale] = $basePath;
            }
            $xml .= $this->generateUrlEntryWithHreflang($urls, $page['priority'], $page['changefreq']);
        }
        
        // Add categories with hreflang and images
        $categories = Category::select('slug', 'image', 'name', 'name_en', 'updated_at')->get();
        foreach ($categories as $category) {
            $urls = [];
            $basePath = $baseUrl . '/categories/' . $category->slug;
            foreach ($supportedLocales as $locale) {
                $urls[$locale] = $basePath;
            }
            
            // Prepare images for this category
            $images = [];
            $categoryName = $category->name_en ?: $category->name;
            
            if ($category->image) {
                $images[] = [
                    'url' => $baseUrl . '/storage/' . $category->image,
                    'title' => $categoryName
                ];
            }
            
            $xml .= $this->generateUrlEntryWithHreflangAndImages($urls, '0.8', 'weekly', $category->updated_at, $images);
        }
        
        // Add types with hreflang and images
        $types = Type::select('slug', 'image', 'name', 'name_en', 'updated_at')->get();
        foreach ($types as $type) {
            $urls = [];
            $basePath = $baseUrl . '/types/' . $type->slug;
            foreach ($supportedLocales as $locale) {
                $urls[$locale] = $basePath;
            }
            
            // Prepare images for this type
            $images = [];
            $typeName = $type->name_en ?: $type->name;
            
            if ($type->image) {
                $images[] = [
                    'url' => $baseUrl . '/storage/' . $type->image,
                    'title' => $typeName
                ];
            }
            
            $xml .= $this->generateUrlEntryWithHreflangAndImages($urls, '0.7', 'weekly', $type->updated_at, $images);
        }
        
        // Add companies with hreflang and images
        $companies = Company::select('id', 'image', 'background', 'name', 'updated_at')->get();
        foreach ($companies as $company) {
            $urls = [];
            $basePath = $baseUrl . '/companies/' . $company->id;
            foreach ($supportedLocales as $locale) {
                $urls[$locale] = $basePath;
            }
            
            // Prepare images for this company
            $images = [];
            
            if ($company->image) {
                $images[] = [
                    'url' => $baseUrl . '/storage/' . $company->image,
                    'title' => $company->name
                ];
            }
            
            if ($company->background && $company->background !== $company->image) {
                $images[] = [
                    'url' => $baseUrl . '/storage/' . $company->background,
                    'title' => $company->name . ' - Background'
                ];
            }
            
            $xml .= $this->generateUrlEntryWithHreflangAndImages($urls, '0.7', 'weekly', $company->updated_at, $images);
        }
        
        // Count active products
        $productCount = Product::active()->count();
        
        // If products exceed limit, we'll use sitemap index
        // Otherwise, add products directly
        if ($productCount <= self::MAX_URLS_PER_SITEMAP) {
            // Get products with image data
            $products = Product::active()
                ->select('slug', 'image', 'thumbnail', 'name', 'name_en', 'updated_at')
                ->orderBy('updated_at', 'desc')
                ->get();
            
            foreach ($products as $product) {
                $urls = [];
                $basePath = $baseUrl . '/products/' . $product->slug;
                foreach ($supportedLocales as $locale) {
                    $urls[$locale] = $basePath;
                }
                
                // Prepare images for this product
                $images = [];
                $productName = $product->name_en ?: $product->name;
                
                if ($product->image) {
                    $images[] = [
                        'url' => $baseUrl . '/storage/' . $product->image,
                        'title' => $productName
                    ];
                }
                
                if ($product->thumbnail && $product->thumbnail !== $product->image) {
                    $images[] = [
                        'url' => $baseUrl . '/storage/' . $product->thumbnail,
                        'title' => $productName
                    ];
                }
                
                // Generate URL entry with hreflang and images
                $xml .= $this->generateUrlEntryWithHreflangAndImages($urls, '0.9', 'weekly', $product->updated_at, $images);
            }
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }
    
    /**
     * Generate sitemap index (for large sites)
     */
    private function generateSitemapIndex(): string
    {
        $baseUrl = config('app.url');
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Main sitemap (static pages, categories, types, companies)
        $xml .= "  <sitemap>\n";
        $xml .= "    <loc>" . htmlspecialchars($baseUrl . '/sitemap.xml', ENT_XML1, 'UTF-8') . "</loc>\n";
        $xml .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
        $xml .= "  </sitemap>\n";
        
        // Images sitemap
        $xml .= "  <sitemap>\n";
        $xml .= "    <loc>" . htmlspecialchars($baseUrl . '/sitemap/images.xml', ENT_XML1, 'UTF-8') . "</loc>\n";
        $xml .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
        $xml .= "  </sitemap>\n";
        
        // Product sitemaps (if needed)
        $productCount = Product::active()->count();
        $pages = ceil($productCount / self::MAX_URLS_PER_SITEMAP);
        
        for ($i = 1; $i <= $pages; $i++) {
            $xml .= "  <sitemap>\n";
            $xml .= "    <loc>" . htmlspecialchars($baseUrl . "/sitemap/products/{$i}.xml", ENT_XML1, 'UTF-8') . "</loc>\n";
            $xml .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
            $xml .= "  </sitemap>\n";
        }
        
        $xml .= '</sitemapindex>';
        
        return $xml;
    }
    
    /**
     * Generate products sitemap (for pagination)
     */
    private function generateProductsSitemap(int $page): string
    {
        $baseUrl = config('app.url');
        $supportedLocales = ['ar', 'en'];
        $perPage = self::MAX_URLS_PER_SITEMAP;
        $offset = ($page - 1) * $perPage;
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
        
        // Get products with image data
        $products = Product::active()
            ->select('slug', 'image', 'thumbnail', 'name', 'name_en', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get();
        
        foreach ($products as $product) {
            $urls = [];
            $basePath = $baseUrl . '/products/' . $product->slug;
            foreach ($supportedLocales as $locale) {
                $urls[$locale] = $basePath;
            }
            
            // Prepare images for this product
            $images = [];
            $productName = $product->name_en ?: $product->name;
            
            if ($product->image) {
                $images[] = [
                    'url' => $baseUrl . '/storage/' . $product->image,
                    'title' => $productName
                ];
            }
            
            if ($product->thumbnail && $product->thumbnail !== $product->image) {
                $images[] = [
                    'url' => $baseUrl . '/storage/' . $product->thumbnail,
                    'title' => $productName
                ];
            }
            
            // Generate URL entry with hreflang and images
            $xml .= $this->generateUrlEntryWithHreflangAndImages($urls, '0.9', 'weekly', $product->updated_at, $images);
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }
    
    /**
     * Generate a single URL entry for sitemap (without hreflang)
     */
    private function generateUrlEntry(string $url, string $priority, string $changefreq, $lastmod = null): string
    {
        $xml = "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($url, ENT_XML1, 'UTF-8') . "</loc>\n";
        $xml .= "    <priority>" . $priority . "</priority>\n";
        $xml .= "    <changefreq>" . $changefreq . "</changefreq>\n";
        
        if ($lastmod) {
            // Use W3C datetime format (YYYY-MM-DDThh:mm:ss+00:00)
            if ($lastmod instanceof \DateTime || $lastmod instanceof \DateTimeInterface) {
                $xml .= "    <lastmod>" . $lastmod->format('Y-m-d\TH:i:s\Z') . "</lastmod>\n";
            } else {
                $xml .= "    <lastmod>" . date('Y-m-d\TH:i:s\Z', strtotime($lastmod)) . "</lastmod>\n";
            }
        } else {
            $xml .= "    <lastmod>" . date('Y-m-d\TH:i:s\Z') . "</lastmod>\n";
        }
        
        $xml .= "  </url>\n";
        
        return $xml;
    }
    
    /**
     * Generate URL entry with hreflang support for multi-language sites
     * 
     * @param array $urls Array of URLs by locale, e.g., ['ar' => '...', 'en' => '...']
     * @param string $priority
     * @param string $changefreq
     * @param mixed $lastmod
     * @return string
     */
    private function generateUrlEntryWithHreflang(array $urls, string $priority, string $changefreq, $lastmod = null): string
    {
        return $this->generateUrlEntryWithHreflangAndImages($urls, $priority, $changefreq, $lastmod, []);
    }
    
    /**
     * Generate URL entry with hreflang and images support
     * 
     * @param array $urls Array of URLs by locale, e.g., ['ar' => '...', 'en' => '...']
     * @param string $priority
     * @param string $changefreq
     * @param mixed $lastmod
     * @param array $images Array of images, e.g., [['url' => '...', 'title' => '...']]
     * @return string
     */
    private function generateUrlEntryWithHreflangAndImages(array $urls, string $priority, string $changefreq, $lastmod = null, array $images = []): string
    {
        // Use Arabic URL as default (can be changed based on your default locale)
        $defaultUrl = $urls['ar'] ?? $urls['en'] ?? reset($urls);
        
        $xml = "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($defaultUrl, ENT_XML1, 'UTF-8') . "</loc>\n";
        
        // Add hreflang links for all languages
        foreach ($urls as $locale => $url) {
            $xml .= "    <xhtml:link rel=\"alternate\" hreflang=\"" . htmlspecialchars($locale, ENT_XML1, 'UTF-8') . "\" href=\"" . htmlspecialchars($url, ENT_XML1, 'UTF-8') . "\" />\n";
        }
        
        // Add x-default (usually points to the default language)
        $defaultLocaleUrl = $urls['ar'] ?? reset($urls);
        $xml .= "    <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"" . htmlspecialchars($defaultLocaleUrl, ENT_XML1, 'UTF-8') . "\" />\n";
        
        // Add images if provided
        if (!empty($images)) {
            foreach ($images as $image) {
                $xml .= $this->generateImageEntry($image['url'], $image['title']);
            }
        }
        
        $xml .= "    <priority>" . $priority . "</priority>\n";
        $xml .= "    <changefreq>" . $changefreq . "</changefreq>\n";
        
        if ($lastmod) {
            // Use W3C datetime format (YYYY-MM-DDThh:mm:ss+00:00)
            if ($lastmod instanceof \DateTime || $lastmod instanceof \DateTimeInterface) {
                $xml .= "    <lastmod>" . $lastmod->format('Y-m-d\TH:i:s\Z') . "</lastmod>\n";
            } else {
                $xml .= "    <lastmod>" . date('Y-m-d\TH:i:s\Z', strtotime($lastmod)) . "</lastmod>\n";
            }
        } else {
            $xml .= "    <lastmod>" . date('Y-m-d\TH:i:s\Z') . "</lastmod>\n";
        }
        
        $xml .= "  </url>\n";
        
        return $xml;
    }
    
    /**
     * Generate images sitemap for all products
     */
    private function generateImagesSitemap(): string
    {
        $baseUrl = config('app.url');
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
        
        // Get all active products with images
        $products = Product::active()
            ->select('slug', 'image', 'thumbnail', 'name', 'name_en', 'updated_at')
            ->where(function ($query) {
                $query->whereNotNull('image')
                      ->orWhereNotNull('thumbnail');
            })
            ->orderBy('updated_at', 'desc')
            ->get();
        
        foreach ($products as $product) {
            $productUrl = $baseUrl . '/products/' . $product->slug;
            $productName = $product->name_en ?: $product->name;
            
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($productUrl, ENT_XML1, 'UTF-8') . "</loc>\n";
            
            // Add main image if exists
            if ($product->image) {
                $imageUrl = $baseUrl . '/storage/' . $product->image;
                $xml .= $this->generateImageEntry($imageUrl, $productName);
            }
            
            // Add thumbnail if exists and different from main image
            if ($product->thumbnail && $product->thumbnail !== $product->image) {
                $thumbnailUrl = $baseUrl . '/storage/' . $product->thumbnail;
                $xml .= $this->generateImageEntry($thumbnailUrl, $productName);
            }
            
            $xml .= "  </url>\n";
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }
    
    /**
     * Generate image entry for sitemap
     */
    private function generateImageEntry(string $imageUrl, string $title): string
    {
        $xml = "    <image:image>\n";
        $xml .= "      <image:loc>" . htmlspecialchars($imageUrl, ENT_XML1, 'UTF-8') . "</image:loc>\n";
        $xml .= "      <image:title>" . htmlspecialchars($title, ENT_XML1, 'UTF-8') . "</image:title>\n";
        $xml .= "    </image:image>\n";
        
        return $xml;
    }
    
    /**
     * Generate URL entry with images (for main sitemap)
     */
    private function generateUrlEntryWithImages(string $url, string $priority, string $changefreq, array $images = [], $lastmod = null): string
    {
        $xml = "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($url, ENT_XML1, 'UTF-8') . "</loc>\n";
        
        // Add images if provided
        if (!empty($images)) {
            foreach ($images as $image) {
                $xml .= $this->generateImageEntry($image['url'], $image['title']);
            }
        }
        
        $xml .= "    <priority>" . $priority . "</priority>\n";
        $xml .= "    <changefreq>" . $changefreq . "</changefreq>\n";
        
        if ($lastmod) {
            if ($lastmod instanceof \DateTime || $lastmod instanceof \DateTimeInterface) {
                $xml .= "    <lastmod>" . $lastmod->format('Y-m-d\TH:i:s\Z') . "</lastmod>\n";
            } else {
                $xml .= "    <lastmod>" . date('Y-m-d\TH:i:s\Z', strtotime($lastmod)) . "</lastmod>\n";
            }
        } else {
            $xml .= "    <lastmod>" . date('Y-m-d\TH:i:s\Z') . "</lastmod>\n";
        }
        
        $xml .= "  </url>\n";
        
        return $xml;
    }
    
    /**
     * Clear sitemap cache (can be called from models or commands)
     */
    public static function clearCache(): void
    {
        Cache::forget('sitemap.xml');
        Cache::forget('sitemap.index.xml');
        Cache::forget('sitemap.images.xml');
        
        // Clear product sitemaps (clear up to 100 pages)
        for ($i = 1; $i <= 100; $i++) {
            Cache::forget("sitemap.products.{$i}.xml");
        }
    }
}
