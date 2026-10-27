<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finance_payments', function (Blueprint $table) {
            $table->foreignId('finance_bank_account_id')
                ->nullable()
                ->after('payment_method')
                ->constrained('finance_bank_accounts')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('finance_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('finance_bank_account_id');
        });
    }
};
