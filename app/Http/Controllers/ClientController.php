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
        $request->validate([
            'name'       => 'required|string|max:255',
            'cnic'       => 'nullable|string|max:50|unique:clients,cnic',
            'phone'      => 'nullable|string|max:20',
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

        $request->validate([
            'name'       => 'required|string|max:255',
            'cnic'       => ['nullable', 'string', 'max:50', Rule::unique('clients', 'cnic')->ignore($client->id)],
            'phone'      => 'nullable|string|max:20',
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
