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
        Schema::table('comments', function (Blueprint $table) {
            // Modify the existing foreign key with onDelete('cascade')
            $table->dropForeign(['article_id']);
            $table->foreign('article_id')
                ->references('id')
                ->on('articles')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the modification in the reverse migration if needed
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['article_id']);
            $table->foreign('article_id')
                ->references('id')
                ->on('articles');
        });
    }
};
