@extends('layout.master')
@section('title', 'Clients')
@section('content')

    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="py-3 d-flex align-items-center justify-content-between">
                    <h4 class="fs-18 fw-semibold m-0">Clients</h4>
                    <div class="d-flex gap-2">
                        @can('client_create')
                            <a href="{{ route('client.create') }}" class="btn btn-primary btn-sm">
                                + Add Client
                            </a>
                        @endcan
                        @can('invoice_create')
                            <a href="{{ route('invoice.create') }}" class="btn btn-outline-primary btn-sm">
                                <i class="mdi mdi-receipt me-1"></i> + Create Invoice
                            </a>
                        @endcan
                        @can('client_trash_view')
                            <a href="{{ route('client.trash') }}" class="btn btn-danger btn-sm">
                                🗑 Trash <span class="badge bg-white text-danger ms-1">{{ $trashCount }}</span>
                            </a>
                        @endcan
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-bordered dt-responsive nowrap align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Client Name</th>
                                        <th>CNIC</th>
                                        <th>Contact No</th>
                                        <th>Referral</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($clients as $client)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $client->name }}</strong></td>
                                            <td>{{ $client->cnic ?? '—' }}</td>
                                            <td>{{ $client->phone ?? '—' }}</td>
                                            <td>
                                                @if ($client->company)
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                                        <i class="mdi mdi-domain me-1"></i>{{ $client->company->company_name ?? $client->company->name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @can('invoice_create')
                                                    <a href="{{ route('invoice.create', ['client_id' => $client->id]) }}"
                                                        class="btn btn-sm btn-outline-primary" title="Create Invoice for {{ $client->name }}">
                                                        <i class="mdi mdi-receipt"></i>
                                                    </a>
                                                    @endcan
                                                    @can('client_edit')
                                                        <a href="{{ route('client.edit', $client->id) }}"
                                                            class="btn btn-sm btn-outline-success" title="Edit">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </a>
                                                    @endcan
                                                    @can('client_trash')
                                                        <form action="{{ route('client.delete', $client->id) }}" method="POST"
                                                            onsubmit="return confirm('Move to trash?')">
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
                                            <td colspan="6" class="text-center text-muted py-4">No clients found.</td>
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
