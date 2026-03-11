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
    Schema::create('items', function (Blueprint $table) {
    $table->id('Itemid'); // primary key
    $table->string('name');
    $table->decimal('available', 10, 2)->default(0);
    $table->string('image')->nullable();
    $table->integer('qty')->default(0);
    $table->integer('borrow')->default(0);
    $table->text('description')->nullable();
    $table->tinyInteger('status')->default(1);
    $table->timestamps();
});
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
