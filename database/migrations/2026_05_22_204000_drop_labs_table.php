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
        Schema::dropIfExists('labs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('labs', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('work_hours')->nullable();
            $table->string('image')->nullable();
            $table->json('xrays')->nullable();
            $table->json('medical_tests')->nullable();
            $table->timestamps();
        });
    }
};
