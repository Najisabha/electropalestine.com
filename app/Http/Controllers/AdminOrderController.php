<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || auth()->user()->role !== 'admin') {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index(): View
    {
        $orders = Order::with('user')->latest()->paginate(20);

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
        $originalQuantity = $order->quantity;

        $order->update($data);

        // زيادة عداد مبيعات المنتج عند تأكيد الطلب (بشكل مبسط بالاعتماد على اسم المنتج)
        if ($originalStatus !== 'confirmed' && $data['status'] === 'confirmed') {
            $product = \App\Models\Product::where('name', $order->product_name)->first();
            if ($product) {
                $product->increment('sales_count', $order->quantity);
            }
        }

        return back()->with('status', 'تم تحديث الطلبية بنجاح.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return back()->with('status', 'تم حذف الطلبية.');
    }
}

