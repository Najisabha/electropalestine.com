@props(['title'])

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="auth-card glass p-4 p-md-5 shadow-lg text-light">

        <!-- Title -->
        <div class="text-center mb-4">
            <div class="auth-logo mb-3">VM</div>
            <h5 class="fw-bold mb-1">{{ $title }}</h5>
            <p class="small text-secondary mb-0">
                أهلاً بك في electropalestine 👋 الرجاء إدخال بياناتك
            </p>
        </div>

        <!-- Slot -->
        {{ $slot }}

    </div>
</div>
