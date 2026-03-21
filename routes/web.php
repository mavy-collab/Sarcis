<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\SubTaskController;
use App\Http\Controllers\TimeLogController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

// Page d'accueil publique
Route::get('/', function () {
    return view('home');
})->name('home');

// Pages publiques
Route::get('/a-propos', fn() => view('about'))->name('about');
Route::get('/fonctionnalites', fn() => view('features'))->name('features');

// Sitemap
Route::get('/sitemap.xml', function () {
    return response()->view('sitemap')->header('Content-Type', 'application/xml');
});


// Auth
Route::middleware('guest')->group(function () {
    Route::get('/inscription', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/inscription', [RegisterController::class, 'register']);
    Route::get('/connexion', [LoginController::class, 'showForm'])->name('login');
    Route::post('/connexion', [LoginController::class, 'login']);
    Route::get('/mot-de-passe-oublie', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/mot-de-passe-oublie', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reinitialiser/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reinitialiser', [PasswordResetController::class, 'reset'])->name('password.update');
});

// Google OAuth (accessible sans être connecté ou déconnecté)
Route::get('/auth/google',          [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::post('/deconnexion', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Vérification email
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', fn() => view('auth.verify-email'))->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('dashboard')->with('success', 'Email vérifié avec succès !');
    })->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Un nouvel email de vérification a été envoyé.');
    })->middleware('throttle:6,1')->name('verification.send');
});

// Zone admin
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',               [AdminController::class, 'dashboard'])     ->name('dashboard');
    Route::get('/users',          [AdminController::class, 'users'])         ->name('users');
    Route::delete('/users/{user}',[AdminController::class, 'destroyUser'])   ->name('users.destroy');
    Route::get('/projects',              [AdminController::class, 'projects'])      ->name('projects');
    Route::delete('/projects/{project}', [AdminController::class, 'destroyProject'])->name('projects.destroy');
    Route::get('/tasks',                 [AdminController::class, 'tasks'])         ->name('tasks');
    Route::get('/security-logs',         [AdminController::class, 'securityLogs'])  ->name('security_logs');
    Route::get('/contact',               [AdminController::class, 'contactMessages'])->name('contact');
    Route::patch('/contact/{contactMessage}/close',  [AdminController::class, 'closeContact'])  ->name('contact.close');
    Route::patch('/contact/{contactMessage}/reopen', [AdminController::class, 'reopenContact']) ->name('contact.reopen');
    Route::delete('/contact/{contactMessage}',       [AdminController::class, 'destroyContact'])->name('contact.destroy');
});

// Zone protégée
Route::middleware(['auth', 'verified', 'throttle:120,1'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil utilisateur
    Route::get('/profile',          [ProfileController::class, 'show'])          ->name('profile.show');
    Route::put('/profile/info',     [ProfileController::class, 'updateInfo'])    ->name('profile.updateInfo');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::delete('/profile',       [ProfileController::class, 'destroy'])        ->name('profile.destroy');

    // Notifications
    Route::get('/notifications/stream', [NotificationController::class, 'stream'])->name('notifications.stream');
    Route::get('/notifications/poll', [NotificationController::class, 'poll'])->name('notifications.poll');
    Route::get('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Contact
    Route::get('/contact',  [ContactController::class, 'create'])->name('contact.create');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

    // Calendrier
    Route::get('/calendrier', [CalendarController::class, 'index'])->name('calendar.index');
    Route::post('/calendrier/notes', [CalendarController::class, 'store'])->name('calendar.store');
    Route::delete('/calendrier/notes/{note}', [CalendarController::class, 'destroy'])->name('calendar.destroy');

    // Invitations
    Route::get('/invitations/{invitation}', [InvitationController::class, 'show'])->name('invitations.show');
    Route::post('/invitations/{invitation}/accept', [InvitationController::class, 'accept'])->name('invitations.accept');
    Route::post('/invitations/{invitation}/decline', [InvitationController::class, 'decline'])->name('invitations.decline');

    // Espaces de travail
    Route::resource('workspaces', WorkspaceController::class);

    // Plans
    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
    Route::post('/plans/upgrade', [PlanController::class, 'upgrade'])->name('plans.upgrade');
    Route::post('/plans/downgrade', [PlanController::class, 'downgrade'])->name('plans.downgrade');
    Route::post('/plans/trial', [PlanController::class, 'activateTrial'])->name('plans.trial');

    // Projets
    Route::resource('projects', ProjectController::class);
    Route::patch('/projects/{project}/status', [ProjectController::class, 'updateStatus'])->name('projects.updateStatus');
    Route::patch('/projects/{project}/toggle-lock', [ProjectController::class, 'toggleLock'])->name('projects.toggleLock');
    Route::post('/projects/{project}/members', [ProjectController::class, 'addMember'])->name('projects.members.add');
    Route::delete('/projects/{project}/members/{user}', [ProjectController::class, 'removeMember'])->name('projects.members.remove');
    Route::get('/projects/{project}/export', [ProjectController::class, 'export'])->name('projects.export');
    Route::get('/projects/{project}/gantt', [ProjectController::class, 'gantt'])->name('projects.gantt');
    Route::get('/projects/{project}/report', [ProjectController::class, 'report'])->name('projects.report');
    Route::get('/projects/{project}/activity', [ProjectController::class, 'activity'])->name('projects.activity');

    // Pièces jointes (projet)
    Route::post('/projects/{project}/attachments', [AttachmentController::class, 'store'])->name('projects.attachments.store');
    Route::get('/projects/{project}/attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('projects.attachments.download');
    Route::delete('/projects/{project}/attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('projects.attachments.destroy');

    // Labels (projet)
    Route::post('/projects/{project}/labels', [LabelController::class, 'store'])->name('projects.labels.store');
    Route::delete('/projects/{project}/labels/{label}', [LabelController::class, 'destroy'])->name('projects.labels.destroy');

    // Tâches (imbriquées dans les projets)
    Route::prefix('projects/{project}/tasks')->name('projects.tasks.')->group(function () {
        Route::post('/', [TaskController::class, 'store'])->name('store');
        Route::get('/{task}', [TaskController::class, 'show'])->name('show');
        Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('edit');
        Route::put('/{task}', [TaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
        Route::patch('/{task}/status', [TaskController::class, 'updateStatus'])->name('status');

        // Commentaires
        Route::post('/{task}/comments', [CommentController::class, 'store'])->name('comments.store');
        Route::delete('/{task}/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

        // Sous-tâches
        Route::post('/{task}/subtasks', [SubTaskController::class, 'store'])->name('subtasks.store');
        Route::patch('/{task}/subtasks/{subTask}/toggle', [SubTaskController::class, 'toggle'])->name('subtasks.toggle');
        Route::delete('/{task}/subtasks/{subTask}', [SubTaskController::class, 'destroy'])->name('subtasks.destroy');

        // Suivi du temps
        Route::post('/{task}/timelogs', [TimeLogController::class, 'store'])->name('timelogs.store');
        Route::delete('/{task}/timelogs/{timeLog}', [TimeLogController::class, 'destroy'])->name('timelogs.destroy');

        // Labels sur tâche
        Route::post('/{task}/labels/attach', [LabelController::class, 'attach'])->name('labels.attach');
        Route::delete('/{task}/labels/{label}/detach', [LabelController::class, 'detach'])->name('labels.detach');
    });
});
