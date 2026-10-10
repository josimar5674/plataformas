<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite no admite MODIFY. Laravel reconstruye la tabla
            // cuando es necesario cambiar el tipo de una columna.
            Schema::table('comercial', function (Blueprint $table) {
                $table->string('unidad')->nullable()->change();
            });

            return;
        }

        DB::statement("
            ALTER TABLE comercial
            MODIFY unidad VARCHAR(255) NULL
        ");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('comercial', function (Blueprint $table) {
                $table->decimal('unidad', 15, 2)->nullable()->change();
            });

            return;
        }

        DB::statement("
            ALTER TABLE comercial
            MODIFY unidad DECIMAL(15,2) NULL
        ");
    }
};