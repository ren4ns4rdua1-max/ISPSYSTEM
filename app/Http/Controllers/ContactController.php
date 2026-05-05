<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\AdminNotification;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        // Rate limit: max 3 messages per email address
        $count = ContactMessage::countByEmail($validated['email']);
        if ($count >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'You have reached the maximum of 3 messages from this email address.',
            ], 429);
        }

        $msg = ContactMessage::create($validated);

        // Notify admins
        AdminNotification::notifyAdmins(
            'contact_message',
            'New Contact Message',
            "{$validated['name']} ({$validated['email']}) sent a message: " . \Illuminate\Support\Str::limit($validated['message'], 80),
            ['contact_message_id' => $msg->id, 'email' => $validated['email']]
        );

        return response()->json([
            'success'   => true,
            'message'   => 'Message sent! We\'ll get back to you within 24 hours.',
            'remaining' => 3 - ($count + 1),
        ]);
    }

    // Admin: list all messages
    public function index(): \Illuminate\View\View
    {
        $messages = ContactMessage::latest()->paginate(20);
        $unread   = ContactMessage::where('is_read', false)->count();
        return view('contact.index', compact('messages', 'unread'));
    }

    // Admin: mark as read
    public function markRead(ContactMessage $message): \Illuminate\Http\JsonResponse
    {
        $message->markAsRead();
        return response()->json(['success' => true]);
    }

    // Admin: delete
    public function destroy(ContactMessage $message): \Illuminate\Http\RedirectResponse
    {
        $message->delete();
        return back()->with('success', 'Message deleted.');
    }
}
