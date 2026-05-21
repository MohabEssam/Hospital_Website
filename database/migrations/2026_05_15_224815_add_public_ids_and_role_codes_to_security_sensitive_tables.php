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
        Schema::table('users', function (Blueprint $table) {
            $table->string('public_id')->nullable()->unique()->after('id');
        });

        foreach (['lab_staff' => 'lab', 'scan_staff' => 'scan_center'] as $from => $to) {
            DB::table('users')->where('role', $from)->update(['role' => $to]);
        }

        $this->backfillUsers();

        Schema::table('lab_requests', function (Blueprint $table) {
            $table->string('public_id')->nullable()->unique()->after('id');
        });
        Schema::table('lab_results', function (Blueprint $table) {
            $table->string('public_id')->nullable()->unique()->after('id');
        });
        Schema::table('scan_requests', function (Blueprint $table) {
            $table->string('public_id')->nullable()->unique()->after('id');
        });
        Schema::table('scan_results', function (Blueprint $table) {
            $table->string('public_id')->nullable()->unique()->after('id');
        });
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->string('public_id')->nullable()->unique()->after('id');
        });

        $this->backfillPublicIds('lab_requests', 'LABREQ');
        $this->backfillPublicIds('lab_results', 'LABRES');
        $this->backfillPublicIds('scan_requests', 'SCANREQ');
        $this->backfillPublicIds('scan_results', 'SCANRES');
        $this->backfillPublicIds('prescriptions', 'RX');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['prescriptions', 'scan_results', 'scan_requests', 'lab_results', 'lab_requests'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                $table->dropUnique($tableName.'_public_id_unique');
                $table->dropColumn('public_id');
            });
        }

        DB::table('users')->where('role', 'lab')->update(['role' => 'lab_staff']);
        DB::table('users')->where('role', 'scan_center')->update(['role' => 'scan_staff']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_public_id_unique');
            $table->dropColumn('public_id');
        });
    }

    private function backfillUsers(): void
    {
        $counters = [];

        DB::table('users')
            ->select(['id', 'role'])
            ->orderBy('id')
            ->get()
            ->each(function (object $user) use (&$counters): void {
                $prefix = $this->prefixForRole((string) $user->role);
                $counters[$prefix] = ($counters[$prefix] ?? 0) + 1;

                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['public_id' => "{$prefix}-{$counters[$prefix]}"]);
            });
    }

    private function backfillPublicIds(string $table, string $prefix): void
    {
        DB::table($table)
            ->select(['id'])
            ->orderBy('id')
            ->get()
            ->each(function (object $row, int $index) use ($table, $prefix): void {
                DB::table($table)
                    ->where('id', $row->id)
                    ->update(['public_id' => $prefix.'-'.($index + 1)]);
            });
    }

    private function prefixForRole(string $role): string
    {
        return match ($role) {
            'admin' => 'ADM',
            'doctor' => 'DR',
            'patient' => 'PAT',
            'lab', 'lab_staff' => 'LAB',
            'pharmacy' => 'PH',
            'scan_center', 'scan_staff' => 'SCAN',
            default => 'USR',
        };
    }
};
