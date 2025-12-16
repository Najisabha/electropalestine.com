<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminRoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $roles = Role::orderBy('id')->get();

        return view('pages.roles', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'key' => ['required', 'string', 'max:255', 'unique:roles,key'],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'max:255'],
        ]);

        // جمع الصلاحيات المحددة مسبقاً
        $data['permissions'] = $data['permissions'] ?? [];

        Role::create($data);

        return back()->with('status', 'تم إنشاء الدور والصلاحيات بنجاح.');
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'key' => ['required', 'string', 'max:255', 'unique:roles,key,' . $role->id],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'max:255'],
        ]);

        // جمع الصلاحيات المحددة مسبقاً
        $data['permissions'] = $data['permissions'] ?? [];

        $role->update($data);

        return back()->with('status', 'تم تحديث بيانات الدور والصلاحيات.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();

        return back()->with('status', 'تم حذف الدور.');
    }
}


