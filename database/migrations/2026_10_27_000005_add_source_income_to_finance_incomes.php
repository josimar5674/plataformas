<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finance_incomes', function (Blueprint $table) {
            $table->foreignId('copied_from_income_id')
                ->nullable()
                ->after('created_by')
                ->constrained('finance_incomes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('finance_incomes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('copied_from_income_id');
        });
    }
};
