<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Campaign;
use App\Models\Order;
use App\Models\User;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\InvoicePdfService;

class StoreController extends Controller
{
    public function home(): View
    {
        $categories = Category::with([
            'types',
            'products' => fn ($q) => $q->active()->latest()->take(6),
        ])->get();

        $featured = Product::with(['category', 'company'])
            ->active()
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        // المنتجات الأكثر مبيعاً: فقط المنتجات التي تم تعليمها كـ \"ضمن المنتجات الأكثر مبيعاً\"
        $bestSelling = Product::with(['category', 'company'])
            ->where('is_best_seller', true)
            ->active()
            ->orderByDesc('sales_count')
            ->orderByDesc('created_at')
            ->take(12)
            ->get();

        // جميع المنتجات لعرضها في قائمة أسفل شريط الأكثر مبيعاً
        $allProducts = Product::with(['category', 'company'])
            ->active()
            ->orderByDesc('created_at')
            ->take(40)
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

        return view('store.home', compact('categories', 'featured', 'bestSelling', 'campaigns', 'allProducts'));
    }

    public function product(Product $product): View
    {
        $product->load(['category.types', 'company', 'type']);

        // جلب تقييمات الطلبات التي تحتوي هذا المنتج
        $reviews = Review::with(['user', 'order'])
            ->whereHas('order', function ($q) use ($product) {
                $q->where('product_name', $product->name)
                    ->orWhere('items', 'like', '%"product_id":' . $product->id . '%');
            })
            ->latest()
            ->get();

        // تحديث متوسط التقييم وعدد التقييمات مباشرة من التقييمات الموجودة
        $ratingCount = $reviews->count();
        $ratingAverage = $ratingCount > 0 ? (float) $reviews->avg('rating') : 0.0;
        $product->rating_count = $ratingCount;
        $product->rating_average = $ratingAverage;
        $product->save();

        $related = Product::where('category_id', $product->category_id)
            ->whereKeyNot($product->getKey())
            ->take(4)
            ->get();

        return view('store.product', [
            'product' => $product,
            'related' => $related,
            'reviews' => $reviews,
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

    public function cart(): View
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = Product::with(['category', 'company'])->find($productId);
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

        return back()->with('status', 'تم إضافة المنتج إلى السلة بنجاح.');
    }

    public function removeFromCart(Product $product): RedirectResponse
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            unset($cart[$product->id]);
            session()->put('cart', $cart);
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

        return back()->with('status', 'تم تحديث الكمية بنجاح.');
    }

    public function clearCart(): RedirectResponse
    {
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
        if (empty($user->address)) {
            return redirect()->route('store.account-settings')
                ->withErrors(['error' => 'يجب إضافة عنوانك أولاً قبل إتمام أي طلب.'])
                ->with('status', null);
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('store.cart')->withErrors(['error' => 'السلة فارغة حالياً.']);
        }

        $productIds = array_keys($cart);
        $products = Product::with(['category', 'company'])
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

    public function checkout(Request $request): View
    {
        $productId = $request->input('product');
        $quantity = $request->input('quantity', 1);

        if (!$productId) {
            abort(404, 'المنتج غير موجود');
        }

        $product = Product::with(['category', 'company'])->findOrFail($productId);

        // منع إتمام الطلب بدون عنوان
        $user = auth()->user();
        if ($user && empty($user->address)) {
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
        return view('store.account-settings', compact('user'));
    }

    public function updateAddress(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
            'governorate' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'secondary_address' => ['nullable', 'string', 'max:500'],
            'zip_code' => ['nullable', 'string', 'max:20'],
            'country_code' => ['nullable', 'string', 'max:10'],
            'phone' => ['required', 'string', 'max:50'],
        ]);

        $user->fill($data);
        // تحديث حقل name الكامل ليتطابق مع الاسم الأول والأخير
        $user->name = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
        $user->save();

        return redirect()->route('store.account-settings')
            ->with('status', 'تم حفظ عنوانك الشخصي بنجاح.');
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
        if (empty($user->address)) {
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

            // خصم المبلغ من الرصيد أولاً، ثم النقاط إذا لم يكف الرصيد
            DB::beginTransaction();
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

                DB::commit();

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

    public function confirmCartOrder(\App\Http\Requests\CartCheckoutRequest $request): RedirectResponse
    {
        $user = $request->user();

        // منع إنشاء أي طلب من السلة بدون عنوان
        if (empty($user->address)) {
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

            DB::beginTransaction();
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

                // تحديث المخزون وعدّاد المبيعات بناءً على هذه الطلبية المؤكدة (سلة كاملة)
                Order::applyInventoryForOrder($order);
                // إضافة النقاط المستحقة للمستخدم بناءً على هذه الطلبية
                Order::awardPointsForOrder($order, $user);

                DB::commit();

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
                DB::rollBack();
                Log::error('Cart checkout error: ' . $e->getMessage());
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

        session()->forget('cart');

        return redirect()->route('store.my-orders')
            ->with('status', 'تم استلام طلبيتك بنجاح! تم إنشاء طلب واحد يحتوي على جميع منتجات السلة وسيتم مراجعته قريباً.');
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
}

