<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $memberProjects = $user->memberProjects()
            ->withCount('tasks')
            ->latest()
            ->take(4)
            ->get();

        $myTasks = Task::where('assigned_to', $user->id)
            ->whereHas('project')
            ->with('project')
            ->orderByRaw("CASE status WHEN 'in_progress' THEN 1 WHEN 'review' THEN 2 WHEN 'todo' THEN 3 ELSE 4 END")
            ->orderBy('due_date')
            ->take(15)
            ->get();

        $stats = [
            'total_projects'  => Project::where('user_id', $user->id)->count(),
            'active_projects' => Project::where('user_id', $user->id)->where('status', 'active')->count(),
            'my_tasks'        => Task::where('assigned_to', $user->id)->whereHas('project')->whereNotIn('status', ['done'])->count(),
            'completed_tasks' => Task::where('assigned_to', $user->id)->whereHas('project')->where('status', 'done')->count(),
        ];

        // Donut : répartition tâches par statut
        $tasksByStatus = [
            'todo'        => Task::where('assigned_to', $user->id)->whereHas('project')->where('status', 'todo')->count(),
            'in_progress' => Task::where('assigned_to', $user->id)->whereHas('project')->where('status', 'in_progress')->count(),
            'review'      => Task::where('assigned_to', $user->id)->whereHas('project')->where('status', 'review')->count(),
            'done'        => Task::where('assigned_to', $user->id)->whereHas('project')->where('status', 'done')->count(),
        ];

        // Barres : tâches actives par priorité
        $tasksByPriority = [
            'urgent' => Task::where('assigned_to', $user->id)->whereHas('project')->whereNotIn('status', ['done'])->where('priority', 'urgent')->count(),
            'high'   => Task::where('assigned_to', $user->id)->whereHas('project')->whereNotIn('status', ['done'])->where('priority', 'high')->count(),
            'medium' => Task::where('assigned_to', $user->id)->whereHas('project')->whereNotIn('status', ['done'])->where('priority', 'medium')->count(),
            'low'    => Task::where('assigned_to', $user->id)->whereHas('project')->whereNotIn('status', ['done'])->where('priority', 'low')->count(),
        ];

        // Prochaines échéances : tâches non terminées avec date limite (tous projets accessibles)
        $allProjectIds = Project::where('user_id', $user->id)
            ->orWhereHas('members', fn($q) => $q->where('users.id', $user->id))
            ->pluck('id');

        $upcomingTasks = Task::whereIn('project_id', $allProjectIds)
            ->whereNotNull('due_date')
            ->whereNotIn('status', ['done'])
            ->with(['project', 'assignedTo'])
            ->orderBy('due_date')
            ->take(8)
            ->get();

        // Ligne : activité sur les 7 derniers jours
        $projectIds = Project::where('user_id', $user->id)->pluck('id');
        $rawActivity = ActivityLog::whereIn('project_id', $projectIds)
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        $activityLabels = [];
        $activityData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $date             = now()->subDays($i)->format('Y-m-d');
            $activityLabels[] = now()->subDays($i)->locale('fr')->isoFormat('ddd D');
            $activityData[]   = (int) ($rawActivity[$date] ?? 0);
        }

        return view('dashboard', compact(
            'upcomingTasks', 'memberProjects', 'myTasks', 'stats',
            'tasksByStatus', 'tasksByPriority', 'activityLabels', 'activityData'
        ));
    }
}
