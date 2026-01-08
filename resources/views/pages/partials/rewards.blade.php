<section class="container py-4 text-light">
    <div class="glass p-4 mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
            <div>
                <p class="text-success small mb-1">مركز مكافآت الولاء</p>
                <h1 class="h4 fw-bold mb-2">إدارة جميع المكافآت المتاحة للمستخدمين</h1>
                <p class="text-secondary small mb-0">
                    يمكنك إنشاء مكافآت جديدة، تعديل القيم والنقاط المطلوبة، والتحكم في حالة التفعيل لكل مكافأة.
                </p>
            </div>
            <button class="btn btn-main btn-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#rewardModal">
                <i class="bi bi-gift"></i>
                <span>إضافة مكافأة جديدة</span>
            </button>
        </div>
    </div>

    <div class="glass p-4">
        <div class="table-responsive small">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead>
                <tr class="text-secondary">
                    <th>القيمة / المنتج</th>
                    <th>العنوان</th>
                    <th>النوع</th>
                    <th>النقاط المطلوبة</th>
                    <th>الكمية</th>
                    <th>الحالة</th>
                    <th class="text-end">إجراءات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($rewards as $reward)
                    @php
                        $type = $reward->type;
                        $typeBadge = match($type) {
                            'wallet_credit' => 'success',
                            'coupon' => 'info',
                            'gift' => 'warning',
                            default => 'secondary',
                        };
                        $typeLabel = match($type) {
                            'wallet_credit' => 'رصيد محفظة',
                            'coupon' => 'كوبون خصم',
                            'gift' => 'هدية عينية',
                            default => $type,
                        };
                    @endphp
                    <tr>
                        <td>
                            @if($reward->type === 'gift')
                                @if($reward->product)
                                    <div class="d-flex align-items-center gap-2">
                                        @if($reward->product->thumbnail)
                                            <img src="{{ asset('storage/'.$reward->product->thumbnail) }}" alt="" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                        @endif
                                        <div class="small">
                                            <div class="fw-semibold">{{ $reward->product->translated_name }}</div>
                                            <div class="text-secondary">{{ $reward->product->price ? '$'.number_format($reward->product->price, 2) : '' }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">لم يتم ربط منتج</span>
                                @endif
                            @elseif($reward->type === 'wallet_credit')
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    رصيد {{ number_format($reward->value ?? 0, 2) }} $
                                </span>
                            @elseif($reward->type === 'coupon')
                                <span class="badge bg-info-subtle text-info border border-info-subtle">
                                    خصم {{ number_format($reward->value ?? 0, 2) }}
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">بدون قيمة</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $reward->title_translated }}</div>
                            @if($reward->description_translated)
                                <div class="text-secondary small">{{ \Illuminate\Support\Str::limit($reward->description_translated, 60) }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $typeBadge }}">{{ $typeLabel }}</span>
                        </td>
                        <td>{{ number_format($reward->points_required) }}</td>
                        <td>{{ $reward->stock ?? '-' }}</td>
                        <td>
                            @if($reward->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">نشطة</span>
                            @else
                                <span class="badge bg-secondary">معطلة</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <button
                                class="btn btn-sm btn-outline-light me-1 reward-edit-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#rewardModal"
                                data-id="{{ $reward->id }}"
                                data-title-ar="{{ is_array($reward->title) ? ($reward->title['ar'] ?? '') : '' }}"
                                data-title-en="{{ is_array($reward->title) ? ($reward->title['en'] ?? '') : '' }}"
                                data-description-ar="{{ is_array($reward->description) ? ($reward->description['ar'] ?? '') : '' }}"
                                data-description-en="{{ is_array($reward->description) ? ($reward->description['en'] ?? '') : '' }}"
                                data-type="{{ $reward->type }}"
                                data-points="{{ $reward->points_required }}"
                                data-value="{{ $reward->value }}"
                                data-stock="{{ $reward->stock }}"
                                data-coupon="{{ $reward->coupon_code }}"
                                data-active="{{ $reward->is_active ? 1 : 0 }}"
                                data-product-id="{{ $reward->product_id }}"
                                data-product-category-id="{{ optional($reward->product)->category_id }}"
                                data-product-type-id="{{ optional($reward->product)->type_id }}"
                            >
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('admin.rewards.destroy', $reward) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه المكافأة؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-secondary py-4">
                            لا توجد مكافآت حتى الآن. استخدم زر <strong>إضافة مكافأة جديدة</strong> أعلاه.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Modal إضافة / تعديل مكافأة -->
<div class="modal fade" id="rewardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark text-light border border-secondary">
            <form id="rewardForm" action="{{ route('admin.rewards.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="rewardFormMethod" value="POST">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="rewardModalTitle">إضافة مكافأة جديدة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">العنوان (عربي)</label>
                            <input type="text" name="title_ar" id="rewardTitleAr" class="form-control bg-dark text-light border-secondary" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">العنوان (إنجليزي)</label>
                            <input type="text" name="title_en" id="rewardTitleEn" class="form-control bg-dark text-light border-secondary" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الوصف (عربي)</label>
                            <input type="text" name="description_ar" id="rewardDescriptionAr" class="form-control bg-dark text-light border-secondary">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الوصف (إنجليزي)</label>
                            <input type="text" name="description_en" id="rewardDescriptionEn" class="form-control bg-dark text-light border-secondary">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">النقاط المطلوبة</label>
                            <input type="number" name="points_required" id="rewardPoints" class="form-control bg-dark text-light border-secondary" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">نوع المكافأة</label>
                            <select name="type" id="rewardType" class="form-select bg-dark text-light border-secondary" required>
                                <option value="gift">هدية عينية</option>
                                <option value="wallet_credit">رصيد محفظة</option>
                                <option value="coupon">كوبون خصم</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="rewardActive" name="is_active" checked>
                                <label class="form-check-label" for="rewardActive">
                                    مفعلة
                                </label>
                            </div>
                        </div>
                    </div>

                    <hr class="border-secondary my-3">

                    {{-- حقول ديناميكية --}}
                    <div id="giftFields" class="row g-3 dynamic-fields d-none">
                        <div class="col-12">
                            <label class="form-label">البحث عن المنتج بالاسم</label>
                            <div class="position-relative">
                                <input
                                    type="text"
                                    id="giftProductSearch"
                                    class="form-control bg-dark text-light border-secondary"
                                    placeholder="اكتب اسم المنتج لاختيار التصنيف والقسم تلقائياً"
                                    autocomplete="off"
                                >
                                <div id="giftProductResults" class="gift-search-dropdown d-none"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">التصنيف الرئيسي</label>
                            <select id="giftCategory" class="form-select bg-dark text-light border-secondary">
                                <option value="">اختر التصنيف</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->translated_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">القسم</label>
                            <select id="giftType" class="form-select bg-dark text-light border-secondary">
                                <option value="">اختر القسم</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">المنتج</label>
                            <select id="giftProduct" class="form-select bg-dark text-light border-secondary">
                                <option value="">اختر المنتج</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الكمية / المخزون</label>
                            <input type="number" name="stock" id="rewardStock" class="form-control bg-dark text-light border-secondary" min="0">
                        </div>
                    </div>

                    <div id="walletFields" class="row g-3 dynamic-fields d-none">
                        <div class="col-md-6">
                            <label class="form-label">قيمة الرصيد ($)</label>
                            <input type="number" step="0.01" id="rewardValueWallet" class="form-control bg-dark text-light border-secondary" min="0.01">
                        </div>
                    </div>

                    <div id="couponFields" class="row g-3 dynamic-fields d-none">
                        <div class="col-md-6">
                            <label class="form-label">قيمة الخصم (%) أو مبلغ</label>
                            <input type="number" step="0.01" id="rewardValueCoupon" class="form-control bg-dark text-light border-secondary" min="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رمز الكوبون</label>
                            <input type="text" id="rewardCouponCode" class="form-control bg-dark text-light border-secondary">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-main">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .gift-search-dropdown {
        position: absolute;
        left: 0;
        right: 0;
        top: 100%;
        margin-top: 4px;
        background: transparent;
        border-radius: 16px;
        max-height: 320px;
        overflow-y: auto;
        overflow-x: hidden;
        z-index: 9999 !important;
        padding: 4px;
        box-shadow: 0 14px 40px rgba(0,0,0,0.8);
        width: 100%;
        display: block !important;
    }
    .gift-search-dropdown.d-none {
        display: none !important;
    }
    .gift-search-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        width: 100%;
        border: 0;
        background: rgba(17,21,24,0.98);
        border-radius: 999px;
        color: #eaf6ef;
        text-align: start;
        cursor: pointer;
        border: 1px solid rgba(255,255,255,0.08);
        box-shadow: 0 4px 16px rgba(0,0,0,0.6);
        margin-bottom: 6px;
        transition: all 0.25s ease;
    }
    .gift-search-item:hover {
        background: linear-gradient(135deg, rgba(13,183,119,0.2), rgba(11,13,17,0.95));
        border-color: rgba(13,183,119,0.6);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(13,183,119,0.3);
    }
    .gift-search-thumb {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        background: #111518;
        box-shadow: 0 0 0 2px rgba(255,255,255,0.08);
        flex-shrink: 0;
    }
    .gift-search-item-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 0.25rem;
    }
    .gift-search-item-price {
        font-size: 0.85rem;
        color: #f5d10c;
        font-weight: 700;
    }
    .gift-search-item-meta {
        font-size: 0.75rem;
        color: #9fb4a4;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('rewardType');
        const giftFields = document.getElementById('giftFields');
        const walletFields = document.getElementById('walletFields');
        const couponFields = document.getElementById('couponFields');

        const allTypes = @json($typesForJs ?? []);
        const allProducts = @json($productsForJs ?? []);

        const giftCategory = document.getElementById('giftCategory');
        const giftType = document.getElementById('giftType');
        const giftProduct = document.getElementById('giftProduct');
        const giftProductSearch = document.getElementById('giftProductSearch');
        const giftProductResults = document.getElementById('giftProductResults');
        let searchTimeout = null;

        function toggleFields() {
            if (!typeSelect || !giftFields || !walletFields || !couponFields) {
                return;
            }
            
            const type = typeSelect.value || 'gift';
            
            // الحصول على الحقول
            const giftProduct = document.getElementById('giftProduct');
            const walletValue = document.getElementById('rewardValueWallet');
            const couponValue = document.getElementById('rewardValueCoupon');
            const couponCode = document.getElementById('rewardCouponCode');
            
            // إظهار/إخفاء الحقول حسب النوع وإدارة الـ name attributes
            if (type === 'gift') {
                giftFields.classList.remove('d-none');
                walletFields.classList.add('d-none');
                couponFields.classList.add('d-none');
                
                // إضافة name للحقول المطلوبة وإزالتها من غير المطلوبة
                if (giftProduct) giftProduct.setAttribute('name', 'product_id');
                if (walletValue) walletValue.removeAttribute('name');
                if (couponValue) couponValue.removeAttribute('name');
                if (couponCode) couponCode.removeAttribute('name');
                
                updateGiftTypeOptions();
                updateGiftProductOptions();
            } else if (type === 'wallet_credit') {
                giftFields.classList.add('d-none');
                walletFields.classList.remove('d-none');
                couponFields.classList.add('d-none');
                
                // إضافة name للحقول المطلوبة وإزالتها من غير المطلوبة
                if (giftProduct) giftProduct.removeAttribute('name');
                if (walletValue) walletValue.setAttribute('name', 'value');
                if (couponValue) couponValue.removeAttribute('name');
                if (couponCode) couponCode.removeAttribute('name');
            } else if (type === 'coupon') {
                giftFields.classList.add('d-none');
                walletFields.classList.add('d-none');
                couponFields.classList.remove('d-none');
                
                // إضافة name للحقول المطلوبة وإزالتها من غير المطلوبة
                if (giftProduct) giftProduct.removeAttribute('name');
                if (walletValue) walletValue.removeAttribute('name');
                if (couponValue) couponValue.setAttribute('name', 'value');
                if (couponCode) couponCode.setAttribute('name', 'coupon_code');
            } else {
                // افتراضي: إخفاء كل الحقول
                giftFields.classList.add('d-none');
                walletFields.classList.add('d-none');
                couponFields.classList.add('d-none');
            }
        }

        function updateGiftTypeOptions(selectedTypeId = null) {
            if (!giftType) return;
            const categoryId = giftCategory ? giftCategory.value : '';
            giftType.innerHTML = '<option value=\"\">اختر القسم</option>';
            allTypes
                .filter(t => !categoryId || String(t.category_id) === String(categoryId))
                .forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t.id;
                    opt.textContent = t.name;
                    if (selectedTypeId && String(selectedTypeId) === String(t.id)) {
                        opt.selected = true;
                    }
                    giftType.appendChild(opt);
                });
        }

        function updateGiftProductOptions(selectedProductId = null) {
            if (!giftProduct) return;
            const categoryId = giftCategory ? giftCategory.value : '';
            const typeId = giftType ? giftType.value : '';
            giftProduct.innerHTML = '<option value=\"\">اختر المنتج</option>';
            allProducts
                .filter(p => (!categoryId || String(p.category_id) === String(categoryId)) &&
                             (!typeId || String(p.type_id) === String(typeId)))
                .forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.id;
                    opt.textContent = p.name;
                    if (selectedProductId && String(selectedProductId) === String(p.id)) {
                        opt.selected = true;
                    }
                    giftProduct.appendChild(opt);
                });
        }

        if (giftCategory) {
            giftCategory.addEventListener('change', () => {
                updateGiftTypeOptions();
                updateGiftProductOptions();
            });
        }

        if (giftType) {
            giftType.addEventListener('change', () => {
                updateGiftProductOptions();
            });
        }

        function selectProductByName(name) {
            if (!name) return;
            const term = name.trim().toLowerCase();
            if (!term) return;

            let product = allProducts.find(p => (p.name || '').toLowerCase() === term);
            if (!product) {
                product = allProducts.find(p => (p.name || '').toLowerCase().includes(term));
            }
            if (!product) return;

            if (giftCategory) giftCategory.value = product.category_id ? String(product.category_id) : '';
            updateGiftTypeOptions(product.type_id || null);
            updateGiftProductOptions(product.id || null);
        }

        function clearSearchResults() {
            if (!giftProductResults) return;
            giftProductResults.innerHTML = '';
            giftProductResults.classList.add('d-none');
        }

        if (giftProductSearch && giftProductResults) {
            giftProductSearch.addEventListener('input', (e) => {
                const term = e.target.value.trim();
                clearTimeout(searchTimeout);

                if (!term || term.length === 0) {
                    clearSearchResults();
                    return;
                }

                searchTimeout = setTimeout(() => {
                    if (!giftProductResults || !giftProductSearch) return;
                    
                    const url = "{{ route('admin.products.search') }}?q=" + encodeURIComponent(term);
                    fetch(url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    })
                        .then(r => {
                            if (!r.ok) {
                                console.error('Search failed:', r.status, r.statusText);
                                throw new Error('Search request failed');
                            }
                            return r.json();
                        })
                        .then(data => {
                            if (!giftProductResults) return;
                            
                            giftProductResults.innerHTML = '';
                            
                            // التحقق من وجود error في الاستجابة
                            if (data && typeof data === 'object' && data.error) {
                                throw new Error(data.error);
                            }
                            
                            // التأكد من أن data هو array
                            const items = Array.isArray(data) ? data : (data ? [data] : []);
                            
                            if (!items || !items.length) {
                                const emptyBtn = document.createElement('div');
                                emptyBtn.className = 'gift-search-item';
                                emptyBtn.style.pointerEvents = 'none';
                                emptyBtn.innerHTML = `
                                    <div class="flex-grow-1 text-center text-secondary py-2">
                                        هذا المنتج غير متوفر حالياً
                                    </div>
                                `;
                                giftProductResults.appendChild(emptyBtn);
                            } else {
                                items.forEach(p => {
                                    const btn = document.createElement('button');
                                    btn.type = 'button';
                                    btn.className = 'gift-search-item';

                                    const imageUrl = p.image_url || '{{ asset('images/LOGO.jpeg') }}';
                                    
                                    btn.innerHTML = `
                                        <img src="${imageUrl}" class="gift-search-thumb" alt="${(p.name || '').replace(/"/g, '&quot;')}" onerror="this.src='{{ asset('images/LOGO.jpeg') }}'">
                                        <div class="flex-grow-1 d-flex flex-column gap-1">
                                            <div class="gift-search-item-title">${(p.name || '').replace(/</g, '&lt;').replace(/>/g, '&gt;')}</div>
                                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                                <div>
                                                    <span class="text-secondary small">سعر البيع:</span>
                                                    <span class="gift-search-item-price ms-1">$${(p.price ?? 0).toFixed(2)}</span>
                                                </div>
                                                <div>
                                                    <span class="text-secondary small">سعر الجملة:</span>
                                                    <span class="text-info ms-1">$${(p.cost_price ?? 0).toFixed(2)}</span>
                                                </div>
                                                <div>
                                                    <span class="text-secondary small">المخزون:</span>
                                                    <span class="text-success ms-1 fw-bold">${p.stock ?? 0}</span>
                                                </div>
                                            </div>
                                        </div>
                                    `;

                                    btn.addEventListener('click', () => {
                                        giftProductSearch.value = p.name || '';
                                        if (giftCategory && p.category_id) {
                                            giftCategory.value = String(p.category_id);
                                        }
                                        updateGiftTypeOptions(p.type_id || null);
                                        updateGiftProductOptions(p.id || null);
                                        clearSearchResults();
                                    });

                                    giftProductResults.appendChild(btn);
                                });
                            }
                            
                            giftProductResults.classList.remove('d-none');
                        })
                        .catch((error) => {
                            console.error('Search error:', error);
                            if (giftProductResults) {
                                giftProductResults.innerHTML = '';
                                const errorBtn = document.createElement('div');
                                errorBtn.className = 'gift-search-item';
                                errorBtn.style.pointerEvents = 'none';
                                errorBtn.style.cursor = 'default';
                                errorBtn.innerHTML = `
                                    <div class="flex-grow-1 text-center text-danger py-2">
                                        <i class="bi bi-exclamation-triangle me-2"></i>
                                        حدث خطأ في البحث. يرجى المحاولة مرة أخرى.
                                    </div>
                                `;
                                giftProductResults.appendChild(errorBtn);
                                giftProductResults.classList.remove('d-none');
                                
                                // إخفاء رسالة الخطأ بعد 5 ثواني
                                setTimeout(() => {
                                    if (giftProductResults && !giftProductSearch.value.trim()) {
                                        clearSearchResults();
                                    }
                                }, 5000);
                            }
                        });
                }, 300);
            });

            // عند الضغط على Enter يتم اختيار المنتج تلقائياً إن وُجد
            giftProductSearch.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    selectProductByName(giftProductSearch.value);
                    clearSearchResults();
                }
            });

            document.addEventListener('click', (event) => {
                if (!giftProductResults.contains(event.target) && event.target !== giftProductSearch) {
                    clearSearchResults();
                }
            });
        }

        if (typeSelect) {
            typeSelect.addEventListener('change', toggleFields);
            toggleFields();
        }

        // إعداد نموذج الإضافة / التعديل
        const rewardModal = document.getElementById('rewardModal');
        const rewardForm = document.getElementById('rewardForm');
        const methodInput = document.getElementById('rewardFormMethod');
        const modalTitle = document.getElementById('rewardModalTitle');

        rewardModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const isEdit = button && button.classList.contains('reward-edit-btn');

            // إعادة تعيين النموذج
            rewardForm.reset();
            document.getElementById('rewardActive').checked = true;
            methodInput.value = 'POST';
            rewardForm.action = "{{ route('admin.rewards.store') }}";
            modalTitle.textContent = 'إضافة مكافأة جديدة';

            if (isEdit) {
                const id = button.getAttribute('data-id');
                const titleAr = button.getAttribute('data-title-ar') || '';
                const titleEn = button.getAttribute('data-title-en') || '';
                const descAr = button.getAttribute('data-description-ar') || '';
                const descEn = button.getAttribute('data-description-en') || '';
                const type = button.getAttribute('data-type') || 'gift';
                const points = button.getAttribute('data-points') || '';
                const value = button.getAttribute('data-value') || '';
                const stock = button.getAttribute('data-stock') || '';
                const coupon = button.getAttribute('data-coupon') || '';
                const active = button.getAttribute('data-active') === '1';
                const productId = button.getAttribute('data-product-id') || '';
                const productCategoryId = button.getAttribute('data-product-category-id') || '';
                const productTypeId = button.getAttribute('data-product-type-id') || '';

                document.getElementById('rewardTitleAr').value = titleAr;
                document.getElementById('rewardTitleEn').value = titleEn;
                document.getElementById('rewardDescriptionAr').value = descAr;
                document.getElementById('rewardDescriptionEn').value = descEn;
                document.getElementById('rewardPoints').value = points;
                document.getElementById('rewardType').value = type;
                document.getElementById('rewardStock').value = stock;
                document.getElementById('rewardValueWallet').value = type === 'wallet_credit' ? value : '';
                document.getElementById('rewardValueCoupon').value = type === 'coupon' ? value : '';
                document.getElementById('rewardCouponCode').value = coupon;
                document.getElementById('rewardActive').checked = active;

                if (type === 'gift') {
                    if (giftCategory) giftCategory.value = productCategoryId || '';
                    updateGiftTypeOptions(productTypeId || null);
                    updateGiftProductOptions(productId || null);
                }

                methodInput.value = 'PUT';
                rewardForm.action = "{{ url('admin/rewards') }}/" + id;
                modalTitle.textContent = 'تعديل مكافأة';
            }
            
            // استدعاء toggleFields بعد فتح المودال لضمان إظهار الحقول الصحيحة
            setTimeout(() => {
                toggleFields();
            }, 100);
        });
        
        // إعادة استدعاء toggleFields عند ظهور المودال بالكامل
        if (rewardModal) {
            rewardModal.addEventListener('shown.bs.modal', function() {
                toggleFields();
            });
        }
    });
</script>

