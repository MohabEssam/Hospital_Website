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
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('job_batches');

        if (Schema::hasTable('appointments')) {
            Schema::table('appointments', function (Blueprint $table): void {
                if ($this->indexExists('appointments', 'appointments_type_index')) {
                    $table->dropIndex('appointments_type_index');
                }

                $columns = array_values(array_filter(
                    ['type', 'reference_id'],
                    fn (string $column): bool => Schema::hasColumn('appointments', $column),
                ));

                if ($columns !== []) {
                    $table->dropColumn($columns);
                }
            });
        }

        $this->addIndexIfMissing('departments', ['name'], 'departments_name_index');
        $this->addIndexIfMissing('departments', ['is_active', 'name'], 'departments_active_name_index');

        $this->addIndexIfMissing('doctors', ['name'], 'doctors_name_index');
        $this->addIndexIfMissing('doctors', ['created_at'], 'doctors_created_at_index');
        $this->addIndexIfMissing('doctors', ['availability_status', 'name'], 'doctors_availability_name_index');
        $this->addIndexIfMissing('doctors', ['department_id', 'name'], 'doctors_department_name_index');
        $this->addIndexIfMissing('doctors', ['department_id', 'availability_status', 'name'], 'doctors_department_availability_name_index');

        $this->addIndexIfMissing('patients', ['name'], 'patients_name_index');
        $this->addIndexIfMissing('patients', ['check_in_date'], 'patients_check_in_date_index');
        $this->addIndexIfMissing('patients', ['created_at'], 'patients_created_at_index');
        $this->addIndexIfMissing('patients', ['date_of_birth'], 'patients_date_of_birth_index');
        $this->addIndexIfMissing('patients', ['doctor_id', 'status'], 'patients_doctor_status_index');

        $this->addIndexIfMissing('appointments', ['appointment_date', 'start_time'], 'appointments_date_time_index');
        $this->addIndexIfMissing('appointments', ['patient_id', 'appointment_date', 'start_time'], 'appointments_patient_date_time_index');
        $this->addIndexIfMissing('appointments', ['status', 'appointment_date'], 'appointments_status_date_index');
        $this->addIndexIfMissing('appointments', ['phone_number', 'appointment_date', 'start_time'], 'appointments_phone_date_time_index');
        $this->addIndexIfMissing('appointments', ['department_id', 'appointment_date', 'start_time'], 'appointments_department_date_time_index');

        $this->addIndexIfMissing('doctor_schedules', ['doctor_id', 'day_of_week', 'start_time', 'is_available'], 'doctor_schedules_slot_lookup_index');

        $this->addIndexIfMissing('patient_care_services', ['is_active', 'sort_order'], 'patient_care_services_active_sort_index');
        $this->addIndexIfMissing('patient_care_services', ['is_bookable', 'is_active'], 'patient_care_services_bookable_active_index');

        $this->addIndexIfMissing('service_bookings', ['booking_date', 'booking_time'], 'service_bookings_date_time_index');
        $this->addIndexIfMissing('service_bookings', ['status', 'booking_date', 'booking_time'], 'service_bookings_status_date_time_index');

        $this->convertFixedStringsToEnums();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->convertEnumsToStrings();

        $this->dropIndexIfExists('service_bookings', 'service_bookings_status_date_time_index');
        $this->dropIndexIfExists('service_bookings', 'service_bookings_date_time_index');

        $this->dropIndexIfExists('patient_care_services', 'patient_care_services_bookable_active_index');
        $this->dropIndexIfExists('patient_care_services', 'patient_care_services_active_sort_index');

        $this->dropIndexIfExists('doctor_schedules', 'doctor_schedules_slot_lookup_index');

        $this->dropIndexIfExists('appointments', 'appointments_department_date_time_index');
        $this->dropIndexIfExists('appointments', 'appointments_phone_date_time_index');
        $this->dropIndexIfExists('appointments', 'appointments_status_date_index');
        $this->dropIndexIfExists('appointments', 'appointments_patient_date_time_index');
        $this->dropIndexIfExists('appointments', 'appointments_date_time_index');

        $this->dropIndexIfExists('patients', 'patients_doctor_status_index');
        $this->dropIndexIfExists('patients', 'patients_date_of_birth_index');
        $this->dropIndexIfExists('patients', 'patients_created_at_index');
        $this->dropIndexIfExists('patients', 'patients_check_in_date_index');
        $this->dropIndexIfExists('patients', 'patients_name_index');

        $this->dropIndexIfExists('doctors', 'doctors_department_availability_name_index');
        $this->dropIndexIfExists('doctors', 'doctors_department_name_index');
        $this->dropIndexIfExists('doctors', 'doctors_availability_name_index');
        $this->dropIndexIfExists('doctors', 'doctors_created_at_index');
        $this->dropIndexIfExists('doctors', 'doctors_name_index');

        $this->dropIndexIfExists('departments', 'departments_active_name_index');
        $this->dropIndexIfExists('departments', 'departments_name_index');

        if (Schema::hasTable('appointments')) {
            Schema::table('appointments', function (Blueprint $table): void {
                if (! Schema::hasColumn('appointments', 'type')) {
                    $table->string('type')->nullable()->after('status')->index();
                }

                if (! Schema::hasColumn('appointments', 'reference_id')) {
                    $table->unsignedBigInteger('reference_id')->nullable()->after('type');
                }
            });
        }

        if (! Schema::hasTable('job_batches')) {
            Schema::create('job_batches', function (Blueprint $table): void {
                $table->string('id')->primary();
                $table->string('name');
                $table->integer('total_jobs');
                $table->integer('pending_jobs');
                $table->integer('failed_jobs');
                $table->longText('failed_job_ids');
                $table->mediumText('options')->nullable();
                $table->integer('cancelled_at')->nullable();
                $table->integer('created_at');
                $table->integer('finished_at')->nullable();
            });
        }

        if (! Schema::hasTable('cache_locks')) {
            Schema::create('cache_locks', function (Blueprint $table): void {
                $table->string('key')->primary();
                $table->string('owner');
                $table->bigInteger('expiration')->index();
            });
        }
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function addIndexIfMissing(string $tableName, array $columns, string $indexName): void
    {
        if (! Schema::hasTable($tableName) || $this->indexExists($tableName, $indexName)) {
            return;
        }

        foreach ($columns as $column) {
            if (! Schema::hasColumn($tableName, $column)) {
                return;
            }
        }

        Schema::table($tableName, function (Blueprint $table) use ($columns, $indexName): void {
            $table->index($columns, $indexName);
        });
    }

    private function dropIndexIfExists(string $tableName, string $indexName): void
    {
        if (! Schema::hasTable($tableName) || ! $this->indexExists($tableName, $indexName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($indexName): void {
            $table->dropIndex($indexName);
        });
    }

    private function indexExists(string $tableName, string $indexName): bool
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return false;
        }

        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::raw('DATABASE()'))
            ->where('table_name', $tableName)
            ->where('index_name', $indexName)
            ->exists();
    }

    private function convertFixedStringsToEnums(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'doctor', 'patient') NOT NULL DEFAULT 'patient'");
        DB::statement("ALTER TABLE doctors MODIFY availability_status ENUM('available', 'unavailable') NOT NULL DEFAULT 'available'");
        DB::statement("ALTER TABLE patients MODIFY status ENUM('active', 'new_patient', 'inactive') NOT NULL DEFAULT 'active'");
        DB::statement("ALTER TABLE appointments MODIFY status ENUM('confirmed', 'pending', 'cancelled') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE service_bookings MODIFY status ENUM('pending', 'confirmed', 'rejected') NOT NULL DEFAULT 'pending'");
    }

    private function convertEnumsToStrings(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE service_bookings MODIFY status VARCHAR(255) NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE appointments MODIFY status VARCHAR(255) NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE patients MODIFY status VARCHAR(255) NOT NULL DEFAULT 'active'");
        DB::statement("ALTER TABLE doctors MODIFY availability_status VARCHAR(255) NOT NULL DEFAULT 'available'");
        DB::statement("ALTER TABLE users MODIFY role VARCHAR(255) NOT NULL DEFAULT 'patient'");
    }
};
