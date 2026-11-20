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
        if (!Schema::hasTable('hajj_seasons')) {
            Schema::create('hajj_seasons', function (Blueprint $table) {
                $table->id();
                $table->string('year', 10)->unique();
                $table->string('hijri_year', 20)->nullable();
                $table->string('name', 100);
                $table->boolean('is_active')->default(false)->index();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });

            // Insert initial default season (2027 as active)
            DB::table('hajj_seasons')->insert([
                [
                    'year'        => '2027',
                    'hijri_year'  => '1448',
                    'name'        => 'Hajj Season 2027 (1448H)',
                    'is_active'   => true,
                    'start_date'  => '2027-04-01',
                    'end_date'    => '2027-06-30',
                    'notes'       => 'Default Active Hajj Season',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
            ]);
        }

        // Ensure packages with null 'year' are backfilled from 'gregorian_year'
        if (Schema::hasTable('packages') && Schema::hasColumn('packages', 'year') && Schema::hasColumn('packages', 'gregorian_year')) {
            DB::statement("UPDATE packages SET `year` = `gregorian_year` WHERE (`year` IS NULL OR `year` = '') AND `gregorian_year` IS NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hajj_seasons');
    }
};
