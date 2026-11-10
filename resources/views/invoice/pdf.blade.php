<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $invoice->invoice_no }}</title>
    <style>
        @page {
            margin: 32px 36px 36px 36px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #222222;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .header-table td {
            vertical-align: top;
        }
        .company-logo {
            max-height: 50px;
            margin-bottom: 8px;
        }
        .company-name {
            font-size: 15px;
            font-weight: bold;
            color: #1a1a1a;
            margin: 0 0 4px 0;
        }
        .company-meta {
            font-size: 10px;
            color: #555555;
            line-height: 1.4;
            margin: 0;
        }
        .invoice-title {
            font-size: 26px;
            font-weight: bold;
            color: #1a1a1a;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: right;
            margin: 0 0 2px 0;
        }
        .invoice-number {
            font-size: 13px;
            font-weight: bold;
            color: #2b5797;
            text-align: right;
            margin: 0 0 10px 0;
        }
        .balance-due-box {
            background-color: #f6f8fa;
            border: 1px solid #e1e4e8;
            padding: 6px 12px;
            text-align: right;
            margin-bottom: 10px;
            float: right;
            border-radius: 3px;
        }
        .balance-due-label {
            font-size: 9px;
            text-transform: uppercase;
            font-weight: bold;
            color: #666;
            margin-bottom: 2px;
        }
        .balance-due-amount {
            font-size: 15px;
            font-weight: bold;
            color: #d92525;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-top: 4px;
        }
        .meta-table td {
            padding: 2px 0;
        }
        .meta-table td.label {
            color: #666;
            text-align: right;
            padding-right: 8px;
        }
        .meta-table td.value {
            font-weight: bold;
            text-align: right;
            color: #222;
        }
        .bill-to-label {
            font-size: 10px;
            font-weight: bold;
            color: #777;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .bill-to-text {
            font-size: 11px;
            font-weight: bold;
            color: #111;
            white-space: pre-line;
            line-height: 1.4;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items-table th {
            background-color: #f8f9fa;
            color: #333;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 6px;
            border-top: 1px solid #d1d5db;
            border-bottom: 1px solid #d1d5db;
            text-align: left;
        }
        .items-table td {
            padding: 8px 6px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
            font-size: 10px;
        }
        .items-table td.desc-col {
            color: #222;
            white-space: pre-line;
            line-height: 1.45;
        }
        .totals-table {
            width: 45%;
            margin-left: auto;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .totals-table td {
            padding: 4px 6px;
            font-size: 10.5px;
        }
        .totals-table td.label {
            color: #555;
            text-align: left;
        }
        .totals-table td.value {
            text-align: right;
            font-weight: bold;
            color: #111;
        }
        .totals-table tr.total-row td {
            border-top: 1px solid #d1d5db;
            border-bottom: 1px solid #d1d5db;
            font-size: 11px;
            font-weight: bold;
            padding: 6px;
        }
        .totals-table tr.balance-row td {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 12px;
            color: #d92525;
            padding: 6px;
        }
        .notes-section {
            border-top: 1px solid #e8e8e8;
            padding-top: 12px;
            margin-top: 20px;
        }
        .section-heading {
            font-size: 10px;
            font-weight: bold;
            color: #777;
            text-transform: uppercase;
            margin: 0 0 4px 0;
        }
        .section-text {
            font-size: 9.5px;
            color: #444;
            margin: 0 0 10px 0;
            white-space: pre-line;
            line-height: 1.4;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>

@php
    $company = $company ?? \App\Models\Company::first();
    $logoSrc = '';
    if ($company && $company->logo && file_exists(public_path('assets/images/logo/' . $company->logo))) {
        $logoSrc = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('assets/images/logo/' . $company->logo)));
    } elseif (file_exists(public_path('assets/images/logo.png'))) {
        $logoSrc = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('assets/images/logo.png')));
    } elseif (file_exists(public_path('assets/images/logo/logo.png'))) {
        $logoSrc = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('assets/images/logo/logo.png')));
    }
