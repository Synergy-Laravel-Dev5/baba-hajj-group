<?php

namespace App\Http\Controllers\MobileOps;

use App\Http\Controllers\Controller;
use App\Models\AppChat;
use App\Models\AppNotification;
use App\Models\AppUser;
use App\Models\Gallery;
use App\Models\HajjBook;
use App\Models\HujjajGuideline;
use App\Models\PackageInquiry;
use App\Models\TutorialVideo;
use App\Models\VisaInquiry;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'inquiries_pending'       => PackageInquiry::where('status', 'pending')->count(),
            'inquiries_contacted'     => PackageInquiry::where('status', 'contacted')->count(),
            'inquiries_closed'        => PackageInquiry::where('status', 'closed')->count(),
            'inquiries_total'         => PackageInquiry::count(),

            'app_users_total'         => AppUser::count(),
            'app_users_active'        => AppUser::where('status', 'active')->count(),
            'app_chats_unread'        => AppChat::where('sender_type', 'user')->where('is_read', false)->count(),

            'hajj_packages_total'     => \App\Models\Package::where('package_type', 'hajj')->count(),
            'umrah_packages_total'    => \App\Models\Package::where('package_type', 'umrah')->count(),

            'visa_inquiries_pending'   => VisaInquiry::where('status', 'pending')->count(),
            'visa_inquiries_contacted' => VisaInquiry::where('status', 'contacted')->count(),
            'visa_inquiries_progress'  => VisaInquiry::where('status', 'in_progress')->count(),
            'visa_inquiries_completed' => VisaInquiry::where('status', 'completed')->count(),
            'visa_inquiries_total'     => VisaInquiry::count(),

            'ziarat_total'            => Gallery::count(),
            'books_total'             => HajjBook::count(),
            'guidelines_total'        => HujjajGuideline::count(),
            'videos_total'            => TutorialVideo::count(),
        ];

        $recentInquiries = PackageInquiry::with(['package', 'appUser'])->latest()->take(5)->get();
        $recentVisaInquiries = VisaInquiry::with(['country', 'visaType'])->latest()->take(5)->get();
        $recentUsers = AppUser::latest()->take(5)->get();

        return view('mobile-ops.dashboard', compact('stats', 'recentInquiries', 'recentVisaInquiries', 'recentUsers'));
    }

    public function inquiries(Request $request)
    {
        $status = $request->get('status');

        $query = PackageInquiry::with(['package', 'appUser'])->latest();

        if (!empty($status) && in_array($status, ['pending', 'contacted', 'closed'])) {
            $query->where('status', $status);
        }

        $inquiries = $query->paginate(15);

        $counts = [
            'all'       => PackageInquiry::count(),
            'pending'   => PackageInquiry::where('status', 'pending')->count(),
            'contacted' => PackageInquiry::where('status', 'contacted')->count(),
            'closed'    => PackageInquiry::where('status', 'closed')->count(),
        ];

        return view('mobile-ops.inquiries.index', compact('inquiries', 'counts', 'status'));
    }

    /**
     * Update inquiry operational status
     */
    public function updateInquiryStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,contacted,closed',
        ]);

        $inquiry = PackageInquiry::findOrFail($id);
        $inquiry->status = $request->status;
        $inquiry->save();

        return back()->with('success', 'Inquiry status updated successfully.');
    }

    /**
     * Confirm Booking & Dispatch WhatsApp Notification
     */
    public function confirmBooking(Request $request, $id)
    {
        $inquiry = PackageInquiry::with(['package', 'appUser'])->findOrFail($id);

        $inquiry->status = 'closed'; // mark as confirmed / closed
        if ($request->filled('admin_notes')) {
            $inquiry->admin_notes = $request->admin_notes;
        }
        $inquiry->save();

        // 1. Send In-App Notification if user is linked or exists with same phone
        $appUser = $inquiry->appUser ?? AppUser::where('phone', $inquiry->contact)->first();
        if ($appUser) {
            AppNotification::create([
                'app_user_id' => $appUser->id,
                'title'       => '🎉 Booking Confirmed!',
                'message'     => 'Your booking #' . $inquiry->id . ' for package "' . ($inquiry->package?->package_title ?? 'Package') . '" has been CONFIRMED by Baba Hajj Group. Check your WhatsApp for full details.',
                'type'        => 'booking_confirmation',
                'data'        => [
                    'inquiry_id' => $inquiry->id,
                    'package_id' => $inquiry->package_id,
                    'status'     => 'confirmed',
                ],
                'is_read'     => false,
            ]);

            // Also post confirmation message into In-App Live Chat
            $pkgTitleForChat = $inquiry->package?->package_title ?? 'Hajj/Umrah Package';
            AppChat::create([
                'app_user_id' => $appUser->id,
                'sender_type' => 'admin',
                'admin_id'    => auth()->id(),
                'message'     => "🎉 *BOOKING CONFIRMED* (Booking #{$inquiry->id})\nAssalam-o-Alaikum {$inquiry->full_name}!\nYour booking for *{$pkgTitleForChat}* has been confirmed by our operations team. We have dispatched your official WhatsApp confirmation as well. You can chat with us here anytime!",
                'is_read'     => false,
            ]);
        }

        // 2. Prepare WhatsApp Confirmation URL
        $cleanPhone = preg_replace('/\D/', '', $inquiry->contact);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '92' . substr($cleanPhone, 1);
        } elseif (!str_starts_with($cleanPhone, '92') && strlen($cleanPhone) === 10) {
            $cleanPhone = '92' . $cleanPhone;
        }

        $packageTitle = $inquiry->package?->package_title ?? 'Hajj/Umrah Package';
        $pax = $inquiry->pax ?? 1;
        $room = ucfirst($inquiry->room_type ?? 'Sharing');
        $duration = $inquiry->duration_days ? "({$inquiry->duration_days} Days)" : "";

        $whatsappText = "Assalam-o-Alaikum *{$inquiry->full_name}*!\n\n"
            . "✨ *BOOKING CONFIRMED* ✨\n"
            . "We are delighted to inform you that your booking with *Baba Hajj & Umrah Group* has been confirmed!\n\n"
            . "📋 *Booking Details:*\n"
            . "• Booking ID: #{$inquiry->id}\n"
            . "• Package: {$packageTitle} {$duration}\n"
            . "• Travelers: {$pax} Pax\n"
            . "• Room Category: {$room}\n\n"
            . "Our operations desk will now initiate your visa approval, flight manifests, and hotel allocation.\n"
            . "You can also view this booking inside the *Baba Hajj Mobile App*.\n\n"
            . "For any inquiries, feel free to reply to this message.\n"
            . "JazakAllah Khair!\n"
            . "*Baba Hajj & Umrah Group*";

        $whatsappUrl = "https://wa.me/{$cleanPhone}?text=" . urlencode($whatsappText);

        // Optional direct WhatsApp gateway auto-dispatch if configured in .env
        $gatewayUrl = env('WHATSAPP_API_URL') ?: config('services.whatsapp.url');
        if (!empty($gatewayUrl)) {
            try {
                \Illuminate\Support\Facades\Http::timeout(5)->post($gatewayUrl, [
                    'phone'   => $cleanPhone,
                    'message' => $whatsappText,
                ]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("WhatsApp gateway auto-dispatch error: " . $e->getMessage());
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'      => true,
                'message'      => 'Booking marked as confirmed and notification dispatched.',
                'whatsapp_url' => $whatsappUrl,
            ]);
        }

        return redirect()->back()->with([
            'success'      => 'Booking confirmed successfully!',
            'whatsapp_url' => $whatsappUrl,
            'client_name'  => $inquiry->full_name,
        ]);
    }

    /**
     * Mobile App API Interactive Documentation Page
     */
    public function apiDocs()
    {
        return view('mobile-ops.api-docs');
    }
}
