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
        Schema::create('sale_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->foreignId('product_unit_id')->nullable()->constrained()->onDelete('set null');
            
            $table->decimal('quantity', 10, 3);
            $table->decimal('cost_price', 15, 2); // من current_cost_price
            $table->decimal('unit_price', 15, 2); // محسوب عبر PricingService
            // subtotal = quantity * unit_price (computed in application)
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            // profit = (unit_price - cost_price) * quantity (computed in application)
            
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Index
            $table->index(['sale_invoice_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_invoice_items');
    }
};
