@extends('layout.master')
@section('title', 'Meal Plans')
@section('content')

    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h4 class="fs-18 fw-semibold m-0">Meal Plans</h4>
                        <p class="text-muted small mb-0">Manage food and meal plans (Room Only, Bed & Breakfast, Half Board, Full Board, 3 Time Buffet, etc.)</p>
                    </div>
                    <div class="d-flex gap-2">
                        @can('meal_plan_create')
                        <a href="{{ route('meal-plan.create') }}" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-plus-circle me-1"></i> Add Meal Plan
                        </a>
                        @endcan
                        @can('meal_plan_trash_view')
                        <a href="{{ route('meal-plan.trash') }}" class="btn btn-danger btn-sm">
                            <i class="mdi mdi-delete-outline me-1"></i> Trash <span class="badge bg-white text-danger ms-1">{{ $trashCount }}</span>
                        </a>
                        @endcan
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="mdi mdi-alert-circle me-1"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-hover table-bordered dt-responsive nowrap align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:50px">#</th>
                                        <th>Meal Plan Name</th>
                                        <th>Code</th>
                                        <th>Description</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center" style="width:120px">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($mealPlans as $mp)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong class="text-dark">{{ $mp->name }}</strong>
                                            </td>
                                            <td>
                                                <code class="text-primary fw-semibold">{{ $mp->code ?? '—' }}</code>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $mp->description ?? '—' }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $mp->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ ucfirst($mp->status) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    @can('meal_plan_edit')
                                                    <a href="{{ route('meal-plan.edit', $mp->id) }}"
                                                        class="btn btn-sm btn-outline-success" title="Edit">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                    @endcan
                                                    @can('meal_plan_trash')
                                                    <form action="{{ route('meal-plan.delete', $mp->id) }}" method="POST"
                                                        onsubmit="return confirm('Move this meal plan to trash?')">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="mdi mdi-silverware-fork-knife fs-24 d-block mb-1"></i>
                                                No Meal Plans found. Click "Add Meal Plan" to create one.
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
