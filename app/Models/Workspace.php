<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'color', 'background_image', 'user_id'];

    // Images Unsplash par défaut
    protected static array $unsplashBackgrounds = [];

    /**
     * Retourne toutes les images disponibles :
     * d'abord les photos locales (public/images/backgrounds/),
     * puis les images Unsplash par défaut.
     */
    public static function availableBackgrounds(): array
    {
        $local = [];
        $dir   = public_path('images/backgrounds');

        if (is_dir($dir)) {
            foreach (glob($dir . '/*.{jpg,jpeg,png,webp,gif}', GLOB_BRACE) as $file) {
                $filename  = pathinfo($file, PATHINFO_FILENAME);
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $local[]   = [
                    'key'   => 'local_' . $filename,
                    'label' => ucfirst(str_replace(['-', '_'], ' ', $filename)),
                    'url'   => asset('images/backgrounds/' . $filename . '.' . $extension),
                ];
            }
        }

        return array_merge($local, static::$unsplashBackgrounds);
    }

    public function getBackgroundUrlAttribute(): ?string
    {
        if (!$this->background_image) return null;
        $bg = collect(self::availableBackgrounds())->firstWhere('key', $this->background_image);
        return $bg ? $bg['url'] : null;
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function getColorClassesAttribute(): array
    {
        return match($this->color) {
            'indigo' => ['bg' => 'bg-indigo-600', 'light' => 'bg-indigo-100', 'text' => 'text-indigo-700', 'border' => 'border-indigo-300'],
            'emerald'=> ['bg' => 'bg-emerald-600', 'light' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'border' => 'border-emerald-300'],
            'rose'   => ['bg' => 'bg-rose-600',    'light' => 'bg-rose-100',    'text' => 'text-rose-700',    'border' => 'border-rose-300'],
            'amber'  => ['bg' => 'bg-amber-500',   'light' => 'bg-amber-100',   'text' => 'text-amber-700',   'border' => 'border-amber-300'],
            'violet' => ['bg' => 'bg-violet-600',  'light' => 'bg-violet-100',  'text' => 'text-violet-700',  'border' => 'border-violet-300'],
            'sky'    => ['bg' => 'bg-sky-600',      'light' => 'bg-sky-100',     'text' => 'text-sky-700',     'border' => 'border-sky-300'],
            'teal'   => ['bg' => 'bg-teal-600',    'light' => 'bg-teal-100',    'text' => 'text-teal-700',    'border' => 'border-teal-300'],
            'orange' => ['bg' => 'bg-orange-500',  'light' => 'bg-orange-100',  'text' => 'text-orange-700',  'border' => 'border-orange-300'],
            default  => ['bg' => 'bg-gray-600',    'light' => 'bg-gray-100',    'text' => 'text-gray-700',    'border' => 'border-gray-300'],
        };
    }
}
