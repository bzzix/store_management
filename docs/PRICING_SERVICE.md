# 💰 نظام التسعير المرن - PricingService

## 📋 جدول المحتويات

1. [نظرة عامة](#نظرة-عامة)
2. [المفاهيم الأساسية](#المفاهيم-الأساسية)
3. [كيفية العمل](#كيفية-العمل)
4. [أمثلة الاستخدام](#أمثلة-الاستخدام)
5. [الإعدادات](#الإعدادات)

---

## 🎯 نظرة عامة

نظام التسعير المرن يسمح بحساب أسعار البيع **تلقائياً** بناءً على:
1. **السعر الأساسي** (Base Price)
2. **الشريحة السعرية** (Profit Margin Tier)
3. **طريقة البيع** (Sale Method: نقدي، تقسيط، آجل)

### المميزات:
- ✅ حساب تلقائي للأسعار
- ✅ شرائح سعرية متعددة
- ✅ طرق بيع مختلفة
- ✅ مرونة كاملة في التعديل
- ✅ تاريخ كامل للأسعار

---

## 📚 المفاهيم الأساسية

### 1. السعر الأساسي (Base Price)
```
السعر الأساسي = سعر التكلفة (من آخر فاتورة شراء)
```
- يُخزن في `products.current_base_price`
- يُحدّث تلقائياً عند استلام فاتورة شراء
- يمكن أن يكون `NULL` قبل أول فاتورة

### 2. الشرائح السعرية (Profit Margin Tiers)
```sql
-- جدول: profit_margin_tiers
id | name        | min_value | max_value | priority
---|-------------|-----------|-----------|----------
1  | شريحة 1     | 0         | 100       | 1
2  | شريحة 2     | 100       | 500       | 2
3  | شريحة 3     | 500       | NULL      | 3
```

كل شريحة تحدد:
- **نطاق الأسعار** (من - إلى)
- **الأولوية** (عند التداخل)
- **نسب الربح** لكل طريقة بيع

### 3. طرق البيع (Sale Methods)
```sql
-- جدول: sale_methods
id | name      | code        | is_active
---|-----------|-------------|----------
1  | نقدي      | cash        | true
2  | تقسيط     | installment | true
3  | آجل       | credit      | true
```

### 4. نسب الربح (Profit Margins)
```sql
-- جدول: profit_margin_tier_methods
tier_id | sale_method_id | margin_type | margin_value
--------|----------------|-------------|-------------
1       | 1 (نقدي)      | percentage  | 20.00
1       | 2 (تقسيط)     | percentage  | 30.00
1       | 3 (آجل)       | percentage  | 25.00
```

---

## ⚙️ كيفية العمل

### تدفق حساب السعر:

```
1. المنتج له base_price = 100 ريال
   ↓
2. تحديد الشريحة السعرية المناسبة
   → الشريحة 1 (0-100)
   ↓
3. تحديد طريقة البيع
   → نقدي (cash)
   ↓
4. جلب نسبة الربح
   → 20%
   ↓
5. حساب السعر النهائي
   → 100 + (100 × 20%) = 120 ريال
```

### الكود:

```php
// app/Services/PricingService.php
class PricingService
{
    public function calculate(float $basePrice, string $saleMethodCode): array
    {
        // 1. Find the appropriate tier
        $tier = $this->findTierForPrice($basePrice);
        
        if (!$tier) {
            throw new Exception('No pricing tier found for this price');
        }
        
        // 2. Get the sale method
        $method = SaleMethod::where('code', $saleMethodCode)
            ->where('is_active', true)
            ->firstOrFail();
        
        // 3. Get the profit margin
        $tierMethod = ProfitMarginTierMethod::where('tier_id', $tier->id)
            ->where('sale_method_id', $method->id)
            ->firstOrFail();
        
        // 4. Calculate profit
        $profit = $this->calculateProfit($basePrice, $tierMethod);
        
        // 5. Calculate final price
        $finalPrice = $basePrice + $profit;
        
        return [
            'base_price' => $basePrice,
            'tier' => $tier->name,
            'sale_method' => $method->name,
            'margin_type' => $tierMethod->margin_type,
            'margin_value' => $tierMethod->margin_value,
            'profit' => $profit,
            'final_price' => $finalPrice,
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
    
    protected function calculateProfit(float $basePrice, $tierMethod): float
    {
        if ($tierMethod->margin_type === 'percentage') {
            return $basePrice * ($tierMethod->margin_value / 100);
        } else {
            return $tierMethod->margin_value;
        }
    }
}
```

---

## 💡 أمثلة الاستخدام

### مثال 1: حساب سعر منتج

```php
use App\Services\PricingService;

$pricingService = new PricingService();

// منتج سعره الأساسي 100 ريال
$result = $pricingService->calculate(
    basePrice: 100,
    saleMethodCode: 'cash'
);

/*
النتيجة:
[
    'base_price' => 100,
    'tier' => 'شريحة 1',
    'sale_method' => 'نقدي',
    'margin_type' => 'percentage',
    'margin_value' => 20.00,
    'profit' => 20.00,
    'final_price' => 120.00
]
*/
```

### مثال 2: مقارنة الأسعار بطرق بيع مختلفة

```php
$basePrice = 100;

$cashPrice = $pricingService->calculate($basePrice, 'cash');
// final_price = 120 (ربح 20%)

$installmentPrice = $pricingService->calculate($basePrice, 'installment');
// final_price = 130 (ربح 30%)

$creditPrice = $pricingService->calculate($basePrice, 'credit');
// final_price = 125 (ربح 25%)
```

### مثال 3: استخدام في Livewire

```php
// app/Livewire/Dashboard/Sales/CreateInvoice.php
class CreateInvoice extends Component
{
    public $productId;
    public $saleMethod = 'cash';
    public $calculatedPrice;
    
    protected $pricingService;
    
    public function boot(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }
    
    public function updatedProductId()
    {
        $product = Product::find($this->productId);
        
        if ($product && $product->current_base_price) {
            $result = $this->pricingService->calculate(
                $product->current_base_price,
                $this->saleMethod
            );
            
            $this->calculatedPrice = $result['final_price'];
        }
    }
    
    public function updatedSaleMethod()
    {
        $this->updatedProductId(); // Recalculate
    }
}
```

---

## 🔧 الإعدادات

### 1. إنشاء شريحة سعرية جديدة

```php
ProfitMarginTier::create([
    'name' => 'شريحة المنتجات الفاخرة',
    'min_value' => 1000,
    'max_value' => null, // لا حد أقصى
    'priority' => 10,
    'is_active' => true,
]);
```

### 2. تعيين نسب الربح للشريحة

```php
$tier = ProfitMarginTier::find(1);

// نقدي: 15%
ProfitMarginTierMethod::create([
    'tier_id' => $tier->id,
    'sale_method_id' => 1, // cash
    'margin_type' => 'percentage',
    'margin_value' => 15.00,
]);

// تقسيط: 25%
ProfitMarginTierMethod::create([
    'tier_id' => $tier->id,
    'sale_method_id' => 2, // installment
    'margin_type' => 'percentage',
    'margin_value' => 25.00,
]);
```

### 3. استخدام قيمة ثابتة بدلاً من نسبة

```php
// ربح ثابت 50 ريال
ProfitMarginTierMethod::create([
    'tier_id' => $tier->id,
    'sale_method_id' => 1,
    'margin_type' => 'fixed',
    'margin_value' => 50.00,
]);
```

---

## 📊 أمثلة واقعية

### السيناريو 1: متجر إلكترونيات

```php
// الشرائح:
// 0-500: ربح 20% نقدي، 30% تقسيط
// 500-2000: ربح 15% نقدي، 25% تقسيط
// 2000+: ربح 10% نقدي، 20% تقسيط

// منتج 1: موبايل سعره 300 ريال
$result = $pricingService->calculate(300, 'cash');
// النتيجة: 360 ريال (ربح 60)

// منتج 2: لابتوب سعره 3000 ريال
$result = $pricingService->calculate(3000, 'cash');
// النتيجة: 3300 ريال (ربح 300)
```

### السيناريو 2: متجر مواد غذائية

```php
// الشرائح:
// 0-50: ربح ثابت 10 ريال
// 50-200: ربح 25%
// 200+: ربح 20%

// منتج 1: أرز سعره 30 ريال
$result = $pricingService->calculate(30, 'cash');
// النتيجة: 40 ريال (ربح ثابت 10)

// منتج 2: زيت سعره 100 ريال
$result = $pricingService->calculate(100, 'cash');
// النتيجة: 125 ريال (ربح 25%)
```

---

## ⚠️ ملاحظات مهمة

### 1. الأولوية عند التداخل
```php
// إذا كان هناك شريحتان تغطيان نفس النطاق:
// الشريحة 1: 0-100 (أولوية 1)
// الشريحة 2: 50-150 (أولوية 2)

// منتج سعره 75:
// سيتم اختيار الشريحة 2 (أولوية أعلى)
```

### 2. التعامل مع NULL
```php
// إذا كان current_base_price = NULL
if (!$product->current_base_price) {
    throw new Exception('Product has no base price yet');
}
```

### 3. التحديث التلقائي للأسعار
```php
// عند استلام فاتورة شراء:
// 1. يتم تحديث current_base_price
// 2. يتم إنشاء سجل في product_prices
// 3. السعر النهائي يُحسب لحظياً عند البيع
```

---

## 🎯 الخلاصة

نظام التسعير المرن يوفر:
- ✅ **مرونة كاملة** في تحديد الأسعار
- ✅ **حساب تلقائي** بدون تدخل يدوي
- ✅ **دعم طرق بيع متعددة**
- ✅ **شرائح سعرية قابلة للتخصيص**
- ✅ **سهولة في الإدارة والتعديل**

للمزيد:
- [تصميم قاعدة البيانات](DATABASE_DESIGN.md)
- [دليل التطوير](DEVELOPMENT_GUIDE.md)
