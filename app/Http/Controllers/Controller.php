<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Project;
use App\Models\SubTask;
use App\Models\Task;
use App\Models\TimeLog;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    /**
     * Retourne le rôle de l'utilisateur dans le projet :
     * 'owner' | 'admin' | 'member' | 'viewer' | null (aucun accès)
     */
    protected function getUserRoleInProject(Project $project): ?string
    {
        $user = Auth::user();
        if (!$user) return null;

        if ($project->user_id === $user->id) return 'owner';

        $member = $project->members()->where('users.id', $user->id)->first();
        return $member ? $member->pivot->role : null;
    }

    /**
     * Accès en lecture : owner, admin, member, viewer.
     * Public projects : tout le monde.
     */
    protected function authorizeProjectAccess(Project $project): void
    {
        if ($project->is_public) return;

        $role = $this->getUserRoleInProject($project);
        if ($role === null) abort(403);
    }

    /**
     * Accès en écriture (tâches, commentaires, sous-tâches, temps, labels) :
     * owner, admin, member uniquement — viewer exclu.
     */
    protected function authorizeWriteAccess(Project $project): void
    {
        $role = $this->getUserRoleInProject($project);
        if ($role === null || $role === 'viewer') {
            abort(403, 'Les observateurs ne peuvent pas modifier le contenu du projet.');
        }
    }

    /**
     * Actions d'administration projet (paramètres, membres, statut) :
     * owner ou admin uniquement.
     */
    protected function authorizeAdminAccess(Project $project): void
    {
        $role = $this->getUserRoleInProject($project);
        if ($role !== 'owner' && $role !== 'admin') {
            abort(403, 'Action réservée aux administrateurs du projet.');
        }
    }

    /**
     * Actions exclusives au propriétaire (suppression, verrouillage).
     */
    protected function authorizeOwnerAccess(Project $project): void
    {
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Action réservée au propriétaire du projet.');
        }
    }

    // ── Vérifications d'appartenance ──────────────────────────────────────

    protected function authorizeTaskInProject(Project $project, Task $task): void
    {
        if ($task->project_id !== $project->id) abort(404);
    }

    protected function authorizeCommentInTask(Task $task, Comment $comment): void
    {
        if ($comment->task_id !== $task->id) abort(404);
    }

    protected function authorizeSubTaskInTask(Task $task, SubTask $subTask): void
    {
        if ($subTask->task_id !== $task->id) abort(404);
    }

    protected function authorizeTimeLogInTask(Task $task, TimeLog $timeLog): void
    {
        if ($timeLog->task_id !== $task->id) abort(404);
    }
}
