<x-auth-card title="تعيين كلمة مرور جديدة">
    @if (session('status'))
        <div class="alert alert-success small text-center glass">{{ session('status') }}</div>
    @endif
    <form method="POST" action="{{ route('password.update') }}" class="d-flex flex-column gap-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div>
            <label class="form-label small text-secondary">البريد الإلكتروني</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="form-control auth-input">
            @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="form-label small text-secondary">كلمة المرور الجديدة</label>
            <input type="password" name="password" required class="form-control auth-input">
            @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="form-label small text-secondary">تأكيد كلمة المرور</label>
            <input type="password" name="password_confirmation" required class="form-control auth-input">
        </div>
        <button class="btn btn-main w-100 py-2 fw-semibold">حفظ كلمة المرور</button>
    </form>
</x-auth-card>

