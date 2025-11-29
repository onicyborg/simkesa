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
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('attendance_id');

            $table->string('recipient_email', 100);
            $table->string('status', 20); // 'success', 'failed'
            $table->text('error_message')->nullable();

            $table->timestamp('sent_at')->nullable();

            $table->timestamps();

            // Foreign key
            $table->foreign('attendance_id')
                ->references('id')->on('attendances')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_logs', function (Blueprint $table) {
            $table->dropForeign(['attendance_id']);
        });

        Schema::dropIfExists('notification_logs');
    }
};
