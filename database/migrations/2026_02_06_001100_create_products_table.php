<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('warehouse_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('profit_margin_tier_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku', 100)->unique();
            $table->string('barcode', 100)->unique()->nullable();
            
            $table->text('description')->nullable();
            $table->string('short_description', 500)->nullable();
            
            // الأسعار الحالية (cached) - NULL حتى أول فاتورة شراء
            $table->decimal('current_cost_price', 15, 2)->nullable();
            $table->decimal('current_base_price', 15, 2)->nullable();
            
            // الوحدة الأساسية
            $table->string('base_unit', 50)->default('piece');
            
            // المخزون
            $table->decimal('stock_quantity', 10, 3)->default(0);
            $table->decimal('min_stock_level', 10, 3)->default(0);
            $table->decimal('max_stock_level', 10, 3)->nullable();
            
            // الصور
            $table->string('main_image')->nullable();
            
            // الحالة
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('track_inventory')->default(true);
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['category_id', 'warehouse_id', 'profit_margin_tier_id']);
            $table->index(['is_active', 'is_featured']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
