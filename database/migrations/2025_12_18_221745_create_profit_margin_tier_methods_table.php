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
        Schema::create('profit_margin_tier_methods', function (Blueprint $table) {
            $table->id();

            $table->foreignId('profit_margin_tier_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('sale_method_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('profit_value', 10, 2)
                ->comment('قيمة الربح لهذه الطريقة');

            $table->timestamps();

            $table->unique(
                ['profit_margin_tier_id', 'sale_method_id'],
                'tier_method_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profit_margin_tier_methods');
    }
};
