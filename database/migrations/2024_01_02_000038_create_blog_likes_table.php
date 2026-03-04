<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_post_id')->constrained('blog_posts')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 50)->nullable();
            $table->timestamps();

            $table->index('blog_post_id');
            // unique per user or per IP (enforced in controller)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_likes');
    }
};
