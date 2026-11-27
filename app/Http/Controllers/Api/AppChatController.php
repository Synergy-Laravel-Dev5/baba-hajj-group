<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppChat;
use App\Models\AppUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppChatController extends Controller
{
    /**
     * Get Conversation History for Authenticated App User
     */
    public function getMessages(Request $request): JsonResponse
    {
        /** @var AppUser $user */
        $user = $request->user();

        // Mark unread admin messages as read
        AppChat::where('app_user_id', $user->id)
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = AppChat::where('app_user_id', $user->id)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id'             => $msg->id,
                    'sender_type'    => $msg->sender_type, // 'user' or 'admin'
                    'is_me'          => $msg->sender_type === 'user',
                    'message'        => $msg->message,
                    'attachment_url' => $msg->attachment_url,
                    'is_read'        => (bool) $msg->is_read,
                    'created_at'     => $msg->created_at->format('Y-m-d H:i:s'),
                    'time_formatted' => $msg->created_at->format('h:i A'),
                    'date_formatted' => $msg->created_at->format('d M Y'),
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Chat messages retrieved successfully.',
            'count'   => $messages->count(),
            'data'    => $messages,
        ], 200);
    }

    /**
     * Send a Chat Message from User to Admin / Agency Support
     */
    public function sendMessage(Request $request): JsonResponse
    {
        /** @var AppUser $user */
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'message'    => 'required_without:attachment|nullable|string|max:5000',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf,doc,docx|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors()
            ], 422);
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('app_chats/attachments', 'public');
        }

        $chat = AppChat::create([
            'app_user_id' => $user->id,
            'sender_type' => 'user',
            'admin_id'    => null,
            'message'     => $request->message ?? ($attachmentPath ? 'Sent an attachment' : ''),
            'attachment'  => $attachmentPath,
            'is_read'     => false,
        ]);

        // Update user's last activity
        $user->last_active_at = now();
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully.',
            'data'    => [
                'id'             => $chat->id,
                'sender_type'    => $chat->sender_type,
                'is_me'          => true,
                'message'        => $chat->message,
                'attachment_url' => $chat->attachment_url,
                'is_read'        => false,
                'created_at'     => $chat->created_at->format('Y-m-d H:i:s'),
                'time_formatted' => $chat->created_at->format('h:i A'),
            ]
        ], 201);
    }

    /**
     * Mark Messages as Read
     */
    public function markAsRead(Request $request): JsonResponse
    {
        /** @var AppUser $user */
        $user = $request->user();

        $updated = AppChat::where('app_user_id', $user->id)
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Messages marked as read.',
            'updated' => $updated,
        ], 200);
    }
}
