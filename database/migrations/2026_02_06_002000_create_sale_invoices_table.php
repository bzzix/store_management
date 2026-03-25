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
        Schema::create('sale_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->foreignId('warehouse_id')->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->foreignId('sale_method_id')->nullable()->constrained()->onDelete('set null');
            
            // التواريخ
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            
            // المبالغ
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            // remaining_amount = total_amount - paid_amount (computed in application)
            
            // الأرباح
            $table->decimal('total_cost', 15, 2)->default(0);
            // total_profit = subtotal - total_cost (computed in application)
            
            // الحالة
            $table->enum('status', ['draft', 'pending', 'completed', 'cancelled', 'returned'])->default('draft');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            
            // ملاحظات
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['customer_id', 'warehouse_id', 'user_id', 'sale_method_id'], 'idx_sale_inv_relations');
            $table->index(['invoice_date', 'status', 'payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_invoices');
    }
};
