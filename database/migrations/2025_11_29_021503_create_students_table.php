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
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('user_id')->nullable(); // akun login (role=student)
            $table->string('nis', 50)->unique();
            $table->string('full_name', 100);

            $table->uuid('class_id');

            $table->string('parent_name', 100);
            $table->string('parent_email', 100);

            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->nullOnDelete();

            $table->foreign('class_id')
                ->references('id')->on('classes')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['class_id']);
        });

        Schema::dropIfExists('students');
    }
};
