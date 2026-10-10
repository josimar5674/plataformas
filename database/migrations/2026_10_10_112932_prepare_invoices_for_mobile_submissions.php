<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->timestamp('received_at')->nullable();
            $table->timestamp('processed_at')->nullable();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->date('invoice_date')->nullable()->change();
            $table->decimal('amount', 12, 2)->nullable()->change();
            $table->string('provider_name')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->date('invoice_date')->nullable(false)->change();
            $table->decimal('amount', 12, 2)->nullable(false)->change();
            $table->string('provider_name')->nullable(false)->change();

            $table->dropColumn([
                'received_at',
                'processed_at',
            ]);
        });
    }
};