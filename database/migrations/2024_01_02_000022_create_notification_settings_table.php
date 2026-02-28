<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Invoice overdue
            $table->boolean('invoice_overdue_app')->default(true);
            $table->boolean('invoice_overdue_email')->default(true);

            // Quote expiring
            $table->boolean('quote_expiring_app')->default(true);
            $table->boolean('quote_expiring_email')->default(true);
            $table->unsignedTinyInteger('quote_expiring_days')->default(3);

            // Debt due
            $table->boolean('debt_due_app')->default(true);
            $table->boolean('debt_due_email')->default(true);
            $table->unsignedTinyInteger('debt_due_days')->default(3);

            // Savings goal
            $table->boolean('savings_goal_app')->default(true);
            $table->boolean('savings_goal_email')->default(true);

            // Project deadline
            $table->boolean('project_deadline_app')->default(true);
            $table->boolean('project_deadline_email')->default(true);
            $table->unsignedTinyInteger('project_deadline_days')->default(7);

            // Budget alert
            $table->boolean('budget_alert_app')->default(true);
            $table->boolean('budget_alert_email')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
