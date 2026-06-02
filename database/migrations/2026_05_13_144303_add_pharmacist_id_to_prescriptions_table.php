<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('prescriptions', 'pharmacist_id')) {
            Schema::table('prescriptions', function (Blueprint $table) {
                $table->foreignId('pharmacist_id')->nullable()->after('patient_id')->constrained('users')->nullOnDelete();
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('prescriptions', 'pharmacist_id')) {
            Schema::table('prescriptions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('pharmacist_id');
            });
        }
    }
};
