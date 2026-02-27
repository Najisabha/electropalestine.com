<?php

namespace App\Http\Controllers;


use App\Models\Campaign;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\Type;
use App\Helpers\ImageHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AdminCampaignController extends Controller
{
    public function index(): View
    {
        $totalCampaigns = Campaign::count();
        $activeCampaigns = Campaign::where('is_active', true)->count();
        $recentCampaigns = Campaign::latest()->take(5)->get();

        return view('pages.campaigns', compact('totalCampaigns', 'activeCampaigns', 'recentCampaigns'));
    }

    public function create(): View
    {
        $products = Product::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $types = Type::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();

        return view('pages.add-campaign', compact('products', 'categories', 'types', 'companies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'shipping_type' => ['required', 'in:none,free,conditional'],
            'shipping_min_amount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['nullable', 'array'],
            'items.*.scope_type' => ['required_with:items', 'in:category,type,company,product'],
            'items.*.scope_id' => ['required_with:items', 'integer'],
            'items.*.discount_type' => ['nullable', 'in:none,percent,amount'],
            'items.*.discount_value' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = ImageHelper::storeWithSequentialName($request->file('image'), 'campaigns', 'public');
            if (!$data['image']) {
                Log::error('فشل رفع صورة الحملة', ['campaign_title' => $data['title']]);
                return back()->withErrors(['error' => 'فشل رفع صورة الحملة. يرجى التحقق من صلاحيات المجلدات.'])->withInput();
            }
        }

        if ($data['shipping_type'] !== 'conditional') {
            $data['shipping_min_amount'] = null;
        }

        // حالياً نفعل جميع الحملات افتراضياً، يمكن لاحقاً إضافة خيار في الواجهة
        $data['is_active'] = true;

        $campaign = Campaign::create($data);

        $items = collect($data['items'] ?? []);

        if ($items->isNotEmpty()) {
            $pivotData = [];

            foreach ($items as $item) {
               $scopeType = $item['scope_type'] ?? null;
               $scopeId = $item['scope_id'] ?? null;
                if (!$scopeType || !$scopeId) {
                    continue;
                }

                $discountType = $item['discount_type'] ?? 'none';
                $discountValue = $item['discount_value'] ?? 0;

                $productIds = collect();

                if ($scopeType === 'category') {
                    $productIds = Product::where('category_id', $scopeId)->pluck('id');
                } elseif ($scopeType === 'company') {
                    $productIds = Product::where('company_id', $scopeId)->pluck('id');
                } elseif ($scopeType === 'product') {
                    $productIds = Product::where('id', $scopeId)->pluck('id');
                } elseif ($scopeType === 'type') {
                    $type = Type::with('companies')->find($scopeId);
                    if ($type) {
                        $companyIds = $type->companies->pluck('id');
                        if ($companyIds->isNotEmpty()) {
                            $productIds = Product::where('type_id', $type->id)
                                ->whereIn('company_id', $companyIds)
                                ->pluck('id');
                        } else {
                            $productIds = Product::where('type_id', $type->id)->pluck('id');
                        }
                    }
                }

                foreach ($productIds as $pid) {
                    $pivotData[$pid] = [
                        'discount_type' => $discountType ?? 'none',
                        'discount_value' => $discountValue ?? 0,
                    ];
                }
            }

            if (!empty($pivotData)) {
                // ربط المنتجات بالحملة مع بيانات الخصم في pivot
                $campaign->products()->sync($pivotData);

                // وسم هذه المنتجات كمنتجات "الأكثر مبيعاً" لتظهر في شريط الأعلى مبيعاً
                $productIdsForBestSeller = array_keys($pivotData);
                Product::whereIn('id', $productIdsForBestSeller)->update(['is_best_seller' => true]);
            }
        }

        return back()->with('status', 'تم حفظ الحملة الإعلانية بنجاح.');
    }

    public function show(Campaign $campaign): View
    {
        $campaign->load(['products' => function ($query) {
            $query->withPivot(['discount_type', 'discount_value']);
        }]);

        return view('pages.campaign-details', compact('campaign'));
    }

    public function edit(Campaign $campaign): View
    {
        $products = Product::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $types = Type::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();

        $campaign->load(['products' => function ($query) {
            $query->withPivot(['discount_type', 'discount_value']);
        }]);

        return view('pages.edit-campaign', compact('campaign', 'products', 'categories', 'types', 'companies'));
    }

    public function update(Request $request, Campaign $campaign): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'shipping_type' => ['required', 'in:none,free,conditional'],
            'shipping_min_amount' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'items' => ['nullable', 'array'],
            'items.*.scope_type' => ['required_with:items', 'in:category,type,company,product'],
            'items.*.scope_id' => ['required_with:items', 'integer'],
            'items.*.discount_type' => ['nullable', 'in:none,percent,amount'],
            'items.*.discount_value' => ['nullable', 'numeric', 'min:0'],
        ]);

        // معالجة الصورة
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة
            if ($campaign->image) {
                ImageHelper::delete($campaign->image, 'public');
            }
            
            $data['image'] = ImageHelper::storeWithSequentialName($request->file('image'), 'campaigns', 'public');
            if (!$data['image']) {
                Log::error('فشل رفع صورة الحملة عند التحديث', ['campaign_id' => $campaign->id]);
                return back()->withErrors(['error' => 'فشل رفع صورة الحملة.'])->withInput();
            }
        }

        if ($data['shipping_type'] !== 'conditional') {
            $data['shipping_min_amount'] = null;
        }

        $data['is_active'] = $request->boolean('is_active');

        $campaign->update($data);

        // معالجة المنتجات
        $items = collect($data['items'] ?? []);
        
        if ($items->isNotEmpty()) {
            $pivotData = [];

            foreach ($items as $item) {
                $scopeType = $item['scope_type'] ?? null;
                $scopeId = $item['scope_id'] ?? null;
                if (!$scopeType || !$scopeId) {
                    continue;
                }

                $discountType = $item['discount_type'] ?? 'none';
                $discountValue = $item['discount_value'] ?? 0;

                $productIds = collect();

                if ($scopeType === 'category') {
                    $productIds = Product::where('category_id', $scopeId)->pluck('id');
                } elseif ($scopeType === 'company') {
                    $productIds = Product::where('company_id', $scopeId)->pluck('id');
                } elseif ($scopeType === 'product') {
                    $productIds = Product::where('id', $scopeId)->pluck('id');
                } elseif ($scopeType === 'type') {
                    $type = Type::with('companies')->find($scopeId);
                    if ($type) {
                        $companyIds = $type->companies->pluck('id');
                        if ($companyIds->isNotEmpty()) {
                            $productIds = Product::where('type_id', $type->id)
                                ->whereIn('company_id', $companyIds)
                                ->pluck('id');
                        } else {
                            $productIds = Product::where('type_id', $type->id)->pluck('id');
                        }
                    }
                }

                foreach ($productIds as $pid) {
                    $pivotData[$pid] = [
                        'discount_type' => $discountType ?? 'none',
                        'discount_value' => $discountValue ?? 0,
                    ];
                }
            }

            if (!empty($pivotData)) {
                $campaign->products()->sync($pivotData);
                $productIdsForBestSeller = array_keys($pivotData);
                Product::whereIn('id', $productIdsForBestSeller)->update(['is_best_seller' => true]);
            }
        } else {
            // إزالة جميع المنتجات إذا لم تكن هناك عناصر
            $campaign->products()->sync([]);
        }

        return back()->with('status', 'تم تحديث الحملة بنجاح.');
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        try {
            Log::info('🔴 بدء حذف الحملة', [
                'campaign_id' => $campaign->id,
                'campaign_title' => $campaign->title,
            ]);

            // حفظ مسار الصورة قبل الحذف
            $imagePath = $campaign->image;

            // حذف العلاقات مع المنتجات
            $campaign->products()->detach();

            // حذف الحملة
            $campaign->delete();

            // حذف الصورة بعد نجاح الحذف
            if ($imagePath) {
                try {
                    ImageHelper::delete($imagePath, 'public');
                    Log::info('🖼️ تم حذف صورة الحملة', ['image_path' => $imagePath]);
                } catch (\Exception $e) {
                    Log::warning('⚠️ فشل حذف صورة الحملة (غير حرج)', ['error' => $e->getMessage()]);
                }
            }

            Log::info('✅ تم حذف الحملة بنجاح', ['campaign_id' => $campaign->id]);

            return back()->with('status', '✅ تم حذف الحملة "' . $campaign->title . '" بنجاح!');

        } catch (\Exception $e) {
            Log::error('❌ خطأ في حذف الحملة', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => '❌ فشل حذف الحملة: ' . $e->getMessage()]);
        }
    }
}


