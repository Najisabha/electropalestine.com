<x-auth-card title="التحقق من رقم الهاتف">
    <div class="text-center mb-4">
        <div class="mb-3">
            <i class="fas fa-mobile-alt text-main" style="font-size: 3rem;"></i>
        </div>
        <p class="text-secondary mb-2">تم إرسال كود التحقق المكون من 6 أرقام إلى</p>
        <p class="fw-bold fs-5 text-main mb-0" dir="ltr">{{ $phone }}</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-3" role="alert">
            <i class="fas fa-check-circle"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info d-flex align-items-center gap-2 mb-3" role="alert">
            <i class="fas fa-info-circle"></i>
            <div>{{ session('info') }}</div>
        </div>
    @endif

    <form method="POST" action="{{ route('verify.phone.submit') }}" class="d-flex flex-column gap-4">
        @csrf
        
        <div>
            <label class="form-label small text-secondary">أدخل كود التحقق</label>
            <input type="text" 
                   name="code" 
                   id="verification-code"
                   maxlength="6" 
                   pattern="[0-9]{6}"
                   inputmode="numeric"
                   placeholder="000000"
                   required 
                   autofocus
                   class="form-control auth-input text-center fs-4 letter-spacing-wide @error('code') is-invalid @enderror"
                   style="letter-spacing: 0.5rem; font-family: monospace;">
            @error('code')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-main w-100 py-2 fw-semibold">
            <i class="fas fa-check me-2"></i>تحقق من الرقم
        </button>
    </form>

    <div class="mt-3 text-center">
        <p class="text-secondary small mb-2">لم يصلك الكود؟</p>
        <form method="POST" action="{{ route('verify.phone.resend') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-link text-main text-decoration-none p-0 fw-semibold">
                <i class="fas fa-redo-alt me-1"></i>إعادة إرسال الكود
            </button>
        </form>
    </div>

    <div class="alert alert-warning d-flex align-items-start gap-2 mt-3 mb-0 py-2 px-3" role="alert">
        <i class="fas fa-exclamation-triangle mt-1"></i>
        <div class="small">
            <strong>ملاحظة:</strong>
            <ul class="mb-0 ps-3 mt-1">
                <li>الكود صالح لمدة <strong>10 دقائق</strong> فقط</li>
                <li>لديك <strong>5 محاولات</strong> لإدخال الكود الصحيح</li>
                <li>إذا لم يصلك الكود، تحقق من رقم هاتفك وحاول مرة أخرى</li>
            </ul>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const codeInput = document.getElementById('verification-code');
        
        // منع إدخال أي شيء غير الأرقام
        codeInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // إرسال النموذج تلقائياً عند إدخال 6 أرقام
            if (this.value.length === 6) {
                this.form.submit();
            }
        });
        
        // منع لصق أي شيء غير الأرقام
        codeInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const numbersOnly = pastedText.replace(/[^0-9]/g, '').substring(0, 6);
            this.value = numbersOnly;
            
            // إرسال النموذج تلقائياً إذا تم لصق 6 أرقام
            if (numbersOnly.length === 6) {
                this.form.submit();
            }
        });
    });
    </script>
</x-auth-card>
