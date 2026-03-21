<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecurityLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'event', 'ip_address', 'user_agent', 'details'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $event, string $details = '', ?int $userId = null): void
    {
        $request = app(Request::class);

        static::create([
            'user_id'    => $userId ?? Auth::id(),
            'event'      => $event,
            'ip_address' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
            'details'    => $details ?: null,
        ]);
    }

    public function getEventLabelAttribute(): string
    {
        return match($this->event) {
            'login_success'    => 'Connexion réussie',
            'login_failed'     => 'Tentative échouée',
            'login_blocked'    => 'Compte bloqué (trop de tentatives)',
            'logout'           => 'Déconnexion',
            'register'         => 'Inscription',
            'unauthorized'     => 'Accès non autorisé',
            'file_rejected'    => 'Fichier dangereux rejeté',
            default            => $this->event,
        };
    }

    public function getEventColorAttribute(): string
    {
        return match($this->event) {
            'login_success', 'register' => 'text-green-700 bg-green-100',
            'login_failed'              => 'text-orange-700 bg-orange-100',
            'login_blocked'             => 'text-red-700 bg-red-100',
            'unauthorized', 'file_rejected' => 'text-red-700 bg-red-100',
            'logout'                    => 'text-gray-600 bg-gray-100',
            default                     => 'text-blue-700 bg-blue-100',
        };
    }
}
