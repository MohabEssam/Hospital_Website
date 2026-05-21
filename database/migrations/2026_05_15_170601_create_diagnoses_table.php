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
        Schema::create('diagnoses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('summary')->nullable();
            $table->text('symptoms')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('active')->index();
            $table->timestamp('diagnosed_at')->useCurrent()->index();
            $table->timestamps();

            $table->index(['patient_id', 'diagnosed_at']);
            $table->index(['doctor_id', 'diagnosed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnoses');
    }
};
