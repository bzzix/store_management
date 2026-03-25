# 🔧 دليل التطوير - نظام إدارة المتجر

## 📋 جدول المحتويات

1. [معايير الكود](#معايير-الكود)
2. [البنية المعمارية](#البنية-المعمارية)
3. [إضافة ميزات جديدة](#إضافة-ميزات-جديدة)
4. [أفضل الممارسات](#أفضل-الممارسات)
5. [الاختبار](#الاختبار)

---

## 📐 معايير الكود

### 1. تسمية الملفات والمجلدات
```
- Controllers: PascalCase (DashboardController.php)
- Models: PascalCase Singular (Product.php)
- Services: PascalCase + Service (PricingService.php)
- Livewire: PascalCase (Dashboard/Pricing/Tiers.php)
- Views: snake_case (dashboard/pricing/tiers.blade.php)
- Migrations: snake_case (create_products_table.php)
```

### 2. تسمية المتغيرات والدوال
```php
// camelCase للمتغيرات والدوال
$productPrice = 100;
public function calculatePrice() {}

// PascalCase للـ Classes
class PricingService {}

// UPPER_CASE للثوابت
const MAX_PRICE = 10000;
```

### 3. قاعدة البيانات
```sql
-- snake_case لجميع الأسماء
table: products
column: current_cost_price

-- جميع الأسماء بالإنجليزية
-- استخدام DECIMAL للقيم المالية
-- استخدام soft deletes للجداول المهمة
```

---

## 🏗️ البنية المعمارية

### 1. Controllers (رقيقة)
```php
class DashboardController extends Controller
{
    // Controllers فقط تستدعي Services
    // لا منطق تجاري في Controllers
    
    public function pricing()
    {
        return view('dashboard.pricing.index');
    }
}
```

### 2. Service Classes (المنطق التجاري)
```php
class PricingService
{
    // جميع المنطق التجاري هنا
    
    public function calculate(float $basePrice, string $saleMethodCode): array
    {
        // Business logic here
        return [
            'base_price' => $basePrice,
            'final_price' => $finalPrice,
            'profit' => $profit,
        ];
    }
}
```

### 3. Models (Eloquent)
```php
class Product extends Model
{
    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    // Accessors
    public function getCurrentPriceAttribute()
    {
        return $this->prices()->where('is_current', true)->first();
    }
    
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

### 4. Livewire Components
```php
class Tiers extends Component
{
    // Properties
    public $name;
    public $minValue;
    
    // Validation
    #[Validate('required|string|max:255')]
    public $name;
    
    // Methods
    public function save()
    {
        $this->validate();
        // Logic here
        $this->dispatch('notify', type: 'success', message: 'Saved!');
    }
    
    public function render()
    {
        return view('livewire.dashboard.pricing.tiers');
    }
}
```

---

## ➕ إضافة ميزات جديدة

### Workflow لإنشاء Module جديد

راجع: [`.agent/workflows/create-module.md`](file:///c:/laragon/www/abdelstar_agri_mng/.agent/workflows/create-module.md)

#### 1. إنشاء Routes
```php
// routes/web.php
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/products', [DashboardController::class, 'products'])
        ->name('dashboard.products');
});
```

#### 2. إنشاء Controller Method
```php
// app/Http/Controllers/DashboardController.php
public function products()
{
    return view('dashboard.products.index');
}
```

#### 3. إنشاء Blade View
```bash
mkdir resources/views/dashboard/products
touch resources/views/dashboard/products/index.blade.php
```

#### 4. إنشاء Livewire Component
```bash
php artisan make:livewire Dashboard/Products/ProductList
```

#### 5. ربط Livewire بـ Blade
```blade
{{-- resources/views/dashboard/products/index.blade.php --}}
<x-app-layout>
    <livewire:dashboard.products.product-list />
</x-app-layout>
```

#### 6. إضافة رابط في Sidebar
```blade
{{-- resources/views/layouts/navigation.blade.php --}}
<x-nav-link :href="route('dashboard.products')" :active="request()->routeIs('dashboard.products')">
    {{ __('Products') }}
</x-nav-link>
```

#### 7. إنشاء Permissions (إذا لزم الأمر)
```php
// database/seeders/RolesSeeder.php
Permission::create(['name' => 'products_view']);
Permission::create(['name' => 'products_add']);
Permission::create(['name' => 'products_edit']);
Permission::create(['name' => 'products_delete']);
```

---

## ✅ أفضل الممارسات

### 1. استخدام Service Classes
```php
// ❌ سيء - منطق في Controller
public function store(Request $request)
{
    $product = new Product();
    $product->name = $request->name;
    $product->price = $request->price;
    // ... lots of logic
    $product->save();
}

// ✅ جيد - استخدام Service
public function store(Request $request)
{
    $this->productService->create($request->validated());
    return redirect()->back()->with('success', 'Product created!');
}
```

### 2. استخدام Form Requests
```php
// app/Http/Requests/StoreProductRequest.php
class StoreProductRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ];
    }
}
```

### 3. استخدام Eloquent Relationships
```php
// ❌ سيء - N+1 Query
foreach ($products as $product) {
    echo $product->category->name;
}

