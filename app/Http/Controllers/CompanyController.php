<?php

namespace App\Http\Controllers;

use App\Enums\CurrencyType;
use App\Models\Company;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CompanyController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('company_view');
        $companies = Company::latest()->get();
        $trashcount = Company::onlyTrashed()->count();
        return view('companies.index', compact('companies', 'trashcount'));
    }

    public function create()
    {
        $this->authorize('company_create');
        $company = null;
        $nextCode = Company::generateCompanyCode();
        return view('companies.create', compact('company', 'nextCode'));
    }

    public function store(Request $request)
    {
        $this->authorize('company_create');

        if (!$request->filled('company_code')) {
            $request->merge([
                'company_code' => Company::generateCompanyCode(),
            ]);
        }

        $request->validate([
            // Referral Details
            'company_name'  => 'required|string|max:255',
            'company_code'  => 'nullable|string|max:255|unique:companies,company_code',
            'quota'         => 'nullable|integer|min:0',
            'currency_type' => ['nullable', new Enum(CurrencyType::class)],

            // Address
            'address'                => 'nullable|array',
            'address.*.address_of'   => 'nullable|string|max:255',
            'address.*.address'      => 'nullable|string',
            'address.*.country'      => 'nullable|string|max:255',
            'address.*.city'         => 'nullable|string|max:255',
            'address.*.po_box'       => 'nullable|string|max:255',
            'address.*.zip_code'     => 'nullable|string|max:255',
            'address.*.is_preferred' => 'nullable|in:Yes,No',

            // Contact
            'contact'                => 'nullable|array',
            'contact.*.contact_type' => 'nullable|string|max:255',
            'contact.*.contact'      => 'nullable|string|max:255',
            'contact.*.is_preferred' => 'nullable|in:Yes,No',

            // Email
            'email'                  => 'nullable|array',
            'email.*.email_type'     => 'nullable|string|max:255',
            'email.*.email'          => 'nullable|email|max:255',
            'email.*.is_preferred'   => 'nullable|in:Yes,No',
        ], [
            'company_name.required' => 'Referral name is required.',
            'company_code.unique'   => 'This referral code has already been taken.',
        ]);

        DB::beginTransaction();

        try {
            $company = Company::create([
                'company_name'  => $request->company_name,
                'company_code'  => $request->company_code,
                'quota'         => $request->quota,
                'currency_type' => $request->currency_type,
            ]);

            // ADDRESS
            foreach ($request->input('address', []) as $addr) {
                if (empty($addr['address_of']) && empty($addr['address'])) {
                    continue;
                }

                $company->addresses()->create([
                    'address_of'   => $addr['address_of'] ?? '',
                    'address'      => $addr['address'] ?? '',
                    'country'      => $addr['country'] ?? '',
                    'city'         => $addr['city'] ?? '',
                    'po_box'       => $addr['po_box'] ?? null,
                    'zip_code'     => $addr['zip_code'] ?? null,
                    'is_preferred' => ($addr['is_preferred'] ?? 'No') === 'Yes',
                ]);
            }

            // CONTACT
            foreach ($request->input('contact', []) as $con) {
                if (empty($con['contact_type']) && empty($con['contact'])) {
                    continue;
                }

                $company->contactNumbers()->create([
                    'contact_type' => $con['contact_type'] ?? '',
                    'contact'      => $con['contact'] ?? '',
                    'is_preferred' => ($con['is_preferred'] ?? 'No') === 'Yes',
                ]);
            }

            // EMAIL
            foreach ($request->input('email', []) as $em) {
                if (empty($em['email_type']) && empty($em['email'])) {
                    continue;
                }

                $company->emails()->create([
                    'email_type'   => $em['email_type'] ?? '',
                    'email'        => $em['email'] ?? '',
                    'is_preferred' => ($em['is_preferred'] ?? 'No') === 'Yes',
                ]);
            }

            DB::commit();

            return redirect()
                ->route('company.index')
                ->with('success', 'Referral created successfully.');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong. ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('company_edit');
        $company = Company::with(['addresses', 'contactNumbers', 'emails'])->findOrFail($id);
        $nextCode = $company->company_code ?: Company::generateCompanyCode();
        return view('companies.create', compact('company', 'nextCode'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('company_edit');
        $company = Company::findOrFail($id);

        if (!$request->filled('company_code')) {
            $request->merge([
                'company_code' => $company->company_code ?: Company::generateCompanyCode(),
            ]);
        }

        $request->validate([
            // Referral Details
            'company_name'  => 'required|string|max:255',
            'company_code'  => ['nullable', 'string', 'max:255', Rule::unique('companies', 'company_code')->ignore($company->id)],
            'quota'         => 'nullable|integer|min:0',
            'currency_type' => ['nullable', new Enum(CurrencyType::class)],

            // Address
            'address'                => 'nullable|array',
            'address.*.address_of'   => 'nullable|string|max:255',
            'address.*.address'      => 'nullable|string',
            'address.*.country'      => 'nullable|string|max:255',
            'address.*.city'         => 'nullable|string|max:255',
            'address.*.po_box'       => 'nullable|string|max:255',
            'address.*.zip_code'     => 'nullable|string|max:255',
            'address.*.is_preferred' => 'nullable|in:Yes,No',

            // Contact
            'contact'                => 'nullable|array',
            'contact.*.contact_type' => 'nullable|string|max:255',
            'contact.*.contact'      => 'nullable|string|max:255',
            'contact.*.is_preferred' => 'nullable|in:Yes,No',

            // Email
            'email'                  => 'nullable|array',
            'email.*.email_type'     => 'nullable|string|max:255',
            'email.*.email'          => 'nullable|email|max:255',
            'email.*.is_preferred'   => 'nullable|in:Yes,No',
        ], [
            'company_name.required' => 'Referral name is required.',
            'company_code.unique'   => 'This referral code has already been taken.',
        ]);

        DB::beginTransaction();

        try {
            $company->update([
                'company_name'  => $request->company_name,
                'company_code'  => $request->company_code,
                'quota'         => $request->quota,
                'currency_type' => $request->currency_type,
            ]);

            $company->addresses()->delete();
            $company->contactNumbers()->delete();
            $company->emails()->delete();

            // ADDRESS
            foreach ($request->input('address', []) as $addr) {
                if (empty($addr['address_of']) && empty($addr['address'])) {
                    continue;
                }

                $company->addresses()->create([
                    'address_of'   => $addr['address_of'] ?? '',
                    'address'      => $addr['address'] ?? '',
                    'country'      => $addr['country'] ?? '',
                    'city'         => $addr['city'] ?? '',
                    'po_box'       => $addr['po_box'] ?? null,
                    'zip_code'     => $addr['zip_code'] ?? null,
                    'is_preferred' => ($addr['is_preferred'] ?? 'No') === 'Yes',
                ]);
            }

            // CONTACT
            foreach ($request->input('contact', []) as $con) {
                if (empty($con['contact_type']) && empty($con['contact'])) {
                    continue;
                }

                $company->contactNumbers()->create([
                    'contact_type' => $con['contact_type'] ?? '',
                    'contact'      => $con['contact'] ?? '',
                    'is_preferred' => ($con['is_preferred'] ?? 'No') === 'Yes',
                ]);
            }

            // EMAIL
            foreach ($request->input('email', []) as $em) {
                if (empty($em['email_type']) && empty($em['email'])) {
                    continue;
                }

                $company->emails()->create([
                    'email_type'   => $em['email_type'] ?? '',
                    'email'        => $em['email'] ?? '',
                    'is_preferred' => ($em['is_preferred'] ?? 'No') === 'Yes',
                ]);
            }

            DB::commit();

            return redirect()
                ->route('company.index')
                ->with('success', 'Referral updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'error' => $e->getMessage()
                ]);
        }
    }

    public function destroy($id)
    {
        $this->authorize('company_trash');
        try {
            $company = Company::findOrFail($id);
            $company->delete();

            return redirect()
                ->back()
                ->with('success', 'Referral moved to trash successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Something went wrong. ' . $e->getMessage());
        }
    }

    public function trash()
    {
        $this->authorize('company_trash_view');
        $companies = Company::onlyTrashed()->get();
        return view('companies.trash', compact('companies'));
    }

    public function restore($id)
    {
        $this->authorize('company_restore');
        try {
            $company = Company::onlyTrashed()->findOrFail($id);
            $company->restore();

            return redirect()
                ->route('company.index')
                ->with('success', 'Referral restored successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Something went wrong. ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('company_show');
        $company = Company::with(['addresses', 'contactNumbers', 'emails'])
            ->findOrFail($id);

        return view('companies.show', compact('company'));
    }
}
