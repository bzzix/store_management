<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Make paymentable nullable to support standalone vouchers
            $table->string('paymentable_type', 50)->nullable()->change();
            $table->unsignedBigInteger('paymentable_id')->nullable()->change();

            // Voucher type (null = linked to invoice, receipt = for customer, disbursement = for supplier)
            $table->enum('voucher_type', ['receipt', 'disbursement'])->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('voucher_type');
            $table->string('paymentable_type', 50)->nullable(false)->change();
            $table->unsignedBigInteger('paymentable_id')->nullable(false)->change();
        });
    }
};
