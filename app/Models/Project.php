<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'status', 'start_date', 'end_date', 'is_public', 'is_locked', 'user_id', 'workspace_id',
    ];

    protected $attributes = [
        'is_public'  => false,
        'is_locked'  => false,
        'status'     => 'planning',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_public'  => 'boolean',
        'is_locked'  => 'boolean',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function projectMembers()
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'planning'  => 'Planification',
            'active'    => 'Actif',
            'on_hold'   => 'En pause',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé',
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'planning'  => 'blue',
            'active'    => 'green',
            'on_hold'   => 'yellow',
            'completed' => 'gray',
            'cancelled' => 'red',
            default     => 'gray',
        };
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function labels()
    {
        return $this->hasMany(Label::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class)->latest('created_at');
    }

    public function getProgressAttribute()
    {
        $total = $this->tasks()->count();
        if ($total === 0) return 0;
        $done = $this->tasks()->where('status', 'done')->count();
        return round(($done / $total) * 100);
    }

    public function isProjectAdmin(int $userId): bool
    {
        return $this->members()
                    ->where('users.id', $userId)
                    ->wherePivot('role', 'admin')
                    ->exists();
    }

    public function isAdminOrOwner(int $userId): bool
    {
        return $this->user_id === $userId || $this->isProjectAdmin($userId);
    }

    /**
     * Renvoie true si le propriétaire du projet est Pro.
     * Les fonctionnalités Pro sont liées au plan du propriétaire du projet.
     */
    public function hasPro(): bool
    {
        $this->loadMissing('owner');
        return $this->owner->isPro();
    }

    public function allTasksDone(): bool
    {
        $total = $this->tasks()->count();
        if ($total === 0) return false;
        return $this->tasks()->where('status', '!=', 'done')->doesntExist();
    }

    public function syncStatusFromTasks(): void
    {
        if ($this->allTasksDone()) {
            $this->update(['status' => 'completed']);
        } elseif ($this->status === 'completed') {
            // Si une tâche est réouverte, repasser en actif
            $this->update(['status' => 'active']);
        }
    }
}
