<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Client Invoices Bank Report</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10px; color: #333; margin: 15px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #1a56db; padding-bottom: 8px; }
        .header h2 { margin: 0; color: #1a56db; font-size: 18px; text-transform: uppercase; letter-spacing: 0.5px; }
        .header h3 { margin: 3px 0 0; color: #333; font-size: 13px; font-weight: normal; }
        .header p { margin: 3px 0 0; color: #555; font-size: 9.5px; }
        
        .kpi-container { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .kpi-box { border: 1px solid #d0d7de; padding: 7px 5px; background: #f8fafc; text-align: center; }
        .kpi-label { font-size: 8.5px; text-transform: uppercase; color: #64748b; font-weight: bold; }
        .kpi-value { font-size: 13px; font-weight: bold; margin-top: 3px; }

        table.data-table { width: 100%; border-collapse: collapse; margin-top: 5px; margin-bottom: 12px; }
        table.data-table th, table.data-table td { border: 1px solid #d0d7de; padding: 5px 6px; text-align: left; font-size: 9px; }
        table.data-table th { background-color: #f1f5f9; font-size: 8.5px; text-transform: uppercase; color: #334155; font-weight: bold; }
        
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .total-row { font-weight: bold; background-color: #f8fafc; }
        .badge-green { color: #15803d; font-weight: bold; }
        .badge-blue { color: #1d4ed8; font-weight: bold; }
        .badge-red { color: #b91c1c; font-weight: bold; }
        .badge-orange { color: #b45309; font-weight: bold; }

        .status-badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 3px;
        }
        .status-paid { background-color: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .status-partial { background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .status-unpaid { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 8px; color: #888; text-align: right; border-top: 1px solid #ddd; padding-top: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Baba Hajj & Umrah Group</h2>
        <h3>Client Invoices Bank Report</h3>
        <p>
            @if(isset($selectedBank) && $selectedBank)
                <strong>Bank:</strong> {{ $selectedBank->name }} ({{ $selectedBank->account_number ?: 'N/A' }}) | 
            @endif
            @if(isset($selectedClient) && $selectedClient)
                <strong>Client:</strong> {{ $selectedClient->name }} | 
            @endif
            <strong>Period:</strong> {{ $fromDate ? \Carbon\Carbon::parse($fromDate)->format('d M Y') : 'Start' }} to {{ $toDate ? \Carbon\Carbon::parse($toDate)->format('d M Y') : 'Present' }}
            | <strong>Generated:</strong> {{ date('d M Y, h:i A') }}
        </p>
    </div>

    {{-- KPI Summary Boxes --}}
    <table class="kpi-container">
        <tr>
            <td class="kpi-box" style="width: 25%;">
                <div class="kpi-label">Total Invoices</div>
                <div class="kpi-value badge-blue">{{ $totalCount ?? 0 }}</div>
                <div style="font-size: 8px; color: #64748b;">Client Invoices</div>
            </td>
            <td class="kpi-box" style="width: 25%;">
                <div class="kpi-label">Total Invoiced (After Discount)</div>
                <div class="kpi-value badge-blue">PKR {{ number_format($totalInvoiced ?? 0, 2) }}</div>
                <div style="font-size: 8px; color: #64748b;">Total Billable</div>
            </td>
            <td class="kpi-box" style="width: 25%;">
                <div class="kpi-label">Total Paid (Received)</div>
                <div class="kpi-value badge-green">PKR {{ number_format($totalPaid ?? 0, 2) }}</div>
                <div style="font-size: 8px; color: #15803d;">
                    {{ ($totalInvoiced ?? 0) > 0 ? number_format((($totalPaid ?? 0) / $totalInvoiced) * 100, 1) : 0 }}% Received
                </div>
            </td>
            <td class="kpi-box" style="width: 25%;">
                <div class="kpi-label">Remaining Balance (Baki)</div>
                <div class="kpi-value badge-red">PKR {{ number_format($totalBalance ?? 0, 2) }}</div>
                <div style="font-size: 8px; color: #b91c1c;">Outstanding Receivables</div>
            </td>
        </tr>
    </table>

    {{-- Detailed Invoices Table --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20px;" class="text-center">#</th>
                <th style="width: 65px;">Date</th>
                <th style="width: 75px;">Invoice #</th>
                <th>Client / Passenger</th>
                <th>Bank Account</th>
                <th class="text-end" style="width: 90px;">After Disc. (PKR)</th>
                <th class="text-end" style="width: 85px;">Paid (PKR)</th>
                <th class="text-end" style="width: 90px;">Baki Due (PKR)</th>
                <th class="text-center" style="width: 55px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $inv)
                @php
                    $displayBank = $inv->bank;
                    if (!$displayBank && $inv->expenseTransactions && $inv->expenseTransactions->isNotEmpty()) {
                        $displayBank = $inv->expenseTransactions->first()->bank;
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $inv->invoice_date ? $inv->invoice_date->format('d M Y') : '—' }}</td>
                    <td><strong>{{ $inv->invoice_no }}</strong></td>
                    <td>
                        {{ $inv->client->name ?? 'Direct Passenger' }}
                        @if(!empty($inv->client->phone))
                            <br><span style="color: #666; font-size: 8px;">{{ $inv->client->phone }}</span>
                        @endif
                    </td>
                    <td>
                        @if($displayBank)
                            {{ $displayBank->name }}
                            @if(!empty($displayBank->account_number))
                                <br><span style="color: #666; font-size: 8px;">{{ $displayBank->account_number }}</span>
                            @endif
                        @else
                            <span style="color: #888;">Cash / No Bank</span>
                        @endif
                    </td>
                    <td class="text-end badge-blue">{{ number_format($inv->total_amount, 2) }}</td>
                    <td class="text-end badge-green">{{ number_format($inv->payment_made, 2) }}</td>
                    <td class="text-end {{ $inv->balance_due > 0 ? 'badge-red' : '' }}">{{ number_format($inv->balance_due, 2) }}</td>
                    <td class="text-center">
                        @if($inv->status === 'paid')
                            <span class="status-badge status-paid">PAID</span>
                        @elseif($inv->status === 'partial')
                            <span class="status-badge status-partial">PARTIAL</span>
                        @else
                            <span class="status-badge status-unpaid">UNPAID</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 15px; color: #777;">
                        No client invoices match the selected criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-end">Grand Total:</td>
                <td class="text-end badge-blue">PKR {{ number_format($totalInvoiced ?? 0, 2) }}</td>
                <td class="text-end badge-green">PKR {{ number_format($totalPaid ?? 0, 2) }}</td>
                <td class="text-end badge-red">PKR {{ number_format($totalBalance ?? 0, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Page generated by Baba Hajj & Umrah System &bull; Confidential
    </div>
</body>
</html>
