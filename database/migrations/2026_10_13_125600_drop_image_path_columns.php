<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shopping_lists', function (Blueprint $table) {
            $table->dropColumn('background_path');
        });

        Schema::table('list_items', function (Blueprint $table) {
            $table->dropColumn('photo_path');
        });
    }

    public function down(): void
    {
        Schema::table('shopping_lists', function (Blueprint $table) {
            $table->string('background_path')->nullable()->after('background_preset');
        });

        Schema::table('list_items', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('note');
        });
    }
};
