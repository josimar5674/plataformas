<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['invoice_type_id']);
            $table->dropColumn('invoice_type_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('configuration_option_id')
                ->after('user_id')
                ->constrained('configuration_options')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['configuration_option_id']);
            $table->dropColumn('configuration_option_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('invoice_type_id')
                ->constrained('invoice_types');
        });
    }
};