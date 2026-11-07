@extends('layout.master')
@section('title', 'Client Invoices')
@section('content')

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h4 class="fs-18 fw-semibold m-0">Invoices</h4>
                    <p class="text-muted fs-12 mb-0">Official client invoices, payments tracking, bank deposits and outstanding balances</p>
                </div>
                <div class="d-flex gap-2">
                    @can('bank_view')
                    <a href="{{ route('bank.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="mdi mdi-bank me-1"></i> Banks
                    </a>
                    @endcan
                    @can('invoice_create')
                    <a href="{{ route('invoice.create') }}" class="btn btn-primary btn-sm">
                        <i class="mdi mdi-plus-circle me-1"></i> + Create Invoice
                    </a>
                    @endcan
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="mdi mdi-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="mdi mdi-alert-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Summary KPI Cards -->
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm mb-0">
                        <div class="card-body p-3">
                            <span class="text-muted fs-11 text-uppercase fw-bold">Total Invoices</span>
                            <h3 class="my-1 fw-bold text-dark">{{ $stats['count'] }}</h3>
                            <span class="text-muted fs-12">Issued across all clients</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm mb-0">
                        <div class="card-body p-3">
                            <span class="text-muted fs-11 text-uppercase fw-bold">Total Billed</span>
                            <h3 class="my-1 fw-bold text-primary">PKR {{ number_format($stats['total_invoiced'], 2) }}</h3>
                            <span class="text-primary fs-12">Total gross revenue</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm mb-0">
                        <div class="card-body p-3">
                            <span class="text-muted fs-11 text-uppercase fw-bold">Payments Received</span>
                            <h3 class="my-1 fw-bold text-success">PKR {{ number_format($stats['total_paid'], 2) }}</h3>
                            <span class="text-success fs-12">Collected in bank / cash</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm mb-0">
                        <div class="card-body p-3">
                            <span class="text-muted fs-11 text-uppercase fw-bold">Outstanding Balance</span>
                            <h3 class="my-1 fw-bold text-danger">PKR {{ number_format($stats['total_balance'], 2) }}</h3>
                            <span class="text-danger fs-12">Pending from clients</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Card -->
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('invoice.index') }}" class="row g-2 align-items-center">
                        <div class="col-md-3">
                            <label class="form-label fs-12 text-muted mb-1">Status</label>
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>-- All Statuses --</option>
                                <option value="unpaid" {{ $status == 'unpaid' ? 'selected' : '' }}>🔴 Unpaid</option>
                                <option value="partial" {{ $status == 'partial' ? 'selected' : '' }}>🟡 Partially Paid</option>
                                <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>🟢 Paid in Full</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fs-12 text-muted mb-1">Client</label>
                            <select name="client_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">-- All Clients --</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}" {{ $clientId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fs-12 text-muted mb-1">Search</label>
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control" placeholder="Search by INV #, Bill To, or Client..." value="{{ $search }}">
                                <button type="submit" class="btn btn-primary"><i class="mdi mdi-magnify"></i> Filter</button>
                            </div>
                        </div>

                        <div class="col-md-1 d-flex align-items-end">
                            <a href="{{ route('invoice.index') }}" class="btn btn-sm btn-outline-secondary w-100" style="margin-top: 22px;">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="width: 120px;">Invoice #</th>
                                    <th>Client / Bill To</th>
                                    <th style="width: 110px;">Date</th>
                                    <th style="width: 110px;">Due Date</th>
                                    <th>Deposited / Bank</th>
                                    <th class="text-end" style="width: 130px;">Total Billed</th>
                                    <th class="text-end" style="width: 130px;">Payment Made</th>
                                    <th class="text-end" style="width: 130px;">Balance Due</th>
                                    <th class="text-center" style="width: 100px;">Status</th>
                                    <th class="text-center pe-3" style="width: 140px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $inv)
                                    @php
                                        $badgeClass = [
                                            'paid'    => 'bg-success',
                                            'partial' => 'bg-warning text-dark',
                                            'unpaid'  => 'bg-danger',
                                            'draft'   => 'bg-secondary',
                                        ][$inv->status] ?? 'bg-secondary';
                                    @endphp
                                    <tr>
                                        <td class="ps-3">
                                            <a href="{{ route('invoice.show', $inv->id) }}" class="fw-bold text-primary fs-14">
                                                #{{ $inv->invoice_no }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($inv->client)
                                                <strong class="text-dark d-block">{{ $inv->client->name }}</strong>
                                            @endif
                                            <span class="text-muted fs-12" style="white-space: pre-line;">{{ \Illuminate\Support\Str::limit($inv->bill_to, 40) }}</span>
                                        </td>
                                        <td class="text-nowrap">{{ formatDate($inv->invoice_date) }}</td>
                                        <td class="text-nowrap">{{ formatDate($inv->due_date) }}</td>
                                        <td>
                                            @if($inv->bank)
                                                <span class="badge bg-soft-primary text-primary fs-11">
                                                    <i class="mdi mdi-bank me-1"></i>{{ $inv->bank->name }}
                                                </span>
                                            @else
                                                <span class="text-muted fs-11">Cash / Default</span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            {{ $inv->currency }} {{ number_format($inv->total_amount, 2) }}
                                        </td>
                                        <td class="text-end fw-semibold text-success">
                                            {{ $inv->currency }} {{ number_format($inv->payment_made, 2) }}
                                        </td>
                                        <td class="text-end fw-bold {{ $inv->balance_due > 0 ? 'text-danger' : 'text-success' }}">
                                            {{ $inv->currency }} {{ number_format($inv->balance_due, 2) }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $badgeClass }} px-2 py-1 fs-11">
                                                {{ ucfirst($inv->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center pe-3">
                                            <div class="d-flex justify-content-center gap-1">
                                                @can('invoice_view')
                                                <a href="{{ route('invoice.show', $inv->id) }}" class="btn btn-sm btn-outline-primary" title="View / Print">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="{{ route('invoice.pdf', $inv->id) }}" target="_blank" class="btn btn-sm btn-outline-danger" title="PDF">
                                                    <i class="mdi mdi-file-pdf-box"></i>
                                                </a>
                                                @endcan
                                                @can('invoice_edit')
                                                <a href="{{ route('invoice.edit', $inv->id) }}" class="btn btn-sm btn-outline-info" title="Edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                @endcan
                                                @can('invoice_trash')
                                                <form action="{{ route('invoice.delete', $inv->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete Invoice #{{ $inv->invoice_no }}? This will also remove linked payment transactions.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5 text-muted">
                                            <i class="mdi mdi-receipt fs-36 text-muted d-block mb-1"></i>
                                            No invoices found.
                                            <a href="{{ route('invoice.create') }}" class="fw-bold text-primary">Click here to generate your first Invoice &rarr;</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($invoices->hasPages())
                        <div class="p-3 border-top d-flex justify-content-end">
                            {{ $invoices->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
