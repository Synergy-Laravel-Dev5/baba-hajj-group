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
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_user_id')->constrained('app_users')->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->string('type', 50)->default('general'); // booking_confirmation, chat_message, announcement, general
            $table->json('data')->nullable(); // e.g. {"inquiry_id": 1, "package_id": 5}
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['app_user_id', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
    }
};
