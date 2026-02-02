<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Notifications\IdImageRejected;

class AdminUserController extends Controller
{
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
                'id_verified_status' => ['nullable', 'string', 'in:verified,pending,unverified'],
                'rejection_reason' => ['nullable', 'string', 'max:500'],
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

            $oldStatus = $user->id_verified_status ?? 'unverified';
            $newStatus = $data['id_verified_status'] ?? ($user->id_verified_status ?? 'unverified');
            
            // إذا تم تغيير الحالة إلى "غير موثق"، حذف الصورة وإرسال إشعار
            if ($newStatus === 'unverified' && $oldStatus !== 'unverified' && $user->id_image) {
                // حذف الصورة من التخزين
                \App\Helpers\ImageHelper::delete($user->id_image, 'public');
                
                // حذف المسار من قاعدة البيانات
                $user->id_image = null;
                
                // إرسال إشعار للمستخدم
                $rejectionReason = $request->input('rejection_reason', null);
                $user->notify(new IdImageRejected($rejectionReason));
            }
            
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
                'id_verified_status' => $newStatus,
            ]);

            if ($updated) {
                $message = 'تم تحديث بيانات المستخدم بنجاح.';
                if ($newStatus === 'unverified' && $oldStatus !== 'unverified') {
                    $message .= ' تم حذف صورة الهوية وإرسال إشعار للمستخدم.';
                }
                return back()->with('status', $message);
            } else {
                return back()->withErrors(['error' => 'حدث خطأ أثناء تحديث البيانات.'])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(User $user): RedirectResponse
    {
        try {
            if ($user->id === auth()->id()) {
                return back()->withErrors(['error' => 'لا يمكنك حذف حسابك الخاص.']);
            }

            $userId = $user->id;
            $userEmail = $user->email;

            // حذف صورة الهوية من التخزين
            if ($user->id_image) {
                try {
                    \App\Helpers\ImageHelper::delete($user->id_image, 'public');
                } catch (\Exception $e) {}
            }

            // تفعيل autocommit وبدء transaction صريح
            DB::statement('SET autocommit=1');
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::statement('START TRANSACTION');

            try {
                // حذف كل ما له علاقة بالمستخدم
                $orderIds = DB::table('orders')->where('user_id', $userId)->pluck('id');
                if ($orderIds->isNotEmpty()) {
                    DB::table('order_items')->whereIn('order_id', $orderIds)->delete();
                    DB::table('reviews')->whereIn('order_id', $orderIds)->delete();
                    DB::table('orders')->where('user_id', $userId)->delete();
                }
                DB::table('reviews')->where('user_id', $userId)->delete();
                DB::table('user_addresses')->where('user_id', $userId)->delete();
                DB::table('user_activities')->where('user_id', $userId)->delete();
                DB::table('user_favorites')->where('user_id', $userId)->delete();
                DB::table('user_rewards')->where('user_id', $userId)->delete();
                DB::table('sessions')->where('user_id', $userId)->delete();
                DB::table('password_reset_tokens')->where('email', $userEmail)->delete();

                // حذف المستخدم من قاعدة البيانات
                $deleted = DB::table('users')->where('id', $userId)->delete();

                // COMMIT لحفظ التغييرات
                DB::statement('COMMIT');
                DB::statement('SET FOREIGN_KEY_CHECKS=1');

                // التحقق من أن الحذف تم فعلياً
                if ($deleted === 0) {
                    return back()->withErrors(['error' => 'فشل حذف المستخدم من قاعدة البيانات. لم يتم العثور على المستخدم.']);
                }

                // التحقق المزدوج: التأكد من عدم وجود المستخدم في القاعدة
                $stillExists = DB::table('users')->where('id', $userId)->exists();
                if ($stillExists) {
                    return back()->withErrors(['error' => 'فشل حذف المستخدم. المستخدم ما زال موجوداً في قاعدة البيانات.']);
                }

                return back()->with('status', 'تم حذف المستخدم وجميع بياناته بنجاح.');
            } catch (\Exception $e) {
                DB::statement('ROLLBACK');
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
                throw $e;
            }
        } catch (\Exception $e) {
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            } catch (\Exception $ex) {}
            return back()->withErrors(['error' => 'حدث خطأ أثناء حذف المستخدم: ' . $e->getMessage()]);
        }
    }
}

