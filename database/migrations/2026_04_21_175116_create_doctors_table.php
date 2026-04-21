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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('doctor_code')->unique();
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('specialty');
            $table->text('biography')->nullable();
            $table->text('address')->nullable();
            $table->string('availability_status')->default('available')->index();
            $table->decimal('consultation_fee', 10, 2)->default(0);
            $table->string('avatar_path')->nullable();
            $table->unsignedTinyInteger('years_of_experience')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
