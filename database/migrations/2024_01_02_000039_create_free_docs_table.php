<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('free_docs', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['invoice', 'quote', 'receipt']);
            $table->string('token', 64)->unique();
            $table->json('document_data');
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['token', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('free_docs');
    }
};
