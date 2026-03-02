<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'milestone_id',
        'title',
        'description',
        'priority',
        'status',
        'start_date',
        'due_date',
        'estimated_hours',
        'actual_hours',
        'order_position',
        'completed_at',
    ];

    protected $casts = [
        'start_date'       => 'date',
        'due_date'         => 'date',
        'completed_at'     => 'datetime',
        'estimated_hours'  => 'decimal:2',
        'actual_hours'     => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(ProjectMilestone::class, 'milestone_id');
    }

    public function timeLogs(): HasMany
    {
        return $this->hasMany(ProjectTimeLog::class, 'task_id');
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'done';
    }

    public function daysRemaining(): ?int
    {
        if (!$this->due_date) return null;
        return (int) now()->diffInDays($this->due_date, false);
    }

    public function priorityColor(): string
    {
        return match($this->priority) {
            'critical' => '#F43F5E',
            'high'     => '#F59E0B',
            'medium'   => '#0E7490',
            'low'      => '#6B7280',
            default    => '#6B7280',
        };
    }

    public function priorityBadgeClass(): string
    {
        return match($this->priority) {
            'critical' => 'badge-priority-critical',
            'high'     => 'badge-priority-high',
            'medium'   => 'badge-priority-medium',
            'low'      => 'badge-priority-low',
            default    => 'badge-priority-low',
        };
    }

    public function statusBadgeClass(): string
    {
        return match($this->status) {
            'todo'        => 'badge-task-todo',
            'in_progress' => 'badge-task-progress',
            'in_review'   => 'badge-task-review',
            'blocked'     => 'badge-task-blocked',
            'done'        => 'badge-task-done',
            default       => 'badge-task-todo',
        };
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'todo'        => 'To Do',
            'in_progress' => 'In Progress',
            'in_review'   => 'In Review',
            'blocked'     => 'Blocked',
            'done'        => 'Done',
            default       => 'To Do',
        };
    }

    public function priorityLabel(): string
    {
        return ucfirst($this->priority);
    }
}
