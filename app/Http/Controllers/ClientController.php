<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Company;
use App\Models\Package;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ClientController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('client_view');
        $package = session('dashboard_package', 'hajj');
        $year    = (int) session('dashboard_year', Carbon::now()->year);

        $clients = Client::with('company')
            ->where(function ($query) use ($package, $year) {
                $query->whereHas('bookings', function ($q) use ($package, $year) {
                    $q->where('package_type', $package)
                        ->where('package_year', $year);
                })
                ->orWhereDoesntHave('bookings');
            })
            ->latest()
            ->get();

        $trashCount = Client::onlyTrashed()->count();

        return view('client.index', compact('clients', 'trashCount'));
    }

    public function create()
    {
        $this->authorize('client_create');
        $companies = Company::orderBy('company_name')->get();
        return view('client.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $this->authorize('client_create');

        $phone = $request->phone ? trim($request->phone) : null;
        if ($phone === '+92' || $phone === '+92 ') {
            $phone = null;
        } elseif ($phone) {
            $digits = preg_replace('/\D/', '', $phone);
            if ($digits === '92' || empty($digits)) {
                $phone = null;
            } elseif (str_starts_with($digits, '0') && strlen($digits) === 11) {
                $digits = '92' . substr($digits, 1);
            } elseif (!str_starts_with($digits, '92') && strlen($digits) === 10) {
                $digits = '92' . $digits;
            }
            if ($digits && str_starts_with($digits, '92') && strlen($digits) === 12) {
                $phone = '+92 ' . substr($digits, 2, 3) . ' ' . substr($digits, 5);
            }
        }

        $cnic = $request->cnic ? trim($request->cnic) : null;
        if ($cnic) {
            $cnicDigits = preg_replace('/\D/', '', $cnic);
            if (empty($cnicDigits)) {
                $cnic = null;
            } elseif (strlen($cnicDigits) === 13) {
                $cnic = substr($cnicDigits, 0, 5) . '-' . substr($cnicDigits, 5, 7) . '-' . substr($cnicDigits, 12, 1);
            }
        }

        $request->merge([
            'phone' => $phone,
            'cnic'  => $cnic,
        ]);
        $request->validate([
            'name'       => 'required|string|max:255',
            'cnic'       => 'nullable|string|max:50|unique:clients,cnic',
            'phone'      => 'nullable|string|max:30',
            'company_id' => 'nullable|exists:companies,id',
        ], [
            'name.required'     => 'Client name is required.',
            'cnic.unique'       => 'This CNIC is already registered with another client.',
            'company_id.exists' => 'Selected referral does not exist.',
        ]);

        $client = Client::create([
            'name'       => $request->name,
            'cnic'       => $request->cnic,
            'phone'      => $request->phone,
            'company_id' => $request->company_id,
            'type'       => 'client',
            'status'     => 'active',
        ]);

        logUserActivity('Client Created', 'Name: ' . $client->name . ' | CNIC: ' . ($client->cnic ?? 'N/A'), $client->id, 'Client');

        return redirect()->route('client.index')->with('success', 'Client added successfully.');
    }

    public function edit($id)
    {
        $this->authorize('client_edit');
        $client = Client::findOrFail($id);
        $companies = Company::orderBy('company_name')->get();
        return view('client.edit', compact('client', 'companies'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('client_edit');
        $client = Client::findOrFail($id);

        $phone = $request->phone ? trim($request->phone) : null;
        if ($phone === '+92' || $phone === '+92 ') {
            $phone = null;
        } elseif ($phone) {
            $digits = preg_replace('/\D/', '', $phone);
            if ($digits === '92' || empty($digits)) {
                $phone = null;
            } elseif (str_starts_with($digits, '0') && strlen($digits) === 11) {
                $digits = '92' . substr($digits, 1);
            } elseif (!str_starts_with($digits, '92') && strlen($digits) === 10) {
                $digits = '92' . $digits;
            }
            if ($digits && str_starts_with($digits, '92') && strlen($digits) === 12) {
                $phone = '+92 ' . substr($digits, 2, 3) . ' ' . substr($digits, 5);
            }
        }

        $cnic = $request->cnic ? trim($request->cnic) : null;
        if ($cnic) {
            $cnicDigits = preg_replace('/\D/', '', $cnic);
            if (empty($cnicDigits)) {
                $cnic = null;
            } elseif (strlen($cnicDigits) === 13) {
                $cnic = substr($cnicDigits, 0, 5) . '-' . substr($cnicDigits, 5, 7) . '-' . substr($cnicDigits, 12, 1);
            }
        }

        $request->merge([
            'phone' => $phone,
            'cnic'  => $cnic,
        ]);
        $request->validate([
            'name'       => 'required|string|max:255',
            'cnic'       => ['nullable', 'string', 'max:50', Rule::unique('clients', 'cnic')->ignore($client->id)],
            'phone'      => 'nullable|string|max:30',
            'company_id' => 'nullable|exists:companies,id',
        ], [
            'name.required'     => 'Client name is required.',
            'cnic.unique'       => 'This CNIC is already registered with another client.',
            'company_id.exists' => 'Selected referral does not exist.',
        ]);

        $client->update([
            'name'       => $request->name,
            'cnic'       => $request->cnic,
            'phone'      => $request->phone,
            'company_id' => $request->company_id,
        ]);

        logUserActivity('Client Updated', 'Name: ' . $client->name . ' | CNIC: ' . ($client->cnic ?? 'N/A'), $client->id, 'Client');

        return redirect()->route('client.index')->with('success', 'Client updated.');
    }

    public function destroy($id)
    {
        $this->authorize('client_trash');
        $client = Client::findOrFail($id);

        logUserActivity('Client Deleted', 'Name: ' . $client->name . ' | Type: ' . $client->type, $client->id, 'Client');

        $client->delete();

        return redirect()->route('client.index')->with('success', 'Moved to trash.');
    }

    public function trash()
    {
        $this->authorize('client_trash_view');
        $clients = Client::with('company')->onlyTrashed()->latest()->get();
        return view('client.trash', compact('clients'));
    }

    public function restore($id)
    {
        $this->authorize('client_restore');
        $client = Client::onlyTrashed()->findOrFail($id);
        $client->restore();

        logUserActivity('Client Restored', 'Name: ' . $client->name . ' | Type: ' . $client->type, $client->id, 'Client');

        return redirect()->route('client.trash')->with('success', 'Client restored.');
    }
}
