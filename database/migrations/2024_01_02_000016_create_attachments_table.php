<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->unsignedBigInteger('attachable_id');
            $table->string('attachable_type', 150);
            
            $table->string('file_path', 255);
            $table->string('file_name', 255);
            $table->integer('file_size')->nullable();
            $table->string('mime_type', 150)->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('attachable_id');
            $table->index('attachable_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
