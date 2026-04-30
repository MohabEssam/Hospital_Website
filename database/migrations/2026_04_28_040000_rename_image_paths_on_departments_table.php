<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->renameColumn('icon_path', 'icon');
            $table->renameColumn('hero_image_path', 'hero_image');
            $table->renameColumn('sidebar_image_path', 'sidebar_image');
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->renameColumn('icon', 'icon_path');
            $table->renameColumn('hero_image', 'hero_image_path');
            $table->renameColumn('sidebar_image', 'sidebar_image_path');
        });
    }
};
