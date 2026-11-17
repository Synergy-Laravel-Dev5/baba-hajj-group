@extends('layout.master')
@section('title', isset($company) && $company ? 'Edit Referral' : 'Add Referral')
@section('header-title', isset($company) && $company ? 'Edit Referral' : 'Add Referral')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container">
                <div class="py-3 d-flex justify-content-between align-items-center">
                    <h4 class="fs-18 fw-semibold m-0">{{ isset($company) && $company ? 'Edit Referral' : 'Add New Referral' }}</h4>
                    <a href="{{ route('company.index') }}" class="btn btn-secondary btn-sm">
                        <i class="mdi mdi-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-4">
                                <form
                                    action="{{ isset($company) && $company ? route('company.update', $company->id) : route('company.store') }}"
                                    method="POST">
                                    @csrf
                                    @if (isset($company) && $company)
                                        @method('PUT')
                                    @endif

                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    @endif

                                    <!-- Navigation Tabs -->
                                    <ul class="nav nav-tabs nav-tabs-custom mb-4" id="referralTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active fw-semibold" id="referral-tab" data-bs-toggle="tab"
                                                data-bs-target="#referral-details" type="button" role="tab">
                                                <i class="mdi mdi-domain me-1 text-primary"></i> Referral Details
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link fw-semibold" id="contact-tab" data-bs-toggle="tab"
                                                data-bs-target="#contact-details" type="button" role="tab">
                                                <i class="mdi mdi-contacts me-1 text-success"></i> Contact Details
                                            </button>
                                        </li>
                                    </ul>

                                    <div class="tab-content" id="referralTabsContent">
                                        <!-- Tab 1: Referral Details (Name, Code [Auto-generated], Quota, Currency Type) -->
                                        <div class="tab-pane fade show active" id="referral-details" role="tabpanel">
                                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                                <h5 class="fs-15 fw-bold text-dark m-0">Referral Information</h5>
                                                <span class="badge bg-primary-subtle text-primary">Core Profile</span>
                                            </div>

                                            <div class="row g-3">
                                                <!-- Referral Name -->
                                                <div class="col-12 col-md-6">
                                                    <div class="mb-3">
                                                        <label for="company_name" class="form-label fw-semibold">
                                                            Referral Name <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light text-muted"><i class="mdi mdi-office-building"></i></span>
                                                            <input type="text" id="company_name" name="company_name"
                                                                placeholder="Enter Referral Name"
                                                                value="{{ old('company_name', $company->company_name ?? '') }}"
                                                                class="form-control @error('company_name') is-invalid @enderror" required>
                                                        </div>
                                                        @error('company_name')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- Referral Code (Auto-generated) -->
                                                <div class="col-12 col-md-6">
                                                    <div class="mb-3">
                                                        <label for="company_code" class="form-label fw-semibold">
                                                            Referral Code <span class="badge bg-secondary-subtle text-secondary fs-11 ms-1">Auto-generated</span>
                                                        </label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light text-muted"><i class="mdi mdi-barcode"></i></span>
                                                            <input type="text" id="company_code" name="company_code"
                                                                placeholder="Auto-generated"
                                                                value="{{ old('company_code', $company->company_code ?? ($nextCode ?? '')) }}"
                                                                class="form-control bg-light fw-medium @error('company_code') is-invalid @enderror" readonly>
                                                        </div>
                                                        <small class="text-muted fs-11">Unique identifier generated automatically</small>
                                                        @error('company_code')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- Quota -->
                                                <div class="col-12 col-md-6">
                                                    <div class="mb-3">
                                                        <label for="quota" class="form-label fw-semibold">Quota</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light text-muted"><i class="mdi mdi-account-group-outline"></i></span>
                                                            <input type="number" id="quota" name="quota"
                                                                placeholder="Enter Quota (e.g. 50)"
                                                                value="{{ old('quota', $company->quota ?? '') }}"
                                                                class="form-control @error('quota') is-invalid @enderror" min="0">
                                                        </div>
                                                        @error('quota')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- Currency Type -->
                                                <div class="col-12 col-md-6">
                                                    <div class="mb-3">
                                                        <label for="currency_type" class="form-label fw-semibold">Currency Type</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light text-muted"><i class="mdi mdi-currency-usd"></i></span>
                                                            <select id="currency_type" name="currency_type"
                                                                class="form-select @error('currency_type') is-invalid @enderror">
                                                                <option value="">Select Currency</option>
                                                                @foreach (\App\Enums\CurrencyType::options() as $value => $label)
                                                                    <option value="{{ $value }}"
                                                                        {{ old('currency_type', isset($company) ? ($company->currency_type?->value ?? $company->currency_type) : 'PKR') == $value ? 'selected' : '' }}>
                                                                        {{ $label }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        @error('currency_type')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tab 2: Contact Details (Address, Contact Numbers, Email) -->
                                        <div class="tab-pane fade" id="contact-details" role="tabpanel">
                                            <!-- ADDRESS SECTION -->
                                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                                <h5 class="fs-15 fw-bold text-dark m-0">
                                                    <i class="mdi mdi-map-marker-outline text-danger me-1"></i> Addresses
                                                </h5>
                                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addRow('address')">
                                                    <i class="mdi mdi-plus me-1"></i> Add Address
                                                </button>
                                            </div>

                                            <div id="address-container">
                                                @php
                                                    $addresses = old('address', isset($company) && $company->addresses->count() ? $company->addresses->toArray() : [['address_of'=>'','address'=>'','country'=>'','city'=>'','po_box'=>'','zip_code'=>'','is_preferred'=>false]]);
                                                @endphp
                                                @foreach ($addresses as $i => $addr)
                                                    <div class="row address-row align-items-end g-2 border-bottom pb-3 mb-3" data-index="{{ $i }}">
                                                        <div class="col-12 col-sm-6 col-md-3">
                                                            <label class="form-label small fw-semibold">Address Of</label>
                                                            <select name="address[{{ $i }}][address_of]" class="form-select form-select-sm">
                                                                <option value="">Select...</option>
                                                                @foreach (['Head Office', 'Branch Office', 'Site Office', 'Warehouse'] as $opt)
                                                                    <option value="{{ $opt }}" {{ ($addr['address_of'] ?? '') == $opt ? 'selected' : '' }}>
                                                                        {{ $opt }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-12 col-sm-6 col-md-3">
                                                            <label class="form-label small fw-semibold">Street Address</label>
                                                            <input type="text" name="address[{{ $i }}][address]"
                                                                value="{{ $addr['address'] ?? '' }}" class="form-control form-control-sm"
                                                                placeholder="Enter Address">
                                                        </div>
                                                        <div class="col-12 col-sm-6 col-md-2">
                                                            <label class="form-label small fw-semibold">Country</label>
                                                            <input type="text" name="address[{{ $i }}][country]"
                                                                value="{{ $addr['country'] ?? '' }}" class="form-control form-control-sm"
                                                                placeholder="Country">
                                                        </div>
                                                        <div class="col-12 col-sm-6 col-md-2">
                                                            <label class="form-label small fw-semibold">City</label>
                                                            <input type="text" name="address[{{ $i }}][city]"
                                                                value="{{ $addr['city'] ?? '' }}" class="form-control form-control-sm"
                                                                placeholder="City">
                                                        </div>
                                                        <div class="col-6 col-md-1">
                                                            <label class="form-label small fw-semibold">Zip</label>
                                                            <input type="text" name="address[{{ $i }}][zip_code]"
                                                                value="{{ $addr['zip_code'] ?? '' }}" class="form-control form-control-sm"
                                                                placeholder="Zip">
                                                        </div>
                                                        <div class="col-6 col-md-1 text-end">
                                                            <button type="button" class="btn btn-sm btn-outline-danger remove-btn" onclick="removeRow(this)" title="Remove">
                                                                <i class="mdi mdi-delete"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <!-- CONTACT NUMBERS SECTION -->
                                            <div class="d-flex justify-content-between align-items-center mb-3 mt-4 pb-2 border-bottom">
                                                <h5 class="fs-15 fw-bold text-dark m-0">
                                                    <i class="mdi mdi-phone-outline text-success me-1"></i> Contact Numbers
                                                </h5>
                                                <button type="button" class="btn btn-outline-success btn-sm" onclick="addRow('contact')">
                                                    <i class="mdi mdi-plus me-1"></i> Add Contact
                                                </button>
                                            </div>

                                            <div id="contact-container">
                                                @php
                                                    $contacts = old('contact', isset($company) && $company->contactNumbers->count() ? $company->contactNumbers->toArray() : [['contact_type'=>'','contact'=>'','is_preferred'=>false]]);
                                                @endphp
                                                @foreach ($contacts as $i => $con)
                                                    <div class="row contact-row align-items-end g-2 border-bottom pb-3 mb-3" data-index="{{ $i }}">
                                                        <div class="col-12 col-sm-6 col-md-4">
                                                            <label class="form-label small fw-semibold">Contact Type</label>
                                                            <select name="contact[{{ $i }}][contact_type]" class="form-select form-select-sm">
                                                                <option value="">Select...</option>
                                                                @foreach (['Mobile', 'Landline', 'WhatsApp', 'Fax'] as $opt)
                                                                    <option value="{{ $opt }}" {{ ($con['contact_type'] ?? '') == $opt ? 'selected' : '' }}>
                                                                        {{ $opt }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-12 col-sm-6 col-md-5">
                                                            <label class="form-label small fw-semibold">Phone / Number</label>
                                                            <input type="text" name="contact[{{ $i }}][contact]"
                                                                value="{{ $con['contact'] ?? '' }}" class="form-control form-control-sm"
                                                                placeholder="e.g. +92 300 1234567">
                                                        </div>
                                                        <div class="col-8 col-md-2">
                                                            <label class="form-label small fw-semibold">Preferred?</label>
                                                            <select name="contact[{{ $i }}][is_preferred]" class="form-select form-select-sm">
                                                                <option value="No" {{ !($con['is_preferred'] ?? false) ? 'selected' : '' }}>No</option>
                                                                <option value="Yes" {{ ($con['is_preferred'] ?? false) ? 'selected' : '' }}>Yes</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-4 col-md-1 text-end">
                                                            <button type="button" class="btn btn-sm btn-outline-danger remove-btn" onclick="removeRow(this)" title="Remove">
                                                                <i class="mdi mdi-delete"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <!-- EMAIL SECTION -->
                                            <div class="d-flex justify-content-between align-items-center mb-3 mt-4 pb-2 border-bottom">
                                                <h5 class="fs-15 fw-bold text-dark m-0">
                                                    <i class="mdi mdi-email-outline text-info me-1"></i> Email Addresses
                                                </h5>
                                                <button type="button" class="btn btn-outline-info btn-sm" onclick="addRow('email')">
                                                    <i class="mdi mdi-plus me-1"></i> Add Email
                                                </button>
                                            </div>

                                            <div id="email-container">
                                                @php
                                                    $emails = old('email', isset($company) && $company->emails->count() ? $company->emails->toArray() : [['email_type'=>'','email'=>'','is_preferred'=>false]]);
                                                @endphp
                                                @foreach ($emails as $i => $em)
                                                    <div class="row email-row align-items-end g-2 border-bottom pb-3 mb-3" data-index="{{ $i }}">
                                                        <div class="col-12 col-sm-6 col-md-4">
                                                            <label class="form-label small fw-semibold">Email Type</label>
                                                            <select name="email[{{ $i }}][email_type]" class="form-select form-select-sm">
                                                                <option value="">Select...</option>
                                                                @foreach (['Official', 'Personal', 'Accounts', 'Billing'] as $opt)
                                                                    <option value="{{ $opt }}" {{ ($em['email_type'] ?? '') == $opt ? 'selected' : '' }}>
                                                                        {{ $opt }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-12 col-sm-6 col-md-5">
                                                            <label class="form-label small fw-semibold">Email Address</label>
                                                            <input type="email" name="email[{{ $i }}][email]"
                                                                value="{{ $em['email'] ?? '' }}" class="form-control form-control-sm"
                                                                placeholder="e.g. info@company.com">
                                                        </div>
                                                        <div class="col-8 col-md-2">
                                                            <label class="form-label small fw-semibold">Preferred?</label>
                                                            <select name="email[{{ $i }}][is_preferred]" class="form-select form-select-sm">
                                                                <option value="No" {{ !($em['is_preferred'] ?? false) ? 'selected' : '' }}>No</option>
                                                                <option value="Yes" {{ ($em['is_preferred'] ?? false) ? 'selected' : '' }}>Yes</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-4 col-md-1 text-end">
                                                            <button type="button" class="btn btn-sm btn-outline-danger remove-btn" onclick="removeRow(this)" title="Remove">
                                                                <i class="mdi mdi-delete"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bottom Action Buttons -->
                                    <div class="row mt-4 pt-3 border-top">
                                        <div class="col-12 d-flex justify-content-end gap-2">
                                            <a href="{{ route('company.index') }}" class="btn btn-secondary px-4">
                                                Cancel
                                            </a>
                                            <button type="submit" class="btn btn-primary px-4">
                                                <i class="mdi mdi-content-save me-1"></i>
                                                {{ isset($company) && $company ? 'Update Referral' : 'Save Referral' }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function addRow(type) {
        const container = document.getElementById(type + '-container');
        if (!container) return;
        const rows = container.querySelectorAll('.' + type + '-row');
        const newIndex = rows.length;
        if (rows.length === 0) return;

        const firstRow = rows[0].cloneNode(true);
        firstRow.setAttribute('data-index', newIndex);

        firstRow.querySelectorAll('input, select').forEach(function(el) {
            const name = el.getAttribute('name');
            if (name) {
                el.setAttribute('name', name.replace(/\[\d+\]/, '[' + newIndex + ']'));
            }
            if (el.tagName === 'SELECT') {
                el.selectedIndex = 0;
            } else {
                el.value = '';
            }
        });

        container.appendChild(firstRow);
        toggleRemoveButtons(type + '-container');
    }

    function removeRow(btn) {
        const row = btn.closest('.row');
        const container = row.parentElement;
        if (container.querySelectorAll('.row').length > 1) {
            row.remove();
            toggleRemoveButtons(container.id);
        }
    }

    function toggleRemoveButtons(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        const rows = container.querySelectorAll('.row');
        rows.forEach(function(row) {
            const btn = row.querySelector('.remove-btn');
            if (!btn) return;
            btn.style.display = rows.length > 1 ? 'inline-block' : 'none';
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleRemoveButtons('address-container');
        toggleRemoveButtons('contact-container');
        toggleRemoveButtons('email-container');
    });
</script>
@endpush
