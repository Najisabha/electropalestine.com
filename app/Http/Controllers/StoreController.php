<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Campaign;
use App\Models\Order;
use App\Models\User;
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
        $categories = Category::with(['types', 'products' => fn ($q) => $q->latest()->take(6)])->get();

        $featured = Product::with(['category', 'company'])
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        // المنتجات الأكثر مبيعاً: فقط المنتجات التي تم تعليمها كـ \"ضمن المنتجات الأكثر مبيعاً\"
        $bestSelling = Product::with(['category', 'company'])
            ->where('is_best_seller', true)
            ->orderByDesc('sales_count')
            ->orderByDesc('created_at')
            ->take(12)
            ->get();

        // جميع المنتجات لعرضها في قائمة أسفل شريط الأكثر مبيعاً
        $allProducts = Product::with(['category', 'company'])
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

        $related = Product::where('category_id', $product->category_id)
            ->whereKeyNot($product->getKey())
            ->take(4)
            ->get();

        return view('store.product', compact('product', 'related'));
    }

    public function category(Category $category): View
    {
        $category->load(['types', 'companies', 'products.company']);

        // جميع الأنواع التابعة لهذا الصنف
        $types = $category->types;

        // الشركات المرتبطة بالصنف عن طريق جدول الربط
        $companies = $category->companies;

        // المنتجات التابعة للصنف (نستخدمها في الشريط أو الشبكة)
        $products = $category->products()
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

        if (isset($cart[$product->id])) {
            $cart[$product->id] += $quantity;
        } else {
            $cart[$product->id] = $quantity;
        }

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

    public function checkout(Request $request): View
    {
        $productId = $request->input('product');
        $quantity = $request->input('quantity', 1);

        if (!$productId) {
            abort(404, 'المنتج غير موجود');
        }

        $product = Product::with(['category', 'company'])->findOrFail($productId);
        $total = $product->price * $quantity;

        $user = auth()->user();
        $userBalance = $user ? ($user->balance ?? 0) : 0;
        $userPoints = $user ? ($user->points ?? 0) : 0;

        return view('store.checkout', compact('product', 'quantity', 'total', 'userBalance', 'userPoints'));
    }

    public function accountSettings(): View
    {
        $user = auth()->user();
        return view('store.account-settings', compact('user'));
    }

    public function myOrders(): View
    {
        $user = auth()->user();
        $orders = \App\Models\Order::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(10);
        return view('store.my-orders', compact('orders'));
    }

    public function myComments(): View
    {
        $user = auth()->user();
        // TODO: إضافة جدول comments لاحقاً
        $comments = collect([]);
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
        $product = Product::where('name', $order->product_name)->first();
        
        // إذا لم نجد المنتج، ننشئ كائن وهمي للمنتج
        if (!$product) {
            $product = (object)[
                'name' => $order->product_name,
                'price' => $order->unit_price,
                'category' => (object)['name' => 'غير محدد'],
                'company' => (object)['name' => 'غير محدد'],
            ];
        } else {
            $product->load(['category', 'company']);
        }

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
        $product = Product::findOrFail($data['product_id']);

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

                // إنشاء الطلبية بحالة confirmed
                $order = Order::create([
                    'user_id' => $user->id,
                    'product_name' => $product->name,
                    'quantity' => $data['quantity'],
                    'unit_price' => $product->price,
                    'total' => $data['total'],
                    'status' => 'confirmed',
                    'payment_method' => $data['payment_method'],
                ]);

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

        session()->forget('cart');

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

