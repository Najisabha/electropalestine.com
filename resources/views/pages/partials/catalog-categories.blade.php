<section class="container py-4 text-light">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">جميع الأصناف الرئيسية</h5>
        <a href="{{ route('admin.catalog') }}" class="btn btn-sm btn-outline-main">الرجوع إلى إدارة التصنيفات</a>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm bg-dark text-light" placeholder="بحث باسم الصنف">
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
                        <th>الاسم</th>
                        <th>الوصف</th>
                        <th>صورة</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $cat)
                        <tr>
                            <td>{{ $cat->name }}</td>
                            <td>{{ $cat->description }}</td>
                            <td>
                                @if ($cat->image)
                                    <img src="{{ asset('storage/' . $cat->image) }}" alt="" style="height:40px;">
                                @else
                                    <span class="text-secondary small">لا توجد</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-sm btn-outline-main" data-bs-toggle="collapse" data-bs-target="#edit-cat-{{ $cat->id }}">تعديل</button>
                                    <form method="POST" action="{{ route('admin.catalog.category.delete', $cat) }}" onsubmit="return confirm('حذف الصنف؟ سيحذف الأنواع والمنتجات التابعة.');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr class="collapse bg-dark" id="edit-cat-{{ $cat->id }}">
                            <td colspan="4">
                                <form method="POST" action="{{ route('admin.catalog.category.update', $cat) }}" class="row g-2 align-items-end" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="col-md-4">
                                        <label class="form-label small text-secondary mb-0">اسم الصنف</label>
                                        <input type="text" name="name" value="{{ $cat->name }}" class="form-control form-control-sm bg-dark text-light">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-secondary mb-0">وصف (اختياري)</label>
                                        <input type="text" name="description" value="{{ $cat->description }}" class="form-control form-control-sm bg-dark text-light">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-secondary mb-0">صورة (اختياري)</label>
                                        <input type="file" name="image" class="form-control form-control-sm bg-dark text-light">
                                    </div>
                                    <div class="col-12 d-flex gap-2 mt-2">
                                        <button class="btn btn-sm btn-main">حفظ التعديلات</button>
                                        <button type="button" class="btn btn-sm btn-outline-main" data-bs-toggle="collapse" data-bs-target="#edit-cat-{{ $cat->id }}">إغلاق</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-secondary">لا توجد أصناف بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $categories->links() }}
        </div>
    </div>
</section>


