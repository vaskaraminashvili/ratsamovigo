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
        Schema::table('users', function (Blueprint $table) {
            $table->string('plan')->default('free')->after('password');
        });

        Schema::create('shopping_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('emoji', 16)->nullable();
            $table->string('accent_color', 7)->default('#059669');
            $table->string('background_preset', 32)->default('sage');
            $table->string('background_path')->nullable();
            $table->string('checkbox_style', 16)->default('box');
            $table->boolean('show_emoji')->default(true);
            $table->boolean('show_quantity')->default(true);
            $table->boolean('show_likes')->default(true);
            $table->boolean('show_author')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
        });

        Schema::create('list_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shopping_list_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 16);
            $table->timestamps();

            $table->unique(['shopping_list_id', 'user_id']);
        });

        Schema::create('share_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shopping_list_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->string('role', 16);
            $table->timestamps();

            $table->index(['shopping_list_id', 'role']);
        });

        Schema::create('list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shopping_list_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('claimed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('quantity', 40)->nullable();
            $table->string('emoji', 16)->nullable();
            $table->string('note', 200)->nullable();
            $table->string('photo_path')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_checked')->default(false);
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['shopping_list_id', 'is_checked']);
        });

        Schema::create('item_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('list_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['list_item_id', 'user_id']);
        });

        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shopping_list_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('question');
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('shopping_list_id');
        });

        Schema::create('poll_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->cascadeOnDelete();
            $table->foreignId('poll_option_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['poll_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poll_votes');
        Schema::dropIfExists('poll_options');
        Schema::dropIfExists('polls');
        Schema::dropIfExists('item_likes');
        Schema::dropIfExists('list_items');
        Schema::dropIfExists('share_links');
        Schema::dropIfExists('list_members');
        Schema::dropIfExists('shopping_lists');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('plan');
        });
    }
};
