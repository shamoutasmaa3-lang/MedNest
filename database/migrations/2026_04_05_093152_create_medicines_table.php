<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
   public function up(): void
{
    Schema::create('medicines', function (Blueprint $table) {
        $table->id(); // medicine_id
        $table->string('name', 80);
        $table->text('description')->nullable();
        $table->string('category');
        $table->boolean('requires_prescription')->default(false);
        $table->decimal('price', 10, 2);
        $table->string('active_ingredient')->nullable();
        $table->string('manufacturer')->nullable();
        $table->string('image')->nullable(); 
        $table->timestamps();
    });
}

   public function down()
{
    Schema::dropIfExists('medicines');
}
};