// ✅ جيد - Eager Loading
$products = Product::with('category')->get();
foreach ($products as $product) {
    echo $product->category->name;
}
```

### 4. استخدام Transactions
```php
DB::transaction(function () use ($data) {
    $invoice = PurchaseInvoice::create($data['invoice']);
    
    foreach ($data['items'] as $item) {
        $invoice->items()->create($item);
        $this->updateStock($item);
        $this->createPriceHistory($item);
    }
});
```

### 5. استخدام Events & Listeners
```php
// عند إنشاء فاتورة شراء
event(new PurchaseInvoiceCreated($invoice));

// Listener يحدث الأسعار تلقائياً
class UpdateProductPrices
{
    public function handle(PurchaseInvoiceCreated $event)
    {
        // Update prices logic
    }
}
```

---

## 🧪 الاختبار

### 1. Unit Tests
```php
// tests/Unit/PricingServiceTest.php
public function test_calculates_price_correctly()
{
    $service = new PricingService();
    $result = $service->calculate(100, 'cash');
    
    $this->assertEquals(120, $result['final_price']);
}
```

### 2. Feature Tests
```php
// tests/Feature/ProductTest.php
public function test_can_create_product()
{
    $response = $this->post('/dashboard/products', [
        'name' => 'Test Product',
        'price' => 100,
    ]);
    
    $response->assertStatus(302);
    $this->assertDatabaseHas('products', ['name' => 'Test Product']);
}
```

### 3. تشغيل الاختبارات
```bash
# جميع الاختبارات
php artisan test

# اختبار محدد
php artisan test --filter=ProductTest

# مع Coverage
php artisan test --coverage
```

---

## 🔍 Debugging

### 1. استخدام dd() و dump()
```php
// إيقاف التنفيذ
dd($variable);

// عرض فقط
dump($variable);
```

### 2. Laravel Debugbar
```bash
composer require barryvdh/laravel-debugbar --dev
```

### 3. Log Files
```php
// كتابة في الـ Log
Log::info('Product created', ['product_id' => $product->id]);
Log::error('Failed to create product', ['error' => $e->getMessage()]);

// عرض الـ Logs
tail -f storage/logs/laravel.log
```

---

## 📦 إضافة حزمة جديدة

```bash
# تثبيت حزمة
composer require vendor/package

# تثبيت حزمة للتطوير فقط
composer require vendor/package --dev

# تحديث الحزم
composer update

# إزالة حزمة
composer remove vendor/package
```

---

## 🚀 النشر (Deployment)

### 1. تحسين الأداء
```bash
# Cache Config
php artisan config:cache

# Cache Routes
php artisan route:cache

# Cache Views
php artisan view:cache

# Optimize Autoloader
composer install --optimize-autoloader --no-dev
```

### 2. بناء الأصول
```bash
npm run build
```

### 3. تشغيل Migrations
```bash
php artisan migrate --force
```

---

## 📝 ملاحظات مهمة

### 1. لا تضع منطقاً في Blade/Livewire Views
```blade
{{-- ❌ سيء --}}
@php
    $total = 0;
    foreach ($items as $item) {
        $total += $item->price;
    }
@endphp

{{-- ✅ جيد --}}
{{ $invoice->total }}
```

### 2. استخدم Accessors للحسابات
```php
// في Model
public function getTotalAttribute()
{
    return $this->items->sum('price');
}

// في View
{{ $invoice->total }}
```

### 3. استخدم Scopes للاستعلامات المتكررة
```php
// في Model
public function scopeActive($query)
{
    return $query->where('is_active', true);
}

// الاستخدام
Product::active()->get();
```

---

## 🎯 الخطوات التالية

1. إنشاء Models للجداول الجديدة
2. تعريف العلاقات في Models
3. إنشاء Service Classes
4. إنشاء Livewire Components للـ CRUD
5. إنشاء Seeders للبيانات التجريبية
6. كتابة الاختبارات

---

للمزيد من المعلومات:
- [تصميم قاعدة البيانات](DATABASE_DESIGN.md)
- [نظام التسعير](PRICING_SERVICE.md)
- [Workflow إنشاء Module](../.agent/workflows/create-module.md)
