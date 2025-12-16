<section class="py-5 text-light">
    <div class="container">
        <h1 class="h4 fw-bold mb-4">{{ __('common.checkout_title') }}</h1>

        <div class="row g-4">
            {{-- ملخص الطلب --}}
            <div class="col-lg-4">
                <div class="glass rounded-4 p-4 sticky-top" style="top: 100px;">
                    <h2 class="h6 fw-semibold mb-3">{{ __('common.order_summary') }}</h2>

                    @if($product)
                        <div class="d-flex gap-3 mb-3">
                            @if(!empty($product->image))
                                <img src="{{ asset('storage/'.$product->image) }}"
                                     class="rounded"
                                     style="width: 80px; height: 80px; object-fit: cover;"
                                     alt="{{ $product->name }}">
                            @else
                                <div class="bg-black rounded d-flex align-items-center justify-content-center"
                                     style="width: 80px; height: 80px;">
                                    <span class="text-secondary small">{{ __('common.no_image') }}</span>
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <h3 class="h6 mb-1">{{ $product->name }}</h3>
                                <p class="text-secondary small mb-1">
                                    {{ $product->category->name ?? __('common.no_category') }} • {{ $product->company->name ?? __('common.unknown_company') }}
                                </p>
                                <p class="text-secondary small mb-0">
                                    {{ __('common.quantity') }}: <strong class="text-white">{{ $quantity }}</strong>
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="border-top border-secondary-subtle pt-3 mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">{{ __('common.unit_price_label') }}:</span>
                            <strong class="text-white">${{ number_format($product->price ?? 0, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">{{ __('common.quantity') }}:</span>
                            <strong class="text-white">{{ $quantity }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary-subtle">
                            <span class="h6 mb-0">{{ __('common.grand_total') }}:</span>
                            <span class="h5 fw-bold text-success mb-0">${{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- خيارات الدفع --}}
            <div class="col-lg-8">
                <div class="glass rounded-4 p-4">
                    <h2 class="h6 fw-semibold mb-4">{{ __('common.choose_payment_method') }}</h2>

                    <form id="checkout-form" method="POST" action="{{ route('store.checkout.confirm') }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id ?? '' }}">
                        <input type="hidden" name="quantity" value="{{ $quantity }}">
                        <input type="hidden" name="total" value="{{ $total }}">

                        <div class="row g-3">
                            {{-- الخيار 1: الفيزا/الماستر كارد --}}
                            <div class="col-12">
                                <div class="payment-option glass rounded-3 p-3 border border-secondary-subtle">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="payment_visa" value="visa_mastercard" required>
                                        <label class="form-check-label w-100" for="payment_visa">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <strong class="d-block mb-1">
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
                                    <div id="visa_details" class="payment-details mt-3 d-none">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <label class="form-label small text-secondary">{{ __('common.card_number') }}</label>
                                                <input type="text" class="form-control auth-input" placeholder="1234 5678 9012 3456" maxlength="19">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small text-secondary">{{ __('common.expiry_date') }}</label>
                                                <input type="text" class="form-control auth-input" placeholder="MM/YY" maxlength="5">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small text-secondary">CVV</label>
                                                <input type="text" class="form-control auth-input" placeholder="123" maxlength="3">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small text-secondary">{{ __('common.cardholder_name') }}</label>
                                                <input type="text" class="form-control auth-input" placeholder="{{ __('common.card_name_placeholder') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- الخيار 2: الرصيد/النقاط --}}
                            <div class="col-12">
                                <div class="payment-option glass rounded-3 p-3 border border-secondary-subtle">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="payment_balance" value="balance_points" required>
                                        <label class="form-check-label w-100" for="payment_balance">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <strong class="d-block mb-1">
                                                        <i class="bi bi-wallet2"></i>
                                                        {{ __('common.pay_from_balance_points') }}
                                                    </strong>
                                                    <span class="text-secondary small">
                                                        {{ __('common.your_balance') }}: <strong class="text-info">${{ number_format($userBalance, 2) }}</strong> • 
                                                        {{ __('common.your_points') }}: <strong class="text-success">{{ number_format($userPoints) }}</strong>
                                                    </span>
                                                </div>
                                                <div>
                                                    <i class="bi bi-wallet2 fs-4 text-info"></i>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div id="balance_details" class="payment-details mt-3 d-none">
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
                                        <input class="form-check-input" type="radio" name="payment_method" id="payment_cod" value="cash_on_delivery" required>
                                        <label class="form-check-label w-100" for="payment_cod">
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
                                    <div id="cod_details" class="payment-details mt-3 d-none">
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
                                        <input class="form-check-input" type="radio" name="payment_method" id="payment_wallet" value="palestinian_wallet" required>
                                        <label class="form-check-label w-100" for="payment_wallet">
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
                                    <div id="wallet_details" class="payment-details mt-3 d-none">
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
                                        <input class="form-check-input" type="radio" name="payment_method" id="payment_agent" value="agent" required>
                                        <label class="form-check-label w-100" for="payment_agent">
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
                                    <div id="agent_details" class="payment-details mt-3 d-none">
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
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-outline-main px-4">
                                        <i class="bi bi-arrow-right"></i>
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
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    const allDetails = document.querySelectorAll('.payment-details');

    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Hide all details
            allDetails.forEach(detail => {
                detail.classList.add('d-none');
            });

            // Show details for selected option
            const method = this.value;
            const detailsId = method.replace('_', '_') + '_details';
            let targetDetails = null;

            if (method === 'visa_mastercard') {
                targetDetails = document.getElementById('visa_details');
            } else if (method === 'balance_points') {
                targetDetails = document.getElementById('balance_details');
            } else if (method === 'cash_on_delivery') {
                targetDetails = document.getElementById('cod_details');
            } else if (method === 'palestinian_wallet') {
                targetDetails = document.getElementById('wallet_details');
            } else if (method === 'agent') {
                targetDetails = document.getElementById('agent_details');
            }

            if (targetDetails) {
                targetDetails.classList.remove('d-none');
            }
        });
    });

        // Validate payment method before submit
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
            if (!selectedMethod) {
                e.preventDefault();
                alert('{{ __('common.please_select_payment') }}');
                return;
            }
        
            // If payment method is balance_points, check sufficient balance
            if (selectedMethod.value === 'balance_points') {
                const total = parseFloat({{ $total }});
                const userBalance = parseFloat({{ $userBalance }});
                const userPoints = parseFloat({{ $userPoints }});
                
                if (userBalance < total && userPoints < total) {
                    e.preventDefault();
                    alert('{{ __('common.insufficient_balance') }}\n{{ __('common.your_balance') }}: $' + userBalance.toFixed(2) + '\n{{ __('common.your_points') }}: ' + userPoints + '\n{{ __('common.grand_total') }}: $' + total.toFixed(2));
                    return;
                }
            }
        
            // Show loading message
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>{{ __('common.processing_order') }}';
            }
        
            // Allow form submission
            return true;
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
