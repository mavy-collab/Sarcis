<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Export – {{ $project->title }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #1e1e2e; background: #fff; font-size: 12px; }

        .header { background: #4f46e5; color: #ffffff; padding: 28px 36px; }
        .header h1 { font-size: 22px; font-weight: 700; margin-bottom: 4px; }
        .header p { font-size: 12px; color: #c7d2fe; margin-bottom: 10px; }
        .header-meta { font-size: 11px; color: #a5b4fc; }
        .header-meta span { margin-right: 16px; }

        .body { padding: 28px 36px; }

        .section { margin-bottom: 24px; }
        .section-title {
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .08em; color: #4f46e5;
            border-bottom: 1px solid #e5e7eb; padding-bottom: 6px; margin-bottom: 12px;
        }

        .progress-wrap { background: #e5e7eb; height: 8px; border-radius: 4px; margin-bottom: 4px; }
        .progress-fill { background: #4f46e5; height: 8px; border-radius: 4px; }
        .progress-label { font-size: 11px; color: #6b7280; }

        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th { background: #f3f4f6; text-align: left; padding: 7px 10px; font-weight: 700; color: #374151; border-bottom: 1px solid #d1d5db; }
        td { padding: 7px 10px; border-bottom: 1px solid #f0f0f0; color: #374151; vertical-align: top; }

        .badge { display: inline; border-radius: 10px; padding: 2px 8px; font-size: 10px; font-weight: 700; }
        .badge-todo        { background: #f3f4f6; color: #6b7280; }
        .badge-in_progress { background: #dbeafe; color: #1d4ed8; }
        .badge-review      { background: #fef3c7; color: #92400e; }
        .badge-done        { background: #d1fae5; color: #065f46; }
        .badge-urgent      { background: #fee2e2; color: #991b1b; }
        .badge-high        { background: #ffedd5; color: #9a3412; }
        .badge-medium      { background: #dbeafe; color: #1e40af; }
        .badge-low         { background: #d1fae5; color: #065f46; }

        .stats-table { width: 100%; border-collapse: collapse; }
        .stats-table td { text-align: center; padding: 12px; border: 1px solid #e5e7eb; }
        .stat-num { font-size: 22px; font-weight: 700; }
        .stat-label { font-size: 10px; color: #9ca3af; margin-top: 2px; }

        .footer { text-align: center; font-size: 10px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding: 14px 36px; margin-top: 8px; }
    </style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <h1>{{ $project->title }}</h1>
    @if($project->description)
        <p>{{ $project->description }}</p>
    @endif
    <div class="header-meta">
        <span>Statut : {{ $project->status_label }}</span>
        <span>Propriétaire : {{ $project->owner->name }}</span>
        @if($project->start_date)<span>Début : {{ $project->start_date->format('d/m/Y') }}</span>@endif
        @if($project->end_date)<span>Échéance : {{ $project->end_date->format('d/m/Y') }}</span>@endif
        <span>Exporté le {{ now()->format('d/m/Y à H:i') }}</span>
    </div>
</div>

<div class="body">

    {{-- Progression --}}
    <div class="section">
        <div class="section-title">Progression</div>
        <div class="progress-wrap">
            <div class="progress-fill" style="width:{{ $project->progress }}%"></div>
        </div>
        <div class="progress-label">{{ $project->progress }}% complété</div>
    </div>

    {{-- Stats --}}
    <div class="section">
        <div class="section-title">Résumé des tâches</div>
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stat-num" style="color:#6b7280">{{ $project->tasks->where('status','todo')->count() }}</div>
                    <div class="stat-label">À faire</div>
                </td>
                <td>
                    <div class="stat-num" style="color:#1d4ed8">{{ $project->tasks->where('status','in_progress')->count() }}</div>
                    <div class="stat-label">En cours</div>
                </td>
                <td>
                    <div class="stat-num" style="color:#92400e">{{ $project->tasks->where('status','review')->count() }}</div>
                    <div class="stat-label">En révision</div>
                </td>
                <td>
                    <div class="stat-num" style="color:#065f46">{{ $project->tasks->where('status','done')->count() }}</div>
                    <div class="stat-label">Terminées</div>
                </td>
                <td>
                    <div class="stat-num" style="color:#991b1b">{{ $project->tasks->where('is_overdue', true)->count() }}</div>
                    <div class="stat-label">En retard</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Tâches --}}
    <div class="section">
        <div class="section-title">Liste des tâches ({{ $project->tasks->count() }})</div>
        @if($project->tasks->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width:4%">#</th>
                    <th style="width:32%">Titre</th>
                    <th style="width:14%">Statut</th>
                    <th style="width:13%">Priorité</th>
                    <th style="width:20%">Assignée à</th>
                    <th style="width:17%">Échéance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($project->tasks as $i => $task)
                <tr>
                    <td style="color:#9ca3af">{{ $i + 1 }}</td>
                    <td>
                        <strong>{{ $task->title }}</strong>
                        @if($task->description)
                            <br><span style="color:#9ca3af;font-size:10px">{{ \Illuminate\Support\Str::limit($task->description, 80) }}</span>
                        @endif
                    </td>
                    <td><span class="badge badge-{{ $task->status }}">{{ $task->status_label }}</span></td>
                    <td><span class="badge badge-{{ $task->priority }}">{{ $task->priority_label }}</span></td>
                    <td>{{ $task->assignedTo?->name ?? '—' }}</td>
                    <td style="{{ $task->is_overdue ? 'color:#dc2626;font-weight:700' : '' }}">
                        {{ $task->due_date ? $task->due_date->format('d/m/Y') : '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <p style="color:#9ca3af;font-style:italic">Aucune tâche dans ce projet.</p>
        @endif
    </div>

    {{-- Membres --}}
    @if($project->members->count() > 0)
    <div class="section">
        <div class="section-title">Membres ({{ $project->members->count() }})</div>
        <table>
            <thead><tr><th>Nom</th><th>Email</th><th>Rôle</th></tr></thead>
            <tbody>
                @foreach($project->members as $member)
                <tr>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->email }}</td>
                    <td>{{ ucfirst($member->pivot->role) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Pièces jointes --}}
    @if($project->attachments->count() > 0)
    <div class="section">
        <div class="section-title">Pièces jointes ({{ $project->attachments->count() }})</div>
        <table>
            <thead><tr><th>Nom du fichier</th><th>Taille</th><th>Ajouté par</th><th>Date</th></tr></thead>
            <tbody>
                @foreach($project->attachments as $att)
                <tr>
                    <td>{{ $att->file_name }}</td>
                    <td>{{ $att->file_size_formatted }}</td>
                    <td>{{ $att->uploader?->name ?? '—' }}</td>
                    <td>{{ $att->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>

<div class="footer">
    Généré par {{ config('app.name') }} &nbsp;·&nbsp; {{ now()->format('d/m/Y à H:i') }}
</div>

</body>
</html>
