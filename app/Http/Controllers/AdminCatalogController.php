<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\Role;
use App\Models\Type;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminCatalogController extends Controller
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
        $categories = Category::with(['types', 'companies'])->orderBy('name')->get();
        $types = Type::with(['category', 'companies'])->orderBy('name')->get();
        $companies = Company::with(['types', 'categories'])->orderBy('name')->get();
        $products = Product::with(['category', 'type', 'company'])->latest()->take(20)->get();

        $roles = Role::orderBy('id')->get();

        return view('pages.catalog-builder', compact('categories', 'types', 'companies', 'products', 'roles'));
    }

    /**
     * صفحات عرض كاملة للأصناف / الأنواع / الشركات
     */
    public function categoriesPage(Request $request): View
    {
        $search = $request->query('q');
        $query = Category::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $categories = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('pages.catalog-categories', compact('categories', 'search'));
    }

    public function typesPage(Request $request): View
    {
        $search = $request->query('q');
        $categoryId = $request->query('category_id');
        $query = Type::with('category');

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $types = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('pages.catalog-types', [
            'types' => $types,
            'search' => $search,
            'categoryId' => $categoryId,
        ]);
    }

    public function companiesPage(Request $request): View
    {
        $search = $request->query('q');
        $query = Company::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $companies = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('pages.catalog-companies', compact('companies', 'search'));
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($data);

        return back()->with('status', 'تم إضافة الصنف الرئيسي.');
    }

    public function storeType(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('types', 'public');
        }

        Type::create($data);

        return back()->with('status', 'تم إضافة النوع.');
    }

    public function storeCompany(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:companies,name'],
            'image' => ['nullable', 'image', 'max:2048'],
            'types' => ['nullable', 'array'],
            'types.*' => ['exists:types,id'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('companies', 'public');
        }

        $company = Company::create($data);
        if (!empty($data['types'])) {
            $company->types()->sync($data['types']);
        }

        return back()->with('status', 'تم إضافة الشركة.');
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'type_id' => ['required', 'exists:types,id'],
            'company_id' => ['required', 'exists:companies,id'],
            'name' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'points_reward' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $rolePrices = $request->input('role_prices', []);
        $data['role_prices'] = collect($rolePrices)
            ->filter(function ($value) {
                return $value !== null && $value !== '';
            })
            ->map(fn ($value) => (float) $value)
            ->toArray();

        // المنتجات الجديدة تكون مفعّلة بشكل افتراضي
        $data['is_active'] = true;

        Product::create($data);

        return back()->with('status', 'تم إضافة المنتج.');
    }

    public function updateCategory(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->id)],
            'name_en' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $data['slug'] = Str::slug($data['name']);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);
        return back()->with('status', 'تم تعديل الصنف.');
    }

    public function updateType(Request $request, Type $type): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);
        $data['slug'] = Str::slug($data['name']);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('types', 'public');
        }
        $type->update($data);
        return back()->with('status', 'تم تعديل النوع.');
    }

    public function updateCompany(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('companies', 'name')->ignore($company->id)],
            'image' => ['nullable', 'image', 'max:2048'],
            'types' => ['nullable', 'array'],
            'types.*' => ['exists:types,id'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('companies', 'public');
        }
        $company->update($data);
        if ($request->has('types')) {
            $company->types()->sync($data['types'] ?? []);
        }
        if ($request->has('categories')) {
            $company->categories()->sync($data['categories'] ?? []);
        }
        return back()->with('status', 'تم تعديل الشركة.');
    }

    public function syncCategoryCompanies(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'companies' => ['nullable', 'array'],
            'companies.*' => ['exists:companies,id'],
        ]);

        $category = Category::findOrFail($data['category_id']);
        $category->companies()->sync($data['companies'] ?? []);

        return back()->withInput()->with('status', 'تم تحديث شركات الصنف.');
    }

    public function syncCompanyCategories(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
        ]);

        $company = Company::findOrFail($data['company_id']);
        $company->categories()->sync($data['categories'] ?? []);

        return back()->withInput()->with('status', 'تم تحديث أصناف الشركة.');
    }

    public function updateProduct(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_best_seller' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['slug'] = Str::slug($data['name']);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }
        $data['is_best_seller'] = $request->boolean('is_best_seller');
        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }
        $product->update($data);
        return back()->with('status', 'تم تعديل المنتج.');
    }

    /**
     * تحديث سريع لحقول محددة في المنتج (السعر، التكلفة، المخزون، الحالة).
     */
    public function quickUpdate(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        $product->update($data);

        return back()->with('status', 'تم حفظ التعديلات السريعة على المنتج.');
    }

    public function destroyCategory(Category $category): RedirectResponse
    {
        $category->delete();
        return back()->with('status', 'تم حذف الصنف.');
    }

    public function destroyType(Type $type): RedirectResponse
    {
        $type->delete();
        return back()->with('status', 'تم حذف النوع.');
    }

    public function destroyCompany(Company $company): RedirectResponse
    {
        $company->delete();
        return back()->with('status', 'تم حذف الشركة.');
    }

    public function destroyProduct(Product $product): RedirectResponse
    {
        $product->delete();
        return back()->with('status', 'تم حذف المنتج.');
    }
}

