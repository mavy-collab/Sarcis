<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\SubTask;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubTaskController extends Controller
{
    public function store(Request $request, Project $project, Task $task)
    {
        $this->authorizeWriteAccess($project);
        $this->authorizeTaskInProject($project, $task);
        if (!$project->hasPro()) {
            return response()->json(['error' => 'Fonctionnalité Pro requise.'], 403);
        }

        $request->validate(['title' => 'required|string|max:255']);

        $subTask = $task->subTasks()->create([
            'title'      => $request->title,
            'is_done'    => false,
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'id'      => $subTask->id,
            'title'   => $subTask->title,
            'is_done' => $subTask->is_done,
        ]);
    }

    public function toggle(Project $project, Task $task, SubTask $subTask)
    {
        $this->authorizeWriteAccess($project);
        $this->authorizeTaskInProject($project, $task);
        $this->authorizeSubTaskInTask($task, $subTask);
        if (!$project->hasPro()) {
            return response()->json(['error' => 'Fonctionnalité Pro requise.'], 403);
        }

        $subTask->update(['is_done' => !$subTask->is_done]);

        return response()->json(['is_done' => $subTask->is_done]);
    }

    public function destroy(Project $project, Task $task, SubTask $subTask)
    {
        $this->authorizeWriteAccess($project);
        $this->authorizeTaskInProject($project, $task);
        $this->authorizeSubTaskInTask($task, $subTask);
        if (!$project->hasPro()) {
            return response()->json(['error' => 'Fonctionnalité Pro requise.'], 403);
        }

        $subTask->delete();
        return response()->json(['success' => true]);
    }
}
