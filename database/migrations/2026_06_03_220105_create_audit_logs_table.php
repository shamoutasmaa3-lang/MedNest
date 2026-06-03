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
    Schema::create('audit_logs', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->string('action');
        $table->unsignedBigInteger('prescription_id')->nullable();
        $table->string('result')->nullable();
        $table->timestamp('created_at')->nullable();

        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('prescription_id')->references('id')->on('prescriptions')->onDelete('cascade');
    });
}

public function down()
{
    Schema::dropIfExists('audit_logs');
}

};
