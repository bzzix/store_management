# 📚 نظام إدارة المتجر - التوثيق الشامل

## 📋 جدول المحتويات

1. [نظرة عامة](#نظرة-عامة)
2. [المتطلبات](#المتطلبات)
3. [التثبيت](#التثبيت)
4. [البنية التقنية](#البنية-التقنية)
5. [الوثائق التفصيلية](#الوثائق-التفصيلية)

---

## 🎯 نظرة عامة

نظام إدارة متجر شامل مبني بـ Laravel يوفر:

- ✅ إدارة المنتجات والتصنيفات
- ✅ نظام تسعير مرن مع شرائح سعرية
- ✅ دعم وحدات بيع متعددة (كيلو، شيكارة، كرتون)
- ✅ إدارة المخزون في مخازن متعددة
- ✅ فواتير الشراء والبيع
- ✅ إدارة الموردين والعملاء
- ✅ نظام مدفوعات مرن
- ✅ تتبع كامل لحركات المخزون
- ✅ تاريخ كامل للأسعار

---

## 💻 المتطلبات

### متطلبات النظام:
- PHP >= 8.2
- MySQL >= 8.0
- Composer
- Node.js & NPM

### حزم Laravel:
- Laravel 11.x
- Laravel Jetstream (Livewire)
- Spatie Laravel Permission
- iziToast للإشعارات

---

## 🚀 التثبيت

### 1. استنساخ المشروع:
```bash
git clone <repository-url>
cd abdelstar_agri_mng
```

### 2. تثبيت الحزم:
```bash
composer install
npm install
```

### 3. إعداد البيئة:
```bash
cp .env.example .env
php artisan key:generate
```

### 4. إعداد قاعدة البيانات:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=abdelstar_agri_mng
DB_USERNAME=root
DB_PASSWORD=
```

### 5. تشغيل Migrations:
```bash
php artisan migrate:fresh --seed
```

### 6. بناء الأصول:
```bash
npm run build
```

### 7. تشغيل السيرفر:
```bash
php artisan serve
```

---

## 🏗️ البنية التقنية

### Frontend:
- **Blade Templates** - للعرض
- **Livewire** - للتفاعل الديناميكي
- **iziToast** - للإشعارات
- **TailwindCSS** - للتنسيق

### Backend:
- **Controllers** - رقيقة، تستدعي Services فقط
- **Service Classes** - تحتوي على المنطق التجاري
- **Models** - Eloquent ORM
- **Policies** - للصلاحيات

### قاعدة البيانات:
- **MySQL** - قاعدة البيانات الرئيسية
- **21 جدول** - تصميم شامل
- **Foreign Keys** - علاقات واضحة
- **Indexes** - محسّنة للأداء

---

## 📖 الوثائق التفصيلية

### 1. [تصميم قاعدة البيانات](DATABASE_DESIGN.md)
- مخطط ERD كامل
- تفاصيل جميع الجداول
- العلاقات والـ Indexes
- أمثلة الاستعلامات

### 2. [نظام التسعير](PRICING_SERVICE.md)
- كيفية عمل نظام التسعير المرن
- الشرائح السعرية
- طرق البيع (نقدي، تقسيط، آجل)
- أمثلة الاستخدام

### 3. [دليل التطوير](DEVELOPMENT_GUIDE.md)
- معايير الكود
- كيفية إضافة ميزات جديدة
- Workflow للـ Modules
- أفضل الممارسات

### 4. [API Documentation](API_DOCUMENTATION.md)
- نقاط النهاية المتاحة
- أمثلة الطلبات والاستجابات
- المصادقة والصلاحيات

---

## 👥 المستخدمون الافتراضيون

بعد تشغيل `php artisan db:seed`:

| البريد الإلكتروني | كلمة المرور | الدور |
|-------------------|-------------|-------|
| bzzixs@gmail.com | password | Super Admin |
| admin@example.com | password | Admin |
| editor@example.com | password | Editor |
| cashier@example.com | password | Cashier |

---

## 🔐 الصلاحيات

النظام يستخدم **Spatie Laravel Permission** مع الأدوار التالية:

- **Super Admin** - صلاحيات كاملة
- **Admin** - إدارة كاملة
- **Editor** - تحرير المحتوى
- **Author** - إنشاء المحتوى
- **Cashier** - البيع والفواتير
- **User** - مستخدم عادي
- **Client** - عميل

---

## 📁 هيكل المشروع

```
abdelstar_agri_mng/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Controllers رقيقة
│   │   └── Livewire/         # Livewire Components
│   ├── Models/               # Eloquent Models
│   ├── Services/             # Business Logic
│   └── Policies/             # Authorization
├── database/
│   ├── migrations/           # Database Migrations
│   └── seeders/              # Data Seeders
├── docs/                     # التوثيق
├── resources/
│   └── views/                # Blade Templates
└── routes/
    └── web.php               # Web Routes
```

---

## 🎯 الميزات الرئيسية

### 1. نظام التسعير المرن
- شرائح سعرية متعددة
- طرق بيع مختلفة (نقدي، تقسيط، آجل)
- حساب تلقائي للأسعار
- تاريخ كامل للأسعار

### 2. وحدات البيع المتعددة
- دعم وحدات متعددة لنفس المنتج
- تحويل تلقائي للوحدة الأساسية
- باركود لكل وحدة

### 3. إدارة المخزون
- مخازن متعددة
- تتبع كامل للحركات
- كميات محجوزة
- تنبيهات الحد الأدنى

### 4. الفواتير
- فواتير شراء وبيع
- حساب تلقائي للضرائب والخصومات
- حالات متعددة (مسودة، معلقة، مكتملة)
- ربط مع المدفوعات

---

## 🔄 تدفق العمل

### إضافة منتج جديد:
1. إنشاء المنتج (بدون أسعار)
2. استلام فاتورة شراء
3. يتم تحديث الأسعار تلقائياً
4. يتم تحديث المخزون

### عملية البيع:
1. اختيار المنتج والوحدة
2. حساب السعر تلقائياً عبر PricingService
3. إنشاء الفاتورة
4. خصم المخزون
5. تسجيل الحركة

---

## 🛠️ الأوامر المفيدة

```bash
# تشغيل Migrations
php artisan migrate

# إعادة بناء قاعدة البيانات
php artisan migrate:fresh --seed

# إنشاء Model جديد
php artisan make:model ModelName

# إنشاء Livewire Component
php artisan make:livewire ComponentName

# مسح الـ Cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## 📞 الدعم والمساعدة

للمزيد من المعلومات، راجع:
- [تصميم قاعدة البيانات](DATABASE_DESIGN.md)
- [نظام التسعير](PRICING_SERVICE.md)
- [دليل التطوير](DEVELOPMENT_GUIDE.md)

---

## 📝 الترخيص

هذا المشروع مرخص تحت [MIT License](LICENSE).
