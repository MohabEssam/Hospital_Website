<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('diagnosis_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('dispensed_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('medication_name');
            $table->string('dosage')->nullable();
            $table->string('frequency')->nullable();
            $table->string('duration')->nullable();
            $table->unsignedSmallInteger('quantity')->nullable();
            $table->text('instructions')->nullable();
            $table->string('status')->default('pending')->index();
            $table->timestamp('prescribed_at')->useCurrent()->index();
            $table->timestamp('dispensed_at')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'status', 'prescribed_at']);
            $table->index(['doctor_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
