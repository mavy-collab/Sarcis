<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $this->authorizeWriteAccess($project);
        $this->denyIfLocked($project);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:todo,in_progress,review,done',
            'priority'    => 'required|in:low,medium,high,urgent',
            'due_date'    => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ], [
            'title.required' => 'Le titre est obligatoire.',
        ]);

        $validated['project_id'] = $project->id;
        $validated['created_by'] = Auth::id();

        $task = Task::create($validated);

        // Si le projet était terminé, une nouvelle tâche le repasse en actif
        $project->fresh(['tasks'])->syncStatusFromTasks();

        ActivityLog::log($project, 'task_created', Auth::user()->name . ' a créé la tâche "' . $task->title . '".');

        // Notifier si la tâche est assignée à quelqu'un d'autre
        if (!empty($validated['assigned_to']) && $validated['assigned_to'] != Auth::id()) {
            UserNotification::notify($validated['assigned_to'], 'task_assigned', [
                'message'       => Auth::user()->name . ' vous a assigné la tâche "' . $task->title . '" dans le projet "' . $project->title . '".',
                'project_id'    => $project->id,
                'project_title' => $project->title,
                'task_id'       => $task->id,
                'task_title'    => $task->title,
                'url'           => route('projects.tasks.show', [$project, $task]),
            ]);
        }

        return back()->with('success', 'Tâche créée avec succès !');
    }

    public function show(Project $project, Task $task)
    {
        $this->authorizeProjectAccess($project);
        $this->authorizeTaskInProject($project, $task);
        $task->load(['assignedTo', 'createdBy', 'comments.user', 'subTasks', 'timeLogs.user', 'labels']);
        $project->loadMissing('labels');
        $users = User::all();
        return view('tasks.show', compact('project', 'task', 'users'));
    }

    public function edit(Project $project, Task $task)
    {
        $this->authorizeTaskInProject($project, $task);
        $this->authorizeWriteAccess($project);
        $this->denyIfLocked($project);
        $users = User::all();
        return view('tasks.edit', compact('project', 'task', 'users'));
    }

    public function update(Request $request, Project $project, Task $task)
    {
        $this->authorizeTaskInProject($project, $task);
        $this->authorizeWriteAccess($project);
        $this->denyIfLocked($project);

        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'status'             => 'required|in:todo,in_progress,review,done',
            'priority'           => 'required|in:low,medium,high,urgent',
            'due_date'           => 'nullable|date',
            'assigned_to'        => 'nullable|exists:users,id',
            'recurrence_type'    => 'nullable|in:daily,weekly,monthly',
            'recurrence_ends_at' => 'nullable|date',
        ]);

        $validated['is_recurring']    = $request->boolean('is_recurring');
        $validated['recurrence_type'] = $validated['is_recurring'] ? ($validated['recurrence_type'] ?? null) : null;
        $validated['recurrence_ends_at'] = $validated['is_recurring'] ? ($validated['recurrence_ends_at'] ?? null) : null;

        $previousAssignee = $task->assigned_to;
        $oldTitle = $task->title;
        $task->update($validated);
        $project->syncStatusFromTasks();

        ActivityLog::log($project, 'task_updated', Auth::user()->name . ' a modifié la tâche "' . $oldTitle . '".');

        // Notifier si l'assignation a changé vers quelqu'un d'autre
        $newAssignee = $validated['assigned_to'] ?? null;
        if ($newAssignee && $newAssignee != $previousAssignee && $newAssignee != Auth::id()) {
            UserNotification::notify($newAssignee, 'task_assigned', [
                'message'       => Auth::user()->name . ' vous a assigné la tâche "' . $task->title . '" dans le projet "' . $project->title . '".',
                'project_id'    => $project->id,
                'project_title' => $project->title,
                'task_id'       => $task->id,
                'task_title'    => $task->title,
                'url'           => route('projects.tasks.show', [$project, $task]),
            ]);
        }

        return redirect()->route('projects.tasks.show', [$project, $task])
            ->with('success', 'Tâche mise à jour !');
    }

    public function updateStatus(Request $request, Project $project, Task $task)
    {
        $this->authorizeTaskInProject($project, $task);
        $this->authorizeWriteAccess($project);

        if ($project->is_locked) {
            return response()->json(['success' => false, 'error' => 'Projet verrouillé.'], 403);
        }

        $request->validate(['status' => 'required|in:todo,in_progress,review,done']);
        $previousStatus = $task->status;
        $task->update(['status' => $request->status]);
        $project->syncStatusFromTasks();

        $statusLabels = ['todo' => 'À faire', 'in_progress' => 'En cours', 'review' => 'En révision', 'done' => 'Terminé'];
        ActivityLog::log($project, 'task_status', Auth::user()->name . ' a changé le statut de "' . $task->title . '" en ' . ($statusLabels[$request->status] ?? $request->status) . '.');

        // Notifier tous les membres du projet quand une tâche est marquée "done"
        if ($request->status === 'done' && $previousStatus !== 'done') {
            $currentUserId = Auth::id();
            $currentUserName = Auth::user()->name;

            // Récupérer tous les membres (owner + membres via pivot)
            $memberIds = $project->members()->pluck('users.id')->toArray();
            $memberIds[] = $project->user_id; // owner
            $memberIds = array_unique($memberIds);

            foreach ($memberIds as $memberId) {
                if ($memberId == $currentUserId) continue; // pas besoin de notifier soi-même
                UserNotification::notify($memberId, 'task_completed', [
                    'message'       => $currentUserName . ' a terminé la tâche "' . $task->title . '" dans le projet "' . $project->title . '".',
                    'project_id'    => $project->id,
                    'project_title' => $project->title,
                    'task_id'       => $task->id,
                    'task_title'    => $task->title,
                    'doer'          => $currentUserName,
                    'url'           => route('projects.tasks.show', [$project, $task]),
                ]);
            }
        }

        // Tâche récurrente : créer la prochaine occurrence quand marquée "done"
        $nextCreated = false;
        if ($request->status === 'done' && $task->is_recurring && $task->recurrence_type) {
            if ($project->hasPro()) {
                $nextDue = $task->nextDueDate();
                $canCreate = $nextDue && (
                    !$task->recurrence_ends_at || $nextDue->lte($task->recurrence_ends_at)
                );
                if ($canCreate) {
                    $project->tasks()->create([
                        'title'              => $task->title,
                        'description'        => $task->description,
                        'priority'           => $task->priority,
                        'assigned_to'        => $task->assigned_to,
                        'created_by'         => $task->created_by,
                        'status'             => 'todo',
                        'due_date'           => $nextDue,
                        'is_recurring'       => true,
                        'recurrence_type'    => $task->recurrence_type,
                        'recurrence_ends_at' => $task->recurrence_ends_at,
                    ]);
                    $nextCreated = true;
                }
            }
        }

        return response()->json([
            'success'      => true,
            'status'       => $task->fresh()->status_label,
            'next_created' => $nextCreated,
        ]);
    }

    public function destroy(Project $project, Task $task)
    {
        $this->authorizeTaskInProject($project, $task);
        $this->authorizeWriteAccess($project);
        $this->denyIfLocked($project);
        $title = $task->title;
        $task->delete();

        // Recalculer le statut du projet après suppression
        $project->fresh(['tasks'])->syncStatusFromTasks();

        ActivityLog::log($project, 'task_deleted', Auth::user()->name . ' a supprimé la tâche "' . $title . '".');
        return redirect()->route('projects.show', $project)->with('success', 'Tâche supprimée !');
    }

    private function denyIfLocked(Project $project)
    {
        if ($project->is_locked) {
            abort(403, 'Ce projet est verrouillé. Aucune modification n\'est possible.');
        }
    }
}
