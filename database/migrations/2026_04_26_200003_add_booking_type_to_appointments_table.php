<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (! Schema::hasColumn('appointments', 'type')) {
                $table->string('type')->nullable()->after('status')->index();
            }
            if (! Schema::hasColumn('appointments', 'reference_id')) {
                $table->unsignedBigInteger('reference_id')->nullable()->after('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex(['type', 'reference_id']);
            $table->dropIndex(['type']);
            $table->dropColumn(['type', 'reference_id']);
        });
    }
};
