<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120)->unique();
            $table->decimal('percentage', 7, 4);
            $table->boolean('active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_tax_rates');
    }
};
