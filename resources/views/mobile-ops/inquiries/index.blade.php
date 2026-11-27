@extends('layout.mobile-master')

@section('content')
    <div class="py-3 d-flex align-items-sm-center justify-content-between flex-column flex-sm-row gap-2">
        <div>
            <h4 class="fs-18 fw-semibold m-0 text-dark">Guest Package Inquiries</h4>
            <p class="text-muted fs-13 mb-0">Review leads and inquiries submitted by guests through the mobile app.</p>
        </div>

        <!-- Status Filter Dropdown -->
        <div class="d-flex align-items-center gap-2">
            <label class="fs-13 fw-semibold text-muted mb-0 text-nowrap">Filter Status:</label>
            <div class="dropdown">
                <button class="btn btn-sm btn-white border shadow-sm dropdown-toggle px-3 d-flex align-items-center gap-2"
                    type="button" data-bs-toggle="dropdown" aria-expanded="false" style="min-width: 170px;">
                    <span class="d-flex align-items-center gap-1 flex-grow-1 text-start">
                        @if (empty($status))
                            <span class="badge bg-dark rounded-circle p-1 me-1"></span> All Statuses ({{ $counts['all'] }})
                        @elseif($status === 'pending')
                            <span class="badge bg-warning rounded-circle p-1 me-1"></span> Pending
                            ({{ $counts['pending'] }})
                        @elseif($status === 'contacted')
                            <span class="badge bg-info rounded-circle p-1 me-1"></span> Contacted
                            ({{ $counts['contacted'] }})
                        @elseif($status === 'closed')
                            <span class="badge bg-success rounded-circle p-1 me-1"></span> Closed ({{ $counts['closed'] }})
                        @endif
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width: 190px;">
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between fs-13 {{ empty($status) ? 'active' : '' }}"
                            href="{{ route('mobile-ops.inquiries.index') }}">
                            <span>All Inquiries</span>
                            <span class="badge bg-dark rounded-pill">{{ $counts['all'] }}</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between fs-13 {{ $status === 'pending' ? 'active' : '' }}"
                            href="{{ route('mobile-ops.inquiries.index', ['status' => 'pending']) }}">
                            <span class="text-warning-emphasis"><i class="mdi mdi-clock-outline me-1"></i> Pending</span>
                            <span class="badge bg-warning text-dark rounded-pill">{{ $counts['pending'] }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between fs-13 {{ $status === 'contacted' ? 'active' : '' }}"
                            href="{{ route('mobile-ops.inquiries.index', ['status' => 'contacted']) }}">
                            <span class="text-info"><i class="mdi mdi-phone-in-talk-outline me-1"></i> Contacted</span>
                            <span class="badge bg-info text-white rounded-pill">{{ $counts['contacted'] }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between fs-13 {{ $status === 'closed' ? 'active' : '' }}"
                            href="{{ route('mobile-ops.inquiries.index', ['status' => 'closed']) }}">
                            <span class="text-success"><i class="mdi mdi-check-circle-outline me-1"></i> Closed</span>
                            <span class="badge bg-success text-white rounded-pill">{{ $counts['closed'] }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('whatsapp_url'))
        <div class="alert alert-success border-success alert-dismissible fade show d-flex align-items-center justify-content-between p-3 mb-3 shadow-sm" role="alert">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar-sm rounded-circle bg-success text-white d-flex align-items-center justify-content-center fs-20">
                    <i class="mdi mdi-whatsapp"></i>
                </div>
                <div>
                    <h5 class="fs-14 fw-bold text-success mb-1">Booking Confirmed Successfully!</h5>
                    <p class="fs-12 mb-0 text-dark">Pre-filled WhatsApp confirmation message ready for <strong>{{ session('client_name') }}</strong>.</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ session('whatsapp_url') }}" target="_blank" class="btn btn-sm btn-success px-3 fw-bold shadow-sm">
                    <i class="mdi mdi-send me-1"></i> Send WhatsApp Alert Now
                </a>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Inquiries Table Card -->
    <div class="card border-0 shadow-sm" style="overflow: visible;">
        <div class="card-body p-0" style="overflow: visible;">
            <div>
                <table class="table  align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Guest Details</th>
                            <th>Target Package</th>
                            <th>Pax & Room</th>
                            <th>Status</th>
                            <th>Submitted At</th>
                            <th class="text-end pe-3" style="width: 180px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inquiries as $item)
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="fw-bold text-dark">{{ $item->full_name }}</span>
                                        @if($item->app_user_id || $item->appUser)
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-10 px-1 py-0" title="Registered Mobile App User">
                                                <i class="mdi mdi-cellphone"></i> App
                                            </span>
                                        @endif
                                    </div>
                                    <div class="fs-12 text-muted mt-1">
                                        <i class="mdi mdi-phone me-1 text-primary"></i>
                                        <a href="tel:{{ $item->contact }}"
                                            class="text-secondary text-decoration-none">{{ $item->contact }}</a>
                                    </div>
                                    @if($item->email)
                                    <div class="fs-12 text-muted">
                                        <i class="mdi mdi-email me-1 text-danger"></i>
                                        <a href="mailto:{{ $item->email }}"
                                            class="text-secondary text-decoration-none">{{ $item->email }}</a>
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1 text-wrap text-start">
                                        {{ $item->package?->package_title ?? 'Custom / Removed Package' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fs-13 fw-semibold">
                                        <i class="mdi mdi-account-group me-1 text-primary"></i>{{ $item->pax }} Pax
                                    </div>
                                    <span
                                        class="badge bg-primary-subtle text-primary border border-primary-subtle text-uppercase fs-11 mt-1">
                                        {{ $item->room_type }}
                                    </span>
                                </td>
                                <td>
                                    @if ($item->status === 'pending')
                                        <span class="badge bg-warning text-dark px-2 py-1">Pending</span>
                                    @elseif($item->status === 'contacted')
                                        <span class="badge bg-info text-white px-2 py-1">Contacted</span>
                                    @else
                                        <span class="badge bg-success text-white px-2 py-1">Closed</span>
                                    @endif
                                </td>
                                <td class="fs-12 text-muted">
                                    <div><i
                                            class="mdi mdi-calendar-blank-outline me-1"></i>{{ $item->created_at->format('d M Y') }}
                                    </div>
                                    <div class="text-muted"><i
                                            class="mdi mdi-clock-outline me-1"></i>{{ $item->created_at->format('h:i A') }}
                                    </div>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <!-- Confirm & Auto WhatsApp Button -->
                                        @php
                                            $cleanPhone = preg_replace('/\D/', '', $item->contact);
                                            if (str_starts_with($cleanPhone, '0')) {
                                                $cleanPhone = '92' . substr($cleanPhone, 1);
                                            } elseif (!str_starts_with($cleanPhone, '92') && strlen($cleanPhone) === 10) {
                                                $cleanPhone = '92' . $cleanPhone;
                                            }
                                            $pkgTitle = $item->package?->package_title ?? 'Hajj/Umrah Package';
                                            $pax = $item->pax ?? 1;
                                            $room = ucfirst($item->room_type ?? 'Sharing');
                                            $duration = $item->duration_days ? "({$item->duration_days} Days)" : "";
                                            $waMsg = "Assalam-o-Alaikum *{$item->full_name}*!\n\n"
                                                . "✨ *BOOKING CONFIRMED* ✨\n"
                                                . "We are delighted to inform you that your booking with *Baba Hajj & Umrah Group* has been confirmed!\n\n"
                                                . "📋 *Booking Details:*\n"
                                                . "• Booking ID: #{$item->id}\n"
                                                . "• Package: {$pkgTitle} {$duration}\n"
                                                . "• Travelers: {$pax} Pax\n"
                                                . "• Room Category: {$room}\n\n"
                                                . "Our operations desk will now initiate your visa approval, flight manifests, and hotel allocation.\n"
                                                . "You can also view this booking inside the *Baba Hajj Mobile App*.\n\n"
                                                . "For any inquiries, feel free to reply to this message.\n"
                                                . "JazakAllah Khair!\n"
                                                . "*Baba Hajj & Umrah Group*";
                                            $directWaUrl = "https://wa.me/{$cleanPhone}?text=" . urlencode($waMsg);
                                        @endphp

                                        @if($item->status !== 'closed')
                                            <form action="{{ route('mobile-ops.inquiries.confirm', $item->id) }}" method="POST" class="d-inline"
                                                onsubmit="return handleConfirmBooking(event, this, '{{ $directWaUrl }}', '{{ addslashes($item->full_name) }}');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success d-inline-flex align-items-center gap-1"
                                                    title="Confirm Booking & Auto Open WhatsApp">
                                                    <i class="mdi mdi-whatsapp"></i> Confirm & WA
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ $directWaUrl }}" target="_blank"
                                                class="btn btn-sm btn-outline-success" title="Chat on WhatsApp">
                                                <i class="mdi mdi-whatsapp"></i>
                                            </a>
                                        @endif

                                        <a href="{{ route('mobile-ops.chat.index', ['inquiry_id' => $item->id]) }}"
                                            class="btn btn-sm btn-outline-info" title="Open Live Chat with Applicant">
                                            <i class="mdi mdi-chat-outline"></i>
                                        </a>

                                        <!-- Details Button opens Popup -->
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#inquiryModal{{ $item->id }}">
                                            <i class="mdi mdi-eye-outline me-1"></i> Details
                                        </button>

                                        <!-- Status Change Dropdown -->
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light border dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                Status
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                                <li class="dropdown-header text-uppercase fs-11 fw-bold text-muted">Change
                                                    Status</li>
                                                <li>
                                                    <form action="{{ route('mobile-ops.inquiries.status', $item->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="contacted">
                                                        <button type="submit"
                                                            class="dropdown-item fs-13 d-flex align-items-center {{ $item->status === 'contacted' ? 'active' : '' }}">
                                                            <i class="mdi mdi-phone-in-talk-outline me-2 text-info"></i>
                                                            Mark as Contacted
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('mobile-ops.inquiries.status', $item->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="closed">
                                                        <button type="submit"
                                                            class="dropdown-item fs-13 d-flex align-items-center {{ $item->status === 'closed' ? 'active' : '' }}">
                                                            <i class="mdi mdi-check-circle-outline me-2 text-success"></i>
                                                            Mark as Closed
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('mobile-ops.inquiries.status', $item->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="pending">
                                                        <button type="submit"
                                                            class="dropdown-item fs-13 d-flex align-items-center {{ $item->status === 'pending' ? 'active' : '' }}">
                                                            <i class="mdi mdi-clock-outline me-2 text-warning"></i> Revert
                                                            to Pending
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <!-- Details Modal with Documents View -->
                            <div class="modal fade" id="inquiryModal{{ $item->id }}" tabindex="-1"
                                aria-labelledby="modalLabel{{ $item->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light py-3">
                                            <div>
                                                <h5 class="modal-title fs-16 fw-bold mb-0"
                                                    id="modalLabel{{ $item->id }}">
                                                    Inquiry Details #{{ $item->id }}
                                                </h5>
                                                <span class="fs-12 text-muted">Submitted on
                                                    {{ $item->created_at->format('d M Y, h:i A') }}</span>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <!-- Basic Information Grid -->
                                            <div class="row g-3 mb-4">
                                                <div class="col-sm-6">
                                                    <div class="p-3 rounded bg-light">
                                                        <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">
                                                            Applicant Name</div>
                                                        <div class="fw-bold text-dark fs-14">{{ $item->full_name }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="p-3 rounded bg-light">
                                                        <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">
                                                            Target Package</div>
                                                        <div class="fw-bold text-dark fs-14">
                                                            {{ $item->package?->package_title ?? 'Custom / Removed Package' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="p-3 rounded bg-light">
                                                        <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">Phone
                                                            / WhatsApp</div>
                                                        <div class="fw-semibold text-dark fs-14">
                                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->contact) }}"
                                                                target="_blank" class="text-success text-decoration-none">
                                                                <i class="mdi mdi-whatsapp me-1"></i> {{ $item->contact }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="p-3 rounded bg-light">
                                                        <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">Email
                                                            Address</div>
                                                        <div class="fw-semibold text-dark fs-14">
                                                            <a href="mailto:{{ $item->email }}"
                                                                class="text-primary text-decoration-none">
                                                                <i class="mdi mdi-email-outline me-1"></i>
                                                                {{ $item->email }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="p-3 rounded bg-light">
                                                        <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">CNIC
                                                            / ID</div>
                                                        <div class="fw-semibold text-dark fs-14">
                                                            {{ $item->cnic_number ?? 'Not Provided' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="p-3 rounded bg-light">
                                                        <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">
                                                            Passport Number</div>
                                                        <div class="fw-semibold text-dark fs-14">
                                                            {{ $item->passport_number ?? 'Not Provided' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="p-3 rounded bg-light">
                                                        <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">Pax &
                                                            Room</div>
                                                        <div class="fw-semibold text-dark fs-14">{{ $item->pax }} Pax
                                                            ({{ ucfirst($item->room_type) }})
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Special Request / Remarks -->
                                            <div class="mb-4">
                                                <h6 class="fs-13 fw-bold text-uppercase text-muted mb-2">Special Requests &
                                                    Remarks</h6>
                                                <div class="p-3 border rounded bg-light-subtle text-dark fs-13">
                                                    {{ $item->special_request ?: 'No special requests submitted.' }}
                                                </div>
                                            </div>

                                            <!-- Documents View Section -->
                                            <div class="mb-2">
                                                <h6
                                                    class="fs-13 fw-bold text-uppercase text-muted mb-3 d-flex align-items-center">
                                                    <i
                                                        class="mdi mdi-file-document-multiple-outline me-1 text-primary fs-16"></i>
                                                    Attached Documents
                                                </h6>

                                                <div class="row g-3">
                                                    <!-- Passport Document Card -->
                                                    <div class="col-md-6">
                                                        <div
                                                            class="border rounded p-3 h-100 d-flex flex-column justify-content-between bg-white shadow-none">
                                                            <div class="d-flex align-items-center mb-3">
                                                                <div class="avatar-sm rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3"
                                                                    style="width: 42px; height: 42px;">
                                                                    <i class="mdi mdi-passport fs-22"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-0 fs-14 fw-bold">Passport Document</h6>
                                                                    <span class="fs-12 text-muted">
                                                                        {{ $item->passport_document ? 'Document Uploaded' : 'Not Uploaded' }}
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            @if ($item->passport_document)
                                                                @php
                                                                    $passportUrl = asset(
                                                                        'storage/' . $item->passport_document,
                                                                    );
                                                                    $passportExt = strtolower(
                                                                        pathinfo(
                                                                            $item->passport_document,
                                                                            PATHINFO_EXTENSION,
                                                                        ),
                                                                    );
                                                                @endphp
                                                                @if (in_array($passportExt, ['jpg', 'jpeg', 'png', 'webp', 'gif']))
                                                                    <div class="mb-3 text-center bg-light rounded p-2">
                                                                        <a href="{{ $passportUrl }}" target="_blank">
                                                                            <img src="{{ $passportUrl }}" alt="Passport"
                                                                                class="img-fluid rounded border"
                                                                                style="max-height: 180px; object-fit: contain;">
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                                <div class="d-flex gap-2">
                                                                    <a href="{{ $passportUrl }}" target="_blank"
                                                                        class="btn btn-sm btn-outline-primary flex-fill">
                                                                        <i class="mdi mdi-eye me-1"></i> View Full
                                                                    </a>
                                                                    <a href="{{ $passportUrl }}" download
                                                                        class="btn btn-sm btn-light border flex-fill">
                                                                        <i class="mdi mdi-download me-1"></i> Download
                                                                    </a>
                                                                </div>
                                                            @else
                                                                <div
                                                                    class="text-center py-3 bg-light rounded text-muted fs-12">
                                                                    <i
                                                                        class="mdi mdi-alert-circle-outline fs-18 d-block mb-1"></i>
                                                                    No passport document attached
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- NIC / Visa Document Card -->
                                                    <div class="col-md-6">
                                                        <div
                                                            class="border rounded p-3 h-100 d-flex flex-column justify-content-between bg-white shadow-none">
                                                            <div class="d-flex align-items-center mb-3">
                                                                <div class="avatar-sm rounded bg-info-subtle text-info d-flex align-items-center justify-content-center me-3"
                                                                    style="width: 42px; height: 42px;">
                                                                    <i class="mdi mdi-card-account-details fs-22"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-0 fs-14 fw-bold">NIC / Visa Document</h6>
                                                                    <span class="fs-12 text-muted">
                                                                        {{ $item->nic_or_visa_document ? 'Document Uploaded' : 'Not Uploaded' }}
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            @if ($item->nic_or_visa_document)
                                                                @php
                                                                    $nicUrl = asset(
                                                                        'storage/' . $item->nic_or_visa_document,
                                                                    );
                                                                    $nicExt = strtolower(
                                                                        pathinfo(
                                                                            $item->nic_or_visa_document,
                                                                            PATHINFO_EXTENSION,
                                                                        ),
                                                                    );
                                                                @endphp
                                                                @if (in_array($nicExt, ['jpg', 'jpeg', 'png', 'webp', 'gif']))
                                                                    <div class="mb-3 text-center bg-light rounded p-2">
                                                                        <a href="{{ $nicUrl }}" target="_blank">
                                                                            <img src="{{ $nicUrl }}" alt="NIC/Visa"
                                                                                class="img-fluid rounded border"
                                                                                style="max-height: 180px; object-fit: contain;">
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                                <div class="d-flex gap-2">
                                                                    <a href="{{ $nicUrl }}" target="_blank"
                                                                        class="btn btn-sm btn-outline-info flex-fill">
                                                                        <i class="mdi mdi-eye me-1"></i> View Full
                                                                    </a>
                                                                    <a href="{{ $nicUrl }}" download
                                                                        class="btn btn-sm btn-light border flex-fill">
                                                                        <i class="mdi mdi-download me-1"></i> Download
                                                                    </a>
                                                                </div>
                                                            @else
                                                                <div
                                                                    class="text-center py-3 bg-light rounded text-muted fs-12">
                                                                    <i
                                                                        class="mdi mdi-alert-circle-outline fs-18 d-block mb-1"></i>
                                                                    No NIC / Visa document attached
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light py-2">
                                            <div class="me-auto d-flex align-items-center gap-2">
                                                <span class="fs-12 fw-semibold text-muted">Current Status:</span>
                                                @if ($item->status === 'pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @elseif($item->status === 'contacted')
                                                    <span class="badge bg-info text-white">Contacted</span>
                                                @else
                                                    <span class="badge bg-success text-white">Closed</span>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($item->status !== 'closed')
                                                    <form action="{{ route('mobile-ops.inquiries.confirm', $item->id) }}" method="POST"
                                                        onsubmit="return handleConfirmBooking(event, this, '{{ $directWaUrl }}', '{{ addslashes($item->full_name) }}');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm d-inline-flex align-items-center gap-1">
                                                            <i class="mdi mdi-whatsapp"></i> Confirm & Auto Open WhatsApp
                                                        </button>
                                                    </form>
                                                @endif
                                                <button type="button" class="btn btn-secondary btn-sm"
                                                    data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-inbox-outline fs-36 d-block mb-2 text-secondary"></i>
                                    <h6 class="text-muted fw-semibold mb-1">No Inquiries Found</h6>
                                    <p class="fs-12 mb-0">There are no guest package inquiries matching this filter.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($inquiries->hasPages())
                <div class="p-3 border-top">
                    {{ $inquiries->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    <style>
        .bg-primary-subtle {
            background-color: rgba(13, 110, 253, 0.1) !important;
        }

        .bg-info-subtle {
            background-color: rgba(13, 202, 240, 0.1) !important;
        }

        .btn-white {
            background-color: #fff;
            color: #333;
        }

        .btn-white:hover {
            background-color: #f8f9fa;
        }
    </style>

    <script>
        function handleConfirmBooking(e, form, waUrl, clientName) {
            if (!confirm('Confirm booking for ' + clientName + ' and auto open WhatsApp confirmation?')) {
                e.preventDefault();
                return false;
            }
            if (waUrl) {
                window.open(waUrl, '_blank');
            }
            return true;
        }

        document.addEventListener('DOMContentLoaded', function () {
            @if (session('whatsapp_url'))
                const waUrl = @json(session('whatsapp_url'));
                if (waUrl) {
                    window.open(waUrl, '_blank');
                }
            @endif
        });
    </script>
@endsection
