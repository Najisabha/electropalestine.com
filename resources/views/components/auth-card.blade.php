@props(['title'])

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="auth-card glass p-4 p-md-5 shadow-lg text-light">

        <!-- Title -->
        <div class="text-center mb-4">
            <img src="{{ asset('images/LOGO-remove background.png') }}" alt="ElectroPalestine Logo" height="64" class="mb-3">
            <h5 class="fw-bold mb-1">{{ $title }}</h5>
            <p class="small text-secondary mb-0">
                أهلاً بك في electropalestine 👋 الرجاء إدخال بياناتك
            </p>
        </div>

        <!-- Slot -->
        {{ $slot }}

    </div>
</div>
