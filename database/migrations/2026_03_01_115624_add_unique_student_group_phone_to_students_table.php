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
    Schema::table('students', function (Blueprint $table) {
        $table->unique(['student_name', 'group_id', 'phone_number'], 'students_name_group_phone_unique');
    });
}

public function down(): void
{
    Schema::table('students', function (Blueprint $table) {
        $table->dropUnique('students_name_group_phone_unique');
    });
}
};
