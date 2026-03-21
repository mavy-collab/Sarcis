<?php

namespace App\Http\Controllers;

use App\Models\CalendarNote;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $year  = (int) $request->get('year',  now()->year);
        $month = (int) $request->get('month', now()->month);

        $current   = Carbon::createFromDate($year, $month, 1);
        $prevMonth = $current->copy()->subMonth();
        $nextMonth = $current->copy()->addMonth();

        $notes = CalendarNote::where('user_id', Auth::id())
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get()
            ->groupBy(fn($n) => $n->date->day);

        // Tâches avec deadline dans ce mois (assignées à l'utilisateur ou créées par lui)
        $taskDeadlines = Task::with('project')
            ->whereNotNull('due_date')
            ->whereYear('due_date', $year)
            ->whereMonth('due_date', $month)
            ->where(fn($q) => $q->where('assigned_to', Auth::id())
                                ->orWhere('created_by', Auth::id()))
            ->orderBy('due_date')
            ->get()
            ->groupBy(fn($t) => $t->due_date->day);

        return view('calendar.index', compact('current', 'prevMonth', 'nextMonth', 'notes', 'taskDeadlines'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date'    => 'required|date',
            'content' => 'required|string|max:1000',
            'color'   => 'nullable|in:indigo,emerald,rose,amber,violet,sky,teal,orange',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['color']   = $validated['color'] ?? 'indigo';

        CalendarNote::create($validated);

        $date = Carbon::parse($validated['date']);

        return redirect()->route('calendar.index', [
            'year'  => $date->year,
            'month' => $date->month,
        ])->with('success', 'Note ajoutée !');
    }

    public function destroy(CalendarNote $note)
    {
        if ($note->user_id !== Auth::id()) abort(403);
        $date = $note->date;
        $note->delete();

        return redirect()->route('calendar.index', [
            'year'  => $date->year,
            'month' => $date->month,
        ])->with('success', 'Note supprimée.');
    }
}
