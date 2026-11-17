@extends('layout.master')
@section('title', 'Referrals')
@section('header-title', 'Referrals')
@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="row mt-5">
                    <div class="col-md-12 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-uppercase">All Referrals</h6>
                        <div class="mb-1 d-flex gap-2">
                            @can('company_create')
                            <a href="{{ route('company.create') }}"
                                class="btn btn-primary btn-sm d-flex align-items-center gap-1"
                                style="font-size: 12px; padding: 4px 12px;">
                                <i data-feather="plus" style="width:14px; height:14px;"></i>
                                Add Referral
                            </a>
                            @endcan
                            @can('company_trash_view')
                            <a href="{{ route('company.trash') }}"
                                class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1"
                                style="font-size: 12px; padding: 4px 12px;">
                                <i data-feather="trash-2" style="width:14px; height:14px;"></i>
                                Trash
                                <span class="badge bg-danger text-white"
                                    style="font-size: 10px;">{{ $trashcount ?? 0 }}</span>
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <hr>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-bordered dt-responsive nowrap align-middle">
                                <thead class="text-center">
                                    <tr>
                                        <th>#</th>
                                        <th class="text-start">Referral Name</th>
                                        <th>Referral Code</th>
                                        <th>Quota</th>
                                        <th>Currency</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @foreach ($companies as $company)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="text-start"><strong>{{ $company->company_name ?? '—' }}</strong></td>
                                            <td><span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">{{ $company->company_code ?? '—' }}</span></td>
                                            <td><span class="fw-semibold">{{ $company->quota ?? '—' }}</span></td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                                    {{ is_object($company->currency_type) ? ($company->currency_type->value ?? $company->currency_type->name) : ($company->currency_type ?? 'PKR') }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-inline-flex gap-1">
                                                    @can('company_show')
                                                    <a href="{{ route('company.show', $company->id) }}"
                                                        class="btn btn-outline-primary btn-sm" title="View Details">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                    @endcan
                                                    @can('company_edit')
                                                    <a href="{{ route('company.edit', $company->id) }}"
                                                        class="btn btn-outline-success btn-sm" title="Edit">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                    @endcan
                                                    @can('company_trash')
                                                    <form action="{{ route('company.delete', $company->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Are you sure you want to delete this Referral?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-outline-danger btn-sm" title="Delete">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
