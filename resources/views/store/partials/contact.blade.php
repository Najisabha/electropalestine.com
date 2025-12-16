<section class="container py-4 text-light">
    <div class="glass p-4">
        <h1 class="h4 fw-bold mb-3">{{ __('common.contact_us') }}</h1>
        <p class="text-secondary small mb-3">{{ __('common.contact_us_description') }}</p>
        
        @if (session('status'))
            <div class="alert alert-success small py-2 mb-3">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger small py-2 mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('store.contact.send') }}" class="row g-3">
            @csrf
            <div class="col-md-6">
                <label class="form-label small text-secondary">{{ __('common.name') }}</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-sm auth-input" placeholder="{{ __('common.full_name') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small text-secondary">{{ __('common.email') }}</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-sm auth-input" placeholder="example@email.com" required>
            </div>
            <div class="col-12">
                <label class="form-label small text-secondary">{{ __('common.message_label') }}</label>
                <textarea name="message" class="form-control form-control-sm auth-input" rows="3" placeholder="{{ __('common.write_message_here') }}" required>{{ old('message') }}</textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-main btn-sm px-4">{{ __('common.send') }}</button>
            </div>
        </form>
    </div>
</section>


