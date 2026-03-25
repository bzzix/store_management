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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('phone', 20);
            $table->string('phone_2', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->default('Saudi Arabia');
            
            // معلومات مالية
            $table->string('tax_number', 50)->nullable();
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            
            // تصنيف العميل
            $table->enum('customer_type', ['individual', 'company'])->default('individual');
            $table->string('company_name')->nullable();
            
            // معلومات إضافية
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['phone', 'customer_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
