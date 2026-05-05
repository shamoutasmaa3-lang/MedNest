<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->string('image_path')->nullable();
            $table->string('digital_signature')->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected', 'dispensed'])->default('pending');
            $table->text('pharmacist_notes')->nullable();
            $table->timestamp('review_date')->nullable();
            $table->json('fhir_data')->nullable(); // ← شلنا after
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
