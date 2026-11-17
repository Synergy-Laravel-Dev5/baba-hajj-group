@extends('layout.master')
@section('title', 'Client Trash')
@section('content')

    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="py-3 d-flex justify-content-between align-items-center">
                    <h4 class="fs-18 fw-semibold m-0">Client Trash</h4>
                    <a href="{{ route('client.index') }}" class="btn btn-secondary btn-sm">← Back</a>
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
                                        <th>Deleted At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($clients as $client)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $client->name }}</strong></td>
                                            <td><span class="font-monospace">{{ $client->formatted_cnic ?? '—' }}</span></td>
                                            <td>
                                                @if ($client->whatsapp_url)
                                                    <a href="{{ $client->whatsapp_url }}" target="_blank" rel="noopener noreferrer"
                                                        class="text-decoration-none d-inline-flex align-items-center gap-1 text-dark"
                                                        title="Click to chat on WhatsApp">
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle p-1 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 20px; height: 20px;">
                                                            <i class="mdi mdi-whatsapp fs-12"></i>
                                                        </span>
                                                        <span class="fw-semibold">{{ $client->formatted_phone }}</span>
                                                    </a>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($client->company)
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                                        {{ $client->company->company_name ?? $client->company->name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>{{ $client->deleted_at->format('d M Y, h:i A') }}</td>
                                            <td>
                                                @can('client_restore')
                                                <a href="{{ route('client.restore', $client->id) }}"
                                                    class="btn btn-sm btn-outline-success">
                                                    <i class="mdi mdi-restore me-1"></i> Restore
                                                </a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">Trash is empty.</td>
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
