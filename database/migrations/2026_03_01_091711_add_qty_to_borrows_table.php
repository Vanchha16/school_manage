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
    Schema::table('borrows', function (Blueprint $table) {
        $table->unsignedInteger('qty')->default(1)->after('item_id');
    });
}

public function down(): void
{
    Schema::table('borrows', function (Blueprint $table) {
        $table->dropColumn('qty');
    });
}
};
