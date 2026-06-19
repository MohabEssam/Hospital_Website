<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Convert all pending/rejected appointments to confirmed
        DB::table('appointments')
            ->whereIn('status', ['pending', 'rejected'])
            ->update([
                'status' => 'confirmed',
                'updated_at' => now(),
            ]);

        Schema::table('appointments', function (Blueprint $table) {
            if (! Schema::hasColumn('appointments', 'auto_confirmed_at')) {
                $table->timestamp('auto_confirmed_at')->nullable()->after('confirmation_email_sent_at');
            }
        });

        // Set auto_confirmed_at for all existing confirmed appointments that were just converted
        DB::table('appointments')
            ->where('status', 'confirmed')
            ->whereNull('auto_confirmed_at')
            ->update(['auto_confirmed_at' => now()]);

        // Update the unique index to only consider confirmed status (pending no longer exists)
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            $indexName = 'appointments_doctor_active_slot_unique';

            $index = DB::selectOne(
                'select count(*) as count from information_schema.statistics where table_schema = database() and table_name = ? and index_name = ?',
                ['appointments', $indexName],
            );

            if ((int) ($index->count ?? 0) > 0) {
                DB::statement('DROP INDEX '.$indexName.' ON appointments');
            }

            DB::statement("
                CREATE UNIQUE INDEX {$indexName}
                ON appointments (
                    doctor_id,
                    appointment_date,
                    start_time,
                    ((CASE WHEN status = 'confirmed' THEN 1 ELSE NULL END))
                )
            ");
        }
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'auto_confirmed_at')) {
                $table->dropColumn('auto_confirmed_at');
            }
        });
    }
};
