<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, CanResetPassword;

    public function hasVerifiedEmail(): bool
    {
        if (app()->environment('local')) {
            return true;
        }
        return parent::hasVerifiedEmail();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'is_admin',
        'plan',
        'plan_expires_at',
        'password',
        'google_id',
        'avatar',
        'trial_used',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'plan_expires_at'   => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
            'trial_used'        => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Un administrateur reste en plan Pro permanent (jamais d'expiration ni de rétrogradation)
        static::saving(function (User $user) {
            if ($user->is_admin && ($user->plan !== 'pro' || $user->plan_expires_at !== null)) {
                $user->plan = 'pro';
                $user->plan_expires_at = null;
            }
        });
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function isPro(): bool
    {
        return true; // Toutes les fonctionnalités sont gratuites pour le moment
    }

    public function isEnterprise(): bool
    {
        return $this->plan === 'enterprise';
    }

    public function planLabel(): string
    {
        return match($this->plan) {
            'pro'        => 'Pro',
            'enterprise' => 'Entreprise',
            default      => 'Gratuit',
        };
    }

    public function maxWorkspaces(): int
    {
        return $this->isPro() ? PHP_INT_MAX : 5;
    }

    public function maxProjectsPerWorkspace(): int
    {
        return $this->isPro() ? PHP_INT_MAX : 1;
    }

    public function maxMembersPerProject(): int
    {
        return $this->isPro() ? PHP_INT_MAX : 3;
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function workspaces()
    {
        return $this->hasMany(Workspace::class);
    }

    public function memberProjects()
    {
        return $this->belongsToMany(Project::class, 'project_members')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
