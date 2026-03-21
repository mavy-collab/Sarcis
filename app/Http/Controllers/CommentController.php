<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Project $project, Task $task)
    {
        $this->authorizeWriteAccess($project);
        $this->authorizeTaskInProject($project, $task);

        if ($project->is_locked) {
            return back()->withErrors(['locked' => 'Ce projet est verrouillé.']);
        }

        $request->validate([
            'content' => 'required|string|max:2000',
        ], [
            'content.required' => 'Le commentaire ne peut pas être vide.',
        ]);

        Comment::create([
            'content' => $request->content,
            'task_id' => $task->id,
            'user_id' => Auth::id(),
        ]);

        ActivityLog::log($project, 'comment_added', Auth::user()->name . ' a commenté la tâche "' . $task->title . '".');

        return back()->with('success', 'Commentaire ajouté !');
    }

    public function destroy(Project $project, Task $task, Comment $comment)
    {
        $this->authorizeTaskInProject($project, $task);
        $this->authorizeCommentInTask($task, $comment);

        if ($comment->user_id !== Auth::id() && $project->user_id !== Auth::id()) {
            abort(403);
        }

        $comment->delete();
        ActivityLog::log($project, 'comment_deleted', Auth::user()->name . ' a supprimé un commentaire sur "' . $task->title . '".');
        return back()->with('success', 'Commentaire supprimé.');
    }
}
