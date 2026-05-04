<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AdminNotificationController extends Controller
{
    /**
     * Get notifications for the current admin.
     */
    public function index(Request $request): View
    {
        $userId = auth()->id();
        
        $notifications = AdminNotification::where('user_id', $userId)
            ->when($request->get('unread') === 'true', function ($query) {
                $query->where('is_read', false);
            })
            ->latest()
            ->paginate(15);

        $unreadCount = AdminNotification::unreadCount($userId);

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Get recent notifications for the dropdown (JSON).
     */
    public function recent(): JsonResponse
    {
        $notifications = AdminNotification::where('user_id', auth()->id())
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => AdminNotification::unreadCount(auth()->id()),
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(AdminNotification $notification): JsonResponse
    {
        // Verify ownership
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        AdminNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    /**
     * Get unread count (JSON).
     */
    public function unreadCount(): JsonResponse
    {
        return response()->json([
            'count' => AdminNotification::unreadCount(auth()->id()),
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(AdminNotification $notification): JsonResponse
    {
        // Verify ownership
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }
}
