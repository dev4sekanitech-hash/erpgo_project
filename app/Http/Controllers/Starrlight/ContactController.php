<?php

namespace App\Http\Controllers\Starrlight;

use App\Http\Controllers\Controller;
use App\Models\Starrlight\ContactMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('starrlight/contacts/index', [
            'contacts' => $messages,
        ]);
    }

    public function show($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->update(['is_read' => true]);

        return Inertia::render('starrlight/contacts/show', [
            'message' => $message,
        ]);
    }

    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        return redirect()->route('starrlight.contacts.index')->with('success', 'Message deleted successfully.');
    }
}
