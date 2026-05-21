<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('department_id')
                ->nullable()
                ->after('doctor_id')
                ->constrained()
                ->nullOnDelete();
            $table->index(['department_id', 'appointment_date']);
        });

        DB::table('appointments')
            ->whereNull('department_id')
            ->update([
                'department_id' => DB::raw('(select department_id from doctors where doctors.id = appointments.doctor_id)'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex('appointments_department_id_appointment_date_index');
            $table->dropConstrainedForeignId('department_id');
        });
    }
};
