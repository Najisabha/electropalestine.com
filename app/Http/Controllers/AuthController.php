<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\ImageHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');
        $loginInput = trim($credentials['login']);
        $normalizedLogin = str_replace(' ', '', $loginInput);

        // ابحث عن المستخدم بالبريد أو الهاتف أو (مقدمة + هاتف)
        $user = User::query()
            ->where('email', $loginInput)
            ->orWhere('phone', $loginInput)
            ->orWhereRaw("REPLACE(CONCAT(COALESCE(whatsapp_prefix,''), phone), ' ', '') = ?", [$normalizedLogin])
            ->first();

        if (!$user || !Auth::attempt(['id' => $user->id, 'password' => $credentials['password']], $remember)) {
            return back()->withErrors([
                'login' => __('auth.failed'),
            ])->onlyInput('login');
        }

        $request->session()->regenerate();

        $user->update(['last_login_at' => now()]);

        if (strtolower($user->role) === 'admin') {
            return redirect()->intended('/admin/dashboard');
        }

        return redirect()->intended('/');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'whatsapp_prefix' => ['required', 'string', 'max:10'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
            'id_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $idImagePath = null;
        if ($request->hasFile('id_image')) {
            $idImagePath = ImageHelper::storeWithSequentialName($request->file('id_image'), 'ids', 'public');
            if (!$idImagePath) {
                Log::error('فشل رفع صورة الهوية عند التسجيل', ['email' => $data['email']]);
                return back()->withErrors(['id_image' => 'فشل رفع صورة الهوية. يرجى التحقق من صلاحيات المجلدات.'])->withInput();
            }
        }

        $user = User::create([
            'name' => $data['first_name'] . ' ' . $data['last_name'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => null,
            'phone' => $data['phone'],
            'whatsapp_prefix' => $data['whatsapp_prefix'],
            'birth_year' => null,
            'birth_month' => null,
            'birth_day' => null,
            'role' => 'user',
            'id_image' => $idImagePath,
            'id_verified_status' => $idImagePath ? 'pending' : 'unverified', // إذا تم رفع صورة، الحالة الافتراضية: قيد التنفيذ
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);

        return redirect('/');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showForgot(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::broker('users')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(string $token): View
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}

