<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expedientes', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('numero_expediente')->unique();
            $table->string('tipo_tramite');
            $table->string('matricula')->nullable();
            $table->string('sede')->nullable();
            $table->string('asignado')->nullable();

            $table->text('pretension_principal')->nullable();
            $table->decimal('cuantia', 15, 2)->nullable();

            $table->date('fecha_presentacion')->nullable();

            $table->text('descripcion_proceso')->nullable();

            $table->string('estado')->default('En trámite');

            $table->boolean('permite_edicion')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expedientes');
    }
};