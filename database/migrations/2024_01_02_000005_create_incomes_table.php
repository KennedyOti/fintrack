<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            
            $table->decimal('amount', 15, 2);
            $table->dateTime('income_date');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'mpesa', 'card', 'paypal', 'other']);
            $table->string('reference_number', 100)->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('user_id');
            $table->index('client_id');
            $table->index('project_id');
            $table->index('income_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
