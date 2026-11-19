<?php

namespace App\Http\Controllers;

use App\Models\HajjSeason;
use App\Models\Package;
use App\Models\Booking;
use App\Models\HajjApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class HajjSeasonController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('hajj_season_view');

        $seasons = HajjSeason::orderByDesc('year')->get();
        $activeSeason = HajjSeason::where('is_active', true)->first();

        // Calculate stats per season
        $seasonStats = [];
        foreach ($seasons as $season) {
            $yr = (string) $season->year;
            $pkgCount = Package::where(function ($q) use ($yr) {
                $q->where('gregorian_year', $yr)
                  ->orWhere('year', $yr);
            })->count();

            $bookings = Booking::with('persons')->where('package_year', $yr)->get();
            $bookingCount = $bookings->count();
            $pilgrimCount = $bookings->sum(function ($b) {
                return max($b->persons->count(), (int)($b->no_of_pax ?: 0));
            });

            $appCount = HajjApplication::where('year', $yr)->count();

            $seasonStats[$season->id] = [
                'packages'     => $pkgCount,
                'bookings'     => $bookingCount,
                'pilgrims'     => $pilgrimCount,
                'applications' => $appCount,
            ];
        }

        return view('hajj_season.index', compact('seasons', 'activeSeason', 'seasonStats'));
    }

    public function store(Request $request)
    {
        $this->authorize('hajj_season_create');

        $request->validate([
            'year'       => 'required|string|max:10|unique:hajj_seasons,year',
            'hijri_year' => 'nullable|string|max:20',
            'name'       => 'required|string|max:100',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'notes'      => 'nullable|string',
            'is_active'  => 'nullable|boolean',
        ]);

        $isActive = $request->boolean('is_active');

        // If no seasons exist yet, default first one to active
        if (HajjSeason::count() === 0) {
            $isActive = true;
        }

        if ($isActive) {
            HajjSeason::query()->update(['is_active' => false]);
        }

        $season = HajjSeason::create([
            'year'       => trim($request->year),
            'hijri_year' => $request->hijri_year ? trim($request->hijri_year) : null,
            'name'       => trim($request->name),
            'start_date' => $request->start_date ?: null,
            'end_date'   => $request->end_date ?: null,
            'notes'      => $request->notes ?: null,
            'is_active'  => $isActive,
        ]);

        if ($isActive) {
            session(['dashboard_year' => (int) $season->year]);
        }

        if (function_exists('logUserActivity')) {
            logUserActivity('hajj_season_create', "Created Hajj Season {$season->name} ({$season->year})", $season->id, 'HajjSeason');
        }

        return redirect()->route('hajj-season.index')->with('success', "Hajj Season '{$season->name}' has been created successfully!");
    }

    public function update(Request $request, $id)
    {
        $this->authorize('hajj_season_edit');

        $season = HajjSeason::findOrFail($id);

        $request->validate([
            'year'       => 'required|string|max:10|unique:hajj_seasons,year,' . $season->id,
            'hijri_year' => 'nullable|string|max:20',
            'name'       => 'required|string|max:100',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'notes'      => 'nullable|string',
            'is_active'  => 'nullable|boolean',
        ]);

        $isActive = $request->boolean('is_active');

        if ($isActive && !$season->is_active) {
            $season->activate();
        }

        $season->update([
            'year'       => trim($request->year),
            'hijri_year' => $request->hijri_year ? trim($request->hijri_year) : null,
            'name'       => trim($request->name),
            'start_date' => $request->start_date ?: null,
            'end_date'   => $request->end_date ?: null,
            'notes'      => $request->notes ?: null,
        ]);

        if (function_exists('logUserActivity')) {
            logUserActivity('hajj_season_update', "Updated Hajj Season {$season->name} ({$season->year})", $season->id, 'HajjSeason');
        }

        return redirect()->route('hajj-season.index')->with('success', "Hajj Season '{$season->name}' updated successfully!");
    }

    public function setActive($id)
    {
        $this->authorize('hajj_season_edit');

        $season = HajjSeason::findOrFail($id);
        $season->activate();

        if (function_exists('logUserActivity')) {
            logUserActivity('hajj_season_activate', "Activated Hajj Season {$season->name} ({$season->year}) as system default", $season->id, 'HajjSeason');
        }

        return redirect()->back()->with('success', "✅ Hajj Season {$season->year} ({$season->name}) is now ACTIVE! The entire system is now operating under this season.");
    }

    public function destroy($id)
    {
        $this->authorize('hajj_season_trash');

        $season = HajjSeason::findOrFail($id);

        if ($season->is_active) {
            return redirect()->back()->with('error', "Cannot delete the ACTIVE Hajj Season ({$season->year}). Please activate another season before deleting this one.");
        }

        $bookingCount = Booking::where('package_year', (string)$season->year)->count();
        if ($bookingCount > 0) {
            return redirect()->back()->with('error', "Cannot delete Hajj Season {$season->year} because it has {$bookingCount} active booking(s) associated with it.");
        }

        $seasonName = $season->name;
        $season->delete();

        if (function_exists('logUserActivity')) {
            logUserActivity('hajj_season_delete', "Deleted Hajj Season {$seasonName}", $id, 'HajjSeason');
        }

        return redirect()->route('hajj-season.index')->with('success', "Hajj Season '{$seasonName}' has been deleted successfully!");
    }
}
