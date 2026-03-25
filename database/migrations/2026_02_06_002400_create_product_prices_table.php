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
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('purchase_invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // الأسعار
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->decimal('base_price', 15, 2)->default(0);
            
            // التواريخ
            $table->dateTime('effective_from');
            $table->dateTime('effective_to')->nullable();
            
            // معلومات إضافية
            $table->boolean('is_current')->default(false);
            $table->enum('change_reason', ['purchase', 'manual', 'promotion', 'adjustment'])->default('purchase');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['product_id', 'purchase_invoice_id', 'user_id']);
            $table->index(['effective_from', 'effective_to', 'is_current']);
            $table->index(['product_id', 'is_current']); // Composite index for performance
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
