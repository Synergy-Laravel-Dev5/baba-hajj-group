@extends('layout.master')
@section('title', 'Client Invoices Bank Report Filter')
@section('content')
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                                <div>
                                    <h4 class="fw-bold m-0 text-dark">
                                        <i class="mdi mdi-bank-check text-primary me-2"></i> Client Invoices Bank Report Filter
                                    </h4>
                                    <p class="text-muted fs-13 mb-0 mt-1">
                                        Filter client invoices by Bank, Client, Payment Status, and Date Range to view invoiced amount after discount, paid amount, and remaining balance (baki).
                                    </p>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('invoice.index') }}" class="btn btn-outline-primary btn-sm">
                                        <i class="mdi mdi-receipt me-1"></i> Invoices List
                                    </a>
                                </div>
                            </div>

                            <form action="{{ route('bank-report.report') }}" method="GET">
                                <div class="row g-3">
                                    {{-- Bank Filter --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold fs-13">
                                            <i class="mdi mdi-bank me-1 text-primary"></i> Bank Account
                                        </label>
                                        <select name="bank_id" class="form-select form-select-sm">
                                            <option value="">-- All Banks --</option>
                                            @foreach ($banks as $bank)
                                                <option value="{{ $bank->id }}" {{ request('bank_id') == $bank->id ? 'selected' : '' }}>
                                                    {{ $bank->name }} {{ !empty($bank->account_number) ? '(' . $bank->account_number . ')' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted fs-11">Filter by deposit / linked bank account</small>
                                    </div>

                                    {{-- Client Filter --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold fs-13">
                                            <i class="mdi mdi-account-group me-1 text-info"></i> Client / Passenger
                                        </label>
                                        <select name="client_id" class="form-select form-select-sm">
                                            <option value="">-- All Clients --</option>
                                            @foreach ($clients as $client)
                                                <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                                    {{ $client->name }} {{ !empty($client->phone) ? '(' . $client->phone . ')' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted fs-11">Filter by specific client or passenger</small>
                                    </div>

                                    {{-- Payment Status Filter --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold fs-13">
                                            <i class="mdi mdi-check-circle-outline me-1 text-success"></i> Payment Status
                                        </label>
                                        <select name="status" class="form-select form-select-sm">
                                            <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>All Statuses (Paid, Partial, Unpaid)</option>
                                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                            <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                                            <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                        </select>
                                        <small class="text-muted fs-11">Filter by invoice clearance status</small>
                                    </div>

                                    {{-- From Date --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-13">
                                            <i class="mdi mdi-calendar-start me-1 text-secondary"></i> From Date (Invoice Date)
                                        </label>
                                        <input type="date" name="from_date" class="form-control form-control-sm"
                                            value="{{ request('from_date') }}">
                                    </div>

                                    {{-- To Date --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold fs-13">
                                            <i class="mdi mdi-calendar-end me-1 text-secondary"></i> To Date (Invoice Date)
                                        </label>
                                        <input type="date" name="to_date" class="form-control form-control-sm"
                                            value="{{ request('to_date') }}">
                                    </div>

                                    {{-- Search Keyword --}}
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold fs-13">
                                            <i class="mdi mdi-magnify me-1 text-secondary"></i> Search Keyword
                                        </label>
                                        <input type="text" name="search" class="form-control form-control-sm"
                                            placeholder="Search by Invoice # (e.g. INV-004492), client name, phone number, bank name, etc." 
                                            value="{{ request('search') }}">
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="col-12 text-end mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                                        <a href="{{ route('bank-report.report') }}" class="btn btn-outline-secondary btn-sm px-3">
                                            <i class="mdi mdi-refresh me-1"></i> View All Invoices
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                                            <i class="mdi mdi-filter me-1"></i> Generate Report
                                        </button>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
