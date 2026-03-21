<?php

namespace App\Http\Controllers;

use App\Models\Label;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $this->authorizeAdminAccess($project);
        if (!$project->hasPro()) {
            return response()->json(['error' => 'Fonctionnalité Pro requise.'], 403);
        }

        $request->validate([
            'name'  => 'required|string|max:50',
            'color' => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
        ]);

        $label = $project->labels()->create([
            'name'  => $request->name,
            'color' => $request->color,
        ]);

        return response()->json([
            'id'         => $label->id,
            'name'       => $label->name,
            'color'      => $label->color,
            'text_color' => $label->text_color,
        ]);
    }

    public function destroy(Project $project, Label $label)
    {
        $this->authorizeAdminAccess($project);
        if ($label->project_id !== $project->id) abort(404);
        if (!$project->hasPro()) {
            return response()->json(['error' => 'Fonctionnalité Pro requise.'], 403);
        }

        $label->delete();
        return response()->json(['success' => true]);
    }

    public function attach(Request $request, Project $project, Task $task)
    {
        $this->authorizeWriteAccess($project);
        $this->authorizeTaskInProject($project, $task);
        if (!$project->hasPro()) {
            return response()->json(['error' => 'Fonctionnalité Pro requise.'], 403);
        }

        $request->validate(['label_id' => 'required|exists:labels,id']);
        $task->labels()->syncWithoutDetaching([$request->label_id]);

        return response()->json(['success' => true]);
    }

    public function detach(Project $project, Task $task, Label $label)
    {
        $this->authorizeWriteAccess($project);
        $this->authorizeTaskInProject($project, $task);
        if ($label->project_id !== $project->id) abort(404);
        if (!$project->hasPro()) {
            return response()->json(['error' => 'Fonctionnalité Pro requise.'], 403);
        }

        $task->labels()->detach($label->id);
        return response()->json(['success' => true]);
    }
}
