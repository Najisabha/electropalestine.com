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
            if (!auth()->check() || auth()->user()->role !== 'admin') {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index(): View
    {
        $users = User::latest()->paginate(20);
        $roles = Role::orderBy('id')->get();

        return view('pages.users', compact('users', 'roles'));
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
}

