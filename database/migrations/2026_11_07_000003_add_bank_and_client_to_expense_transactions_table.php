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
        if (Schema::hasTable('expense_transactions')) {
            // Make expense_id nullable and relax payment_type & currency enum
            try {
                DB::statement("ALTER TABLE `expense_transactions` MODIFY `expense_id` BIGINT UNSIGNED NULL");
            } catch (\Exception $e) {
                // Ignore if already nullable
            }

            try {
                DB::statement("ALTER TABLE `expense_transactions` MODIFY `payment_type` VARCHAR(50) NOT NULL DEFAULT 'cash'");
            } catch (\Exception $e) {
                // Ignore
            }

            try {
                DB::statement("ALTER TABLE `expense_transactions` MODIFY `currency` VARCHAR(10) NOT NULL DEFAULT 'PKR'");
            } catch (\Exception $e) {
                // Ignore
            }

            Schema::table('expense_transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('expense_transactions', 'bank_id')) {
                    $table->unsignedBigInteger('bank_id')->nullable()->after('expense_id')->index();
                    $table->foreign('bank_id')->references('id')->on('banks')->onDelete('set null');
                }
                if (!Schema::hasColumn('expense_transactions', 'client_id')) {
                    $table->unsignedBigInteger('client_id')->nullable()->after('bank_id')->index();
                    $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
                }
                if (!Schema::hasColumn('expense_transactions', 'company_id')) {
                    $table->unsignedBigInteger('company_id')->nullable()->after('client_id')->index();
                    $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
                }
                if (!Schema::hasColumn('expense_transactions', 'transaction_type')) {
                    $table->string('transaction_type', 50)->default('debit')->after('payment_type');
                }
                if (!Schema::hasColumn('expense_transactions', 'payee_type')) {
                    $table->string('payee_type', 50)->default('expense')->after('transaction_type');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('expense_transactions')) {
            Schema::table('expense_transactions', function (Blueprint $table) {
                if (Schema::hasColumn('expense_transactions', 'bank_id')) {
                    $table->dropForeign(['bank_id']);
                    $table->dropColumn('bank_id');
                }
                if (Schema::hasColumn('expense_transactions', 'client_id')) {
                    $table->dropForeign(['client_id']);
                    $table->dropColumn('client_id');
                }
                if (Schema::hasColumn('expense_transactions', 'company_id')) {
                    $table->dropForeign(['company_id']);
                    $table->dropColumn('company_id');
                }
                if (Schema::hasColumn('expense_transactions', 'transaction_type')) {
                    $table->dropColumn('transaction_type');
                }
                if (Schema::hasColumn('expense_transactions', 'payee_type')) {
                    $table->dropColumn('payee_type');
                }
            });
        }
    }
};
