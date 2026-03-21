<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['project_id', 'user_id', 'type', 'description'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log(Project $project, string $type, string $description): void
    {
        static::create([
            'project_id'  => $project->id,
            'user_id'     => Auth::id(),
            'type'        => $type,
            'description' => $description,
        ]);
    }

    // Icône et couleur selon le type
    public function getIconAttribute(): array
    {
        return match(true) {
            str_starts_with($this->type, 'task_created')    => ['icon' => 'M12 4v16m8-8H4',           'color' => 'text-green-600',  'bg' => 'bg-green-100'],
            str_starts_with($this->type, 'task_deleted')    => ['icon' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16', 'color' => 'text-red-600', 'bg' => 'bg-red-100'],
            str_starts_with($this->type, 'task_status')     => ['icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'color' => 'text-indigo-600', 'bg' => 'bg-indigo-100'],
            str_starts_with($this->type, 'task_updated')    => ['icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', 'color' => 'text-blue-600', 'bg' => 'bg-blue-100'],
            str_starts_with($this->type, 'task_assigned')   => ['icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'color' => 'text-purple-600', 'bg' => 'bg-purple-100'],
            str_starts_with($this->type, 'comment')         => ['icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'color' => 'text-gray-600', 'bg' => 'bg-gray-100'],
            str_starts_with($this->type, 'member')          => ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'text-teal-600', 'bg' => 'bg-teal-100'],
            str_starts_with($this->type, 'attachment')      => ['icon' => 'M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13', 'color' => 'text-orange-600', 'bg' => 'bg-orange-100'],
            default                                          => ['icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'text-gray-500', 'bg' => 'bg-gray-100'],
        };
    }
}
