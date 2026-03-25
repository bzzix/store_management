# 📊 خدمة حساب الأسعار (Pricing Service)

## نظرة عامة

خدمة `PricingService` مسؤولة عن حساب الأسعار النهائية بناءً على:
- **السعر الأساسي** للمنتج
- **طريقة البيع** (نقدي، قسط، آجل)
- **الشريحة السعرية** المناسبة

الخدمة تعتمد على ثلاث نماذج Eloquent:
- `ProfitMarginTier` - الشرائح السعرية
- `SaleMethod` - طرق البيع
- `ProfitMarginTierMethod` - جدول الربط بين الشرائح وطرق البيع

---

## 🏗️ البنية المعمارية

### الجداول المرتبطة

```
profit_margin_tiers (الشرائح السعرية)
├── id
├── name (اسم الشريحة)
├── min_value (الحد الأدنى)
├── max_value (الحد الأقصى - nullable)
├── priority (الأولوية)
└── is_active (التفعيل)

sale_methods (طرق البيع)
├── id
├── name (اسم الطريقة)
├── code (الكود - فريد)
├── priority (الأولوية)
└── is_active (التفعيل)

profit_margin_tier_methods (جدول العلاقة)
├── id
├── profit_margin_tier_id (مفتاح أجنبي)
├── sale_method_id (مفتاح أجنبي)
├── profit_value (قيمة الربح)
└── timestamps
```

---

## 🚀 طرق الاستخدام

### 1️⃣ الاستخدام الأساسي

```php
use App\Services\PricingService;

$pricingService = new PricingService();

// حساب السعر النهائي
$result = $pricingService->calculate(1000, 'installment');

// الإرجاع:
// Array (
//     [base_price] => 1000
//     [final_price] => 1030
//     [profit] => 30
//     [tier] => ProfitMarginTier object
//     [sale_method] => installment
//     [fallback] => null
// )
```

### 2️⃣ الاستخدام مع Dependency Injection

```php
use App\Services\PricingService;

class ProductController extends Controller
{
    public function store(Request $request, PricingService $pricingService)
    {
        $result = $pricingService->calculate(
            $request->base_price,
            $request->sale_method_code
        );

        if ($result['fallback']) {
            return response()->json(
                ['error' => $result['fallback']], 
                400
            );
        }

        // حفظ السعر النهائي
        Product::create([
            'name' => $request->name,
            'base_price' => $result['base_price'],
            'final_price' => $result['final_price'],
            'profit' => $result['profit'],
        ]);

        return response()->json(['success' => true]);
    }
}
```

### 3️⃣ الاستخدام الآمن مع معالجة الأخطاء

```php
$pricingService = new PricingService();

// هذه الدالة توفر حماية من الاستثناءات
$result = $pricingService->safeCalculate(500, 'cash');

// حتى لو حدث خطأ، ستحصل على نتيجة بدلاً من استثناء
// [fallback] سيحتوي على رسالة الخطأ
```

### 4️⃣ الحصول على طرق البيع المتاحة

```php
$pricingService = new PricingService();

$methods = $pricingService->availableSaleMethods();

// الإرجاع:
// [
//     'cash' => 'كاش',
//     'installment' => 'قسط',
//     'credit' => 'آجل'
// ]

// يمكن استخدام هذا في القوائم المنسدلة
@foreach ($methods as $code => $name)
    <option value="{{ $code }}">{{ $name }}</option>
@endforeach
```

---

## 📚 توثيق الدوال

### `calculate(float $basePrice, string $saleMethodCode): array`

حساب السعر النهائي للمنتج بناءً على السعر الأساسي وطريقة البيع.

**المعاملات:**
| المعامل | النوع | الوصف |
|--------|------|-------|
| `$basePrice` | float | السعر الأساسي للمنتج |
| `$saleMethodCode` | string | كود طريقة البيع (cash, installment, credit) |

**الإرجاع:**
```php
[
    'base_price' => float,           // السعر الأساسي
    'final_price' => float,          // السعر النهائي
    'profit' => float,               // قيمة الربح المضافة
    'tier' => ProfitMarginTier|null, // بيانات الشريحة
    'sale_method' => string,         // كود طريقة البيع
    'fallback' => null|string        // رسالة الخطأ (إن وجدت)
]
```

**أمثلة:**
```php
// مثال 1: سعر في الشريحة الأولى بطريقة دفع نقدي
$result = $pricingService->calculate(200, 'cash');
// [final_price] => 210

// مثال 2: سعر في الشريحة الثالثة بطريقة قسط
$result = $pricingService->calculate(2000, 'installment');
// [final_price] => 2040

// مثال 3: سعر بدون شريحة مناسبة
$result = $pricingService->calculate(200, 'invalid_method');
// [fallback] => 'invalid_sale_method'
// [final_price] => 200 (نفس السعر الأساسي)
```

---

### `safeCalculate(float $basePrice, string $saleMethodCode): array`

نسخة آمنة من `calculate()` مع معالجة الاستثناءات.

**الاستخدام:**
```php
try {
    $result = $pricingService->safeCalculate(1000, 'installment');
    // لن يرمي استثناء حتى لو حدث خطأ
} catch (\Exception $e) {
    // لن يصل هنا عادة
}
```

---

### `availableSaleMethods(): array`

الحصول على جميع طرق البيع المفعلة والمتاحة.

**الإرجاع:**
```php
[
    'cash' => 'كاش',
    'installment' => 'قسط',
    'credit' => 'آجل'
]
```

**الاستخدام في Blade:**
```blade
<select name="sale_method">
    @foreach ($methods as $code => $name)
        <option value="{{ $code }}">{{ $name }}</option>
    @endforeach
</select>
```

