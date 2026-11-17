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
                if (!Schema::hasColumn('booking_persons', 'medical_document')) {
                    $table->string('medical_document')->nullable()->after('passport_image');
                }
                if (!Schema::hasColumn('booking_persons', 'id_document')) {
                    $table->string('id_document')->nullable()->after('medical_document');
                }
                if (!Schema::hasColumn('booking_persons', 'portal_password')) {
                    $table->string('portal_password')->nullable()->after('id_document');
                }
            });
        }

        if (Schema::hasTable('person_documents')) {
            Schema::table('person_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('person_documents', 'medical')) {
                    $table->string('medical')->nullable()->after('ticket');
                }
                if (!Schema::hasColumn('person_documents', 'id_document')) {
                    $table->string('id_document')->nullable()->after('medical');
                }
                if (!Schema::hasColumn('person_documents', 'password')) {
                    $table->string('password')->nullable()->after('id_document');
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
                if (Schema::hasColumn('booking_persons', 'portal_password')) {
                    $table->dropColumn('portal_password');
                }
                if (Schema::hasColumn('booking_persons', 'id_document')) {
                    $table->dropColumn('id_document');
                }
                if (Schema::hasColumn('booking_persons', 'medical_document')) {
                    $table->dropColumn('medical_document');
                }
            });
        }

        if (Schema::hasTable('person_documents')) {
            Schema::table('person_documents', function (Blueprint $table) {
                if (Schema::hasColumn('person_documents', 'password')) {
                    $table->dropColumn('password');
                }
                if (Schema::hasColumn('person_documents', 'id_document')) {
                    $table->dropColumn('id_document');
                }
                if (Schema::hasColumn('person_documents', 'medical')) {
                    $table->dropColumn('medical');
                }
            });
        }
    }
};
