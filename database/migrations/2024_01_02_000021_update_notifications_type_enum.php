<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM(
            'invoice_overdue',
            'quote_expiring',
            'debt_due',
            'savings_goal',
            'project_deadline',
            'budget_alert',
            'payment_received',
            'system'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM(
            'invoice_due',
            'invoice_overdue',
            'payment_received',
            'debt_due',
            'system'
        ) NOT NULL");
    }
};
