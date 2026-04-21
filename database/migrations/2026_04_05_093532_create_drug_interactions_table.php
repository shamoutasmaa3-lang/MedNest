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
    Schema::create('drug_interactions', function (Blueprint $table) {
        $table->id(); // interaction_id
        $table->foreignId('medicine_id_1')->constrained('medicines')->onDelete('cascade');
        $table->foreignId('medicine_id_2')->constrained('medicines')->onDelete('cascade');
        $table->enum('severity', ['low', 'moderate', 'high', 'severe']);
        $table->text('description')->nullable();
        $table->timestamps();
    });
}
    public function down()
{
    Schema::dropIfExists('drug_interactions');
}
};
