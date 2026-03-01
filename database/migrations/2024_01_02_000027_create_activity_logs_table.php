<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            // The user who performed the action (null = system/guest)
            $table->unsignedBigInteger('user_id')->nullable();
            // Dot-notation action identifier: e.g. admin.user.suspended, user.login
            $table->string('action', 100);
            // Polymorphic subject (what was acted upon)
            $table->string('subject_type', 150)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            // Human-readable log message
            $table->text('description');
            // Extra structured data (old/new values, etc.)
            $table->json('properties')->nullable();
            // Request context
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['subject_type', 'subject_id']);
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
