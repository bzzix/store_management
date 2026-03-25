# 🗂️ دليل البنية المعمارية - نظام إدارة المتجر

## 📋 جدول المحتويات

1. [نظرة عامة](#نظرة-عامة)
2. [هيكل المجلدات](#هيكل-المجلدات)
3. [طبقات التطبيق](#طبقات-التطبيق)
4. [تدفق البيانات](#تدفق-البيانات)
5. [الأنماط المستخدمة](#الأنماط-المستخدمة)

---

## 🎯 نظرة عامة

النظام مبني على **Laravel MVC** مع إضافة طبقة **Services** للمنطق التجاري.

### المبادئ الأساسية:
- **Separation of Concerns** - فصل المسؤوليات
- **Single Responsibility** - مسؤولية واحدة لكل Class
- **DRY (Don't Repeat Yourself)** - عدم تكرار الكود
- **SOLID Principles** - مبادئ البرمجة الكائنية

---

## 📁 هيكل المجلدات

```
abdelstar_agri_mng/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── DashboardController.php      # Controllers رقيقة
│   │   ├── Livewire/
│   │   │   └── Dashboard/
│   │   │       ├── Pricing/
│   │   │       │   └── Tiers.php            # Livewire Components
│   │   │       └── Products/
│   │   ├── Middleware/
│   │   └── Requests/                        # Form Requests
│   ├── Models/
│   │   ├── Product.php                      # Eloquent Models
│   │   ├── Category.php
│   │   └── ProfitMarginTier.php
│   ├── Services/
│   │   ├── PricingService.php               # Business Logic
│   │   ├── ProductService.php
│   │   └── InvoiceService.php
│   ├── Policies/                            # Authorization
│   ├── Events/                              # Domain Events
│   └── Listeners/                           # Event Handlers
├── database/
│   ├── migrations/                          # Database Schema
│   ├── seeders/                             # Data Seeders
│   └── factories/                           # Model Factories
├── resources/
│   ├── views/
│   │   ├── dashboard/
│   │   │   ├── pricing/
│   │   │   │   └── index.blade.php
│   │   │   └── products/
│   │   └── livewire/
│   │       └── dashboard/
│   │           └── pricing/
│   │               └── tiers.blade.php
│   └── js/
│       └── app.js
├── routes/
│   ├── web.php                              # Web Routes
│   └── api.php                              # API Routes
├── docs/                                    # Documentation
│   ├── README.md
│   ├── DATABASE_DESIGN.md
│   ├── PRICING_SERVICE.md
│   └── DEVELOPMENT_GUIDE.md
└── .agent/
    └── workflows/                           # Development Workflows
        └── create-module.md
```

---

## 🏗️ طبقات التطبيق

### 1. Presentation Layer (العرض)

#### Routes
```php
// routes/web.php
Route::get('/dashboard/pricing', [DashboardController::class, 'pricing'])
    ->name('dashboard.pricing');
```

#### Controllers
```php
// app/Http/Controllers/DashboardController.php
class DashboardController extends Controller
{
    // رقيقة - فقط تستدعي Views أو Services
    public function pricing()
    {
        return view('dashboard.pricing.index');
    }
}
```

#### Views (Blade + Livewire)
```blade
{{-- resources/views/dashboard/pricing/index.blade.php --}}
<x-app-layout>
    <livewire:dashboard.pricing.tiers />
</x-app-layout>
```

---

### 2. Application Layer (التطبيق)

#### Livewire Components
```php
// app/Livewire/Dashboard/Pricing/Tiers.php
class Tiers extends Component
{
    protected $tierService;
    
    public function boot(ProfitMarginTierService $tierService)
    {
        $this->tierService = $tierService;
    }
    
    public function save()
    {
        $this->validate();
        $this->tierService->create($this->formData);
        $this->dispatch('notify', type: 'success');
    }
}
```

#### Form Requests
```php
// app/Http/Requests/StoreProductRequest.php
class StoreProductRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'required|unique:products',
            'price' => 'required|numeric|min:0',
        ];
    }
}
```

---

### 3. Domain Layer (المنطق التجاري)

#### Services
```php
// app/Services/PricingService.php
class PricingService
{
    public function calculate(float $basePrice, string $saleMethodCode): array
    {
        $tier = $this->findTierForPrice($basePrice);
        $method = $this->getSaleMethod($saleMethodCode);
        
        $profit = $this->calculateProfit($tier, $method);
        
        return [
            'base_price' => $basePrice,
            'final_price' => $basePrice + $profit,
            'profit' => $profit,
        ];
    }
    
    protected function findTierForPrice(float $price)
    {
        return ProfitMarginTier::where('is_active', true)
            ->where('min_value', '<=', $price)
            ->where(function ($q) use ($price) {
                $q->whereNull('max_value')
                  ->orWhere('max_value', '>=', $price);
            })
            ->orderByDesc('priority')
            ->first();
    }
}
```

#### Events
```php
// app/Events/PurchaseInvoiceCreated.php
class PurchaseInvoiceCreated
{
    public function __construct(
        public PurchaseInvoice $invoice
    ) {}
}
```

#### Listeners
```php
// app/Listeners/UpdateProductPrices.php
class UpdateProductPrices
{
    public function handle(PurchaseInvoiceCreated $event)
    {
        foreach ($event->invoice->items as $item) {
            $this->priceService->createPriceHistory($item);
            $this->productService->updateCurrentPrice($item->product);
        }
    }
}
```

---

### 4. Data Layer (البيانات)

#### Models
```php
// app/Models/Product.php
class Product extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'name', 'sku', 'category_id', 'current_cost_price'
    ];
    
    protected $casts = [
        'current_cost_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];
    
    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function prices()
    {
        return $this->hasMany(ProductPrice::class);
    }
    
    public function units()
    {
        return $this->hasMany(ProductUnit::class);
    }
    
    // Accessors
    public function getCurrentPriceAttribute()
    {
        return $this->prices()
            ->where('is_current', true)
            ->first();
    }
    
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

#### Repositories (اختياري)
```php
// app/Repositories/ProductRepository.php
class ProductRepository
{
    public function findWithRelations(int $id, array $relations = [])
    {
        return Product::with($relations)->findOrFail($id);
    }
    
    public function getActiveProducts()
    {
        return Product::active()
            ->with(['category', 'currentPrice'])
            ->get();
    }
}
```

---

## 🔄 تدفق البيانات

### مثال: إنشاء فاتورة شراء

```
1. User Request
   ↓
2. Route → Controller
   ↓
3. Controller → Service
   ↓
4. Service → Model (Create Invoice)
   ↓
5. Service → Event (PurchaseInvoiceCreated)
   ↓
6. Listener → Update Prices
   ↓
7. Listener → Update Stock
   ↓
8. Service → Return Result
   ↓
9. Controller → Redirect with Success
```

### الكود:

```php
// 1. Route
Route::post('/invoices', [InvoiceController::class, 'store']);

// 2. Controller
public function store(StorePurchaseInvoiceRequest $request)
{
    $invoice = $this->invoiceService->create($request->validated());
    return redirect()->back()->with('success', 'Invoice created!');
}

// 3. Service
public function create(array $data): PurchaseInvoice
{
    return DB::transaction(function () use ($data) {
        $invoice = PurchaseInvoice::create($data['invoice']);
        
        foreach ($data['items'] as $itemData) {
            $item = $invoice->items()->create($itemData);
        }
        
        event(new PurchaseInvoiceCreated($invoice));
        
        return $invoice;
    });
}

// 4. Listener
public function handle(PurchaseInvoiceCreated $event)
{
    foreach ($event->invoice->items as $item) {
        // Update prices
        ProductPrice::create([
            'product_id' => $item->product_id,
            'purchase_invoice_id' => $event->invoice->id,
            'cost_price' => $item->unit_price,
            'base_price' => $item->unit_price,
            'effective_from' => now(),
            'is_current' => true,
        ]);
        
        // Update product
        $item->product->update([
            'current_cost_price' => $item->unit_price,
            'current_base_price' => $item->unit_price,
        ]);
    }
}
```

---

## 🎨 الأنماط المستخدمة

### 1. Service Pattern
```php
// فصل المنطق التجاري عن Controllers
class ProductService
{
    public function create(array $data): Product
    {
        // Business logic here
    }
}
```

### 2. Repository Pattern (اختياري)
```php
// فصل الاستعلامات عن Models
class ProductRepository
{
    public function findActive(): Collection
    {
        return Product::active()->get();
    }
}
```

### 3. Observer Pattern
```php
// app/Observers/ProductObserver.php
class ProductObserver
{
    public function creating(Product $product)
    {
        $product->slug = Str::slug($product->name);
    }
}
```

### 4. Strategy Pattern
```php
// مثال: طرق الدفع المختلفة
interface PaymentStrategy
{
    public function process(float $amount): bool;
}

class CashPayment implements PaymentStrategy
{
    public function process(float $amount): bool
    {
        // Cash payment logic
    }
}

class BankTransferPayment implements PaymentStrategy
{
    public function process(float $amount): bool
    {
        // Bank transfer logic
    }
}
```

### 5. Factory Pattern
```php
// database/factories/ProductFactory.php
class ProductFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'sku' => $this->faker->unique()->ean13(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
        ];
    }
}
```

---

## 🔐 الأمان والصلاحيات

### Policies
```php
// app/Policies/ProductPolicy.php
class ProductPolicy
{
    public function create(User $user)
    {
        return $user->hasPermissionTo('products_add');
    }
    
    public function update(User $user, Product $product)
    {
        return $user->hasPermissionTo('products_edit');
    }
}
```

### Middleware
```php
// في Routes
Route::middleware(['auth', 'permission:products_view'])
    ->get('/products', [ProductController::class, 'index']);
```

---

## 📊 الأداء

### 1. Eager Loading
```php
// ❌ N+1 Problem
$products = Product::all();
foreach ($products as $product) {
    echo $product->category->name;
}

// ✅ Eager Loading
$products = Product::with('category')->get();
```

### 2. Caching
```php
$products = Cache::remember('active_products', 3600, function () {
    return Product::active()->get();
});
```

### 3. Query Optimization
```php
// استخدام select() لتحديد الأعمدة المطلوبة فقط
Product::select('id', 'name', 'price')->get();

// استخدام chunk() للبيانات الكبيرة
Product::chunk(100, function ($products) {
    foreach ($products as $product) {
        // Process
    }
});
```

---

## 📝 الخلاصة

البنية المعمارية للنظام:
- ✅ **واضحة ومنظمة**
- ✅ **قابلة للتوسع**
- ✅ **سهلة الصيانة**
- ✅ **تتبع أفضل الممارسات**

للمزيد:
- [دليل التطوير](DEVELOPMENT_GUIDE.md)
- [تصميم قاعدة البيانات](DATABASE_DESIGN.md)
