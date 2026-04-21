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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('patient_code')->unique();
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->date('check_in_date')->nullable();
            $table->string('treatment')->nullable()->index();
            $table->string('room_number')->nullable();
            $table->string('status')->default('active')->index();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->string('avatar_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
