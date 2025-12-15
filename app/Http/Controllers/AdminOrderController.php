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
        $this->middleware('auth');
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

        $order->update($data);

        return back()->with('status', 'تم تحديث الطلبية بنجاح.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return back()->with('status', 'تم حذف الطلبية.');
    }
}

