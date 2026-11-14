@extends('layout.master')
@section('title', 'Add Client')
@section('header-title', 'Add Client')
@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container">
                <div class="py-3 d-flex justify-content-between align-items-center">
                    <h4 class="fs-18 fw-semibold m-0">Add New Client</h4>
                    <a href="{{ route('client.index') }}" class="btn btn-secondary btn-sm">Back</a>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Client Information</h5>
                            </div>
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $e)
                                                <li>{{ $e }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <form action="{{ route('client.store') }}" method="POST">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">Client Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" value="{{ old('name') }}"
                                                    class="form-control" placeholder="Enter client name" required>
                                                @error('name')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">CNIC</label>
                                                <input type="text" name="cnic" value="{{ old('cnic') }}"
                                                    class="form-control" placeholder="XXXXX-XXXXXXX-X">
                                                @error('cnic')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">Contact No</label>
                                                <input type="text" name="phone" value="{{ old('phone') }}"
                                                    class="form-control" placeholder="e.g. 0300-1234567">
                                                @error('phone')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">Referral</label>
                                                <select name="company_id" class="form-select select2" data-placeholder="-- Select Referral --">
                                                    <option value="">-- Select Referral --</option>
                                                    @foreach ($companies as $comp)
                                                        <option value="{{ $comp->id }}" {{ old('company_id') == $comp->id ? 'selected' : '' }}>
                                                            {{ $comp->company_name ?? $comp->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('company_id')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12 text-end">
                                            <button type="submit" class="btn btn-primary px-4">
                                                Save Client
                                            </button>
                                            <a href="{{ route('client.index') }}" class="btn btn-secondary">
                                                Cancel
                                            </a>
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
