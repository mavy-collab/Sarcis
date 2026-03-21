<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class DowngradeExpiredPlans extends Command
{
    protected $signature   = 'plans:downgrade-expired';
    protected $description = 'Remet les plans expirés en Gratuit';

    public function handle(): void
    {
        $count = User::where('plan', '!=', 'free')
            ->whereNotNull('plan_expires_at')
            ->where('plan_expires_at', '<', now())
            ->update(['plan' => 'free', 'plan_expires_at' => null]);

        $this->info("$count utilisateur(s) rétrogradé(s) vers le plan Gratuit.");
    }
}
