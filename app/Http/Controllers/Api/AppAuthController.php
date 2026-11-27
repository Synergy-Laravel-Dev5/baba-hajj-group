<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\AppUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AppAuthController extends Controller
{
    /**
     * Register a new Mobile App User
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:150',
            'phone'       => 'required|string|max:30|unique:app_users,phone',
            'email'       => 'nullable|email|max:150|unique:app_users,email',
            'password'    => 'required|string|min:6',
            'fcm_token'   => 'nullable|string|max:255',
            'device_name' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = AppUser::create([
            'name'           => trim($request->name),
            'phone'          => trim($request->phone),
            'email'          => !empty($request->email) ? trim(strtolower($request->email)) : null,
            'password'       => Hash::make($request->password),
            'fcm_token'      => $request->fcm_token,
            'device_name'    => $request->device_name ?? 'Mobile Device',
            'status'         => 'active',
            'last_active_at' => now(),
        ]);

        // Create Welcome Notification
        AppNotification::create([
            'app_user_id' => $user->id,
            'title'       => 'Welcome to Baba Hajj & Umrah Group',
            'message'     => 'Assalam-o-Alaikum ' . $user->name . '! Your mobile account has been created successfully. Explore our packages, Hajj guide, live videos, and feel free to chat with us anytime.',
            'type'        => 'announcement',
            'is_read'     => false,
        ]);

        $deviceName = $request->device_name ?: 'Mobile App';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful. Welcome to Baba Hajj Group!',
            'data'    => [
                'token'      => $token,
                'token_type' => 'Bearer',
                'user'       => [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'phone'      => $user->phone,
                    'email'      => $user->email,
                    'avatar_url' => $user->avatar_url,
                    'status'     => $user->status,
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                ]
            ]
        ], 201);
    }

    /**
     * Login for Mobile App User (via phone or email + password)
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'identifier'  => 'required|string', // accepts phone or email
            'password'    => 'required|string',
            'fcm_token'   => 'nullable|string|max:255',
            'device_name' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors()
            ], 422);
        }

        $identifier = trim($request->identifier);
        $cleanPhone = preg_replace('/[^0-9]/', '', $identifier);

        // Find user by email or by phone
        $user = AppUser::where(function ($q) use ($identifier, $cleanPhone) {
            $q->where('email', strtolower($identifier))
              ->orWhere('phone', $identifier);
            if (!empty($cleanPhone) && strlen($cleanPhone) >= 7) {
                $q->orWhereRaw("REGEXP_REPLACE(phone, '[^0-9]', '') = ?", [$cleanPhone]);
            }
        })->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No mobile account found with these credentials.'
            ], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password. Please check your credentials.'
            ], 401);
        }

        if ($user->status === 'suspended') {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been suspended. Please contact support.'
            ], 403);
        }

        // Update activity and fcm token
        $user->last_active_at = now();
        if ($request->filled('fcm_token')) {
            $user->fcm_token = $request->fcm_token;
        }
        if ($request->filled('device_name')) {
            $user->device_name = $request->device_name;
        }
        $user->save();

        $deviceName = $request->device_name ?: 'Mobile App';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data'    => [
                'token'      => $token,
                'token_type' => 'Bearer',
                'user'       => [
                    'id'                         => $user->id,
                    'name'                       => $user->name,
                    'phone'                      => $user->phone,
                    'email'                      => $user->email,
                    'avatar_url'                 => $user->avatar_url,
                    'status'                     => $user->status,
                    'unread_notifications_count' => $user->notifications()->where('is_read', false)->count(),
                    'unread_chats_count'         => $user->unreadChats()->count(),
                    'created_at'                 => $user->created_at->format('Y-m-d H:i:s'),
                ]
            ]
        ], 200);
    }

    /**
     * Get Current Authenticated User Profile
     */
    public function profile(Request $request): JsonResponse
    {
        /** @var AppUser $user */
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'User profile retrieved successfully.',
            'data'    => [
                'id'                         => $user->id,
                'name'                       => $user->name,
                'phone'                      => $user->phone,
                'email'                      => $user->email,
                'avatar_url'                 => $user->avatar_url,
                'status'                     => $user->status,
                'device_name'                => $user->device_name,
                'unread_notifications_count' => $user->notifications()->where('is_read', false)->count(),
                'unread_chats_count'         => $user->unreadChats()->count(),
                'total_bookings_count'       => $user->inquiries()->count(),
                'created_at'                 => $user->created_at->format('Y-m-d H:i:s'),
            ]
        ], 200);
    }

    /**
     * Update User Profile (Name, Email, Phone, Avatar, FCM token)
     */
    public function updateProfile(Request $request): JsonResponse
    {
        /** @var AppUser $user */
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name'      => 'nullable|string|max:150',
            'phone'     => 'nullable|string|max:30|unique:app_users,phone,' . $user->id,
            'email'     => 'nullable|email|max:150|unique:app_users,email,' . $user->id,
            'avatar'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'fcm_token' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors()
            ], 422);
        }

        if ($request->filled('name')) {
            $user->name = trim($request->name);
        }
        if ($request->filled('phone')) {
            $user->phone = trim($request->phone);
        }
        if ($request->filled('email')) {
            $user->email = trim(strtolower($request->email));
        }
        if ($request->filled('fcm_token')) {
            $user->fcm_token = $request->fcm_token;
        }

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('app_users/avatars', 'public');
            $user->avatar = $path;
        }

        $user->last_active_at = now();
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'phone'      => $user->phone,
                'email'      => $user->email,
                'avatar_url' => $user->avatar_url,
                'status'     => $user->status,
            ]
        ], 200);
    }

    /**
     * Change Password
     */
    public function changePassword(Request $request): JsonResponse
    {
        /** @var AppUser $user */
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors()
            ], 422);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.'
        ], 200);
    }

    /**
     * Invalidate Token (Logout)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.'
        ], 200);
    }
}
