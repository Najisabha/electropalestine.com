<div class="max-w-6xl mx-auto px-6 py-10 space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-green-300">مرحباً {{ auth()->user()->name }}</p>
            <h1 class="text-2xl font-bold text-white">لوحة التحكم الإدارية</h1>
        </div>
        <div class="px-4 py-2 rounded-full bg-green-500/10 text-green-200 border border-green-500/30">
            قاعدة البيانات: الصنف الرئيسي • النوع • الشركة • المنتج
        </div>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="glass rounded-2xl p-4 border border-white/5">
            <p class="text-gray-400 text-sm">الأصناف الرئيسية</p>
            <div class="text-3xl font-black text-white">{{ $metrics['categories'] }}</div>
        </div>
        <div class="glass rounded-2xl p-4 border border-white/5">
            <p class="text-gray-400 text-sm">الأنواع</p>
            <div class="text-3xl font-black text-white">{{ $metrics['types'] }}</div>
        </div>
        <div class="glass rounded-2xl p-4 border border-white/5">
            <p class="text-gray-400 text-sm">الشركات</p>
            <div class="text-3xl font-black text-white">{{ $metrics['companies'] }}</div>
        </div>
        <div class="glass rounded-2xl p-4 border border-white/5">
            <p class="text-gray-400 text-sm">المنتجات</p>
            <div class="text-3xl font-black text-white">{{ $metrics['products'] }}</div>
        </div>
    </div>

    <div class="glass rounded-2xl p-6 border border-white/5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-white">أحدث المنتجات</h2>
            <span class="text-sm text-gray-400">آخر ٥ عناصر</span>
        </div>
        <div class="overflow-hidden rounded-xl border border-white/5">
            <table class="w-full text-sm text-gray-300">
                <thead class="bg-white/5 text-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-right">المنتج</th>
                        <th class="px-4 py-3 text-right">الصنف</th>
                        <th class="px-4 py-3 text-right">النوع</th>
                        <th class="px-4 py-3 text-right">الشركة</th>
                        <th class="px-4 py-3 text-right">السعر</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($latestProducts as $product)
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3 text-white font-medium">{{ $product->name }}</td>
                            <td class="px-4 py-3">{{ $product->category->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $product->type->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $product->company->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-green-300 font-semibold">${{ number_format($product->price, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-400">لا توجد بيانات بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

