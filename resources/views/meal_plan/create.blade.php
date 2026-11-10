@extends('layout.master')
@section('title', 'Add Meal Plan')
@section('header-title', 'Add Meal Plan')
@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container">
                <div class="py-3 d-flex justify-content-between align-items-center">
                    <h4 class="fs-18 fw-semibold m-0">Add New Meal Plan</h4>
                    <a href="{{ route('meal-plan.index') }}" class="btn btn-secondary btn-sm">
                        <i class="mdi mdi-arrow-left me-1"></i> Back to List
                    </a>
                </div>
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-light">
                                <h5 class="mb-0 fw-semibold"><i class="mdi mdi-silverware-fork-knife me-1 text-primary"></i> Meal Plan Information</h5>
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

                                <form action="{{ route('meal-plan.store') }}" method="POST">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Meal Plan Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="e.g. Full Board (FB), 3 Time Buffet Meal" required>
                                            @error('name')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Code / Abbreviation</label>
                                            <input type="text" name="code" value="{{ old('code') }}" class="form-control" placeholder="e.g. FB, BB, RO, BUFFET3 (auto-generated if empty)">
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                            <select name="status" class="form-select" required>
                                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label fw-semibold">Description / Menu Details</label>
                                            <textarea name="description" class="form-control" rows="3" placeholder="e.g. Daily Breakfast, Lunch & Dinner buffet served at hotel restaurant">{{ old('description') }}</textarea>
                                        </div>

                                        <div class="col-12 mt-4 text-end">
                                            <a href="{{ route('meal-plan.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                                            <button type="submit" class="btn btn-primary px-4">
                                                <i class="mdi mdi-content-save me-1"></i> Save Meal Plan
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
