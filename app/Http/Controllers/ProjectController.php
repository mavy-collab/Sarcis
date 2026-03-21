<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\User;
use App\Models\UserNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $projects = Project::where('user_id', $user->id)
            ->orWhereHas('members', fn($q) => $q->where('users.id', $user->id))
            ->withCount('tasks')
            ->latest()
            ->get();

        $grouped = $projects->groupBy('status');

        return view('projects.index', compact('projects', 'grouped'));
    }

    public function toggleLock(Project $project)
    {
        $this->authorizeOwner($project);
        $project->update(['is_locked' => !$project->is_locked]);

        $msg = $project->is_locked ? 'Projet verrouillé. Plus aucune modification possible.' : 'Projet déverrouillé.';
        return back()->with('success', $msg);
    }

    public function updateStatus(Request $request, Project $project)
    {
        $this->authorizeAdminOrOwner($project);

        $request->validate([
            'status' => 'required|in:planning,active,on_hold,completed,cancelled',
        ]);

        if ($request->status === 'completed' && !$project->allTasksDone()) {
            return back()->withErrors(['status' => 'Impossible de terminer le projet : toutes les tâches doivent être marquées "Terminé" d\'abord.']);
        }

        $project->update(['status' => $request->status]);

        return back()->with('success', 'Statut du projet mis à jour.');
    }

    public function create()
    {
        $workspaceId = request('workspace_id');

        if (!$workspaceId) {
            $hasWorkspaces = \App\Models\Workspace::where('user_id', Auth::id())->exists();
            if (!$hasWorkspaces) {
                return redirect()->route('workspaces.create')
                    ->with('info', 'Créez d\'abord un espace de travail avant d\'ajouter un projet.');
            }
            return redirect()->route('workspaces.index')
                ->with('info', 'Sélectionnez un espace de travail pour créer un projet.');
        }

        $workspace = \App\Models\Workspace::where('id', $workspaceId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $max = Auth::user()->maxProjectsPerWorkspace();
        if ($workspace->projects()->count() >= $max) {
            return redirect()->route('workspaces.show', $workspace)
                ->with('limit_error', 'Limite atteinte : maximum ' . $max . ' projet(s) par espace sur le plan ' . Auth::user()->planLabel() . '. Passez au plan Pro.');
        }

        return view('projects.create', compact('workspace'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'status'              => 'required|in:planning,active,on_hold,completed,cancelled',
            'start_date'          => 'nullable|date',
            'end_date'            => 'nullable|date|after_or_equal:start_date',
            'workspace_id'        => ['required', \Illuminate\Validation\Rule::exists('workspaces', 'id')->where('user_id', Auth::id())],
            'tasks'               => 'nullable|array',
            'tasks.*.title'       => 'required|string|max:255',
            'tasks.*.priority'    => 'nullable|in:low,medium,high,urgent',
            'tasks.*.status'      => 'nullable|in:todo,in_progress,review,done',
            'tasks.*.due_date'    => 'nullable|date',
        ], [
            'title.required'           => 'Le titre est obligatoire.',
            'workspace_id.required'    => 'Un espace de travail est obligatoire.',
            'end_date.after_or_equal'  => 'La date de fin doit être après la date de début.',
            'tasks.*.title.required'   => 'Le titre de chaque tâche est obligatoire.',
        ]);

        $max = Auth::user()->maxProjectsPerWorkspace();
        $workspaceProjectCount = Project::where('workspace_id', $validated['workspace_id'])->count();
        if ($workspaceProjectCount >= $max) {
            return redirect()->route('workspaces.show', $validated['workspace_id'])
                ->with('limit_error', 'Limite atteinte sur le plan ' . Auth::user()->planLabel() . '. Passez au plan Pro pour créer plus de projets.');
        }

        $validated['user_id'] = Auth::id();

        $project = Project::create($validated);

        if (!empty($validated['tasks'])) {
            foreach ($validated['tasks'] as $taskData) {
                $project->tasks()->create([
                    'title'      => $taskData['title'],
                    'priority'   => $taskData['priority'] ?? 'medium',
                    'status'     => $taskData['status'] ?? 'todo',
                    'due_date'   => $taskData['due_date'] ?? null,
                    'created_by' => Auth::id(),
                ]);
            }
        }

        return redirect()->route('workspaces.show', $project->workspace_id)->with('success', 'Projet créé avec succès !');
    }

    public function show(Project $project)
    {
        $this->authorizeView($project);

        $project->load(['owner', 'members', 'tasks.assignedTo', 'tasks.labels', 'attachments.uploader', 'labels']);

        $tasksByStatus = [
            'todo'        => $project->tasks->where('status', 'todo'),
            'in_progress' => $project->tasks->where('status', 'in_progress'),
            'review'      => $project->tasks->where('status', 'review'),
            'done'        => $project->tasks->where('status', 'done'),
        ];

        $users = User::all();
        $isProjectAdmin = Auth::check() && $project->isAdminOrOwner(Auth::id());

        return view('projects.show', compact('project', 'tasksByStatus', 'users', 'isProjectAdmin'));
    }

    public function edit(Project $project)
    {
        $this->authorizeAdminOrOwner($project);
        $users = User::where('id', '!=', Auth::id())->get();
        return view('projects.edit', compact('project', 'users'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorizeAdminOrOwner($project);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:planning,active,on_hold,completed,cancelled',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project)->with('success', 'Projet mis à jour avec succès !');
    }

    public function destroy(Project $project)
    {
        $this->authorizeOwner($project);
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Projet supprimé avec succès !');
    }

    public function export(Project $project)
    {
        $this->authorizeView($project);

        if (!$project->hasPro()) {
            return redirect()->route('plans.index')
                ->with('info', 'L\'export PDF est disponible sur le plan Pro.');
        }

        $project->load(['owner', 'tasks.assignedTo', 'members', 'attachments']);
        $pdf = Pdf::loadView('projects.export', compact('project'))
            ->setPaper('a4', 'portrait');
        return $pdf->download(\Illuminate\Support\Str::slug($project->title, '_') . '.pdf');
    }

    public function gantt(Project $project)
    {
        $this->authorizeView($project);

        if (!$project->hasPro()) {
            return redirect()->route('plans.index')
                ->with('info', 'Le diagramme de Gantt est disponible sur le plan Pro.');
        }

        $project->load(['tasks.assignedTo']);
        return view('projects.gantt', compact('project'));
    }

    public function report(Project $project)
    {
        $this->authorizeView($project);

        if (!$project->hasPro()) {
            return redirect()->route('plans.index')
                ->with('info', 'Les rapports sont disponibles sur le plan Pro.');
        }

        $project->load(['tasks.assignedTo', 'tasks.timeLogs.user', 'members']);

        $taskStats = [
            'todo'        => $project->tasks->where('status', 'todo')->count(),
            'in_progress' => $project->tasks->where('status', 'in_progress')->count(),
            'review'      => $project->tasks->where('status', 'review')->count(),
            'done'        => $project->tasks->where('status', 'done')->count(),
        ];

        $priorityStats = [
            'urgent' => $project->tasks->where('priority', 'urgent')->count(),
            'high'   => $project->tasks->where('priority', 'high')->count(),
            'medium' => $project->tasks->where('priority', 'medium')->count(),
            'low'    => $project->tasks->where('priority', 'low')->count(),
        ];

        $overdueTasks    = $project->tasks->filter(fn($t) => $t->is_overdue);
        $memberWorkload  = $project->tasks->whereNotNull('assigned_to')
            ->groupBy('assigned_to')
            ->map(fn($tasks) => [
                'name'  => $tasks->first()->assignedTo?->name ?? 'Inconnu',
                'count' => $tasks->count(),
                'done'  => $tasks->where('status', 'done')->count(),
            ]);

        // Temps passé par collaborateur (tous les timelogs du projet, groupés par user)
        $timeByMember = collect();
        foreach ($project->tasks as $task) {
            foreach ($task->timeLogs as $log) {
                $uid = $log->user_id;
                if (!$timeByMember->has($uid)) {
                    $timeByMember->put($uid, [
                        'name'     => $log->user->name ?? 'Inconnu',
                        'total'    => 0,
                        'tasks'    => collect(),
                    ]);
                }
                $entry = $timeByMember->get($uid);
                $entry['total'] += $log->duration;
                $taskKey = $task->id;
                if (!$entry['tasks']->has($taskKey)) {
                    $entry['tasks']->put($taskKey, ['title' => $task->title, 'duration' => 0]);
                }
                $t = $entry['tasks']->get($taskKey);
                $t['duration'] += $log->duration;
                $entry['tasks']->put($taskKey, $t);
                $timeByMember->put($uid, $entry);
            }
        }
        $timeByMember = $timeByMember->sortByDesc('total');

        return view('projects.report', compact('project', 'taskStats', 'priorityStats', 'overdueTasks', 'memberWorkload', 'timeByMember'));
    }

    public function activity(Project $project)
    {
        $this->authorizeView($project);

        if (!$project->hasPro()) {
            return redirect()->route('plans.index')
                ->with('info', "L'historique d'activité est disponible sur le plan Pro.");
        }

        $activities = $project->activityLogs()
            ->with('user')
            ->latest()
            ->paginate(50);

        return view('projects.activity', compact('project', 'activities'));
    }

    public function addMember(Request $request, Project $project)
    {
        $this->authorizeAdminOrOwner($project);

        $request->validate([
            'email' => 'required|email',
            'role'  => 'required|in:admin,member,viewer',
        ]);

        // Seul le propriétaire peut assigner le rôle admin
        if ($request->role === 'admin' && $project->user_id !== Auth::id()) {
            return back()->withErrors(['role' => 'Seul le propriétaire peut assigner le rôle admin.']);
        }

        // Recherche sans exposer si l'email existe (anti-énumération)
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Aucun utilisateur trouvé avec ces informations.']);
        }

        if ($user->id === $project->user_id) {
            return back()->withErrors(['email' => 'Cet utilisateur est déjà le propriétaire.']);
        }

        // Limite : ne compte que les nouveaux membres (pas les mises à jour de rôle)
        $isExisting = $project->members()->where('users.id', $user->id)->exists();
        if (!$isExisting) {
            $maxMembers = Auth::user()->maxMembersPerProject();
            if ($project->members()->count() >= $maxMembers) {
                return back()->withErrors(['email' => "Limite atteinte : maximum {$maxMembers} collaborateur(s) sur le plan " . Auth::user()->planLabel() . '. Passez au plan Pro.']);
            }
        }

        // Annuler toute invitation en attente existante pour cet utilisateur
        ProjectInvitation::where('project_id', $project->id)
            ->where('invited_user_id', $user->id)
            ->where('status', 'pending')
            ->update(['status' => 'declined']);

        // Créer une invitation en attente de confirmation
        $invitation = ProjectInvitation::create([
            'project_id'      => $project->id,
            'inviter_id'      => Auth::id(),
            'invited_user_id' => $user->id,
            'role'            => $request->role,
            'status'          => 'pending',
            'expires_at'      => now()->addDays(7),
        ]);

        // Notification à l'invité avec lien vers la page de confirmation
        UserNotification::notify($user->id, 'project_invitation', [
            'message'       => Auth::user()->name . ' vous invite à rejoindre le projet "' . $project->title . '" en tant que ' . $request->role . '.',
            'project_id'    => $project->id,
            'project_title' => $project->title,
            'inviter_name'  => Auth::user()->name,
            'role'          => $request->role,
            'url'           => route('invitations.show', $invitation),
        ]);

        ActivityLog::log($project, 'member_invited', Auth::user()->name . ' a invité ' . $user->name . ' à rejoindre le projet.');

        return back()->with('success', 'Invitation envoyée à ' . $user->name . '. Il doit confirmer pour rejoindre le projet.');
    }

    public function removeMember(Project $project, User $user)
    {
        $this->authorizeAdminOrOwner($project);

        // Empêcher la suppression du dernier admin si ce n'est pas le propriétaire qui retire
        $memberRole = $project->members()->where('users.id', $user->id)->first()?->pivot->role;
        if ($memberRole === 'admin' && $project->user_id !== Auth::id()) {
            return back()->withErrors(['member' => 'Seul le propriétaire peut retirer un administrateur.']);
        }

        // Désassigner les tâches du membre retiré
        $project->tasks()->where('assigned_to', $user->id)->update(['assigned_to' => null]);

        $project->members()->detach($user->id);

        ActivityLog::log($project, 'member_removed', Auth::user()->name . ' a retiré ' . $user->name . ' du projet.');

        UserNotification::notify($user->id, 'member_removed', [
            'message'       => 'Vous avez été retiré du projet "' . $project->title . '".',
            'project_id'    => $project->id,
            'project_title' => $project->title,
            'url'           => route('projects.index'),
        ]);

        return back()->with('success', 'Membre retiré du projet.');
    }

    private function authorizeView(Project $project): void
    {
        if (!Auth::check()) abort(403);
        $this->authorizeProjectAccess($project);
    }

    private function authorizeOwner(Project $project): void
    {
        $this->authorizeOwnerAccess($project);
    }

    private function authorizeAdminOrOwner(Project $project): void
    {
        $this->authorizeAdminAccess($project);
    }
}
