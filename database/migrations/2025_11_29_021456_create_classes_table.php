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
        Schema::create('classes', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name', 50); // contoh: X RPL 1

            $table->uuid('batch_id');
            $table->uuid('homeroom_teacher_id')->nullable(); // wali kelas (user guru)

            $table->timestamps();

            // Foreign keys
            $table->foreign('batch_id')
                ->references('id')->on('batches')
                ->cascadeOnDelete();

            $table->foreign('homeroom_teacher_id')
                ->references('id')->on('users')
                ->nullOnDelete(); // kalau gurunya dihapus, set null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropForeign(['homeroom_teacher_id']);
        });

        Schema::dropIfExists('classes');
    }
};
