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
        Schema::create('scan_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('diagnosis_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('scan_type');
            $table->string('body_area')->nullable();
            $table->boolean('contrast_required')->default(false);
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
        Schema::dropIfExists('scan_requests');
    }
};
