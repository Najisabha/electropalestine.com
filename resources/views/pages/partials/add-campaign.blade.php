<section class="container py-4 text-light">
    @if (session('status'))
        <div class="alert alert-success small py-2 mb-3">{{ session('status') }}</div>
    @endif

    {{-- نموذج إنشاء / تعديل الحملة الإعلانية في مربع واحد منسّق --}}
    <form method="POST" action="{{ route('admin.campaign.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="glass p-4">
            <div class="row g-3">
                {{-- اسم الحملة --}}
                <div class="col-12 col-lg-6">
                    <label class="form-label small text-secondary">اسم الحملة</label>
                    <input type="text"
                           name="title"
                           class="form-control form-control-sm auth-input"
                           placeholder="مثال: حملة تخفيضات نهاية العام"
                           value="{{ old('title') }}"
                           required>
                </div>

                {{-- تواريخ البداية والنهاية --}}
                <div class="col-6 col-lg-3">
                    <label class="form-label small text-secondary">تاريخ البدء</label>
                    <input type="date"
                           name="start_date"
                           class="form-control form-control-sm auth-input"
                           value="{{ old('start_date') }}">
                </div>
                <div class="col-6 col-lg-3">
                    <label class="form-label small text-secondary">تاريخ الانتهاء</label>
                    <input type="date"
                           name="end_date"
                           class="form-control form-control-sm auth-input"
                           value="{{ old('end_date') }}">
                </div>

                {{-- وصف الحملة --}}
                <div class="col-12 col-lg-8">
                    <label class="form-label small text-secondary">وصف الحملة</label>
                    <textarea name="description"
                              class="form-control form-control-sm auth-input"
                              rows="3"
                              placeholder="تفاصيل مختصرة عن الحملة والرسالة الإعلانية">{{ old('description') }}</textarea>
                </div>

                {{-- صورة الحملة --}}
                <div class="col-12 col-lg-4">
                    <label class="form-label small text-secondary">صورة الحملة (اختياري)</label>
                    <input type="file" name="image" class="form-control form-control-sm auth-input">
                </div>

                {{-- نطاق الحملة: جدول بعناصر يتم إضافتها بزر + --}}
                <div class="col-12 col-lg-6">
                    <label class="form-label small text-secondary d-block mb-1">نطاق الحملة (اختياري)</label>
                    <p class="text-secondary small mb-2">
                        أضف عناصر إلى الحملة باستخدام زر
                        <strong>+</strong>.
                        عند إضافة:
                        <br>- <strong>صنف رئيسي</strong>: تشمل الحملة جميع الأنواع والشركات والمنتجات التابعة له.
                        <br>- <strong>نوع</strong>: تشمل الحملة المنتجات الموجودة داخل الشركات المرتبطة بهذا النوع فقط.
                        <br>- <strong>شركة</strong>: تشمل الحملة جميع المنتجات التابعة لهذه الشركة.
                        <br>- <strong>منتج</strong>: تشمل الحملة هذا المنتج فقط.
                    </p>

                    @php($categories = $categories ?? collect())
                    @php($types = $types ?? collect())
                    @php($companies = $companies ?? collect())
                    @php($products = $products ?? collect())

                    <div class="glass bg-dark bg-opacity-50 border border-secondary-subtle rounded-3 p-2">
                        <div class="table-responsive mb-2">
                            <table class="table table-dark table-sm align-middle mb-0">
                                <thead class="small text-secondary">
                                    <tr>
                                        <th style="width: 18%">نوع العنصر</th>
                                        <th style="width: 32%">الوصف</th>
                                        <th style="width: 25%">ما الذي سيتم تضمينه؟</th>
                                        <th style="width: 12%">نوع الخصم</th>
                                        <th style="width: 13%">قيمة الخصم</th>
                                        <th class="text-center" style="width: 10%">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody id="campaign-items-table-body">
                                    <tr id="campaign-items-empty-row">
                                        <td colspan="6" class="text-center text-secondary small">
                                            لم تتم إضافة أي عناصر بعد، استخدم زر <strong>+</strong> في الأسفل.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                {{-- التوصيل: زر مع ثلاث خيارات --}}
                <div class="col-12 col-lg-6">
                    <label class="form-label small text-secondary d-block mb-1">التوصيل</label>
                    <div class="btn-group btn-group-sm mb-2" role="group">
                        @php($shippingType = old('shipping_type','none'))
                        <input type="radio" class="btn-check" name="shipping_type" id="ship-none" value="none" autocomplete="off" {{ $shippingType === 'none' ? 'checked' : '' }}>
                        <label class="btn btn-outline-light" for="ship-none">لا يوجد توصيل مجاني</label>

                        <input type="radio" class="btn-check" name="shipping_type" id="ship-free" value="free" autocomplete="off" {{ $shippingType === 'free' ? 'checked' : '' }}>
                        <label class="btn btn-outline-light" for="ship-free">توصيل مجاني</label>

                        <input type="radio" class="btn-check" name="shipping_type" id="ship-conditional" value="conditional" autocomplete="off" {{ $shippingType === 'conditional' ? 'checked' : '' }}>
                        <label class="btn btn-outline-light" for="ship-conditional">توصيل مجاني بشرط</label>
                    </div>
                    <div id="shipping-conditional-box" class="{{ $shippingType === 'conditional' ? '' : 'd-none' }} mt-2">
                        <label class="form-label small text-secondary mb-1">الحد الأدنى لقيمة الطلب للحصول على التوصيل المجاني</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-dark text-secondary border-secondary">USD</span>
                            <input type="number" step="0.01" name="shipping_min_amount" class="form-control auth-input"
                                   value="{{ old('shipping_min_amount') }}" placeholder="مثال: 100">
                        </div>
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end mt-2">
                    <button type="submit" class="btn btn-main d-flex align-items-center gap-2 px-4">
                        <i class="bi bi-save"></i>
                        <span>حفظ الحملة</span>
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- 3) تم حذف لوحة الأداء التجريبية، يمكن لاحقاً ربطها بتقارير حقيقية من منصات الإعلان --}}
</section>

