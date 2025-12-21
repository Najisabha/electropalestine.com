<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || strtolower(auth()->user()->role) !== 'admin') {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $query = Order::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->query('user_id'));
        }

        $orders = $query->paginate(20)->withQueryString();

        return view('pages.orders', compact('orders'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string'],
        ]);

        $data['total'] = $data['quantity'] * $data['unit_price'];

        $originalStatus = $order->status;
        $order->update($data);

        // عند تغيير حالة الطلب إلى confirmed لأول مرة، نقوم بتحديث المخزون وعدّاد المبيعات والنقاط
        if ($originalStatus !== 'confirmed' && $data['status'] === 'confirmed') {
            // تأكد من تحميل العناصر من قاعدة البيانات
            $order->refresh();
            Order::applyInventoryForOrder($order);
            Order::awardPointsForOrder($order);
        }

        return back()->with('status', 'تم تحديث الطلبية بنجاح.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return back()->with('status', 'تم حذف الطلبية.');
    }
}

