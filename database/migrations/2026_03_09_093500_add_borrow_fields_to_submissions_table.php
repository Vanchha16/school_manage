<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('student_submissions')) {
            return;
        }

        Schema::table('student_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('student_submissions', 'item_id')) {
                $table->unsignedBigInteger('item_id')->nullable()->after('group_id');
            }

            if (!Schema::hasColumn('student_submissions', 'qty')) {
                $table->integer('qty')->default(1)->after('item_id');
            }

            if (!Schema::hasColumn('student_submissions', 'status')) {
                $table->string('status')->default('BORROWED')->after('qty');
            }

            if (!Schema::hasColumn('student_submissions', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }

            if (!Schema::hasColumn('student_submissions', 'due_date')) {
                $table->date('due_date')->nullable()->after('notes');
            }

            if (!Schema::hasColumn('student_submissions', 'student_id')) {
                $table->unsignedBigInteger('student_id')->nullable()->after('due_date');
            }

            if (!Schema::hasColumn('student_submissions', 'is_student_existing')) {
                $table->boolean('is_student_existing')->default(false)->after('student_id');
            }

            if (!Schema::hasColumn('student_submissions', 'is_student_added')) {
                $table->boolean('is_student_added')->default(false)->after('is_student_existing');
            }

            if (!Schema::hasColumn('student_submissions', 'is_borrow_approved')) {
                $table->boolean('is_borrow_approved')->default(false)->after('is_student_added');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('student_submissions')) {
            return;
        }

        Schema::table('student_submissions', function (Blueprint $table) {
            $columns = [
                'item_id',
                'qty',
                'status',
                'notes',
                'due_date',
                'student_id',
                'is_student_existing',
                'is_student_added',
                'is_borrow_approved',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('student_submissions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};