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
        Schema::table('booking_persons', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_persons', 'father_name')) {
                $table->string('father_name', 191)->nullable()->after('given_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_persons', function (Blueprint $table) {
            if (Schema::hasColumn('booking_persons', 'father_name')) {
                $table->dropColumn('father_name');
            }
        });
    }
};
