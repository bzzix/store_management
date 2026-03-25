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
        Schema::create('sale_methods', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                ->comment('اسم طريقة البيع');

            $table->string('code')
                ->unique()
                ->comment('كود برمجي (cash, installment, credit)');

            $table->boolean('is_active')->default(true);

            $table->integer('priority')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_methods');
    }
};
