<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\AppUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppNotificationController extends Controller
{
    /**
     * Get Notifications for Authenticated App User
     */
    public function index(Request $request): JsonResponse
    {
        /** @var AppUser $user */
        $user = $request->user();

        $notifications = AppNotification::where('app_user_id', $user->id)
            ->latest('id')
            ->get()
            ->map(function ($notif) {
                return [
                    'id'             => $notif->id,
                    'title'          => $notif->title,
                    'message'        => $notif->message,
                    'type'           => $notif->type,
                    'data'           => $notif->data,
                    'is_read'        => (bool) $notif->is_read,
                    'created_at'     => $notif->created_at->format('Y-m-d H:i:s'),
                    'time_formatted' => $notif->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success'      => true,
            'message'      => 'Notifications retrieved successfully.',
            'unread_count' => $notifications->where('is_read', false)->count(),
            'count'        => $notifications->count(),
            'data'         => $notifications,
        ], 200);
    }

    /**
     * Mark a Single Notification as Read
     */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        /** @var AppUser $user */
        $user = $request->user();

        $notification = AppNotification::where('app_user_id', $user->id)->find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found.'
            ], 404);
        }

        $notification->is_read = true;
        $notification->save();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.'
        ], 200);
    }

    /**
     * Mark All Notifications as Read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        /** @var AppUser $user */
        $user = $request->user();

        AppNotification::where('app_user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.'
        ], 200);
    }
}
