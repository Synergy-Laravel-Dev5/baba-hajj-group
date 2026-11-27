<?php

namespace App\Http\Controllers\MobileOps;

use App\Http\Controllers\Controller;
use App\Models\AppChat;
use App\Models\AppNotification;
use App\Models\AppUser;
use App\Models\PackageInquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * Display the Admin Live App Chat Portal
     */
    public function index(Request $request)
    {
        // 1. Auto-link any PackageInquiry without app_user_id to an AppUser
        $unlinkedInquiries = PackageInquiry::whereNull('app_user_id')->get();
        foreach ($unlinkedInquiries as $unlinked) {
            $user = AppUser::where('phone', $unlinked->contact)->first();
            if (!$user) {
                $user = AppUser::create([
                    'name'     => $unlinked->full_name ?: 'Pilgrim Guest',
                    'phone'    => $unlinked->contact,
                    'email'    => $unlinked->email,
                    'password' => bcrypt(Str::random(16)),
                    'status'   => 'active',
                ]);
            }
            $unlinked->update(['app_user_id' => $user->id]);
        }

        $selectedUserId = $request->query('user_id');
        $selectedInquiryId = $request->query('inquiry_id');

        if ($selectedInquiryId) {
            $targetInquiry = PackageInquiry::find($selectedInquiryId);
            if ($targetInquiry) {
                if (!$targetInquiry->app_user_id) {
                    $user = AppUser::firstOrCreate(
                        ['phone' => $targetInquiry->contact],
                        [
                            'name'     => $targetInquiry->full_name ?: 'Pilgrim Guest',
                            'email'    => $targetInquiry->email,
                            'password' => bcrypt(Str::random(16)),
                            'status'   => 'active',
                        ]
                    );
                    $targetInquiry->update(['app_user_id' => $user->id]);
                }
                $selectedUserId = $targetInquiry->app_user_id;
            }
        }

        // Fetch all app users who have chats OR bookings
        $users = AppUser::withCount(['unreadChats', 'chats', 'inquiries'])
            ->with([
                'chats' => function ($q) {
                    $q->latest('id')->take(1);
                },
                'inquiries' => function ($q) {
                    $q->latest('id')->with('package');
                },
            ])
            ->where(function ($q) {
                $q->has('chats')
                  ->orHas('inquiries');
            })
            ->orderByRaw('COALESCE(
                (SELECT MAX(created_at) FROM app_chats WHERE app_chats.app_user_id = app_users.id),
                (SELECT MAX(created_at) FROM package_inquiries WHERE package_inquiries.app_user_id = app_users.id),
                app_users.created_at
            ) DESC')
            ->get();

        if ($users->isEmpty()) {
            $users = AppUser::withCount(['unreadChats', 'chats', 'inquiries'])
                ->with([
                    'chats' => function ($q) {
                        $q->latest('id')->take(1);
                    },
                    'inquiries' => function ($q) {
                        $q->latest('id')->with('package');
                    },
                ])
                ->latest('id')
                ->get();
        }

        $selectedUser = null;
        if ($selectedUserId) {
            $selectedUser = AppUser::with(['inquiries' => function ($q) {
                $q->latest('id')->with('package');
            }])->find($selectedUserId);
        } elseif ($users->isNotEmpty()) {
            $selectedUser = $users->first();
        }

        $selectedInquiry = null;
        if ($selectedUser) {
            if ($selectedInquiryId) {
                $selectedInquiry = PackageInquiry::with('package')->find($selectedInquiryId);
            }
            if (!$selectedInquiry && $selectedUser->inquiries->isNotEmpty()) {
                $selectedInquiry = $selectedUser->inquiries->first();
            }

            // Mark user messages as read by admin
            AppChat::where('app_user_id', $selectedUser->id)
                ->where('sender_type', 'user')
                ->where('is_read', false)
                ->update(['is_read' => true]);

            $messages = AppChat::where('app_user_id', $selectedUser->id)
                ->with('admin')
                ->orderBy('id', 'asc')
                ->get();
        } else {
            $messages = collect();
        }

        $totalUnreadCount = AppChat::where('sender_type', 'user')
            ->where('is_read', false)
            ->count();

        return view('mobile-ops.chat.index', compact('users', 'selectedUser', 'selectedInquiry', 'messages', 'totalUnreadCount'));
    }

    /**
     * Get Messages for Active User via AJAX
     */
    public function getMessages(int $userId): JsonResponse
    {
        $user = AppUser::findOrFail($userId);

        // Mark as read
        AppChat::where('app_user_id', $user->id)
            ->where('sender_type', 'user')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = AppChat::where('app_user_id', $user->id)
            ->with('admin')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id'             => $msg->id,
                    'sender_type'    => $msg->sender_type,
                    'is_admin'       => $msg->sender_type === 'admin',
                    'message'        => $msg->message,
                    'attachment_url' => $msg->attachment_url,
                    'admin_name'     => $msg->admin?->name ?? 'Support Agent',
                    'created_at'     => $msg->created_at->format('h:i A, d M Y'),
                    'time_formatted' => $msg->created_at->format('h:i A'),
                ];
            });

        return response()->json([
            'success'  => true,
            'user'     => [
                'id'         => $user->id,
                'name'       => $user->name,
                'phone'      => $user->phone,
                'avatar_url' => $user->avatar_url,
            ],
            'messages' => $messages,
        ]);
    }

    /**
     * Send Message from Admin to App User
     */
    public function sendMessage(Request $request, int $userId)
    {
        $request->validate([
            'message'    => 'required_without:attachment|nullable|string|max:5000',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf,doc,docx|max:5120',
        ]);

        $user = AppUser::findOrFail($userId);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('app_chats/attachments', 'public');
        }

        $chat = AppChat::create([
            'app_user_id' => $user->id,
            'sender_type' => 'admin',
            'admin_id'    => auth()->id(),
            'message'     => $request->message ?? ($attachmentPath ? 'Sent an attachment' : ''),
            'attachment'  => $attachmentPath,
            'is_read'     => false,
        ]);

        // Send In-App Notification to User
        AppNotification::create([
            'app_user_id' => $user->id,
            'title'       => 'New message from Support',
            'message'     => \Illuminate\Support\Str::limit($chat->message, 80),
            'type'        => 'chat_message',
            'data'        => [
                'chat_id' => $chat->id,
            ],
            'is_read'     => false,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Message sent.',
                'data'    => [
                    'id'             => $chat->id,
                    'sender_type'    => 'admin',
                    'is_admin'       => true,
                    'message'        => $chat->message,
                    'attachment_url' => $chat->attachment_url,
                    'created_at'     => $chat->created_at->format('h:i A, d M Y'),
                    'time_formatted' => $chat->created_at->format('h:i A'),
                ]
            ]);
        }

        return redirect()->route('mobile-ops.chat.index', ['user_id' => $user->id])->with('success', 'Message sent.');
    }
}
