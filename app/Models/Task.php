<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'status', 'priority', 'due_date',
        'project_id', 'assigned_to', 'created_by',
        'is_recurring', 'recurrence_type', 'recurrence_ends_at',
    ];

    protected $casts = [
        'due_date'           => 'date',
        'recurrence_ends_at' => 'date',
        'is_recurring'       => 'boolean',
    ];

    public function getRecurrenceLabelAttribute(): string
    {
        return match($this->recurrence_type) {
            'daily'   => 'Quotidienne',
            'weekly'  => 'Hebdomadaire',
            'monthly' => 'Mensuelle',
            default   => '',
        };
    }

    public function nextDueDate(): ?\Carbon\Carbon
    {
        if (!$this->due_date || !$this->recurrence_type) return null;
        return match($this->recurrence_type) {
            'daily'   => $this->due_date->copy()->addDay(),
            'weekly'  => $this->due_date->copy()->addWeek(),
            'monthly' => $this->due_date->copy()->addMonth(),
            default   => null,
        };
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function subTasks()
    {
        return $this->hasMany(SubTask::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class);
    }

    public function labels()
    {
        return $this->belongsToMany(Label::class);
    }

    public function getTotalTimeLoggedAttribute(): int
    {
        return $this->timeLogs->sum('duration');
    }

    public function getTotalTimeFormattedAttribute(): string
    {
        $total = $this->getTotalTimeLoggedAttribute();
        $h = intdiv($total, 3600);
        $m = intdiv($total % 3600, 60);
        if ($h > 0) return "{$h}h " . str_pad($m, 2, '0', STR_PAD_LEFT) . "m";
        if ($m > 0) return "{$m}m";
        return $total > 0 ? "{$total}s" : '0m';
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'todo'        => 'À faire',
            'in_progress' => 'En cours',
            'review'      => 'En révision',
            'done'        => 'Terminé',
            default       => $this->status,
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'todo'        => 'gray',
            'in_progress' => 'blue',
            'review'      => 'yellow',
            'done'        => 'green',
            default       => 'gray',
        };
    }

    public function getPriorityLabelAttribute()
    {
        return match($this->priority) {
            'low'    => 'Faible',
            'medium' => 'Moyenne',
            'high'   => 'Haute',
            'urgent' => 'Urgente',
            default  => $this->priority,
        };
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'low'    => 'green',
            'medium' => 'blue',
            'high'   => 'orange',
            'urgent' => 'red',
            default  => 'gray',
        };
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'done';
    }
}
