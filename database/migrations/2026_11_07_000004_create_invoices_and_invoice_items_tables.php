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
        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_no', 50)->unique()->index();
                $table->unsignedBigInteger('client_id')->index();
                $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->unsignedBigInteger('bank_id')->nullable()->index();
                $table->text('bill_to')->nullable();
                $table->date('invoice_date');
                $table->date('due_date');
                $table->string('terms', 100)->nullable()->default('Due on Receipt');
                $table->string('currency', 10)->default('PKR');
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('discount', 15, 2)->default(0);
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->decimal('payment_made', 15, 2)->default(0);
                $table->decimal('balance_due', 15, 2)->default(0);
                $table->text('notes')->nullable();
                $table->text('terms_conditions')->nullable();
                $table->enum('status', ['draft', 'unpaid', 'partial', 'paid', 'cancelled'])->default('unpaid')->index();
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
                $table->foreign('bank_id')->references('id')->on('banks')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('invoice_items')) {
            Schema::create('invoice_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('invoice_id')->index();
                $table->string('item_name')->nullable();
                $table->longText('description')->nullable();
                $table->decimal('qty', 10, 2)->default(1.00);
                $table->decimal('rate', 15, 2)->default(0.00);
                $table->decimal('amount', 15, 2)->default(0.00);
                $table->timestamps();

                $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
