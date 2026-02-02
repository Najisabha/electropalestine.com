<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Type;
use App\Models\Company;
use App\Models\Product;
use App\Models\Campaign;
use App\Models\Order;
use App\Models\User;
use App\Models\Review;
use App\Models\UserAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\InvoicePdfService;
use App\Helpers\ActivityLogger;
use App\Models\UserFavorite;
use App\Models\UserReward;

class StoreController extends Controller
{
    public function home(): View
    {
        // Cache الصفحة الرئيسية لمدة 10 دقائق
        $cacheKey = 'store.home.' . app()->getLocale();
        $data = Cache::remember($cacheKey, 600, function () {
            $categories = Category::with([
                'types',
                'products' => fn ($q) => $q->active()->latest()->take(6),
            ])->get();

            // استخدام scopes محسنة
            $featured = Product::withRelations()
                ->newest(8)
                ->get();

            // المنتجات الأكثر مبيعاً: فقط المنتجات التي تم تعليمها كـ \"ضمن المنتجات الأكثر مبيعاً\"
            $bestSelling = Product::withRelations()
                ->bestSelling(12)
                ->get();

            // جميع المنتجات لعرضها في قائمة أسفل شريط الأكثر مبيعاً
            $allProducts = Product::withRelations()
                ->newest(40)
                ->get();

            $campaigns = Campaign::query()
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()->toDateString());
                })
                ->where(function ($q) {
                    $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()->toDateString());
                })
                ->orderByDesc('starts_at')
                ->orderByDesc('created_at')
                ->take(10)
                ->get();

            return compact('categories', 'featured', 'bestSelling', 'campaigns', 'allProducts');
        });

        return view('store.home', $data);
    }

    public function product(Product $product): View
    {
        // تسجيل نشاط عرض المنتج
        if (auth()->check()) {
            ActivityLogger::logProductView($product);
        }
        $product->load(['category.types', 'company', 'type']);

        // Cache التقييمات لمدة 5 دقائق
        $reviewsCacheKey = 'product.reviews.' . $product->id;
        $reviews = Cache::remember($reviewsCacheKey, 300, function () use ($product) {
            return Review::with(['user', 'order'])
                ->whereHas('order', function ($q) use ($product) {
                    $q->where('product_name', $product->name)
                        ->orWhere('items', 'like', '%"product_id":' . $product->id . '%');
                })
                ->latest()
                ->get();
        });

        // تحديث متوسط التقييم وعدد التقييمات فقط إذا تغيرت التقييمات
        // سيتم التحديث تلقائياً عند إضافة/حذف تقييم من خلال Review model events
        // هنا نستخدم البيانات المحفوظة في المنتج فقط

        // Cache المنتجات المشابهة لمدة 15 دقيقة (مع eager loading محسن)
        $relatedCacheKey = 'product.related.' . $product->category_id . '.' . $product->id;
        $related = Cache::remember($relatedCacheKey, 900, function () use ($product) {
            return Product::withRelations()
                ->where('category_id', $product->category_id)
                ->whereKeyNot($product->getKey())
                ->active()
                ->latest('created_at')
                ->take(4)
                ->get();
        });

        return view('store.product', [
            'product' => $product,
            'related' => $related,
            'reviews' => $reviews,
        ]);
    }

    public function productReviews(Product $product): View
    {
        $product->load(['category.types', 'company', 'type']);

        // Cache التقييمات لمدة 5 دقائق
        $reviewsCacheKey = 'product.reviews.' . $product->id;
        $reviews = Cache::remember($reviewsCacheKey, 300, function () use ($product) {
            return Review::with(['user', 'order'])
                ->whereHas('order', function ($q) use ($product) {
                    $q->where('product_name', $product->name)
                        ->orWhere('items', 'like', '%"product_id":' . $product->id . '%');
                })
                ->latest()
                ->get();
        });

        $ratingCount = $reviews->count();
        $ratingAverage = $ratingCount > 0 ? (float) $reviews->avg('rating') : 0.0;

        return view('store.product-reviews', [
            'product' => $product,
            'reviews' => $reviews,
            'ratingCount' => $ratingCount,
            'ratingAverage' => $ratingAverage,
        ]);
    }

    public function category(Category $category): View
    {
        $category->load([
            'types',
            'companies',
            'products' => fn ($q) => $q->active()->with('company'),
        ]);

        // جميع الأنواع التابعة لهذا الصنف
        $types = $category->types;

        // الشركات المرتبطة بالصنف عن طريق جدول الربط
        $companies = $category->companies;

        // المنتجات التابعة للصنف (نستخدمها في الشريط أو الشبكة)
        $products = $category->products()
            ->active()
            ->with('company')
            ->orderByDesc('created_at')
            ->get();

        return view('store.category', compact('category', 'types', 'companies', 'products'));
    }

    public function products(Request $request): View
    {
        // جلب جميع المنتجات مع الفلترة (استخدام scope محسن)
        $query = Product::active()->withRelations();

        // البحث بالاسم
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('name_en', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // فلتر حسب الصنف
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // فلتر حسب النوع
        if ($request->has('type_id') && $request->type_id) {
            $query->where('type_id', $request->type_id);
        }

        // فلتر حسب الشركة
        if ($request->has('company_id') && $request->company_id) {
            $query->where('company_id', $request->company_id);
        }

        // فلتر السعر
        $minPrice = max(0, min(10000, (int)$request->get('min_price', 0)));
        $maxPrice = max(0, min(10000, (int)$request->get('max_price', 10000)));
        if ($minPrice > 0 || $maxPrice < 10000) {
            $query->whereBetween('price', [$minPrice, $maxPrice]);
        }

        // فلتر التقييم
        if ($request->has('min_rating') && $request->min_rating > 0) {
            $query->where('rating_average', '>=', $request->min_rating);
        }

        // فلتر المخزون
        if ($request->has('in_stock')) {
            if ($request->in_stock === '1') {
                $query->where('stock', '>', 0);
            } elseif ($request->in_stock === '0') {
                $query->where('stock', '<=', 0);
            }
        }

        // فلتر المنتجات المميزة
        if ($request->has('featured') && $request->featured === '1') {
            $query->where('is_best_seller', true);
        }

        // الترتيب
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'best_selling':
                $query->orderBy('sales_count', 'desc');
                break;
            case 'most_popular':
                $query->orderBy('rating_average', 'desc')->orderBy('rating_count', 'desc');
                break;
            case 'highest_rated':
                $query->orderBy('rating_average', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // تحديد عدد العناصر في الصفحة
        $perPage = in_array((int)$request->get('per_page', 12), [9, 12, 24, 48]) 
            ? (int)$request->get('per_page', 12) 
            : 12;
        
        $products = $query->paginate($perPage)->withQueryString();

        // Cache البيانات للفلترة لمدة ساعة (نادراً ما تتغير)
        $locale = app()->getLocale();
        $categories = Cache::remember("filter.categories.{$locale}", 3600, function () {
            return Category::select('id', 'name', 'slug')->orderBy('name')->get();
        });
        $types = Cache::remember("filter.types.{$locale}", 3600, function () {
            return Type::select('id', 'name', 'slug', 'category_id')
                ->with('category:id,name,slug')
                ->orderBy('name')
                ->get();
        });
        $companies = Cache::remember("filter.companies.{$locale}", 3600, function () {
            return Company::select('id', 'name')->orderBy('name')->get();
        });

        return view('store.products', [
            'products' => $products,
            'categories' => $categories,
            'types' => $types,
            'companies' => $companies,
            'search' => $request->get('search', ''),
            'categoryId' => $request->get('category_id'),
            'typeId' => $request->get('type_id'),
            'companyId' => $request->get('company_id'),
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'minRating' => $request->get('min_rating', 0),
            'inStock' => $request->get('in_stock'),
            'featured' => $request->get('featured'),
            'sort' => $sort,
            'perPage' => $perPage,
        ]);
    }

    public function typeProducts(Type $type): View
    {
        $type->load(['category', 'products' => fn ($q) => $q->active()->with(['company', 'category'])]);
        
        $category = $type->category;

        // جلب جميع المنتجات التابعة لهذا النوع
        $query = Product::where('type_id', $type->id)
            ->active()
            ->with(['company', 'category', 'type']);

        // تطبيق الفلاتر من Request
        $sort = request()->get('sort', 'newest');
        $minPrice = max(0, min(10000, (int)request()->get('min_price', 0)));
        $maxPrice = max(0, min(10000, (int)request()->get('max_price', 10000)));
        $minRating = request()->get('min_rating', 0);
        $companyId = request()->get('company_id', null);
        $inStock = request()->get('in_stock', null);
        $perPage = request()->get('per_page', 9);

        // فلتر السعر
        if ($minPrice > 0 || $maxPrice < 10000) {
            $query->whereBetween('price', [$minPrice, $maxPrice]);
        }

        // فلتر التقييم
        if ($minRating > 0) {
            $query->where('rating_average', '>=', $minRating);
        }

        // فلتر الشركة
        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        // فلتر المخزون
        if ($inStock === '1') {
            $query->where('stock', '>', 0);
        } elseif ($inStock === '0') {
            $query->where('stock', '<=', 0);
        }

        // الترتيب
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'best_selling':
                $query->orderBy('sales_count', 'desc');
                break;
            case 'most_popular':
                $query->orderBy('rating_average', 'desc')->orderBy('rating_count', 'desc');
                break;
            case 'highest_rated':
                $query->orderBy('rating_average', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // تحديد عدد العناصر في الصفحة (9, 15, 30)
        $perPageOptions = [9, 15, 30];
        $perPage = in_array((int)$perPage, $perPageOptions) ? (int)$perPage : 9;
        $products = $query->paginate($perPage);

        // جلب جميع الشركات المتاحة للفلتر (الشركات التي لديها منتجات في هذا النوع)
        $companies = Company::whereHas('products', function ($q) use ($type) {
            $q->where('type_id', $type->id)->where('is_active', true);
        })->orderBy('name')->get();

        return view('store.type-products', [
            'type' => $type,
            'category' => $category,
            'products' => $products,
            'companies' => $companies,
            'sort' => $sort,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'minRating' => $minRating,
            'companyId' => $companyId,
            'inStock' => $inStock,
            'perPage' => $perPage,
        ]);
    }

    public function companyProducts(Company $company): View
    {
        $company->load(['categories', 'types', 'products' => fn ($q) => $q->active()->with(['category', 'type'])]);

        // جلب جميع المنتجات التابعة لهذه الشركة
        $query = Product::where('company_id', $company->id)
            ->active()
            ->withRelations();

        // تطبيق الفلاتر من Request
        $sort = request()->get('sort', 'newest');
        $minPrice = max(0, min(10000, (int)request()->get('min_price', 0)));
        $maxPrice = max(0, min(10000, (int)request()->get('max_price', 10000)));
        $minRating = request()->get('min_rating', 0);
        $typeId = request()->get('type_id', null);
        $categoryId = request()->get('category_id', null);
        $inStock = request()->get('in_stock', null);
        $perPage = request()->get('per_page', 9);

        // فلتر السعر
        if ($minPrice > 0 || $maxPrice < 10000) {
            $query->whereBetween('price', [$minPrice, $maxPrice]);
        }

        // فلتر التقييم
        if ($minRating > 0) {
            $query->where('rating_average', '>=', $minRating);
        }

        // فلتر النوع
        if ($typeId) {
            $query->where('type_id', $typeId);
        }

        // فلتر الصنف
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // فلتر المخزون
        if ($inStock === '1') {
            $query->where('stock', '>', 0);
        } elseif ($inStock === '0') {
            $query->where('stock', '<=', 0);
        }

        // الترتيب
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'best_selling':
                $query->orderBy('sales_count', 'desc');
                break;
            case 'most_popular':
                $query->orderBy('rating_average', 'desc')->orderBy('rating_count', 'desc');
                break;
            case 'highest_rated':
                $query->orderBy('rating_average', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // تحديد عدد العناصر في الصفحة (9, 15, 30)
        $perPageOptions = [9, 15, 30];
        $perPage = in_array((int)$perPage, $perPageOptions) ? (int)$perPage : 9;
        $products = $query->paginate($perPage);

        // جلب جميع الأنواع المتاحة للفلتر (الأنواع التي لديها منتجات لهذه الشركة)
        $types = Type::whereHas('products', function ($q) use ($company) {
            $q->where('company_id', $company->id)->where('is_active', true);
        })->with('category')->orderBy('name')->get();

        // جلب جميع الأصناف المتاحة للفلتر
        $categories = Category::whereHas('products', function ($q) use ($company) {
            $q->where('company_id', $company->id)->where('is_active', true);
        })->orderBy('name')->get();

        return view('store.company-products', [
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
        ]);
    }

    public function cart(): View
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return view('store.cart', ['cartItems' => [], 'total' => 0]);
        }

        // جلب جميع المنتجات في query واحدة بدلاً من loop (محسن)
        $productIds = array_keys($cart);
        $products = Product::withRelations()
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $cartItems = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = $products->get($productId);
            if ($product) {
                $cartItems[$productId] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity,
                ];
                $total += $product->price * $quantity;
            }
        }

        return view('store.cart', compact('cartItems', 'total'));
    }

    public function addToCart(Request $request, Product $product): RedirectResponse
    {
        $cart = session()->get('cart', []);
        $quantity = $request->input('quantity', 1);

        $currentInCart = $cart[$product->id] ?? 0;
        $requestedTotal = $currentInCart + $quantity;

        // لا نسمح بأن تتجاوز الكمية المتاحة في المخزون
        if ($product->stock <= 0) {
            return back()->withErrors(['error' => 'هذا المنتج غير متوفر حالياً في المخزون.']);
        }

        if ($requestedTotal > $product->stock) {
            $cart[$product->id] = (int) $product->stock;
            session()->put('cart', $cart);

            return back()->withErrors([
                'error' => 'لا يمكن طلب أكثر من الكمية المتوفرة في المخزون. تم تعيين الكمية في السلة إلى ' . $product->stock . '.',
            ]);
        }

        $cart[$product->id] = $requestedTotal;

        session()->put('cart', $cart);

        // تسجيل نشاط إضافة إلى السلة
        if (auth()->check()) {
            ActivityLogger::logAddToCart($product, $quantity);
        }

        return back()->with('status', 'تم إضافة المنتج إلى السلة بنجاح.');
    }

    public function removeFromCart(Product $product): RedirectResponse
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            unset($cart[$product->id]);
            session()->put('cart', $cart);
            
            // تسجيل نشاط حذف من السلة
            if (auth()->check()) {
                ActivityLogger::logRemoveFromCart($product);
            }
        }

        return back()->with('status', 'تم حذف المنتج من السلة.');
    }

    public function updateCart(Request $request, Product $product): RedirectResponse
    {
        $quantity = $request->input('quantity', 1);

        if ($quantity <= 0) {
            return $this->removeFromCart($product);
        }

        // لا نسمح بأن تتجاوز الكمية المتاحة في المخزون
        if ($product->stock <= 0) {
            return back()->withErrors(['error' => 'هذا المنتج غير متوفر حالياً في المخزون.']);
        }

        if ($quantity > $product->stock) {
            $quantity = (int) $product->stock;
            $cart = session()->get('cart', []);
            $cart[$product->id] = $quantity;
            session()->put('cart', $cart);

            return back()->withErrors([
                'error' => 'لا يمكن طلب أكثر من الكمية المتوفرة في المخزون. تم تعيين الكمية في السلة إلى ' . $product->stock . '.',
            ]);
        }

        $cart = session()->get('cart', []);
        $cart[$product->id] = $quantity;
        session()->put('cart', $cart);

        // تسجيل نشاط تحديث السلة
        if (auth()->check()) {
            ActivityLogger::logUpdateCart($product, $quantity);
        }

        return back()->with('status', 'تم تحديث الكمية بنجاح.');
    }

    public function clearCart(): RedirectResponse
    {
        // تسجيل نشاط تفريغ السلة
        if (auth()->check()) {
            ActivityLogger::logClearCart();
        }
        
        session()->forget('cart');
        return redirect()->route('store.cart')->with('status', 'تم تفريغ السلة.');
    }

    /**
     * صفحة إتمام الطلب للسلة بالكامل (طلب واحد يحتوي على عدة منتجات).
     */
    public function checkoutCart(Request $request): RedirectResponse|View
    {
        if (!auth()->check()) {
            return redirect()->route('login')->withErrors(['error' => 'يجب تسجيل الدخول لإتمام الطلب.']);
        }

        // منع إتمام الطلب بدون عنوان
        $user = auth()->user();
        if (!$user->defaultAddress) {
            return redirect()->route('store.account-settings')
                ->withErrors(['error' => 'يجب إضافة عنوانك أولاً قبل إتمام أي طلب.'])
                ->with('status', null);
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('store.cart')->withErrors(['error' => 'السلة فارغة حالياً.']);
        }

        $productIds = array_keys($cart);
        $products = Product::withRelations()
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $items = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = $products->get($productId);
            if (!$product) {
                continue;
            }
            $subtotal = $product->price * $quantity;
            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];
            $total += $subtotal;
        }

        if (empty($items)) {
            return redirect()->route('store.cart')->withErrors(['error' => 'لا توجد منتجات صالحة في السلة.']);
        }

        $user = auth()->user();
        $userBalance = $user->balance ?? 0;
        $userPoints = $user->points ?? 0;

        return view('store.cart-checkout', [
            'items' => $items,
            'total' => $total,
            'userBalance' => $userBalance,
            'userPoints' => $userPoints,
        ]);
    }

    public function checkout(Request $request): View|RedirectResponse
    {
        $productId = $request->input('product');
        $quantity = $request->input('quantity', 1);

        if (!$productId) {
            abort(404, 'المنتج غير موجود');
        }

        $product = Product::withRelations()->findOrFail($productId);

        // منع إتمام الطلب بدون عنوان
        $user = auth()->user();
        if ($user && !$user->defaultAddress) {
            return redirect()->route('store.account-settings')
                ->withErrors(['error' => 'يجب إضافة عنوانك أولاً قبل إتمام أي طلب.'])
                ->with('status', null);
        }
        $total = $product->price * $quantity;
        $userBalance = $user ? ($user->balance ?? 0) : 0;
        $userPoints = $user ? ($user->points ?? 0) : 0;

        return view('store.checkout', compact('product', 'quantity', 'total', 'userBalance', 'userPoints'));
    }

    public function accountSettings(): View
    {
        $user = auth()->user();
        
        // تحميل العناوين مباشرة من قاعدة البيانات
        if ($user) {
            try {
                // تحميل العناوين مع المستخدم
                $user->load('addresses');
            } catch (\Exception $e) {
                // في حالة وجود خطأ في قاعدة البيانات (مثل corruption)، نستخدم مصفوفة فارغة
                \Log::error('Error loading user addresses: ' . $e->getMessage());
                $user->setRelation('addresses', collect([]));
            }
        }
        
        return view('store.account-settings', compact('user'));
    }

    /**
     * حذف حساب المستخدم.
     */
    public function deleteAccount(Request $request): RedirectResponse
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'يجب تسجيل الدخول أولاً.']);
        }

        // التحقق من أن المستخدم يريد حذف حسابه
        $request->validate([
            'confirm_delete' => 'required|in:1',
        ], [
            'confirm_delete.required' => 'يجب تأكيد حذف الحساب.',
            'confirm_delete.in' => 'يجب تأكيد حذف الحساب.',
        ]);

        try {
            // حذف صورة الهوية إن وجدت
            if ($user->id_image) {
                try {
                    \App\Helpers\ImageHelper::delete($user->id_image, 'public');
                } catch (\Exception $e) {
                    \Log::warning('Could not delete user ID image: ' . $e->getMessage());
                }
            }

            // تسجيل الخروج قبل حذف الحساب
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // حذف الحساب (سيتم حذف البيانات المرتبطة تلقائياً بسبب cascade)
            $user->delete();

            return redirect()->route('home')->with('status', 'تم حذف حسابك بنجاح. نأسف لرؤيتك تغادرنا.');
        } catch (\Exception $e) {
            \Log::error('Error deleting user account: ' . $e->getMessage());
            return redirect()->route('store.account-settings')
                ->withErrors(['error' => 'حدث خطأ أثناء حذف الحساب. يرجى المحاولة مرة أخرى أو التواصل مع الدعم.']);
        }
    }

    /**
     * صفحة استبدال نقاط الولاء.
     */
    public function points(): View|RedirectResponse
    {
        if (!auth()->check()) {
            return redirect()->route('login')->withErrors(['error' => __('common.login_required')]);
        }

        $user = auth()->user();
        $userPoints = (int) ($user->points ?? 0);
        $userBalance = (float) ($user->balance ?? 0);

        // قيمة النقطة الواحدة بالدولار (يمكن تعديلها من لوحة التحكم لاحقاً)
        $pointValue = 0.1;
        $pointsValue = $userPoints * $pointValue;

        // مستويات الولاء
        $tiers = collect([
            ['key' => 'bronze', 'label' => __('common.tier_bronze'), 'threshold' => 0],
            ['key' => 'silver', 'label' => __('common.tier_silver'), 'threshold' => 500],
            ['key' => 'gold', 'label' => __('common.tier_gold'), 'threshold' => 1000],
            ['key' => 'platinum', 'label' => __('common.tier_platinum'), 'threshold' => 2000],
        ]);

        $currentTier = $tiers->last(fn($tier) => $userPoints >= $tier['threshold']);
        $nextTier = $tiers->first(fn($tier) => $tier['threshold'] > $userPoints);

        $progressToNext = $nextTier
            ? min(100, round(($userPoints / max(1, $nextTier['threshold'])) * 100, 1))
            : 100;
        $pointsToNext = $nextTier ? max(0, $nextTier['threshold'] - $userPoints) : 0;

        $rewards = $this->buildRewardsDataset();
        $history = $this->buildPointsHistory();

        return view('store.points', [
            'userPoints' => $userPoints,
            'userBalance' => $userBalance,
            'pointValue' => $pointValue,
            'pointsValue' => $pointsValue,
            'currentTier' => $currentTier,
            'nextTier' => $nextTier,
            'progressToNext' => $progressToNext,
            'pointsToNext' => $pointsToNext,
            'rewards' => $rewards,
            'history' => $history,
            'tiers' => $tiers,
        ]);
    }

    /**
     * إنشاء أو تحديث عنوان (يستخدمه نموذج العناوين المتعددة).
     */
    public function saveAddress(Request $request): RedirectResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return redirect()->route('store.account-settings')
                    ->withErrors(['error' => 'يجب تسجيل الدخول أولاً.']);
            }

            $data = $request->validate([
                'address_id' => ['nullable', 'integer', 'exists:user_addresses,id'],
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'city' => ['nullable', 'string', 'max:255'],
                'governorate' => ['nullable', 'string', 'max:255'],
                'zip_code' => ['nullable', 'string', 'max:20'],
                'country_code' => ['nullable', 'string', 'max:10'],
                'phone' => ['required', 'string', 'max:50'],
                'street' => ['nullable', 'string', 'max:500'],
                'is_default' => ['nullable', 'boolean'],
            ]);

            // تحويل is_default من checkbox (قد يكون "1" أو null) إلى boolean
            $data['is_default'] = !empty($data['is_default']) && $data['is_default'] !== '0';

            // التأكد من إضافة user_id للبيانات
            $data['user_id'] = $user->id;
            
            // إجبار commit فوري - التأكد من عدم وجود معاملة نشطة
            DB::statement('SET AUTOCOMMIT=1');
            $connection = DB::connection();
            if ($connection->transactionLevel() > 0) {
                Log::warning('Active transaction found before save, committing', [
                    'level' => $connection->transactionLevel()
                ]);
                while ($connection->transactionLevel() > 0) {
                    $connection->commit();
                }
            }

            // إنشاء أو تحديث
            if (!empty($data['address_id'])) {
                $address = $user->addresses()->whereKey($data['address_id'])->firstOrFail();
                $address->fill($data);
                $saved = $address->save(); // حفظ فوري بعد التحديث
                if (!$saved) {
                    throw new \Exception('فشل حفظ العنوان في قاعدة البيانات');
                }
            } else {
                // إذا كان هذا أول عنوان للمستخدم، اجعله افتراضياً تلقائياً
                if ($user->addresses()->count() === 0) {
                    $data['is_default'] = true;
                }
                // إنشاء العنوان مع التأكد من حفظ user_id
                $address = new UserAddress($data);
                $address->user_id = $user->id;
                $saved = $address->save(); // حفظ فوري بعد الإنشاء
                if (!$saved) {
                    throw new \Exception('فشل حفظ العنوان في قاعدة البيانات');
                }
                
                // التحقق من أن العنوان تم إنشاؤه فعلاً
                if (!$address->id) {
                    throw new \Exception('فشل إنشاء العنوان - لا يوجد معرف');
                }
            }
            
            // إجبار commit فوري - التأكد من أن البيانات محفوظة
            $pdo = DB::connection()->getPdo();
            if ($pdo->inTransaction()) {
                $pdo->commit();
            }

            // إدارة العنوان الافتراضي
            if ($data['is_default']) {
                $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
                $address->is_default = true;
                $saved = $address->save(); // حفظ التحديث
                if (!$saved) {
                    throw new \Exception('فشل تحديث العنوان الافتراضي');
                }
            } else {
                // إذا لم يتم تحديد العنوان كافتراضي وكان هذا العنوان الوحيد، اجعله افتراضياً
                if ($user->addresses()->where('id', '!=', $address->id)->count() === 0) {
                    $address->is_default = true;
                    $saved = $address->save(); // حفظ التحديث
                    if (!$saved) {
                        throw new \Exception('فشل تحديث العنوان الافتراضي');
                    }
                }
            }

            // مزامنة العنوان الافتراضي مع حقول المستخدم الحالية لاستخدامها في الشحن / الفواتير
            if ($address->is_default) {
                $user->city = $address->city;
                $user->governorate = $address->governorate;
                $user->zip_code = $address->zip_code;
                $user->country_code = $address->country_code;
                $user->phone = $address->phone;
                $user->address = $address->street;
                $user->save();
            }

            // تحديث نموذج العنوان من قاعدة البيانات للتأكد من البيانات المحدثة
            $address->refresh();
            
            // التحقق من أن العنوان موجود فعلاً في قاعدة البيانات
            $verifyAddress = UserAddress::find($address->id);
            if (!$verifyAddress) {
                throw new \Exception('العنوان غير موجود في قاعدة البيانات بعد الحفظ');
            }
            
            // إعادة تحميل المستخدم والعناوين من قاعدة البيانات للتأكد من البيانات المحدثة
            $user->refresh();
            $user->load('addresses');
            
            // التحقق من أن العنوان موجود في علاقة المستخدم
            $addressInRelation = $user->addresses->where('id', $address->id)->first();
            if (!$addressInRelation) {
                Log::warning('Address saved but not found in user relation', [
                    'user_id' => $user->id,
                    'address_id' => $address->id
                ]);
            }

            // تسجيل النجاح في الـ log للتأكد من الحفظ
            Log::info('Address saved successfully', [
                'user_id' => $user->id,
                'address_id' => $address->id,
                'address' => $address->toArray(),
                'total_addresses' => $user->addresses->count(),
                'verified_in_db' => $verifyAddress !== null,
                'verified_in_relation' => $addressInRelation !== null
            ]);

            return redirect()->route('store.account-settings')
                ->with('status', __('addresses.saved_successfully'));
        } catch (\Exception $e) {
            // تسجيل الخطأ في الـ log
            Log::error('Error saving address', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('store.account-settings')
                ->withErrors(['error' => 'حدث خطأ أثناء حفظ العنوان: ' . $e->getMessage()]);
        }
    }

    public function setDefaultAddress(Request $request, \App\Models\UserAddress $address): RedirectResponse
    {
        try {
            $user = $request->user();

            abort_unless($address->user_id === $user->id, 403);

            // إجبار commit فوري - التأكد من عدم وجود معاملة نشطة
            DB::statement('SET AUTOCOMMIT=1');
            $connection = DB::connection();
            if ($connection->transactionLevel() > 0) {
                Log::warning('Active transaction found before set default, committing', [
                    'level' => $connection->transactionLevel()
                ]);
                while ($connection->transactionLevel() > 0) {
                    $connection->commit();
                }
            }

            $user->addresses()->update(['is_default' => false]);
            $address->is_default = true;
            $saved = $address->save();

            if (!$saved) {
                throw new \Exception('فشل تحديث العنوان الافتراضي');
            }

            // مزامنة مع حقول المستخدم
            $user->city = $address->city;
            $user->governorate = $address->governorate;
            $user->zip_code = $address->zip_code;
            $user->country_code = $address->country_code;
            $user->phone = $address->phone;
            $user->address = $address->street;
            $user->save();

            // إجبار commit فوري - التأكد من أن التحديث تم
            $pdo = DB::connection()->getPdo();
            if ($pdo->inTransaction()) {
                $pdo->commit();
            }

            // تحديث العنوان من قاعدة البيانات
            $address->refresh();

            Log::info('Address set as default successfully', [
                'user_id' => $user->id,
                'address_id' => $address->id
            ]);

            return redirect()->route('store.account-settings')
                ->with('status', 'تم تعيين العنوان الافتراضي بنجاح.');
        } catch (\Exception $e) {
            Log::error('Error setting default address', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('store.account-settings')
                ->withErrors(['error' => 'حدث خطأ أثناء تعيين العنوان الافتراضي: ' . $e->getMessage()]);
        }
    }

    public function destroyAddress(Request $request, \App\Models\UserAddress $address): RedirectResponse
    {
        try {
            $user = $request->user();
            abort_unless($address->user_id === $user->id, 403);

            // إجبار commit فوري - التأكد من عدم وجود معاملة نشطة
            DB::statement('SET AUTOCOMMIT=1');
            $connection = DB::connection();
            if ($connection->transactionLevel() > 0) {
                Log::warning('Active transaction found before delete, committing', [
                    'level' => $connection->transactionLevel()
                ]);
                while ($connection->transactionLevel() > 0) {
                    $connection->commit();
                }
            }

            $wasDefault = $address->is_default;
            $addressId = $address->id;
            $deleted = $address->delete();

            if (!$deleted) {
                throw new \Exception('فشل حذف العنوان من قاعدة البيانات');
            }

            // إجبار commit فوري - التأكد من أن الحذف تم
            $pdo = DB::connection()->getPdo();
            if ($pdo->inTransaction()) {
                $pdo->commit();
            }

            // التحقق من أن العنوان تم حذفه فعلاً
            $verifyDeleted = UserAddress::find($addressId);
            if ($verifyDeleted) {
                throw new \Exception('العنوان لا يزال موجوداً في قاعدة البيانات بعد الحذف');
            }

            // إذا حُذف العنوان الافتراضي، أزل البيانات المنسوخة من المستخدم (اختياري)
            if ($wasDefault) {
                $user->city = null;
                $user->governorate = null;
                $user->zip_code = null;
                $user->country_code = null;
                $user->address = null;
                $user->save();
                
                // commit فوري
                if ($pdo->inTransaction()) {
                    $pdo->commit();
                }
            }

            Log::info('Address deleted successfully', [
                'user_id' => $user->id,
                'address_id' => $addressId,
                'was_default' => $wasDefault
            ]);

            return redirect()->route('store.account-settings')
                ->with('status', 'تم حذف العنوان بنجاح.');
        } catch (\Exception $e) {
            Log::error('Error deleting address', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('store.account-settings')
                ->withErrors(['error' => 'حدث خطأ أثناء حذف العنوان: ' . $e->getMessage()]);
        }
    }

    public function updateAddress(Request $request): RedirectResponse
    {
        $user = auth()->user();
        
        // التحقق من اكتمال التسجيل - إذا كان مكتملاً، لا يمكن تعديل المعلومات الشخصية
        $isRegistrationComplete = $user->isRegistrationComplete();
        
        $validationRules = [
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'city' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'governorate' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'secondary_address' => ['nullable', 'string', 'max:500'],
            'zip_code' => ['nullable', 'string', 'max:20'],
            'country_code' => ['nullable', 'string', 'max:10'],
            'phone' => ['required', 'string', 'max:50'],
        ];
        
        // إذا لم يكمل التسجيل، يمكن تعديل المعلومات الشخصية
        if (!$isRegistrationComplete) {
            $validationRules = array_merge($validationRules, [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'whatsapp_prefix' => ['required', 'string', 'max:10'],
                'birth_year' => ['nullable', 'integer', 'min:1900', 'max:' . now()->year],
                'birth_month' => ['nullable', 'integer', 'between:1,12'],
                'birth_day' => ['nullable', 'integer', 'between:1,31'],
            ]);
        } else {
            // إذا كان التسجيل مكتملاً، يمكن تحديث الاسم فقط إذا كان موجوداً
            $validationRules['first_name'] = ['sometimes', 'string', 'max:255'];
            $validationRules['last_name'] = ['sometimes', 'string', 'max:255'];
        }

        $data = $request->validate($validationRules);

        // إذا لم يكمل التسجيل، تحديث المعلومات الشخصية
        if (!$isRegistrationComplete) {
            $user->first_name = $data['first_name'] ?? $user->first_name;
            $user->last_name = $data['last_name'] ?? $user->last_name;
            $user->whatsapp_prefix = $data['whatsapp_prefix'] ?? $user->whatsapp_prefix;
            $user->birth_year = $data['birth_year'] ?? $user->birth_year;
            $user->birth_month = $data['birth_month'] ?? $user->birth_month;
            $user->birth_day = $data['birth_day'] ?? $user->birth_day;
        }

        // إجبار commit فوري - التأكد من عدم وجود معاملة نشطة
        DB::statement('SET AUTOCOMMIT=1');
        $connection = DB::connection();
        if ($connection->transactionLevel() > 0) {
            Log::warning('Active transaction found before update address, committing', [
                'level' => $connection->transactionLevel()
            ]);
            while ($connection->transactionLevel() > 0) {
                $connection->commit();
            }
        }

        // تحديث بيانات العنوان والمعلومات الأخرى
        $user->fill($data);
        
        // تحديث حقل name الكامل ليتطابق مع الاسم الأول والأخير
        $user->name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
        $saved = $user->save();

        if (!$saved) {
            return redirect()->route('store.account-settings')
                ->withErrors(['error' => 'فشل حفظ البيانات.']);
        }

        // إجبار commit فوري - التأكد من أن التحديث تم
        $pdo = DB::connection()->getPdo();
        if ($pdo->inTransaction()) {
            $pdo->commit();
        }

        return redirect()->route('store.account-settings')
            ->with('status', 'تم حفظ بياناتك بنجاح.');
    }

    public function uploadIdImage(Request $request): RedirectResponse
    {
        $user = auth()->user();
        
        // التحقق: إذا كانت الحالة "موثق"، لا يمكن رفع/تعديل الصورة إلا من ADMIN
        $status = $user->id_verified_status ?? 'unverified';
        if ($status === 'verified') {
            return back()->withErrors(['id_image' => 'لا يمكن تعديل صورة الهوية لأنها موثقة. يرجى التواصل مع المدير.']);
        }
        
        $data = $request->validate([
            'id_image' => ['required', 'image', 'max:2048'],
        ]);

        // حذف الصورة القديمة إذا كانت موجودة
        if ($user->id_image) {
            \App\Helpers\ImageHelper::delete($user->id_image, 'public');
        }

        // رفع الصورة الجديدة
        $idImagePath = \App\Helpers\ImageHelper::storeWithSequentialName($request->file('id_image'), 'ids', 'public');
        if (!$idImagePath) {
            return back()->withErrors(['id_image' => 'فشل رفع صورة الهوية. يرجى التحقق من صلاحيات المجلدات.']);
        }

        // تحديث المستخدم: رفع الصورة وتغيير الحالة إلى "قيد التنفيذ"
        $user->id_image = $idImagePath;
        $user->id_verified_status = 'pending'; // الحالة الافتراضية عند رفع صورة
        $user->save();

        return redirect()->route('store.account-settings')
            ->with('status', 'تم رفع صورة الهوية بنجاح. ستكون الحالة "قيد التنفيذ" حتى يتم مراجعتها من قبل المدير.');
    }

    public function deleteIdImage(Request $request): RedirectResponse
    {
        $user = auth()->user();
        
        // التحقق: إذا كانت الحالة "موثق"، لا يمكن حذف الصورة إلا من ADMIN
        $status = $user->id_verified_status ?? 'unverified';
        if ($status === 'verified') {
            return back()->withErrors(['error' => 'لا يمكن حذف صورة الهوية لأنها موثقة. يرجى التواصل مع المدير.']);
        }

        if ($user->id_image) {
            // حذف الصورة من التخزين
            \App\Helpers\ImageHelper::delete($user->id_image, 'public');
            
            // حذف المسار من قاعدة البيانات
            $user->id_image = null;
            $user->id_verified_status = 'unverified'; // تغيير الحالة إلى غير موثق
            $user->save();

            return redirect()->route('store.account-settings')
                ->with('status', 'تم حذف صورة الهوية بنجاح.');
        }

        return back()->withErrors(['error' => 'لا توجد صورة هوية للحذف.']);
    }

    public function updateCurrency(Request $request): RedirectResponse
    {
        $user = auth()->user();
        
        $data = $request->validate([
            'currency' => ['required', 'string', 'in:USD,ILS,JOD'],
        ]);

        $oldCurrency = $user->preferred_currency ?? 'USD';
        $user->preferred_currency = $data['currency'];
        $user->save();

        // حفظ العملة في session أيضاً
        session(['preferred_currency' => $data['currency']]);
        
        // تحديث العملة في الـ auth user object
        auth()->user()->preferred_currency = $data['currency'];
        
        // تسجيل نشاط تغيير العملة
        if ($oldCurrency !== $data['currency']) {
            ActivityLogger::logCurrencyChange($oldCurrency, $data['currency']);
        }

        return redirect()->route('store.account-settings')
            ->with('status', 'تم حفظ العملة المفضلة بنجاح.');
    }

    public function getExchangeRates(): \Illuminate\Http\JsonResponse
    {
        try {
            // استخدام API مجاني لأسعار الصرف (مثال: exchangerate-api.com أو fixer.io)
            // هنا سنستخدم قيم افتراضية أو API مجاني
            
            // يمكن استخدام API مثل: https://api.exchangerate-api.com/v4/latest/USD
            $usdToIls = 3.65; // قيمة افتراضية - يجب استبدالها بـ API حقيقي
            $usdToJod = 0.71; // قيمة افتراضية - يجب استبدالها بـ API حقيقي
            $ilsToJod = $usdToJod / $usdToIls;

            // محاولة الحصول على أسعار حقيقية من API
            try {
                $response = @file_get_contents('https://api.exchangerate-api.com/v4/latest/USD');
                if ($response) {
                    $data = json_decode($response, true);
                    if (isset($data['rates'])) {
                        $usdToIls = $data['rates']['ILS'] ?? $usdToIls;
                        $usdToJod = $data['rates']['JOD'] ?? $usdToJod;
                        $ilsToJod = $usdToJod / $usdToIls;
                    }
                }
            } catch (\Exception $e) {
                // في حالة فشل API، نستخدم القيم الافتراضية
                Log::warning('Failed to fetch exchange rates from API: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'rates' => [
                    'USD_to_ILS' => round($usdToIls, 4),
                    'USD_to_JOD' => round($usdToJod, 4),
                    'ILS_to_JOD' => round($ilsToJod, 4),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching exchange rates: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'فشل تحميل أسعار الصرف',
            ], 500);
        }
    }

    public function myOrders(): View
    {
        $user = auth()->user();
        $orders = \App\Models\Order::with('review')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(10);
        return view('store.my-orders', compact('orders'));
    }

    public function showReviewForm(Order $order): View
    {
        $user = auth()->user();

        abort_unless($order->user_id === $user->id, 403);
        abort_unless($order->status === 'confirmed', 403);

        $order->load('review');

        return view('store.review-order', [
            'order' => $order,
        ]);
    }

    public function submitReview(Request $request, Order $order): RedirectResponse
    {
        $user = auth()->user();

        if ($order->user_id !== $user->id || $order->status !== 'confirmed') {
            abort(403);
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $existing = $order->review;
        $created = false;

        if ($existing) {
            $existing->update($data);
        } else {
            $created = true;
            $review = new Review($data);
            $review->user_id = $user->id;
            $order->review()->save($review);
        }

        // إضافة نقاط للمستخدم فقط عند أول تقييم جديد
        if ($created) {
            $points = (int) config('catalog.review_points', 5);
            if ($points > 0) {
                $user->increment('points', $points);
            }
        }

        // تحديث تقييمات المنتجات المرتبطة بهذه الطلبية (rating_average, rating_count)
        $items = $order->items;
        $productsToUpdate = collect();

        if (is_array($items) && !empty($items)) {
            $productIds = collect($items)
                ->pluck('product_id')
                ->filter()
                ->unique()
                ->values();

            if ($productIds->isNotEmpty()) {
                $productsToUpdate = Product::whereIn('id', $productIds)->get();
            }
        }

        // في حال كانت الطلبية القديمة لا تحتوي product_id في items، نستخدم اسم المنتج
        if ($productsToUpdate->isEmpty() && $order->product_name) {
            $fallbackProduct = Product::where('name', $order->product_name)->first();
            if ($fallbackProduct) {
                $productsToUpdate = collect([$fallbackProduct]);
            }
        }

        foreach ($productsToUpdate as $product) {
            // جميع التقييمات لكل الطلبات التي تحتوي هذا المنتج (حسب الاسم أو product_id داخل items)
            $query = Review::whereHas('order', function ($q) use ($product) {
                $q->where('product_name', $product->name)
                    ->orWhere('items', 'like', '%"product_id":' . $product->id . '%');
            });

            $ratingCount = (int) $query->count();
            $ratingAverage = $ratingCount > 0 ? (float) $query->avg('rating') : 0.0;

            $product->rating_count = $ratingCount;
            $product->rating_average = $ratingAverage;
            $product->save();
        }

        return redirect()->route('store.my-orders')
            ->with('status', __('common.review_saved_successfully'));
    }

    public function myComments(): View
    {
        $user = auth()->user();

        $reviews = Review::with('order')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $comments = $reviews->map(function (Review $review) {
            $order = $review->order;
            $productName = $order?->product_name;

            $items = $order?->items;
            if (is_array($items) && !empty($items)) {
                $productName = $items[0]['name'] ?? $productName;
            }

            return (object) [
                'product_name' => $productName ?? 'طلبية',
                'rating' => $review->rating,
                'comment' => $review->comment,
                'created_at' => $review->created_at,
            ];
        });

        return view('store.my-comments', compact('comments'));
    }

    public function downloadInvoice(Order $order)
    {
        $user = auth()->user();
        
        // التأكد من أن الطلبية تخص المستخدم المسجل دخوله
        if ($order->user_id !== $user->id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الفاتورة.');
        }

        $order->load('user');

        // استخدام TCPDF لدعم أفضل للعربية
        $pdfService = new InvoicePdfService();
        $pdf = $pdfService->generateInvoice($order, $order->user);
        
        return response($pdf->Output('invoice_' . $order->id . '.pdf', 'D'), 200)
            ->header('Content-Type', 'application/pdf');
    }

    public function confirmOrder(Request $request): RedirectResponse
    {
        if (!auth()->check()) {
            return redirect()->route('login')->withErrors(['error' => 'يجب تسجيل الدخول لإتمام الطلب.']);
        }

        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'total' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string'],
        ]);

        $user = auth()->user();

        // منع إنشاء أي طلب بدون عنوان
        if (!$user->defaultAddress) {
            return redirect()->route('store.account-settings')
                ->withErrors(['error' => 'يجب إضافة عنوانك أولاً قبل تأكيد الطلب.'])
                ->with('status', null);
        }

        $product = Product::findOrFail($data['product_id']);

        // التحقق من توفر الكمية المطلوبة في المخزون
        if ($product->stock <= 0) {
            return back()->withErrors(['error' => 'هذا المنتج غير متوفر حالياً في المخزون.']);
        }

        if ($data['quantity'] > $product->stock) {
            return back()->withErrors([
                'error' => 'الكمية المطلوبة (' . $data['quantity'] . ') أكبر من الكمية المتاحة في المخزون (' . $product->stock . ').',
            ]);
        }

        // إذا كانت طريقة الدفع هي balance_points
        if ($data['payment_method'] === 'balance_points') {
            // تحديث بيانات المستخدم من قاعدة البيانات
            $user->refresh();
            
            // التحقق من وجود رصيد كافي
            $userBalance = $user->balance ?? 0;
            $userPoints = $user->points ?? 0;
            $totalNeeded = $data['total'];

            // التحقق من الرصيد الكافي
            if ($userBalance < $totalNeeded && $userPoints < $totalNeeded) {
                return back()->withErrors(['error' => 'الرصيد أو النقاط غير كافية لإتمام الطلب.']);
            }

            // تفعيل autocommit مؤقتاً (لحل مشكلة transaction مفتوح)
            DB::statement('SET autocommit=1');
            
            // خصم المبلغ من الرصيد أولاً، ثم النقاط إذا لم يكف الرصيد
            try {
                $remaining = $totalNeeded;
                
                // خصم من الرصيد أولاً
                if ($userBalance > 0 && $remaining > 0) {
                    if ($userBalance >= $remaining) {
                        $user->balance = $userBalance - $remaining;
                        $remaining = 0;
                    } else {
                        $remaining = $remaining - $userBalance;
                        $user->balance = 0;
                    }
                }
                
                // خصم من النقاط إذا لم يكف الرصيد
                if ($userPoints > 0 && $remaining > 0) {
                    if ($userPoints >= $remaining) {
                        $user->points = $userPoints - $remaining;
                        $remaining = 0;
                    } else {
                        $remaining = $remaining - $userPoints;
                        $user->points = 0;
                    }
                }
                
                $user->save();

                // إنشاء الطلبية بحالة confirmed (منتج واحد)
                $order = Order::create([
                    'user_id' => $user->id,
                    'product_name' => $product->name,
                    'quantity' => $data['quantity'],
                    'unit_price' => $product->price,
                    'total' => $data['total'],
                    'status' => 'confirmed',
                    'payment_method' => $data['payment_method'],
                ]);

                // تسجيل نشاط إتمام الطلب
                ActivityLogger::logOrderPlaced($order);
                
                // تخزين العناصر في حقل items (طلب يحتوي على منتج واحد)
                $order->update([
                    'items' => [[
                        'product_id'   => $product->id,
                        'name'         => $product->name,
                        'quantity'     => (int) $data['quantity'],
                        'unit_price'   => (float) $product->price,
                        'total'        => (float) $data['total'],
                    ]],
                ]);

                // تحديث المخزون وعدّاد المبيعات بناءً على هذه الطلبية المؤكدة
                Order::applyInventoryForOrder($order);
                // إضافة النقاط المستحقة للمستخدم بناءً على هذه الطلبية
                Order::awardPointsForOrder($order, $user);

                // DB::commit(); - لا حاجة لها لأن autocommit مفعّل

                // تحميل الطلبية مع المستخدم المرتبط بها (للتأكد من الحصول على بيانات صحيحة)
                $order->load('user');
                $orderUser = $order->user; // المستخدم الذي قام بالطلب
                
                // التأكد من وجود المستخدم والبريد الإلكتروني
                if (!$orderUser || !$orderUser->email) {
                    \Log::error('المستخدم غير موجود أو لا يوجد بريد إلكتروني للطلبية #' . $order->id . ' - User ID: ' . ($orderUser ? $orderUser->id : 'null'));
                } else {
                    // إنشاء وإرسال الفاتورة PDF
                    try {
                        // استخدام TCPDF لدعم أفضل للعربية
                        $pdfService = new InvoicePdfService();
                        $pdf = $pdfService->generateInvoice($order, $orderUser);
                        $pdfContent = $pdf->Output('', 'S');
                        
                        // إرسال البريد الإلكتروني مع الفاتورة إلى بريد المستخدم الذي قام بالطلب
                        try {
                            Mail::send('emails.invoice', [
                                'order' => $order,
                                'user' => $orderUser,
                                'product' => $product,
                            ], function ($message) use ($orderUser, $order, $pdfContent) {
                                $message->to($orderUser->email, $orderUser->first_name . ' ' . $orderUser->last_name)
                                        ->subject('فاتورة طلبية #' . $order->id . ' - electropalestine')
                                        ->attachData($pdfContent, 'invoice_' . $order->id . '.pdf', [
                                            'mime' => 'application/pdf',
                                        ]);
                            });
                            \Log::info('تم إرسال الفاتورة بالبريد إلى: ' . $orderUser->email . ' للطلبية #' . $order->id);
                        } catch (\Exception $mailException) {
                            // في حالة فشل إرسال البريد، نكمل العملية
                            \Log::error('فشل إرسال الفاتورة بالبريد للطلبية #' . $order->id . ': ' . $mailException->getMessage());
                            \Log::error('تفاصيل الخطأ: ' . $mailException->getTraceAsString());
                        }
                    } catch (\Exception $pdfException) {
                        \Log::error('فشل إنشاء PDF للطلبية #' . $order->id . ': ' . $pdfException->getMessage());
                    }
                }

                // مسح السلة
                session()->forget('cart');

                return redirect()->route('store.my-orders')
                    ->with('status', 'تم تأكيد طلبيتك بنجاح! تم إرسال الفاتورة إلى بريدك الإلكتروني.');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withErrors(['error' => 'حدث خطأ أثناء معالجة الطلب. يرجى المحاولة مرة أخرى.']);
            }
        }

        // إذا كانت طريقة دفع أخرى، ننشئ الطلبية بحالة pending
        $order = Order::create([
            'user_id' => $user->id,
            'product_name' => $product->name,
            'quantity' => $data['quantity'],
            'unit_price' => $product->price,
            'total' => $data['total'],
            'status' => 'pending',
            'payment_method' => $data['payment_method'],
        ]);
        
        // تسجيل نشاط إتمام الطلب
        ActivityLogger::logOrderPlaced($order);

        $order->update([
            'items' => [[
                'product_id'   => $product->id,
                'name'         => $product->name,
                'quantity'     => (int) $data['quantity'],
                'unit_price'   => (float) $product->price,
                'total'        => (float) $data['total'],
            ]],
        ]);

        // في حالة الشراء الفردي لا نلمس السلة

        return redirect()->route('store.my-orders')
            ->with('status', 'تم استلام طلبيتك بنجاح! سيتم مراجعته قريباً.');
    }

    public function switchLanguage(Request $request, string $locale): RedirectResponse
    {
        // Validate locale
        if (!in_array($locale, ['ar', 'en'])) {
            $locale = 'ar';
        }

        // Store locale in session
        Session::put('locale', $locale);

        // Redirect back to previous page or home
        return redirect()->back();
    }

    public function showContact(): View
    {
        return view('store.contact');
    }
    
    public function trackOrder(Request $request): View
    {
        return view('store.track-order');
    }

    public function confirmCartOrder(\App\Http\Requests\CartCheckoutRequest $request): RedirectResponse
    {
        $user = $request->user();

        // منع إنشاء أي طلب من السلة بدون عنوان
        if (!$user->defaultAddress) {
            return redirect()->route('store.account-settings')
                ->withErrors(['error' => 'يجب إضافة عنوانك أولاً قبل تأكيد الطلب من السلة.'])
                ->with('status', null);
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('store.cart')->withErrors(['error' => 'السلة فارغة حالياً.']);
        }

        $data = $request->validated();

        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $items = [];
        $total = 0;
        $stockErrors = [];

        foreach ($cart as $productId => $quantity) {
            $product = $products->get($productId);
            if (!$product) {
                continue;
            }

            // التحقق من توفر المخزون لكل منتج في السلة
            if ($product->stock <= 0) {
                $stockErrors[] = 'المنتج "' . $product->name . '" غير متوفر حالياً في المخزون.';
                continue;
            }

            if ($quantity > $product->stock) {
                $stockErrors[] = 'الكمية المطلوبة من المنتج "' . $product->name . '" (' . $quantity . ') أكبر من الكمية المتاحة في المخزون (' . $product->stock . ').';
                continue;
            }

            $lineTotal = $product->price * $quantity;
            $items[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => (int) $quantity,
                'unit_price' => (float) $product->price,
                'total' => (float) $lineTotal,
            ];
            $total += $lineTotal;
        }

        if (!empty($stockErrors)) {
            return redirect()->route('store.cart')->withErrors(['error' => implode(' ', $stockErrors)]);
        }

        if (empty($items)) {
            return redirect()->route('store.cart')->withErrors(['error' => 'لا توجد منتجات صالحة في السلة.']);
        }

        // منطق الدفع بالرصيد/النقاط إن لزم
        if ($data['payment_method'] === 'balance_points') {
            $user->refresh();

            $userBalance = $user->balance ?? 0;
            $userPoints = $user->points ?? 0;
            $totalNeeded = $total;

            if ($userBalance < $totalNeeded && $userPoints < $totalNeeded) {
                return back()->withErrors(['error' => 'الرصيد أو النقاط غير كافية لإتمام الطلب.']);
            }

            // تفعيل autocommit
            DB::statement('SET autocommit=1');
            
            try {
                $remaining = $totalNeeded;

                if ($userBalance > 0 && $remaining > 0) {
                    if ($userBalance >= $remaining) {
                        $user->balance = $userBalance - $remaining;
                        $remaining = 0;
                    } else {
                        $remaining = $remaining - $userBalance;
                        $user->balance = 0;
                    }
                }

                if ($userPoints > 0 && $remaining > 0) {
                    if ($userPoints >= $remaining) {
                        $user->points = $userPoints - $remaining;
                        $remaining = 0;
                    } else {
                        $remaining = $remaining - $userPoints;
                        $user->points = 0;
                    }
                }

                $user->save();

                $order = Order::create([
                    'user_id' => $user->id,
                    'product_name' => $items[0]['name'] . (count($items) > 1 ? ' +' . (count($items) - 1) . ' منتجات' : ''),
                    'quantity' => collect($items)->sum('quantity'),
                    'unit_price' => $items[0]['unit_price'],
                    'total' => $total,
                    'status' => 'confirmed',
                    'payment_method' => $data['payment_method'],
                    'shipping_address' => $user->address ?? null,
                    'items' => $items,
                ]);
                
                // تسجيل نشاط إتمام الطلب
                ActivityLogger::logOrderPlaced($order);

                // تحديث المخزون وعدّاد المبيعات بناءً على هذه الطلبية المؤكدة (سلة كاملة)
                Order::applyInventoryForOrder($order);
                // إضافة النقاط المستحقة للمستخدم بناءً على هذه الطلبية
                Order::awardPointsForOrder($order, $user);

                // DB::commit(); - لا حاجة

                $order->load('user');
                $orderUser = $order->user;

                if ($orderUser && $orderUser->email) {
                    try {
                        $pdfService = new InvoicePdfService();
                        $pdf = $pdfService->generateInvoice($order, $orderUser);
                        $pdfContent = $pdf->Output('', 'S');

                        try {
                            Mail::send('emails.invoice', [
                                'order' => $order,
                                'user' => $orderUser,
                            ], function ($message) use ($orderUser, $order, $pdfContent) {
                                $message->to($orderUser->email, $orderUser->first_name . ' ' . $orderUser->last_name)
                                        ->subject('فاتورة طلبية #' . $order->id . ' - electropalestine')
                                        ->attachData($pdfContent, 'invoice_' . $order->id . '.pdf', [
                                            'mime' => 'application/pdf',
                                        ]);
                            });
                            Log::info('تم إرسال الفاتورة بالبريد إلى: ' . $orderUser->email . ' للطلبية #' . $order->id);
                        } catch (\Exception $mailException) {
                            Log::error('فشل إرسال الفاتورة بالبريد للطلبية #' . $order->id . ': ' . $mailException->getMessage());
                        }
                    } catch (\Exception $pdfException) {
                        Log::error('فشل إنشاء PDF للطلبية #' . $order->id . ': ' . $pdfException->getMessage());
                    }
                }

                session()->forget('cart');

                return redirect()->route('store.my-orders')
                    ->with('status', 'تم تأكيد طلبيتك بنجاح! تم إنشاء طلب واحد يحتوي على جميع منتجات السلة وإرسال الفاتورة إلى بريدك الإلكتروني.');
            } catch (\Exception $e) {
                Log::error('Cart checkout error', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                return back()->withErrors(['error' => 'حدث خطأ أثناء معالجة الطلب. يرجى المحاولة مرة أخرى.']);
            }
        }

        // طرق الدفع الأخرى: إنشاء الطلب بحالة pending
        $order = Order::create([
            'user_id' => $user->id,
            'product_name' => $items[0]['name'] . (count($items) > 1 ? ' +' . (count($items) - 1) . ' منتجات' : ''),
            'quantity' => collect($items)->sum('quantity'),
            'unit_price' => $items[0]['unit_price'],
            'total' => $total,
            'status' => 'pending',
            'payment_method' => $data['payment_method'],
            'shipping_address' => $user->address ?? null,
            'items' => $items,
        ]);
        
        // تسجيل نشاط إتمام الطلب
        ActivityLogger::logOrderPlaced($order);

        session()->forget('cart');

        return redirect()->route('store.my-orders')
            ->with('status', 'تم استلام طلبيتك بنجاح! تم إنشاء طلب واحد يحتوي على جميع منتجات السلة وسيتم مراجعته قريباً.');
    }

    /**
     * بيانات افتراضية للجوائز (في حال عدم توفر مودل Reward حالياً).
     */
    protected function buildRewardsDataset()
    {
        $rewards = collect();

        if (class_exists(\App\Models\Reward::class)) {
            $rewards = \App\Models\Reward::query()
                ->where('is_active', true)
                ->orderBy('points_required')
                ->get()
                ->map(function ($reward) {
                    $type = $reward->type;
                    // توحيد تسمية نوع الرصيد بين لوحة الأدمن والواجهة الأمامية
                    if ($type === 'wallet_credit') {
                        $type = 'wallet';
                    }

                    return (object) [
                        'id' => $reward->id,
                        'type' => $type,
                        'points_required' => (int) $reward->points_required,
                        'value' => $reward->value,
                        'image' => $reward->image ?? null,
                        'title' => $reward->title_translated ?? null,
                        'description' => $reward->description_translated ?? null,
                    ];
                });
        }

        return $rewards;
    }

    /**
     * معالجة استبدال النقاط.
     */
    public function redeemPoints(Request $request)
    {
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'يجب تسجيل الدخول أولاً'], 401);
            }
            return redirect()->route('login')->withErrors(['error' => 'يجب تسجيل الدخول أولاً']);
        }

        $data = $request->validate([
            'reward_id' => ['required', 'integer', 'exists:rewards,id'],
        ]);

        $user = auth()->user();
        $reward = \App\Models\Reward::findOrFail($data['reward_id']);

        // التحقق من أن المكافأة مفعلة
        if (!$reward->is_active) {
            $message = 'هذه المكافأة غير متاحة حالياً.';
            if ($request->expectsJson()) {
                return response()->json(['error' => $message], 400);
            }
            return back()->withErrors(['error' => $message]);
        }

        // التحقق من وجود نقاط كافية
        $userPoints = (int) ($user->points ?? 0);
        if ($userPoints < $reward->points_required) {
            $message = 'نقاطك غير كافية لاستبدال هذه المكافأة.';
            if ($request->expectsJson()) {
                return response()->json(['error' => $message], 400);
            }
            return back()->withErrors(['error' => $message]);
        }

        // تفعيل autocommit
        DB::statement('SET autocommit=1');
        
        try {
            // خصم النقاط
            $user->points = $userPoints - $reward->points_required;
            $user->save();

            // معالجة حسب نوع المكافأة
            if ($reward->type === 'wallet_credit') {
                // إضافة الرصيد للمحفظة
                $currentBalance = (float) ($user->balance ?? 0);
                $user->balance = $currentBalance + ($reward->value ?? 0);
                $user->save();
            } elseif ($reward->type === 'coupon') {
                // إنشاء كوبون خصم للمستخدم
                $couponCode = $reward->coupon_code ?? strtoupper(substr(md5($user->id . $reward->id . time()), 0, 10));
                $expiresAt = now()->addMonths(3); // صلاحية 3 أشهر
                
                UserReward::create([
                    'user_id' => $user->id,
                    'reward_id' => $reward->id,
                    'coupon_code' => $couponCode,
                    'discount_value' => $reward->value,
                    'discount_type' => 'percent', // يمكن جعله قابل للتعديل
                    'expires_at' => $expiresAt,
                ]);

                // تقليل المخزون إذا كان موجوداً
                if ($reward->stock !== null && $reward->stock > 0) {
                    $reward->stock = $reward->stock - 1;
                    $reward->save();
                }
            }
            // gift لا يحتاج معالجة إضافية هنا (سيتم التعامل معها لاحقاً)

            // DB::commit(); - لا حاجة

            if ($reward->type === 'wallet_credit') {
                $message = 'تم استبدال النقاط بنجاح! تم إضافة ' . number_format($reward->value, 2) . '$ إلى محفظتك.';
            } elseif ($reward->type === 'coupon') {
                $couponCode = $reward->coupon_code ?? 'تم إنشاؤه';
                $message = 'تم استبدال النقاط بنجاح! تم إضافة كوبون خصم جديد إلى قائمة كوبوناتك.';
            } else {
                $message = 'تم استبدال النقاط بنجاح! سيتم مراجعة طلبك قريباً.';
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                ]);
            }

            return redirect()->route('store.points')->with('status', $message);
        } catch (\Exception $e) {
            \Log::error('Points redemption error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorMessage = 'حدث خطأ أثناء استبدال النقاط. يرجى المحاولة مرة أخرى.';
            if ($request->expectsJson()) {
                return response()->json(['error' => $errorMessage], 500);
            }
            return back()->withErrors(['error' => $errorMessage]);
        }
    }

    /**
     * سجل افتراضي لعرض جدول تاريخ الاستبدال.
     */
    protected function buildPointsHistory()
    {
        // حالياً لا يوجد جدول حقيقي لسجل الاستبدال، لذلك نعيد قائمة فارغة
        return collect();
    }

    public function sendContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $contactEmail = env('CONTACT_EMAIL', 'nageammar628@gmail.com');

        try {
            Mail::send('emails.contact', [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'contactMessage' => $validated['message'],
            ], function ($message) use ($contactEmail, $validated) {
                $message->to($contactEmail)
                    ->subject('New Contact Message from ' . config('app.name'))
                    ->replyTo($validated['email'], $validated['name']);
            });

            return redirect()->route('store.contact')
                ->with('status', __('common.message_sent_success'));
        } catch (\Exception $e) {
            Log::error('Failed to send contact email: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => __('common.message_sent_error')]);
        }
    }

    public function favorites(): View
    {
        $user = auth()->user();
        
        try {
            $favoriteProducts = $user->favoriteProducts()
                ->withRelations()
                ->paginate(20);
        } catch (\Exception $e) {
            // في حالة عدم وجود الجدول بعد
            $favoriteProducts = collect()->paginate(20);
        }

        return view('store.favorites', [
            'favoriteProducts' => $favoriteProducts,
        ]);
    }

    public function coupons(): View
    {
        $user = auth()->user();
        
        try {
            $userCoupons = UserReward::where('user_id', $user->id)
                ->whereHas('reward', function ($q) {
                    $q->where('type', 'coupon');
                })
                ->with('reward')
                ->orderByDesc('created_at')
                ->paginate(20);
        } catch (\Exception $e) {
            // في حالة عدم وجود الجدول بعد
            $userCoupons = collect()->paginate(20);
        }

        return view('store.coupons', [
            'userCoupons' => $userCoupons,
        ]);
    }

    public function toggleFavorite(Product $product): \Illuminate\Http\JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول أولاً',
            ], 401);
        }

        try {
            $user = auth()->user();
            $favorite = UserFavorite::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->first();

            if ($favorite) {
                // حذف من المفضلة
                $favorite->delete();
                $isFavorite = false;
                $message = 'تم إزالة المنتج من المفضلة';
            } else {
                // إضافة إلى المفضلة
                UserFavorite::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                ]);
                $isFavorite = true;
                $message = 'تم إضافة المنتج إلى المفضلة';
            }

            return response()->json([
                'success' => true,
                'is_favorite' => $isFavorite,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling favorite: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث المفضلة. يرجى التأكد من تنفيذ migrations.',
            ], 500);
        }
    }
}

