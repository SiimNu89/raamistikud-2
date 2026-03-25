<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
            $table->dropColumn(['author_id', 'content', 'published']);
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('author_id')->nullable()->after('id');
            $table->text('content')->nullable()->after('description');
            $table->boolean('published')->default(true)->after('content');
        });
    }
};
