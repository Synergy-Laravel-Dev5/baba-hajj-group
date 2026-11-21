<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

if (!trait_exists(HasApiTokens::class)) {
    eval('
        namespace Laravel\Sanctum;
        trait HasApiTokens {
            public function createToken(string $name, array $abilities = ["*"], ?\DateTimeInterface $expiresAt = null) {
                return (object)["plainTextToken" => base64_encode(random_bytes(32))];
            }
            public function tokens() {
                return $this->morphMany(\Illuminate\Database\Eloquent\Model::class, "tokenable");
            }
        }
    ');
}

class Booking extends Model
{
    use HasApiTokens;
    use SoftDeletes;

    protected $fillable = [
        'package_id',
        'client_id',
        'company_id',
        'booking_for',
        'package_type',
        'camp',
        'room_breakdown',
        'qurbani_option',
        'qurbani_qty',
        'qurbani_charges',
        'passport_number',
        'package_year',
        'cnic',
        'no_of_pax',
        'care_of',
        'voucher_number',
        'card_number',
        'remarks',
        'phone',
        'emergency_phone',
        'departure_date',
        'departure_flight',
        'departure_time',
        'departure_airline',
        'departure_pnr',
        'arrival_date',
        'arrival_flight',
        'arrival_time',
        'arrival_airline',
        'arrival_pnr',
        'visa_passport',
        'visa_given_name',
        'visa_sur_name',
        'visa_dob',
        'visa_company',
        'visa_send_to',
        'package_name',
        'package_cost',
        'visa_charges',
        'flight_charges',
        'flight_charges_less',
        'other_charges',
        'discount',
        'commission',
        'total_amount',
        'total_received',
        'balance',
        'status',
        'agreement_signature',
        'app_password',
        'app_password_plain',
        'app_last_login_at',
    ];

    protected $casts = [
        'room_breakdown' => 'array',
    ];

    protected static function booted()
    {
        static::saving(function ($booking) {
            if (empty($booking->departure_date)) $booking->departure_date = null;
            if (empty($booking->arrival_date)) $booking->arrival_date = null;
            if (empty($booking->departure_time)) $booking->departure_time = null;
            if (empty($booking->arrival_time)) $booking->arrival_time = null;
            if (empty($booking->visa_dob)) $booking->visa_dob = null;
        });
    }

    /**
     * Auto-generate sequential package booking code, e.g. "AZ 01", "AZ 02", etc.
     */
    public static function generateNextPackageBookingCode(?int $packageId, ?int $excludeBookingId = null): string
    {
        $package = $packageId ? Package::find($packageId) : null;
        $rawCode = $package ? ($package->code ?: ($package->package_number ?: 'AZ')) : 'AZ';

        // Extract alphabetic/symbol prefix before trailing digits, e.g. "AZ01" -> "AZ", "AZ 01" -> "AZ", "AZ" -> "AZ"
        if (preg_match('/^([A-Za-z\s_-]+)/', $rawCode, $m)) {
            $prefix = strtoupper(trim(preg_replace('/[-_\s]+$/', '', $m[1])));
        } else {
            $prefix = 'AZ';
        }
        if (empty($prefix)) {
            $prefix = 'AZ';
        }

        // Include trashed bookings so codes in Trash are never reused or rolled back
        $query = self::withTrashed();
        if ($packageId) {
            $query->where('package_id', $packageId);
        }
        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }
        $bookingIds = $query->pluck('id');

        $maxNum = 0;
        if ($bookingIds->isNotEmpty()) {
            $existingCodes = BookingPerson::whereIn('booking_id', $bookingIds)
                ->whereNotNull('hb_number')
                ->where('hb_number', '!=', '')
                ->pluck('hb_number');

            foreach ($existingCodes as $c) {
                if (preg_match('/(\d+)$/', trim($c), $match)) {
                    $num = (int) $match[1];
                    if ($num > $maxNum) {
                        $maxNum = $num;
                    }
                }
            }
        }

        // Also check any BookingPerson with this prefix across all records (including trashed)
        $allPrefixCodes = BookingPerson::whereNotNull('hb_number')
            ->where('hb_number', '!=', '')
            ->where(function ($q) use ($prefix) {
                $q->where('hb_number', 'like', $prefix . ' %')
                  ->orWhere('hb_number', 'like', $prefix . '%');
            })
            ->pluck('hb_number');

        foreach ($allPrefixCodes as $c) {
            if (preg_match('/^' . preg_quote($prefix, '/') . '[-_\s]?(\d+)$/i', trim($c), $match)) {
                $num = (int) $match[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }

        $next = max($maxNum, $bookingIds->count()) + 1;

        return $prefix . ' ' . str_pad($next, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Auto-generate sequential invoice number (e.g. "INV-00001", "INV-00002")
     */
    public static function generateNextInvoiceNo(): string
    {
        $lastBooking = static::withTrashed()
            ->whereNotNull('voucher_number')
            ->where('voucher_number', '!=', '')
            ->orderBy('id', 'desc')
            ->first();

        $nextNum = 1;
        if ($lastBooking && preg_match('/(\d+)/', $lastBooking->voucher_number, $matches)) {
            $nextNum = (int) $matches[1] + 1;
        } else {
            $maxId = (int) static::withTrashed()->max('id');
            $nextNum = max(1, $maxId + 1);
        }

        return 'INV-' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Accessor for Invoice Number (fallback to INV-{id})
     */
    public function getInvoiceNoAttribute(): string
    {
        if (!empty($this->voucher_number)) {
            return $this->voucher_number;
        }
        return 'INV-' . str_pad($this->id ?: 1, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Accessor for Booking Prefix (e.g. "AZ 01", "AZ 03")
     */
    public function getBookingPrefixAttribute(): string
    {
        if ($this->relationLoaded('persons')) {
            $personHb = $this->persons->pluck('hb_number')->filter()->first();
        } else {
            $personHb = $this->persons()->whereNotNull('hb_number')->where('hb_number', '!=', '')->value('hb_number');
        }

        if (!empty($personHb)) {
            return \App\Models\BookingPerson::formatHbNumber($personHb);
        }

        if ($this->package_id) {
            $package = $this->relationLoaded('package') ? $this->package : Package::find($this->package_id);
            $rawCode = $package ? ($package->code ?: ($package->package_number ?: 'AZ')) : 'AZ';
            $prefix = 'AZ';
            if (preg_match('/^([A-Za-z\s_-]+)/', $rawCode, $m)) {
                $prefix = strtoupper(trim(preg_replace('/[-_\s]+$/', '', $m[1])));
            }
            if (empty($prefix)) {
                $prefix = 'AZ';
            }

            // Count bookings (including trashed so deleted prefixes are never reused)
            $order = self::withTrashed()
                ->where('package_id', $this->package_id)
                ->where('id', '<=', $this->id)
                ->count();

            return $prefix . ' ' . str_pad(max(1, $order), 2, '0', STR_PAD_LEFT);
        }

        return 'AZ ' . str_pad($this->id ?: 1, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Accessor for formatted Room Type Summary (e.g. "Quad (2 Pax)", "Triple (1 Pax)")
     */
    public function getRoomTypeSummaryAttribute(): ?string
    {
        $types = [];

        $rb = $this->room_breakdown;
        if (is_string($rb)) {
            $rb = json_decode($rb, true);
        }

        if (is_array($rb)) {
            $paxByType = [
                'Sharing' => 0,
                'Quad'    => 0,
                'Triple'  => 0,
                'Double'  => 0,
                'Single'  => 0,
            ];

            foreach ($rb as $key => $val) {
                $count = (int) $val;
                if ($count <= 0) continue;

                $lower = strtolower($key);
                if (str_contains($lower, 'sharing')) {
                    $paxByType['Sharing'] += $count;
                } elseif (str_contains($lower, 'quad')) {
                    $paxByType['Quad'] += $count;
                } elseif (str_contains($lower, 'triple')) {
                    $paxByType['Triple'] += $count;
                } elseif (str_contains($lower, 'double')) {
                    $paxByType['Double'] += $count;
                } elseif (str_contains($lower, 'single')) {
                    $paxByType['Single'] += $count;
                }
            }

            foreach ($paxByType as $t => $cnt) {
                if ($cnt > 0) {
                    $types[] = "{$t} ({$cnt} Pax)";
                }
            }
        }

        if (empty($types)) {
            $hotels = $this->relationLoaded('hotels') ? $this->hotels : $this->hotels;
            if ($hotels && $hotels->isNotEmpty()) {
                $hotelTypes = $hotels->pluck('room_type')->filter()->unique();
                foreach ($hotelTypes as $ht) {
                    $types[] = ucfirst(strtolower($ht));
                }
            }
        }

        if (empty($types)) {
            $persons = $this->relationLoaded('persons') ? $this->persons : $this->persons;
            if ($persons && $persons->isNotEmpty()) {
                $personTypes = $persons->pluck('room_type')->filter()->unique();
                foreach ($personTypes as $pt) {
                    $types[] = ucfirst(strtolower($pt));
                }
            }
        }

        return !empty($types) ? implode(', ', $types) : null;
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function persons()
    {
        return $this->hasMany(BookingPerson::class)->orderBy('id', 'asc');
    }

    public function hotels()
    {
        return $this->hasMany(BookingHotel::class);
    }

    public function transports()
    {
        return $this->hasMany(BookingTransport::class);
    }

    public function visas()
    {
        return $this->hasMany(BookingVisa::class);
    }

    public function roomAllocations()
    {
        return $this->hasMany(BookingPersonRoomAllocation::class, 'booking_id');
    }

    public function documents()
    {
        return $this->hasMany(PersonDocument::class, 'booking_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'booking_id');
    }
}
