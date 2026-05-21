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
        Schema::create('lab_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('diagnosis_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('lab_id')->nullable()->constrained()->nullOnDelete();
            $table->string('test_name');
            $table->string('specimen')->nullable();
            $table->string('priority')->default('routine')->index();
            $table->text('instructions')->nullable();
            $table->string('status')->default('pending')->index();
            $table->timestamp('requested_at')->useCurrent()->index();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'status', 'requested_at']);
            $table->index(['doctor_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_requests');
    }
};
