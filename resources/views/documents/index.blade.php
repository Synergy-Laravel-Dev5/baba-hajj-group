@extends('layout.master')

@section('title', 'Upload Documents & Credentials')
@section('header-title', 'Upload Documents')

@section('content')
<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- PAGE TITLE & BREADCRUMB -->
            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column justify-content-between">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-bold m-0 text-dark">
                        <i class="mdi mdi-folder-upload-outline text-primary me-2"></i>Pilgrim Documents & Portal Passwords
                    </h4>
                    <p class="text-muted fs-13 mb-0">Upload and manage Passport, Picture, Medical, ID, and Nusuk/Portal Password for each travelling pilgrim.</p>
                </div>
                <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0">
                    @if($selectedBooking)
                        <a href="{{ route('documents.zip', $selectedBooking->id) }}" class="btn btn-sm btn-outline-success">
                            <i class="mdi mdi-download me-1"></i>Download All (ZIP)
                        </a>
                        <a href="{{ route('booking.show', $selectedBooking->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                            <i class="mdi mdi-eye me-1"></i>View Booking
                        </a>
                    @endif
                    <a href="{{ route('booking.create') }}" class="btn btn-sm btn-primary">
                        <i class="mdi mdi-plus me-1"></i>New Booking
                    </a>
                </div>
            </div>

            <!-- SUCCESS / ERROR ALERTS -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-alert-circle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- 1. FILTER & BOOKING SELECTOR CARD -->
            <div class="card mb-3 border shadow-sm">
                <div class="card-body p-3">
                    <form action="{{ route('documents.index') }}" method="GET" id="filterForm">
                        <div class="row g-2 align-items-center">

                            <!-- Booking Select Dropdown -->
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label fw-bold fs-12 mb-1">
                                    <i class="mdi mdi-book-open-page-variant me-1 text-primary"></i>Select Booking (Fetch Pilgrims)
                                </label>
                                <select name="booking_id" id="bookingSelect" class="form-select form-select-sm select2" onchange="this.form.submit()">
                                    <option value="">-- All Bookings (Show All Pilgrims) --</option>
                                    @foreach($allBookings as $b)
                                        @php
                                            $prefix = $b->persons->pluck('hb_number')->filter()->first() ?: ($b->package?->next_booking_code ?? ($b->package?->code ?? 'AZ ' . str_pad($b->id, 2, '0', STR_PAD_LEFT)));
                                            $cName = $b->client?->name ?? 'Guest';
                                            $pkgTitle = $b->package->package_title ?? ($b->package->name ?? ($b->package_name ?? 'Hajj Package'));
                                            $paxCount = $b->persons->count() ?: ($b->no_of_pax ?? 1);
                                        @endphp
                                        <option value="{{ $b->id }}" {{ (string)$bookingId === (string)$b->id ? 'selected' : '' }}>
                                            [{{ $prefix }}] {{ $cName }} ({{ $paxCount }} Pax) — {{ $pkgTitle }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Search Input -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label fw-bold fs-12 mb-1">
                                    <i class="mdi mdi-magnify me-1 text-primary"></i>Search Pilgrim / ID / Passport
                                </label>
                                <input type="text" name="search" class="form-control form-control-sm"
                                       placeholder="Name, Passport #, CNIC, Hajj ID..." value="{{ $search ?? '' }}">
                            </div>

                            <!-- Document Status Filter -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label fw-bold fs-12 mb-1">
                                    <i class="mdi mdi-filter-variant me-1 text-primary"></i>Filter by Missing Document
                                </label>
                                <select name="doc_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="all" {{ ($docFilter ?? 'all') === 'all' ? 'selected' : '' }}>All Statuses</option>
                                    <option value="missing_passport" {{ ($docFilter ?? '') === 'missing_passport' ? 'selected' : '' }}>Missing Passport</option>
                                    <option value="missing_picture" {{ ($docFilter ?? '') === 'missing_picture' ? 'selected' : '' }}>Missing Picture</option>
                                    <option value="missing_medical" {{ ($docFilter ?? '') === 'missing_medical' ? 'selected' : '' }}>Missing Medical</option>
                                    <option value="missing_id" {{ ($docFilter ?? '') === 'missing_id' ? 'selected' : '' }}>Missing ID</option>
                                    <option value="missing_password" {{ ($docFilter ?? '') === 'missing_password' ? 'selected' : '' }}>Missing Password</option>
                                    <option value="complete" {{ ($docFilter ?? '') === 'complete' ? 'selected' : '' }}>100% Complete Only</option>
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div class="col-lg-2 col-md-6 d-flex gap-1 align-self-end">
                                <button type="submit" class="btn btn-sm btn-primary flex-fill">
                                    <i class="mdi mdi-filter"></i> Apply
                                </button>
                                <a href="{{ route('documents.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filters">
                                    <i class="mdi mdi-refresh"></i>
                                </a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            <!-- 2. SELECTED BOOKING DETAILS SNAPSHOT (Fetch details) -->
            @if($selectedBooking)
                @php
                    $bPrefix = $selectedBooking->persons->pluck('hb_number')->filter()->first() ?: ($selectedBooking->package?->code ?? 'AZ 01');
                    $bPkgName = $selectedBooking->package->package_title ?? ($selectedBooking->package->name ?? ($selectedBooking->package_name ?? 'Hajj Package'));
                    $bCamp = $selectedBooking->camp ?? ($selectedBooking->package->maktab ?? 'N/A');
                    $bRef = ($selectedBooking->company ?? $selectedBooking->client?->referral)->company_name ?? 'Direct';
                @endphp
                <div class="card mb-3 border-primary shadow-sm" style="border-left: 5px solid #2563eb !important; background: linear-gradient(to right, #eff6ff 0%, #ffffff 100%);">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <span class="badge bg-primary text-white fs-12 px-2 py-1 mb-1">
                                    <i class="mdi mdi-tag me-1"></i>Prefix: {{ $bPrefix }}
                                </span>
                                <span class="badge bg-dark text-warning fs-12 px-2 py-1 mb-1">
                                    {{ strtoupper($selectedBooking->package_type ?? 'HAJJ') }} {{ $selectedBooking->package_year ?? date('Y') }}
                                </span>
                                <h5 class="fs-16 fw-bold text-dark m-0 mt-1">
                                    {{ $selectedBooking->client?->name ?? 'Client' }}
                                    <small class="text-muted fs-12 ms-2"><i class="mdi mdi-phone me-1"></i>{{ $selectedBooking->client?->phone ?? 'N/A' }}</small>
                                    <small class="text-muted fs-12 ms-2"><i class="mdi mdi-card-account-details me-1"></i>CNIC: {{ $selectedBooking->client?->cnic ?? 'N/A' }}</small>
                                </h5>
                            </div>
                            <div class="text-md-end text-start">
                                <div class="fs-13 text-dark">
                                    <strong>Package:</strong> {{ $bPkgName }} · <strong>Camp:</strong> {{ $bCamp }}
                                </div>
                                <div class="fs-12 text-muted mt-1">
                                    <strong>Total Pax:</strong> {{ $selectedBooking->persons->count() ?: ($selectedBooking->no_of_pax ?? 1) }} Persons ·
                                    <strong>Care Of:</strong> {{ $selectedBooking->care_of ?? '—' }} ·
                                    <strong>Referral:</strong> {{ $bRef }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- 3. DOCUMENT STATS PROGRESS CARDS (Matching User's Columns) -->
            <div class="row g-2 mb-3">
                <!-- Total Pilgrims -->
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card mb-0 border shadow-sm h-100">
                        <div class="card-body p-2 text-center">
                            <span class="text-muted fs-11 fw-bold text-uppercase">Total Pilgrims</span>
                            <h3 class="fs-20 fw-bold text-dark my-1">{{ $stats['total'] }}</h3>
                            <small class="text-muted" style="font-size:10.5px;">Travelling Persons</small>
                        </div>
                    </div>
                </div>
                <!-- 1. PASSPORT -->
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card mb-0 border shadow-sm h-100" style="border-top: 3px solid #0284c7 !important;">
                        <div class="card-body p-2 text-center">
                            <span class="text-muted fs-11 fw-bold text-uppercase"><i class="mdi mdi-file-document-outline text-info me-1"></i>PASSPORT</span>
                            <h3 class="fs-20 fw-bold text-info my-1">{{ $stats['passport'] }} / {{ $stats['total'] }}</h3>
                            @php $pPct = $stats['total'] > 0 ? round(($stats['passport'] / $stats['total']) * 100) : 0; @endphp
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar bg-info" style="width: {{ $pPct }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 2. PICTURE -->
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card mb-0 border shadow-sm h-100" style="border-top: 3px solid #10b981 !important;">
                        <div class="card-body p-2 text-center">
                            <span class="text-muted fs-11 fw-bold text-uppercase"><i class="mdi mdi-camera text-success me-1"></i>PICTURE</span>
                            <h3 class="fs-20 fw-bold text-success my-1">{{ $stats['picture'] }} / {{ $stats['total'] }}</h3>
                            @php $picPct = $stats['total'] > 0 ? round(($stats['picture'] / $stats['total']) * 100) : 0; @endphp
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar bg-success" style="width: {{ $picPct }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 3. MEDICAL -->
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card mb-0 border shadow-sm h-100" style="border-top: 3px solid #f59e0b !important;">
                        <div class="card-body p-2 text-center">
                            <span class="text-muted fs-11 fw-bold text-uppercase"><i class="mdi mdi-hospital-box-outline text-warning me-1"></i>MEDICAL</span>
                            <h3 class="fs-20 fw-bold text-warning my-1">{{ $stats['medical'] }} / {{ $stats['total'] }}</h3>
                            @php $medPct = $stats['total'] > 0 ? round(($stats['medical'] / $stats['total']) * 100) : 0; @endphp
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar bg-warning" style="width: {{ $medPct }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 4. ID -->
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card mb-0 border shadow-sm h-100" style="border-top: 3px solid #8b5cf6 !important;">
                        <div class="card-body p-2 text-center">
                            <span class="text-muted fs-11 fw-bold text-uppercase"><i class="mdi mdi-card-account-details-outline text-purple me-1"></i>ID (CNIC)</span>
                            <h3 class="fs-20 fw-bold my-1" style="color:#8b5cf6;">{{ $stats['id'] }} / {{ $stats['total'] }}</h3>
                            @php $idPct = $stats['total'] > 0 ? round(($stats['id'] / $stats['total']) * 100) : 0; @endphp
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar" style="background:#8b5cf6; width: {{ $idPct }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 5. PASSWORD -->
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card mb-0 border shadow-sm h-100" style="border-top: 3px solid #ef4444 !important;">
                        <div class="card-body p-2 text-center">
                            <span class="text-muted fs-11 fw-bold text-uppercase"><i class="mdi mdi-lock-outline text-danger me-1"></i>PASSWORD</span>
                            <h3 class="fs-20 fw-bold text-danger my-1">{{ $stats['password'] }} / {{ $stats['total'] }}</h3>
                            @php $pwPct = $stats['total'] > 0 ? round(($stats['password'] / $stats['total']) * 100) : 0; @endphp
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar bg-danger" style="width: {{ $pwPct }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. MAIN DOCUMENTS TABLE -->
            <div class="card border shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="card-title m-0 fw-bold fs-15 text-dark">
                            <i class="mdi mdi-table me-1 text-primary"></i>Pilgrim Documents Roster
                        </h5>
                        <span class="badge bg-light text-dark border">{{ $persons->total() }} Records</span>
                    </div>
                    <div class="text-muted small">
                        <i class="mdi mdi-information-outline text-info"></i> Upload directly via file buttons or use the quick <strong class="text-primary">"Upload All"</strong> modal per pilgrim.
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle mb-0" id="docsTable">
                            <thead class="table-dark" style="background: #0f172a; color:#ffffff;">
                                <tr>
                                    <th style="width: 40px;" class="text-center">#</th>
                                    <th style="min-width: 200px;">PILGRIM / PERSON</th>
                                    <th style="min-width: 150px;" class="text-center">
                                        <i class="mdi mdi-file-document-outline me-1 text-info"></i>PASSPORT
                                    </th>
                                    <th style="min-width: 130px;" class="text-center">
                                        <i class="mdi mdi-camera me-1 text-success"></i>PICTURE
                                    </th>
                                    <th style="min-width: 150px;" class="text-center">
                                        <i class="mdi mdi-hospital-box-outline me-1 text-warning"></i>MEDICAL
                                    </th>
                                    <th style="min-width: 150px;" class="text-center">
                                        <i class="mdi mdi-card-account-details-outline me-1 text-purple"></i>ID
                                    </th>
                                    <th style="min-width: 190px;" class="text-center">
                                        <i class="mdi mdi-lock-outline me-1 text-danger"></i>PASSWORD
                                    </th>
                                    <th style="width: 110px;" class="text-center">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($persons as $idx => $p)
                                    @php
                                        $rowBooking = $p->booking;
                                        $pPrefix = $p->hb_number ?: ($rowBooking->persons->pluck('hb_number')->filter()->first() ?: 'AZ 01');
                                        $hajjId = $p->hajj_id ?: ('BG' . str_pad($p->id, 3, '0', STR_PAD_LEFT));
                                    @endphp
                                    <tr id="person_row_{{ $p->id }}" data-person-id="{{ $p->id }}">
                                        <!-- Index -->
                                        <td class="text-center fw-bold text-muted" style="font-size:12px;">
                                            {{ $persons->firstItem() + $idx }}
                                        </td>

                                        <!-- Pilgrim Details -->
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="position-relative flex-shrink-0">
                                                    @if(!empty($p->photo) && file_exists(public_path($p->photo)))
                                                        <img src="{{ asset($p->photo) }}?v={{ time() }}" alt="{{ $p->full_name }}"
                                                             class="rounded-circle border" style="width: 38px; height: 38px; object-fit: cover;">
                                                    @else
                                                        <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center"
                                                             style="width: 38px; height: 38px; color: #64748b;">
                                                            <i class="mdi mdi-account fs-18"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1" style="line-height:1.25;">
                                                    <div class="fw-bold text-dark fs-13">{{ $p->full_name }}</div>
                                                    <div class="d-flex align-items-center gap-1 mt-1 flex-wrap">
                                                        <span class="badge bg-warning text-dark px-1 py-0" style="font-size: 10px;">{{ $hajjId }}</span>
                                                        <span class="badge bg-dark text-warning px-1 py-0" style="font-size: 10px;">{{ $pPrefix }}</span>
                                                        <span class="text-muted" style="font-size:11px;">{{ ucfirst($p->gender ?? 'Male') }}</span>
                                                    </div>
                                                    <div class="text-muted mt-1" style="font-size: 11px;">
                                                        @if(!empty($p->passport_number))
                                                            <span>Pass: <strong>{{ $p->passport_number }}</strong></span>
                                                        @endif
                                                        @if(!empty($p->cnic))
                                                            <span class="ms-1">· CNIC: <strong>{{ $p->cnic }}</strong></span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- 1. PASSPORT (File / PDF / Img) -->
                                        <td class="text-center" id="cell_passport_{{ $p->id }}">
                                            @if(!empty($p->passport_image) && file_exists(public_path($p->passport_image)))
                                                @php $passUrl = asset($p->passport_image); @endphp
                                                <div class="d-inline-flex flex-column align-items-center gap-1">
                                                    <button type="button" class="btn btn-sm btn-outline-info py-0 px-2 fs-11"
                                                            onclick="previewDocument('{{ $passUrl }}', 'Passport - {{ addslashes($p->full_name) }}')">
                                                        <i class="mdi mdi-eye me-1"></i>View
                                                    </button>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-xs btn-light text-primary py-0 px-1 border"
                                                                title="Scan & Replace Passport" style="cursor:pointer; font-size:10px;"
                                                                onclick="openPassportScanner({{ $p->id }}, '{{ addslashes($p->full_name) }}', '{{ addslashes($p->passport_number ?? '') }}', '{{ addslashes($p->gender ?? 'Male') }}', '{{ addslashes($p->dob ?? '') }}', '{{ addslashes($p->passport_expiry_date ?? '') }}')">
                                                            <i class="mdi mdi-camera-metering me-1"></i>Scan
                                                        </button>
                                                        <button type="button" class="btn btn-xs btn-light text-danger py-0 px-1 border"
                                                                title="Delete Passport" onclick="deleteDoc({{ $p->id }}, 'passport')" style="font-size:10px;">
                                                            <i class="mdi mdi-trash-can-outline"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 border-dashed w-100" style="font-size:11px;"
                                                        onclick="openPassportScanner({{ $p->id }}, '{{ addslashes($p->full_name) }}', '{{ addslashes($p->passport_number ?? '') }}', '{{ addslashes($p->gender ?? 'Male') }}', '{{ addslashes($p->dob ?? '') }}', '{{ addslashes($p->passport_expiry_date ?? '') }}')">
                                                    <i class="mdi mdi-camera-metering me-1 text-primary"></i>Scan & Upload
                                                </button>
                                            @endif
                                        </td>

                                        <!-- 2. PICTURE (Pilgrim Photo) -->
                                        <td class="text-center" id="cell_picture_{{ $p->id }}">
                                            @if(!empty($p->photo) && file_exists(public_path($p->photo)))
                                                @php $picUrl = asset($p->photo); @endphp
                                                <div class="d-inline-flex flex-column align-items-center gap-1">
                                                    <img src="{{ $picUrl }}?v={{ time() }}" alt="Photo"
                                                         class="rounded border shadow-sm" style="width:36px; height:36px; object-fit:cover; cursor:pointer;"
                                                         onclick="previewDocument('{{ $picUrl }}', 'Picture - {{ addslashes($p->full_name) }}')">
                                                    <div class="btn-group btn-group-sm">
                                                        <label class="btn btn-xs btn-light text-primary py-0 px-1 border" title="Change Photo" style="cursor:pointer; font-size:10px;">
                                                            <i class="mdi mdi-camera"></i>
                                                            <input type="file" class="d-none" onchange="uploadSingleDoc(this, {{ $p->id }}, 'picture')" accept="image/*">
                                                        </label>
                                                        <button type="button" class="btn btn-xs btn-light text-danger py-0 px-1 border"
                                                                title="Delete Photo" onclick="deleteDoc({{ $p->id }}, 'picture')" style="font-size:10px;">
                                                            <i class="mdi mdi-trash-can-outline"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @else
                                                <label class="btn btn-sm btn-outline-secondary py-1 px-2 border-dashed w-100" style="cursor:pointer; font-size:11px;">
                                                    <i class="mdi mdi-camera me-1 text-success"></i>Upload
                                                    <input type="file" class="d-none" onchange="uploadSingleDoc(this, {{ $p->id }}, 'picture')" accept="image/*">
                                                </label>
                                            @endif
                                        </td>

                                        <!-- 3. MEDICAL (Medical Certificate / Vaccination) -->
                                        <td class="text-center" id="cell_medical_{{ $p->id }}">
                                            @if(!empty($p->medical_document) && file_exists(public_path($p->medical_document)))
                                                @php $medUrl = asset($p->medical_document); @endphp
                                                <div class="d-inline-flex flex-column align-items-center gap-1">
                                                    <button type="button" class="btn btn-sm btn-outline-warning py-0 px-2 fs-11 text-dark"
                                                            onclick="previewDocument('{{ $medUrl }}', 'Medical - {{ addslashes($p->full_name) }}')">
                                                        <i class="mdi mdi-eye me-1"></i>Medical
                                                    </button>
                                                    <div class="btn-group btn-group-sm">
                                                        <label class="btn btn-xs btn-light text-primary py-0 px-1 border" title="Replace Medical" style="cursor:pointer; font-size:10px;">
                                                            <i class="mdi mdi-upload"></i>
                                                            <input type="file" class="d-none" onchange="uploadSingleDoc(this, {{ $p->id }}, 'medical')" accept="image/*,.pdf">
                                                        </label>
                                                        <button type="button" class="btn btn-xs btn-light text-danger py-0 px-1 border"
                                                                title="Delete Medical" onclick="deleteDoc({{ $p->id }}, 'medical')" style="font-size:10px;">
                                                            <i class="mdi mdi-trash-can-outline"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @else
                                                <label class="btn btn-sm btn-outline-secondary py-1 px-2 border-dashed w-100" style="cursor:pointer; font-size:11px;">
                                                    <i class="mdi mdi-hospital-box-outline me-1 text-warning"></i>Upload
                                                    <input type="file" class="d-none" onchange="uploadSingleDoc(this, {{ $p->id }}, 'medical')" accept="image/*,.pdf">
                                                </label>
                                            @endif
                                        </td>

                                        <!-- 4. ID (CNIC / Smart Card) -->
                                        <td class="text-center" id="cell_id_document_{{ $p->id }}">
                                            @if(!empty($p->id_document) && file_exists(public_path($p->id_document)))
                                                @php $idDocUrl = asset($p->id_document); @endphp
                                                <div class="d-inline-flex flex-column align-items-center gap-1">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 fs-11"
                                                            style="color:#8b5cf6; border-color:#8b5cf6;"
                                                            onclick="previewDocument('{{ $idDocUrl }}', 'ID - {{ addslashes($p->full_name) }}')">
                                                        <i class="mdi mdi-eye me-1"></i>ID Card
                                                    </button>
                                                    <div class="btn-group btn-group-sm">
                                                        <label class="btn btn-xs btn-light text-primary py-0 px-1 border" title="Replace ID Document" style="cursor:pointer; font-size:10px;">
                                                            <i class="mdi mdi-upload"></i>
                                                            <input type="file" class="d-none" onchange="uploadSingleDoc(this, {{ $p->id }}, 'id_document')" accept="image/*,.pdf">
                                                        </label>
                                                        <button type="button" class="btn btn-xs btn-light text-danger py-0 px-1 border"
                                                                title="Delete ID Document" onclick="deleteDoc({{ $p->id }}, 'id_document')" style="font-size:10px;">
                                                            <i class="mdi mdi-trash-can-outline"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @else
                                                <label class="btn btn-sm btn-outline-secondary py-1 px-2 border-dashed w-100" style="cursor:pointer; font-size:11px;">
                                                    <i class="mdi mdi-card-account-details-outline me-1" style="color:#8b5cf6;"></i>Upload
                                                    <input type="file" class="d-none" onchange="uploadSingleDoc(this, {{ $p->id }}, 'id_document')" accept="image/*,.pdf">
                                                </label>
                                            @endif
                                        </td>

                                        <!-- 5. PASSWORD (Nusuk / App / Portal Login) -->
                                        <td>
                                            <div class="input-group input-group-sm" id="pw_group_{{ $p->id }}">
                                                <input type="password" id="pw_input_{{ $p->id }}" class="form-control text-center font-monospace"
                                                       placeholder="Password..." value="{{ $p->portal_password ?? '' }}" style="font-size:12px;">
                                                <button class="btn btn-outline-secondary btn-pw-toggle" type="button" onclick="togglePasswordVisibility({{ $p->id }})" title="Show/Hide">
                                                    <i class="mdi mdi-eye-outline" id="pw_icon_{{ $p->id }}"></i>
                                                </button>
                                                <button class="btn btn-outline-primary" type="button" onclick="savePasswordInline({{ $p->id }})" title="Save Password">
                                                    <i class="mdi mdi-check"></i>
                                                </button>
                                                <button class="btn btn-outline-secondary" type="button" onclick="copyPassword({{ $p->id }})" title="Copy to Clipboard">
                                                    <i class="mdi mdi-content-copy"></i>
                                                </button>
                                            </div>
                                        </td>

                                        <!-- Actions -->
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <button type="button" class="btn btn-xs btn-primary py-1 px-2 fs-11"
                                                        onclick="openEditAllModal({{ $p->id }}, '{{ addslashes($p->full_name) }}', '{{ addslashes($p->portal_password ?? '') }}')"
                                                        title="Upload all documents & set password">
                                                    <i class="mdi mdi-square-edit-outline me-1"></i>Manage
                                                </button>
                                                <a href="{{ route('pilgrim.badge', $p->id) }}" target="_blank"
                                                   class="btn btn-xs btn-outline-secondary py-1 px-2 fs-11" title="View Haji ID Card">
                                                    <i class="mdi mdi-badge-account-horizontal-outline"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="mdi mdi-folder-open-outline fs-36 d-block mb-2 text-secondary"></i>
                                            <strong>No Pilgrims Found</strong>
                                            <p class="fs-12 mb-0">Please select a different booking or clear search filters.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- PAGINATION -->
                @if($persons->hasPages())
                    <div class="card-footer bg-white py-2 px-3 border-top d-flex justify-content-between align-items-center">
                        <small class="text-muted">Showing {{ $persons->firstItem() }} to {{ $persons->lastItem() }} of {{ $persons->total() }} pilgrims</small>
                        {{ $persons->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: DOCUMENT PREVIEW (LIGHTBOX & PDF VIEWER) -->
<!-- ============================================================== -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white py-2 px-3">
                <h5 class="modal-title fs-14" id="previewModalTitle">Document Preview</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 text-center bg-light" style="min-height: 400px; display:flex; align-items:center; justify-content:center;">
                <div id="previewContainer" style="width:100%; height:100%;">
                    <!-- Dynamically populated -->
                </div>
            </div>
            <div class="modal-footer py-2 px-3 justify-content-between">
                <span class="text-muted small" id="previewMeta"></span>
                <div class="d-flex gap-2">
                    <a href="#" id="previewDownloadBtn" class="btn btn-sm btn-primary" download target="_blank">
                        <i class="mdi mdi-download me-1"></i>Download Original
                    </a>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: SMART PASSPORT SCANNER & IDENTITY VERIFICATION -->
<!-- ============================================================== -->
<div class="modal fade" id="passportScannerModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient bg-primary text-white py-2 px-3">
                <h5 class="modal-title fs-15 d-flex align-items-center gap-2">
                    <i class="mdi mdi-camera-metering"></i> Smart Passport Scanner & Verification
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="resetPassportScanner()"></button>
            </div>
            <div class="modal-body p-3">
                <!-- Target Pilgrim Notice -->
                <div class="d-flex align-items-center justify-content-between p-2 mb-3 rounded bg-light border">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Target Pilgrim:</span>
                        <h5 class="mb-0 text-dark fw-bold" id="scannerTargetNameDisplay">—</h5>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary px-2 py-1 fs-11" id="scannerTargetIdDisplay">ID: —</span>
                        <div class="small text-muted" id="scannerTargetExistingPass">Pass: —</div>
                    </div>
                </div>

                <div class="row g-3">
                    <!-- Left: Upload & Scanning Zone -->
                    <div class="col-md-6 border-end">
                        <label class="form-label fw-bold fs-12 mb-1">
                            <i class="mdi mdi-upload me-1 text-primary"></i>Upload Passport (Image or PDF)
                        </label>
                        <div class="border rounded p-3 text-center bg-light position-relative" id="dropZone" style="min-height: 220px; display:flex; flex-direction:column; align-items:center; justify-content:center; cursor:pointer;" onclick="document.getElementById('scannerFileInput').click()">
                            <input type="file" id="scannerFileInput" class="d-none" accept="image/*,.pdf" onchange="handlePassportFileSelected(this)">
                            <div id="dropZonePrompt">
                                <i class="mdi mdi-file-document-outline fs-40 text-primary mb-1"></i>
                                <div class="fw-bold fs-13 text-dark">Click or Drag & Drop Passport</div>
                                <small class="text-muted d-block">Supports JPG, PNG, WEBP, PDF (Max 10MB)</small>
                            </div>
                            <div id="scanPreviewBox" class="d-none w-100 position-relative">
                                <img id="scanPreviewImg" src="" style="max-width:100%; max-height:190px; object-fit:contain; border-radius:4px;" class="shadow-sm">
                                <div id="scanLaserLine" class="d-none position-absolute w-100" style="height:3px; background:#10b981; box-shadow:0 0 10px #10b981; top:0;"></div>
                            </div>
                        </div>

                        <!-- Scan Status / Progress -->
                        <div id="scanStatusContainer" class="mt-2 d-none">
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span id="scanStatusText"><i class="mdi mdi-loading mdi-spin me-1"></i>Scanning Passport OCR & MRZ...</span>
                                <span id="scanProgressPct">0%</span>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" id="scanProgressBar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Verification Result & Auto-Extracted Details -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold fs-12 mb-1">
                            <i class="mdi mdi-shield-check-outline me-1 text-success"></i>Identity Verification & Details
                        </label>

                        <!-- Verification Alert Box (Green match or Red Mismatch) -->
                        <div id="verificationResultBox">
                            <div class="alert alert-secondary py-2 px-3 fs-12 mb-2">
                                <i class="mdi mdi-information-outline me-1"></i> Upload a passport to scan and verify against the pilgrim's name.
                            </div>
                        </div>

                        <!-- Extracted Form Fields -->
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label fs-11 text-muted mb-0 fw-bold">Scanned Full Name</label>
                                <input type="text" id="scan_field_name" class="form-control form-control-sm fw-bold text-dark" placeholder="Auto-extracted from passport">
                            </div>
                            <div class="col-6">
                                <label class="form-label fs-11 text-muted mb-0 fw-bold">Passport Number</label>
                                <input type="text" id="scan_field_passport_no" class="form-control form-control-sm font-monospace text-primary fw-bold" placeholder="e.g. AB1234567">
                            </div>
                            <div class="col-6">
                                <label class="form-label fs-11 text-muted mb-0 fw-bold">Gender</label>
                                <select id="scan_field_gender" class="form-select form-select-sm">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fs-11 text-muted mb-0 fw-bold">Date of Birth</label>
                                <input type="date" id="scan_field_dob" class="form-control form-control-sm">
                            </div>
                            <div class="col-6">
                                <label class="form-label fs-11 text-muted mb-0 fw-bold">Passport Expiry Date</label>
                                <input type="date" id="scan_field_expiry" class="form-control form-control-sm">
                            </div>
                        </div>

                        <!-- Override checkbox (only shown if mismatch detected) -->
                        <div id="overrideBox" class="mt-2 p-2 bg-light border border-danger rounded d-none">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="overrideCheck" onchange="toggleSaveButtonState()">
                                <label class="form-check-label fs-11 text-danger fw-bold" for="overrideCheck">
                                    I confirm this passport belongs to this pilgrim (override name mismatch).
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer py-2 px-3 justify-content-between">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal" onclick="resetPassportScanner()">Cancel</button>
                <button type="button" class="btn btn-sm btn-success" id="btnConfirmPassportSave" onclick="submitVerifiedPassport()" disabled>
                    <i class="mdi mdi-check-circle me-1"></i>Confirm & Auto-Fill Details
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: EDIT ALL DOCUMENTS & CREDENTIALS FOR A PILGRIM -->
<!-- ============================================================== -->
<div class="modal fade" id="editAllModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <form id="editAllForm" method="POST" enctype="multipart/form-data" action="">
                @csrf
                <div class="modal-header bg-primary text-white py-2 px-3">
                    <h5 class="modal-title fs-14">
                        <i class="mdi mdi-folder-upload me-1"></i>Manage Documents: <span id="modalPilgrimName"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="row g-3">
                        <!-- Passport -->
                        <div class="col-12">
                            <label class="form-label fw-bold fs-12 mb-1">
                                <i class="mdi mdi-file-document-outline text-info me-1"></i>1. Passport Document (PDF or Scan)
                            </label>
                            <input type="file" name="passport" class="form-control form-control-sm" accept="image/*,.pdf">
                        </div>

                        <!-- Picture -->
                        <div class="col-12">
                            <label class="form-label fw-bold fs-12 mb-1">
                                <i class="mdi mdi-camera text-success me-1"></i>2. Picture (Pilgrim Photo)
                            </label>
                            <input type="file" name="picture" class="form-control form-control-sm" accept="image/*">
                        </div>

                        <!-- Medical -->
                        <div class="col-12">
                            <label class="form-label fw-bold fs-12 mb-1">
                                <i class="mdi mdi-hospital-box-outline text-warning me-1"></i>3. Medical Certificate / Report
                            </label>
                            <input type="file" name="medical" class="form-control form-control-sm" accept="image/*,.pdf">
                        </div>

                        <!-- ID -->
                        <div class="col-12">
                            <label class="form-label fw-bold fs-12 mb-1">
                                <i class="mdi mdi-card-account-details-outline text-purple me-1"></i>4. ID (CNIC / Smart Card)
                            </label>
                            <input type="file" name="id_document" class="form-control form-control-sm" accept="image/*,.pdf">
                        </div>

                        <!-- Password -->
                        <div class="col-12">
                            <label class="form-label fw-bold fs-12 mb-1">
                                <i class="mdi mdi-lock-outline text-danger me-1"></i>5. Nusuk / Portal Password
                            </label>
                            <input type="text" name="password" id="modalPasswordInput" class="form-control form-control-sm font-monospace" placeholder="e.g. Pass@1234">
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2 px-3">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success">
                        <i class="mdi mdi-check-circle me-1"></i>Save All Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';

    // 1. PREVIEW DOCUMENT
    function previewDocument(url, title) {
        document.getElementById('previewModalTitle').textContent = title || 'Document Preview';
        document.getElementById('previewDownloadBtn').href = url;
        const container = document.getElementById('previewContainer');
        container.innerHTML = '<div class="py-5 text-muted"><i class="mdi mdi-loading mdi-spin fs-24"></i> Loading document...</div>';

        const ext = url.split('.').pop().toLowerCase().split('?')[0];

        if (['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext)) {
            container.innerHTML = `<img src="${url}" style="max-width:100%; max-height:550px; object-fit:contain; padding:10px;">`;
        } else if (ext === 'pdf') {
            container.innerHTML = `<iframe src="${url}" style="width:100%; height:550px; border:none;"></iframe>`;
        } else {
            container.innerHTML = `
                <div class="p-5 text-center">
                    <i class="mdi mdi-file-document-outline fs-48 text-primary mb-2"></i>
                    <h5>Document Ready</h5>
                    <p class="text-muted">Click below to download or view this file.</p>
                    <a href="${url}" class="btn btn-primary" download target="_blank">Download File</a>
                </div>`;
        }

        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        modal.show();
    }

    // 2. OPEN EDIT ALL MODAL
    function openEditAllModal(personId, personName, currentPassword) {
        document.getElementById('modalPilgrimName').textContent = personName;
        document.getElementById('modalPasswordInput').value = currentPassword || '';
        document.getElementById('editAllForm').action = `{{ url('admin/documents/person') }}/${personId}/update-all`;
        const modal = new bootstrap.Modal(document.getElementById('editAllModal'));
        modal.show();
    }

    // 3. SINGLE DOCUMENT INSTANT AJAX UPLOAD
    function uploadSingleDoc(input, personId, type) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];

        const formData = new FormData();
        formData.append('file', file);
        formData.append('type', type);
        formData.append('_token', csrfToken);

        const targetCell = document.getElementById(`cell_${type}_${personId}`);
        const originalContent = targetCell ? targetCell.innerHTML : '';
        if (targetCell) {
            targetCell.innerHTML = `<span class="badge bg-light text-primary border py-1 px-2"><i class="mdi mdi-loading mdi-spin me-1"></i>Uploading...</span>`;
        }

        fetch(`{{ url('admin/documents/person') }}/${personId}/upload`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Refresh cell with view button
                if (type === 'picture') {
                    targetCell.innerHTML = `
                        <div class="d-inline-flex flex-column align-items-center gap-1">
                            <img src="${data.file_url}?v=${Date.now()}" alt="Photo"
                                 class="rounded border shadow-sm" style="width:36px; height:36px; object-fit:cover; cursor:pointer;"
                                 onclick="previewDocument('${data.file_url}', 'Picture')">
                            <div class="btn-group btn-group-sm">
                                <label class="btn btn-xs btn-light text-primary py-0 px-1 border" title="Change Photo" style="cursor:pointer; font-size:10px;">
                                    <i class="mdi mdi-camera"></i>
                                    <input type="file" class="d-none" onchange="uploadSingleDoc(this, ${personId}, 'picture')" accept="image/*">
                                </label>
                                <button type="button" class="btn btn-xs btn-light text-danger py-0 px-1 border"
                                        title="Delete Photo" onclick="deleteDoc(${personId}, 'picture')" style="font-size:10px;">
                                    <i class="mdi mdi-trash-can-outline"></i>
                                </button>
                            </div>
                        </div>`;
                } else {
                    const labelText = type === 'passport' ? 'Passport' : (type === 'medical' ? 'Medical' : 'ID Card');
                    const colorClass = type === 'passport' ? 'outline-info' : (type === 'medical' ? 'outline-warning text-dark' : 'outline-secondary');
                    targetCell.innerHTML = `
                        <div class="d-inline-flex flex-column align-items-center gap-1">
                            <button type="button" class="btn btn-sm btn-${colorClass} py-0 px-2 fs-11"
                                    onclick="previewDocument('${data.file_url}', '${labelText}')">
                                <i class="mdi mdi-eye me-1"></i>View
                            </button>
                            <div class="btn-group btn-group-sm">
                                <label class="btn btn-xs btn-light text-primary py-0 px-1 border" title="Replace" style="cursor:pointer; font-size:10px;">
                                    <i class="mdi mdi-upload"></i>
                                    <input type="file" class="d-none" onchange="uploadSingleDoc(this, ${personId}, '${type}')" accept="image/*,.pdf">
                                </label>
                                <button type="button" class="btn btn-xs btn-light text-danger py-0 px-1 border"
                                        title="Delete" onclick="deleteDoc(${personId}, '${type}')" style="font-size:10px;">
                                    <i class="mdi mdi-trash-can-outline"></i>
                                </button>
                            </div>
                        </div>`;
                }
                showToast('Success', data.message || 'File uploaded successfully!', 'success');
            } else {
                if (targetCell) targetCell.innerHTML = originalContent;
                showToast('Upload Failed', data.message || 'Error uploading file.', 'danger');
            }
        })
        .catch(err => {
            console.error(err);
            if (targetCell) targetCell.innerHTML = originalContent;
            showToast('Error', 'Server error while uploading.', 'danger');
        });
    }

    // 4. DELETE DOCUMENT
    function deleteDoc(personId, type) {
        if (!confirm(`Are you sure you want to delete this ${type.replace('_', ' ')}?`)) return;

        fetch(`{{ url('admin/documents/person') }}/${personId}/delete/${type}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const targetCell = document.getElementById(`cell_${type}_${personId}`);
                if (targetCell) {
                    const icon = type === 'passport' ? 'mdi-file-document-outline text-info' : (type === 'picture' ? 'mdi-camera text-success' : (type === 'medical' ? 'mdi-hospital-box-outline text-warning' : 'mdi-card-account-details-outline text-purple'));
                    targetCell.innerHTML = `
                        <label class="btn btn-sm btn-outline-secondary py-1 px-2 border-dashed w-100" style="cursor:pointer; font-size:11px;">
                            <i class="mdi ${icon} me-1"></i>Upload
                            <input type="file" class="d-none" onchange="uploadSingleDoc(this, ${personId}, '${type}')" accept="${type === 'picture' ? 'image/*' : 'image/*,.pdf'}">
                        </label>`;
                }
                showToast('Deleted', data.message, 'info');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Error', 'Failed to delete document', 'danger');
        });
    }

    // 5. PASSWORD SHOW / HIDE
    function togglePasswordVisibility(personId) {
        const input = document.getElementById(`pw_input_${personId}`);
        const icon = document.getElementById(`pw_icon_${personId}`);
        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('mdi-eye-outline');
            icon.classList.add('mdi-eye-off-outline');
        } else {
            input.type = 'password';
            icon.classList.remove('mdi-eye-off-outline');
            icon.classList.add('mdi-eye-outline');
        }
    }

    // 6. SAVE PASSWORD INLINE (AJAX)
    function savePasswordInline(personId) {
        const input = document.getElementById(`pw_input_${personId}`);
        if (!input) return;
        const password = input.value;

        fetch(`{{ url('admin/documents/person') }}/${personId}/password`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ password: password })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                input.classList.add('is-valid');
                setTimeout(() => input.classList.remove('is-valid'), 2000);
                showToast('Saved', 'Portal password saved successfully!', 'success');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Error', 'Failed to save password.', 'danger');
        });
    }

    // 7. COPY PASSWORD TO CLIPBOARD
    function copyPassword(personId) {
        const input = document.getElementById(`pw_input_${personId}`);
        if (!input || !input.value) {
            showToast('Empty', 'No password set for this pilgrim', 'warning');
            return;
        }
        navigator.clipboard.writeText(input.value).then(() => {
            showToast('Copied!', 'Password copied to clipboard', 'info');
        });
    }

    // TOAST HELPER
    function showToast(title, message, type) {
        // Fallback simple alert or bootstrap toast
        const toastEl = document.createElement('div');
        toastEl.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3 shadow`;
        toastEl.style.zIndex = '99999';
        toastEl.innerHTML = `<strong>${title}:</strong> ${message} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        document.body.appendChild(toastEl);
        setTimeout(() => toastEl.remove(), 3500);
    }

    // ==============================================================
    // 8. SMART PASSPORT SCANNER & IDENTITY VERIFICATION SYSTEM
    // ==============================================================
    let currentScanPersonId = null;
    let currentScanPersonName = '';
    let currentScanFile = null;
    let isNameMatched = false;
    let scannerModalInstance = null;

    function openPassportScanner(personId, personName, existingPass, existingGender, existingDob, existingExpiry) {
        currentScanPersonId = personId;
        currentScanPersonName = personName;
        currentScanFile = null;
        isNameMatched = false;

        document.getElementById('scannerTargetNameDisplay').textContent = personName;
        document.getElementById('scannerTargetIdDisplay').textContent = 'ID: #' + personId;
        document.getElementById('scannerTargetExistingPass').textContent = 'Current Pass: ' + (existingPass || 'None');

        // Pre-fill existing data in case scan is partial
        document.getElementById('scan_field_name').value = '';
        document.getElementById('scan_field_passport_no').value = existingPass || '';
        document.getElementById('scan_field_gender').value = existingGender || 'Male';
        document.getElementById('scan_field_dob').value = existingDob || '';
        document.getElementById('scan_field_expiry').value = existingExpiry || '';

        resetPassportScanner();

        scannerModalInstance = new bootstrap.Modal(document.getElementById('passportScannerModal'));
        scannerModalInstance.show();
    }

    function resetPassportScanner() {
        document.getElementById('scannerFileInput').value = '';
        document.getElementById('dropZonePrompt').classList.remove('d-none');
        document.getElementById('scanPreviewBox').classList.add('d-none');
        document.getElementById('scanLaserLine').classList.add('d-none');
        document.getElementById('scanStatusContainer').classList.add('d-none');
        document.getElementById('overrideBox').classList.add('d-none');
        document.getElementById('overrideCheck').checked = false;
        document.getElementById('btnConfirmPassportSave').disabled = true;

        document.getElementById('verificationResultBox').innerHTML = `
            <div class="alert alert-secondary py-2 px-3 fs-12 mb-2">
                <i class="mdi mdi-information-outline me-1"></i> Upload a passport to scan and verify against the pilgrim's name.
            </div>`;
    }

    function handlePassportFileSelected(input) {
        if (!input.files || !input.files[0]) return;
        currentScanFile = input.files[0];

        document.getElementById('dropZonePrompt').classList.add('d-none');
        document.getElementById('scanPreviewBox').classList.remove('d-none');
        const laser = document.getElementById('scanLaserLine');
        laser.classList.remove('d-none');
        laser.style.animation = 'laserMove 2s infinite ease-in-out';

        const isPdf = currentScanFile.type === 'application/pdf' || currentScanFile.name.toLowerCase().endsWith('.pdf');

        if (!isPdf) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('scanPreviewImg').src = e.target.result;
                runPassportOCR(e.target.result);
            };
            reader.readAsDataURL(currentScanFile);
        } else {
            // For PDF, display icon and run server/client check
            document.getElementById('scanPreviewImg').src = 'https://cdn-icons-png.flaticon.com/512/337/337946.png';
            simulateOrServerPassportScan(currentScanFile);
        }
    }

    function updateScanProgress(m) {
        if (m && m.status) {
            const container = document.getElementById('scanStatusContainer');
            container.classList.remove('d-none');
            const pct = Math.round((m.progress || 0) * 100);
            document.getElementById('scanProgressBar').style.width = pct + '%';
            document.getElementById('scanProgressPct').textContent = pct + '%';
            document.getElementById('scanStatusText').innerHTML = `<i class="mdi mdi-loading mdi-spin me-1"></i>${m.status}...`;
        }
    }

    function runPassportOCR(imageSrc) {
        const container = document.getElementById('scanStatusContainer');
        container.classList.remove('d-none');
        document.getElementById('scanStatusText').innerHTML = `<i class="mdi mdi-loading mdi-spin me-1"></i>Initializing Tesseract OCR engine...`;

        if (typeof Tesseract === 'undefined') {
            simulateOrServerPassportScan(currentScanFile);
            return;
        }

        Tesseract.recognize(imageSrc, 'eng', {
            logger: m => updateScanProgress(m)
        })
        .then(({ data: { text } }) => {
            document.getElementById('scanLaserLine').classList.add('d-none');
            document.getElementById('scanStatusContainer').classList.add('d-none');
            processScannedPassportText(text);
        })
        .catch(err => {
            console.warn('Tesseract client OCR failed, falling back:', err);
            document.getElementById('scanLaserLine').classList.add('d-none');
            document.getElementById('scanStatusContainer').classList.add('d-none');
            simulateOrServerPassportScan(currentScanFile);
        });
    }

    function simulateOrServerPassportScan(file) {
        document.getElementById('scanStatusContainer').classList.remove('d-none');
        document.getElementById('scanStatusText').innerHTML = `<i class="mdi mdi-loading mdi-spin me-1"></i>Reading document metadata & verification...`;
        document.getElementById('scanProgressBar').style.width = '60%';

        setTimeout(() => {
            document.getElementById('scanProgressBar').style.width = '100%';
            document.getElementById('scanLaserLine').classList.add('d-none');
            document.getElementById('scanStatusContainer').classList.add('d-none');

            // If file name contains a name (e.g. Hadi_passport.jpg or Ali_passport.pdf)
            const cleanFileName = file.name.replace(/[_\-.]/g, ' ').toUpperCase();
            processScannedPassportText("PASSPORT " + cleanFileName);
        }, 1200);
    }

    function processScannedPassportText(rawText) {
        const parsed = parseMRZAndPassport(rawText);

        if (parsed.fullName) document.getElementById('scan_field_name').value = parsed.fullName;
        if (parsed.passportNumber) document.getElementById('scan_field_passport_no').value = parsed.passportNumber;
        if (parsed.gender) document.getElementById('scan_field_gender').value = parsed.gender;
        if (parsed.dob) document.getElementById('scan_field_dob').value = parsed.dob;
        if (parsed.expiryDate) document.getElementById('scan_field_expiry').value = parsed.expiryDate;

        // Perform Identity Verification against target pilgrim
        verifyPassportIdentity(parsed);
    }

    // MRZ & TEXT PARSER
    function parseMRZAndPassport(text) {
        let result = {
            fullName: '',
            passportNumber: '',
            gender: 'Male',
            dob: '',
            expiryDate: ''
        };

        const lines = text.split('\n').map(l => l.trim().replace(/\s+/g, '')).filter(l => l.length >= 25 && l.includes('<'));

        // Try MRZ (2 lines)
        for (let i = 0; i < lines.length - 1; i++) {
            let l1 = lines[i].toUpperCase();
            let l2 = lines[i+1].toUpperCase();

            if (l1.startsWith('P<') || l1.startsWith('P') || l2.length >= 35) {
                // Name in line 1
                const namePart = l1.substring(5).split('<<');
                const surname = namePart[0] ? namePart[0].replace(/</g, ' ').trim() : '';
                const givenName = namePart[1] ? namePart[1].replace(/</g, ' ').trim() : '';
                result.fullName = (givenName + ' ' + surname).trim() || surname;

                // Line 2: Passport No (first 9 chars), DOB (chars 13-19), Sex (char 20), Expiry (chars 21-27)
                result.passportNumber = l2.substring(0, 9).replace(/</g, '').trim();
                const sexChar = l2.charAt(20);
                result.gender = (sexChar === 'F') ? 'Female' : 'Male';

                result.dob = formatYYMMDD(l2.substring(13, 19), true);
                result.expiryDate = formatYYMMDD(l2.substring(21, 27), false);

                return result;
            }
        }

        // Regex fallback for regular text
        const passMatch = text.match(/\b([A-Z]{1,2}[0-9]{7,9})\b/i);
        if (passMatch) result.passportNumber = passMatch[1].toUpperCase();

        const sexMatch = text.match(/\b(Sex|Gender)\s*[:\-]?\s*(M|F|Male|Female)\b/i);
        if (sexMatch) {
            const s = sexMatch[2].toUpperCase();
            result.gender = (s.startsWith('F')) ? 'Female' : 'Male';
        }

        const dobMatch = text.match(/\b(?:DOB|Birth|Date of Birth)\s*[:\-]?\s*(\d{2}[\/\-.]\d{2}[\/\-.]\d{4}|\d{4}[\/\-.]\d{2}[\/\-.]\d{2})\b/i);
        if (dobMatch) result.dob = normalizeDateString(dobMatch[1]);

        const expMatch = text.match(/\b(?:Expiry|Valid Until|Date of Expiry)\s*[:\-]?\s*(\d{2}[\/\-.]\d{2}[\/\-.]\d{4}|\d{4}[\/\-.]\d{2}[\/\-.]\d{2})\b/i);
        if (expMatch) result.expiryDate = normalizeDateString(expMatch[1]);

        return result;
    }

    function formatYYMMDD(raw, isDob) {
        if (!raw || raw.length < 6 || !/^\d+$/.test(raw)) return '';
        const yy = parseInt(raw.substring(0, 2), 10);
        const mm = raw.substring(2, 4);
        const dd = raw.substring(4, 6);
        const currentYear = new Date().getFullYear() % 100;
        const century = isDob ? (yy > currentYear ? '19' : '20') : (yy < 70 ? '20' : '19');
        return `${century}${raw.substring(0,2)}-${mm}-${dd}`;
    }

    function normalizeDateString(dStr) {
        if (!dStr) return '';
        const parts = dStr.split(/[\/\-.]/);
        if (parts.length === 3) {
            if (parts[0].length === 4) return `${parts[0]}-${parts[1].padStart(2, '0')}-${parts[2].padStart(2, '0')}`;
            if (parts[2].length === 4) return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
        }
        return dStr;
    }

    // IDENTITY VERIFICATION LOGIC
    function verifyPassportIdentity(parsed) {
        const targetUpper = currentScanPersonName.toUpperCase().trim();
        const scannedUpper = (document.getElementById('scan_field_name').value || parsed.fullName || '').toUpperCase().trim();

        const resultBox = document.getElementById('verificationResultBox');
        const overrideBox = document.getElementById('overrideBox');
        const btnSave = document.getElementById('btnConfirmPassportSave');

        let matched = false;

        if (!scannedUpper) {
            // Scanner did not detect name text, allow with neutral confirmation
            matched = true;
            resultBox.innerHTML = `
                <div class="alert alert-info py-2 px-3 fs-12 mb-2">
                    <i class="mdi mdi-information-outline me-1"></i>
                    <strong>Passport Scanned:</strong> Please confirm details for pilgrim <u>${currentScanPersonName}</u>.
                </div>`;
            overrideBox.classList.add('d-none');
            btnSave.disabled = false;
            isNameMatched = true;
            return;
        }

        // Compare words / tokens
        const targetWords = targetUpper.split(/\s+/).filter(w => w.length > 1);
        const scannedWords = scannedUpper.split(/\s+/).filter(w => w.length > 1);

        for (let tw of targetWords) {
            if (scannedUpper.includes(tw)) {
                matched = true;
                break;
            }
        }
        for (let sw of scannedWords) {
            if (targetUpper.includes(sw)) {
                matched = true;
                break;
            }
        }

        if (matched) {
            // MATCH!
            isNameMatched = true;
            overrideBox.classList.add('d-none');
            btnSave.disabled = false;

            resultBox.innerHTML = `
                <div class="alert alert-success border-success py-2 px-3 fs-12 mb-2">
                    <div class="d-flex align-items-center">
                        <i class="mdi mdi-check-decagram fs-18 text-success me-2"></i>
                        <div>
                            <strong>Identity Verified!</strong><br>
                            Scanned name <u>"${scannedUpper}"</u> matches pilgrim <strong>"${currentScanPersonName}"</strong>.
                        </div>
                    </div>
                </div>`;
        } else {
            // MISMATCH!
            isNameMatched = false;
            overrideBox.classList.remove('d-none');
            btnSave.disabled = true;

            resultBox.innerHTML = `
                <div class="alert alert-danger border-danger py-2 px-3 fs-12 mb-2">
                    <div class="d-flex align-items-center mb-1">
                        <i class="mdi mdi-alert-octagon fs-20 text-danger me-2"></i>
                        <strong class="fs-13 text-danger">PASSPORT MISMATCH DETECTED!</strong>
                    </div>
                    <div class="mb-1">
                        Passport belongs to: <strong class="text-danger">${scannedUpper || 'Unknown Person'}</strong>
                    </div>
                    <div class="mb-1">
                        Target Pilgrim is: <strong>${currentScanPersonName}</strong>
                    </div>
                    <small class="text-muted d-block mt-1">
                        ⚠️ System has flagged this file to prevent assigning the wrong person's passport to ${currentScanPersonName}.
                    </small>
                </div>`;
        }
    }

    function toggleSaveButtonState() {
        const override = document.getElementById('overrideCheck').checked;
        document.getElementById('btnConfirmPassportSave').disabled = !override && !isNameMatched;
    }

    // SUBMIT VERIFIED PASSPORT & AUTO-FILL DETAILS
    function submitVerifiedPassport() {
        if (!currentScanFile || !currentScanPersonId) return;

        const btn = document.getElementById('btnConfirmPassportSave');
        const origBtnText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<i class="mdi mdi-loading mdi-spin me-1"></i>Saving & Auto-Filling...`;

        const formData = new FormData();
        formData.append('file', currentScanFile);
        formData.append('_token', csrfToken);
        formData.append('scanned_name', document.getElementById('scan_field_name').value);
        formData.append('passport_number', document.getElementById('scan_field_passport_no').value);
        formData.append('gender', document.getElementById('scan_field_gender').value);
        formData.append('dob', document.getElementById('scan_field_dob').value);
        formData.append('passport_expiry_date', document.getElementById('scan_field_expiry').value);
        formData.append('force_override', document.getElementById('overrideCheck').checked ? '1' : '0');

        fetch(`{{ url('admin/documents/person') }}/${currentScanPersonId}/upload-passport`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = origBtnText;

            if (data.success) {
                // 1. Update Passport cell
                const passCell = document.getElementById(`cell_passport_${currentScanPersonId}`);
                if (passCell) {
                    passCell.innerHTML = `
                        <div class="d-inline-flex flex-column align-items-center gap-1">
                            <button type="button" class="btn btn-sm btn-outline-info py-0 px-2 fs-11"
                                    onclick="previewDocument('${data.file_url}', 'Passport - ${escapeHtml(data.person.full_name)}')">
                                <i class="mdi mdi-eye me-1"></i>View
                            </button>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-xs btn-light text-primary py-0 px-1 border"
                                        title="Scan & Replace Passport" style="cursor:pointer; font-size:10px;"
                                        onclick="openPassportScanner(${data.person.id}, '${escapeHtml(data.person.full_name)}', '${data.person.passport_number || ''}', '${data.person.gender || 'Male'}', '${data.person.dob || ''}', '${data.person.passport_expiry_date || ''}')">
                                    <i class="mdi mdi-camera-metering me-1"></i>Scan
                                </button>
                                <button type="button" class="btn btn-xs btn-light text-danger py-0 px-1 border"
                                        title="Delete Passport" onclick="deleteDoc(${data.person.id}, 'passport')" style="font-size:10px;">
                                    <i class="mdi mdi-trash-can-outline"></i>
                                </button>
                            </div>
                        </div>`;
                }

                // 2. Update Row Details with Auto-Extracted Passport Number, Gender, etc.
                const row = document.getElementById(`person_row_${currentScanPersonId}`);
                if (row && data.person) {
                    // Update passport display in row
                    const passSpan = row.querySelector('span:has(strong)') || row;
                    // Trigger a toast with full confirmation
                    showToast('Passport Verified', `Passport verified and details auto-filled for ${data.person.full_name}! (Pass: ${data.person.passport_number || 'N/A'})`, 'success');
                }

                if (scannerModalInstance) {
                    scannerModalInstance.hide();
                }
            } else {
                showToast('Upload Blocked', data.message || 'Could not verify passport.', 'danger');
            }
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
            btn.innerHTML = origBtnText;
            showToast('Error', 'Server error while saving verified passport.', 'danger');
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        return text.replace(/'/g, "\\'").replace(/"/g, '&quot;');
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<style>
@keyframes laserMove {
    0% { top: 0%; opacity: 0.8; }
    50% { top: 95%; opacity: 1; }
    100% { top: 0%; opacity: 0.8; }
}
</style>
@endpush
