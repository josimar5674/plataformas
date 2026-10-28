<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finance_incomes', function (Blueprint $table) {
            $table->foreignId('inversion_id')->nullable()->after('business_customer_id')
                ->constrained('inversiones')->nullOnDelete();
            $table->string('invoice_number', 100)->nullable()->after('income_date');
        });

        Schema::create('finance_income_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finance_income_id')->constrained('finance_incomes')->cascadeOnDelete();
            $table->foreignId('comercial_id')->nullable()->constrained('comercial')->nullOnDelete();
            $table->string('product_name_snapshot', 255);
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('unit_price_usd', 15, 2);
            $table->decimal('subtotal_usd', 16, 2);
            $table->foreignId('finance_tax_rate_id')->nullable()->constrained('finance_tax_rates')->nullOnDelete();
            $table->string('tax_name_snapshot', 120)->nullable();
            $table->decimal('tax_percentage_snapshot', 7, 4)->default(0);
            $table->decimal('tax_amount_usd', 16, 2)->default(0);
            $table->decimal('total_usd', 16, 2);
            $table->decimal('subtotal_hnl', 18, 2);
            $table->decimal('tax_amount_hnl', 18, 2)->default(0);
            $table->decimal('total_hnl', 18, 2);
            $table->timestamps();
            $table->index(['finance_income_id', 'comercial_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_income_items');
        Schema::table('finance_incomes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('inversion_id');
            $table->dropColumn('invoice_number');
        });
    }
};
