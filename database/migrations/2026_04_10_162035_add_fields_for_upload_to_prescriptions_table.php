<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('prescriptions', 'file')) {
                $table->string('file')->nullable()->after('digital_signature');
            }
        });

        
        DB::statement("ALTER TABLE prescriptions MODIFY COLUMN status ENUM('pending', 'verified', 'rejected', 'dispensed', 'approved') DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn('file');
        });
        
        DB::statement("ALTER TABLE prescriptions MODIFY COLUMN status ENUM('pending', 'verified', 'rejected', 'dispensed') DEFAULT 'pending'");
    }
};