<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
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

    public function index(): View
    {
        $users = User::withCount('orders')
            ->withSum('orders', 'total')
            ->with(['orders' => function ($query) {
                $query->latest()->take(5);
            }])
            ->latest()
            ->paginate(20);
        $roles = Role::orderBy('id')->get();

        return view('pages.users', compact('users', 'roles'));
    }

    /**
     * صفحة العملاء مع إحصائيات الإنفاق والطلبات.
     */
    public function customers(Request $request): View
    {
        $filter = $request->query('filter'); // top_spenders, inactive
        $days = (int) $request->query('days', 90);
        $limit = (int) $request->query('limit', 20);

        $baseQuery = User::customerStatsQuery();

        if ($filter === 'top_spenders') {
            $baseQuery->orderByDesc('total_spent');
        } elseif ($filter === 'top_orders') {
            $baseQuery->orderByDesc('orders_count');
        } elseif ($filter === 'inactive') {
            // استخدم الميثود المخصص للعملاء غير النشطين
            $customers = User::inactiveCustomers($days);

            return view('pages.customers', [
                'customers' => $customers,
                'filter' => $filter,
                'days' => $days,
                'limit' => $limit,
            ]);
        } else {
            $baseQuery->orderByDesc('last_order_at');
        }

        $customers = $baseQuery->limit($limit)->get();

        return view('pages.customers', [
            'customers' => $customers,
            'filter' => $filter,
            'days' => $days,
            'limit' => $limit,
        ]);
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', 'string', 'max:255'],
        ]);

        $user->update([
            'role' => $data['role'],
        ]);

        return back()->with('status', 'تم تحديث دور المستخدم بنجاح.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        try {
            $data = $request->validate([
                'first_name' => ['required', 'string', 'max:100'],
                'last_name' => ['required', 'string', 'max:100'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
                'phone' => ['required', 'string', 'max:30'],
                'whatsapp_prefix' => ['required', 'string', 'max:10'],
                'birth_year' => ['required', 'integer', 'min:1900', 'max:' . now()->year],
                'birth_month' => ['required', 'integer', 'between:1,12'],
                'birth_day' => ['required', 'integer', 'between:1,31'],
                'points' => ['nullable', 'integer', 'min:0'],
                'balance' => ['nullable', 'numeric', 'min:0'],
                'role' => ['required', 'string', 'max:255'],
            ], [
                'first_name.required' => 'الاسم الأول مطلوب',
                'last_name.required' => 'اسم العائلة مطلوب',
                'email.required' => 'البريد الإلكتروني مطلوب',
                'email.email' => 'البريد الإلكتروني غير صحيح',
                'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل',
                'phone.required' => 'رقم الجوال مطلوب',
                'whatsapp_prefix.required' => 'مقدمة واتساب مطلوبة',
                'birth_year.required' => 'سنة الميلاد مطلوبة',
                'birth_month.required' => 'شهر الميلاد مطلوب',
                'birth_day.required' => 'يوم الميلاد مطلوب',
                'role.required' => 'الدور مطلوب',
            ]);

            $updated = $user->update([
                'name' => $data['first_name'] . ' ' . $data['last_name'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'whatsapp_prefix' => $data['whatsapp_prefix'],
                'birth_year' => $data['birth_year'],
                'birth_month' => $data['birth_month'],
                'birth_day' => $data['birth_day'],
                'points' => $data['points'] ?? $user->points,
                'balance' => $data['balance'] ?? $user->balance,
                'role' => $data['role'],
            ]);

            if ($updated) {
                return back()->with('status', 'تم تحديث بيانات المستخدم بنجاح.');
            } else {
                return back()->withErrors(['error' => 'حدث خطأ أثناء تحديث البيانات.'])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        return back()->with('status', 'تم حذف المستخدم بنجاح.');
    }
}

