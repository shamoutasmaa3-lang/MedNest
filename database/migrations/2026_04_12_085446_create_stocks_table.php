<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('stocks', function (Blueprint $table) {
        $table->id();
        $table->foreignId('medicine_id')->constrained()->onDelete('cascade');
        $table->integer('quantity')->default(0);
        $table->date('expiration_date')->nullable();
        $table->string('location', 100)->nullable(); // موقع التخزين (رف، خزانة...)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
   public function down()
{
    Schema::dropIfExists('stocks');
}
};
