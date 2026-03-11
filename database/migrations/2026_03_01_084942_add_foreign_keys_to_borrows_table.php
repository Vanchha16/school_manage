<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $db = DB::getDatabaseName();

        // Drop FK on student_id if exists
        $studentFk = DB::selectOne("
            SELECT CONSTRAINT_NAME as name
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = 'borrows'
              AND COLUMN_NAME = 'student_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
            LIMIT 1
        ", [$db]);

        if ($studentFk) {
            DB::statement("ALTER TABLE borrows DROP FOREIGN KEY `{$studentFk->name}`");
        }

        // Drop FK on item_id if exists
        $itemFk = DB::selectOne("
            SELECT CONSTRAINT_NAME as name
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = 'borrows'
              AND COLUMN_NAME = 'item_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
            LIMIT 1
        ", [$db]);

        if ($itemFk) {
            DB::statement("ALTER TABLE borrows DROP FOREIGN KEY `{$itemFk->name}`");
        }

        // Add FK constraints
        Schema::table('borrows', function (Blueprint $table) {
            $table->foreign('student_id')
                ->references('student_id')->on('students')
                ->cascadeOnUpdate()->cascadeOnDelete();

            // items PK is Itemid
            $table->foreign('item_id')
                ->references('Itemid')->on('items')
                ->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        $db = DB::getDatabaseName();

        $studentFk = DB::selectOne("
            SELECT CONSTRAINT_NAME as name
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = 'borrows'
              AND COLUMN_NAME = 'student_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
            LIMIT 1
        ", [$db]);

        if ($studentFk) {
            DB::statement("ALTER TABLE borrows DROP FOREIGN KEY `{$studentFk->name}`");
        }

        $itemFk = DB::selectOne("
            SELECT CONSTRAINT_NAME as name
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = 'borrows'
              AND COLUMN_NAME = 'item_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
            LIMIT 1
        ", [$db]);

        if ($itemFk) {
            DB::statement("ALTER TABLE borrows DROP FOREIGN KEY `{$itemFk->name}`");
        }
    }
};