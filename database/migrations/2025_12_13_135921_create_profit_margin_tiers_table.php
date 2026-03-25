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
        Schema::create('profit_margin_tiers', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                ->comment('اسم الشريحة');

            $table->decimal('min_value', 15, 2)
                ->comment('يبدأ من');

            $table->decimal('max_value', 15, 2)
                ->nullable()
                ->comment('ينتهي عند (NULL يعني مفتوح)');

            // تنظيم واستخدام فعلي
            $table->integer('priority')
                ->default(0)
                ->comment('الأولوية عند التداخل');

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            // تحسين الأداء
            $table->index(['min_value', 'max_value']);
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profit_margin_tiers');
    }
};
