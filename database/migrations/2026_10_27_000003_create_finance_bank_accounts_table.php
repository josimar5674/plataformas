<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name', 120);
            $table->string('account_name', 160);
            $table->string('account_number', 80);
            $table->string('currency', 3)->default('HNL');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['active', 'bank_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_bank_accounts');
    }
};
