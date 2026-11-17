<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $booking->voucher_number ?? $booking->id }} — {{ $booking->client->name ?? 'Client' }}</title>
    <style>
        @page {
            margin: 15mm 12mm 15mm 12mm;
            size: A4 portrait;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.35;
            color: #1e293b;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        /* Layout Tables */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Header */
        .header-table {
            background-color: #091322;
            color: #ffffff;
            padding: 16px 20px;
            border-bottom: 3px solid #c9a84c;
            margin-bottom: 12px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #ffffff;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .company-sub {
            font-size: 9.5px;
            color: #fef08a;
            font-weight: bold;
            text-transform: uppercase;
        }
        .company-meta {
            font-size: 9px;
            color: #cbd5e1;
            margin-top: 3px;
        }
        .inv-title {
            font-size: 16px;
            font-weight: bold;
            color: #fef08a;
            text-align: right;
            margin-bottom: 3px;
        }
        .inv-meta-right {
            font-size: 10px;
            color: #e2e8f0;
            text-align: right;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .status-paid { background-color: #10b981; color: #ffffff; }
        .status-partial { background-color: #f59e0b; color: #ffffff; }
        .status-unpaid { background-color: #ef4444; color: #ffffff; }

        /* Meta Two Column */
        .meta-table {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            margin-bottom: 14px;
        }
        .meta-col {
            width: 50%;
            padding: 12px 16px;
            vertical-align: top;
        }
        .meta-col-left {
            border-right: 1px solid #e2e8f0;
        }
        .meta-head {
            font-size: 9px;
            font-weight: bold;
            color: #9a7828;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .meta-val-large {
            font-size: 13.5px;
            font-weight: bold;
            color: #091322;
            margin-bottom: 4px;
        }
        .meta-line {
            font-size: 10px;
            color: #475569;
            margin-bottom: 2px;
        }
        .meta-line strong {
            color: #091322;
        }
        .prefix-badge {
            display: inline-block;
            background-color: #091322;
            color: #fef08a;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 9.5px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        /* Section Titles */
        .section-title {
            font-size: 10.5px;
            font-weight: bold;
            color: #091322;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1.5px solid #091322;
            padding-bottom: 3px;
            margin: 14px 0 8px 0;
        }

        /* Content Data Tables */
        .data-table {
            width: 100%;
            margin-bottom: 12px;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #091322;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            padding: 6px 8px;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1.5px solid #cbd5e1;
            text-align: left;
        }
        .data-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9.5px;
            vertical-align: middle;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }

        .hajj-badge {
            background-color: #1e3a8a;
            color: #ffffff;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 8.5px;
            font-weight: bold;
        }

        /* Ledger Box */
        .ledger-table {
            width: 290px;
            margin-left: auto;
            border-collapse: collapse;
            font-size: 10px;
            margin-top: 8px;
        }
        .ledger-table td {
            padding: 4px 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        .ledger-table td:last-child {
            text-align: right;
            font-weight: bold;
        }
        .ledger-total td {
            background-color: #091322;
            color: #ffffff;
            font-weight: bold;
            font-size: 11px;
            border-top: 1.5px solid #c9a84c;
        }
        .ledger-total td:last-child {
            color: #fef08a;
            font-size: 12px;
        }
        .ledger-balance td {
            background-color: #fef2f2;
            color: #991b1b;
            font-weight: bold;
            font-size: 11px;
        }
        .ledger-balance.settled td {
            background-color: #f0fdf4;
            color: #166534;
        }

        /* Footer */
        .footer-table {
            margin-top: 24px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            font-size: 8.5px;
            color: #64748b;
        }
        .sign-table {
            width: 100%;
            margin-top: 30px;
        }
        .sign-cell {
            width: 50%;
            text-align: center;
            padding: 0 30px;
        }
        .sign-line {
            border-top: 1px dashed #94a3b8;
            padding-top: 4px;
            font-size: 9px;
            font-weight: bold;
            color: #334155;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    @php
        $logoPath = public_path('assets/images/logo.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
        $balance = (float)$booking->balance;
        $received = (float)$booking->total_received;
        $total = (float)$booking->total_amount;

        $fullPkgName = $booking->package->package_title ?? ($booking->package->name ?? ($booking->package_name ?? 'Hajj Package'));
        $pkgPrefix = $booking->persons->pluck('hb_number')->filter()->first() ?: ($booking->package?->next_booking_code ?? ($booking->package?->code ?? 'AZ 01'));
        $campName = $booking->camp ?? ($booking->package->maktab ?? ($booking->package->camp_category ? 'Maktab ' . $booking->package->camp_category : ''));
    @endphp

    <!-- HEADER TABLE -->
    <table class="header-table">
        <tr>
            <td style="width: 60%; vertical-align: middle;">
                <table style="width: 100%;">
                    <tr>
                        @if($logoBase64)
                            <td style="width: 65px; vertical-align: middle;">
                                <img src="data:image/png;base64,{{ $logoBase64 }}" style="height: 52px; width: auto; background:#ffffff; padding:2px; border-radius:4px;">
                            </td>
                        @endif
                        <td style="vertical-align: middle; padding-left: 8px;">
                            <div class="company-name">BABA HAJJ GROUP</div>
                            <div class="company-sub">VIP Hajj & Umrah Tour Management System</div>
                            <div class="company-meta">Approved Tour Operator · KSA Munazzam Portal</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 40%; vertical-align: middle; text-align: right;">
                <div class="inv-title">BOOKING INVOICE</div>
                <div class="inv-meta-right">Voucher / Inv #: <strong>{{ $booking->voucher_number ?? $booking->id }}</strong></div>
                <div class="inv-meta-right">Date: {{ \Carbon\Carbon::parse($booking->created_at)->format('d M, Y') }}</div>
                <div>
                    @if($balance <= 0 && $total > 0)
                        <span class="status-badge status-paid">Fully Paid</span>
                    @elseif($received > 0)
                        <span class="status-badge status-partial">Partially Paid</span>
                    @else
                        <span class="status-badge status-unpaid">Payment Pending</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- CLIENT & PACKAGE METADATA TABLE -->
    <table class="meta-table">
        <tr>
            <!-- Left: Billed to Client -->
            <td class="meta-col meta-col-left">
                <div class="meta-head">Billed To Client</div>
                <div class="meta-val-large">{{ $booking->client->name ?? 'Guest Client' }}</div>
                @if($booking->client?->cnic)
                    <div class="meta-line">CNIC: <strong>{{ $booking->client->cnic }}</strong></div>
                @endif
                @if($booking->client?->phone || $booking->phone)
                    <div class="meta-line">Contact: <strong>{{ $booking->client->phone ?? $booking->phone }}</strong></div>
                @endif
                @if($booking->care_of)
                    <div class="meta-line">Care Of: <strong>{{ $booking->care_of }}</strong></div>
                @endif
            </td>

            <!-- Right: Package & Camp Details -->
            <td class="meta-col">
                <div class="meta-head">Package & Camp Details</div>
                <div class="meta-val-large" style="color: #091322;">{{ $fullPkgName }}</div>
                <div class="meta-line" style="margin-bottom: 4px;">
                    Prefix: <span class="prefix-badge">{{ $pkgPrefix }}</span>
                </div>
                <div class="meta-line">Season: <strong>{{ strtoupper($booking->package_type ?? 'HAJJ') }} {{ $booking->package_year ?? date('Y') }}</strong></div>
                @if(!empty($campName))
                    <div class="meta-line">Camp / Maktab: <strong>{{ $campName }}</strong></div>
                @endif
                <div class="meta-line">Pilgrim Count: <strong>{{ $booking->persons->count() ?: ($booking->no_of_pax ?? 1) }} Pax</strong></div>
                @if($booking->qurbani_charges > 0 || ($booking->qurbani_option && $booking->qurbani_option !== 'not_included'))
                    @php
                        $qStatus = 'Included';
                        if ($booking->qurbani_option === 'separate_request') {
                            $qStatus = 'Separate Request';
                        }
                    @endphp
                    <div class="meta-line">Qurbani: <strong style="color:#047857;">{{ $qStatus }} @if($booking->qurbani_qty) ({{ $booking->qurbani_qty }} {{ $booking->qurbani_qty > 1 ? 'Heads' : 'Head' }}) @endif</strong></div>
                @endif
            </td>
        </tr>
    </table>

    <!-- 1. TRAVELLING PILGRIMS LIST -->
    @if($booking->persons && $booking->persons->count() > 0)
        <div class="section-title">Travelling Pilgrims Manifest ({{ $booking->persons->count() }} Pax)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;">#</th>
                    <th>Pilgrim Full Name</th>
                    <th>Hajj ID</th>
                    <th>Prefix</th>
                    <th>Passport #</th>
                    <th>CNIC</th>
                    <th>Gender</th>
                    <th>Contact</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->persons as $idx => $person)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>
                            <strong>{{ $person->full_name }}</strong>
                            @if($person->father_name)
                                <div style="font-size: 8px; color: #64748b;">S/O: {{ $person->father_name }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="hajj-badge">{{ $person->hajj_id ?? ('BG'.str_pad($person->id, 3, '0', STR_PAD_LEFT)) }}</span>
                        </td>
                        <td>
                            <span class="prefix-badge" style="font-size: 8.5px;">{{ $person->hb_number ?? $pkgPrefix }}</span>
                        </td>
                        <td>{{ $person->passport_number ?? '—' }}</td>
                        <td>{{ $person->cnic ?? '—' }}</td>
                        <td>{{ ucfirst($person->gender ?? 'Male') }}</td>
                        <td>{{ $person->phone ?? ($booking->phone ?? '—') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- 2. ACCOMMODATION SCHEDULE -->
    @php
        $stayList = $booking->hotels->count() > 0 ? $booking->hotels : ($booking->package?->accommodations ?? collect());
    @endphp
    @if($stayList && $stayList->count() > 0)
        <div class="section-title">Hotel & Stay Itinerary</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 65px;">Location</th>
                    <th>Hotel Name</th>
                    <th style="width: 80px;">Check In</th>
                    <th style="width: 80px;">Check Out</th>
                    <th style="width: 60px;">Meal Plan</th>
                    <th style="width: 90px;">Basis / Room</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stayList as $h)
                    @php
                        $hName = $h->hotel_name ?? ($h->package_a_hotel ?? ($h->place ?? 'Designated Hotel'));
                        $loc = ucfirst($h->location ?? ($h->place ?? 'Makkah'));
                        $inDate = $h->check_in ? \Carbon\Carbon::parse($h->check_in)->format('d M, Y') : '—';
                        $outDate = $h->check_out ? \Carbon\Carbon::parse($h->check_out)->format('d M, Y') : '—';
                        $meal = $h->meal_plan ?? ($h->package_a_food_package ?? ($h->food_package ?? 'Room Only'));
                    @endphp
                    <tr>
                        <td><strong>{{ $loc }}</strong></td>
                        <td>
                            <strong>{{ $hName }}</strong>
                            @if(!empty($h->room_number))
                                <span style="font-size:8px; background:#f1f5f9; padding:1px 4px; border:1px solid #cbd5e1;">Room {{ $h->room_number }}</span>
                            @endif
                        </td>
                        <td>{{ $inDate }}</td>
                        <td>{{ $outDate }}</td>
                        <td>{{ $meal }}</td>
                        <td>{{ ucfirst($h->allotment_basis ?? 'Room') }} ({{ ucfirst($h->room_type ?? 'Quad') }})</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- 3. FINANCIAL CHARGES & BREAKDOWN -->
    <div class="section-title">Financial Costing & Ledger Summary</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Item Description</th>
                <th class="text-center" style="width: 80px;">Rate</th>
                <th class="text-center" style="width: 60px;">Qty</th>
                <th class="text-right" style="width: 100px;">Amount (PKR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ $fullPkgName }}</strong>
                    <div style="font-size: 8px; color: #64748b;">Standard Package Inclusions, Accommodation & Services</div>
                </td>
                <td class="text-center">{{ number_format($booking->package_cost, 0) }}</td>
                <td class="text-center">{{ $booking->no_of_pax }} Pax</td>
                <td class="text-right fw-bold">{{ number_format($booking->package_cost * $booking->no_of_pax, 0) }}</td>
            </tr>

            @if($booking->visa_charges > 0)
                <tr>
                    <td><strong>Visa Processing & Ministry Fees</strong></td>
                    <td class="text-center">—</td>
                    <td class="text-center">{{ $booking->no_of_pax }} Pax</td>
                    <td class="text-right">{{ number_format($booking->visa_charges, 0) }}</td>
                </tr>
            @endif

            @if($booking->flight_charges > 0)
                <tr>
                    <td><strong>International / Domestic Flight Airfares</strong></td>
                    <td class="text-center">—</td>
                    <td class="text-center">{{ $booking->no_of_pax }} Pax</td>
                    <td class="text-right">{{ number_format($booking->flight_charges, 0) }}</td>
                </tr>
            @endif

            @if($booking->qurbani_charges > 0)
                <tr>
                    <td><strong>Hajj Qurbani (Adahi / Nusuk)</strong></td>
                    <td class="text-center">—</td>
                    <td class="text-center">{{ $booking->qurbani_qty ?? 1 }}</td>
                    <td class="text-right">{{ number_format($booking->qurbani_charges, 0) }}</td>
                </tr>
            @endif

            @if($booking->other_charges > 0)
                <tr>
                    <td><strong>Additional Services & Supplements</strong></td>
                    <td class="text-center">—</td>
                    <td class="text-center">—</td>
                    <td class="text-right">{{ number_format($booking->other_charges, 0) }}</td>
                </tr>
            @endif

            @if(($booking->discount ?? 0) > 0)
                <tr style="color: #991b1b;">
                    <td><strong>Special Package Discount / Rebate</strong></td>
                    <td class="text-center">—</td>
                    <td class="text-center">—</td>
                    <td class="text-right">- {{ number_format($booking->discount, 0) }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- LEDGER TOTALS TABLE -->
    <table style="width: 100%;">
        <tr>
            <td style="width: 55%; vertical-align: top; font-size: 8.5px; color: #64748b; padding-right: 15px;">
                <div style="font-weight: bold; color: #091322; margin-bottom: 3px;">Payment Terms & Notes:</div>
                <div>1. Remit payments only to official designated bank accounts of Baba Hajj Group.</div>
                <div>2. All bookings are subject to final Nusuk Masar quota verification & Ministry of Hajj approvals.</div>
            </td>
            <td style="width: 45%; vertical-align: top;">
                <table class="ledger-table">
                    <tr>
                        <td>Gross Subtotal:</td>
                        <td>PKR {{ number_format($booking->total_amount + ($booking->discount ?? 0), 0) }}</td>
                    </tr>
                    @if(($booking->discount ?? 0) > 0)
                        <tr style="color: #991b1b;">
                            <td>Discount:</td>
                            <td>- PKR {{ number_format($booking->discount, 0) }}</td>
                        </tr>
                    @endif
                    <tr class="ledger-total">
                        <td>Net Total Amount:</td>
                        <td>PKR {{ number_format($booking->total_amount, 0) }}</td>
                    </tr>
                    <tr style="color: #047857;">
                        <td>Total Cleared:</td>
                        <td>PKR {{ number_format($booking->total_received, 0) }}</td>
                    </tr>
                    <tr class="ledger-balance {{ $balance <= 0 ? 'settled' : '' }}">
                        <td>Balance Due:</td>
                        <td>PKR {{ number_format($balance, 0) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- SIGNATURES -->
    <table class="sign-table">
        <tr>
            <td class="sign-cell">
                <div class="sign-line">Client / Representative Signature</div>
            </td>
            <td class="sign-cell">
                <div class="sign-line">Authorized Signatory & Stamp</div>
            </td>
        </tr>
    </table>

</body>
</html>
