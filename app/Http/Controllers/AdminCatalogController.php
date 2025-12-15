<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
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
        $this->middleware('auth');
    }

    public function index(): View
    {
        $categories = Category::with('types')->orderBy('name')->get();
        $types = Type::with('category')->orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        $products = Product::with(['category', 'type', 'company'])->latest()->take(20)->get();

        return view('pages.catalog-builder', compact('categories', 'types', 'companies', 'products'));
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
            'description' => ['nullable', 'string'],
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
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('companies', 'public');
        }

        Company::create($data);

        return back()->with('status', 'تم إضافة الشركة.');
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'type_id' => ['required', 'exists:types,id'],
            'company_id' => ['required', 'exists:companies,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return back()->with('status', 'تم إضافة المنتج.');
    }

    public function updateCategory(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->id)],
            'description' => ['nullable', 'string'],
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
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('companies', 'public');
        }
        $company->update($data);
        return back()->with('status', 'تم تعديل الشركة.');
    }

    public function updateProduct(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);
        $data['slug'] = Str::slug($data['name']);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }
        $product->update($data);
        return back()->with('status', 'تم تعديل المنتج.');
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

