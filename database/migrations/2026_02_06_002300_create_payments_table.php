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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number', 50)->unique();
            
            // المرجع (فاتورة شراء أو بيع) - Polymorphic
            $table->string('paymentable_type', 50);
            $table->unsignedBigInteger('paymentable_id');
            
            // الطرف (مورد أو عميل) - Polymorphic
            $table->string('payer_type', 50);
            $table->unsignedBigInteger('payer_id');
            
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            
            // المبلغ
            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'credit_card', 'other']);
            
            // التواريخ
            $table->date('payment_date');
            
            // معلومات إضافية
            $table->string('reference_number', 100)->nullable(); // رقم الشيك أو التحويل
            $table->text('notes')->nullable();
            
            // الحالة
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['paymentable_type', 'paymentable_id']);
            $table->index(['payer_type', 'payer_id']);
            $table->index(['payment_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
