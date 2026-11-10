@extends('layout.master')

@section('title', 'Edit Package')

@section('content')
    <style>
        .accommodation-row {
            position: relative;
        }
        .accommodation-row:not(:first-child) {
            margin-top: 25px !important;
        }
        .section-subhead {
            font-size: 14px;
            font-weight: 700;
            color: #071527;
            border-bottom: 2px solid #C59A27;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
    </style>

    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="py-3 d-flex align-items-center justify-content-between">
                    <h4 class="fs-18 fw-semibold m-0">Package / <strong>Edit #{{ $package->id }}</strong></h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('package.show', $package->id) }}" class="btn btn-info btn-sm text-white">
                            <i class="mdi mdi-eye me-1"></i> View Brochure
                        </a>
                        <a href="{{ route('package.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="mdi mdi-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>

                @if (isset($errors) && $errors->any())
                    <div class="alert alert-danger">
                        <ul class="m-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('package.update', $package->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="package[package_type]" value="{{ $package->package_type ?: 'hajj' }}">

                    <ul class="nav nav-pills mb-3" id="packageTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active fw-bold" data-bs-toggle="pill" data-bs-target="#tab-package" type="button">
                                📋 PACKAGE & DURATION
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" data-bs-toggle="pill" data-bs-target="#tab-camp" type="button">
                                ⛺ CAMP & PRICING
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" data-bs-toggle="pill" data-bs-target="#tab-qurbani" type="button">
                                🐑 QURBANI
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" data-bs-toggle="pill" data-bs-target="#tab-accommodation" type="button">
                                🏨 ACCOMMODATION & ITINERARY
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" data-bs-toggle="pill" data-bs-target="#tab-terms" type="button">
                                📜 INCLUSIONS & INSTRUCTIONS
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" data-bs-toggle="pill" data-bs-target="#tab-transport" type="button">
                                🚌 TRANSPORT & FLIGHTS
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" data-bs-toggle="pill" data-bs-target="#tab-training" type="button">
                                🎁 TRAINING & GIFTS
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content card">

                        {{-- ═════════════════════════════════════════
                             TAB 1: PACKAGE & DURATION
                        ═════════════════════════════════════════ --}}
                        <div class="tab-pane fade show active card-body" id="tab-package">
                            <h5 class="section-subhead">📋 Package Basic Info & Duration</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Referral</label>
                                    <select name="company_id" class="form-select">
                                        <option value="">-- Select Referral --</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}" {{ old('company_id', $package->company_id) == $company->id ? 'selected' : '' }}>
                                                {{ $company->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Package #</label>
                                    <input type="text" name="package_number" class="form-control" placeholder="e.g. PKG-2027-01" value="{{ old('package_number', $package->package_number) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Package Title</label>
                                    <input type="text" name="package_title" class="form-control" placeholder="e.g. HAJJ PACKAGE 1448 - 2027" value="{{ old('package_title', $package->package_title ?? 'HAJJ PACKAGE 1448 - 2027') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Package Code</label>
                                    <input type="text" name="code" class="form-control" placeholder="e.g. HJ-27-LS" value="{{ old('code', $package->code) }}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Stay Type</label>
                                    <select name="stay_type" class="form-select">
                                        <option value="LONG STAY" {{ old('stay_type', $package->stay_type) == 'LONG STAY' ? 'selected' : '' }}>LONG STAY</option>
                                        <option value="SHORT STAY" {{ old('stay_type', $package->stay_type) == 'SHORT STAY' ? 'selected' : '' }}>SHORT STAY</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Stay Duration</label>
                                    <input type="text" name="stay_duration" class="form-control" placeholder="e.g. 29 - 30 DAYS" value="{{ old('stay_duration', $package->stay_duration ?? ($package->days ? $package->days . ' DAYS' : '29 - 30 DAYS')) }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold">Total Days</label>
                                    <input type="number" min="1" name="days" class="form-control" placeholder="30" value="{{ old('days', $package->days ?? 30) }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold">Hijri Year</label>
                                    <input type="text" name="hijri_year" class="form-control" placeholder="1448" value="{{ old('hijri_year', $package->hijri_year ?? '1448') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold">Gregorian Year</label>
                                    <input type="text" name="gregorian_year" class="form-control" placeholder="2027" value="{{ old('gregorian_year', $package->gregorian_year ?? ($package->year ?? '2027')) }}">
                                </div>
                            </div>

                            <h5 class="section-subhead">✈️ Flight Route & Dates (For Brochure Header)</h5>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Departure Date & Hijri</label>
                                    <input type="text" name="departure_date_str" class="form-control" placeholder="e.g. 28-29 Apr / 21-22 Zil Qadh" value="{{ old('departure_date_str', $package->departure_date_str ?? '28-29 Apr / 21-22 Zil Qadh') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Departure Sector</label>
                                    <input type="text" name="departure_sector" class="form-control" placeholder="e.g. Karachi to Jeddah" value="{{ old('departure_sector', $package->departure_sector ?? 'Karachi to Jeddah') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Arrival Date & Hijri</label>
                                    <input type="text" name="arrival_date_str" class="form-control" placeholder="e.g. 28 May / 22 Zil Hajj" value="{{ old('arrival_date_str', $package->arrival_date_str ?? '28 May / 22 Zil Hajj') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Arrival Sector</label>
                                    <input type="text" name="arrival_sector" class="form-control" placeholder="e.g. Jeddah/Madinah to Karachi" value="{{ old('arrival_sector', $package->arrival_sector ?? 'Jeddah/Madinah to Karachi') }}">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-primary" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('button[data-bs-target=\'#tab-camp\']')).show()">
                                    Next: Camp & Pricing <i class="mdi mdi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        {{-- ═════════════════════════════════════════
                             TAB 2: CAMP & MAKTAB PRICING
                        ═════════════════════════════════════════ --}}
                        <div class="tab-pane fade card-body" id="tab-camp">
                            <h5 class="section-subhead">⛺ Maktab / Camp Category & Zone</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Maktab Category</label>
                                    <input type="text" name="camp_category" class="form-control" placeholder="e.g. C, A, A & C" value="{{ old('camp_category', $package->camp_category ?? ($package->maktab ?? 'C')) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Camp Zone</label>
                                    <input type="text" name="camp_zone" class="form-control" placeholder="e.g. ZONE 5 or ZONE 1 OR 2" value="{{ old('camp_zone', $package->camp_zone ?? ($package->zone ?? 'ZONE 5')) }}">
                                </div>
                            </div>

                            <div class="row g-4 mb-4">
                                {{-- Maktab C Rates --}}
                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-light">
                                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">📍 MAKTAB C (ZONE 5) RATES</h6>
                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Quad / Sharing (PKR)</label>
                                                <input type="number" name="maktab_c_quad_pkr" class="form-control" placeholder="0" value="{{ old('maktab_c_quad_pkr', $package->maktab_c_quad_pkr ?? ($package->adult_pkr ?? '')) }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Quad / Sharing (USD $)</label>
                                                <input type="number" name="maktab_c_quad_usd" class="form-control" placeholder="0" value="{{ old('maktab_c_quad_usd', $package->maktab_c_quad_usd ?? ($package->adult_usd ?? '')) }}">
                                            </div>
                                        </div>
                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Triple (PKR)</label>
                                                <input type="number" name="maktab_c_triple_pkr" class="form-control" placeholder="0" value="{{ old('maktab_c_triple_pkr', $package->maktab_c_triple_pkr ?? '') }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Triple (USD $)</label>
                                                <input type="number" name="maktab_c_triple_usd" class="form-control" placeholder="0" value="{{ old('maktab_c_triple_usd', $package->maktab_c_triple_usd ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Double (PKR)</label>
                                                <input type="number" name="maktab_c_double_pkr" class="form-control" placeholder="0" value="{{ old('maktab_c_double_pkr', $package->maktab_c_double_pkr ?? '') }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Double (USD $)</label>
                                                <input type="number" name="maktab_c_double_usd" class="form-control" placeholder="0" value="{{ old('maktab_c_double_usd', $package->maktab_c_double_usd ?? '') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Maktab A Rates (Optional Comparison) --}}
                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-light">
                                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">📍 MAKTAB A (ZONE 1 OR 2) RATES (Optional)</h6>
                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Quad / Sharing (PKR)</label>
                                                <input type="number" name="maktab_a_quad_pkr" class="form-control" placeholder="0" value="{{ old('maktab_a_quad_pkr', $package->maktab_a_quad_pkr ?? '') }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Quad / Sharing (USD $)</label>
                                                <input type="number" name="maktab_a_quad_usd" class="form-control" placeholder="0" value="{{ old('maktab_a_quad_usd', $package->maktab_a_quad_usd ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Triple (PKR)</label>
                                                <input type="number" name="maktab_a_triple_pkr" class="form-control" placeholder="0" value="{{ old('maktab_a_triple_pkr', $package->maktab_a_triple_pkr ?? '') }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Triple (USD $)</label>
                                                <input type="number" name="maktab_a_triple_usd" class="form-control" placeholder="0" value="{{ old('maktab_a_triple_usd', $package->maktab_a_triple_usd ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Double (PKR)</label>
                                                <input type="number" name="maktab_a_double_pkr" class="form-control" placeholder="0" value="{{ old('maktab_a_double_pkr', $package->maktab_a_double_pkr ?? '') }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Double (USD $)</label>
                                                <input type="number" name="maktab_a_double_usd" class="form-control" placeholder="0" value="{{ old('maktab_a_double_usd', $package->maktab_a_double_usd ?? '') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Azizia Separate Room Addon --}}
                            <h5 class="section-subhead">🏢 Azizia Separate Room Add-On Rates</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Quad / Sharing (PKR / USD)</label>
                                    <div class="input-group">
                                        <input type="number" name="azizia_quad_pkr" class="form-control" placeholder="PKR" value="{{ old('azizia_quad_pkr', $package->azizia_quad_pkr ?? '') }}">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="azizia_quad_usd" class="form-control" placeholder="USD" value="{{ old('azizia_quad_usd', $package->azizia_quad_usd ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Triple (PKR / USD)</label>
                                    <div class="input-group">
                                        <input type="number" name="azizia_triple_pkr" class="form-control" placeholder="PKR" value="{{ old('azizia_triple_pkr', $package->azizia_triple_pkr ?? '') }}">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="azizia_triple_usd" class="form-control" placeholder="USD" value="{{ old('azizia_triple_usd', $package->azizia_triple_usd ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Double (PKR / USD)</label>
                                    <div class="input-group">
                                        <input type="number" name="azizia_double_pkr" class="form-control" placeholder="PKR" value="{{ old('azizia_double_pkr', $package->azizia_double_pkr ?? '') }}">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="azizia_double_usd" class="form-control" placeholder="USD" value="{{ old('azizia_double_usd', $package->azizia_double_usd ?? '') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('button[data-bs-target=\'#tab-package\']')).show()">
                                    <i class="mdi mdi-arrow-left me-1"></i> Prev
                                </button>
                                <button type="button" class="btn btn-primary" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('button[data-bs-target=\'#tab-qurbani\']')).show()">
                                    Next: Qurbani <i class="mdi mdi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        {{-- ═════════════════════════════════════════
                             TAB 3: QURBANI
                        ═════════════════════════════════════════ --}}
                        <div class="tab-pane fade card-body" id="tab-qurbani">
                            <h5 class="section-subhead">🐑 Qurbani Policy & Charges</h5>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Qurbani Status in Package</label>
                                    <select name="qurbani_status" class="form-select">
                                        <option value="Not Included (Nusuk Masar System)" {{ old('qurbani_status', $package->qurbani_status) == 'Not Included (Nusuk Masar System)' ? 'selected' : '' }}>
                                            Not Included (Nusuk Masar System)
                                        </option>
                                        <option value="Included in Package" {{ old('qurbani_status', $package->qurbani_status) == 'Included in Package' ? 'selected' : '' }}>
                                            Included in Package
                                        </option>
                                        <option value="Separate Optional Add-on" {{ old('qurbani_status', $package->qurbani_status) == 'Separate Optional Add-on' ? 'selected' : '' }}>
                                            Separate Optional Add-on
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Estimated Qurbani Charges (Per Person SAR / PKR)</label>
                                    <input type="number" name="qurbani_charges" class="form-control" placeholder="e.g. 750" value="{{ old('qurbani_charges', $package->qurbani_charges ?? 0) }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Qurbani Notice Text (Appears in Brochure)</label>
                                    <textarea name="qurbani_note" class="form-control" rows="3">{{ old('qurbani_note', $package->qurbani_note ?? 'Qurbani is not included in the package. Qurbani charges will be added separately as per the applicable charges in the Nusuk Masar system.') }}</textarea>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('button[data-bs-target=\'#tab-camp\']')).show()">
                                    <i class="mdi mdi-arrow-left me-1"></i> Prev
                                </button>
                                <button type="button" class="btn btn-primary" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('button[data-bs-target=\'#tab-accommodation\']')).show()">
                                    Next: Accommodation <i class="mdi mdi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        {{-- ═════════════════════════════════════════
                             TAB 4: ACCOMMODATION & ITINERARY
                        ═════════════════════════════════════════ --}}
                        {{-- ═════════════════════════════════════════
                             TAB 4: ACCOMMODATION & ITINERARY
                        ═════════════════════════════════════════ --}}
                        <div class="tab-pane fade card-body" id="tab-accommodation">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="section-subhead m-0">🏨 Stay Schedule (Makkah, Azizia, Hajj Days, Madinah)</h5>
                                    <small class="text-muted">Hotels load dynamically from Hotel CRUD; Meal Plans load dynamically from Meal Plan CRUD.</small>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm" id="add-accommodation-btn">
                                    <i class="mdi mdi-plus me-1"></i> Add Stay Row
                                </button>
                            </div>

                            @php
                                $accs = old('accommodations');
                                if ($accs === null) {
                                    $accs = $package->accommodations ?? collect();
                                }
                                $hasAccs = is_countable($accs) ? (count($accs) > 0) : false;
                            @endphp

                            <div id="no-acc-placeholder" class="text-center py-4 border rounded bg-white text-muted mb-3 {{ $hasAccs ? 'd-none' : '' }}">
                                <i class="mdi mdi-hotel fs-36 text-primary d-block mb-2"></i>
                                <p class="mb-1 fw-semibold text-dark">No Stay Segments Added Yet</p>
                                <p class="small text-muted mb-3">Click "+ Add Stay Row" above to add Makkah, Madinah, Azizia, or Hajj Days stay segments.</p>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('add-accommodation-btn').click()">
                                    <i class="mdi mdi-plus me-1"></i> Add First Stay Row
                                </button>
                            </div>

                            <div id="accommodations-container">
                                @if ($hasAccs)
                                    @foreach ($accs as $idx => $acc)
                                        @php
                                            $isObj = is_object($acc);
                                            $curPlace = old('accommodations.'.$idx.'.place', $isObj ? ($acc->place ?? '') : ($acc['place'] ?? ''));
                                            $curHotel = old('accommodations.'.$idx.'.package_a.hotel', $isObj ? ($acc->package_a_hotel ?? ($acc->hotel ?? '')) : ($acc['package_a']['hotel'] ?? ($acc['hotel'] ?? '')));
                                            $curMeal  = old('accommodations.'.$idx.'.food_package', $isObj ? ($acc->food_package ?? ($acc->package_a_food_package ?? '')) : ($acc['food_package'] ?? ''));
                                            $curNote  = old('accommodations.'.$idx.'.note', $isObj ? ($acc->note ?? '') : ($acc['note'] ?? ''));
                                            $curSharing = old('accommodations.'.$idx.'.sharing', $isObj ? ($acc->sharing ?? '') : ($acc['sharing'] ?? ''));
                                            $curSharingType = old('accommodations.'.$idx.'.sharing_type', $isObj ? ($acc->sharing_type ?? '') : ($acc['sharing_type'] ?? ''));
                                            $rawCheckIn = $isObj ? ($acc->check_in ? \Carbon\Carbon::parse($acc->check_in)->format('Y-m-d') : '') : ($acc['check_in'] ?? '');
                                            $rawCheckOut = $isObj ? ($acc->check_out ? \Carbon\Carbon::parse($acc->check_out)->format('Y-m-d') : '') : ($acc['check_out'] ?? '');
                                        @endphp
                                        <div class="accommodation-row border rounded p-3 mb-3 bg-light" data-row-index="{{ $idx }}">
                                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                                <span class="badge bg-dark stay-segment-badge">Stay Segment #{{ $loop->iteration }}</span>
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-acc-btn py-0 px-2" style="font-size:12px;">
                                                    <i class="mdi mdi-delete-outline"></i> Remove
                                                </button>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <label class="form-label fw-bold small">Segment / Place</label>
                                                    <div class="input-group input-group-sm">
                                                        <select class="form-select acc-place-select" style="max-width:110px;">
                                                            <option value="">Place...</option>
                                                            <option value="MAKKAH" {{ str_contains(strtoupper($curPlace), 'MAKKAH') ? 'selected' : '' }}>Makkah</option>
                                                            <option value="AZIZIA" {{ str_contains(strtoupper($curPlace), 'AZIZIA') ? 'selected' : '' }}>Azizia</option>
                                                            <option value="HAJJ - DAYS (MINA / ARAFAT)" {{ str_contains(strtoupper($curPlace), 'MINA') || str_contains(strtoupper($curPlace), 'ARAFAT') ? 'selected' : '' }}>Mina/Arafat</option>
                                                            <option value="MADINAH" {{ str_contains(strtoupper($curPlace), 'MADINAH') ? 'selected' : '' }}>Madinah</option>
                                                            <option value="OTHER">Other</option>
                                                        </select>
                                                        <input type="text" name="accommodations[{{ $idx }}][place]" class="form-control acc-place-input" placeholder="e.g. MAKKAH - HOTEL NAME" value="{{ $curPlace }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-bold small">Hotel / Building</label>
                                                    <select class="form-select form-select-sm acc-hotel-select mb-1">
                                                        <option value="">-- Select from Hotel CRUD --</option>
                                                        @foreach ($hotels as $h)
                                                            <option value="{{ $h->name }}" data-place="{{ strtolower($h->place ?? '') }}" {{ $curHotel == $h->name ? 'selected' : '' }}>
                                                                {{ $h->name }} ({{ $h->place ?? 'Hotel' }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="text" name="accommodations[{{ $idx }}][package_a][hotel]" class="form-control form-control-sm acc-hotel-input" placeholder="Hotel / Building Name" value="{{ $curHotel }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-bold small">Arrival Date & Hijri</label>
                                                    <input type="date" name="accommodations[{{ $idx }}][check_in]" class="form-control form-control-sm mb-1" value="{{ old('accommodations.'.$idx.'.check_in', $rawCheckIn) }}">
                                                    <input type="text" name="accommodations[{{ $idx }}][note]" class="form-control form-control-sm" placeholder="Hijri e.g. 21-22 Zil Qadh 1448" value="{{ $curNote }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-bold small">Departure Date & Hijri</label>
                                                    <input type="date" name="accommodations[{{ $idx }}][check_out]" class="form-control form-control-sm mb-1" value="{{ old('accommodations.'.$idx.'.check_out', $rawCheckOut) }}">
                                                    <input type="text" name="accommodations[{{ $idx }}][sharing]" class="form-control form-control-sm" placeholder="Hijri e.g. 29 Zil Qadh 1448" value="{{ $curSharing }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold small">Meal Plan (Meal Plan CRUD)</label>
                                                    <select name="accommodations[{{ $idx }}][food_package]" class="form-select form-select-sm">
                                                        <option value="">-- Select Meal Plan --</option>
                                                        @if(isset($mealPlans) && $mealPlans->count())
                                                            @foreach ($mealPlans as $mp)
                                                                <option value="{{ $mp->name }}" {{ $curMeal == $mp->name ? 'selected' : '' }}>
                                                                    {{ $mp->name }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold small">Meal Detail Note (Optional)</label>
                                                    <input type="text" name="accommodations[{{ $idx }}][sharing_type]" class="form-control form-control-sm" placeholder="e.g. Special Pakistani Menu, 3 Times Buffet" value="{{ $curSharingType }}">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('button[data-bs-target=\'#tab-qurbani\']')).show()">
                                    <i class="mdi mdi-arrow-left me-1"></i> Prev
                                </button>
                                <button type="button" class="btn btn-primary" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('button[data-bs-target=\'#tab-terms\']')).show()">
                                    Next: Inclusions & Terms <i class="mdi mdi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        {{-- ═════════════════════════════════════════
                             TAB 5: INCLUSIONS & INSTRUCTIONS
                        ═════════════════════════════════════════ --}}
                        <div class="tab-pane fade card-body" id="tab-terms">
                            <h5 class="section-subhead">📜 Package Inclusions, Instructions & Notes</h5>

                            @php
                                $inclContent = $package->terms->content ?? (is_array($package->package_included_points) ? implode("\n", $package->package_included_points) : ($package->package_included_points ?? ''));
                                $instContent = is_array($package->instructions_points) ? implode("\n", $package->instructions_points) : ($package->instructions_points ?? '');
                                $docContent = $package->documents_required ?? '';
                                $noteContent = $package->notes ?? ($package->important_note ?? '');
                            @endphp

                            <div class="row g-4 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Package Included (One per line)</label>
                                    <textarea name="terms_content" class="form-control" rows="8">{{ old('terms_content', $inclContent) }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Instructions & Guidelines (One per line)</label>
                                    <textarea name="instructions_content" class="form-control" rows="8">{{ old('instructions_content', $instContent) }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Documents Required (One per line)</label>
                                    <textarea name="documents_required" class="form-control" rows="8">{{ old('documents_required', $docContent) }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Important Notes & Disclaimer</label>
                                <textarea name="notes" class="form-control" rows="4">{{ old('notes', $noteContent) }}</textarea>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('button[data-bs-target=\'#tab-accommodation\']')).show()">
                                    <i class="mdi mdi-arrow-left me-1"></i> Prev
                                </button>
                                <button type="button" class="btn btn-primary" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('button[data-bs-target=\'#tab-transport\']')).show()">
                                    Next: Transport <i class="mdi mdi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        {{-- ═════════════════════════════════════════
                             TAB 6: TRANSPORT & FLIGHTS
                        ═════════════════════════════════════════ --}}
                        <div class="tab-pane fade card-body" id="tab-transport">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="section-subhead m-0">🚌 Transport & Flight Routes</h5>
                                    <small class="text-muted">Routes load dynamically from Route CRUD; Vehicles load dynamically from Vehicle CRUD.</small>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm" id="add-package-transport-btn">
                                    <i class="mdi mdi-plus me-1"></i> Add Route Segment
                                </button>
                            </div>

                            <div id="package-transports-container">
                                @php
                                    $transData = old('transports');
                                    if ($transData === null) {
                                        $transData = $package->transports && $package->transports->count() > 0 ? $package->transports : collect([ (object)['route' => '', 'vehicle' => '', 'type' => 'Full Complete Hajj Transport'] ]);
                                    }
                                @endphp
                                @foreach ($transData as $tIdx => $tRow)
                                    @php
                                        $tIsObj = is_object($tRow);
                                        $tRoute = old('transports.'.$tIdx.'.route', $tIsObj ? ($tRow->route ?? '') : ($tRow['route'] ?? ''));
                                        $tVeh   = old('transports.'.$tIdx.'.vehicle', $tIsObj ? ($tRow->vehicle ?? '') : ($tRow['vehicle'] ?? ''));
                                        $tType  = old('transports.'.$tIdx.'.type', $tIsObj ? ($tRow->type ?? '') : ($tRow['type'] ?? ''));
                                    @endphp
                                    <div class="transport-row border rounded p-3 mb-3 bg-light" data-row-index="{{ $tIdx }}">
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                            <span class="badge bg-secondary transport-segment-badge">Transport Segment #{{ $loop->iteration }}</span>
                                            @if ($tIdx > 0)
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-trans-btn py-0 px-2" style="font-size:12px;">
                                                    <i class="mdi mdi-delete-outline"></i> Remove
                                                </button>
                                            @endif
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-5">
                                                <label class="form-label fw-bold small">Transport Sector / Route (Route CRUD) <span class="text-danger">*</span></label>
                                                <select name="transports[{{ $tIdx }}][route]" class="form-select form-select-sm pkg-route-select">
                                                    <option value="">-- Select Route from CRUD --</option>
                                                    @if(isset($routes) && $routes->count())
                                                        <optgroup label="Routes (Route Management)">
                                                            @foreach ($routes as $r)
                                                                @php $rVal = $r->formatted_route ?: ($r->start_place . ' TO ' . $r->end_place); @endphp
                                                                <option value="{{ $rVal }}" {{ $tRoute == $rVal ? 'selected' : '' }}>
                                                                    {{ $rVal }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endif
                                                    @if(isset($travelRoutes) && $travelRoutes->count())
                                                        <optgroup label="Travel Routes (Itineraries)">
                                                            @foreach ($travelRoutes as $tr)
                                                                <option value="{{ $tr->name }}" {{ $tRoute == $tr->name ? 'selected' : '' }}>
                                                                    {{ $tr->name }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold small">Vehicle Type (Vehicle CRUD) <span class="text-danger">*</span></label>
                                                <select name="transports[{{ $tIdx }}][vehicle]" class="form-select form-select-sm pkg-vehicle-select">
                                                    <option value="">-- Select Vehicle from CRUD --</option>
                                                    @if(isset($vehicles) && $vehicles->count())
                                                        @foreach ($vehicles as $veh)
                                                            @php
                                                                $vLabel = trim(($veh->vehicle_type ?: 'Vehicle') . ($veh->brand_name ? ' - ' . $veh->brand_name : '') . ($veh->plate_number ? ' (' . $veh->plate_number . ')' : ''));
                                                                $vVal = $veh->vehicle_type ?: $vLabel;
                                                            @endphp
                                                            <option value="{{ $vVal }}" {{ $tVeh == $vVal ? 'selected' : '' }}>
                                                                {{ $vLabel }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold small">Transport Service / Notes</label>
                                                <input type="text" name="transports[{{ $tIdx }}][type]" class="form-control form-control-sm" placeholder="e.g. Full Complete Hajj Transport / AC Buses" value="{{ $tType }}">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('button[data-bs-target=\'#tab-terms\']')).show()">
                                    <i class="mdi mdi-arrow-left me-1"></i> Prev
                                </button>
                                <button type="button" class="btn btn-primary" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('button[data-bs-target=\'#tab-training\']')).show()">
                                    Next: Training & Gifts <i class="mdi mdi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        {{-- ═════════════════════════════════════════
                             TAB 7: TRAINING & GIFTS
                        ═════════════════════════════════════════ --}}
                        <div class="tab-pane fade card-body" id="tab-training">
                            <h5 class="section-subhead">🎁 Hajj Training Sessions & Gifts</h5>

                            @php
                                $selectedSessions = $package->trainingSessions->pluck('id')->toArray();
                                $selectedGiveaways = $package->giveaways->pluck('id')->toArray();
                            @endphp

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Select Training Sessions</label>
                                    <select name="training_sessions[]" class="form-select" multiple size="4">
                                        @foreach ($trainingSessions as $session)
                                            <option value="{{ $session->id }}" {{ in_array($session->id, $selectedSessions) ? 'selected' : '' }}>
                                                {{ $session->title ?? $session->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Hold Ctrl to select multiple sessions</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Select Giveaways & Gifts</label>
                                    <select name="giveaways[]" class="form-select" multiple size="4">
                                        @foreach ($giveaways as $giveaway)
                                            <option value="{{ $giveaway->id }}" {{ in_array($giveaway->id, $selectedGiveaways) ? 'selected' : '' }}>
                                                {{ $giveaway->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Hold Ctrl to select multiple gifts</small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('button[data-bs-target=\'#tab-transport\']')).show()">
                                    <i class="mdi mdi-arrow-left me-1"></i> Prev
                                </button>
                                <button type="submit" class="btn btn-success px-5 fw-bold">
                                    <i class="mdi mdi-check-circle me-1"></i> UPDATE PACKAGE
                                </button>
                            </div>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        const allHotels = @json($hotels ?? []);
        const allMealPlans = @json($mealPlans ?? []);
        const allRoutes = [
            @if(isset($routes) && $routes->count())
                @foreach ($routes as $r)
                    @php $rVal = $r->formatted_route ?: ($r->start_place . ' TO ' . $r->end_place); @endphp
                    { val: @json($rVal), label: @json($rVal), group: 'Routes (Route Management)' },
                @endforeach
            @endif
            @if(isset($travelRoutes) && $travelRoutes->count())
                @foreach ($travelRoutes as $tr)
                    { val: @json($tr->name), label: @json($tr->name), group: 'Travel Routes' },
                @endforeach
            @endif
        ];
        const allVehicles = [
            @if(isset($vehicles) && $vehicles->count())
                @foreach ($vehicles as $veh)
                    @php
                        $vLabel = trim(($veh->vehicle_type ?: 'Vehicle') . ($veh->brand_name ? ' - ' . $veh->brand_name : '') . ($veh->plate_number ? ' (' . $veh->plate_number . ')' : ''));
                        $vVal = $veh->vehicle_type ?: $vLabel;
                    @endphp
                    { val: @json($vVal), label: @json($vLabel) },
                @endforeach
            @endif
        ];

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function filterHotelsForSelect(selectEl, placeVal) {
            if (!selectEl) return;
            const currentVal = selectEl.value;
            const placeClean = (placeVal || '').trim().toLowerCase();

            selectEl.innerHTML = '<option value="">-- Select from Hotel CRUD --</option>';

            allHotels.forEach(h => {
                const hotelPlace = (h.place || '').toLowerCase();
                let shouldShow = true;

                if (placeClean) {
                    if (placeClean.includes('makkah') || placeClean.includes('mecca')) {
                        shouldShow = hotelPlace.includes('makkah') || hotelPlace.includes('mecca');
                    } else if (placeClean.includes('madinah') || placeClean.includes('medina')) {
                        shouldShow = hotelPlace.includes('madinah') || hotelPlace.includes('medina');
                    } else if (placeClean.includes('azizia') || placeClean.includes('aziziya')) {
                        shouldShow = hotelPlace.includes('azizia') || hotelPlace.includes('aziziya');
                    } else if (placeClean.includes('mina') || placeClean.includes('arafat')) {
                        shouldShow = hotelPlace.includes('mina') || hotelPlace.includes('arafat');
                    }
                }

                const opt = document.createElement('option');
                opt.value = h.name;
                opt.textContent = `${h.name} (${h.place || 'Hotel'})`;
                opt.dataset.place = hotelPlace;
                if (!shouldShow) {
                    opt.style.color = '#888';
                }
                if (h.name === currentVal) {
                    opt.selected = true;
                }
                selectEl.appendChild(opt);
            });
        }

        function bindAccommodationRowEvents(rowEl) {
            const placeSel = rowEl.querySelector('.acc-place-select');
            const placeInput = rowEl.querySelector('.acc-place-input');
            const hotelSel = rowEl.querySelector('.acc-hotel-select');
            const hotelInput = rowEl.querySelector('.acc-hotel-input');

            if (placeSel && placeInput) {
                placeSel.addEventListener('change', function() {
                    if (this.value && this.value !== 'OTHER') {
                        placeInput.value = this.value;
                    }
                    filterHotelsForSelect(hotelSel, placeInput.value);
                });

                placeInput.addEventListener('input', function() {
                    filterHotelsForSelect(hotelSel, this.value);
                });
            }

            if (hotelSel && hotelInput) {
                hotelSel.addEventListener('change', function() {
                    if (this.value) {
                        hotelInput.value = this.value;
                    }
                });
            }

            // Initial filter if place is preset
            if (hotelSel && placeInput && placeInput.value) {
                filterHotelsForSelect(hotelSel, placeInput.value);
            }
        }

        let accCounter = document.querySelectorAll('.accommodation-row').length;

        function updateAccPlaceholder() {
            const container = document.getElementById('accommodations-container');
            const placeholder = document.getElementById('no-acc-placeholder');
            if (!container || !placeholder) return;
            const rows = container.querySelectorAll('.accommodation-row');
            if (rows.length === 0) {
                placeholder.classList.remove('d-none');
            } else {
                placeholder.classList.add('d-none');
            }
        }

        document.getElementById('add-accommodation-btn')?.addEventListener('click', function() {
            const container = document.getElementById('accommodations-container');
            const idx = accCounter++;
            const newIndexNumber = container.querySelectorAll('.accommodation-row').length + 1;

            let mealOptionsHtml = '<option value="">-- Select Meal Plan from CRUD --</option>';
            allMealPlans.forEach(mp => {
                mealOptionsHtml += `<option value="${escapeHtml(mp.name)}">${escapeHtml(mp.name)}</option>`;
            });

            const html = `
            <div class="accommodation-row border rounded p-3 mb-3 bg-light" data-row-index="${idx}">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                    <span class="badge bg-dark stay-segment-badge">Stay Segment #${newIndexNumber}</span>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-acc-btn py-0 px-2" style="font-size:12px;">
                        <i class="mdi mdi-delete-outline"></i> Remove
                    </button>
                </div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Segment / Place</label>
                        <div class="input-group input-group-sm">
                            <select class="form-select acc-place-select" style="max-width:110px;">
                                <option value="">Place...</option>
                                <option value="MAKKAH">Makkah</option>
                                <option value="AZIZIA">Azizia</option>
                                <option value="HAJJ - DAYS (MINA / ARAFAT)">Mina/Arafat</option>
                                <option value="MADINAH">Madinah</option>
                                <option value="OTHER">Other</option>
                            </select>
                            <input type="text" name="accommodations[${idx}][place]" class="form-control acc-place-input" placeholder="e.g. MAKKAH - HOTEL NAME">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Hotel / Building</label>
                        <select class="form-select form-select-sm acc-hotel-select mb-1">
                            <option value="">-- Select from Hotel CRUD --</option>
                        </select>
                        <input type="text" name="accommodations[${idx}][package_a][hotel]" class="form-control form-control-sm acc-hotel-input" placeholder="Hotel / Building Name">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Arrival Date & Hijri</label>
                        <input type="date" name="accommodations[${idx}][check_in]" class="form-control form-control-sm mb-1">
                        <input type="text" name="accommodations[${idx}][note]" class="form-control form-control-sm" placeholder="Hijri e.g. 21 - 22 Zil Qadh 1448">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Departure Date & Hijri</label>
                        <input type="date" name="accommodations[${idx}][check_out]" class="form-control form-control-sm mb-1">
                        <input type="text" name="accommodations[${idx}][sharing]" class="form-control form-control-sm" placeholder="Hijri e.g. 29 Zil Qadh 1448">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Meal Plan (Meal Plan CRUD)</label>
                        <select name="accommodations[${idx}][food_package]" class="form-select form-select-sm">
                            ${mealOptionsHtml}
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Meal Detail Note (Optional)</label>
                        <input type="text" name="accommodations[${idx}][sharing_type]" class="form-control form-control-sm" placeholder="e.g. Special Asian Menu, 3 Times Buffet">
                    </div>
                </div>
            </div>`;

            container.insertAdjacentHTML('beforeend', html);
            const addedRow = container.lastElementChild;
            bindAccommodationRowEvents(addedRow);
            updateAccPlaceholder();
        });

        document.getElementById('accommodations-container')?.addEventListener('click', function(e) {
            const btn = e.target.closest('.remove-acc-btn');
            if (btn) {
                const row = btn.closest('.accommodation-row');
                if (row) {
                    row.remove();
                    document.querySelectorAll('.stay-segment-badge').forEach((badge, i) => {
                        badge.textContent = `Stay Segment #${i + 1}`;
                    });
                    updateAccPlaceholder();
                }
            }
        });

        // Initialize existing rows
        document.querySelectorAll('.accommodation-row').forEach(row => {
            bindAccommodationRowEvents(row);
        });
        updateAccPlaceholder();

        // ═════════════════════════════════════════
        // DYNAMIC PACKAGE TRANSPORTS
        // ═════════════════════════════════════════
        let transCounter = document.querySelectorAll('.transport-row').length || 1;

        function buildRouteOptionsHtml(selectedVal) {
            let options = '<option value="">-- Select Route from CRUD --</option>';
            const selectedClean = (selectedVal || '').toLowerCase().trim();
            const groups = {};

            allRoutes.forEach(r => {
                const grp = r.group || 'Routes';
                if (!groups[grp]) groups[grp] = [];
                const isSel = (r.val.toLowerCase().trim() === selectedClean);
                groups[grp].push({ ...r, isSel });
            });

            for (const [groupName, items] of Object.entries(groups)) {
                options += `<optgroup label="${escapeHtml(groupName)}">`;
                items.forEach(item => {
                    options += `<option value="${escapeHtml(item.val)}" ${item.isSel ? 'selected' : ''}>${escapeHtml(item.label)}</option>`;
                });
                options += `</optgroup>`;
            }
            return options;
        }

        function buildVehicleOptionsHtml(selectedVal) {
            let options = '<option value="">-- Select Vehicle from CRUD --</option>';
            const selectedClean = (selectedVal || '').toLowerCase().trim();
            allVehicles.forEach(veh => {
                const isSel = (veh.val.toLowerCase().trim() === selectedClean);
                options += `<option value="${escapeHtml(veh.val)}" ${isSel ? 'selected' : ''}>${escapeHtml(veh.label)}</option>`;
            });
            return options;
        }

        document.getElementById('add-package-transport-btn')?.addEventListener('click', function() {
            const container = document.getElementById('package-transports-container');
            const idx = transCounter++;
            const newIndexNumber = container.querySelectorAll('.transport-row').length + 1;

            const html = `
            <div class="transport-row border rounded p-3 mb-3 bg-light" data-row-index="${idx}">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                    <span class="badge bg-secondary transport-segment-badge">Transport Segment #${newIndexNumber}</span>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-trans-btn py-0 px-2" style="font-size:12px;">
                        <i class="mdi mdi-delete-outline"></i> Remove
                    </button>
                </div>
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-bold small">Transport Sector / Route (Route CRUD) <span class="text-danger">*</span></label>
                        <select name="transports[${idx}][route]" class="form-select form-select-sm pkg-route-select">
                            ${buildRouteOptionsHtml('')}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Vehicle Type (Vehicle CRUD) <span class="text-danger">*</span></label>
                        <select name="transports[${idx}][vehicle]" class="form-select form-select-sm pkg-vehicle-select">
                            ${buildVehicleOptionsHtml('')}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Transport Service / Notes</label>
                        <input type="text" name="transports[${idx}][type]" class="form-control form-control-sm" placeholder="e.g. Full Complete Hajj Transport / AC Buses">
                    </div>
                </div>
            </div>`;

            container.insertAdjacentHTML('beforeend', html);
        });

        document.getElementById('package-transports-container')?.addEventListener('click', function(e) {
            const btn = e.target.closest('.remove-trans-btn');
            if (btn) {
                const row = btn.closest('.transport-row');
                if (row) {
                    row.remove();
                    document.querySelectorAll('.transport-segment-badge').forEach((badge, i) => {
                        badge.textContent = `Transport Segment #${i + 1}`;
                    });
                }
            }
        });
    </script>
@endsection
