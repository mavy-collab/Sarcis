<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\SecurityLog;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalTasks    = Task::count();
        $completedTasks = Task::where('status', 'done')->count();
        $storageByte   = Attachment::sum('file_size');

        $stats = [
            'total_users'        => User::count(),
            'total_projects'     => Project::count(),
            'total_tasks'        => $totalTasks,
            'total_workspaces'   => Workspace::count(),
            'active_projects'    => Project::where('status', 'active')->count(),
            'completed_tasks'    => $completedTasks,
            'admins'             => User::where('is_admin', true)->count(),
            'new_users_month'    => User::whereMonth('created_at', now()->month)->count(),
            'new_users_today'    => User::whereDate('created_at', today())->count(),
            'unverified_users'   => User::whereNull('email_verified_at')->count(),
            'google_users'       => User::whereNotNull('google_id')->count(),
            'email_users'        => User::whereNull('google_id')->count(),
            'completion_rate'    => $totalTasks > 0 ? round($completedTasks / $totalTasks * 100) : 0,
            'storage_mb'         => round($storageByte / 1048576, 1),
            'failed_logins_today'=> SecurityLog::where('event', 'login_failed')->whereDate('created_at', today())->count(),
        ];

        $recentUsers         = User::latest()->take(5)->get();
        $recentProjects      = Project::with('owner')->latest()->take(5)->get();
        $openContactsCount   = ContactMessage::where('status', 'open')->count();
        $recentFailedLogins  = SecurityLog::where('event', 'login_failed')
                                ->latest('created_at')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentProjects', 'openContactsCount', 'recentFailedLogins'));
    }

    public function users(Request $request)
    {
        $search = $request->input('search');

        $users = User::withCount(['projects', 'memberProjects'])
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%")
                                        ->orWhere('email', 'like', "%$search%"))
            ->latest()
            ->paginate(15);

        return view('admin.users', compact('users', 'search'));
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $email = $user->email;
        $name  = $user->name;

        // La suppression cascade : projets, espaces, tâches, commentaires, notifications
        $user->delete();

        SecurityLog::log(
            'admin_user_deleted',
            'Admin ' . auth()->user()->email . ' a supprimé le compte de ' . $name . ' (' . $email . ')',
            auth()->id()
        );

        return back()->with('success', "L'utilisateur {$name} a été supprimé.");
    }

    public function projects(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $projects = Project::with('owner', 'workspace')
            ->withCount('tasks')
            ->when($search, fn($q) => $q->where('title', 'like', "%$search%"))
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15);

        return view('admin.projects', compact('projects', 'search', 'status'));
    }

    public function destroyProject(Project $project)
    {
        $project->delete();

        return back()->with('success', 'Le projet a été supprimé.');
    }

    public function securityLogs(Request $request)
    {
        $event = $request->input('event');
        $search = $request->input('search');

        $logs = SecurityLog::with('user')
            ->when($event, fn($q) => $q->where('event', $event))
            ->when($search, fn($q) => $q->where('ip_address', 'like', "%$search%")
                                        ->orWhere('details', 'like', "%$search%"))
            ->latest('created_at')
            ->paginate(50);

        $events = SecurityLog::select('event')->distinct()->pluck('event');

        return view('admin.security_logs', compact('logs', 'events', 'event', 'search'));
    }

    public function tasks(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $priority = $request->input('priority');

        $tasks = Task::with(['project', 'assignedTo'])
            ->when($search, fn($q) => $q->where('title', 'like', "%$search%"))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($priority, fn($q) => $q->where('priority', $priority))
            ->latest()
            ->paginate(20);

        return view('admin.tasks', compact('tasks', 'search', 'status', 'priority'));
    }

    public function contactMessages(Request $request)
    {
        $status = $request->input('status', 'open');

        $messages = ContactMessage::with('user')
            ->where('status', $status)
            ->latest()
            ->paginate(20);

        $openCount = ContactMessage::where('status', 'open')->count();

        return view('admin.contact', compact('messages', 'openCount'));
    }

    public function closeContact(ContactMessage $contactMessage)
    {
        $contactMessage->update(['status' => 'closed']);
        return back()->with('success', 'Message marqué comme traité.');
    }

    public function reopenContact(ContactMessage $contactMessage)
    {
        $contactMessage->update(['status' => 'open']);
        return back()->with('success', 'Message réouvert.');
    }

    public function destroyContact(ContactMessage $contactMessage)
    {
        $contactMessage->delete();
        return back()->with('success', 'Message supprimé.');
    }
}
