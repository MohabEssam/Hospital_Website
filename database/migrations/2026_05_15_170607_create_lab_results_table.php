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
        Schema::create('lab_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_request_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lab_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('entered_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->longText('result_text')->nullable();
            $table->json('file_paths')->nullable();
            $table->string('status')->default('final')->index();
            $table->timestamp('resulted_at')->useCurrent()->index();
            $table->timestamps();

            $table->index(['patient_id', 'resulted_at']);
            $table->index(['doctor_id', 'resulted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_results');
    }
};
