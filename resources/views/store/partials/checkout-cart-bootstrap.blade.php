@php
    /** @var array $items */
    /** @var float $total */
    /** @var float $userBalance */
    /** @var float $userPoints */
@endphp

<section class="py-5 text-light">
    <div class="container">
        <h1 class="h4 fw-bold mb-4">{{ __('common.checkout_title') }}</h1>

        {{-- الصف الأول: جدول الطلبية --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="glass rounded-4 p-4">
                    <h2 class="h6 fw-semibold mb-3">{{ __('common.order_summary') }}</h2>

                    <div class="table-responsive mb-3">
                        <table class="table table-dark table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('common.product') }}</th>
                                    <th class="text-center" style="width: 70px;">{{ __('common.quantity') }}</th>
                                    <th class="text-end" style="width: 90px;">{{ __('common.unit_price') }}</th>
                                    <th class="text-end" style="width: 90px;">{{ __('common.total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                    @php($p = $item['product'])
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if(!empty($p->image))
                                                    <img src="{{ asset('storage/'.$p->image) }}"
                                                         alt="{{ $p->translated_name }}"
                                                         style="width:40px;height:40px;object-fit:cover;border-radius:8px;">
                                                @endif
                                                <div>
                                                    <div class="fw-semibold">{{ $p->translated_name }}</div>
                                                    <div class="text-secondary small">
                                                        {{ $p->category->translated_name ?? $p->category->name ?? __('common.no_category') }}
                                                        • {{ $p->company->name ?? __('common.unknown_company') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            {{ $item['quantity'] }}
                                        </td>
                                        <td class="text-end text-success">
                                            ${{ number_format($p->price, 2) }}
                                        </td>
                                        <td class="text-end text-success fw-bold">
                                            ${{ number_format($item['subtotal'], 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="border-top border-secondary-subtle pt-3 mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">{{ __('common.product_count') }}:</span>
                            <strong class="text-white">{{ count($items) }}</strong>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">{{ __('common.total_quantity') }}:</span>
                            <strong class="text-white">
                                {{ collect($items)->sum('quantity') }}
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary-subtle">
                            <span class="h6 mb-0">{{ __('common.grand_total') }}:</span>
                            <span class="h5 fw-bold text-success mb-0">${{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

        {{-- الصف الثاني: خيارات الدفع --}}
        <div class="row g-4">
            <div class="col-12">
                <div class="glass rounded-4 p-4">
                    <h2 class="h6 fw-semibold mb-4">{{ __('common.choose_payment_method') }}</h2>

                    <form id="cart-checkout-form" method="POST" action="{{ route('store.cart.checkout.confirm') }}">
                        @csrf

                        <div class="row g-3">
                            {{-- الخيار 1: الفيزا/الماستر كارد --}}
                            <div class="col-12">
                                <div class="payment-option glass rounded-3 p-3 border border-secondary-subtle">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="cart_payment_visa" value="visa_mastercard" required>
                                        <label class="form-check-label w-100" for="cart_payment_visa">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <strong class=" d-block mb-1">
                                                        <i class="bi bi-credit-card-2-front"></i>
                                                        {{ __('common.pay_with_visa_mastercard') }}
                                                    </strong>
                                                    <span class="text-secondary small">{{ __('common.secure_card_payment') }}</span>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <i class="bi bi-credit-card fs-4 text-primary"></i>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- الخيار 2: الرصيد/النقاط --}}
                            <div class="col-12">
                                <div class="payment-option glass rounded-3 p-3 border border-secondary-subtle">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="cart_payment_balance" value="balance_points" required>
                                        <label class="form-check-label w-100" for="cart_payment_balance">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <strong class="d-block mb-1">
                                                        <i class="bi bi-wallet2"></i>
                                                        {{ __('common.pay_from_balance_points') }}
                                                    </strong>
                                                    <span class="text-secondary small">
                                                        {{ __('common.your_balance') }}:
                                                        <strong class="text-info">${{ number_format($userBalance, 2) }}</strong>
                                                        • {{ __('common.your_points') }}:
                                                        <strong class="text-success">{{ number_format($userPoints) }}</strong>
                                                    </span>
                                                </div>
                                                <div>
                                                    <i class="bi bi-wallet2 fs-4 text-info"></i>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div id="cart_balance_details" class="payment-details mt-3 d-none">
                                        <div class="alert alert-info small mb-0">
                                            {{ __('common.balance_deduction_note') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- الخيار 3: الدفع عند الاستلام --}}
                            <div class="col-12">
                                <div class="payment-option glass rounded-3 p-3 border border-secondary-subtle">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="cart_payment_cod" value="cash_on_delivery" required>
                                        <label class="form-check-label w-100" for="cart_payment_cod">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <strong class="d-block mb-1">
                                                        <i class="bi bi-cash-coin"></i>
                                                        {{ __('common.cash_on_delivery') }}
                                                    </strong>
                                                    <span class="text-secondary small">{{ __('common.pay_cash_on_delivery') }}</span>
                                                </div>
                                                <div>
                                                    <i class="bi bi-cash-coin fs-4 text-warning"></i>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div id="cart_cod_details" class="payment-details mt-3 d-none">
                                        <div class="alert alert-warning small mb-0">
                                            {{ __('common.delivery_confirmation_note') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- الخيار 4: المحافظ الفلسطينية --}}
                            <div class="col-12">
                                <div class="payment-option glass rounded-3 p-3 border border-secondary-subtle">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="cart_payment_wallet" value="palestinian_wallet" required>
                                        <label class="form-check-label w-100" for="cart_payment_wallet">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <strong class="d-block mb-1">
                                                        <i class="bi bi-phone"></i>
                                                        {{ __('common.pay_via_palestinian_wallet') }}
                                                    </strong>
                                                    <span class="text-secondary small">Reflact, Jawwal Pay, Pal Pay</span>
                                                </div>
                                                <div>
                                                    <i class="bi bi-phone fs-4 text-success"></i>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div id="cart_wallet_details" class="payment-details mt-3 d-none">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <label class="form-label small text-secondary">{{ __('common.select_wallet') }}</label>
                                                <select class="form-select auth-input" name="wallet_type">
                                                    <option value="">-- {{ __('common.select_wallet') }} --</option>
                                                    <option value="reflact">Reflact</option>
                                                    <option value="jawwal_pay">Jawwal Pay</option>
                                                    <option value="pal_pay">Pal Pay</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small text-secondary">{{ __('common.wallet_phone') }}</label>
                                                <input type="text" class="form-control auth-input" placeholder="05XX XXX XXX" name="wallet_phone">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- الخيار 5: الدفع عن طريق وكيل --}}
                            <div class="col-12">
                                <div class="payment-option glass rounded-3 p-3 border border-secondary-subtle">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="cart_payment_agent" value="agent" required>
                                        <label class="form-check-label w-100" for="cart_payment_agent">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <strong class="d-block mb-1">
                                                        <i class="bi bi-person-check"></i>
                                                        {{ __('common.pay_via_agent') }}
                                                    </strong>
                                                    <span class="text-secondary small">{{ __('common.pay_via_agent_note') }}</span>
                                                </div>
                                                <div>
                                                    <i class="bi bi-person-check fs-4 text-primary"></i>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div id="cart_agent_details" class="payment-details mt-3 d-none">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <label class="form-label small text-secondary">{{ __('common.agent_name') }}</label>
                                                <input type="text" class="form-control auth-input" placeholder="{{ __('common.agent_name_placeholder') }}" name="agent_name">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small text-secondary">{{ __('common.additional_notes') }}</label>
                                                <textarea class="form-control auth-input" rows="2" placeholder="{{ __('common.agent_notes_placeholder') }}" name="agent_notes"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- زر التأكيد --}}
                            <div class="col-12 mt-4">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-main px-5">
                                        <i class="bi bi-check-circle"></i>
                                        {{ __('common.confirm_order') }}
                                    </button>
                                    <a href="{{ route('store.cart') }}" class="btn btn-outline-main px-4">
                                        <i class="bi bi-arrow-left"></i>
                                        {{ __('common.cancel') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentRadios = document.querySelectorAll('#cart-checkout-form input[name="payment_method"]');
    const allDetails = document.querySelectorAll('#cart-checkout-form .payment-details');

    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            allDetails.forEach(d => d.classList.add('d-none'));
            const method = this.value;
            let targetId = null;
            if (method === 'balance_points') targetId = 'cart_balance_details';
            if (method === 'cash_on_delivery') targetId = 'cart_cod_details';
            if (method === 'palestinian_wallet') targetId = 'cart_wallet_details';
            if (method === 'agent') targetId = 'cart_agent_details';

            if (targetId) {
                const el = document.getElementById(targetId);
                if (el) el.classList.remove('d-none');
            }
        });
    });

    const form = document.getElementById('cart-checkout-form');
    form.addEventListener('submit', function (e) {
        const selected = form.querySelector('input[name="payment_method"]:checked');
        if (!selected) {
            e.preventDefault();
            alert('{{ __('common.please_select_payment') }}');
            return;
        }

        if (selected.value === 'balance_points') {
            const total = {{ $total }};
            const userBalance = {{ $userBalance }};
            const userPoints = {{ $userPoints }};
            if (userBalance < total && userPoints < total) {
                e.preventDefault();
                alert('{{ __('common.insufficient_balance') }}\n{{ __('common.your_balance') }}: $' + userBalance.toFixed(2) + '\n{{ __('common.your_points') }}: ' + userPoints + '\n{{ __('common.grand_total') }}: $' + total.toFixed(2));
                return;
            }
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>{{ __('common.processing_order') }}';
        }
    });
});
</script>

<style>
.payment-option {
    transition: all 0.3s ease;
    cursor: pointer;
}
.payment-option:hover {
    border-color: var(--primary) !important;
    background: rgba(13, 183, 119, 0.1);
}
.payment-option .form-check-input:checked ~ .form-check-label {
    color: var(--primary);
}
.payment-details {
    animation: fadeIn 0.3s ease;
}
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

