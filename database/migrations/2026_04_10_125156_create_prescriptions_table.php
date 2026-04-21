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
    Schema::create('prescriptions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
        $table->string('image_path')->nullable(); // صورة الوصفة إن وجدت
        $table->string('digital_signature')->nullable();
        $table->enum('status', ['pending', 'verified', 'rejected', 'dispensed'])->default('pending');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
