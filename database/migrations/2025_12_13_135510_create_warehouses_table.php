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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('اسم المخزن');
            $table->string('code', 50)->unique()->comment('كود المخزن');

            $table->text('address')->nullable()->comment('عنوان المخزن');
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();

            $table->boolean('is_main')->default(false)->comment('هل هو المخزن الرئيسي؟');

            $table->integer('capacity')->nullable()->comment('السعة القصوى');

            $table->decimal('current_stock_value', 15, 2)
                ->default(0)
                ->comment('قيمة المخزون الحالية');

            $table->foreignId('manager_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('مدير المخزن');

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
