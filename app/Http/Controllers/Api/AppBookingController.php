<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\AppUser;
use App\Models\Package;
use App\Models\PackageInquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppBookingController extends Controller
{
    /**
     * Submit a Booking / Inquiry from Mobile App
     * Accessible by both registered mobile users (auth:sanctum) and guest/random users!
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'package_id'           => 'required|exists:packages,id',
            'full_name'            => 'required|string|max:150',
            'contact'              => 'required|string|max:30',
            'email'                => 'nullable|email|max:150',
            'passport_number'      => 'nullable|string|max:50',
            'cnic_number'          => 'nullable|string|max:30',
            'pax'                  => 'nullable|integer|min:1',
            'duration_days'        => 'nullable|string|max:50',
            'room_type'            => 'nullable|in:sharing,quad,triple,double',
            'special_request'      => 'nullable|string|max:2000',
            'passport_document'    => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'nic_or_visa_document' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Determine if user is authenticated via Sanctum
        $appUser = auth('sanctum')->user();
        $appUserId = null;

        if ($appUser && $appUser instanceof AppUser) {
            $appUserId = $appUser->id;
        } else {
            // Check if phone matches an existing app user
            $existing = AppUser::where('phone', trim($request->contact))->first();
            if ($existing) {
                $appUserId = $existing->id;
            }
        }

        // Handle file uploads
        $passportPath = $request->hasFile('passport_document')
            ? $request->file('passport_document')->store('inquiries/passports', 'public')
            : null;

        $nicVisaPath = $request->hasFile('nic_or_visa_document')
            ? $request->file('nic_or_visa_document')->store('inquiries/documents', 'public')
            : null;

        $package = Package::find($request->package_id);

        $inquiry = PackageInquiry::create([
            'package_id'           => $request->package_id,
            'app_user_id'          => $appUserId,
            'full_name'            => trim($request->full_name),
            'contact'              => trim($request->contact),
            'email'                => !empty($request->email) ? trim(strtolower($request->email)) : null,
            'passport_number'      => $request->passport_number,
            'cnic_number'          => $request->cnic_number,
            'pax'                  => $request->pax ?? 1,
            'duration_days'        => $request->duration_days,
            'room_type'            => $request->room_type ?? 'sharing',
            'special_request'      => $request->special_request,
            'passport_document'    => $passportPath,
            'nic_or_visa_document' => $nicVisaPath,
            'status'               => 'pending',
        ]);

        // Send In-App Notification if user is linked
        if ($appUserId) {
            AppNotification::create([
                'app_user_id' => $appUserId,
                'title'       => 'Booking Request Received',
                'message'     => 'Your booking request for "' . ($package->package_title ?? 'Package #' . $package->id) . '" has been received! Our team will contact you shortly.',
                'type'        => 'booking_received',
                'data'        => [
                    'inquiry_id' => $inquiry->id,
                    'package_id' => $package->id,
                    'status'     => 'pending',
                ],
                'is_read'     => false,
            ]);
        }

        // WhatsApp direct link helper for the user
        $agencyPhone = '923000000000'; // Default agency support whatsapp
        $cleanContact = preg_replace('/[^0-9]/', '', $request->contact);
        $packageTitle = $package->package_title ?? 'Hajj/Umrah Package';
        $whatsappMessage = "Assalam-o-Alaikum! I have submitted a booking inquiry (ID #{$inquiry->id}) for {$packageTitle} for {$inquiry->pax} person(s). Please confirm.";
        $whatsappLink = "https://wa.me/?text=" . urlencode($whatsappMessage);

        return response()->json([
            'success' => true,
            'message' => 'Booking request submitted successfully! Our team will contact you soon.',
            'data'    => [
                'booking_id'      => $inquiry->id,
                'package_id'      => $inquiry->package_id,
                'package_title'   => $packageTitle,
                'full_name'       => $inquiry->full_name,
                'contact'         => $inquiry->contact,
                'pax'             => $inquiry->pax,
                'room_type'       => $inquiry->room_type,
                'status'          => $inquiry->status,
                'whatsapp_link'   => $whatsappLink,
                'created_at'      => $inquiry->created_at->format('Y-m-d H:i:s'),
            ]
        ], 201);
    }

    /**
     * Get Logged-in App User's Bookings
     */
    public function myBookings(Request $request): JsonResponse
    {
        /** @var AppUser $user */
        $user = $request->user();

        $inquiries = PackageInquiry::with(['package'])
            ->where(function ($q) use ($user) {
                $q->where('app_user_id', $user->id)
                  ->orWhere('contact', $user->phone);
            })
            ->latest('id')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'User bookings retrieved successfully.',
            'count'   => $inquiries->count(),
            'data'    => $inquiries->map(function ($item) {
                return [
                    'id'                       => $item->id,
                    'package_id'               => $item->package_id,
                    'package_title'            => $item->package?->package_title ?? 'Package',
                    'package_type'             => ucfirst($item->package?->package_type ?? 'hajj'),
                    'package_image'            => $item->package?->package_image_url ?? null,
                    'full_name'                => $item->full_name,
                    'contact'                  => $item->contact,
                    'pax'                      => $item->pax,
                    'room_type'                => $item->room_type,
                    'duration_days'            => $item->duration_days,
                    'status'                   => $item->status,
                    'admin_notes'              => $item->admin_notes,
                    'passport_document_url'    => $item->passport_document_url,
                    'nic_or_visa_document_url' => $item->nic_or_visa_document_url,
                    'created_at'               => $item->created_at->format('Y-m-d H:i:s'),
                ];
            })
        ], 200);
    }

    /**
     * Get Single Booking Details
     */
    public function show(Request $request, int $id): JsonResponse
    {
        /** @var AppUser $user */
        $user = $request->user();

        $inquiry = PackageInquiry::with(['package.accommodations', 'package.company'])
            ->where('id', $id)
            ->where(function ($q) use ($user) {
                $q->where('app_user_id', $user->id)
                  ->orWhere('contact', $user->phone);
            })
            ->first();

        if (!$inquiry) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking details retrieved successfully.',
            'data'    => [
                'id'                       => $inquiry->id,
                'package_id'               => $inquiry->package_id,
                'package_title'            => $inquiry->package?->package_title ?? 'Package',
                'package_type'             => ucfirst($inquiry->package?->package_type ?? 'hajj'),
                'full_name'                => $inquiry->full_name,
                'contact'                  => $inquiry->contact,
                'email'                    => $inquiry->email,
                'passport_number'          => $inquiry->passport_number,
                'cnic_number'              => $inquiry->cnic_number,
                'pax'                      => $inquiry->pax,
                'room_type'                => $inquiry->room_type,
                'duration_days'            => $inquiry->duration_days,
                'special_request'          => $inquiry->special_request,
                'status'                   => $inquiry->status,
                'admin_notes'              => $inquiry->admin_notes,
                'passport_document_url'    => $inquiry->passport_document_url,
                'nic_or_visa_document_url' => $inquiry->nic_or_visa_document_url,
                'created_at'               => $inquiry->created_at->format('Y-m-d H:i:s'),
            ]
        ], 200);
    }
}
