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
        Schema::table('list_items', function (Blueprint $table) {
            $table->string('group_name', 120)->nullable()->after('name');
            $table->index(['shopping_list_id', 'group_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('list_items', function (Blueprint $table) {
            $table->dropIndex(['shopping_list_id', 'group_name']);
            $table->dropColumn('group_name');
        });
    }
};
