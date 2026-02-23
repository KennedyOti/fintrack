<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->decimal('target_amount', 15, 2)->nullable();
            $table->decimal('current_balance', 15, 2)->default(0);
            
            $table->enum('status', ['active', 'archived'])->default('active');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_accounts');
    }
};
