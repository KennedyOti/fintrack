<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'client_id',
        'title',
        'description',
        'budget',
        'start_date',
        'deadline',
        'status',
        'progress_percent',
    ];

    protected $casts = [
        'start_date'       => 'date',
        'deadline'         => 'date',
        'budget'           => 'decimal:2',
        'progress_percent' => 'integer',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectMilestone::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class);
    }

    public function timeLogs(): HasMany
    {
        return $this->hasMany(ProjectTimeLog::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // Financial helpers
    public function totalIncome()
    {
        return $this->incomes()->sum('amount');
    }

    public function totalExpenses()
    {
        return $this->expenses()->sum('amount');
    }

    public function profit()
    {
        return $this->totalIncome() - $this->totalExpenses();
    }

    public function budgetRemaining()
    {
        return $this->budget - $this->totalExpenses();
    }

    // Task helpers
    public function totalTasksCount(): int
    {
        return $this->tasks()->count();
    }

    public function completedTasksCount(): int
    {
        return $this->tasks()->where('status', 'done')->count();
    }

    public function totalHoursLogged(): float
    {
        return (float) $this->timeLogs()->sum('hours');
    }

    // Timeline helpers
    public function isOverdue(): bool
    {
        return $this->deadline
            && $this->deadline->isPast()
            && !in_array($this->status, ['completed', 'cancelled']);
    }

    public function daysRemaining(): ?int
    {
        if (!$this->deadline) return null;
        return (int) now()->diffInDays($this->deadline, false);
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'in_progress' => '#0E7490',
            'completed'   => '#22C55E',
            'on_hold'     => '#F59E0B',
            'cancelled'   => '#F43F5E',
            'planned'     => '#94A3B8',
            default       => '#94A3B8',
        };
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'planned'     => 'Planned',
            'in_progress' => 'In Progress',
            'on_hold'     => 'On Hold',
            'completed'   => 'Completed',
            'cancelled'   => 'Cancelled',
            default       => 'Planned',
        };
    }
}
