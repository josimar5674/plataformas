<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('finance_income_id')
                ->constrained('finance_incomes')
                ->cascadeOnDelete();

            $table->date('payment_date');
            $table->decimal('amount_hnl', 16, 2);

            // Se guarda como texto para no acoplar el historial a un catálogo aún no definido.
            $table->string('payment_method', 80);
            $table->string('reference', 120)->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('received_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();

            $table->index(['finance_income_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_payments');
    }
};
