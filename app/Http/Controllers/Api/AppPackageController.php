<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AppPackageController extends Controller
{
    /**
     * Get all active packages with optional filters (type: hajj|umrah, search, duration)
     */
    public function index(Request $request): JsonResponse
    {
        $type   = $request->query('type');
        $search = $request->query('search');

        $query = Package::with(['accommodations', 'company', 'transportFlights', 'transports']);

        if (Schema::hasColumn('packages', 'status')) {
            $query->where('status', 'active');
        }

        if (!empty($type)) {
            $query->where('package_type', strtolower(trim($type)));
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $term = '%' . trim($search) . '%';
                $q->where('package_title', 'like', $term)
                  ->orWhere('package_code', 'like', $term)
                  ->orWhere('primary_hotel', 'like', $term)
                  ->orWhere('departure_city', 'like', $term);
            });
        }

        $packages = $query->latest('id')->get();

        return response()->json([
            'success' => true,
            'message' => 'Packages retrieved successfully.',
            'count'   => $packages->count(),
            'data'    => $packages->map(function ($pkg) {
                return $this->formatPackageSummary($pkg);
            })
        ], 200);
    }

    /**
     * Dedicated Endpoint: Get Hajj Packages
     */
    public function hajj(Request $request): JsonResponse
    {
        $request->merge(['type' => 'hajj']);
        return $this->index($request);
    }

    /**
     * Dedicated Endpoint: Get Umrah Packages
     */
    public function umrah(Request $request): JsonResponse
    {
        $request->merge(['type' => 'umrah']);
        return $this->index($request);
    }

    /**
     * Get Full Details of a Specific Package
     */
    public function show(int $id): JsonResponse
    {
        $package = Package::with([
            'accommodations',
            'itinerary',
            'terms',
            'maktabAddress',
            'transports',
            'transportFlights',
            'transportTrains',
            'giveaways',
            'company',
        ])->find($id);

        if (!$package) {
            return response()->json([
                'success' => false,
                'message' => 'Package not found.'
            ], 404);
        }

        $startingPrice = $this->calculateStartingPrice($package);

        // Parse repeatable inclusions
        $parsedInclusions = [];
        if (!empty($package->inclusions)) {
            $decoded = json_decode($package->inclusions, true);
            $parsedInclusions = is_array($decoded)
                ? $decoded
                : array_values(array_filter(array_map('trim', explode("\n", $package->inclusions))));
        }

        return response()->json([
            'success' => true,
            'message' => 'Package details retrieved successfully.',
            'data'    => [
                'id'                       => $package->id,
                'title'                    => $package->package_title ?? $package->package_name ?? $package->name ?? 'Package',
                'subtitle'                 => $package->subtitle,
                'package_type'             => ucfirst($package->package_type ?? 'hajj'),
                'package_code'             => $package->package_code ?? $package->code ?? null,
                'is_group_package'         => (bool) $package->is_group_package,
                'visa_status'              => $package->visa_status ?? 'included',
                'departure_city'           => $package->departure_city,
                'destination_city'         => $package->destination_city,
                'destination_country'      => $package->destination_country,
                'starting_price'           => $startingPrice,
                'currency'                 => $package->currency ?? 'PKR',
                'room_rates'               => [
                    'sharing'    => (float) ($package->price_sharing ?? 0),
                    'quad'       => (float) ($package->price_quad ?? 0),
                    'triple'     => (float) ($package->price_triple ?? 0),
                    'double'     => (float) ($package->price_double ?? 0),
                    'per_person' => (float) ($package->price_per_person ?? 0),
                ],
                'duration_days'            => $package->duration_days ?? $package->days ?? ($package->stay_nights ? $package->stay_nights + 1 : null),
                'stay_nights'              => $package->stay_nights,
                'valid_from'               => optional($package->valid_from)->format('Y-m-d') ?? $package->start_date ?? null,
                'valid_until'              => optional($package->valid_until)->format('Y-m-d') ?? $package->end_date ?? null,
                'primary_hotel'            => $package->primary_hotel,
                'hotel_star_rating'        => $package->hotel_star_rating,
                'meal_plan'                => $package->meal_plan,
                'key_features'             => $package->key_features,
                'parsed_inclusions'        => $parsedInclusions,
                'hotel_amenities'          => is_array($package->hotel_amenities) ? $package->hotel_amenities : [],
                'exclusions'               => $package->exclusions,
                'requirements_text'        => $package->requirements_text,
                'description_notes'        => $package->description_notes ?? $package->notes,
                'terms_and_conditions'     => $package->terms ? $package->terms->content : null,
                'accommodations'           => $package->accommodations,
                'itinerary'                => $package->itinerary,
                'maktab_address'           => $package->maktabAddress,
                'transports'               => $package->transports,
                'flights'                  => $package->transportFlights,
                'trains'                   => $package->transportTrains,
                'package_image'            => $package->package_image_url ?? (!empty($package->image ?? $package->banner_image) ? asset($package->image ?? $package->banner_image) : null),
                'banner_image'             => $package->banner_image_url ?? null,
                'giveaways'                => $package->giveaways,
                'company'                  => $package->company ? [
                    'id'    => $package->company->id,
                    'name'  => $package->company->name ?? null,
                    'phone' => $package->company->phone ?? null,
                    'email' => $package->company->email ?? null,
                    'logo'  => !empty($package->company->logo) ? asset($package->company->logo) : null,
                ] : null,
            ]
        ], 200);
    }

    /**
     * Format Package Summary for Listing
     */
    private function formatPackageSummary($pkg): array
    {
        $startingPrice = $this->calculateStartingPrice($pkg);

        $makkah = $pkg->accommodations->firstWhere('place', 'makkah');
        $madinah = $pkg->accommodations->firstWhere('place', 'madinah');

        return [
            'id'                  => $pkg->id,
            'title'               => $pkg->package_title ?? $pkg->package_name ?? $pkg->name ?? 'Package',
            'package_type'        => ucfirst($pkg->package_type ?? 'hajj'),
            'package_code'        => $pkg->package_code ?? $pkg->code ?? null,
            'starting_price'      => $startingPrice,
            'currency'            => $pkg->currency ?? 'PKR',
            'room_rates'          => [
                'sharing'    => (float) ($pkg->price_sharing ?? 0),
                'quad'       => (float) ($pkg->price_quad ?? 0),
                'triple'     => (float) ($pkg->price_triple ?? 0),
                'double'     => (float) ($pkg->price_double ?? 0),
                'per_person' => (float) ($pkg->price_per_person ?? 0),
            ],
            'duration_days'       => $pkg->duration_days ?? $pkg->days ?? ($pkg->stay_nights ? $pkg->stay_nights + 1 : null),
            'stay_nights'         => $pkg->stay_nights,
            'valid_from'          => optional($pkg->valid_from)->format('Y-m-d') ?? $pkg->start_date ?? null,
            'valid_until'         => optional($pkg->valid_until)->format('Y-m-d') ?? $pkg->end_date ?? null,
            'primary_hotel'       => $pkg->primary_hotel,
            'hotel_star_rating'   => $pkg->hotel_star_rating,
            'hotels'              => [
                'makkah'  => $makkah ? $makkah->hotel : null,
                'madinah' => $madinah ? $madinah->hotel : null,
            ],
            'key_features'        => $pkg->key_features,
            'package_image'       => $pkg->package_image_url ?? (!empty($pkg->image ?? $pkg->banner_image) ? asset($pkg->image ?? $pkg->banner_image) : null),
            'banner_image'        => $pkg->banner_image_url ?? null,
        ];
    }

    private function calculateStartingPrice($pkg): float
    {
        if (!empty($pkg->price_per_person) && $pkg->price_per_person > 0) {
            return (float) $pkg->price_per_person;
        } elseif (!empty($pkg->price_sharing) && $pkg->price_sharing > 0) {
            return (float) $pkg->price_sharing;
        } elseif (!empty($pkg->price_quad) && $pkg->price_quad > 0) {
            return (float) $pkg->price_quad;
        } elseif (!empty($pkg->price_triple) && $pkg->price_triple > 0) {
            return (float) $pkg->price_triple;
        } elseif (!empty($pkg->price_double) && $pkg->price_double > 0) {
            return (float) $pkg->price_double;
        } elseif (!empty($pkg->adult_pkr) && $pkg->adult_pkr > 0) {
            return (float) $pkg->adult_pkr;
        } elseif (!empty($pkg->price) && $pkg->price > 0) {
            return (float) $pkg->price;
        }
        return 0.0;
    }
}
