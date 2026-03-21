<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Compte administrateur prédéfini
        $this->call(AdminSeeder::class);

        // Utilisateurs de démonstration
        $admin = User::create([
            'name'     => 'Admin Demo',
            'email'    => 'admin@demo.com',
            'password' => Hash::make('password'),
        ]);

        $alice = User::create([
            'name'     => 'Alice Martin',
            'email'    => 'alice@demo.com',
            'password' => Hash::make('password'),
        ]);

        $bob = User::create([
            'name'     => 'Bob Dupont',
            'email'    => 'bob@demo.com',
            'password' => Hash::make('password'),
        ]);

        // Projet 1 - Refonte Site Web (public, actif)
        $project1 = Project::create([
            'title'       => 'Refonte du site web',
            'description' => 'Modernisation complète du site web de l\'entreprise avec un design responsive et des performances améliorées.',
            'status'      => 'active',
            'start_date'  => now()->subDays(30),
            'end_date'    => now()->addDays(60),
            'is_public'   => true,
            'user_id'     => $admin->id,
        ]);

        $project1->members()->attach($alice->id, ['role' => 'manager']);
        $project1->members()->attach($bob->id, ['role' => 'member']);

        $tasks1 = [
            ['Audit du site actuel', 'Analyser les performances et problèmes UX du site existant.', 'done', 'high', -25],
            ['Maquettes UI/UX', 'Créer les maquettes Figma pour toutes les pages principales.', 'done', 'high', -15],
            ['Développement frontend', 'Intégration HTML/CSS/JS des nouvelles maquettes.', 'in_progress', 'high', 20],
            ['Intégration backend', 'Connecter les APIs et la base de données.', 'todo', 'medium', 40],
            ['Tests et débogage', 'Tests cross-browser et correction des bugs.', 'todo', 'medium', 55],
            ['Mise en production', 'Déploiement sur le serveur de production.', 'todo', 'urgent', 60],
        ];

        foreach ($tasks1 as $i => [$title, $desc, $status, $priority, $days]) {
            Task::create([
                'title'       => $title,
                'description' => $desc,
                'status'      => $status,
                'priority'    => $priority,
                'due_date'    => now()->addDays($days),
                'project_id'  => $project1->id,
                'assigned_to' => [$admin->id, $alice->id, $bob->id][$i % 3],
                'created_by'  => $admin->id,
            ]);
        }

        // Projet 2 - Application Mobile (public, planification)
        $project2 = Project::create([
            'title'       => 'Application mobile e-commerce',
            'description' => 'Développement d\'une application mobile iOS/Android pour notre plateforme e-commerce.',
            'status'      => 'planning',
            'start_date'  => now()->addDays(15),
            'end_date'    => now()->addDays(120),
            'is_public'   => true,
            'user_id'     => $alice->id,
        ]);

        $project2->members()->attach($admin->id, ['role' => 'viewer']);
        $project2->members()->attach($bob->id, ['role' => 'member']);

        $tasks2 = [
            ['Cahier des charges', 'Définir les fonctionnalités et les user stories.', 'in_progress', 'high', 10],
            ['Architecture technique', 'Choix du framework et architecture de l\'app.', 'todo', 'high', 20],
            ['Design des écrans', 'Maquettes de toutes les vues de l\'application.', 'todo', 'medium', 40],
            ['Développement iOS', 'Implémentation sur Swift/SwiftUI.', 'todo', 'medium', 90],
            ['Développement Android', 'Implémentation sur Kotlin.', 'todo', 'medium', 90],
        ];

        foreach ($tasks2 as $i => [$title, $desc, $status, $priority, $days]) {
            Task::create([
                'title'       => $title,
                'description' => $desc,
                'status'      => $status,
                'priority'    => $priority,
                'due_date'    => now()->addDays($days),
                'project_id'  => $project2->id,
                'assigned_to' => [$alice->id, $bob->id, $admin->id][$i % 3],
                'created_by'  => $alice->id,
            ]);
        }

        // Projet 3 - Privé (admin)
        $project3 = Project::create([
            'title'       => 'Migration base de données',
            'description' => 'Migration de MySQL vers PostgreSQL pour améliorer les performances.',
            'status'      => 'active',
            'start_date'  => now()->subDays(10),
            'end_date'    => now()->addDays(30),
            'is_public'   => false,
            'user_id'     => $admin->id,
        ]);

        $tasks3 = [
            ['Sauvegarde complète', 'Backup de toutes les données avant migration.', 'done', 'urgent', -5],
            ['Script de migration', 'Écrire les scripts de migration des tables.', 'in_progress', 'high', 10],
            ['Tests de migration', 'Vérifier l\'intégrité des données migrées.', 'todo', 'high', 20],
            ['Mise en production', 'Basculement vers la nouvelle base de données.', 'todo', 'urgent', 30],
        ];

        foreach ($tasks3 as $i => [$title, $desc, $status, $priority, $days]) {
            $task = Task::create([
                'title'       => $title,
                'description' => $desc,
                'status'      => $status,
                'priority'    => $priority,
                'due_date'    => now()->addDays($days),
                'project_id'  => $project3->id,
                'assigned_to' => $admin->id,
                'created_by'  => $admin->id,
            ]);

            // Ajouter des commentaires sur la première tâche
            if ($i === 0) {
                Comment::create([
                    'content' => 'Sauvegarde effectuée avec succès ! Tous les fichiers sont sécurisés.',
                    'task_id' => $task->id,
                    'user_id' => $admin->id,
                ]);
            }
        }

        $this->command->info('');
        $this->command->info('✓ Données de démonstration créées !');
        $this->command->info('');
        $this->command->info('Comptes de test :');
        $this->command->info('  admin@demo.com  / password');
        $this->command->info('  alice@demo.com  / password');
        $this->command->info('  bob@demo.com    / password');
        $this->command->info('');
    }
}
