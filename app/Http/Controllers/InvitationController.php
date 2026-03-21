<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ProjectInvitation;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    public function show(ProjectInvitation $invitation)
    {
        // Seul l'invité peut voir son invitation
        if ($invitation->invited_user_id !== Auth::id()) {
            abort(403);
        }

        if (!$invitation->isPending()) {
            return redirect()->route('projects.index')
                ->with('info', 'Cette invitation n\'est plus valide.');
        }

        $invitation->load(['project', 'inviter']);

        return view('invitations.show', compact('invitation'));
    }

    public function accept(ProjectInvitation $invitation)
    {
        if ($invitation->invited_user_id !== Auth::id()) {
            abort(403);
        }

        if (!$invitation->isPending()) {
            return redirect()->route('projects.index')
                ->with('info', 'Cette invitation n\'est plus valide.');
        }

        $project = $invitation->project;

        // Vérifier la limite de membres
        $maxMembers = Auth::user()->maxMembersPerProject();
        if ($project->members()->count() >= $maxMembers) {
            $invitation->update(['status' => 'declined']);
            return redirect()->route('projects.index')
                ->with('error', 'Le projet a atteint la limite de membres.');
        }

        // Ajouter le membre au projet
        $project->members()->syncWithoutDetaching([
            Auth::id() => ['role' => $invitation->role],
        ]);

        $invitation->update(['status' => 'accepted']);

        ActivityLog::log($project, 'member_added', Auth::user()->name . ' a rejoint le projet.');

        // Notifier le créateur de l'invitation
        UserNotification::notify($invitation->inviter_id, 'invitation_accepted', [
            'message'       => Auth::user()->name . ' a accepté votre invitation pour le projet "' . $project->title . '".',
            'project_id'    => $project->id,
            'project_title' => $project->title,
            'url'           => route('projects.show', $project),
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Vous avez rejoint le projet "' . $project->title . '".');
    }

    public function decline(ProjectInvitation $invitation)
    {
        if ($invitation->invited_user_id !== Auth::id()) {
            abort(403);
        }

        if (!$invitation->isPending()) {
            return redirect()->route('projects.index')
                ->with('info', 'Cette invitation n\'est plus valide.');
        }

        $invitation->update(['status' => 'declined']);

        // Notifier le créateur de l'invitation
        UserNotification::notify($invitation->inviter_id, 'invitation_declined', [
            'message'       => Auth::user()->name . ' a refusé votre invitation pour le projet "' . $invitation->project->title . '".',
            'project_id'    => $invitation->project_id,
            'project_title' => $invitation->project->title,
            'url'           => route('projects.show', $invitation->project_id),
        ]);

        return redirect()->route('projects.index')
            ->with('info', 'Invitation refusée.');
    }
}
