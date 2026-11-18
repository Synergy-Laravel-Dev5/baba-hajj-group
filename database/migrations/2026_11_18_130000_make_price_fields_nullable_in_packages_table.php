<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('packages')) {
            // Using raw statement for maximum compatibility across MySQL/MariaDB
            DB::statement("
                ALTER TABLE `packages` 
                MODIFY COLUMN `price_sharing` DECIMAL(12,2) NULL DEFAULT 0.00,
                MODIFY COLUMN `price_quad` DECIMAL(12,2) NULL DEFAULT 0.00,
                MODIFY COLUMN `price_triple` DECIMAL(12,2) NULL DEFAULT 0.00,
                MODIFY COLUMN `price_double` DECIMAL(12,2) NULL DEFAULT 0.00,
                MODIFY COLUMN `price_per_person` DECIMAL(12,2) NULL DEFAULT 0.00
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('packages')) {
            DB::statement("
                ALTER TABLE `packages` 
                MODIFY COLUMN `price_sharing` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                MODIFY COLUMN `price_quad` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                MODIFY COLUMN `price_triple` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                MODIFY COLUMN `price_double` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                MODIFY COLUMN `price_per_person` DECIMAL(12,2) NOT NULL DEFAULT 0.00
            ");
        }
    }
};
