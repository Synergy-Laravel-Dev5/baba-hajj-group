@extends('layout.master')
@section('title', 'Banks Management')
@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h4 class="fs-18 fw-semibold m-0">Banks Management</h4>
                        <p class="text-muted fs-12 mb-0">Manage company bank accounts, live balances, active/inactive status and bank ledgers</p>
                    </div>
                    <div class="d-flex gap-2">
                        @can('bank_report_view')
                        <a href="{{ route('bank-report.filter') }}" class="btn btn-outline-primary btn-sm">
                            <i class="mdi mdi-file-chart me-1"></i> Bank Invoices Report
                        </a>
                        @endcan
                        @can('bank_create')
                        <a href="{{ route('bank.create') }}" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-plus-circle me-1"></i> + Add Bank
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

                <!-- Summary Cards -->
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm mb-0">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted fs-12 text-uppercase fw-bold">Total Banks</span>
                                    <h3 class="my-1 fw-bold text-dark">{{ $totalBanks ?? $banks->count() }}</h3>
                                    <span class="text-muted fs-11">All registered company banks</span>
                                </div>
                                <div class="avatar-sm bg-soft-primary rounded d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-bank fs-24 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm mb-0">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted fs-12 text-uppercase fw-bold">Active Banks</span>
                                    <h3 class="my-1 fw-bold text-success">{{ $activeBanks ?? $banks->where('status', 'active')->count() }}</h3>
                                    <span class="text-success fs-11">Available in Invoice dropdown</span>
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
                                    <span class="text-muted fs-12 text-uppercase fw-bold">Inactive Banks</span>
                                    <h3 class="my-1 fw-bold text-secondary">{{ $inactiveBanks ?? $banks->where('status', 'inactive')->count() }}</h3>
                                    <span class="text-muted fs-11">Hidden from Invoice dropdown</span>
                                </div>
                                <div class="avatar-sm bg-soft-secondary rounded d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-cancel fs-24 text-secondary"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm mb-0">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted fs-12 text-uppercase fw-bold">Total Bank Balance</span>
                                    <h3 class="my-1 fw-bold {{ ($totalLiquidity ?? 0) >= 0 ? 'text-primary' : 'text-danger' }}">
                                        PKR {{ number_format($totalLiquidity ?? 0, 2) }}
                                    </h3>
                                    <span class="text-muted fs-11">Net liquidity across active banks</span>
                                </div>
                                <div class="avatar-sm bg-soft-info rounded d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-cash-multiple fs-24 text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter & Search Card -->
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('bank.index') }}" class="row g-2 align-items-center">
                            <div class="col-md-3">
                                <label class="form-label fs-12 text-muted mb-1">Status</label>
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="all" {{ ($status ?? 'all') == 'all' ? 'selected' : '' }}>-- All Statuses --</option>
                                    <option value="active" {{ ($status ?? '') == 'active' ? 'selected' : '' }}>🟢 Active Only</option>
                                    <option value="inactive" {{ ($status ?? '') == 'inactive' ? 'selected' : '' }}>⚪ Inactive Only</option>
                                </select>
                            </div>

                            <div class="col-md-7">
                                <label class="form-label fs-12 text-muted mb-1">Search</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control" placeholder="Search by Bank Name, Account Title, Account / IBAN, or Branch..." value="{{ $search ?? '' }}">
                                    <button type="submit" class="btn btn-primary px-3">
                                        <i class="mdi mdi-magnify me-1"></i> Filter
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <a href="{{ route('bank.index') }}" class="btn btn-sm btn-outline-secondary w-100" style="margin-top: 22px;">
                                    <i class="mdi mdi-refresh me-1"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Banks Table Card -->
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-bordered table-hover dt-responsive nowrap align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">S:NO</th>
                                        <th>Bank Name</th>
                                        <th>Account Title</th>
                                        <th>Account / IBAN Number</th>
                                        <th>Branch</th>
                                        <th class="text-end" style="width: 150px;">Live Balance</th>
                                        <th class="text-center" style="width: 110px;">Status</th>
                                        <th class="text-center" style="width: 160px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($banks as $bank)
                                        <tr>
                                            <td class="text-center fw-semibold text-muted">{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs bg-light rounded text-primary d-flex align-items-center justify-content-center me-2">
                                                        <i class="mdi mdi-bank fs-16"></i>
                                                    </div>
                                                    <div>
                                                        <strong class="text-dark fs-14">{{ $bank->name }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-semibold text-secondary">{{ $bank->account_name ?: '—' }}</span>
                                            </td>
                                            <td>
                                                @if($bank->account_number)
                                                    <code class="text-dark fw-bold fs-12 bg-light px-2 py-1 rounded">{{ $bank->account_number }}</code>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $bank->branch ?: '—' }}</span>
                                            </td>
                                            <td class="text-end">
                                                <strong class="fs-13 {{ $bank->balance >= 0 ? 'text-success' : 'text-danger' }}">
                                                    PKR {{ number_format($bank->balance, 2) }}
                                                </strong>
                                            </td>
                                            <td class="text-center">
                                                @can('bank_edit')
                                                <form action="{{ route('bank.toggle-status', $bank->id) }}" method="POST" class="d-inline" title="Click to toggle status">
                                                    @csrf
                                                    @method('PATCH')
                                                    @if ($bank->status == 'active')
                                                        <button type="submit" class="btn btn-xs btn-success rounded-pill px-2 py-0 border-0 fs-11" onclick="return confirm('Change status to Inactive?')">
                                                            <i class="mdi mdi-check-circle me-1"></i> Active
                                                        </button>
                                                    @else
                                                        <button type="submit" class="btn btn-xs btn-danger rounded-pill px-2 py-0 border-0 fs-11" onclick="return confirm('Change status to Active?')">
                                                            <i class="mdi mdi-close-circle me-1"></i> Inactive
                                                        </button>
                                                    @endif
                                                </form>
                                                @else
                                                    <span class="badge {{ $bank->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ ucfirst($bank->status) }}
                                                    </span>
                                                @endcan
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    @can('bank_view')
                                                    <a href="{{ route('bank.ledger', $bank->id) }}" class="btn btn-sm btn-outline-success" title="View Bank Ledger">
                                                        <i class="mdi mdi-book-open-page-variant"></i>
                                                    </a>
                                                    <a href="{{ route('expense.transaction.report', ['bank_id' => $bank->id]) }}" class="btn btn-sm btn-outline-primary" title="View Bank Statement & Transactions">
                                                        <i class="mdi mdi-file-chart-outline"></i>
                                                    </a>
                                                    @endcan
                                                    @can('bank_edit')
                                                    <a href="{{ route('bank.edit', $bank->id) }}" class="btn btn-sm btn-outline-info" title="Edit Bank">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                    @endcan
                                                    @can('bank_trash')
                                                    <form action="{{ route('bank.destroy', $bank->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete bank {{ $bank->name }}?')" title="Delete Bank">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-muted">
                                                <i class="mdi mdi-bank-remove fs-36 text-muted d-block mb-1"></i>
                                                No banks registered yet. 
                                                <a href="{{ route('bank.create') }}" class="fw-bold text-primary">Click here to add your first Bank &rarr;</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
