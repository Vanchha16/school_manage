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
                $table->id('student_id');              // instead of "student id"
                $table->string('student_name');        // instead of "student name"
                $table->string('phone_number')->nullable(); // instead of "phone number"
                $table->string('group_name')->nullable();   // instead of "group" (reserved word)
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
