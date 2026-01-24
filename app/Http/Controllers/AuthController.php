<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PhoneVerification;
use App\Helpers\ImageHelper;
use App\Helpers\ActivityLogger;
use App\Helpers\SMSHelper;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;
use Illuminate\Database\QueryException;
use Laravel\Socialite\Facades\Socialite;

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
        
        // تسجيل نشاط تسجيل الدخول
        ActivityLogger::logLogin();

        if (strtolower($user->role) === 'admin') {
            return redirect()->intended('/admin/dashboard');
        }

        return redirect()->route('home');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        try {
            $data = $request->validate([
                'first_name' => ['required', 'string', 'max:100'],
                'last_name' => ['required', 'string', 'max:100'],
                'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9]+$/', 'unique:users,phone'],
                'whatsapp_prefix' => ['required', 'string', 'max:10'],
                'password' => ['required', 'confirmed', PasswordRule::defaults()],
            ], [
                'phone.regex' => 'رقم الهاتف يجب أن يحتوي على أرقام فقط',
                'phone.unique' => 'رقم الهاتف مستخدم بالفعل',
            ]);

            Log::info('بدء عملية التسجيل', [
                'phone' => $data['phone'],
                'name' => $data['first_name'] . ' ' . $data['last_name']
            ]);

            // تفعيل autocommit مؤقتاً
            DB::statement("SET autocommit=1");
            
            // استخدام INSERT مباشر
            $hashedPassword = Hash::make($data['password']);
            $name = $data['first_name'] . ' ' . $data['last_name'];
            $now = now();
            
            $userId = DB::table('users')->insertGetId([
                'name' => $name,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => null,
                'phone' => $data['phone'],
                'whatsapp_prefix' => $data['whatsapp_prefix'],
                'role' => 'user',
                'id_verified_status' => 'unverified',
                'points' => 0,
                'balance' => 0,
                'balance_ils' => 0,
                'balance_usd' => 0,
                'balance_jod' => 0,
                'default_payment_wallet' => 'ILS',
                'preferred_currency' => 'USD',
                'password' => $hashedPassword,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if (!$userId) {
                Log::error('فشل إنشاء المستخدم');
                return back()->withErrors(['phone' => 'فشل إنشاء الحساب'])->withInput();
            }

            Log::info('تم إنشاء المستخدم', ['user_id' => $userId, 'phone' => $data['phone']]);

            // جلب المستخدم
            $user = User::find($userId);
            if (!$user) {
                Log::error('فشل جلب المستخدم بعد الإنشاء', ['user_id' => $userId]);
                return back()->withErrors(['phone' => 'فشل جلب بيانات الحساب'])->withInput();
            }

            Log::info('تم التحقق من حفظ المستخدم', ['user_id' => $user->id]);

            // إنشاء كود التحقق
            $fullPhone = $data['whatsapp_prefix'] . $data['phone'];
            $verification = PhoneVerification::createVerificationCode($fullPhone);
            
            Log::info('تم إنشاء كود التحقق', [
                'user_id' => $user->id,
                'phone' => $fullPhone,
                'code' => $verification->code // في الإنتاج، احذف هذا السطر
            ]);

            // إرسال SMS
            SMSHelper::sendVerificationCode($fullPhone, $verification->code);
            
            // حفظ معلومات المستخدم في الـ session للتحقق
            Session::put('pending_verification', [
                'user_id' => $user->id,
                'phone' => $fullPhone,
            ]);

            // التوجيه لصفحة التحقق
            return redirect()->route('verify.phone')->with('info', 'تم إرسال كود التحقق إلى رقم هاتفك');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('خطأ في التحقق من البيانات', ['errors' => $e->errors()]);
            throw $e;
        } catch (QueryException $e) {
            Log::error('خطأ في قاعدة البيانات', [
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            return back()->withErrors(['phone' => 'خطأ في قاعدة البيانات: ' . $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            Log::error('خطأ غير متوقع في التسجيل', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return back()->withErrors(['phone' => 'حدث خطأ: ' . $e->getMessage()])->withInput();
        }
    }

    public function showVerifyPhone()
    {
        // التحقق من وجود معلومات التحقق في الـ session
        if (!Session::has('pending_verification')) {
            return redirect()->route('register')->with('error', 'الرجاء التسجيل أولاً');
        }

        $pendingData = Session::get('pending_verification');
        $phone = $pendingData['phone'];

        return view('auth.verify-phone', compact('phone'));
    }

    public function verifyPhone(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]+$/'],
        ], [
            'code.size' => 'الكود يجب أن يكون 6 أرقام',
            'code.regex' => 'الكود يجب أن يحتوي على أرقام فقط',
        ]);

        // التحقق من وجود معلومات التحقق في الـ session
        if (!Session::has('pending_verification')) {
            return redirect()->route('register')->with('error', 'الرجاء التسجيل أولاً');
        }

        $pendingData = Session::get('pending_verification');
        $phone = $pendingData['phone'];
        $userId = $pendingData['user_id'];

        // جلب آخر كود صالح
        $verification = PhoneVerification::getLatestCode($phone);

        if (!$verification) {
            return back()->withErrors(['code' => 'انتهت صلاحية الكود. الرجاء طلب كود جديد.']);
        }

        // التحقق من عدد المحاولات
        if ($verification->attempts >= 5) {
            return back()->withErrors(['code' => 'تم تجاوز عدد المحاولات المسموح بها. الرجاء طلب كود جديد.']);
        }

        // التحقق من الكود
        if ($verification->verify($request->code)) {
            // تحديث حالة المستخدم
            $user = User::find($userId);
            if ($user) {
                // يمكن إضافة حقل phone_verified في جدول users لاحقاً
                
                // تسجيل الدخول
                Auth::login($user);
                
                // حذف معلومات التحقق من الـ session
                Session::forget('pending_verification');
                
                Log::info('تم التحقق من الهاتف بنجاح', ['user_id' => $user->id]);
                
                return redirect('/')->with('success', 'تم التحقق من رقم هاتفك بنجاح!');
            }
        }

        return back()->withErrors(['code' => 'الكود غير صحيح. يرجى المحاولة مرة أخرى.']);
    }

    public function resendVerificationCode(Request $request): RedirectResponse
    {
        // التحقق من وجود معلومات التحقق في الـ session
        if (!Session::has('pending_verification')) {
            return redirect()->route('register')->with('error', 'الرجاء التسجيل أولاً');
        }

        $pendingData = Session::get('pending_verification');
        $phone = $pendingData['phone'];

        // إنشاء كود جديد
        $verification = PhoneVerification::createVerificationCode($phone);
        
        Log::info('إعادة إرسال كود التحقق', [
            'phone' => $phone,
            'code' => $verification->code // في الإنتاج، احذف هذا السطر
        ]);

        // إرسال SMS
        SMSHelper::sendVerificationCode($phone, $verification->code);

        return back()->with('success', 'تم إرسال كود جديد إلى رقم هاتفك');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Redirect the user to the OAuth provider.
     */
    public function redirectToProvider(string $provider): RedirectResponse
    {
        // التحقق من وجود الإعدادات
        $config = config("services.{$provider}");
        if (empty($config['client_id']) || empty($config['client_secret'])) {
            Log::error("Missing OAuth credentials for provider: {$provider}");
            return redirect()->route('login')->withErrors([
                'login' => __('إعدادات تسجيل الدخول عبر :provider غير مكتملة. يرجى التواصل مع الإدارة.', ['provider' => ucfirst($provider)]),
            ]);
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle OAuth provider callback.
     */
    public function handleProviderCallback(string $provider): RedirectResponse
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Exception $e) {
            Log::error('Social login error', [
                'provider' => $provider,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')->withErrors([
                'login' => __('حدث خطأ أثناء تسجيل الدخول عبر مزود خارجي، الرجاء المحاولة مرة أخرى.'),
            ]);
        }

        if (!$socialUser->getEmail()) {
            Log::warning('Social login attempt without email', [
                'provider' => $provider,
                'user_id' => $socialUser->getId(),
            ]);

            return redirect()->route('login')->withErrors([
                'login' => __('لا يمكن استكمال تسجيل الدخول بدون بريد إلكتروني من :provider.', ['provider' => ucfirst($provider)]),
            ]);
        }

        try {
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                // إنشاء مستخدم جديد من بيانات مزود الخدمة
                $name = $socialUser->getName() ?: $socialUser->getNickname() ?: $socialUser->getEmail();
                $nameParts = preg_split('/\s+/', $name, 2);
                $firstName = $nameParts[0] ?? $name;
                $lastName = $nameParts[1] ?? '';

                $user = User::create([
                    'name' => $name,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $socialUser->getEmail(),
                    'phone' => null,
                    'whatsapp_prefix' => null,
                    'birth_year' => null,
                    'birth_month' => null,
                    'birth_day' => null,
                    'role' => 'user',
                    'id_image' => null,
                    'id_verified_status' => 'unverified',
                    'points' => 0,
                    'balance' => 0,
                    'preferred_currency' => 'USD',
                    'password' => Hash::make(str()->random(32)),
                ]);

                if (!$user || !$user->id) {
                    Log::error('Failed to create user from social login', [
                        'provider' => $provider,
                        'email' => $socialUser->getEmail(),
                    ]);

                    return redirect()->route('login')->withErrors([
                        'login' => __('حدث خطأ أثناء إنشاء الحساب. يرجى المحاولة مرة أخرى.'),
                    ]);
                }
            }

            Auth::login($user, true);

            $user->update(['last_login_at' => now()]);
            ActivityLogger::logLogin();

            if (strtolower($user->role) === 'admin') {
                return redirect()->intended('/admin/dashboard');
            }

            return redirect()->route('home');

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error during social login', [
                'provider' => $provider,
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->route('login')->withErrors([
                'login' => __('حدث خطأ في قاعدة البيانات. يرجى المحاولة مرة أخرى.'),
            ]);

        } catch (Exception $e) {
            Log::error('Unexpected error during social login', [
                'provider' => $provider,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')->withErrors([
                'login' => __('حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى.'),
            ]);
        }
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

