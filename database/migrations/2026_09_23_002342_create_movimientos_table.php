<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos', function (Blueprint $table) {

            $table->id();

            $table->foreignId('expediente_id')
                ->constrained('expedientes')
                ->cascadeOnDelete();

            $table->date('fecha');
            $table->text('descripcion');

            $table->string('archivo')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};