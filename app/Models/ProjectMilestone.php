<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectMilestone extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'start_date',
        'due_date',
        'amount',
        'status',
        'priority',
        'color',
        'order_position',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date'   => 'date',
        'amount'     => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class, 'milestone_id');
    }

    public function completedTasksCount(): int
    {
        return $this->tasks()->where('status', 'done')->count();
    }

    public function taskProgress(): int
    {
        $total = $this->tasks()->count();
        if ($total === 0) return 0;
        return (int) round(($this->completedTasksCount() / $total) * 100);
    }

    public function priorityColor(): string
    {
        return match($this->priority) {
            'critical' => '#F43F5E',
            'high'     => '#F59E0B',
            'medium'   => '#0E7490',
            'low'      => '#6B7280',
            default    => '#0E7490',
        };
    }

    public function priorityBadgeClass(): string
    {
        return match($this->priority) {
            'critical' => 'badge-priority-critical',
            'high'     => 'badge-priority-high',
            'medium'   => 'badge-priority-medium',
            'low'      => 'badge-priority-low',
            default    => 'badge-priority-medium',
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'completed'   => '#22C55E',
            'in_progress' => '#0E7490',
            'pending'     => '#94A3B8',
            default       => '#94A3B8',
        };
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'completed'   => 'Completed',
            'in_progress' => 'In Progress',
            'pending'     => 'Pending',
            default       => 'Pending',
        };
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }

    public function displayColor(): string
    {
        return $this->color ?? $this->priorityColor();
    }
}
