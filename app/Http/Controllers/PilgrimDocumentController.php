<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingPerson;
use App\Models\PersonDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class PilgrimDocumentController extends Controller
{
    /**
     * Display a listing of bookings and their pilgrims' documents.
     */
    public function index(Request $request)
    {
        $bookingId = $request->get('booking_id');
        $search = trim($request->get('search', ''));
        $docFilter = $request->get('doc_filter', 'all');
        $packageType = $request->get('package_type', session('dashboard_package', 'all'));

        // 1. Fetch bookings list for selector
        $bookingsQuery = Booking::with(['client', 'package', 'persons'])
            ->orderBy('id', 'desc');

        if ($packageType !== 'all' && in_array($packageType, ['hajj', 'umrah'])) {
            $bookingsQuery->where('package_type', $packageType);
        }

        $allBookings = $bookingsQuery->get(['id', 'client_id', 'package_id', 'package_name', 'package_type', 'package_year', 'created_at', 'no_of_pax']);

        // 2. Load selected booking if booking_id provided
        $selectedBooking = null;
        if (!empty($bookingId)) {
            $selectedBooking = Booking::with(['client', 'package', 'company', 'persons'])->find($bookingId);
        }

        // 3. Query persons
        $personsQuery = BookingPerson::with(['booking.client', 'booking.package']);

        if (!empty($bookingId)) {
            $personsQuery->where('booking_id', $bookingId);
        } elseif ($packageType !== 'all' && in_array($packageType, ['hajj', 'umrah'])) {
            $personsQuery->whereHas('booking', function ($q) use ($packageType) {
                $q->where('package_type', $packageType);
            });
        }

        if (!empty($search)) {
            $personsQuery->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('passport_number', 'like', "%{$search}%")
                    ->orWhere('cnic', 'like', "%{$search}%")
                    ->orWhere('hajj_id', 'like', "%{$search}%")
                    ->orWhere('hb_number', 'like', "%{$search}%")
                    ->orWhereHas('booking', function ($bq) use ($search) {
                        $bq->where('package_name', 'like', "%{$search}%")
                            ->orWhereHas('client', function ($cq) use ($search) {
                                $cq->where('name', 'like', "%{$search}%")
                                    ->orWhere('phone', 'like', "%{$search}%");
                            });
                    });
            });
        }

        // Filter by document status
        if ($docFilter === 'missing_passport') {
            $personsQuery->where(function ($q) {
                $q->whereNull('passport_image')->orWhere('passport_image', '');
            });
        } elseif ($docFilter === 'missing_picture') {
            $personsQuery->where(function ($q) {
                $q->whereNull('photo')->orWhere('photo', '');
            });
        } elseif ($docFilter === 'missing_medical') {
            $personsQuery->where(function ($q) {
                $q->whereNull('medical_document')->orWhere('medical_document', '');
            });
        } elseif ($docFilter === 'missing_id') {
            $personsQuery->where(function ($q) {
                $q->whereNull('id_document')->orWhere('id_document', '');
            });
        } elseif ($docFilter === 'missing_password') {
            $personsQuery->where(function ($q) {
                $q->whereNull('portal_password')->orWhere('portal_password', '');
            });
        } elseif ($docFilter === 'complete') {
            $personsQuery->whereNotNull('passport_image')->where('passport_image', '!=', '')
                ->whereNotNull('photo')->where('photo', '!=', '')
                ->whereNotNull('medical_document')->where('medical_document', '!=', '')
                ->whereNotNull('id_document')->where('id_document', '!=', '')
                ->whereNotNull('portal_password')->where('portal_password', '!=', '');
        }

        $persons = $personsQuery->orderBy('id', 'desc')->paginate(25)->withQueryString();

        // 4. Calculate Stats for the active scope
        $statsScope = BookingPerson::query();
        if (!empty($bookingId)) {
            $statsScope->where('booking_id', $bookingId);
        } elseif ($packageType !== 'all' && in_array($packageType, ['hajj', 'umrah'])) {
            $statsScope->whereHas('booking', function ($q) use ($packageType) {
                $q->where('package_type', $packageType);
            });
        }

        $totalPilgrims = (clone $statsScope)->count();
        $totalPassports = (clone $statsScope)->whereNotNull('passport_image')->where('passport_image', '!=', '')->count();
        $totalPictures = (clone $statsScope)->whereNotNull('photo')->where('photo', '!=', '')->count();
        $totalMedical = (clone $statsScope)->whereNotNull('medical_document')->where('medical_document', '!=', '')->count();
        $totalIds = (clone $statsScope)->whereNotNull('id_document')->where('id_document', '!=', '')->count();
        $totalPasswords = (clone $statsScope)->whereNotNull('portal_password')->where('portal_password', '!=', '')->count();

        $stats = [
            'total' => $totalPilgrims,
            'passport' => $totalPassports,
            'picture' => $totalPictures,
            'medical' => $totalMedical,
            'id' => $totalIds,
            'password' => $totalPasswords,
        ];

        return view('documents.index', compact('allBookings', 'selectedBooking', 'persons', 'stats', 'bookingId', 'search', 'docFilter', 'packageType'));
    }

    /**
     * AJAX endpoint to fetch booking information and all its pilgrims with document status.
     */
    public function fetchBooking($id)
    {
        $booking = Booking::with(['client', 'package', 'company', 'persons'])->find($id);
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
        }

        $persons = $booking->persons->map(function ($p) {
            return [
                'id' => $p->id,
                'full_name' => $p->full_name,
                'gender' => $p->gender,
                'hajj_id' => $p->hajj_id,
                'hb_number' => $p->hb_number,
                'passport_number' => $p->passport_number,
                'cnic' => $p->cnic,
                'phone' => $p->phone,
                'passport' => $p->passport_image ? asset($p->passport_image) : null,
                'picture' => $p->photo ? asset($p->photo) : null,
                'medical' => $p->medical_document ? asset($p->medical_document) : null,
                'id_document' => $p->id_document ? asset($p->id_document) : null,
                'password' => $p->portal_password ?? '',
            ];
        });

        return response()->json([
            'success' => true,
            'booking' => [
                'id' => $booking->id,
                'prefix' => $booking->persons->pluck('hb_number')->filter()->first() ?: ($booking->package?->code ?? 'AZ 01'),
                'client_name' => $booking->client?->name ?? 'N/A',
                'client_phone' => $booking->client?->phone ?? 'N/A',
                'client_cnic' => $booking->client?->cnic ?? 'N/A',
                'package_name' => $booking->package->package_title ?? ($booking->package->name ?? ($booking->package_name ?? 'N/A')),
                'camp' => $booking->camp ?? ($booking->package->maktab ?? 'N/A'),
                'no_of_pax' => $booking->persons->count() ?: ($booking->no_of_pax ?? 1),
                'season' => strtoupper($booking->package_type ?? 'HAJJ') . ' ' . ($booking->package_year ?? date('Y')),
                'referral' => ($booking->company ?? $booking->client?->referral)->company_name ?? 'Direct',
            ],
            'persons' => $persons,
        ]);
    }

    /**
     * Upload an individual document (Passport, Picture, Medical, or ID) for a pilgrim.
     */
    public function upload(Request $request, $personId)
    {
        $person = BookingPerson::findOrFail($personId);
        $type = $request->input('type'); // 'passport', 'picture', 'medical', 'id_document'

        $validTypes = ['passport', 'picture', 'medical', 'id_document'];
        if (!in_array($type, $validTypes)) {
            return response()->json(['success' => false, 'message' => 'Invalid document type'], 422);
        }

        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,webp,pdf,doc,docx|max:10240', // 10MB
        ]);

        $file = $request->file('file');
        $uploadDir = public_path('uploads/booking_persons');
        if (!File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true, true);
        }

        $prefix = match ($type) {
            'passport' => 'pass_',
            'picture' => 'photo_',
            'medical' => 'med_',
            'id_document' => 'id_',
        };

        $filename = $prefix . time() . '_' . $person->id . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadDir, $filename);
        $relativePath = 'uploads/booking_persons/' . $filename;

        // Update BookingPerson
        switch ($type) {
            case 'passport':
                if (!empty($person->passport_image) && file_exists(public_path($person->passport_image))) {
                    @unlink(public_path($person->passport_image));
                }
                $person->passport_image = $relativePath;
                break;
            case 'picture':
                if (!empty($person->photo) && file_exists(public_path($person->photo))) {
                    @unlink(public_path($person->photo));
                }
                $person->photo = $relativePath;
                break;
            case 'medical':
                if (!empty($person->medical_document) && file_exists(public_path($person->medical_document))) {
                    @unlink(public_path($person->medical_document));
                }
                $person->medical_document = $relativePath;
                break;
            case 'id_document':
                if (!empty($person->id_document) && file_exists(public_path($person->id_document))) {
                    @unlink(public_path($person->id_document));
                }
                $person->id_document = $relativePath;
                break;
        }
        $person->save();

        // Also sync PersonDocument model
        $doc = PersonDocument::firstOrNew(['person_id' => $person->id]);
        $doc->booking_id = $person->booking_id;
        if ($type === 'passport') $doc->passport = $relativePath;
        if ($type === 'picture') $doc->visa = $relativePath; // or secondary field
        if ($type === 'medical') $doc->medical = $relativePath;
        if ($type === 'id_document') {
            $doc->id_document = $relativePath;
            $doc->nic_front = $relativePath;
        }
        $doc->save();

        return response()->json([
            'success' => true,
            'message' => ucfirst(str_replace('_', ' ', $type)) . ' uploaded successfully!',
            'type' => $type,
            'file_url' => asset($relativePath),
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'person_id' => $person->id,
        ]);
    }

    /**
     * Update portal/Nusuk password for a pilgrim.
     */
    public function updatePassword(Request $request, $personId)
    {
        $person = BookingPerson::findOrFail($personId);
        $password = $request->input('password', '');

        $person->portal_password = $password;
        $person->save();

        // Sync PersonDocument
        $doc = PersonDocument::firstOrNew(['person_id' => $person->id]);
        $doc->booking_id = $person->booking_id;
        $doc->password = $password;
        $doc->save();

        return response()->json([
            'success' => true,
            'message' => 'Password saved successfully!',
            'password' => $password,
            'person_id' => $person->id,
        ]);
    }

    /**
     * Update all 5 columns (Passport, Picture, Medical, ID, and Password) in one go.
     */
    public function updateAll(Request $request, $personId)
    {
        $person = BookingPerson::findOrFail($personId);
        $uploadDir = public_path('uploads/booking_persons');
        if (!File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true, true);
        }

        // 1. Passport
        if ($request->hasFile('passport')) {
            $file = $request->file('passport');
            $filename = 'pass_' . time() . '_' . $person->id . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $person->passport_image = 'uploads/booking_persons/' . $filename;
        }

        // 2. Picture
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $filename = 'photo_' . time() . '_' . $person->id . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $person->photo = 'uploads/booking_persons/' . $filename;
        }

        // 3. Medical
        if ($request->hasFile('medical')) {
            $file = $request->file('medical');
            $filename = 'med_' . time() . '_' . $person->id . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $person->medical_document = 'uploads/booking_persons/' . $filename;
        }

        // 4. ID
        if ($request->hasFile('id_document')) {
            $file = $request->file('id_document');
            $filename = 'id_' . time() . '_' . $person->id . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $person->id_document = 'uploads/booking_persons/' . $filename;
        }

        // 5. Password
        if ($request->has('password')) {
            $person->portal_password = $request->input('password');
        }

        $person->save();

        // Sync PersonDocument
        $doc = PersonDocument::firstOrNew(['person_id' => $person->id]);
        $doc->booking_id = $person->booking_id;
        if (!empty($person->passport_image)) $doc->passport = $person->passport_image;
        if (!empty($person->medical_document)) $doc->medical = $person->medical_document;
        if (!empty($person->id_document)) {
            $doc->id_document = $person->id_document;
            $doc->nic_front = $person->id_document;
        }
        if (isset($person->portal_password)) $doc->password = $person->portal_password;
        $doc->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'All documents & credentials updated successfully!',
                'person' => [
                    'id' => $person->id,
                    'passport' => $person->passport_image ? asset($person->passport_image) : null,
                    'picture' => $person->photo ? asset($person->photo) : null,
                    'medical' => $person->medical_document ? asset($person->medical_document) : null,
                    'id_document' => $person->id_document ? asset($person->id_document) : null,
                    'password' => $person->portal_password ?? '',
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Pilgrim documents and password updated successfully!');
    }

    /**
     * Delete an individual document.
     */
    public function deleteDocument(Request $request, $personId, $type)
    {
        $person = BookingPerson::findOrFail($personId);

        $path = null;
        switch ($type) {
            case 'passport':
                $path = $person->passport_image;
                $person->passport_image = null;
                break;
            case 'picture':
                $path = $person->photo;
                $person->photo = null;
                break;
            case 'medical':
                $path = $person->medical_document;
                $person->medical_document = null;
                break;
            case 'id_document':
                $path = $person->id_document;
                $person->id_document = null;
                break;
        }

        if ($path && file_exists(public_path($path))) {
            @unlink(public_path($path));
        }

        $person->save();

        // Sync PersonDocument
        $doc = PersonDocument::where('person_id', $person->id)->first();
        if ($doc) {
            if ($type === 'passport') $doc->passport = null;
            if ($type === 'medical') $doc->medical = null;
            if ($type === 'id_document') {
                $doc->id_document = null;
                $doc->nic_front = null;
            }
            $doc->save();
        }

        return response()->json([
            'success' => true,
            'message' => ucfirst(str_replace('_', ' ', $type)) . ' deleted successfully!',
            'type' => $type,
            'person_id' => $person->id,
        ]);
    }

    /**
     * Download all documents of a booking as a single ZIP archive.
     */
    public function downloadZip($id)
    {
        $booking = Booking::with('persons')->findOrFail($id);

        if (!class_exists('ZipArchive')) {
            return redirect()->back()->with('error', 'ZipArchive extension is not available on this server.');
        }

        $zip = new ZipArchive();
        $zipFilename = 'Booking_' . $booking->id . '_Documents_' . date('Ymd_His') . '.zip';
        $zipPath = public_path('uploads/' . $zipFilename);

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($booking->persons as $idx => $p) {
                $folder = 'Pilgrim_' . ($idx + 1) . '_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $p->full_name);

                $docs = [
                    'Passport' => $p->passport_image,
                    'Picture' => $p->photo,
                    'Medical' => $p->medical_document,
                    'ID' => $p->id_document,
                ];

                foreach ($docs as $name => $relPath) {
                    if (!empty($relPath) && file_exists(public_path($relPath))) {
                        $ext = pathinfo($relPath, PATHINFO_EXTENSION);
                        $zip->addFile(public_path($relPath), $folder . '/' . $name . '.' . $ext);
                    }
                }

                // Add Credentials text file if password exists
                if (!empty($p->portal_password)) {
                    $credContent = "Pilgrim Name: {$p->full_name}\n";
                    $credContent .= "Hajj ID: {$p->hajj_id}\n";
                    $credContent .= "Passport Number: {$p->passport_number}\n";
                    $credContent .= "CNIC: {$p->cnic}\n";
                    $credContent .= "Portal / Nusuk Password: {$p->portal_password}\n";
                    $zip->addFromString($folder . '/credentials.txt', $credContent);
                }
            }
            $zip->close();

            return response()->download($zipPath)->deleteFileAfterSend(true);
        }

        return redirect()->back()->with('error', 'Could not create ZIP archive.');
    }
}
