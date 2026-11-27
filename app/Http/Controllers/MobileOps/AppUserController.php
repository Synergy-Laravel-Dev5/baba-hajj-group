<?php

namespace App\Http\Controllers\MobileOps;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use Illuminate\Http\Request;

class AppUserController extends Controller
{
    /**
     * Display list of Mobile App Registered Users
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $query = AppUser::withCount(['inquiries', 'chats', 'notifications'])
            ->latest('id');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $term = '%' . trim($search) . '%';
                $q->where('name', 'like', $term)
                  ->orWhere('phone', 'like', $term)
                  ->orWhere('email', 'like', $term);
            });
        }

        if (!empty($status) && in_array($status, ['active', 'suspended', 'inactive'])) {
            $query->where('status', $status);
        }

        $users = $query->paginate(15);

        $counts = [
            'total'     => AppUser::count(),
            'active'    => AppUser::where('status', 'active')->count(),
            'suspended' => AppUser::where('status', 'suspended')->count(),
        ];

        return view('mobile-ops.users.index', compact('users', 'counts', 'search', 'status'));
    }

    /**
     * Toggle status (active / suspended)
     */
    public function toggleStatus(Request $request, int $id)
    {
        $user = AppUser::findOrFail($id);
        $user->status = ($user->status === 'active') ? 'suspended' : 'active';
        $user->save();

        return back()->with('success', "User account status updated to {$user->status}.");
    }
}