---

### `isValidSaleMethod(string $code): bool` (Protected)

التحقق من أن طريقة البيع موجودة ومفعلة.

```php
// استخدام داخلي فقط
$isValid = $this->isValidSaleMethod('cash'); // true
```

---

### `fallback(float $basePrice, string $reason, $tier = null): array` (Protected)

إرجاع قيمة افتراضية آمنة عند فشل الحساب.

---

## ⚙️ معالجة الأخطاء

الخدمة توفر ثلاث حالات Fallback:

### 1. `invalid_sale_method`
طريقة البيع غير موجودة أو معطلة.

```php
$result = $pricingService->calculate(1000, 'invalid');
// [fallback] => 'invalid_sale_method'
// [final_price] => 1000
```

### 2. `no_tier`
لا توجد شريحة سعرية مناسبة للسعر.

```php
// إذا لم توجد شريحة تغطي السعر
$result = $pricingService->calculate(999999, 'cash');
// [fallback] => 'no_tier'
// [final_price] => 999999
```

### 3. `no_sale_method`
الشريحة موجودة لكن لا توجد طريقة بيع مرتبطة بها.

```php
$result = $pricingService->calculate(500, 'somemethod');
// [fallback] => 'no_sale_method'
// [final_price] => 500
```

---

## 📋 مثال شامل في Controller

```php
<?php

namespace App\Http\Controllers;

use App\Services\PricingService;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function create(PricingService $pricingService)
    {
        $saleMethods = $pricingService->availableSaleMethods();
        
        return view('products.create', compact('saleMethods'));
    }

    public function store(Request $request, PricingService $pricingService)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'sale_method_code' => 'required|in:cash,installment,credit',
        ]);

        // حساب السعر النهائي
        $result = $pricingService->calculate(
            $validated['base_price'],
            $validated['sale_method_code']
        );

        // التحقق من وجود خطأ
        if ($result['fallback']) {
            return back()->withErrors([
                'pricing' => 'خطأ في حساب السعر: ' . $result['fallback']
            ]);
        }

        // حفظ المنتج
        Product::create([
            'name' => $validated['name'],
            'base_price' => $result['base_price'],
            'final_price' => $result['final_price'],
            'profit' => $result['profit'],
            'profit_margin_tier_id' => $result['tier']->id,
        ]);

        return redirect()->route('products.index')
                       ->with('success', 'تم إضافة المنتج بنجاح');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }
}
```

---

## 🗂️ هيكل البيانات النموذجي

### بيانات الشريحة (profit_margin_tiers)
| name | min_value | max_value | priority | is_active |
|------|-----------|-----------|----------|-----------|
| حتى 300 | 0 | 300 | 4 | 1 |
| من 301 إلى 700 | 301 | 700 | 3 | 1 |
| من 701 إلى 1100 | 701 | 1100 | 2 | 1 |
| أكبر من 1100 | 1101 | null | 1 | 1 |

### بيانات طرق البيع (sale_methods)
| name | code | priority | is_active |
|------|------|----------|-----------|
| كاش | cash | 3 | 1 |
| قسط | installment | 2 | 1 |
| آجل | credit | 1 | 1 |

### قيم الأرباح (profit_margin_tier_methods)
| tier_id | sale_method_id | profit_value |
|---------|---|---|
| 1 | 1 (cash) | 10 |
| 1 | 2 (installment) | 20 |
| 1 | 3 (credit) | 80 |
| 2 | 1 (cash) | 15 |
| 2 | 2 (installment) | 30 |
| ... | ... | ... |

---

## 🔄 مثال على عملية الحساب

```
السعر الأساسي: 500 ريال
طريقة البيع: installment

1. ابحث عن الشريحة المناسبة:
   - min_value <= 500 <= max_value
   - النتيجة: الشريحة الثانية (من 301 إلى 700)

2. ابحث عن طريقة البيع في الشريحة:
   - code = 'installment' AND is_active = true
   - النتيجة: موجودة برقم 2

3. احصل على قيمة الربح:
   - profit_value = 30

4. احسب السعر النهائي:
   - final_price = 500 + 30 = 530
```

---

## 📝 الملاحظات الهامة

1. **ترتيب الأولويات**: عند وجود شرائح متداخلة، يتم اختيار الشريحة ذات الأولوية الأعلى.

2. **الشرائح المفتوحة**: يمكن ترك `max_value` كـ `null` لجعل الشريحة مفتوحة من الأعلى.

3. **الحماية من الأخطاء**: استخدم `safeCalculate()` في الأماكن الحساسة.

4. **التخزين**: يُنصح بتخزين `final_price` في قاعدة البيانات لتجنب إعادة الحساب.

5. **الأداء**: الخدمة تستخدم eager loading افتراضياً للعلاقات.

---

## 🐛 استكشاف الأخطاء

### المشكلة: لا يتم إرجاع السعر الصحيح

**الحل**: تحقق من:
1. أن الشريحة `is_active = true`
2. أن طريقة البيع `is_active = true`
3. أن قيم `min_value` و `max_value` صحيحة

### المشكلة: `fallback` دائماً موجود

**الحل**: جرب:
```php
// تحقق من البيانات
DB::table('profit_margin_tiers')->where('is_active', true)->get();
DB::table('sale_methods')->where('is_active', true)->get();
DB::table('profit_margin_tier_methods')->get();

// استخدم safeCalculate لرؤية الخطأ الفعلي
$result = $pricingService->safeCalculate(1000, 'cash');
dd($result);
```

---

## 📞 الدعم والمساعدة

للمزيد من التفاصيل، راجع:
- [Models Documentation](./MODELS.md)
- [Database Seeders](./SEEDERS.md)
