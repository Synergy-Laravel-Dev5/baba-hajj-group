@extends('layout.master')
@section('title', 'Meal Plans Trash')
@section('content')

    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="fs-18 fw-semibold m-0">Meal Plans — Trash</h4>
                        <p class="text-muted small mb-0">Restore or permanently delete archived meal plans</p>
                    </div>
                    <a href="{{ route('meal-plan.index') }}" class="btn btn-secondary btn-sm">
                        <i class="mdi mdi-arrow-left me-1"></i> Back to Meal Plans
                    </a>
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
                                        <th>Deleted At</th>
                                        <th class="text-center" style="width:100px">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($mealPlans as $mp)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong class="text-dark">{{ $mp->name }}</strong></td>
                                            <td><code>{{ $mp->code ?? '—' }}</code></td>
                                            <td>{{ $mp->deleted_at ? $mp->deleted_at->format('d M Y, h:i A') : '—' }}</td>
                                            <td class="text-center">
                                                @can('meal_plan_restore')
                                                <a href="{{ route('meal-plan.restore', $mp->id) }}"
                                                    class="btn btn-sm btn-outline-primary" title="Restore">
                                                    <i class="mdi mdi-restore me-1"></i> Restore
                                                </a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                No deleted meal plans found.
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
