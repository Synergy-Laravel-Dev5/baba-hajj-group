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
        Schema::table('package_inquiries', function (Blueprint $table) {
            if (!Schema::hasColumn('package_inquiries', 'app_user_id')) {
                $table->foreignId('app_user_id')->nullable()->after('package_id')->constrained('app_users')->nullOnDelete();
            }
            if (!Schema::hasColumn('package_inquiries', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('special_request');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_inquiries', function (Blueprint $table) {
            if (Schema::hasColumn('package_inquiries', 'app_user_id')) {
                $table->dropConstrainedForeignId('app_user_id');
            }
            if (Schema::hasColumn('package_inquiries', 'admin_notes')) {
                $table->dropColumn('admin_notes');
            }
        });
    }
};
