@extends('layout.master')
@section('title', 'Bank Ledger - ' . $bank->name)
@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <!-- Top Bar -->
                <div class="py-3 d-flex align-items-center justify-content-between flex-wrap gap-2 d-print-none">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="fs-18 fw-semibold m-0">Bank Ledger: {{ $bank->name }}</h4>
                            <span class="badge {{ $bank->status === 'active' ? 'bg-success' : 'bg-danger' }} px-2 py-1">
                                {{ ucfirst($bank->status) }}
                            </span>
                        </div>
                        <p class="text-muted fs-12 mb-0">
                            {{ $bank->account_name ? $bank->account_name . ' | ' : '' }}
                            {{ $bank->account_number ? 'A/C: ' . $bank->account_number . ' | ' : '' }}
                            {{ $bank->branch ? 'Branch: ' . $bank->branch : '' }}
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <button onclick="window.print()" class="btn btn-outline-primary btn-sm">
                            <i class="mdi mdi-printer me-1"></i> Print Statement
                        </button>
                        <a href="{{ route('bank.index') }}" class="btn btn-secondary btn-sm">
                            <i class="mdi mdi-arrow-left me-1"></i> All Banks
                        </a>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm mb-0">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted fs-11 text-uppercase fw-bold">Total In (Credits)</span>
                                    <h4 class="my-1 fw-bold text-success">PKR {{ number_format($totalCredits, 2) }}</h4>
                                    <span class="text-muted fs-11">Client receipts & transfers in</span>
                                </div>
                                <div class="avatar-sm bg-soft-success rounded d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-arrow-bottom-left fs-24 text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm mb-0">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted fs-11 text-uppercase fw-bold">Total Out (Debits)</span>
                                    <h4 class="my-1 fw-bold text-danger">PKR {{ number_format($totalDebits, 2) }}</h4>
                                    <span class="text-muted fs-11">Expenses & transfers out</span>
                                </div>
                                <div class="avatar-sm bg-soft-danger rounded d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-arrow-top-right fs-24 text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm mb-0">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted fs-11 text-uppercase fw-bold">Closing Balance</span>
                                    <h4 class="my-1 fw-bold {{ $running >= 0 ? 'text-primary' : 'text-danger' }}">
                                        PKR {{ number_format($running, 2) }}
                                    </h4>
                                    <span class="text-muted fs-11">Net statement balance</span>
                                </div>
                                <div class="avatar-sm bg-soft-primary rounded d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-scale-balance fs-24 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm mb-0">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted fs-11 text-uppercase fw-bold">Total Transactions</span>
                                    <h4 class="my-1 fw-bold text-dark">{{ $sortedEntries->count() }}</h4>
                                    <span class="text-muted fs-11">All ledger movements</span>
                                </div>
                                <div class="avatar-sm bg-soft-info rounded d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-history fs-24 text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Date Filter -->
                <div class="card shadow-sm border-0 mb-3 d-print-none">
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('bank.ledger', $bank->id) }}" class="row g-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label fs-12 text-muted mb-1">Switch Bank</label>
                                <select class="form-select form-select-sm" onchange="window.location.href='/admin/bank/ledger/' + this.value">
                                    @foreach($allBanks as $b)
                                        <option value="{{ $b->id }}" {{ $b->id == $bank->id ? 'selected' : '' }}>
                                            {{ $b->name }}{{ $b->account_number ? ' (' . $b->account_number . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fs-12 text-muted mb-1">From Date</label>
                                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $fromDate }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fs-12 text-muted mb-1">To Date</label>
                                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $toDate }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-1">
                                <button type="submit" class="btn btn-sm btn-primary w-100" style="margin-top:22px;">
                                    <i class="mdi mdi-filter me-1"></i> Filter
                                </button>
                                <a href="{{ route('bank.ledger', $bank->id) }}" class="btn btn-sm btn-outline-secondary" style="margin-top:22px;">
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Ledger Transactions Table -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 fw-semibold text-primary">
                            <i class="mdi mdi-book-open-page-variant me-1"></i> Ledger Movements & Running Balance
                        </h5>
                        <span class="badge bg-primary px-3 py-1 fs-12">
                            Current Live Balance: PKR {{ number_format($bank->balance, 2) }}
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">#</th>
                                        <th style="width: 100px;">Date</th>
                                        <th style="width: 120px;">Category</th>
                                        <th>Party / Payee</th>
                                        <th>Ref / Inv #</th>
                                        <th>Narration / Details</th>
                                        <th class="text-end" style="width: 130px;">Credit (In +)</th>
                                        <th class="text-end" style="width: 130px;">Debit (Out -)</th>
                                        <th class="text-end" style="width: 140px;">Running Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sortedEntries as $e)
                                        <tr>
                                            <td class="text-center fw-semibold text-muted">{{ $loop->iteration }}</td>
                                            <td class="fw-semibold text-dark">
                                                {{ $e->date ? \Carbon\Carbon::parse($e->date)->format('d M, Y') : '—' }}
                                            </td>
                                            <td>
                                                <span class="badge {{ $e->credit > 0 ? 'bg-success' : 'bg-primary' }} px-2 py-1 fs-11">
                                                    {{ $e->category }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong class="text-dark">{{ $e->party }}</strong>
                                            </td>
                                            <td>
                                                @if($e->reference)
                                                    <code class="text-dark bg-light px-2 py-0 rounded">{{ $e->reference }}</code>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-secondary fs-12">{{ $e->description ?: '—' }}</span>
                                            </td>
                                            <td class="text-end fw-bold {{ $e->credit > 0 ? 'text-success' : 'text-muted' }}">
                                                {{ $e->credit > 0 ? number_format($e->credit, 2) : '—' }}
                                            </td>
                                            <td class="text-end fw-bold {{ $e->debit > 0 ? 'text-danger' : 'text-muted' }}">
                                                {{ $e->debit > 0 ? number_format($e->debit, 2) : '—' }}
                                            </td>
                                            <td class="text-end fw-bold {{ $e->running_balance >= 0 ? 'text-primary' : 'text-danger' }}">
                                                PKR {{ number_format($e->running_balance, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5 text-muted">
                                                <i class="mdi mdi-folder-open-outline fs-36 text-muted d-block mb-1"></i>
                                                No transactions recorded for this bank in selected date range.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="table-light">
                                    <tr class="fw-bold">
                                        <td colspan="6" class="text-end py-2">Totals:</td>
                                        <td class="text-end text-success py-2">PKR {{ number_format($totalCredits, 2) }}</td>
                                        <td class="text-end text-danger py-2">PKR {{ number_format($totalDebits, 2) }}</td>
                                        <td class="text-end py-2 {{ $running >= 0 ? 'text-primary' : 'text-danger' }}">
                                            PKR {{ number_format($running, 2) }}
                                        </td>
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
