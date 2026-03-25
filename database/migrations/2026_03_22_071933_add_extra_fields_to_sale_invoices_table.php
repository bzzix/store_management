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
        Schema::table('sale_invoices', function (Blueprint $table) {
            $table->decimal('previous_balance', 15, 2)->default(0)->after('paid_amount');
            $table->string('car_number', 50)->nullable()->after('notes');
            $table->string('driver_name', 100)->nullable()->after('car_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_invoices', function (Blueprint $table) {
            $table->dropColumn(['previous_balance', 'car_number', 'driver_name']);
        });
    }
};
