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
            // If you had group_name before, remove it.
            if (Schema::hasColumn('students', 'group_name')) {
                $table->dropColumn('group_name');
            }

            // Add group_id only once.
            if (!Schema::hasColumn('students', 'group_id')) {
                $table->unsignedBigInteger('group_id')->nullable()->after('phone_number');
            }

            // Create the FK once (this is the single migration that owns it).
            $table->foreign('group_id')
                ->references('group_id')
                ->on('groups')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'group_id')) {
                // dropForeign will throw if it doesn't exist, so guard it.
                try {
                    $table->dropForeign(['group_id']);
                } catch (\Throwable $e) {
                    // ignore
                }
                $table->dropColumn('group_id');
            }

            if (!Schema::hasColumn('students', 'group_name')) {
                $table->string('group_name')->nullable();
            }
        });
    }
};
