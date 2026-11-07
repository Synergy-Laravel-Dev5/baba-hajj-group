@extends('layout.master')
@section('title', 'Invoice ' . $invoice->invoice_no)
@section('content')

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Top Action Toolbar (Hidden in Print) -->
            <div class="py-3 d-flex align-items-center justify-content-between flex-wrap gap-2 d-print-none">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h4 class="fs-18 fw-semibold m-0">Invoice {{ $invoice->invoice_no }}</h4>
                        @php
                            $badgeClass = [
                                'paid'    => 'bg-success',
                                'partial' => 'bg-warning text-dark',
                                'unpaid'  => 'bg-danger',
                                'draft'   => 'bg-secondary',
                            ][$invoice->status] ?? 'bg-secondary';
                        @endphp
                        <span class="badge {{ $badgeClass }} px-2 py-1">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </div>
                    <p class="text-muted fs-12 mb-0">Generated on {{ formatDateTime($invoice->created_at) }}</p>
                </div>

                <div class="d-flex gap-2">
                    @can('invoice_payment')
                        @if($invoice->balance_due > 0)
                            <button type="button" class="btn btn-success btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                <i class="mdi mdi-cash-plus me-1"></i> + Record Payment
                            </button>
                        @endif
                    @endcan
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()">
                        <i class="mdi mdi-printer me-1"></i> Print Invoice
                    </button>
                    @can('invoice_view')
                    <a href="{{ route('invoice.pdf', $invoice->id) }}" target="_blank" class="btn btn-danger btn-sm">
                        <i class="mdi mdi-file-pdf-box me-1"></i> Download PDF
                    </a>
                    @endcan
                    @can('invoice_edit')
                    <a href="{{ route('invoice.edit', $invoice->id) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="mdi mdi-pencil me-1"></i> Edit
                    </a>
                    @endcan
                    <a href="{{ route('invoice.index') }}" class="btn btn-secondary btn-sm">
                        &larr; All Invoices
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show d-print-none">
                    <i class="mdi mdi-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show d-print-none">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Invoice Paper Container -->
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">
                    <div class="card shadow-sm border-0 invoice-paper mb-5">
                        <div class="card-body p-4 p-md-5">

                            <!-- Header: Company & Invoice Info -->
                            <div class="row mb-4 align-items-start">
                                <div class="col-sm-7">
                                    <div class="mb-3">
                                        @if($company && $company->logo && file_exists(public_path('assets/images/logo/' . $company->logo)))
                                            <img src="{{ asset('assets/images/logo/' . $company->logo) }}" alt="Logo" style="max-height: 60px; width: auto;" class="img-fluid">
                                        @elseif(file_exists(public_path('assets/images/logo/logo.png')))
                                            <img src="{{ asset('assets/images/logo/logo.png') }}" alt="Logo" style="max-height: 60px; width: auto;" class="img-fluid">
                                        @else
                                            <h3 class="fw-bold text-primary mb-0">BABA HAJJ GROUP</h3>
                                        @endif
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1">{{ $company ? ($company->company_name ?? $company->name) : 'BABA HAJJ GROUP' }}</h5>
                                    @if($company && $company->addresses && $company->addresses->count() > 0)
                                        <p class="text-muted mb-0 fs-13" style="line-height: 1.5;">
                                            {{ $company->addresses->first()->address_line1 ?? '' }}<br>
                                            {{ $company->addresses->first()->city ?? '' }}, {{ $company->addresses->first()->country ?? 'Pakistan' }}
                                        </p>
                                    @endif
                                </div>

                                <div class="col-sm-5 text-sm-end mt-3 mt-sm-0">
                                    <h2 class="text-uppercase fw-bold text-dark mb-1" style="letter-spacing: 1px;">Invoice</h2>
                                    <h5 class="text-primary fw-bold mb-3"># {{ $invoice->invoice_no }}</h5>

                                    <div class="bg-light p-2 rounded mb-3 d-inline-block text-sm-end border" style="min-width: 200px;">
                                        <small class="text-muted text-uppercase fw-bold d-block fs-11">Balance Due</small>
                                        <span class="fs-18 fw-bold text-danger">
                                            {{ $invoice->currency }} {{ number_format($invoice->balance_due, 2) }}
                                        </span>
                                    </div>

                                    <table class="table table-sm table-borderless mb-0 fs-13 ms-sm-auto" style="width: auto;">
                                        <tr>
                                            <td class="text-muted pe-2 py-1 text-sm-end">Invoice Date :</td>
                                            <td class="fw-semibold py-1 text-sm-end">{{ formatDate($invoice->invoice_date) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted pe-2 py-1 text-sm-end">Due Date :</td>
                                            <td class="fw-semibold py-1 text-sm-end">{{ formatDate($invoice->due_date) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <hr class="my-4 text-muted">

                            <!-- Bill To Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-muted text-uppercase fw-bold fs-12 mb-1">Bill To (Client / Passenger Details)</h6>
                                    <div class="fs-14 fw-semibold text-dark" style="white-space: pre-line; line-height: 1.6;">
                                        {{ $invoice->bill_to }}
                                    </div>
                                </div>
                            </div>

                            <!-- Line Items Table -->
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered align-top mb-0" style="border-color: #dee2e6;">
                                    <thead style="background-color: #f8f9fa;">
                                        <tr>
                                            <th style="width: 40px;" class="text-center">#</th>
                                            <th>Item & Description</th>
                                            <th style="width: 90px;" class="text-center">Qty</th>
                                            <th style="width: 140px;" class="text-end">Rate</th>
                                            <th style="width: 150px;" class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoice->items as $item)
                                            <tr>
                                                <td class="text-center fw-semibold text-muted">{{ $loop->iteration }}</td>
                                                <td>
                                                    @if($item->item_name)
                                                        <h6 class="fw-bold text-dark mb-2">{{ $item->item_name }}</h6>
                                                    @endif
                                                    @if($item->description)
                                                        <div class="fs-13 text-secondary" style="white-space: pre-line; line-height: 1.6;">
                                                            {{ $item->description }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="text-center fw-semibold">{{ number_format($item->qty, 2) }}</td>
                                                <td class="text-end fw-semibold">{{ number_format($item->rate, 2) }}</td>
                                                <td class="text-end fw-bold text-dark">{{ number_format($item->amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Totals Box -->
                            <div class="row justify-content-end mb-4">
                                <div class="col-sm-6 col-md-5">
                                    <table class="table table-sm table-borderless mb-0 fs-14">
                                        <tr class="border-bottom">
                                            <td class="text-muted py-2">Sub Total</td>
                                            <td class="text-end fw-semibold py-2">{{ number_format($invoice->subtotal, 2) }}</td>
                                        </tr>
                                        @if(($invoice->discount ?? 0) > 0)
                                        <tr class="border-bottom">
                                            <td class="text-danger py-2">Discount (-)</td>
                                            <td class="text-end text-danger fw-semibold py-2">- {{ number_format($invoice->discount, 2) }}</td>
                                        </tr>
                                        @endif
                                        <tr class="border-bottom">
                                            <td class="fw-bold py-2">Total {{ $invoice->currency }}</td>
                                            <td class="text-end fw-bold py-2">{{ number_format($invoice->total_amount, 2) }}</td>
                                        </tr>
                                        <tr class="border-bottom">
                                            <td class="text-muted py-2">Payment Made (-)</td>
                                            <td class="text-end text-success fw-semibold py-2">{{ number_format($invoice->payment_made, 2) }}</td>
                                        </tr>
                                        <tr class="bg-light">
                                            <td class="fw-bold py-2 fs-15">Balance Due</td>
                                            <td class="text-end fw-bold py-2 fs-16 text-danger">
                                                {{ $invoice->currency }} {{ number_format($invoice->balance_due, 2) }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <hr class="my-4 text-muted">

                            <!-- Payment History Log -->
                            @if(isset($payments) && $payments->count() > 0)
                                <div class="mb-4 d-print-none">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="text-uppercase fw-bold fs-12 text-muted mb-0">
                                            <i class="mdi mdi-cash-check text-success me-1"></i> Recorded Client Payment Receipts
                                        </h6>
                                        <span class="badge bg-success-subtle text-success fs-11">
                                            Total Paid: {{ $invoice->currency }} {{ number_format($invoice->payment_made, 2) }}
                                        </span>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered align-middle fs-13 mb-0" style="border-color: #dee2e6;">
                                            <thead style="background-color: #f8f9fa;">
                                                <tr>
                                                    <th style="width: 40px;" class="text-center">#</th>
                                                    <th style="width: 110px;">Payment Date</th>
                                                    <th style="width: 130px;">Amount Received</th>
                                                    <th style="width: 110px;">Method</th>
                                                    <th>Deposited In (Bank / Cash)</th>
                                                    <th>Reference #</th>
                                                    <th>Notes / Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($payments as $p)
                                                    <tr>
                                                        <td class="text-center text-muted fw-semibold">{{ $loop->iteration }}</td>
                                                        <td class="fw-semibold text-dark">{{ formatDate($p->date) }}</td>
                                                        <td class="fw-bold text-success">
                                                            {{ $p->currency }} {{ number_format($p->amount, 2) }}
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-soft-primary text-primary text-uppercase fs-11">
                                                                {{ $p->payment_type ?? 'Bank' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($p->bank)
                                                                <strong class="text-dark">{{ $p->bank->name }}</strong>
                                                                @if($p->bank->account_number)
                                                                    <div class="text-muted fs-11">{{ $p->bank->account_name ? $p->bank->account_name . ' — ' : '' }}{{ $p->bank->account_number }}</div>
                                                                @endif
                                                            @else
                                                                <span class="badge bg-soft-secondary text-secondary">Cash Payment</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($p->reference_no)
                                                                <code class="text-dark bg-light px-2 py-0 rounded">{{ $p->reference_no }}</code>
                                                            @else
                                                                <span class="text-muted">—</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="text-muted fs-12">{{ $p->description ?: '—' }}</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <!-- Bank Details Box (If Bank Linked) -->
                            @if($invoice->bank)
                                <div class="card bg-light border-0 mb-4">
                                    <div class="card-body p-3">
                                        <h6 class="text-uppercase fw-bold fs-12 text-primary mb-2">
                                            <i class="mdi mdi-bank me-1"></i> Official Bank Payment Details
                                        </h6>
                                        <div class="row fs-13">
                                            <div class="col-sm-6 mb-1">
                                                <span class="text-muted">Bank Name:</span>
                                                <strong class="text-dark ms-1">{{ $invoice->bank->name }}</strong>
                                            </div>
                                            <div class="col-sm-6 mb-1">
                                                <span class="text-muted">Account Title:</span>
                                                <strong class="text-dark ms-1">{{ $invoice->bank->account_name ?: '—' }}</strong>
                                            </div>
                                            <div class="col-sm-6 mb-1">
                                                <span class="text-muted">Account / IBAN:</span>
                                                <strong class="text-dark ms-1">{{ $invoice->bank->account_number ?: '—' }}</strong>
                                            </div>
                                            <div class="col-sm-6 mb-1">
                                                <span class="text-muted">Branch:</span>
                                                <strong class="text-dark ms-1">{{ $invoice->bank->branch ?: '—' }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Notes & Terms -->
                            <div class="row fs-12 text-muted">
                                @if($invoice->notes)
                                    <div class="col-12 mb-3">
                                        <strong class="text-dark d-block mb-1">Notes:</strong>
                                        <p class="mb-0" style="white-space: pre-line;">{{ $invoice->notes }}</p>
                                    </div>
                                @endif
                                @if($invoice->terms_conditions)
                                    <div class="col-12">
                                        <strong class="text-dark d-block mb-1">Terms & Conditions:</strong>
                                        <p class="mb-0" style="white-space: pre-line;">{{ $invoice->terms_conditions }}</p>
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Record Payment Modal -->
@if($invoice->balance_due > 0)
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title fw-semibold text-white" id="paymentModalLabel">
                    <i class="mdi mdi-cash-plus me-1"></i> Record Client Payment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('invoice.payment', $invoice->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-soft-success py-2 px-3 mb-3 d-flex justify-content-between align-items-center">
                        <span class="fs-13 fw-semibold">Current Balance Due:</span>
                        <strong class="fs-15 text-danger">{{ $invoice->currency }} {{ number_format($invoice->balance_due, 2) }}</strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Amount (<span class="text-primary">{{ $invoice->currency }}</span>) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" max="{{ $invoice->balance_due }}" name="payment_amount" class="form-control form-control-lg fw-bold text-success" value="{{ $invoice->balance_due }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deposited Into Bank Account</label>
                        <select name="bank_id" class="form-select">
                            <option value="">-- Cash in Hand / No Bank --</option>
                            @foreach($banks as $b)
                                <option value="{{ $b->id }}" {{ ($invoice->bank_id == $b->id) ? 'selected' : '' }}>
                                    {{ $b->name }}{{ $b->account_name ? ' — ' . $b->account_name : '' }}{{ $b->account_number ? ' (' . $b->account_number . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted fs-11">Selecting a bank will automatically reflect this deposit in the Bank's Live Balance & Statement.</small>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Method</label>
                            <select name="payment_type" class="form-select">
                                <option value="bank_transfer" selected>Bank Transfer / Online</option>
                                <option value="cash">Cash</option>
                                <option value="cheque">Cheque</option>
                                <option value="online">Online / Card</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Reference / Cheque #</label>
                            <input type="text" name="reference_no" class="form-control" placeholder="e.g. TXN-12345" value="{{ $invoice->invoice_no }}">
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">Payment Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-semibold px-4">
                        <i class="mdi mdi-check-circle me-1"></i> Confirm & Save Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection
