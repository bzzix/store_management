<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add financial tracking columns to customers
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('total_invoices', 15, 2)->default(0)->after('opening_balance');
            $table->decimal('total_paid', 15, 2)->default(0)->after('total_invoices');
        });

        // Add financial tracking columns to suppliers
        Schema::table('suppliers', function (Blueprint $table) {
            $table->decimal('total_invoices', 15, 2)->default(0)->after('opening_balance');
            $table->decimal('total_paid', 15, 2)->default(0)->after('total_invoices');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['total_invoices', 'total_paid']);
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['total_invoices', 'total_paid']);
        });
    }
};
