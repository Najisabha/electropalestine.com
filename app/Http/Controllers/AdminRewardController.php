<?php

namespace App\Http\Controllers;

use App\Models\Reward;
use App\Models\Category;
use App\Models\Type;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminRewardController extends Controller
{
    public function index(): View
    {
        $rewards = Reward::with('product')->orderByDesc('created_at')->get();
        $categories = Category::orderBy('name')->get();
        $types = Type::orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();

        $typesForJs = $types->map(function ($t) {
            return [
                'id' => $t->id,
                'category_id' => $t->category_id,
                'name' => $t->translated_name,
            ];
        });

        $productsForJs = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'category_id' => $p->category_id,
                'type_id' => $p->type_id,
                'name' => $p->translated_name,
            ];
        });

        return view('pages.rewards', [
            'rewards' => $rewards,
            'categories' => $categories,
            'types' => $types,
            'products' => $products,
            'typesForJs' => $typesForJs,
            'productsForJs' => $productsForJs,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $data = $this->validateData($request);

            if ($data['type'] === 'gift' && $request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('rewards', 'public');
            }

            Reward::create($data);

            return redirect()->route('admin.rewards')->with('status', 'تم إنشاء المكافأة بنجاح.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Reward store error: ' . $e->getMessage());
            \Log::error('Reward store data: ' . json_encode($request->all()));
            return back()->withErrors(['error' => 'حدث خطأ أثناء حفظ المكافأة: ' . $e->getMessage()])->withInput();
        }
    }

    public function update(Request $request, Reward $reward): RedirectResponse
    {
        $data = $this->validateData($request, $reward->id);

        if ($data['type'] === 'gift' && $request->hasFile('image')) {
            if ($reward->image) {
                Storage::disk('public')->delete($reward->image);
            }
            $data['image'] = $request->file('image')->store('rewards', 'public');
        }

        $reward->update($data);

        return redirect()->route('admin.rewards')->with('status', 'تم تحديث المكافأة بنجاح.');
    }

    public function destroy(Reward $reward): RedirectResponse
    {
        if ($reward->image) {
            Storage::disk('public')->delete($reward->image);
        }

        $reward->delete();

        return redirect()->route('admin.rewards')->with('status', 'تم حذف المكافأة بنجاح.');
    }

    public function searchProducts(Request $request)
    {
        try {
            $term = trim($request->get('q', ''));

            if ($term === '' || strlen($term) === 0) {
                return response()->json([]);
            }

            $products = Product::active()
                ->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term . '%')
                      ->orWhere('name_en', 'like', $term . '%');
                })
                ->orderBy('name')
                ->limit(10)
                ->get();

            $results = $products->map(function (Product $product) {
                $image = $product->thumbnail ?: $product->image;

                return [
                    'id' => $product->id,
                    'name' => $product->translated_name,
                    'price' => (float) ($product->price ?? 0),
                    'cost_price' => (float) ($product->cost_price ?? 0),
                    'stock' => (int) ($product->stock ?? 0),
                    'image_url' => $image ? asset('storage/' . $image) : null,
                    'category_id' => $product->category_id,
                    'type_id' => $product->type_id,
                ];
            });

            return response()->json($results);
        } catch (\Exception $e) {
            \Log::error('Product search error: ' . $e->getMessage());
            \Log::error('Product search trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'حدث خطأ في البحث: ' . $e->getMessage()], 500);
        }
    }

    protected function validateData(Request $request, ?int $rewardId = null): array
    {
        $type = $request->input('type');
        
        $rules = [
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string', 'max:1000'],
            'description_en' => ['nullable', 'string', 'max:1000'],
            'points_required' => ['required', 'integer', 'min:1'],
            'type' => ['required', 'in:gift,wallet_credit,coupon'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ];

        // إضافة القواعد المشروطة حسب النوع
        if ($type === 'gift') {
            $rules['product_id'] = ['required', 'integer', 'exists:products,id'];
            $rules['stock'] = ['nullable', 'integer', 'min:0'];
        } elseif ($type === 'wallet_credit') {
            $rules['value'] = ['required', 'numeric', 'min:0.01'];
        } elseif ($type === 'coupon') {
            $rules['value'] = ['required', 'numeric', 'min:0.01'];
            $rules['coupon_code'] = ['required', 'string', 'max:255'];
        }

        $base = $request->validate($rules);

        $title = [
            'ar' => $base['title_ar'],
            'en' => $base['title_en'],
        ];

        $description = [];
        if (!empty($base['description_ar'] ?? null)) {
            $description['ar'] = $base['description_ar'];
        }
        if (!empty($base['description_en'] ?? null)) {
            $description['en'] = $base['description_en'];
        }

        $productId = $base['type'] === 'gift' ? ($base['product_id'] ?? null) : null;

        return [
            'title' => $title,
            'description' => $description,
            'points_required' => $base['points_required'],
            'type' => $base['type'],
            'product_id' => $productId,
            'value' => $base['type'] === 'gift' ? null : ($base['value'] ?? null),
            'stock' => $base['type'] === 'gift' ? ($base['stock'] ?? null) : null,
            'coupon_code' => $base['type'] === 'coupon' ? ($base['coupon_code'] ?? null) : null,
            'is_active' => (bool) ($base['is_active'] ?? true),
        ];
    }
}

