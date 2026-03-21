<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeLog extends Model
{
    protected $fillable = ['task_id', 'user_id', 'duration', 'note'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function formatSeconds(int $seconds): string
    {
        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);
        if ($h > 0) return "{$h}h " . str_pad($m, 2, '0', STR_PAD_LEFT) . "m";
        if ($m > 0) return "{$m}m";
        return "{$seconds}s";
    }

    public function getDurationFormattedAttribute(): string
    {
        $h = intdiv($this->duration, 3600);
        $m = intdiv($this->duration % 3600, 60);
        $s = $this->duration % 60;

        if ($h > 0) {
            return "{$h}h " . str_pad($m, 2, '0', STR_PAD_LEFT) . "m";
        }
        if ($m > 0) {
            return "{$m}m " . str_pad($s, 2, '0', STR_PAD_LEFT) . "s";
        }
        return "{$s}s";
    }
}
