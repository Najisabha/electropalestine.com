<x-auth-card title="{{ __('common.register') }}">
    <form method="POST" action="{{ route('register.attempt') }}" class="d-flex flex-column gap-4" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small text-secondary">{{ __('common.first_name') }}</label>
                <input type="text" name="first_name" value="{{ old('first_name') }}" required class="form-control auth-input">
                @error('first_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label small text-secondary">{{ __('common.last_name') }}</label>
                <input type="text" name="last_name" value="{{ old('last_name') }}" required class="form-control auth-input">
                @error('last_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label small text-secondary">{{ __('common.email') }}</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="form-control auth-input">
                @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label small text-secondary">{{ __('common.whatsapp_prefix') }}</label>
                <input type="text" name="whatsapp_prefix" value="{{ old('whatsapp_prefix', '+970') }}" required class="form-control auth-input">
                @error('whatsapp_prefix')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label small text-secondary">{{ __('common.phone') }}</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required class="form-control auth-input">
                @error('phone')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label small text-secondary">{{ __('common.birth_date') }}</label>
                <div class="row g-2">
                    <div class="col-4">
                        <input type="number" name="birth_year" value="{{ old('birth_year') }}" required class="form-control auth-input" placeholder="{{ __('common.year') }}">
                        @error('birth_year')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-4">
                        <input type="number" name="birth_month" value="{{ old('birth_month') }}" required class="form-control auth-input" placeholder="{{ __('common.month') }}">
                        @error('birth_month')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-4">
                        <input type="number" name="birth_day" value="{{ old('birth_day') }}" required class="form-control auth-input" placeholder="{{ __('common.day') }}">
                        @error('birth_day')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label small text-secondary">{{ __('common.password') }}</label>
                <input type="password" name="password" required class="form-control auth-input">
                @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label small text-secondary">{{ __('common.confirm_password') }}</label>
                <input type="password" name="password_confirmation" required class="form-control auth-input">
            </div>
            <div class="col-12">
                <label class="form-label small text-secondary">{{ __('common.id_image') }}</label>
                <input type="file" name="id_image" class="form-control auth-input">
                @error('id_image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
        </div>
        <button class="btn btn-main w-100 py-2 fw-semibold">{{ __('common.register') }}</button>
        <p class="text-center small text-secondary mb-0">
            {{ __('common.have_account') }} <a href="{{ route('login') }}" class="link-success">{{ __('common.login') }}</a>
        </p>
    </form>
</x-auth-card>

