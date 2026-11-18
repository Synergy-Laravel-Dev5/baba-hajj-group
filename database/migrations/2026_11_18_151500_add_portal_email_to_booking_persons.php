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
        if (Schema::hasTable('booking_persons')) {
            Schema::table('booking_persons', function (Blueprint $table) {
                if (!Schema::hasColumn('booking_persons', 'portal_email')) {
                    $table->string('portal_email')->nullable()->after('portal_password');
                }
            });
        }

        if (Schema::hasTable('person_documents')) {
            Schema::table('person_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('person_documents', 'email')) {
                    $table->string('email')->nullable()->after('password');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('booking_persons')) {
            Schema::table('booking_persons', function (Blueprint $table) {
                if (Schema::hasColumn('booking_persons', 'portal_email')) {
                    $table->dropColumn('portal_email');
                }
            });
        }

        if (Schema::hasTable('person_documents')) {
            Schema::table('person_documents', function (Blueprint $table) {
                if (Schema::hasColumn('person_documents', 'email')) {
                    $table->dropColumn('email');
                }
            });
        }
    }
};
