<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $needRole   = !Schema::hasColumn('users', 'role');
        $needStatus = !Schema::hasColumn('users', 'status');

        if ($needRole || $needStatus) {
            Schema::table('users', function (Blueprint $table) use ($needRole, $needStatus) {

                if ($needRole) {
                    // default role = student (roles: admin, staff, student)
                    $table->string('role')->default('student')->after('password');
                }

                if ($needStatus) {
                    // 1 = active, 0 = inactive
                    $table->tinyInteger('status')->default(1)->after('role');
                }
            });
        }

        // Backfill old users if role exists but null
        if (Schema::hasColumn('users', 'role')) {
            DB::table('users')->whereNull('role')->update(['role' => 'student']);
        }
    }

    public function down(): void
    {
        // ✅ safer rollback
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};