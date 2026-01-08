<x-auth-card title="{{ __('common.login') }}">

    @if (session('status'))
        <div class="alert alert-success small text-center glass">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}" class="d-flex flex-column gap-4">
        @csrf

        <!-- Email or Phone -->
        <div class="form-group">
            <label class="form-label small text-secondary">{{ __('common.email_or_phone') }}</label>
            <input
                type="text"
                name="login"
                value="{{ old('login') }}"
                required
                autofocus
                class="form-control auth-input"
                placeholder="{{ __('common.email_or_phone') }}"
            >
            @error('login')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label class="form-label small text-secondary">{{ __('common.password') }}</label>
            <input
                type="password"
                name="password"
                required
                class="form-control auth-input"
                placeholder="••••••••"
            >
            @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember + Forgot -->
        <div class="d-flex justify-content-between align-items-center small">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="rememberCheck">
                <label class="form-check-label text-secondary" for="rememberCheck">
                    {{ __('common.remember_me') }}
                </label>
            </div>

            <a href="{{ route('password.request') }}" class="link-success text-decoration-none">
                {{ __('common.forgot_password') }}
            </a>
        </div>

        <!-- Submit -->
        <button class="btn btn-main w-100 py-2 fw-bold">
            {{ __('common.login') }}
        </button>

        <!-- Register -->
        <p class="text-center small text-secondary mb-0">
            {{ __('common.no_account') }}
            <a href="{{ route('register') }}" class="link-success fw-semibold">
                {{ __('common.register') }}
            </a>
        </p>
    </form>

</x-auth-card>

