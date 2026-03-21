<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Attachment;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $this->authorizeProjectAccess($project);
        if (!$project->hasPro()) {
            return back()->with('error', 'Les pièces jointes sont disponibles uniquement sur le plan Pro du propriétaire du projet.');
        }

        $request->validate([
            'file' => [
                'required', 'file', 'max:20480',
                'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,zip,rar',
            ],
        ], [
            'file.required' => 'Veuillez sélectionner un fichier.',
            'file.max'      => 'Le fichier ne doit pas dépasser 20 Mo.',
            'file.mimes'    => 'Type de fichier non autorisé.',
        ]);

        // Double-check: block dangerous extensions regardless of MIME
        $ext = strtolower($request->file('file')->getClientOriginalExtension());
        $blocked = ['php', 'php3', 'php4', 'php5', 'phtml', 'exe', 'bat', 'sh', 'js', 'html', 'htm', 'svg', 'xml', 'py', 'rb', 'pl'];
        if (in_array($ext, $blocked)) {
            return back()->withErrors(['file' => 'Ce type de fichier est interdit.']);
        }

        $file      = $request->file('file');
        $fileName  = $file->getClientOriginalName();
        // Renommer le fichier avec un UUID pour éviter l'énumération et les conflits
        $safeName  = Str::uuid() . '.' . $ext;
        $path      = $file->storeAs('attachments/' . $project->id, $safeName, 'public');

        $project->attachments()->create([
            'file_name' => $fileName,
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'user_id'   => Auth::id(),
        ]);

        ActivityLog::log($project, 'attachment_added', Auth::user()->name . ' a ajouté le fichier "' . $fileName . '".');

        return back()->with('success', 'Fichier uploadé avec succès.');
    }

    public function download(Project $project, Attachment $attachment)
    {
        $this->authorizeProjectAccess($project);

        if ($attachment->attachable_type !== Project::class || $attachment->attachable_id !== $project->id) {
            abort(404);
        }

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }

    public function destroy(Project $project, Attachment $attachment)
    {
        $this->authorizeProjectAccess($project);

        // IDOR : vérifier que la pièce jointe appartient bien à ce projet
        if ($attachment->attachable_type !== Project::class || $attachment->attachable_id !== $project->id) {
            abort(404);
        }

        if ($attachment->user_id !== Auth::id() && $project->user_id !== Auth::id()) {
            abort(403);
        }

        $fileName = $attachment->file_name;
        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        ActivityLog::log($project, 'attachment_deleted', Auth::user()->name . ' a supprimé le fichier "' . $fileName . '".');

        return back()->with('success', 'Pièce jointe supprimée.');
    }
}
