<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("
            CREATE UNIQUE INDEX appointments_doctor_active_slot_unique
            ON appointments (
                doctor_id,
                appointment_date,
                start_time,
                ((CASE WHEN status <> 'cancelled' THEN 1 ELSE NULL END))
            )
        ");
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('DROP INDEX appointments_doctor_active_slot_unique ON appointments');
    }
};
