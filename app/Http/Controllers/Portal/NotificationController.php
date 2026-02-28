<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Show all notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        $query = Auth::user()->notifications()->latest();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by read status
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->status === 'read') {
                $query->where('is_read', true);
            }
        }

        $notifications = $query->paginate(20)->withQueryString();
        $unreadCount   = Auth::user()->notifications()->where('is_read', false)->count();

        return view('portal.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark a single notification as read (AJAX or redirect).
     */
    public function markRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllRead()
    {
        Auth::user()->notifications()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'All notifications marked as read.']);
        }

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a single notification.
     */
    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notification deleted.');
    }

    /**
     * Delete all read notifications.
     */
    public function destroyRead()
    {
        Auth::user()->notifications()
            ->where('is_read', true)
            ->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Read notifications cleared.');
    }

    /**
     * Return unread count as JSON (for polling or page refresh).
     */
    public function unreadCount()
    {
        return response()->json([
            'count' => Auth::user()->notifications()->where('is_read', false)->count(),
        ]);
    }

    /**
     * Manually trigger notification generation for the current user (used from Settings).
     */
    public function generateNow(NotificationService $service)
    {
        $user = Auth::user();

        // Clear the today-dedup by deleting TODAY's unread notifications first
        // so the user can get a fresh set when clicking "Check Now"
        $user->notifications()
            ->whereDate('created_at', today())
            ->where('is_read', false)
            ->delete();

        $service->generateForUser($user);

        $newCount = $user->notifications()->where('is_read', false)->count();

        if (request()->expectsJson()) {
            return response()->json([
                'success'   => true,
                'new_count' => $newCount,
                'message'   => $newCount > 0
                    ? "Found {$newCount} new notification(s) for you."
                    : 'Everything looks good — no new alerts right now.',
            ]);
        }

        $msg = $newCount > 0
            ? "Notifications refreshed! {$newCount} new notification(s) found."
            : 'Everything looks good — no new alerts at this time.';

        return redirect()->route('notifications.index')->with('success', $msg);
    }
}
