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
        if (Schema::hasTable('clients')) {
            Schema::table('clients', function (Blueprint $table) {
                if (!Schema::hasColumn('clients', 'company_id')) {
                    $table->foreignId('company_id')
                        ->nullable()
                        ->after('id')
                        ->constrained('companies')
                        ->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('clients')) {
            Schema::table('clients', function (Blueprint $table) {
                if (Schema::hasColumn('clients', 'company_id')) {
                    $table->dropForeign(['company_id']);
                    $table->dropColumn('company_id');
                }
            });
        }
    }
};
