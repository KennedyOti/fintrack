<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecurringTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'type',
        'name',
        'amount',
        'frequency',
        'start_date',
        'next_due_date',
        'end_date',
        'category_id',
        'client_id',
        'project_id',
        'vendor_name',
        'payment_method',
        'reference_number',
        'notes',
        'is_active',
        'last_generated_at',
    ];

    protected $casts = [
        'amount'            => 'decimal:2',
        'start_date'        => 'date',
        'next_due_date'     => 'date',
        'end_date'          => 'date',
        'is_active'         => 'boolean',
        'last_generated_at' => 'datetime',
    ];

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // Helpers

    /**
     * Given a reference date, return the next due date after one period.
     */
    public function calculateNextDueDate(Carbon $from): Carbon
    {
        return match ($this->frequency) {
            'daily'     => $from->copy()->addDay(),
            'weekly'    => $from->copy()->addWeek(),
            'monthly'   => $from->copy()->addMonth(),
            'quarterly' => $from->copy()->addMonths(3),
            'yearly'    => $from->copy()->addYear(),
        };
    }

    public function isDueToday(): bool
    {
        return $this->is_active && $this->next_due_date->isToday();
    }

    public function isOverdue(): bool
    {
        return $this->is_active && $this->next_due_date->isPast() && !$this->next_due_date->isToday();
    }

    public function frequencyLabel(): string
    {
        return match ($this->frequency) {
            'daily'     => 'Daily',
            'weekly'    => 'Weekly',
            'monthly'   => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly'    => 'Yearly',
        };
    }
}
