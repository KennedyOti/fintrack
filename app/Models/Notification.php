<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'is_read',
        'read_at',
        'related_id',
        'related_type',
    ];

    protected $casts = [
        'is_read'    => 'boolean',
        'read_at'    => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    // Icon for each notification type
    public function getIcon(): string
    {
        return match ($this->type) {
            'invoice_overdue'   => 'fas fa-file-invoice-dollar',
            'quote_expiring'    => 'fas fa-file-contract',
            'debt_due'          => 'fas fa-handshake',
            'savings_goal'      => 'fas fa-piggy-bank',
            'project_deadline'  => 'fas fa-diagram-project',
            'budget_alert'      => 'fas fa-chart-pie',
            'payment_received'  => 'fas fa-circle-check',
            default             => 'fas fa-bell',
        };
    }

    // Icon color for each notification type
    public function getIconColor(): string
    {
        return match ($this->type) {
            'invoice_overdue'   => '#F43F5E',
            'quote_expiring'    => '#F59E0B',
            'debt_due'          => '#F97316',
            'savings_goal'      => '#22C55E',
            'project_deadline'  => '#8B5CF6',
            'budget_alert'      => '#EF4444',
            'payment_received'  => '#10B981',
            default             => '#0E7490',
        };
    }

    // Background color for the icon bubble
    public function getIconBg(): string
    {
        return match ($this->type) {
            'invoice_overdue'   => 'rgba(244, 63, 94, 0.12)',
            'quote_expiring'    => 'rgba(245, 158, 11, 0.12)',
            'debt_due'          => 'rgba(249, 115, 22, 0.12)',
            'savings_goal'      => 'rgba(34, 197, 94, 0.12)',
            'project_deadline'  => 'rgba(139, 92, 246, 0.12)',
            'budget_alert'      => 'rgba(239, 68, 68, 0.12)',
            'payment_received'  => 'rgba(16, 185, 129, 0.12)',
            default             => 'rgba(14, 116, 144, 0.12)',
        };
    }

    // Human-readable type label
    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'invoice_overdue'   => 'Invoice Overdue',
            'quote_expiring'    => 'Quote Expiring',
            'debt_due'          => 'Debt Due',
            'savings_goal'      => 'Savings Goal',
            'project_deadline'  => 'Project Deadline',
            'budget_alert'      => 'Budget Alert',
            'payment_received'  => 'Payment Received',
            default             => 'System',
        };
    }

    // Link to the related resource
    public function getLink(): string
    {
        if (!$this->related_id) {
            return '#';
        }

        return match ($this->type) {
            'invoice_overdue'  => route('invoices.show', $this->related_id),
            'quote_expiring'   => route('quotes.show', $this->related_id),
            'debt_due'         => route('debts.payable.show', $this->related_id),
            'savings_goal'     => route('savings.show', $this->related_id),
            'project_deadline' => route('projects.show', $this->related_id),
            'budget_alert'     => route('expense.categories.index'),
            'payment_received' => route('invoices.show', $this->related_id),
            default            => '#',
        };
    }
}
