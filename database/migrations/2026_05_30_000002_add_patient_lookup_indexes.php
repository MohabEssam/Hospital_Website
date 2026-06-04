<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table): void {
            $indexes = collect(Schema::getIndexes('patients'))->pluck('name')->toArray();

            if (! in_array('patients_name_index', $indexes, true)) {
                $table->index('name', 'patients_name_index');
            }

            if (! in_array('patients_phone_index', $indexes, true)) {
                if (Schema::getConnection()->getDriverName() === 'mysql') {
                    DB::statement('ALTER TABLE patients ADD INDEX patients_phone_index (phone(30))');

                    return;
                }

                $table->index('phone', 'patients_phone_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table): void {
            $indexes = collect(Schema::getIndexes('patients'))->pluck('name')->toArray();

            if (in_array('patients_name_index', $indexes, true)) {
                $table->dropIndex('patients_name_index');
            }

            if (in_array('patients_phone_index', $indexes, true)) {
                $table->dropIndex('patients_phone_index');
            }
        });
    }
};
