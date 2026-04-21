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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->date('appointment_date')->index();
            $table->time('start_time');
            $table->time('end_time');
            $table->string('status')->default('pending')->index();
            $table->string('treatment');
            $table->text('notes')->nullable();
            $table->decimal('fee', 10, 2)->default(0);
            $table->timestamps();

            $table->index(['doctor_id', 'appointment_date']);
            $table->index(['doctor_id', 'appointment_date', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
