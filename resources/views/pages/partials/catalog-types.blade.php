<section class="container py-4 text-light">
    <div class="d-flex justify-content-between align-items-center mb-3">
        @php($categoryName = optional($types->first())->category->name ?? null)
        <h5 class="mb-0">
            جميع الأنواع
            @if(!empty($categoryId) && $categoryName)
                <span class="text-secondary small"> (للصنف: {{ $categoryName }})</span>
            @endif
        </h5>
        <a href="{{ route('admin.catalog') }}" class="btn btn-sm btn-outline-main">الرجوع إلى إدارة التصنيفات</a>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm bg-dark text-light" placeholder="بحث باسم النوع">
        </div>
        <div class="col-md-2">
            <button class="btn btn-sm btn-main w-100">بحث</button>
        </div>
    </form>

    <div class="glass p-3">
        <div class="table-responsive">
            <table class="table table-dark table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>النوع</th>
                        <th>الصنف الرئيسي</th>
                        <th>صورة</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($types as $type)
                        <tr>
                            <td>{{ $type->name }}</td>
                            <td>{{ $type->category->name ?? '-' }}</td>
                            <td>
                                @if ($type->image)
                                    <img src="{{ asset('storage/' . $type->image) }}" alt="" style="height:40px;">
                                @else
                                    <span class="text-secondary small">لا توجد</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-sm btn-outline-main" data-bs-toggle="collapse" data-bs-target="#edit-type-{{ $type->id }}">تعديل</button>
                                    <form method="POST" action="{{ route('admin.catalog.type.delete', $type) }}" onsubmit="return confirm('حذف النوع؟ سيحذف المنتجات التابعة.');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr class="collapse bg-dark" id="edit-type-{{ $type->id }}">
                            <td colspan="4">
                                <form method="POST" action="{{ route('admin.catalog.type.update', $type) }}" class="row g-2 align-items-end" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="col-md-4">
                                        <label class="form-label small text-secondary mb-0">اسم النوع</label>
                                        <input type="text" name="name" value="{{ $type->name }}" class="form-control form-control-sm bg-dark text-light">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-secondary mb-0">صورة (اختياري)</label>
                                        <input type="file" name="image" class="form-control form-control-sm bg-dark text-light">
                                    </div>
                                    <div class="col-12 d-flex gap-2 mt-2">
                                        <button class="btn btn-sm btn-main">حفظ التعديلات</button>
                                        <button type="button" class="btn btn-sm btn-outline-main" data-bs-toggle="collapse" data-bs-target="#edit-type-{{ $type->id }}">إغلاق</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-secondary">لا توجد أنواع بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $types->links() }}
        </div>
    </div>
</section>


