<?php

namespace App\Http\Controllers;

use App\Models\KeepNotes;
use Illuminate\Http\Request;

/**
 * Notes API. Every route is behind auth:sanctum (see routes/api.php) and every
 * query is scoped to the signed-in user, so users can only read and change
 * their own notes.
 */
class KeepNotesController extends Controller
{
    public function addNote(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
        ]);

        $note = $request->user()->notes()->create([
            'title' => $data['title'],
            'content' => $data['content'] ?? '',
        ]);

        return response()->json($note, 201);
    }

    public function editNote(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
        ]);

        $note = $this->findOwnNote($request, $data['id']);
        $note->update([
            'title' => $data['title'],
            'content' => $data['content'] ?? '',
        ]);

        return response()->json($note);
    }

    /**
     * Reminder / archive / trash / delete-permanently actions.
     */
    public function editAction(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'integer'],
            'reminder' => ['nullable', 'date'],
            'archive' => ['nullable', 'date'],
            'trash' => ['nullable', 'date'],
            'deleted_at' => ['nullable', 'date'],
        ]);

        $note = $this->findOwnNote($request, $data['id']);
        $note->fill([
            'reminder' => $data['reminder'] ?? null,
            'archive' => $data['archive'] ?? null,
            'trash' => $data['trash'] ?? null,
        ]);
        // "Delete permanently" in the Trash view sends deleted_at (soft delete).
        $note->deleted_at = $data['deleted_at'] ?? null;
        $note->save();

        return response()->json($note);
    }

    public function getAllNotes(Request $request)
    {
        return $request->user()->notes()->get();
    }

    /**
     * Notes that are not in the trash.
     */
    public function getDepartment(Request $request)
    {
        return $request->user()->notes()->whereNull('trash')->get();
    }

    /**
     * Returns 404 when the note does not exist OR belongs to someone else,
     * so the API never reveals other users' notes.
     */
    private function findOwnNote(Request $request, int $id): KeepNotes
    {
        return $request->user()->notes()->findOrFail($id);
    }
}
