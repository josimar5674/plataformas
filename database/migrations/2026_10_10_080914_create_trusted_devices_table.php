<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trusted_devices', function (Blueprint $table) {
            $table->id();

            // Usuario propietario del dispositivo
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Identificador persistente del dispositivo
            $table->string('device_uuid', 100)->unique();

            // Información para identificarlo en Laravel
            $table->string('device_name')->nullable();
            $table->string('platform', 30)->nullable();

            // Control de autorización
            $table->boolean('is_active')->default(true);
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('last_used_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trusted_devices');
    }
};