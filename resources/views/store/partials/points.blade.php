@php
    $currencySymbol = '$';
@endphp

<style>
    .loyalty-progress {
        border-radius: 999px;
        overflow: hidden;
        box-shadow: 0 6px 18px rgba(13, 183, 119, 0.35);
    }
    .loyalty-card {
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }
    .loyalty-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 14px 32px rgba(13, 183, 119, 0.3);
        border-color: rgba(13, 183, 119, 0.55) !important;
    }
    .loyalty-tab-btn.active {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark)) !important;
        color: #fff !important;
        border-color: transparent !important;
        box-shadow: 0 6px 16px rgba(13, 183, 119, 0.35);
    }
    .loyalty-tab-btn {
        border-radius: 14px;
    }
    .loyalty-history table td,
    .loyalty-history table th {
        border-color: rgba(255,255,255,0.08) !important;
    }
</style>

<section class="container mb-4">
    <div class="row g-3 align-items-stretch">
        <div class="col-lg-8">
            <div class="glass p-4 h-100">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <p class="text-white-50 mb-1">{{ __('common.current_points_balance') }}</p>
                        <h1 class="display-5 fw-bold text-white mb-2">
                            {{ number_format($userPoints) }}
                            <span class="fs-6 text-warning">{{ __('common.points_short') }}</span>
                        </h1>
                        <div class="text-info fw-semibold">
                            {{ __('common.points_value_currency', [
                                'value' => number_format($pointsValue, 2),
                                'currency' => $currencySymbol
                            ]) }}
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-dark border border-primary text-white px-3 py-2 rounded-pill">
                            {{ $currentTier['label'] ?? __('common.tier_bronze') }}
                        </span>
                        <div class="small text-white-50 mt-2">
                            @if($nextTier)
                                {{ __('common.points_to_next_tier', ['points' => number_format($pointsToNext), 'tier' => $nextTier['label']]) }}
                            @else
                                {{ __('common.next_tier_unlocked') }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="progress loyalty-progress mt-4" style="height: 12px; background: rgba(255,255,255,.08);">
                    <div class="progress-bar" role="progressbar"
                         style="width: {{ $progressToNext }}%; background: linear-gradient(90deg,#0db777,#0a8d5b);"
                         aria-valuenow="{{ $progressToNext }}" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <div class="d-flex justify-content-between text-white-50 small mt-2">
                    <span>{{ __('common.progress_to_next_tier') }}</span>
                    <span>{{ $progressToNext }}%</span>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="glass p-4 h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-dark p-3 me-3 border border-secondary">
                        <i class="bi bi-gem text-warning fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-white mb-1">{{ __('common.redeem_points') }}</h6>
                        <p class="text-white-50 small mb-0">{{ __('common.redeem_points_hint') }}</p>
                    </div>
                </div>
                <ul class="list-unstyled text-white-50 small mb-0">
                    <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>{{ __('common.redeem_points_wallet') }}</li>
                    <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>{{ __('common.redeem_points_coupon') }}</li>
                    <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>{{ __('common.redeem_points_gift') }}</li>
                    <li class="mb-0"><i class="bi bi-check2 text-success me-2"></i>{{ __('common.redeem_points_secure') }}</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="container">
    <div class="glass p-3 mb-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <button class="btn btn-sm btn-outline-main loyalty-tab-btn active reward-filter" data-type="all">
                <i class="bi bi-stars me-2"></i>{{ __('common.tab_all_rewards') }}
            </button>
            <button class="btn btn-sm btn-outline-main loyalty-tab-btn reward-filter" data-type="wallet">
                <i class="bi bi-wallet2 me-2"></i>{{ __('common.tab_wallet') }}
            </button>
            <button class="btn btn-sm btn-outline-main loyalty-tab-btn reward-filter" data-type="coupon">
                <i class="bi bi-ticket-perforated me-2"></i>{{ __('common.tab_coupons') }}
            </button>
            <button class="btn btn-sm btn-outline-main loyalty-tab-btn reward-filter" data-type="gift">
                <i class="bi bi-gift me-2"></i>{{ __('common.tab_gifts') }}
            </button>
        </div>
    </div>

    <div class="row g-3" id="rewardsGrid">
        @forelse($rewards as $reward)
            @php
                $type = $reward->type ?? 'gift';
                $pointsRequired = (int) ($reward->points_required ?? 0);
                $hasEnough = $userPoints >= $pointsRequired;
                $typeIcon = match($type) {
                    'wallet' => 'fa-solid fa-wallet',
                    'coupon' => 'fa-solid fa-ticket-simple',
                    default => 'fa-solid fa-gift',
                };
                $typeLabel = match($type) {
                    'wallet' => __('common.tab_wallet'),
                    'coupon' => __('common.tab_coupons'),
                    default => __('common.tab_gifts'),
                };
            @endphp
            <div class="col-12 col-md-6 col-lg-4 reward-card" data-type="{{ $type }}">
                <div class="glass loyalty-card h-100 p-3 d-flex flex-column {{ $hasEnough ? '' : 'opacity-75' }}">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-dark p-3 border border-secondary-subtle">
                                <i class="{{ $typeIcon }} text-warning fs-5"></i>
                            </div>
                            <div>
                                <p class="text-white-50 small mb-1">{{ $typeLabel }}</p>
                                <h5 class="text-white fw-bold mb-0">{{ $reward->title ?? __('common.loyalty_reward_default') }}</h5>
                            </div>
                        </div>
                        <span class="badge bg-dark border border-warning text-warning px-3 py-2">
                            {{ number_format($pointsRequired) }} {{ __('common.points_short') }}
                        </span>
                    </div>
                    <p class="text-white-50 small mb-3">
                        {{ $reward->description ?? __('common.loyalty_reward_placeholder') }}
                    </p>
                    <div class="mt-auto d-flex align-items-center justify-content-between">
                        <div class="text-success fw-semibold">
                            @if($type === 'wallet')
                                {{ __('common.reward_wallet_label', ['value' => $reward->value]) }}
                            @elseif($type === 'coupon')
                                {{ __('common.reward_coupon_label', ['value' => $reward->value]) }}
                            @else
                                {{ __('common.reward_gift_label') }}
                            @endif
                        </div>
                        <button
                            class="btn btn-main btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#redeemConfirmModal"
                            data-reward-id="{{ $reward->id }}"
                            data-reward="{{ $reward->title ?? __('common.loyalty_reward_default') }}"
                            data-points="{{ number_format($pointsRequired) }}"
                            {{ $hasEnough ? '' : 'disabled' }}
                        >
                            <i class="bi bi-arrow-repeat me-1"></i>{{ __('common.redeem_now') }}
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="glass p-4 text-center text-white-50">
                    {{ __('common.no_rewards_available') }}
                </div>
            </div>
        @endforelse
    </div>
</section>

<section class="container mt-4">
    <div class="glass p-4 loyalty-history">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h5 class="text-white mb-1">{{ __('common.points_history') }}</h5>
                <p class="text-white-50 small mb-0">{{ __('common.points_history_hint') }}</p>
            </div>
            <span class="badge bg-dark border border-secondary text-white-50 px-3 py-2">
                <i class="bi bi-clock-history me-2"></i>{{ __('common.history_points_spent') }}
            </span>
        </div>

        @if($history->isEmpty())
            <div class="text-center text-white-50 py-3">
                {{ __('common.history_no_entries') }}
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-dark align-middle mb-0">
                    <thead>
                        <tr class="text-white-50">
                            <th>{{ __('common.history_date') }}</th>
                            <th>{{ __('common.history_reward') }}</th>
                            <th>{{ __('common.history_status') }}</th>
                            <th class="text-end">{{ __('common.history_points') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $entry)
                            @php
                                $status = $entry->status ?? 'pending';
                                $statusClass = match($status) {
                                    'completed' => 'success',
                                    'rejected' => 'danger',
                                    default => 'warning',
                                };
                            @endphp
                            <tr>
                                <td class="text-white">{{ $entry->date }}</td>
                                <td class="text-white-50">{{ $entry->reward }}</td>
                                <td>
                                    <span class="badge bg-{{ $statusClass }}">
                                        @if($status === 'completed')
                                            {{ __('common.status_completed') }}
                                        @elseif($status === 'rejected')
                                            {{ __('common.status_rejected') }}
                                        @else
                                            {{ __('common.status_pending') }}
                                        @endif
                                    </span>
                                </td>
                                <td class="text-end text-warning fw-semibold">
                                    {{ number_format($entry->points ?? 0) }} {{ __('common.points_short') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</section>

<!-- Confirm Modal -->
<div class="modal fade" id="redeemConfirmModal" tabindex="-1" aria-hidden="true"
     data-template="{{ __('common.redeem_confirm_body_template') }}">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass border border-secondary-subtle">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">{{ __('common.redeem_confirm_title') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="redeemModalBody" class="text-white-50 mb-3"></p>
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-white">
                        <div class="fw-semibold" id="redeemModalReward"></div>
                        <small class="text-white-50">{{ __('common.points_required') }}: <span id="redeemModalPoints"></span></small>
                    </div>
                    <div class="badge bg-dark border border-warning text-warning px-3 py-2">
                        <i class="bi bi-stars me-1"></i>{{ __('common.secure_redeem') }}
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-main" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn btn-main" id="redeemConfirmButton">{{ __('common.redeem_confirm_cta') }}</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filters = document.querySelectorAll('.reward-filter');
        const cards = document.querySelectorAll('.reward-card');

        filters.forEach(button => {
            button.addEventListener('click', () => {
                filters.forEach(b => b.classList.remove('active'));
                button.classList.add('active');

                const type = button.dataset.type;
                cards.forEach(card => {
                    card.style.display = (type === 'all' || card.dataset.type === type) ? 'block' : 'none';
                });
            });
        });

        const modalEl = document.getElementById('redeemConfirmModal');
        let currentRewardId = null;
        
        if (modalEl) {
            modalEl.addEventListener('show.bs.modal', event => {
                const trigger = event.relatedTarget;
                if (!trigger) return;

                // الحصول على reward_id
                currentRewardId = trigger.getAttribute('data-reward-id') || null;
                
                const rewardName = trigger.getAttribute('data-reward') || '';
                const points = trigger.getAttribute('data-points') || '';
                const template = modalEl.getAttribute('data-template') || '';

                const bodyText = template.replace(':points', points).replace(':reward', rewardName);

                const bodyEl = modalEl.querySelector('#redeemModalBody');
                const rewardEl = modalEl.querySelector('#redeemModalReward');
                const pointsEl = modalEl.querySelector('#redeemModalPoints');

                if (bodyEl) bodyEl.textContent = bodyText;
                if (rewardEl) rewardEl.textContent = rewardName;
                if (pointsEl) pointsEl.textContent = points;
            });
        }
        
        const confirmBtn = document.getElementById('redeemConfirmButton');
        if (confirmBtn && modalEl) {
            confirmBtn.addEventListener('click', () => {
                if (!currentRewardId) {
                    alert('خطأ: لم يتم تحديد المكافأة.');
                    return;
                }

                // تعطيل الزر أثناء المعالجة
                confirmBtn.disabled = true;
                const originalText = confirmBtn.innerHTML;
                confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>جاري المعالجة...';

                // إرسال الطلب
                const formData = new FormData();
                formData.append('reward_id', currentRewardId);
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route("store.points.redeem") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || data.error || 'حدث خطأ في الاستبدال');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    // Confetti للاحتفال
                    if (typeof confetti === 'function') {
                        confetti({
                            particleCount: 120,
                            spread: 70,
                            origin: { y: 0.6 }
                        });
                    }
                    
                    const bsModal = bootstrap.Modal.getInstance(modalEl);
                    if (bsModal) bsModal.hide();
                    
                    // إعادة تحميل الصفحة لعرض التحديثات
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                })
                .catch(error => {
                    console.error('Redemption error:', error);
                    alert('خطأ: ' + error.message);
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = originalText;
                });
            });
        }
    });
</script>
