<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'invoice_overdue_app',
        'invoice_overdue_email',
        'quote_expiring_app',
        'quote_expiring_email',
        'quote_expiring_days',
        'debt_due_app',
        'debt_due_email',
        'debt_due_days',
        'savings_goal_app',
        'savings_goal_email',
        'project_deadline_app',
        'project_deadline_email',
        'project_deadline_days',
        'budget_alert_app',
        'budget_alert_email',
    ];

    protected $casts = [
        'invoice_overdue_app'    => 'boolean',
        'invoice_overdue_email'  => 'boolean',
        'quote_expiring_app'     => 'boolean',
        'quote_expiring_email'   => 'boolean',
        'quote_expiring_days'    => 'integer',
        'debt_due_app'           => 'boolean',
        'debt_due_email'         => 'boolean',
        'debt_due_days'          => 'integer',
        'savings_goal_app'       => 'boolean',
        'savings_goal_email'     => 'boolean',
        'project_deadline_app'   => 'boolean',
        'project_deadline_email' => 'boolean',
        'project_deadline_days'  => 'integer',
        'budget_alert_app'       => 'boolean',
        'budget_alert_email'     => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return defaults array for firstOrCreate.
     */
    public static function defaults(): array
    {
        return [
            'invoice_overdue_app'    => true,
            'invoice_overdue_email'  => true,
            'quote_expiring_app'     => true,
            'quote_expiring_email'   => true,
            'quote_expiring_days'    => 3,
            'debt_due_app'           => true,
            'debt_due_email'         => true,
            'debt_due_days'          => 3,
            'savings_goal_app'       => true,
            'savings_goal_email'     => true,
            'project_deadline_app'   => true,
            'project_deadline_email' => true,
            'project_deadline_days'  => 7,
            'budget_alert_app'       => true,
            'budget_alert_email'     => false,
        ];
    }
}
