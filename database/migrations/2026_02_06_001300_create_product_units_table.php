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
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            
            $table->string('unit_name', 50); // 'كيلو', 'شيكارة', 'كرتون'
            $table->string('unit_name_en', 50)->nullable(); // 'kg', 'bag', 'carton'
            $table->decimal('unit_value', 10, 3); // 1 شيكارة = 25 كيلو
            $table->boolean('is_default')->default(false);
            $table->string('barcode', 100)->unique()->nullable();
            
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['product_id', 'unit_name']);
            
            // Indexes
            $table->index(['product_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_units');
    }
};
