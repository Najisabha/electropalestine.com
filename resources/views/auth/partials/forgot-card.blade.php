<x-auth-card title="استعادة كلمة المرور">
    @if (session('status'))
        <div class="alert alert-success small text-center glass">{{ session('status') }}</div>
    @endif
    <form method="POST" action="{{ route('password.email') }}" class="d-flex flex-column gap-4">
        @csrf
        <div>
            <label class="form-label small text-secondary">البريد الإلكتروني</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="form-control auth-input">
            @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>
        <p class="small text-secondary mb-0">سنرسل رابط إعادة تعيين كلمة المرور إلى بريدك.</p>
        <button class="btn btn-main w-100 py-2 fw-semibold">إرسال الرابط</button>
        <p class="text-center small text-secondary mb-0">
            تذكرت كلمة المرور؟ <a href="{{ route('login') }}" class="link-success">العودة لتسجيل الدخول</a>
        </p>
    </form>
</x-auth-card>

