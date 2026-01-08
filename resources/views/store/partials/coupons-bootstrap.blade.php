<section class="py-5 text-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 fw-bold mb-0">
                <i class="bi bi-ticket-perforated text-warning me-2"></i>
                كوبوناتي
            </h1>
            <a href="{{ route('store.points') }}" class="btn btn-outline-main">
                <i class="bi bi-stars me-2"></i>
                استبدال النقاط
            </a>
        </div>

        @if (session('status'))
            <div class="alert alert-success small py-2 mb-3">{{ session('status') }}</div>
        @endif

        @if($userCoupons->count() > 0)
            <div class="row g-4">
                @foreach($userCoupons as $userCoupon)
                    @php
                        $reward = $userCoupon->reward;
                        $isValid = $userCoupon->isValid();
                        $isExpired = $userCoupon->isExpired();
                    @endphp
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="glass rounded-4 p-4 h-100 position-relative {{ !$isValid ? 'opacity-50' : '' }}">
                            @if($userCoupon->is_used)
                                <span class="badge bg-secondary position-absolute top-0 end-0 m-3">
                                    <i class="bi bi-check-circle me-1"></i>
                                    مستخدم
                                </span>
                            @elseif($isExpired)
                                <span class="badge bg-danger position-absolute top-0 end-0 m-3">
                                    <i class="bi bi-x-circle me-1"></i>
                                    منتهي
                                </span>
                            @else
                                <span class="badge bg-success position-absolute top-0 end-0 m-3">
                                    <i class="bi bi-check-circle me-1"></i>
                                    صالح
                                </span>
                            @endif

                            <div class="mb-3">
                                <h5 class="text-white fw-bold mb-2">
                                    {{ $reward->title_translated ?? 'كوبون خصم' }}
                                </h5>
                                @if($reward->description_translated)
                                    <p class="text-white-50 small mb-0">
                                        {{ $reward->description_translated }}
                                    </p>
                                @endif
                            </div>

                            <div class="mb-3 p-3 bg-dark rounded-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-white-50 small">كود الكوبون:</span>
                                    <button class="btn btn-sm btn-outline-light copy-coupon-code" 
                                            data-code="{{ $userCoupon->coupon_code }}"
                                            title="نسخ الكود">
                                        <i class="bi bi-copy me-1"></i>
                                        {{ $userCoupon->coupon_code }}
                                    </button>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="text-white-50 small">قيمة الخصم:</span>
                                    <span class="text-warning fw-bold">
                                        @if($userCoupon->discount_type === 'percent')
                                            {{ number_format($userCoupon->discount_value, 0) }}%
                                        @else
                                            {{ number_format($userCoupon->discount_value, 2) }} ₪
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center text-white-50 small">
                                <div>
                                    <i class="bi bi-calendar3 me-1"></i>
                                    @if($userCoupon->expires_at)
                                        ينتهي: {{ $userCoupon->expires_at->format('Y/m/d') }}
                                    @else
                                        بدون انتهاء
                                    @endif
                                </div>
                                <div>
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $userCoupon->created_at->format('Y/m/d') }}
                                </div>
                            </div>

                            @if($isValid)
                                <div class="mt-3">
                                    <a href="{{ route('store.products') }}" class="btn btn-main w-100">
                                        <i class="bi bi-cart-plus me-2"></i>
                                        استخدم الكوبون
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $userCoupons->links() }}
            </div>
        @else
            <div class="glass rounded-4 p-5 text-center">
                <i class="bi bi-ticket-perforated display-1 text-secondary mb-3"></i>
                <h3 class="h5 text-secondary mb-2">لا توجد كوبونات</h3>
                <p class="text-secondary small mb-4">لم تقم باستبدال أي كوبونات بعد. استبدل نقاطك للحصول على كوبونات خصم حصرية!</p>
                <a href="{{ route('store.points') }}" class="btn btn-main">
                    <i class="bi bi-stars me-2"></i>
                    استبدال النقاط
                </a>
            </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // نسخ كود الكوبون
    document.querySelectorAll('.copy-coupon-code').forEach(button => {
        button.addEventListener('click', function() {
            const code = this.getAttribute('data-code');
            navigator.clipboard.writeText(code).then(() => {
                const originalHTML = this.innerHTML;
                this.innerHTML = '<i class="bi bi-check me-1"></i> تم النسخ!';
                this.classList.remove('btn-outline-light');
                this.classList.add('btn-success');
                
                setTimeout(() => {
                    this.innerHTML = originalHTML;
                    this.classList.remove('btn-success');
                    this.classList.add('btn-outline-light');
                }, 2000);
            }).catch(() => {
                alert('فشل نسخ الكود. الكود: ' + code);
            });
        });
    });
});
</script>
@endpush
