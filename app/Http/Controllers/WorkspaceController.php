<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkspaceController extends Controller
{
    public function index()
    {
        $workspaces = Workspace::where('user_id', Auth::id())
            ->withCount('projects')
            ->with('projects')
            ->latest()
            ->get();

        $workspaceCount = $workspaces->count();
        $maxWorkspaces  = Auth::user()->maxWorkspaces();

        return view('workspaces.index', compact('workspaces', 'workspaceCount', 'maxWorkspaces'));
    }

    public function create()
    {
        $user = Auth::user();
        $max  = $user->maxWorkspaces();

        if (Workspace::where('user_id', $user->id)->count() >= $max) {
            return redirect()->route('workspaces.index')
                ->with('limit_error', 'Limite atteinte : maximum ' . $max . ' espaces de travail sur le plan ' . $user->planLabel() . '. Passez au plan Pro pour en créer davantage.');
        }

        $backgrounds = Workspace::availableBackgrounds();
        return view('workspaces.create', compact('backgrounds'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $max  = $user->maxWorkspaces();

        if (Workspace::where('user_id', $user->id)->count() >= $max) {
            return redirect()->route('workspaces.index')
                ->with('limit_error', 'Limite atteinte : maximum ' . $max . ' espaces de travail sur le plan ' . $user->planLabel() . '.');
        }

        $validKeys = implode(',', array_column(Workspace::availableBackgrounds(), 'key'));

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'color'            => 'required|in:indigo,emerald,rose,amber,violet,sky,teal,orange',
            'background_image' => 'nullable|in:' . $validKeys,
        ], [
            'name.required' => 'Le nom de l\'espace de travail est obligatoire.',
        ]);

        $validated['user_id'] = Auth::id();

        $workspace = Workspace::create($validated);

        return redirect()->route('workspaces.show', $workspace)
            ->with('success', 'Espace de travail créé avec succès !');
    }

    public function show(Workspace $workspace)
    {
        $this->authorizeOwner($workspace);
        $workspace->load(['projects' => fn($q) => $q->withCount('tasks')->latest()]);
        $maxProjects = Auth::user()->maxProjectsPerWorkspace();
        return view('workspaces.show', compact('workspace', 'maxProjects'));
    }

    public function edit(Workspace $workspace)
    {
        $this->authorizeOwner($workspace);
        $backgrounds = Workspace::availableBackgrounds();
        return view('workspaces.edit', compact('workspace', 'backgrounds'));
    }

    public function update(Request $request, Workspace $workspace)
    {
        $this->authorizeOwner($workspace);

        $validKeys = implode(',', array_column(Workspace::availableBackgrounds(), 'key'));

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'color'            => 'required|in:indigo,emerald,rose,amber,violet,sky,teal,orange',
            'background_image' => 'nullable|in:' . $validKeys,
        ]);

        $workspace->update($validated);

        return redirect()->route('workspaces.show', $workspace)
            ->with('success', 'Espace de travail mis à jour !');
    }

    public function destroy(Workspace $workspace)
    {
        $this->authorizeOwner($workspace);

        // Notifier les membres de chaque projet avant suppression
        $workspace->load('projects.members');
        foreach ($workspace->projects as $project) {
            foreach ($project->members as $member) {
                UserNotification::notify($member->id, 'project_deleted', [
                    'message'        => 'Le projet "' . $project->title . '" a été supprimé suite à la suppression de l\'espace de travail "' . $workspace->name . '".',
                    'project_title'  => $project->title,
                    'workspace_name' => $workspace->name,
                    'url'            => route('projects.index'),
                ]);
            }
            $project->delete();
        }

        $workspace->delete();
        return redirect()->route('workspaces.index')
            ->with('success', 'Espace de travail supprimé.');
    }

    private function authorizeOwner(Workspace $workspace)
    {
        if ($workspace->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
