# 🔌 API Documentation

## 📋 جدول المحتويات

1. [نظرة عامة](#نظرة-عامة)
2. [المصادقة](#المصادقة)
3. [نقاط النهاية](#نقاط-النهاية)
4. [أمثلة الاستخدام](#أمثلة-الاستخدام)
5. [معالجة الأخطاء](#معالجة-الأخطاء)

---

## 🎯 نظرة عامة

### Base URL
```
http://localhost:8000/api
```

### Headers
```http
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token}
```

### Response Format
جميع الاستجابات بصيغة JSON:
```json
{
    "success": true,
    "message": "Success message",
    "data": {}
}
```

---

## 🔐 المصادقة

### 1. تسجيل الدخول
```http
POST /api/login
```

**Request:**
```json
{
    "email": "admin@example.com",
    "password": "password"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "token": "1|abc123...",
        "user": {
            "id": 1,
            "name": "Admin User",
            "email": "admin@example.com"
        }
    }
}
```

### 2. تسجيل الخروج
```http
POST /api/logout
Authorization: Bearer {token}
```

---

## 📍 نقاط النهاية

### Products

#### Get All Products
```http
GET /api/products
```

**Query Parameters:**
- `page` (optional): رقم الصفحة
- `per_page` (optional): عدد العناصر في الصفحة
- `search` (optional): البحث
- `category_id` (optional): فلترة حسب التصنيف

**Response:**
```json
{
    "success": true,
    "data": {
        "products": [
            {
                "id": 1,
                "name": "Product Name",
                "sku": "SKU123",
                "current_base_price": 100.00,
                "stock_quantity": 50,
                "category": {
                    "id": 1,
                    "name": "Category Name"
                }
            }
        ],
        "pagination": {
            "total": 100,
            "per_page": 15,
            "current_page": 1,
            "last_page": 7
        }
    }
}
```

#### Get Single Product
```http
GET /api/products/{id}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Product Name",
        "sku": "SKU123",
        "current_cost_price": 80.00,
        "current_base_price": 100.00,
        "stock_quantity": 50,
        "category": {},
        "units": [],
        "images": []
    }
}
```

#### Create Product
```http
POST /api/products
Authorization: Bearer {token}
```

**Request:**
```json
{
    "name": "New Product",
    "sku": "SKU456",
    "category_id": 1,
    "base_unit": "piece",
    "description": "Product description",
    "is_active": true
}
```

#### Update Product
```http
PUT /api/products/{id}
Authorization: Bearer {token}
```

#### Delete Product
```http
DELETE /api/products/{id}
Authorization: Bearer {token}
```

---

### Categories

#### Get All Categories
```http
GET /api/categories
```

#### Create Category
```http
POST /api/categories
Authorization: Bearer {token}
```

**Request:**
```json
{
    "name": "New Category",
    "parent_id": null,
    "description": "Category description",
    "is_active": true
}
```

---

### Pricing

#### Calculate Price
```http
POST /api/pricing/calculate
Authorization: Bearer {token}
```

**Request:**
```json
{
    "base_price": 100.00,
    "sale_method_code": "cash"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "base_price": 100.00,
        "tier": "Tier 1",
        "sale_method": "Cash",
        "margin_type": "percentage",
        "margin_value": 20.00,
        "profit": 20.00,
        "final_price": 120.00
    }
}
```

#### Get Pricing Tiers
```http
GET /api/pricing/tiers
```

---

### Invoices

#### Create Purchase Invoice
```http
POST /api/invoices/purchase
Authorization: Bearer {token}
```

**Request:**
```json
{
    "supplier_id": 1,
    "warehouse_id": 1,
    "invoice_date": "2026-02-06",
    "items": [
        {
            "product_id": 1,
            "quantity": 10,
            "unit_price": 80.00
        }
    ]
}
```

#### Create Sale Invoice
```http
POST /api/invoices/sale
Authorization: Bearer {token}
```

**Request:**
```json
{
    "customer_id": 1,
    "warehouse_id": 1,
    "sale_method_id": 1,
    "invoice_date": "2026-02-06",
    "items": [
        {
            "product_id": 1,
            "product_unit_id": 1,
            "quantity": 2
        }
    ]
}
```

---

## 💡 أمثلة الاستخدام

### JavaScript (Fetch)
```javascript
// Login
const login = async () => {
    const response = await fetch('http://localhost:8000/api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            email: 'admin@example.com',
            password: 'password'
        })
    });
    
    const data = await response.json();
    const token = data.data.token;
    
    // Store token
    localStorage.setItem('token', token);
};

// Get Products
const getProducts = async () => {
    const token = localStorage.getItem('token');
    
    const response = await fetch('http://localhost:8000/api/products', {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    });
    
    const data = await response.json();
    console.log(data.data.products);
};
```

### PHP (cURL)
```php
// Login
$ch = curl_init('http://localhost:8000/api/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'admin@example.com',
    'password' => 'password'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$data = json_decode($response, true);
$token = $data['data']['token'];

// Get Products
$ch = curl_init('http://localhost:8000/api/products');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$products = json_decode($response, true);
```

---

## ⚠️ معالجة الأخطاء

### Error Response Format
```json
{
    "success": false,
    "message": "Error message",
    "errors": {
        "field_name": ["Error detail"]
    }
}
```

### HTTP Status Codes
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

### مثال: Validation Error
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "name": ["The name field is required."],
        "email": ["The email must be a valid email address."]
    }
}
```

---

## 📝 ملاحظات

1. جميع التواريخ بصيغة `YYYY-MM-DD`
2. جميع الأسعار بصيغة `decimal(15,2)`
3. Token صالح لمدة 24 ساعة
4. Rate Limit: 60 طلب في الدقيقة

---

للمزيد:
- [دليل التطوير](DEVELOPMENT_GUIDE.md)
- [نظام التسعير](PRICING_SERVICE.md)
