<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mark one notification as read and redirect to its URL.
     */
    public function read(UserNotification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        $data = $notification->data ?? [];

        // Si la notification pointe vers une tâche qui n'existe plus, rediriger vers le projet
        if (!empty($data['task_id']) && !Task::find($data['task_id'])) {
            if (!empty($data['project_id'])) {
                return redirect()->route('projects.show', $data['project_id'])
                    ->with('info', 'Cette tâche a été supprimée.');
            }
            return redirect()->route('projects.index')
                ->with('info', 'Cette tâche a été supprimée.');
        }

        return redirect($notification->url);
    }

    /**
     * Mark all notifications as read.
     */
    public function readAll()
    {
        UserNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'Toutes les notifications ont été lues.');
    }

    /**
     * Return notifications as JSON for polling.
     */
    public function poll()
    {
        $notifications = UserNotification::where('user_id', Auth::id())
            ->latest()->take(10)->get()
            ->map(fn($n) => [
                'id'         => $n->id,
                'message'    => $n->message,
                'icon'       => $n->icon,
                'is_read'    => $n->isRead(),
                'type'       => $n->type,
                'time'       => $n->created_at->diffForHumans(),
                'read_url'   => route('notifications.read', $n),
                'delete_url' => route('notifications.destroy', $n),
            ]);

        return response()->json([
            'unread_count' => UserNotification::where('user_id', Auth::id())
                ->whereNull('read_at')->count(),
            'notifications' => $notifications,
        ]);
    }

    /**
     * Server-Sent Events stream for real-time notifications.
     */
    public function stream(Request $request)
    {
        $userId = Auth::id();

        return response()->stream(function () use ($userId) {
            $startTime  = time();
            $maxSeconds = 28;
            $lastId     = (int) $request->header('Last-Event-ID', 0);

            while (true) {
                if (connection_aborted() || (time() - $startTime) >= $maxSeconds) {
                    break;
                }

                $notifications = UserNotification::where('user_id', $userId)
                    ->latest()->take(10)->get();

                $unreadCount = UserNotification::where('user_id', $userId)
                    ->whereNull('read_at')->count();

                $latestId = $notifications->isNotEmpty() ? $notifications->first()->id : 0;
                $hasNew   = $latestId > $lastId;

                $payload = json_encode([
                    'unread_count'  => $unreadCount,
                    'notifications' => $notifications->map(fn($n) => [
                        'id'         => $n->id,
                        'message'    => $n->message,
                        'icon'       => $n->icon,
                        'is_read'    => $n->isRead(),
                        'type'       => $n->type,
                        'time'       => $n->created_at->diffForHumans(),
                        'read_url'   => route('notifications.read', $n),
                        'delete_url' => route('notifications.destroy', $n),
                    ]),
                    'has_new' => $hasNew,
                ]);

                echo "id: {$latestId}\n";
                echo "event: " . ($hasNew ? 'new_notification' : 'heartbeat') . "\n";
                echo "data: {$payload}\n\n";

                if (ob_get_level() > 0) ob_flush();
                flush();

                $lastId = $latestId;
                sleep(3);
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection'        => 'keep-alive',
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(UserNotification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->delete();

        return back();
    }
}
