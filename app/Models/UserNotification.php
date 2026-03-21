<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotification extends Model
{
    protected $table = 'user_notifications';

    protected $fillable = ['user_id', 'type', 'data', 'read_at'];

    protected $casts = [
        'data'    => 'array',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function markAsRead(): void
    {
        if (!$this->isRead()) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Create a notification for a user.
     */
    public static function notify(int $userId, string $type, array $data): self
    {
        return static::create([
            'user_id' => $userId,
            'type'    => $type,
            'data'    => $data,
        ]);
    }

    public function getIconAttribute(): string
    {
        return match($this->type) {
            'member_added'        => '👥',
            'member_removed'      => '🚪',
            'task_assigned'       => '✅',
            'task_completed'      => '🎉',
            'project_deleted'     => '🗑️',
            'project_invitation'  => '📩',
            'invitation_accepted' => '✅',
            'invitation_declined' => '❌',
            default               => '🔔',
        };
    }

    public function getMessageAttribute(): string
    {
        return $this->data['message'] ?? '';
    }

    public function getUrlAttribute(): string
    {
        return $this->data['url'] ?? '#';
    }
}
