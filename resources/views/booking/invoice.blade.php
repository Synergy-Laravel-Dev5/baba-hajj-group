<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Invoice #{{ $booking->voucher_number ?? $booking->id }} — {{ $booking->client->name ?? 'Client' }} | Baba Hajj Group</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --navy: #091322;
            --navy-light: #16243b;
            --gold: #c9a84c;
            --gold-dark: #9a7828;
            --gold-light: #fef08a;
            --green: #10b981;
            --green-dark: #047857;
            --red: #ef4444;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-500: #64748b;
            --gray-800: #1e293b;
        }

        body {
            background: #e2e8f0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #0f172a;
            font-size: 13px;
            line-height: 1.45;
            padding: 24px 0 60px;
            margin: 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .invoice-sheet {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--gray-200);
        }

        /* Top Action Bar (Screen Only) */
        .action-bar-top {
            max-width: 860px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.18s ease;
        }

        .btn-print {
            background: linear-gradient(135deg, #091322, #1e3a8a);
            color: #ffffff;
        }
        .btn-print:hover {
            color: #fde047;
            transform: translateY(-1px);
        }

        .btn-back {
            background: #ffffff;
            color: var(--gray-800);
            border-color: var(--gray-200);
        }
        .btn-back:hover {
            background: var(--gray-100);
        }

        /* Invoice Header */
        .invoice-header {
            background: linear-gradient(135deg, #070e19 0%, #0f1e36 60%, #172b4d 100%);
            color: #ffffff;
            padding: 28px 36px;
            border-bottom: 4px solid var(--gold);
            position: relative;
        }

        .invoice-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            flex-wrap: wrap;
        }

        .company-branding {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .company-logo {
            height: 64px;
            width: auto;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08);
            padding: 4px;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .company-name {
            font-family: 'Cinzel', serif;
            font-size: 22px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 0.5px;
            line-height: 1.15;
            margin-bottom: 3px;
        }

        .company-sub {
            font-size: 11px;
            color: var(--gold-light);
            font-weight: 600;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .invoice-title-block {
            text-align: right;
        }

        .invoice-badge-title {
            font-family: 'Cinzel', serif;
            font-size: 20px;
            font-weight: 800;
            color: var(--gold-light);
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .invoice-meta-num {
            font-size: 13px;
            color: #cbd5e1;
            font-weight: 600;
        }

        .invoice-status-pill {
            display: inline-block;
            margin-top: 6px;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-paid {
            background: rgba(16, 185, 129, 0.2);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.45);
        }
        .status-partial {
            background: rgba(245, 158, 11, 0.2);
            color: #fde047;
            border: 1px solid rgba(245, 158, 11, 0.45);
        }
        .status-unpaid {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.45);
        }

        /* Meta Two-Column Card */
        .meta-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
        }

        .meta-col {
            padding: 20px 36px;
        }

        .meta-col:first-child {
            border-right: 1px solid var(--gray-200);
        }

        .meta-heading {
            font-size: 10px;
            font-weight: 800;
            color: var(--gold-dark);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .meta-val-large {
            font-size: 16px;
            font-weight: 800;
            color: var(--navy);
            margin-bottom: 4px;
        }

        .meta-kv {
            font-size: 12px;
            color: #475569;
            margin-bottom: 3px;
        }
        .meta-kv strong {
            color: #0f172a;
        }

        /* Content Padding */
        .invoice-body {
            padding: 24px 36px;
        }

        /* Section Block */
        .inv-section-title {
            font-size: 12px;
            font-weight: 800;
            color: var(--navy);
            text-transform: uppercase;
            letter-spacing: 0.6px;
            border-bottom: 1.5px solid var(--navy);
            padding-bottom: 5px;
            margin: 20px 0 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Table Design */
        .inv-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 14px;
        }

        .inv-table th {
            background: var(--gray-100);
            color: var(--navy);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10.5px;
            letter-spacing: 0.4px;
            padding: 8px 12px;
            border-top: 1px solid var(--gray-200);
            border-bottom: 1.5px solid var(--gray-200);
        }

        .inv-table td {
            padding: 8px 12px;
            border-bottom: 1px solid var(--gray-200);
            vertical-align: middle;
        }

        .inv-table tr:hover {
            background: rgba(248, 250, 252, 0.6);
        }

        /* Summary Ledger */
        .ledger-box {
            display: flex;
            justify-content: flex-end;
            margin-top: 16px;
        }

        .ledger-table {
            width: 380px;
            border-collapse: collapse;
            font-size: 12.5px;
        }

        .ledger-table td {
            padding: 6px 12px;
            border-bottom: 1px solid var(--gray-200);
        }

        .ledger-table td:last-child {
            text-align: right;
            font-weight: 700;
        }

        .ledger-table tr.total-row td {
            background: var(--navy);
            color: #ffffff;
            font-weight: 800;
            font-size: 14px;
            border-top: 2px solid var(--gold);
        }
        .ledger-table tr.total-row td:last-child {
            color: var(--gold-light);
            font-size: 16px;
        }

        .ledger-table tr.balance-row td {
            font-size: 13.5px;
            font-weight: 800;
            background: #fef2f2;
            color: #991b1b;
        }
        .ledger-table tr.balance-row.settled td {
            background: #f0fdf4;
            color: #166534;
        }

        /* Bank & Notes Footer */
        .inv-footer {
            background: var(--gray-50);
            border-top: 1px solid var(--gray-200);
            padding: 20px 36px;
            font-size: 11px;
            color: #475569;
        }

        .sign-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 36px;
            padding-top: 20px;
        }

        .sign-line {
            border-top: 1px dashed #94a3b8;
            padding-top: 6px;
            text-align: center;
            font-weight: 700;
            color: #334155;
            font-size: 11.5px;
            text-transform: uppercase;
        }

        /* Print Media */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .invoice-sheet {
                box-shadow: none !important;
                border: none !important;
                max-width: 100% !important;
                border-radius: 0 !important;
            }
            .invoice-header {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

    <!-- SCREEN ACTIONS -->
    <div class="action-bar-top no-print">
        <a href="{{ route('booking.show', $booking->id) }}" class="btn-action btn-back">
            <i class="bi bi-arrow-left"></i> Back to Booking
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('booking.voucher', $booking->id) }}" class="btn-action btn-back" target="_blank">
                <i class="bi bi-ticket-perforated"></i> Hotel Voucher
            </a>
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="bi bi-printer-fill"></i> Print Official Invoice
            </button>
        </div>
    </div>

    <!-- INVOICE SHEET -->
    <div class="invoice-sheet" id="invoiceSheet">

        <!-- HEADER -->
        <div class="invoice-header">
            <div class="invoice-header-flex">
                <div class="company-branding">
                    <img src="{{ asset('assets/images/logo.png') }}?v={{ time() }}" alt="Baba Hajj Group" class="company-logo">
                    <div>
                        <div class="company-name">BABA HAJJ GROUP</div>
                        <div class="company-sub">VIP Hajj & Umrah Tour Management System</div>
                        <div style="font-size:11px; color:#cbd5e1; margin-top:3px;">
                            <i class="bi bi-shield-check text-warning me-1"></i>Approved Tour Operator · KSA Munazzam Portal
                        </div>
                    </div>
                </div>

                <div class="invoice-title-block">
                    <div class="invoice-badge-title">BOOKING INVOICE</div>
                    <div class="invoice-meta-num">Voucher / Inv #: <strong>{{ $booking->voucher_number ?? $booking->id }}</strong></div>
                    <div class="invoice-meta-num">Date: {{ \Carbon\Carbon::parse($booking->created_at)->format('d M, Y') }}</div>
                    @php
                        $balance = (float)$booking->balance;
                        $received = (float)$booking->total_received;
                        $total = (float)$booking->total_amount;
                    @endphp
                    @if($balance <= 0 && $total > 0)
                        <div class="invoice-status-pill status-paid"><i class="bi bi-check-circle-fill me-1"></i>Fully Paid</div>
                    @elseif($received > 0)
                        <div class="invoice-status-pill status-partial"><i class="bi bi-clock-history me-1"></i>Partially Paid</div>
                    @else
                        <div class="invoice-status-pill status-unpaid"><i class="bi bi-exclamation-circle-fill me-1"></i>Payment Pending</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- CLIENT & PACKAGE META -->
        <div class="meta-container">
            <!-- Billed To -->
            <div class="meta-col">
                <div class="meta-heading"><i class="bi bi-person-fill"></i> Billed To Client</div>
                <div class="meta-val-large">{{ $booking->client->name ?? 'Guest Client' }}</div>
                @if($booking->client?->cnic)
                    <div class="meta-kv">CNIC: <strong>{{ $booking->client->cnic }}</strong></div>
                @endif
                @if($booking->client?->phone || $booking->phone)
                    <div class="meta-kv">Contact: <strong>{{ $booking->client->phone ?? $booking->phone }}</strong></div>
                @endif
                @php
                    $refCompany = $booking->company ?? $booking->client?->referral;
                @endphp
                @if($refCompany)
                    <div class="meta-kv">Referral / Agent: <strong style="color:var(--navy);">{{ $refCompany->company_name }}</strong></div>
                @endif
                @if($booking->care_of)
                    <div class="meta-kv">Care Of: <strong>{{ $booking->care_of }}</strong></div>
                @endif
            </div>

            <!-- Booking Package Details -->
            <div class="meta-col">
                <div class="meta-heading"><i class="bi bi-box-seam-fill"></i> Package & Camp Details</div>
                <div class="meta-val-large" style="font-size:15px; color:var(--gold-dark);">
                    {{ $booking->package->package_title ?? $booking->package->name ?? $booking->package_name ?? 'Hajj Package' }}
                    @if($booking->package && ($booking->package->code || $booking->package->package_number))
                        <span class="badge bg-dark text-light ms-1" style="font-size:10px;">{{ $booking->package->code ?? $booking->package->package_number }}</span>
                    @endif
                </div>
                <div class="meta-kv">Season: <strong>{{ strtoupper($booking->package_type ?? 'HAJJ') }} {{ $booking->package_year ?? date('Y') }}</strong></div>
                <div class="meta-kv">Camp / Maktab: <strong>{{ $booking->camp ?? ($booking->package->camp_category ?? 'Maktab C (Zone 5)') }}</strong></div>
                <div class="meta-kv">Pilgrim Count: <strong>{{ $booking->persons->count() ?: ($booking->no_of_pax ?? 1) }} Pax</strong></div>
                @if($booking->qurbani_charges > 0 || ($booking->qurbani_option && $booking->qurbani_option !== 'not_included'))
                    <div class="meta-kv">Qurbani: <strong class="text-success">{{ ucfirst(str_replace('_', ' ', $booking->qurbani_option)) }} @if($booking->qurbani_qty) ({{ $booking->qurbani_qty }} Heads) @endif</strong></div>
                @endif
            </div>
        </div>

        <!-- INVOICE BODY -->
        <div class="invoice-body">

            <!-- 1. TRAVELLING PILGRIMS LIST -->
            @if($booking->persons && $booking->persons->count() > 0)
                <div class="inv-section-title"><i class="bi bi-people-fill text-primary"></i> Travelling Pilgrims Manifest ({{ $booking->persons->count() }} Pax)</div>
                <table class="inv-table">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Pilgrim Full Name</th>
                            <th>Passport #</th>
                            <th>HB / Pilgrim ID</th>
                            <th>Gender</th>
                            <th>City / Origin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($booking->persons as $idx => $person)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <strong>{{ $person->full_name }}</strong>
                                    @if($person->father_name)
                                        <div class="text-muted" style="font-size:10.5px;">S/O: {{ $person->father_name }}</div>
                                    @endif
                                </td>
                                <td>{{ $person->passport_number ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-secondary" style="font-size:10px;">{{ $person->hb_number ?? 'HB-'.str_pad($person->id, 4, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td>{{ ucfirst($person->gender ?? 'Male') }}</td>
                                <td>{{ $person->city ?? 'Pakistan' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <!-- 2. ACCOMMODATION / STAY SCHEDULE -->
            @php
                $stayList = $booking->hotels->count() > 0 ? $booking->hotels : ($booking->package?->accommodations ?? collect());
            @endphp
            @if($stayList && $stayList->count() > 0)
                <div class="inv-section-title"><i class="bi bi-building-fill text-warning"></i> Hotel & Stay Itinerary</div>
                <table class="inv-table">
                    <thead>
                        <tr>
                            <th>Location</th>
                            <th>Hotel / Accommodation</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Meal Plan</th>
                            <th>Basis</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stayList as $h)
                            @php
                                $hName = $h->hotel_name ?? ($h->package_a_hotel ?? ($h->place ?? 'Designated Hotel'));
                                $loc = ucfirst($h->location ?? ($h->place ?? 'Makkah'));
                                $inDate = $h->check_in ? \Carbon\Carbon::parse($h->check_in)->format('d M, Y') : '—';
                                $outDate = $h->check_out ? \Carbon\Carbon::parse($h->check_out)->format('d M, Y') : '—';
                                $hijriIn = $h->hijri_check_in ?? ($h->note ?? '');
                                $hijriOut = $h->hijri_check_out ?? ($h->sharing ?? '');
                                $meal = $h->meal_plan ?? ($h->package_a_food_package ?? ($h->food_package ?? 'Room Only'));
                            @endphp
                            <tr>
                                <td><strong>{{ $loc }}</strong></td>
                                <td>
                                    <strong>{{ $hName }}</strong>
                                    @if(!empty($h->room_number))
                                        <span class="badge bg-light text-dark border ms-1" style="font-size:10px;">Room: {{ $h->room_number }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $inDate }}</div>
                                    @if($hijriIn)<div class="text-primary fw-semibold" style="font-size:10px;">{{ $hijriIn }}</div>@endif
                                </td>
                                <td>
                                    <div>{{ $outDate }}</div>
                                    @if($hijriOut)<div class="text-primary fw-semibold" style="font-size:10px;">{{ $hijriOut }}</div>@endif
                                </td>
                                <td><span class="badge bg-success-subtle text-success border" style="font-size:10px;">{{ $meal }}</span></td>
                                <td>{{ ucfirst($h->allotment_basis ?? 'Room') }} ({{ ucfirst($h->room_type ?? 'Quad') }})</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <!-- 3. FINANCIAL CHARGES & BREAKDOWN -->
            <div class="inv-section-title"><i class="bi bi-cash-stack text-success"></i> Financial Costing & Ledger Summary</div>
            <table class="inv-table">
                <thead>
                    <tr>
                        <th>Item Description</th>
                        <th class="text-center" style="width:100px;">Rate / Unit</th>
                        <th class="text-center" style="width:80px;">Qty / Pax</th>
                        <th class="text-end" style="width:140px;">Amount (PKR)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>{{ $booking->package->package_title ?? $booking->package->name ?? 'Hajj Package' }}</strong>
                            <div class="text-muted" style="font-size:10.5px;">Standard Package Inclusions, Accommodation & Services</div>
                        </td>
                        <td class="text-center">{{ number_format($booking->package_cost, 0) }}</td>
                        <td class="text-center">{{ $booking->no_of_pax }} Pax</td>
                        <td class="text-end fw-bold">{{ number_format($booking->package_cost * $booking->no_of_pax, 0) }}</td>
                    </tr>

                    @if($booking->visa_charges > 0)
                        <tr>
                            <td><strong>Visa Processing & Ministry Fees</strong></td>
                            <td class="text-center">—</td>
                            <td class="text-center">{{ $booking->no_of_pax }} Pax</td>
                            <td class="text-end">{{ number_format($booking->visa_charges, 0) }}</td>
                        </tr>
                    @endif

                    @if($booking->flight_charges > 0)
                        <tr>
                            <td><strong>International / Domestic Flight Airfares</strong></td>
                            <td class="text-center">—</td>
                            <td class="text-center">{{ $booking->no_of_pax }} Pax</td>
                            <td class="text-end">{{ number_format($booking->flight_charges, 0) }}</td>
                        </tr>
                    @endif

                    @if($booking->qurbani_charges > 0)
                        <tr>
                            <td><strong>Hajj Qurbani (Adahi / Nusuk)</strong> <span class="text-muted small">({{ $booking->qurbani_qty ?? 1 }} heads)</span></td>
                            <td class="text-center">—</td>
                            <td class="text-center">{{ $booking->qurbani_qty ?? 1 }}</td>
                            <td class="text-end">{{ number_format($booking->qurbani_charges, 0) }}</td>
                        </tr>
                    @endif

                    @if($booking->other_charges > 0)
                        <tr>
                            <td><strong>Additional Services & Supplements</strong></td>
                            <td class="text-center">—</td>
                            <td class="text-center">—</td>
                            <td class="text-end">{{ number_format($booking->other_charges, 0) }}</td>
                        </tr>
                    @endif

                    @if(($booking->discount ?? 0) > 0)
                        <tr class="text-danger">
                            <td><strong>Special Concession / Package Rebate</strong></td>
                            <td class="text-center">—</td>
                            <td class="text-center">—</td>
                            <td class="text-end">- {{ number_format($booking->discount, 0) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <!-- LEDGER TOTALS BOX -->
            <div class="ledger-box">
                <table class="ledger-table">
                    <tr>
                        <td>Gross Subtotal:</td>
                        <td>PKR {{ number_format($booking->total_amount + ($booking->discount ?? 0), 0) }}</td>
                    </tr>
                    @if(($booking->discount ?? 0) > 0)
                        <tr class="text-danger">
                            <td>Discount Applied:</td>
                            <td>- PKR {{ number_format($booking->discount, 0) }}</td>
                        </tr>
                    @endif
                    <tr class="total-row">
                        <td>Net Total Invoice Amount:</td>
                        <td>PKR {{ number_format($booking->total_amount, 0) }}</td>
                    </tr>
                    <tr style="color:var(--green-dark);">
                        <td>Total Received / Cleared:</td>
                        <td>PKR {{ number_format($booking->total_received, 0) }}</td>
                    </tr>
                    <tr class="balance-row {{ $booking->balance <= 0 ? 'settled' : '' }}">
                        <td>Outstanding Balance Due:</td>
                        <td>PKR {{ number_format($booking->balance, 0) }}</td>
                    </tr>
                </table>
            </div>

            <!-- PAYMENT TRANSACTIONS LEDGER (IF ANY) -->
            @if($booking->transactions && $booking->transactions->count() > 0)
                <div class="inv-section-title mt-4"><i class="bi bi-receipt text-primary"></i> Payment Receipts / Credited Transactions</div>
                <table class="inv-table">
                    <thead>
                        <tr>
                            <th>Receipt #</th>
                            <th>Payment Date</th>
                            <th>Payment Method / Account</th>
                            <th>Status</th>
                            <th class="text-end">Amount (PKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($booking->transactions as $txn)
                            <tr>
                                <td><strong>#{{ str_pad($txn->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                                <td>{{ $txn->payment_date ? \Carbon\Carbon::parse($txn->payment_date)->format('d M, Y') : '—' }}</td>
                                <td>{{ strtoupper($txn->payment_method ?? 'Cash / Bank Transfer') }}</td>
                                <td><span class="badge bg-success" style="font-size:10px;">Confirmed</span></td>
                                <td class="text-end fw-bold text-success">PKR {{ number_format($txn->amount, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

        </div>

        <!-- FOOTER & SIGNATURES -->
        <div class="inv-footer">
            <div class="row g-3">
                <div class="col-md-7">
                    <div class="fw-bold text-dark mb-1" style="font-size:11.5px;">Official Hajj Package Terms & Policies:</div>
                    <ul class="ps-3 mb-0" style="line-height:1.5;">
                        <li>Package includes standard accommodations, camp zones, and ground services as notified.</li>
                        <li>All bookings are subject to final Nusuk Masar quota verification & Ministry of Hajj approvals.</li>
                        <li>Payments should be remitted only to official designated bank accounts of Baba Hajj Group.</li>
                    </ul>
                </div>
                <div class="col-md-5 text-md-end">
                    <div class="fw-bold text-dark mb-1">Company Contact & Verification:</div>
                    <div>Baba Hajj Group Head Office</div>
                    <div>Official Helpline: +92 300 0000000 / +966 50 0000000</div>
                    <div>Website: www.babahajjgroup.com · Email: info@babahajjgroup.com</div>
                </div>
            </div>

            <div class="sign-grid">
                <div>
                    <div class="sign-line">Client / Representative Signature</div>
                </div>
                <div>
                    <div class="sign-line">Authorized Signatory & Official Stamp</div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