{{-- زر ومحرر إضافة عنصر (خارج الإطار) --}}
<div class="container mt-2 mb-3">
    <div id="campaign-item-editor" class="mb-3 border border-secondary-subtle rounded-3 p-3 d-none">
        <div class="row g-2 small">
            <div class="col-12">
                <span class="text-secondary">
                    اختر أولاً الصنف الرئيسي، ثم نوع المنتج أو الشركة، ثم المنتج (اختياري).
                    إذا لم تختر منتجاً سيتم تطبيق الخصم على جميع المنتجات داخل النطاق المحدد.
                </span>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-secondary mb-1">الصنف الرئيسي</label>
                <select id="ci-category" class="form-select form-select-sm bg-dark text-light border-secondary">
                    <option value="">بدون تحديد</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-secondary mb-1">نوع المنتج</label>
                <select id="ci-type" class="form-select form-select-sm bg-dark text-light border-secondary">
                    <option value="">بدون تحديد</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-secondary mb-1">الشركة</label>
                <select id="ci-company" class="form-select form-select-sm bg-dark text-light border-secondary">
                    <option value="">بدون تحديد</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-secondary mb-1">المنتج (اختياري)</label>
                <select id="ci-product" class="form-select form-select-sm bg-dark text-light border-secondary">
                    <option value="">بدون تحديد</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-secondary mb-1">نوع الخصم</label>
                <select id="ci-discount-type" class="form-select form-select-sm bg-dark text-light border-secondary">
                    <option value="none">بدون خصم</option>
                    <option value="percent">نسبة مئوية %</option>
                    <option value="amount">قيمة ثابتة</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-secondary mb-1">قيمة الخصم</label>
                <input id="ci-discount-value" type="number" step="0.01" class="form-control form-control-sm bg-dark text-light" placeholder="مثال: 10">
            </div>
            <div class="col-md-6 d-flex align-items-end justify-content-end gap-2">
                <button type="button" id="ci-cancel" class="btn btn-sm btn-outline-secondary">
                    إلغاء
                </button>
                <button type="button" id="ci-save" class="btn btn-sm btn-main">
                    إضافة إلى الجدول
                </button>
            </div>
        </div>
    </div>

    <div class="text-end">
        <button type="button" id="campaign-item-add" class="btn btn-sm btn-success px-4">
            <i class="bi bi-plus-lg"></i>
            <span>إضافة عنصر إلى الحملة</span>
        </button>
    </div>
</div>

