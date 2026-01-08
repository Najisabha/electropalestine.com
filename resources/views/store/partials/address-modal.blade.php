@php
    /** @var \App\Models\User|null $authUser */
    $authUser = auth()->user();
    $addresses = $authUser?->addresses ?? collect();
@endphp

@if($authUser)
<div class="modal fade" id="shippingAddressesModal" tabindex="-1" aria-labelledby="shippingAddressesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass text-light border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-semibold" id="shippingAddressesLabel">
                    {{ __('addresses.section_title') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('common.cancel') }}"></button>
            </div>
            <div class="modal-body">
                {{-- قائمة العناوين --}}
                @forelse($addresses as $address)
                    <div class="glass mb-3 rounded-3 p-3 d-flex flex-column flex-md-row align-items-start gap-3">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <div class="fw-semibold text-start">
                                    {{ $address->first_name }} {{ $address->last_name }}
                                    @if($address->phone)
                                        <span class="text-secondary small ms-2">
                                            {{ $address->country_code }} {{ $address->phone }}
                                        </span>
                                    @endif
                                </div>
                                <a href="{{ route('store.account-settings') }}#addressCollapse"
                                   class="small text-decoration-none">
                                    {{ __('addresses.edit_address') }}
                                </a>
                            </div>
                            <div class="text-secondary small text-start">
                                @if($address->city)
                                    {{ $address->city }}<br>
                                @endif
                                @if($address->street)
                                    {{ $address->street }}<br>
                                @endif
                                @if($address->governorate || $address->zip_code)
                                    {{ $address->governorate }}{{ $address->governorate && $address->zip_code ? ', ' : '' }}{{ $address->zip_code }}
                                @endif
                            </div>
                        </div>
                        <div class="d-flex flex-column align-items-center ms-md-auto">
                            <form method="POST" action="{{ route('store.addresses.default', $address) }}">
                                @csrf
                                <input type="radio"
                                       class="form-check-input mb-2"
                                       name="selected_address_id"
                                       value="{{ $address->id }}"
                                       @checked($address->is_default)
                                       onchange="this.form.submit()">
                            </form>
                            @if($address->is_default)
                                <span class="badge bg-success-subtle text-success small">
                                    {{ __('addresses.default') }}
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-secondary small mb-0 text-center">
                        {{ __('addresses.no_addresses') }}
                    </p>
                @endforelse
            </div>
            <div class="modal-footer border-0 flex-column">
                <a href="{{ route('store.account-settings') }}#addressCollapse"
                   class="btn btn-main w-100">
                    {{ __('addresses.add_new') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endif

