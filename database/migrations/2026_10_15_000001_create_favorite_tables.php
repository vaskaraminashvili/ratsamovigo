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
        Schema::create('favorite_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('quantity', 40)->nullable();
            $table->string('emoji', 16)->nullable();
            $table->string('note', 200)->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('favorite_dishes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('emoji', 16)->nullable();
            $table->string('note', 200)->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('favorite_dish_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('favorite_dish_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('quantity', 40)->nullable();
            $table->string('emoji', 16)->nullable();
            $table->string('note', 200)->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorite_dish_items');
        Schema::dropIfExists('favorite_dishes');
        Schema::dropIfExists('favorite_items');
    }
};
