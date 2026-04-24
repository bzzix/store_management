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
        Schema::table('sale_invoice_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->change();
            $table->string('custom_name')->nullable()->after('product_id');
            $table->boolean('is_custom')->default(false)->after('custom_name');
        });

        Schema::table('purchase_invoice_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->change();
            $table->string('custom_name')->nullable()->after('product_id');
            $table->boolean('is_custom')->default(false)->after('custom_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_invoice_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable(false)->change();
            $table->dropColumn(['custom_name', 'is_custom']);
        });

        Schema::table('purchase_invoice_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable(false)->change();
            $table->dropColumn(['custom_name', 'is_custom']);
        });
    }
};
