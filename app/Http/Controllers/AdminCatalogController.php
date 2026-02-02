<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\Role;
use App\Models\Type;
use App\Helpers\ImageHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminCatalogController extends Controller
{
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
            Log::info('بدء رفع صورة الفئة', [
                'category_name' => $data['name'],
                'file_name' => $request->file('image')->getClientOriginalName(),
                'file_size' => $request->file('image')->getSize()
            ]);
            
            $data['image'] = ImageHelper::storeWithSequentialName($request->file('image'), 'categories', 'public');
            
            if (!$data['image']) {
                Log::error('فشل رفع صورة الفئة', [
                    'category_name' => $data['name'],
                    'file_name' => $request->file('image')->getClientOriginalName()
                ]);
                return back()->withErrors(['error' => 'فشل رفع صورة الفئة. يرجى التحقق من صلاحيات المجلدات.'])->withInput();
            }
            
            Log::info('تم رفع صورة الفئة بنجاح', [
                'category_name' => $data['name'],
                'image_path' => $data['image']
            ]);
        }

        $category = Category::create($data);
        
        Log::info('تم إنشاء الفئة بنجاح', [
            'category_id' => $category->id,
            'category_name' => $category->name,
            'image_path' => $category->image
        ]);

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
            $data['image'] = ImageHelper::storeWithSequentialName($request->file('image'), 'types', 'public');
            if (!$data['image']) {
                Log::error('فشل رفع صورة النوع', ['type_name' => $data['name']]);
                return back()->withErrors(['error' => 'فشل رفع صورة النوع. يرجى التحقق من صلاحيات المجلدات.'])->withInput();
            }
        }

        Type::create($data);

        return back()->with('status', 'تم إضافة النوع.');
    }

    public function storeCompany(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:companies,name'],
            'image' => ['nullable', 'image', 'max:2048'],
            'background' => ['nullable', 'image', 'max:2048'],
            'description' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'types' => ['nullable', 'array'],
            'types.*' => ['exists:types,id'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = ImageHelper::storeWithSequentialName($request->file('image'), 'companies', 'public');
            if (!$data['image']) {
                Log::error('فشل رفع صورة الشركة', ['company_name' => $data['name']]);
                return back()->withErrors(['error' => 'فشل رفع صورة الشركة. يرجى التحقق من صلاحيات المجلدات.'])->withInput();
            }
        }

        if ($request->hasFile('background')) {
            $data['background'] = ImageHelper::storeWithSequentialName($request->file('background'), 'companies', 'public');
            if (!$data['background']) {
                Log::error('فشل رفع صورة الخلفية للشركة', ['company_name' => $data['name']]);
                return back()->withErrors(['error' => 'فشل رفع صورة الخلفية. يرجى التحقق من صلاحيات المجلدات.'])->withInput();
            }
        }

        $company = Company::create($data);
        if (!empty($data['types'])) {
            $company->types()->sync($data['types']);
        }

        return back()->with('status', 'تم إضافة الشركة.');
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        try {
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

            // التأكد من وجود مجلد التخزين قبل رفع الصورة
            if ($request->hasFile('image')) {
                $productsPath = 'products';
                $publicDisk = Storage::disk('public');
                
                // التأكد من وجود المجلد
                if (!$publicDisk->exists($productsPath)) {
                    $publicDisk->makeDirectory($productsPath, 0755, true);
                }
                
                $data['image'] = ImageHelper::storeWithSequentialName($request->file('image'), $productsPath, 'public');
                
                // التحقق من نجاح رفع الصورة
                if (!$data['image']) {
                    Log::error('فشل رفع صورة المنتج', ['product_name' => $data['name']]);
                    return back()->withErrors(['error' => 'فشل رفع صورة المنتج. يرجى التحقق من صلاحيات المجلدات.'])->withInput();
                }
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

            $product = Product::create($data);
            
            // التحقق من نجاح إنشاء المنتج
            if (!$product || !$product->id) {
                Log::error('فشل إنشاء المنتج في قاعدة البيانات', ['data' => $data]);
                return back()->withErrors(['error' => 'فشل إضافة المنتج. يرجى المحاولة مرة أخرى.'])->withInput();
            }

            Log::info('تم إضافة منتج بنجاح', ['product_id' => $product->id, 'product_name' => $product->name]);

            return back()->with('status', 'تم إضافة المنتج.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // أخطاء التحقق من البيانات
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            // تسجيل الخطأ بالتفاصيل
            Log::error('خطأ في إضافة المنتج', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['image']),
            ]);
            
            return back()->withErrors([
                'error' => 'حدث خطأ أثناء إضافة المنتج: ' . $e->getMessage() . '. يرجى التحقق من سجلات الأخطاء.'
            ])->withInput();
        }
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
        
        // حذف الصورة القديمة إذا تم رفع صورة جديدة
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إن وجدت
            if ($category->image) {
                ImageHelper::delete($category->image, 'public');
            }
            $data['image'] = ImageHelper::storeWithSequentialName($request->file('image'), 'categories', 'public');
            if (!$data['image']) {
                Log::error('فشل رفع صورة الفئة عند التحديث', ['category_id' => $category->id]);
                return back()->withErrors(['error' => 'فشل رفع صورة الفئة. يرجى التحقق من صلاحيات المجلدات.'])->withInput();
            }
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
        
        // حذف الصورة القديمة إذا تم رفع صورة جديدة
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إن وجدت
            if ($type->image) {
                ImageHelper::delete($type->image, 'public');
            }
            $data['image'] = ImageHelper::storeWithSequentialName($request->file('image'), 'types', 'public');
            if (!$data['image']) {
                Log::error('فشل رفع صورة النوع عند التحديث', ['type_id' => $type->id]);
                return back()->withErrors(['error' => 'فشل رفع صورة النوع. يرجى التحقق من صلاحيات المجلدات.'])->withInput();
            }
        }
        $type->update($data);
        return back()->with('status', 'تم تعديل النوع.');
    }

    public function updateCompany(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('companies', 'name')->ignore($company->id)],
            'image' => ['nullable', 'image', 'max:2048'],
            'background' => ['nullable', 'image', 'max:2048'],
            'description' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'types' => ['nullable', 'array'],
            'types.*' => ['exists:types,id'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
        ]);
        
        // حذف الصورة القديمة إذا تم رفع صورة جديدة
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إن وجدت
            if ($company->image) {
                ImageHelper::delete($company->image, 'public');
            }
            $data['image'] = ImageHelper::storeWithSequentialName($request->file('image'), 'companies', 'public');
            if (!$data['image']) {
                Log::error('فشل رفع صورة الشركة عند التحديث', ['company_id' => $company->id]);
                return back()->withErrors(['error' => 'فشل رفع صورة الشركة. يرجى التحقق من صلاحيات المجلدات.'])->withInput();
            }
        }
        
        // حذف الصورة الخلفية القديمة إذا تم رفع صورة جديدة
        if ($request->hasFile('background')) {
            // حذف الصورة الخلفية القديمة إن وجدت
            if ($company->background) {
                ImageHelper::delete($company->background, 'public');
            }
            $data['background'] = ImageHelper::storeWithSequentialName($request->file('background'), 'companies', 'public');
            if (!$data['background']) {
                Log::error('فشل رفع صورة الخلفية للشركة عند التحديث', ['company_id' => $company->id]);
                return back()->withErrors(['error' => 'فشل رفع صورة الخلفية. يرجى التحقق من صلاحيات المجلدات.'])->withInput();
            }
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
        try {
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
            
            // حذف الصورة القديمة إذا تم رفع صورة جديدة
            if ($request->hasFile('image')) {
                $productsPath = 'products';
                $publicDisk = Storage::disk('public');
                
                // التأكد من وجود المجلد
                if (!$publicDisk->exists($productsPath)) {
                    $publicDisk->makeDirectory($productsPath, 0755, true);
                }
                
                // حذف الصورة القديمة إن وجدت
                if ($product->image) {
                    ImageHelper::delete($product->image, 'public');
                    Log::info('تم حذف صورة المنتج القديمة', ['product_id' => $product->id, 'old_image' => $product->image]);
                }
                
                // حذف الصورة المصغرة القديمة إن وجدت
                if ($product->thumbnail) {
                    ImageHelper::delete($product->thumbnail, 'public');
                }
                
                $data['image'] = ImageHelper::storeWithSequentialName($request->file('image'), $productsPath, 'public');
                
                // التحقق من نجاح رفع الصورة
                if (!$data['image']) {
                    Log::error('فشل رفع صورة المنتج عند التحديث', [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'file_name' => $request->file('image')->getClientOriginalName()
                    ]);
                    return back()->withErrors(['error' => 'فشل رفع صورة المنتج. يرجى التحقق من صلاحيات المجلدات.'])->withInput();
                }
                
                Log::info('تم رفع صورة المنتج بنجاح عند التحديث', [
                    'product_id' => $product->id,
                    'new_image' => $data['image']
                ]);
            }
            
            $data['is_best_seller'] = $request->boolean('is_best_seller');
            if ($request->has('is_active')) {
                $data['is_active'] = $request->boolean('is_active');
            }
            
            $product->update($data);
            
            Log::info('تم تعديل المنتج بنجاح', ['product_id' => $product->id, 'product_name' => $product->name]);
            
            return back()->with('status', 'تم تعديل المنتج.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('خطأ في التحقق من صحة البيانات عند تعديل المنتج', [
                'product_id' => $product->id,
                'errors' => $e->errors()
            ]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('خطأ غير متوقع عند تعديل المنتج', [
                'product_id' => $product->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'حدث خطأ أثناء تعديل المنتج. يرجى المحاولة مرة أخرى.'])->withInput();
        }
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
        // حذف الصورة المرتبطة بالصنف
        if ($category->image) {
            ImageHelper::delete($category->image, 'public');
        }
        
        $category->delete();
        return back()->with('status', 'تم حذف الصنف.');
    }

    public function destroyType(Type $type): RedirectResponse
    {
        // حذف الصورة المرتبطة بالنوع
        if ($type->image) {
            ImageHelper::delete($type->image, 'public');
        }
        
        $type->delete();
        return back()->with('status', 'تم حذف النوع.');
    }

    public function destroyCompany(Company $company): RedirectResponse
    {
        // حذف الصورة المرتبطة بالشركة
        if ($company->image) {
            ImageHelper::delete($company->image, 'public');
        }
        
        // حذف الصورة الخلفية المرتبطة بالشركة
        if ($company->background) {
            ImageHelper::delete($company->background, 'public');
        }
        
        $company->delete();
        return back()->with('status', 'تم حذف الشركة.');
    }

    public function destroyProduct(Product $product): RedirectResponse
    {
        $productId = $product->id;
        $productName = $product->name;
        $imagePath = $product->image;
        $thumbnailPath = $product->thumbnail;
        $categoryId = $product->category_id;
        
        try {
            Log::info('🔴 بدء عملية حذف المنتج', [
                'product_id' => $productId,
                'product_name' => $productName,
            ]);

            // 1. حذف العلاقات المرتبطة أولاً
            Log::info('🔗 حذف العلاقات المرتبطة...');
            
            try {
                // تحديث order_items لتجنب مشاكل foreign key
                $orderItemsUpdated = \DB::table('order_items')
                    ->where('product_id', $productId)
                    ->update(['product_id' => null]);
                
                // حذف favorites
                $favoritesDeleted = \DB::table('user_favorites')
                    ->where('product_id', $productId)
                    ->delete();
                
                // حذف campaigns
                $campaignsDeleted = \DB::table('campaign_product')
                    ->where('product_id', $productId)
                    ->delete();
                
                // تحديث rewards
                $rewardsUpdated = \DB::table('rewards')
                    ->where('product_id', $productId)
                    ->update(['product_id' => null]);
                
                Log::info('✅ تم حذف/تحديث العلاقات', [
                    'order_items_updated' => $orderItemsUpdated,
                    'favorites_deleted' => $favoritesDeleted,
                    'campaigns_deleted' => $campaignsDeleted,
                    'rewards_updated' => $rewardsUpdated,
                ]);
            } catch (\Exception $e) {
                Log::warning('⚠️ مشكلة في حذف العلاقات (سنحاول المتابعة)', [
                    'error' => $e->getMessage()
                ]);
            }

            // 2. حذف المنتج من قاعدة البيانات مباشرة
            Log::info('⚡ حذف المنتج من قاعدة البيانات...');
            
            $rowsDeleted = \DB::table('products')->where('id', $productId)->delete();
            
            Log::info('📊 نتيجة الحذف', [
                'rows_deleted' => $rowsDeleted,
                'product_id' => $productId
            ]);

            // 3. التحقق من الحذف الفعلي
            $existsAfter = \DB::table('products')->where('id', $productId)->exists();
            
            Log::info('🔍 التحقق من الحذف', [
                'exists_after' => $existsAfter,
                'deleted' => !$existsAfter
            ]);

            if ($rowsDeleted === 0 || $existsAfter) {
                Log::error('❌ فشل حذف المنتج', [
                    'rows_deleted' => $rowsDeleted,
                    'still_exists' => $existsAfter
                ]);
                
                return back()->withErrors([
                    'error' => '❌ فشل حذف المنتج من قاعدة البيانات. عدد الصفوف المحذوفة: ' . $rowsDeleted . '. ما زال موجوداً: ' . ($existsAfter ? 'نعم' : 'لا')
                ]);
            }

            Log::info('✅ تم التحقق من حذف المنتج من قاعدة البيانات نهائياً');

            // 4. الآن نحذف الصور بعد نجاح الحذف
            if ($imagePath) {
                try {
                    ImageHelper::delete($imagePath, 'public');
                    Log::info('🖼️ تم حذف صورة المنتج', ['path' => $imagePath]);
                } catch (\Exception $e) {
                    Log::warning('⚠️ فشل حذف الصورة (غير حرج)', ['error' => $e->getMessage()]);
                }
            }
            
            if ($thumbnailPath) {
                try {
                    ImageHelper::delete($thumbnailPath, 'public');
                    Log::info('🖼️ تم حذف الصورة المصغرة', ['path' => $thumbnailPath]);
                } catch (\Exception $e) {
                    Log::warning('⚠️ فشل حذف الصورة المصغرة (غير حرج)', ['error' => $e->getMessage()]);
                }
            }

            // 5. مسح الكاش
            try {
                \Illuminate\Support\Facades\Cache::forget('store.home.ar');
                \Illuminate\Support\Facades\Cache::forget('store.home.en');
                \Illuminate\Support\Facades\Cache::forget('product.related.' . $categoryId);
                SitemapController::clearCache();
                Log::info('🧹 تم مسح الكاش');
            } catch (\Exception $e) {
                Log::warning('⚠️ مشكلة في مسح الكاش (غير حرج)', ['error' => $e->getMessage()]);
            }
            
            Log::info('🎉 نجحت عملية الحذف بالكامل', [
                'product_id' => $productId,
                'product_name' => $productName,
            ]);
            
            return redirect()->route('admin.catalog')
                ->with('status', '✅ تم حذف المنتج "' . $productName . '" (ID: ' . $productId . ') نهائياً من قاعدة البيانات!')
                ->with('deleted_product_id', $productId);

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('❌ خطأ في قاعدة البيانات', [
                'product_id' => $productId,
                'error' => $e->getMessage(),
                'sql_state' => $e->errorInfo[0] ?? null,
            ]);

            return back()->withErrors([
                'error' => '❌ خطأ في قاعدة البيانات: ' . $e->getMessage()
            ]);

        } catch (\Exception $e) {
            Log::error('❌ خطأ غير متوقع', [
                'product_id' => $productId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()->withErrors([
                'error' => '❌ خطأ: ' . $e->getMessage() . ' في ' . basename($e->getFile()) . ':' . $e->getLine()
            ]);
        }
    }
}

