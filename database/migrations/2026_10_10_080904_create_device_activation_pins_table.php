<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_activation_pins', function (Blueprint $table) {
            $table->id();

            // Administrador que generó el PIN
            $table->foreignId('generated_by')
                ->constrained('users')
                ->cascadeOnDelete();

            // Hash del PIN, nunca almacenarlo en texto plano
            $table->string('pin_hash');

            // Vigencia del PIN
            $table->timestamp('expires_at')->index();

            // Fecha en que se utilizó
            $table->timestamp('used_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_activation_pins');
    }
};