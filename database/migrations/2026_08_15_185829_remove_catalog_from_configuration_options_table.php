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
            Schema::table('configuration_options', function (Blueprint $table) {
                $table->dropIndex('configuration_options_type_index');
            });

            Schema::table('configuration_options', function (Blueprint $table) {
                $table->dropColumn('catalog');
            });

            Schema::table('configuration_options', function (Blueprint $table) {
                $table->index('type', 'configuration_options_type_index');
            });

            return;
        }

        Schema::table('configuration_options', function (Blueprint $table) {
            $table->dropColumn('catalog');
        });
    }

    public function down(): void
    {
        Schema::table('configuration_options', function (Blueprint $table) {
            $table->string('catalog')->nullable();
        });
    }
};