<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TimeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeLogController extends Controller
{
    public function store(Request $request, Project $project, Task $task)
    {
        $this->authorizeWriteAccess($project);
        $this->authorizeTaskInProject($project, $task);
        if (!$project->hasPro()) {
            return response()->json(['error' => 'Fonctionnalité Pro requise.'], 403);
        }

        $request->validate([
            'duration' => 'required|integer|min:1',
            'note'     => 'nullable|string|max:255',
        ]);

        $log = $task->timeLogs()->create([
            'user_id'  => Auth::id(),
            'duration' => $request->duration,
            'note'     => $request->note,
        ]);

        $log->load('user');

        return response()->json([
            'id'                 => $log->id,
            'duration'           => $log->duration,
            'duration_formatted' => $log->duration_formatted,
            'note'               => $log->note,
            'user'               => $log->user->name,
            'created_at'         => $log->created_at->format('d/m/Y H:i'),
            'total_formatted'    => $task->fresh(['timeLogs'])->total_time_formatted,
        ]);
    }

    public function destroy(Project $project, Task $task, TimeLog $timeLog)
    {
        $this->authorizeWriteAccess($project);
        $this->authorizeTaskInProject($project, $task);
        $this->authorizeTimeLogInTask($task, $timeLog);
        if (!$project->hasPro()) {
            return response()->json(['error' => 'Fonctionnalité Pro requise.'], 403);
        }

        if ($timeLog->user_id !== Auth::id() && $project->user_id !== Auth::id()) {
            return response()->json(['error' => 'Non autorisé.'], 403);
        }

        $timeLog->delete();

        return response()->json([
            'success'         => true,
            'total_formatted' => $task->fresh(['timeLogs'])->total_time_formatted,
        ]);
    }
}