@endphp

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <!-- Left: Company Details -->
            <td style="width: 58%;">
                @if($logoSrc)
                    <img src="{{ $logoSrc }}" class="company-logo" alt="Logo">
                @endif
                <h4 class="company-name">{{ $company ? ($company->company_name ?? $company->name) : 'BABA HAJJ GROUP' }}</h4>
                <p class="company-meta">
                    @if($company && $company->addresses && $company->addresses->count() > 0)
                        {{ $company->addresses->first()->address_line1 ?? '' }}<br>
                        {{ $company->addresses->first()->city ?? '' }}, {{ $company->addresses->first()->country ?? 'Pakistan' }}
                    @endif
                </p>
            </td>

            <!-- Right: Invoice Metadata & Balance Due -->
            <td style="width: 42%; text-align: right;">
                <div class="invoice-title">Invoice</div>
                <div class="invoice-number"># {{ $invoice->invoice_no }}</div>

                <div class="balance-due-box clearfix">
                    <div class="balance-due-label">Balance Due</div>
                    <div class="balance-due-amount">
                        {{ $invoice->currency }} {{ number_format($invoice->balance_due, 2) }}
                    </div>
                </div>

                <div style="clear: both;"></div>

                <table class="meta-table">
                    <tr>
                        <td class="label">Invoice Date :</td>
                        <td class="value">{{ formatDate($invoice->invoice_date) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Due Date :</td>
                        <td class="value">{{ formatDate($invoice->due_date) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Bill To & Bank Details Section -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; border-top: 1px solid #e8e8e8;">
        <tr>
            <td style="width: {{ $invoice->bank ? '58%' : '100%' }}; vertical-align: top; padding-top: 12px; padding-right: 15px;">
                <div class="bill-to-label">Bill To (Client / Passenger Details)</div>
                <div class="bill-to-text">{{ $invoice->bill_to }}</div>
            </td>
            @if($invoice->bank)
                <td style="width: 42%; vertical-align: top; padding-top: 12px;">
                    <div style="background-color: #f8f9fa; border: 1px solid #e1e4e8; border-radius: 4px; padding: 8px 10px;">
                        <div style="font-size: 9.5px; font-weight: bold; color: #2b5797; text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.5px;">
                            Official Bank Payment Details
                        </div>
                        <table style="width: 100%; border-collapse: collapse; font-size: 9.5px;">
                            <tr>
                                <td style="color: #666; width: 75px; padding: 2px 0;">Bank:</td>
                                <td style="font-weight: bold; color: #111; padding: 2px 0;">{{ $invoice->bank->name }}</td>
                            </tr>
                            @if($invoice->bank->account_name)
                                <tr>
                                    <td style="color: #666; padding: 2px 0;">Account Title:</td>
                                    <td style="font-weight: bold; color: #111; padding: 2px 0;">{{ $invoice->bank->account_name }}</td>
                                </tr>
                            @endif
                            @if($invoice->bank->account_number)
                                <tr>
                                    <td style="color: #666; padding: 2px 0;">Account/IBAN:</td>
                                    <td style="font-weight: bold; color: #2b5797; padding: 2px 0;">{{ $invoice->bank->account_number }}</td>
                                </tr>
                            @endif
                            @if($invoice->bank->branch)
                                <tr>
                                    <td style="color: #666; padding: 2px 0;">Branch:</td>
                                    <td style="color: #444; padding: 2px 0;">{{ $invoice->bank->branch }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </td>
            @endif
        </tr>
    </table>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 25px; text-align: center;">#</th>
                <th>Item & Description</th>
                <th style="width: 55px; text-align: center;">Qty</th>
                <th style="width: 95px; text-align: right;">Rate</th>
                <th style="width: 100px; text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td style="text-align: center; color: #666;">{{ $loop->iteration }}</td>
                    <td class="desc-col">
                        @if($item->item_name)
                            <strong style="color: #000; font-size: 10.5px;">{{ $item->item_name }}</strong><br>
                        @endif
                        {{ $item->description }}
                    </td>
                    <td style="text-align: center;">{{ number_format($item->qty, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($item->rate, 2) }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($item->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals Table -->
    <table class="totals-table">
        <tr>
            <td class="label">Sub Total</td>
            <td class="value">{{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        @if(($invoice->discount ?? 0) > 0)
        <tr>
            <td class="label" style="color: #d92525;">Discount (-)</td>
            <td class="value" style="color: #d92525;">- {{ number_format($invoice->discount, 2) }}</td>
        </tr>
        @endif
        <tr class="total-row">
            <td class="label" style="font-weight: bold; color: #000;">Total {{ $invoice->currency }}</td>
            <td class="value">{{ number_format($invoice->total_amount, 2) }}</td>
        </tr>
        <tr>
            <td class="label">Payment Made (-)</td>
            <td class="value" style="color: #166534;">{{ number_format($invoice->payment_made, 2) }}</td>
        </tr>
        <tr class="balance-row">
            <td class="label" style="font-weight: bold; color: #000;">Balance Due</td>
            <td class="value">{{ $invoice->currency }} {{ number_format($invoice->balance_due, 2) }}</td>
        </tr>
    </table>

    <div style="clear: both;"></div>

    <!-- Notes & Terms -->
    <div class="notes-section">
        @if($invoice->notes)
            <div class="section-heading">Notes</div>
            <p class="section-text">{{ $invoice->notes }}</p>
        @endif

        @if($invoice->terms_conditions)
            <div class="section-heading">Terms & Conditions</div>
            <p class="section-text">{{ $invoice->terms_conditions }}</p>
        @endif
    </div>

</body>
</html>
