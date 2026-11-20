<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class HajjSeason extends Model
{
    use HasFactory;

    protected $table = 'hajj_seasons';

    protected $fillable = [
        'year',
        'hijri_year',
        'name',
        'is_active',
        'start_date',
        'end_date',
        'notes',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    /**
     * Get active year (strictly defaults to 2027, never 2026)
     */
    public static function getActiveYear(): string
    {
        try {
            $season = static::where('is_active', true)->where('year', '!=', '2026')->first();
            if ($season && !empty($season->year)) {
                return (string) $season->year;
            }
        } catch (\Throwable $e) {}

        return '2027';
    }

    /**
     * Get active season name
     */
    public static function getActiveSeasonName(): string
    {
        try {
            $season = static::where('is_active', true)->where('year', '!=', '2026')->first();
            if ($season && !empty($season->name)) {
                return (string) $season->name;
            }
        } catch (\Throwable $e) {}

        return 'Hajj Season 2027 (1448H)';
    }

    /**
     * Get active Hijri year (e.g. 1448 or 1448H)
     */
    public static function getActiveHijriYear(bool $withSuffix = false): string
    {
        try {
            $season = static::where('is_active', true)->where('year', '!=', '2026')->first();
            if ($season && !empty($season->hijri_year)) {
                $hYear = preg_replace('/[^0-9]/', '', (string)$season->hijri_year);
                return $withSuffix ? ($hYear . 'H') : $hYear;
            }
        } catch (\Throwable $e) {}

        return $withSuffix ? '1448H' : '1448';
    }

    /**
     * Get the currently active Hajj Season (cached or fetched)
     */
    public static function getActive(): self
    {
        $season = static::where('is_active', true)->first();

        if (!$season) {
            // Fallback: get the latest year season and mark it active
            $season = static::orderByDesc('year')->first();
            if ($season) {
                $season->activate();
            } else {
                // If table is completely empty, create default 2027 season
                $season = static::create([
                    'year'        => '2027',
                    'hijri_year'  => '1448',
                    'name'        => 'Hajj Season 2027 (1448H)',
                    'is_active'   => true,
                    'start_date'  => '2027-04-01',
                    'end_date'    => '2027-06-30',
                    'notes'       => 'Default Active Season',
                ]);
            }
        }

        return $season;
    }

    /**
     * Set this season as the active one and deactivate all others
     */
    public function activate(): bool
    {
        // Deactivate all others
        static::where('id', '!=', $this->id)->update(['is_active' => false]);

        $this->is_active = true;
        $saved = $this->save();

        // Sync with dashboard session if in web request
        if (function_exists('session')) {
            try {
                session(['dashboard_year' => (int) $this->year]);
            } catch (\Throwable $e) {}
        }

        return $saved;
    }

    /**
     * Packages linked to this season's year
     */
    public function packages()
    {
        return $this->hasMany(Package::class, 'gregorian_year', 'year');
    }

    /**
     * Bookings linked to this season's year
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'package_year', 'year');
    }

    /**
     * Hajj Applications linked to this season's year
     */
    public function applications()
    {
        return $this->hasMany(HajjApplication::class, 'year', 'year');
    }
}
