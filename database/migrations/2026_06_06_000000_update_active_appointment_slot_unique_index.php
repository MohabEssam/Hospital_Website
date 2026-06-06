<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const INDEX_NAME = 'appointments_doctor_active_slot_unique';

    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        $this->dropIndexIfExists();

        DB::statement("
            CREATE UNIQUE INDEX ".self::INDEX_NAME."
            ON appointments (
                doctor_id,
                appointment_date,
                start_time,
                ((CASE WHEN status IN ('pending', 'confirmed') THEN 1 ELSE NULL END))
            )
        ");
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        $this->dropIndexIfExists();

        DB::statement("
            CREATE UNIQUE INDEX ".self::INDEX_NAME."
            ON appointments (
                doctor_id,
                appointment_date,
                start_time,
                ((CASE WHEN status <> 'cancelled' THEN 1 ELSE NULL END))
            )
        ");
    }

    private function dropIndexIfExists(): void
    {
        $index = DB::selectOne(
            'select count(*) as count from information_schema.statistics where table_schema = database() and table_name = ? and index_name = ?',
            ['appointments', self::INDEX_NAME],
        );

        if ((int) ($index->count ?? 0) > 0) {
            DB::statement('DROP INDEX '.self::INDEX_NAME.' ON appointments');
        }
    }
};
