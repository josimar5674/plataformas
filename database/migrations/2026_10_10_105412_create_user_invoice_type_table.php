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
    Schema::create('user_invoice_type', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('configuration_option_id')
            ->constrained('configuration_options')
            ->cascadeOnDelete();

        $table->timestamps();

        $table->unique([
            'user_id',
            'configuration_option_id',
        ]);
    });
}

public function down(): void
{
    Schema::dropIfExists('user_invoice_type');
}
};
