@extends('layout.master')
@section('title', 'Add Bank')
@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="fs-18 fw-semibold m-0">Add Bank</h4>
                        <p class="text-muted fs-12 mb-0">Create a new company bank account with active/inactive status</p>
                    </div>
                    <a href="{{ route('bank.index') }}" class="btn btn-secondary btn-sm">
                        <i class="mdi mdi-arrow-left"></i> Back to Banks
                    </a>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light py-3 border-bottom">
                        <h5 class="card-title m-0 fw-semibold text-primary">
                            <i class="mdi mdi-bank me-1"></i> Bank Account Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('bank.store') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <!-- Bank Name -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Bank Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           placeholder="e.g. Meezan Bank, Bank Alfalah, HBL" 
                                           value="{{ old('name') }}" required autofocus>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Account Title / Name -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Account Title / Beneficiary Name</label>
                                    <input type="text" name="account_name" class="form-control @error('account_name') is-invalid @enderror" 
                                           placeholder="e.g. Baba Hajj Group" 
                                           value="{{ old('account_name') }}">
                                    @error('account_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Account Number / IBAN -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Account Number / IBAN</label>
                                    <input type="text" name="account_number" class="form-control @error('account_number') is-invalid @enderror" 
                                           placeholder="e.g. 01010101010101 or PK36MEZN0001010101010101" 
                                           value="{{ old('account_number') }}">
                                    @error('account_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Branch Name / Code -->
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Branch Name / Code</label>
                                    <input type="text" name="branch" class="form-control @error('branch') is-invalid @enderror" 
                                           placeholder="e.g. Main Branch, Karachi" 
                                           value="{{ old('branch') }}">
                                    @error('branch')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>🟢 Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>🔴 Inactive</option>
                                    </select>
                                    <small class="text-muted">Only active banks appear in invoice dropdowns.</small>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                <a href="{{ route('bank.index') }}" class="btn btn-secondary px-4">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                    <i class="mdi mdi-check-circle me-1"></i> Save Bank
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
