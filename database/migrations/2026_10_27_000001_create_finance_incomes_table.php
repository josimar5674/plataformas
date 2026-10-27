<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_incomes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('business_customer_id')
                ->constrained('business_customers')
                ->restrictOnDelete();

            $table->string('description', 255);
            $table->date('income_date');

            // Todos los ingresos se registran originalmente en USD.
            $table->decimal('subtotal_usd', 14, 2);

            // Se conserva una copia del impuesto aplicado para proteger el historial.
            $table->foreignId('finance_tax_rate_id')
                ->nullable()
                ->constrained('finance_tax_rates')
                ->nullOnDelete();
            $table->string('tax_name_snapshot', 120)->nullable();
            $table->decimal('tax_percentage_snapshot', 7, 4)->default(0);
            $table->decimal('tax_amount_usd', 14, 2)->default(0);
            $table->decimal('total_usd', 14, 2);

            // Tasa mensual aplicada al ingreso; nunca se recalcula al cambiar la configuración.
            $table->decimal('exchange_rate', 12, 4);
            $table->decimal('subtotal_hnl', 16, 2);
            $table->decimal('tax_amount_hnl', 16, 2)->default(0);
            $table->decimal('total_hnl', 16, 2);

            $table->string('status', 20)->default('pending');
            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();

            $table->index(['income_date', 'status']);
            $table->index(['business_customer_id', 'income_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_incomes');
    }
};
