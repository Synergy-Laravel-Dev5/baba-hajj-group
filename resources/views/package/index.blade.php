@extends('layout.master')

@section('title', 'Packages')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h4 class="fs-18 fw-semibold m-0">{{ ucfirst($package) }} Packages</h4>

                        <div class="btn-group btn-group-sm me-2" role="group">
                            <a href="{{ route('package.index', ['package' => 'hajj']) }}"
                               class="btn {{ $package === 'hajj' ? 'btn-warning text-dark fw-bold' : 'btn-outline-secondary' }}">
                                <i class="mdi mdi-kaaba me-1"></i>Hajj ({{ $hajjCount ?? 0 }})
                            </a>
                            <a href="{{ route('package.index', ['package' => 'umrah']) }}"
                               class="btn {{ $package === 'umrah' ? 'btn-success text-white fw-bold' : 'btn-outline-secondary' }}">
                                <i class="mdi mdi-mosque me-1"></i>Umrah ({{ $umrahCount ?? 0 }})
                            </a>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        @can('package_create')
                            @if($package === 'umrah')
                                <a href="{{ route('mobile-ops.umrah-packages.create') }}" class="btn btn-success btn-sm d-flex align-items-center gap-1">
                                    <i class="mdi mdi-plus"></i> Add Umrah Package
                                </a>
                            @else
                                <a href="{{ route('package.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                                    <i class="mdi mdi-plus"></i> Add Hajj Package
                                </a>
                            @endif
                        @endcan
                        @can('package_trash_view')
                            <a href="{{ route('package.trash') }}" class="btn btn-danger btn-sm">
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
                                        <th>Package Name</th>
                                        <th>Code</th>
                                        <th>Maktab</th>
                                        <th>Days</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($packages as $package)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $package->package_title ? $package->package_title . ($package->name ? ' - ' . $package->name : '') : $package->name }}</strong>
                                            </td>
                                            <td>{{ $package->code }}</td>
                                            <td>{{ $package->maktab }}</td>
                                            <td>{{ $package->days }}</td>
                                            <td>{{ $package->created_at->format('d M, Y') }}</td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @can('package_edit')
                                                        <a href="{{ route('package.edit', $package->id) }}"
                                                            class="btn btn-sm btn-outline-success" title="Edit">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </a>
                                                    @endcan
                                                    @can('package_show')
                                                        <a href="{{ route('package.show', $package->id) }}" target="_blank"
                                                            class="btn btn-sm btn-outline-primary" title="View & Print Official Flyer">
                                                            <i class="mdi mdi-printer me-1"></i> Flyer
                                                        </a>
                                                    @endcan
                                                    @can('package_trash')
                                                        <form action="{{ route('package.delete', $package->id) }}"
                                                            method="POST" onsubmit="return confirm('Move to trash?')">
                                                            @csrf
                                                            @method('DELETE')
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
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <i class="mdi mdi-package-variant-closed fs-24 d-block mb-1"></i>
                                                No {{ ucfirst($package) }} packages found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $packages->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
