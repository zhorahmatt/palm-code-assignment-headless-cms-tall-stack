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
        Schema::table('posts', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('published_at');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->unsignedBigInteger('author_id')->nullable()->after('meta_description');

            // Add foreign key constraint for author_id
            $table->foreign('author_id')->references('id')->on('users')->onDelete('set null');

            // Add index for author_id for better query performance
            $table->index('author_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
            $table->dropIndex(['author_id']);
            $table->dropColumn(['meta_title', 'meta_description', 'author_id']);
        });
    }
};
