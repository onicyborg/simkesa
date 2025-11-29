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
        Schema::create('attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('student_id');
            $table->date('attendance_date');

            $table->string('status', 10); // 'H', 'S', 'I', 'A', dll
            $table->text('remark')->nullable();

            $table->uuid('recorded_by'); // user guru/admin yang input

            $table->timestamps();

            // Unik: 1 siswa 1 record per hari
            $table->unique(['student_id', 'attendance_date']);

            // Foreign keys
            $table->foreign('student_id')
                ->references('id')->on('students')
                ->cascadeOnDelete();

            $table->foreign('recorded_by')
                ->references('id')->on('users')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropUnique(['student_id', 'attendance_date']);
            $table->dropForeign(['student_id']);
            $table->dropForeign(['recorded_by']);
        });

        Schema::dropIfExists('attendances');
    }
};
