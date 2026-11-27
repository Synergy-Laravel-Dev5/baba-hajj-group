<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            DB::statement("ALTER TABLE `package_inquiries` MODIFY `status` ENUM('pending', 'contacted', 'in_progress', 'completed', 'closed', 'cancelled') NOT NULL DEFAULT 'pending'");
        } catch (\Throwable $e) {
            // Ignore if driver does not support
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE `package_inquiries` MODIFY `status` ENUM('pending', 'contacted', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
        } catch (\Throwable $e) {
        }
    }
};
