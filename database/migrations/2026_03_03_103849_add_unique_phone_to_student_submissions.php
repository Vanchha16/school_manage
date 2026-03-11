<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_submissions', function (Blueprint $table) {
            $table->unique('phone_number', 'submissions_phone_number_unique');
        });
    }

    public function down(): void
    {
        Schema::table('student_submissions', function (Blueprint $table) {
            $table->dropUnique('submissions_phone_number_unique');
        });
    }
};
