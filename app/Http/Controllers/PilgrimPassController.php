<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingPerson;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PilgrimPassController extends Controller
{
    use AuthorizesRequests;
    /**
     * Public Live Mobile E-Pass / Itinerary view.
     * Anyone scanning the QR code on the ID card will open this mobile pass.
     *
     * @param int|string $id BookingPerson ID or Booking ID
     */
    public function show($id)
    {
        $person = BookingPerson::with([
            'booking.client',
            'booking.company',
            'booking.package',
            'booking.hotels',
            'booking.transports',
            'booking.visas'
        ])->find($id);

        $booking = null;
        if ($person) {
            $booking = $person->booking;
        } else {
            $booking = Booking::with([
                'client',
                'company',
                'package',
                'persons',
                'hotels',
                'transports',
                'visas'
            ])->find($id);

            if ($booking) {
                $person = $booking->persons->first();
            }
        }

        if (!$booking) {
            abort(404, 'Pilgrim / Booking record not found.');
        }

        // Categorize Hotels
        $makkahHotels = $booking->hotels->filter(function ($h) {
            $loc = strtolower($h->location ?? '');
            $name = strtolower($h->hotel_name ?? '');
            return str_contains($loc, 'makkah') || str_contains($name, 'makkah');
        });

        $aziziaHotels = $booking->hotels->filter(function ($h) {
            $loc = strtolower($h->location ?? '');
            $name = strtolower($h->hotel_name ?? '');
            return str_contains($loc, 'azizia') || str_contains($loc, 'aziziya') || str_contains($name, 'azizia') || str_contains($name, 'aziziya');
        });

        $madinahHotels = $booking->hotels->filter(function ($h) {
            $loc = strtolower($h->location ?? '');
            $name = strtolower($h->hotel_name ?? '');
            return str_contains($loc, 'madinah') || str_contains($loc, 'medina') || str_contains($name, 'madinah') || str_contains($name, 'medina');
        });

        $otherHotels = $booking->hotels->filter(function ($h) use ($makkahHotels, $aziziaHotels, $madinahHotels) {
            return !$makkahHotels->contains('id', $h->id) &&
                   !$aziziaHotels->contains('id', $h->id) &&
                   !$madinahHotels->contains('id', $h->id);
        });

        return view('pilgrim.epass', compact('person', 'booking', 'makkahHotels', 'aziziaHotels', 'madinahHotels', 'otherHotels'));
    }

    /**
     * Single Printable Luxury Lanyard ID Badge.
     */
    public function badge($id)
    {
        $this->authorize('pilgrim_badge_view');
        $person = BookingPerson::with([
            'booking.client',
            'booking.company',
            'booking.package',
            'booking.hotels',
            'booking.transports',
            'booking.visas'
        ])->findOrFail($id);

        $booking = $person->booking;
        if ($booking) {
            $booking->loadMissing(['package.accommodations']);
        }

        $makkahHotel = $booking->hotels->first(function ($h) {
            $loc = strtolower($h->location ?? '');
            $name = strtolower($h->hotel_name ?? '');
            return str_contains($loc, 'makkah') || str_contains($name, 'makkah');
        }) ?? $booking->hotels->first();

        $aziziaHotel = $booking->hotels->first(function ($h) {
            $loc = strtolower($h->location ?? '');
            $name = strtolower($h->hotel_name ?? '');
            return str_contains($loc, 'azizia') || str_contains($loc, 'aziziya') || str_contains($name, 'azizia') || str_contains($name, 'aziziya');
        });

        $madinahHotel = $booking->hotels->first(function ($h) {
            $loc = strtolower($h->location ?? '');
            $name = strtolower($h->hotel_name ?? '');
            return str_contains($loc, 'madinah') || str_contains($loc, 'medina') || str_contains($name, 'madinah') || str_contains($name, 'medina');
        });

        // Fallback to package accommodations if booking hotel rows are not assigned
        if (!$makkahHotel && $booking->package && $booking->package->accommodations) {
            $acc = $booking->package->accommodations->first(function ($a) {
                $p = strtolower($a->place ?? '');
                $h = strtolower($a->package_a_hotel ?? '');
                return str_contains($p, 'makkah') || str_contains($h, 'makkah');
            }) ?? $booking->package->accommodations->first();
            if ($acc && ($acc->package_a_hotel || $acc->place)) {
                $makkahHotel = (object)[
                    'hotel_name' => $acc->package_a_hotel ?? $acc->place,
                    'room_number' => $person->room_number ?? null
                ];
            }
        }

        if (!$aziziaHotel && $booking->package && $booking->package->accommodations) {
            $acc = $booking->package->accommodations->first(function ($a) {
                $p = strtolower($a->place ?? '');
                $h = strtolower($a->package_a_hotel ?? '');
                return str_contains($p, 'azizia') || str_contains($h, 'azizia') || str_contains($p, 'aziziya');
            });
            if ($acc && ($acc->package_a_hotel || $acc->place)) {
                $aziziaHotel = (object)[
                    'hotel_name' => $acc->package_a_hotel ?? $acc->place,
                    'room_number' => null
                ];
            }
        }

        if (!$madinahHotel && $booking->package && $booking->package->accommodations) {
            $acc = $booking->package->accommodations->first(function ($a) {
                $p = strtolower($a->place ?? '');
                $h = strtolower($a->package_a_hotel ?? '');
                return str_contains($p, 'madinah') || str_contains($h, 'madinah') || str_contains($p, 'medina');
            });
            if ($acc && ($acc->package_a_hotel || $acc->place)) {
                $madinahHotel = (object)[
                    'hotel_name' => $acc->package_a_hotel ?? $acc->place,
                    'room_number' => $person->room_number ?? null
                ];
            }
        }

        return view('pilgrim.badge', compact('person', 'booking', 'makkahHotel', 'aziziaHotel', 'madinahHotel'));
    }

    /**
     * Bulk Printable Lanyard ID Badges Sheet (A4 2-Column Grid) for all pilgrims in a booking.
     */
    public function bulkBadges($bookingId)
    {
        $this->authorize('pilgrim_badge_view');
        $booking = Booking::with([
            'client',
            'company',
            'package.accommodations',
            'persons',
            'hotels',
            'transports',
            'visas'
        ])->findOrFail($bookingId);

        $makkahHotel = $booking->hotels->first(function ($h) {
            $loc = strtolower($h->location ?? '');
            $name = strtolower($h->hotel_name ?? '');
            return str_contains($loc, 'makkah') || str_contains($name, 'makkah');
        }) ?? $booking->hotels->first();

        $aziziaHotel = $booking->hotels->first(function ($h) {
            $loc = strtolower($h->location ?? '');
            $name = strtolower($h->hotel_name ?? '');
            return str_contains($loc, 'azizia') || str_contains($loc, 'aziziya') || str_contains($name, 'azizia') || str_contains($name, 'aziziya');
        });

        $madinahHotel = $booking->hotels->first(function ($h) {
            $loc = strtolower($h->location ?? '');
            $name = strtolower($h->hotel_name ?? '');
            return str_contains($loc, 'madinah') || str_contains($loc, 'medina') || str_contains($name, 'madinah') || str_contains($name, 'medina');
        });

        // Fallback to package accommodations if booking hotel rows are not assigned
        if (!$makkahHotel && $booking->package && $booking->package->accommodations) {
            $acc = $booking->package->accommodations->first(function ($a) {
                $p = strtolower($a->place ?? '');
                $h = strtolower($a->package_a_hotel ?? '');
                return str_contains($p, 'makkah') || str_contains($h, 'makkah');
            }) ?? $booking->package->accommodations->first();
            if ($acc && ($acc->package_a_hotel || $acc->place)) {
                $makkahHotel = (object)[
                    'hotel_name' => $acc->package_a_hotel ?? $acc->place,
                    'room_number' => null
                ];
            }
        }

        if (!$aziziaHotel && $booking->package && $booking->package->accommodations) {
            $acc = $booking->package->accommodations->first(function ($a) {
                $p = strtolower($a->place ?? '');
                $h = strtolower($a->package_a_hotel ?? '');
                return str_contains($p, 'azizia') || str_contains($h, 'azizia') || str_contains($p, 'aziziya');
            });
            if ($acc && ($acc->package_a_hotel || $acc->place)) {
                $aziziaHotel = (object)[
                    'hotel_name' => $acc->package_a_hotel ?? $acc->place,
                    'room_number' => null
                ];
            }
        }

        if (!$madinahHotel && $booking->package && $booking->package->accommodations) {
            $acc = $booking->package->accommodations->first(function ($a) {
                $p = strtolower($a->place ?? '');
                $h = strtolower($a->package_a_hotel ?? '');
                return str_contains($p, 'madinah') || str_contains($h, 'madinah') || str_contains($p, 'medina');
            });
            if ($acc && ($acc->package_a_hotel || $acc->place)) {
                $madinahHotel = (object)[
                    'hotel_name' => $acc->package_a_hotel ?? $acc->place,
                    'room_number' => null
                ];
            }
        }

        return view('pilgrim.bulk_badges', compact('booking', 'makkahHotel', 'aziziaHotel', 'madinahHotel'));
    }
}
