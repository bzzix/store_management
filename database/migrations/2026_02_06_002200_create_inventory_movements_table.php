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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->foreignId('warehouse_id')->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            
            // نوع الحركة
            $table->enum('movement_type', ['in', 'out', 'transfer', 'adjustment', 'return']);
            $table->string('reference_type', 50)->nullable(); // purchase_invoice, sale_invoice, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            
            // الكميات
            $table->decimal('quantity', 10, 3);
            $table->decimal('quantity_before', 10, 3);
            $table->decimal('quantity_after', 10, 3);
            
            // القيمة المالية
            $table->decimal('unit_cost', 15, 2)->nullable();
            // total_cost = quantity * unit_cost (computed in application)
            
            // معلومات إضافية
            $table->text('notes')->nullable();
            $table->dateTime('movement_date');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['product_id', 'warehouse_id', 'movement_type', 'movement_date'], 'idx_inv_mov_main');
            $table->index(['reference_type', 'reference_id'], 'idx_inv_mov_ref');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
