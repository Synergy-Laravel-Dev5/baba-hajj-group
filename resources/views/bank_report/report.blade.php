@extends('layout.master')
@section('title', 'Client Invoices Bank Report')
@section('content')
<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            {{-- Top Action Header --}}
            <div class="py-3 d-flex flex-wrap align-items-center justify-content-between gap-2 border-bottom mb-3 d-print-none">
                <div>
                    <h4 class="fs-18 fw-bold m-0 text-dark">
                        <i class="mdi mdi-bank-check text-primary me-1"></i> Client Invoices Bank Report
                    </h4>
                    <span class="text-muted fs-12">
                        Bank settlement report showing amount after discount, paid amount, and remaining balance (baki) per client & bank.
                    </span>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('bank-report.filter') }}" class="btn btn-outline-primary btn-sm">
                        <i class="mdi mdi-filter-variant me-1"></i> Modify Filter
                    </a>
                    <a href="{{ route('bank-report.pdf', request()->query()) }}" class="btn btn-outline-danger btn-sm" target="_blank">
                        <i class="mdi mdi-file-pdf-box me-1"></i> Download PDF
                    </a>
                    <button type="button" class="btn btn-outline-dark btn-sm" onclick="window.print()">
                        <i class="mdi mdi-printer me-1"></i> Print Report
                    </button>
                    <a href="{{ route('invoice.index') }}" class="btn btn-secondary btn-sm">
                        <i class="mdi mdi-receipt me-1"></i> Invoices List
                    </a>
                </div>
            </div>

            {{-- Summary Cards (KPIs) --}}
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm mb-0">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-11 text-uppercase fw-bold">Total Invoices</span>
                                <h4 class="my-1 fw-bold text-dark">{{ $totalCount }}</h4>
                                <span class="text-muted fs-11">Client {{ Str::plural('invoice', $totalCount) }}</span>
                            </div>
                            <div class="avatar-sm bg-soft-primary rounded d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-receipt-text-outline fs-24 text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm mb-0">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-11 text-uppercase fw-bold">Total Invoiced (After Discount)</span>
                                <h4 class="my-1 fw-bold text-primary">PKR {{ number_format($totalInvoiced, 2) }}</h4>
                                <span class="text-muted fs-11">Total billable to clients</span>
                            </div>
                            <div class="avatar-sm bg-soft-info rounded d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-currency-usd fs-24 text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm mb-0">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-11 text-uppercase fw-bold">Total Paid (Received)</span>
                                <h4 class="my-1 fw-bold text-success">PKR {{ number_format($totalPaid, 2) }}</h4>
                                <span class="text-success fs-11">
                                    {{ $totalInvoiced > 0 ? number_format(($totalPaid / $totalInvoiced) * 100, 1) : 0 }}% Collected
                                </span>
                            </div>
                            <div class="avatar-sm bg-soft-success rounded d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-check-circle-outline fs-24 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm mb-0">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-11 text-uppercase fw-bold">Remaining Balance (Baki)</span>
                                <h4 class="my-1 fw-bold text-danger">PKR {{ number_format($totalBalance, 2) }}</h4>
                                <span class="text-danger fs-11">Outstanding receivables</span>
                            </div>
                            <div class="avatar-sm bg-soft-danger rounded d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-clock-alert-outline fs-24 text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Active Filter Ribbon --}}
            <div class="alert alert-light border shadow-sm p-2 mb-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex flex-wrap align-items-center gap-3 fs-13">
                    <div>
                        <strong class="text-dark"><i class="mdi mdi-bank text-primary me-1"></i>Bank:</strong> 
                        <span class="text-secondary">{{ $selectedBank ? $selectedBank->name . ' (' . ($selectedBank->account_number ?: 'N/A') . ')' : 'All Banks' }}</span>
                    </div>
                    <div>
                        <strong class="text-dark"><i class="mdi mdi-account text-info me-1"></i>Client:</strong> 
                        <span class="text-secondary">{{ $selectedClient ? $selectedClient->name : 'All Clients' }}</span>
                    </div>
                    <div>
                        <strong class="text-dark"><i class="mdi mdi-check-circle text-success me-1"></i>Status:</strong> 
                        <span class="text-secondary text-capitalize">{{ $status ?: 'All' }}</span>
                    </div>
                    <div>
                        <strong class="text-dark"><i class="mdi mdi-calendar-range text-muted me-1"></i>Date:</strong> 
                        <span class="text-secondary">
                            {{ $fromDate ? \Carbon\Carbon::parse($fromDate)->format('d M Y') : 'Start' }} &rarr; {{ $toDate ? \Carbon\Carbon::parse($toDate)->format('d M Y') : 'Present' }}
                        </span>
                    </div>
                    @if($search)
                    <div>
                        <strong class="text-dark"><i class="mdi mdi-magnify text-secondary me-1"></i>Search:</strong> 
                        <span class="badge bg-secondary">{{ $search }}</span>
                    </div>
                    @endif
                </div>
                <div>
                    <a href="{{ route('bank-report.report') }}" class="btn btn-link btn-sm text-decoration-none p-0 text-muted fs-12">
                        <i class="mdi mdi-close-circle-outline me-1"></i>Reset Filter
                    </a>
                </div>
            </div>

            {{-- Bank-wise Breakdown Matrix --}}
            @if(count($bankBreakdown) > 0)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light py-2 border-bottom d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 fw-semibold text-primary fs-14">
                            <i class="mdi mdi-chart-box-outline me-1"></i> Bank-wise Invoices & Collection Breakdown
                        </h5>
                        <span class="badge bg-soft-primary text-primary px-2 py-1 fs-11">
                            {{ count($bankBreakdown) }} {{ Str::plural('Bank', count($bankBreakdown)) }}
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle mb-0">
                                <thead class="table-light fs-12">
                                    <tr>
                                        <th>Bank Account</th>
                                        <th class="text-center" style="width: 100px;">Invoices</th>
                                        <th class="text-end" style="width: 180px;">Total (After Discount)</th>
                                        <th class="text-end" style="width: 180px;">Paid Received</th>
                                        <th class="text-end" style="width: 180px;">Remaining Balance (Baki)</th>
                                        <th class="text-center" style="width: 120px;">Collected %</th>
                                    </tr>
                                </thead>
                                <tbody class="fs-12">
                                    @foreach($bankBreakdown as $row)
                                        @php
                                            $pct = $row['total_invoiced'] > 0 ? round(($row['total_paid'] / $row['total_invoiced']) * 100, 1) : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong class="text-dark"><i class="mdi mdi-bank text-info me-1"></i>{{ $row['bank']->name }}</strong>
                                                @if(!empty($row['bank']->account_number) && $row['bank']->account_number !== 'N/A')
                                                    <span class="text-muted fs-11">({{ $row['bank']->account_number }})</span>
                                                @endif
                                                @if(!empty($row['bank']->branch) && $row['bank']->branch !== 'No Bank Linked')
                                                    <small class="text-muted d-block fs-11">{{ $row['bank']->branch }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center fw-semibold text-secondary">
                                                {{ $row['count'] }}
                                            </td>
                                            <td class="text-end fw-bold text-primary">
                                                PKR {{ number_format($row['total_invoiced'], 2) }}
                                            </td>
                                            <td class="text-end fw-bold text-success">
                                                PKR {{ number_format($row['total_paid'], 2) }}
                                            </td>
                                            <td class="text-end fw-bold text-danger">
                                                PKR {{ number_format($row['total_balance'], 2) }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $pct >= 100 ? 'bg-success' : ($pct > 0 ? 'bg-warning text-dark' : 'bg-danger') }} px-2 py-1 fs-11">
                                                    {{ $pct }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light fw-bold fs-12">
                                    <tr>
                                        <td>Grand Total:</td>
                                        <td class="text-center">{{ $totalCount }}</td>
                                        <td class="text-end text-primary">PKR {{ number_format($totalInvoiced, 2) }}</td>
                                        <td class="text-end text-success">PKR {{ number_format($totalPaid, 2) }}</td>
                                        <td class="text-end text-danger">PKR {{ number_format($totalBalance, 2) }}</td>
                                        <td class="text-center">
                                            {{ $totalInvoiced > 0 ? number_format(($totalPaid / $totalInvoiced) * 100, 1) : 0 }}%
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ── DETAILED CLIENT INVOICES TABLE ── --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="card-title m-0 fw-semibold text-dark">
                            <i class="mdi mdi-receipt-text-check me-1 text-primary"></i> Client Invoices Bank Records
                        </h5>
                        <small class="text-muted fs-11">
                            Individual client invoices with after discount amount, received payments, and remaining balance (baki).
                        </small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary px-3 py-1 fs-12">{{ $invoices->count() }} Invoices</span>
                        <span class="badge bg-soft-success text-success border border-success px-3 py-1 fs-12 fw-bold">
                            Total Paid: PKR {{ number_format($totalPaid, 2) }}
                        </span>
                        <span class="badge bg-soft-danger text-danger border border-danger px-3 py-1 fs-12 fw-bold">
                            Remaining Baki: PKR {{ number_format($totalBalance, 2) }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 45px;" class="text-center">#</th>
                                    <th style="width: 105px;">Invoice Date</th>
                                    <th style="width: 130px;">Invoice #</th>
                                    <th>Client / Passenger Details</th>
                                    <th>Bank Account</th>
                                    <th class="text-end" style="width: 155px;">After Discount (PKR)</th>
                                    <th class="text-end" style="width: 145px;">Paid Amount (PKR)</th>
                                    <th class="text-end" style="width: 155px;">Remaining Baki (PKR)</th>
                                    <th class="text-center" style="width: 105px;">Status</th>
                                    <th class="text-center d-print-none" style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $inv)
                                    @php
                                        // Bank details: direct or via transactions
                                        $displayBank = $inv->bank;
                                        if (!$displayBank && $inv->expenseTransactions->isNotEmpty()) {
                                            $displayBank = $inv->expenseTransactions->first()->bank;
                                        }
                                    @endphp
                                    <tr>
                                        <td class="text-center fw-semibold text-muted">{{ $loop->iteration }}</td>
                                        <td class="fw-semibold text-dark">
                                            {{ $inv->invoice_date ? $inv->invoice_date->format('d M, Y') : '—' }}
                                        </td>
                                        <td>
                                            <a href="{{ route('invoice.show', $inv->id) }}" class="fw-bold text-primary text-decoration-none">
                                                <i class="mdi mdi-receipt me-1"></i>{{ $inv->invoice_no }}
                                            </a>
                                            @if($inv->due_date)
                                                <small class="text-muted d-block fs-11">Due: {{ $inv->due_date->format('d M Y') }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">
                                                <i class="mdi mdi-account-circle-outline text-primary me-1"></i>
                                                {{ $inv->client->name ?? 'Direct Passenger' }}
                                            </div>
                                            @if(!empty($inv->client->phone))
                                                <small class="text-muted fs-11 d-block"><i class="mdi mdi-phone me-1"></i>{{ $inv->client->phone }}</small>
                                            @endif
                                            @if(!empty($inv->bill_to) && $inv->bill_to !== ($inv->client->name ?? ''))
                                                <small class="text-secondary fs-11 d-block text-truncate" style="max-width: 200px;">
                                                    {{ Str::limit($inv->bill_to, 40) }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($displayBank)
                                                <strong class="text-dark"><i class="mdi mdi-bank text-info me-1"></i>{{ $displayBank->name }}</strong>
                                                @if(!empty($displayBank->account_number))
                                                    <small class="text-muted d-block fs-11">{{ $displayBank->account_number }}</small>
                                                @endif
                                                @if(!empty($displayBank->branch))
                                                    <small class="text-muted d-block fs-10">{{ $displayBank->branch }}</small>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary">Cash / No Bank</span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold text-primary fs-14">
                                            PKR {{ number_format($inv->total_amount, 2) }}
                                            @if($inv->discount > 0)
                                                <small class="text-muted d-block fs-10 font-normal">
                                                    (Sub: {{ number_format($inv->subtotal, 0) }} - Disc: {{ number_format($inv->discount, 0) }})
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold text-success fs-14">
                                            PKR {{ number_format($inv->payment_made, 2) }}
                                        </td>
                                        <td class="text-end fw-bold fs-14 {{ $inv->balance_due > 0 ? 'text-danger' : 'text-muted' }}">
                                            PKR {{ number_format($inv->balance_due, 2) }}
                                        </td>
                                        <td class="text-center">
                                            @if($inv->status === 'paid')
                                                <span class="badge bg-success-subtle text-success border border-success px-2 py-1 fs-11 text-uppercase">
                                                    <i class="mdi mdi-check-circle me-1"></i>Paid
                                                </span>
                                            @elseif($inv->status === 'partial')
                                                <span class="badge bg-warning-subtle text-warning border border-warning px-2 py-1 fs-11 text-uppercase">
                                                    <i class="mdi mdi-clock-outline me-1"></i>Partial
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1 fs-11 text-uppercase">
                                                    <i class="mdi mdi-alert-circle me-1"></i>Unpaid
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center d-print-none">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('invoice.show', $inv->id) }}" class="btn btn-outline-primary btn-sm p-1" title="View Details">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="{{ route('invoice.pdf', $inv->id) }}" class="btn btn-outline-danger btn-sm p-1" title="PDF" target="_blank">
                                                    <i class="mdi mdi-file-pdf-box"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5 text-muted">
                                            <i class="mdi mdi-receipt-text-remove fs-36 text-muted d-block mb-2"></i>
                                            <h5>No client invoices found</h5>
                                            <p class="fs-13 text-muted mb-0">No client invoice matches your selected filters.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold fs-13">
                                    <td colspan="5" class="text-end py-2">Grand Total:</td>
                                    <td class="text-end py-2 text-primary fs-14">PKR {{ number_format($totalInvoiced, 2) }}</td>
                                    <td class="text-end py-2 text-success fs-14">PKR {{ number_format($totalPaid, 2) }}</td>
                                    <td class="text-end py-2 text-danger fs-14">PKR {{ number_format($totalBalance, 2) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
