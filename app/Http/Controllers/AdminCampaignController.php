<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\Type;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCampaignController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || strtolower(auth()->user()->role) !== 'admin') {
                abort(403);
            }
            return $next($request);
        });
    }

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
            $data['image'] = $request->file('image')->store('campaigns', 'public');
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
}


