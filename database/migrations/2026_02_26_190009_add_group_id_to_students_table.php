<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * NOTE:
         * group_id + its FK are already created in:
         * 2026_02_26_170542_alter_students_add_group_id_drop_group_name.php
         *
         * This migration is kept only to preserve history, but it must be a no-op
         * to avoid duplicate column / duplicate FK errors.
         */
    }

    public function down(): void
    {
        // no-op (see note in up())
    }
};