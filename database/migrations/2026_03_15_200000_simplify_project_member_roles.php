<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Convertir les anciens rôles vers les nouveaux
        DB::table('project_members')->where('role', 'manager')->update(['role' => 'member']);
        DB::table('project_members')->where('role', 'owner')->update(['role' => 'admin']);

        // Réduire l'enum à 3 rôles
        DB::statement("ALTER TABLE project_members MODIFY COLUMN role ENUM('admin', 'member', 'viewer') NOT NULL DEFAULT 'member'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE project_members MODIFY COLUMN role ENUM('owner', 'manager', 'member', 'viewer') NOT NULL DEFAULT 'member'");
    }
};
