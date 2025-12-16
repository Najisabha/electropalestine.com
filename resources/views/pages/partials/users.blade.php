@php use Illuminate\Support\Facades\Storage; @endphp
<section class="container py-4 text-light">
    <div class="glass p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 fw-bold mb-0">إظهار المستخدمين</h1>
            <span class="badge bg-success text-dark">{{ $users->total() }} مستخدم</span>
        </div>
        <p class="text-secondary small mb-3">عرض البيانات الكاملة للمستخدمين وإدارة أدوارهم.</p>
        <div class="table-responsive">
            <table class="table table-dark table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم الأول</th>
                        <th>الاسم الأخير</th>
                        <th>البريد</th>
                        <th>مقدمة واتساب</th>
                        <th>رقم الجوال</th>
                        <th>تاريخ الميلاد</th>
                        <th>الدور الحالي</th>
                        <th>صورة الهوية</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->first_name }}</td>
                            <td>{{ $user->last_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->whatsapp_prefix }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>{{ $user->birth_year }}-{{ str_pad($user->birth_month, 2, '0', STR_PAD_LEFT) }}-{{ str_pad($user->birth_day, 2, '0', STR_PAD_LEFT) }}</td>
                            <td><span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-info text-dark' }}">{{ $user->role }}</span></td>
                            <td>
                                @if ($user->id_image)
                                    <a href="{{ asset('storage/'.$user->id_image) }}" target="_blank" class="link-success small">عرض</a>
                                @else
                                    <span class="text-secondary small">لا يوجد</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <form method="POST" action="{{ route('admin.users.role', $user) }}" class="d-flex gap-1 flex-wrap">
                                        @csrf
                                        @method('PUT')
                                        <select name="role" class="form-select form-select-sm bg-dark text-light border-secondary w-auto">
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->key }}" {{ $user->role === $role->key ? 'selected' : '' }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-main">تحديث الدور</button>
                                    </form>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <button type="button" class="btn btn-sm btn-outline-main">توثيق الهوية</button>
                                        <button type="button" class="btn btn-sm btn-danger">حذف المستخدم</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-secondary">لا يوجد مستخدمون.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $users->links() }}
        </div>
    </div>
</section>

