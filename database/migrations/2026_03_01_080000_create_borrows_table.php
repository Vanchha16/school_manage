<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrows', function (Blueprint $table) {
            $table->id();

            // FK columns (constraints added in later migration)
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('item_id');

            // qty is added in 2026_03_01_091711_add_qty_to_borrows_table

            $table->dateTime('borrow_date');
            $table->date('due_date')->nullable();

            // initially date; later migration converts to dateTime
            $table->date('return_date')->nullable();

            $table->string('status', 20)->default('BORROWED');

            $table->text('notes')->nullable();
            $table->text('return_notes')->nullable();
            $table->string('condition', 50)->nullable();

            $table->timestamps();

            // helpful indexes
            $table->index(['student_id']);
            $table->index(['item_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrows');
    }
};
