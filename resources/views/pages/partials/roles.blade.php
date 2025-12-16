<section class="container py-4 text-light">
    <div class="glass p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <p class="text-success small mb-1">إدارة الأدوار و الصلاحيات</p>
                <h1 class="h4 fw-bold mb-0">الأدوار في النظام</h1>
                <p class="text-secondary small mb-0">
                    هنا يمكنك إنشاء أدوار جديدة، تحديد الصلاحيات لكل دور، ثم ربط هذه الأدوار بالمستخدمين من صفحة "إظهار المستخدمين".
                </p>
            </div>
            <span class="badge bg-success text-dark">{{ $roles->count() }} دور</span>
        </div>

        @if (session('status'))
            <div class="alert alert-success small py-2 mb-3">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.roles.store') }}" class="row g-3">
            @csrf
            <div class="col-12 col-md-4">
                <label class="form-label small text-secondary">اسم الدور (يظهر في لوحة التحكم)</label>
                <input type="text" name="name" class="form-control form-control-sm auth-input" value="{{ old('name') }}" placeholder="مثال: مدير النظام" required>
                @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label small text-secondary">مفتاح الدور (بالإنجليزية)</label>
                <input type="text" name="key" class="form-control form-control-sm auth-input" value="{{ old('key') }}" placeholder="مثال: admin, editor, support" required>
                @error('key')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label small text-secondary">وصف مختصر</label>
                <input type="text" name="description" class="form-control form-control-sm auth-input" value="{{ old('description') }}" placeholder="مثال: يمتلك جميع الصلاحيات في المتجر">
            </div>
            <div class="col-12">
                <label class="form-label small text-secondary">الصلاحيات (اكتبها مفصولة بفاصلة , )</label>
                <textarea name="permissions" class="form-control form-control-sm auth-input" rows="2" placeholder="مثال: إدارة المستخدمين, إدارة الطلبات, إدارة التصنيفات">{{ old('permissions') }}</textarea>
                <div class="form-text text-muted small mt-1">
                    هذه الصلاحيات حالياً وصفية (تستخدم للوضوح في لوحة التحكم)، ويمكن ربطها لاحقاً بنظام تحقق فعلي في الكود.
                </div>
            </div>
            <div class="col-12 d-flex justify-content-end">
                <button class="btn btn-main btn-sm px-4">إضافة دور جديد</button>
            </div>
        </form>
    </div>

    <div class="glass p-4">
        <h2 class="h5 fw-bold mb-3">الأدوار الحالية</h2>
        @if($roles->isEmpty())
            <p class="text-secondary small mb-0">لا توجد أدوار بعد. قم بإنشاء أول دور من النموذج أعلاه.</p>
        @else
            <div class="table-responsive small">
                <table class="table table-dark table-striped align-middle mb-0">
                    <thead>
                    <tr class="text-secondary">
                        <th>#</th>
                        <th>اسم الدور</th>
                        <th>المفتاح</th>
                        <th>الوصف</th>
                        <th>الصلاحيات</th>
                        <th>إجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td>{{ $role->name }}</td>
                            <td><span class="badge bg-secondary">{{ $role->key }}</span></td>
                            <td>{{ $role->description }}</td>
                            <td>
                                @if (!empty($role->permissions))
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($role->permissions as $perm)
                                            <span class="badge bg-info text-dark">{{ $perm }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-secondary">لا توجد صلاحيات محددة</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <button class="btn btn-sm btn-outline-main" type="button" data-bs-toggle="collapse" data-bs-target="#edit-role-{{ $role->id }}">
                                        تعديل
                                    </button>
                                    <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" type="submit">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr class="collapse bg-black" id="edit-role-{{ $role->id }}">
                            <td colspan="6">
                                <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="row g-2 mt-2">
                                    @csrf
                                    @method('PUT')
                                    <div class="col-12 col-md-3">
                                        <label class="form-label small text-secondary mb-1">اسم الدور</label>
                                        <input type="text" name="name" class="form-control form-control-sm auth-input" value="{{ old('name', $role->name) }}">
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label class="form-label small text-secondary mb-1">مفتاح الدور</label>
                                        <input type="text" name="key" class="form-control form-control-sm auth-input" value="{{ old('key', $role->key) }}">
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label class="form-label small text-secondary mb-1">الوصف</label>
                                        <input type="text" name="description" class="form-control form-control-sm auth-input" value="{{ old('description', $role->description) }}">
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label class="form-label small text-secondary mb-1">الصلاحيات (مفصولة بفاصلة)</label>
                                        <input type="text" name="permissions" class="form-control form-control-sm auth-input"
                                               value="{{ old('permissions', is_array($role->permissions) ? implode(', ', $role->permissions) : '') }}">
                                    </div>
                                    <div class="col-12 d-flex justify-content-end">
                                        <button class="btn btn-sm btn-main px-4">حفظ التعديلات</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</section>