<script>
    (function () {
        let itemIndex = 0;
        const addBtn = document.getElementById('campaign-item-add');
        const tableBody = document.getElementById('campaign-items-table-body');
        const emptyRow = document.getElementById('campaign-items-empty-row');
        const editor = document.getElementById('campaign-item-editor');
        const ciCategory = document.getElementById('ci-category');
        const ciType = document.getElementById('ci-type');
        const ciCompany = document.getElementById('ci-company');
        const ciProduct = document.getElementById('ci-product');
        const ciDiscountType = document.getElementById('ci-discount-type');
        const ciDiscountValue = document.getElementById('ci-discount-value');
        const ciSave = document.getElementById('ci-save');
        const ciCancel = document.getElementById('ci-cancel');

        if (!addBtn || !tableBody || !editor) {
            return;
        }

        const typeLabels = {
            category: 'صنف رئيسي',
            type: 'نوع',
            company: 'شركة',
            product: 'منتج',
        };

        const typeDescription = {
            category: 'يشمل جميع الأنواع والشركات والمنتجات التابعة لهذا الصنف.',
            type: 'يشمل المنتجات الموجودة داخل الشركات المرتبطة بهذا النوع.',
            company: 'يشمل جميع المنتجات التابعة لهذه الشركة.',
            product: 'يشمل هذا المنتج فقط.',
        };

        const inputNames = {
            category: 'category_ids[]',
            type: 'type_ids[]',
            company: 'company_ids[]',
            product: 'products[]',
        };

        function ensureNotEmpty() {
            if (emptyRow) {
                const hasRows = tableBody.querySelectorAll('tr[data-item-type]').length > 0;
                emptyRow.style.display = hasRows ? 'none' : '';
            }
        }

        function resetEditor() {
            ciCategory.value = '';
            ciType.value = '';
            ciCompany.value = '';
            ciProduct.value = '';
            ciDiscountType.value = 'none';
            ciDiscountValue.value = '';
        }

        function determineScope() {
            const categoryId = ciCategory.value || null;
            const typeId = ciType.value || null;
            const companyId = ciCompany.value || null;
            const productId = ciProduct.value || null;

            // أولوية أعمق: منتج > شركة > نوع > صنف
            if (productId) {
                return { scopeType: 'product', scopeId: productId };
            }
            if (companyId) {
                return { scopeType: 'company', scopeId: companyId };
            }
            if (typeId) {
                return { scopeType: 'type', scopeId: typeId };
            }
            if (categoryId) {
                return { scopeType: 'category', scopeId: categoryId };
            }
            return { scopeType: null, scopeId: null };
        }

        function getLabel(scopeType, scopeId) {
            let selectEl = null;
            if (scopeType === 'category') selectEl = ciCategory;
            if (scopeType === 'type') selectEl = ciType;
            if (scopeType === 'company') selectEl = ciCompany;
            if (scopeType === 'product') selectEl = ciProduct;
            if (!selectEl) return '';
            const opt = selectEl.querySelector(`option[value="${scopeId}"]`);
            return opt ? opt.textContent : '';
        }

        function addItemFromEditor() {
            const { scopeType, scopeId } = determineScope();
            if (!scopeType || !scopeId) {
                alert('اختر على الأقل صنفاً رئيسياً أو نوعاً أو شركة أو منتجاً قبل الإضافة.');
                return;
            }

            const discountType = ciDiscountType.value || 'none';
            const discountValue = ciDiscountValue.value || '';

            const id = scopeId;
            const label = getLabel(scopeType, scopeId);

            const tr = document.createElement('tr');
            tr.dataset.itemType = scopeType;
            tr.dataset.itemId = id;

            tr.innerHTML = `
                <td class="small">${typeLabels[scopeType] || ''}</td>
                <td class="small">${label}</td>
                <td class="small text-secondary">${typeDescription[scopeType] || ''}</td>
                <td>
                    <select name="items[${itemIndex}][discount_type]" class="form-select form-select-sm bg-dark text-light border-secondary">
                        <option value="none">بدون خصم</option>
                        <option value="percent">نسبة مئوية %</option>
                        <option value="amount">قيمة ثابتة</option>
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${itemIndex}][discount_value]" class="form-control form-control-sm bg-dark text-light" placeholder="مثال: 10">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger campaign-item-remove">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <input type="hidden" name="items[${itemIndex}][scope_type]" value="${scopeType}">
                    <input type="hidden" name="items[${itemIndex}][scope_id]" value="${id}">
                </td>
            `;

            tableBody.appendChild(tr);
            itemIndex++;
            resetEditor();
            ensureNotEmpty();
        }

        addBtn.addEventListener('click', function () {
            editor.classList.toggle('d-none');
        });

        ciSave.addEventListener('click', addItemFromEditor);

        ciCancel.addEventListener('click', function () {
            editor.classList.add('d-none');
            resetEditor();
        });

        tableBody.addEventListener('click', function (e) {
            const btn = e.target.closest('.campaign-item-remove');
            if (!btn) return;
            const row = btn.closest('tr');
            if (row) {
                row.remove();
                ensureNotEmpty();
            }
        });

        ensureNotEmpty();
    })();
</script>

