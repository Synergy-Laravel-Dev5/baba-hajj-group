@extends('layout.mobile-master')

@section('content')
    <div class="py-3 d-flex align-items-sm-center justify-content-between flex-column flex-sm-row gap-2">
        <div>
            <h4 class="fs-18 fw-semibold m-0 text-dark">Mobile App Registered Users</h4>
            <p class="text-muted fs-13 mb-0">Manage registered pilgrims and accounts created via the mobile application.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-sm btn-white border shadow-sm dropdown-toggle px-3" type="button"
                    data-bs-toggle="dropdown">
                    <i class="mdi mdi-filter-variant me-1"></i> Filter Status: {{ ucfirst($status ?? 'All') }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li><a class="dropdown-item fs-13" href="{{ route('mobile-ops.users.index') }}">All Users ({{ $counts['total'] }})</a></li>
                    <li><a class="dropdown-item fs-13 text-success" href="{{ route('mobile-ops.users.index', ['status' => 'active']) }}">Active ({{ $counts['active'] }})</a></li>
                    <li><a class="dropdown-item fs-13 text-danger" href="{{ route('mobile-ops.users.index', ['status' => 'suspended']) }}">Suspended ({{ $counts['suspended'] }})</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-3 mb-3">
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-12 fw-semibold text-uppercase">Total App Users</span>
                        <h4 class="fw-bold fs-22 my-1 text-dark">{{ $counts['total'] }}</h4>
                        <span class="fs-11 text-muted">Registered in Mobile App</span>
                    </div>
                    <div class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fs-20">
                        <i class="mdi mdi-account-group"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-12 fw-semibold text-uppercase">Active Accounts</span>
                        <h4 class="fw-bold fs-22 my-1 text-success">{{ $counts['active'] }}</h4>
                        <span class="fs-11 text-muted">Can login & book packages</span>
                    </div>
                    <div class="avatar-sm rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center fs-20">
                        <i class="mdi mdi-account-check"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-12 fw-semibold text-uppercase">Suspended Accounts</span>
                        <h4 class="fw-bold fs-22 my-1 text-danger">{{ $counts['suspended'] }}</h4>
                        <span class="fs-11 text-muted">Temporarily restricted</span>
                    </div>
                    <div class="avatar-sm rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center fs-20">
                        <i class="mdi mdi-account-cancel"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
            <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Users Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="fs-14 fw-bold m-0 text-dark">Registered Pilgrims Directory</h5>

            <form action="{{ route('mobile-ops.users.index') }}" method="GET" class="d-flex align-items-center gap-2 m-0">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Search name, phone, email..." value="{{ $search ?? '' }}" style="min-width: 240px;">
                <button type="submit" class="btn btn-sm btn-primary">Search</button>
                @if(!empty($search) || !empty($status))
                    <a href="{{ route('mobile-ops.users.index') }}" class="btn btn-sm btn-light border">Reset</a>
                @endif
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">User Details</th>
                            <th>Contact / WhatsApp</th>
                            <th>Bookings</th>
                            <th>Status</th>
                            <th>Registered At</th>
                            <th>Last Active</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($user->avatar_url)
                                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                                class="rounded-circle" style="width: 38px; height: 38px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-primary-subtle text-primary border border-primary-subtle d-flex align-items-center justify-content-center fw-bold fs-13"
                                                style="width: 38px; height: 38px;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold text-dark fs-13">{{ $user->name }}</div>
                                            <div class="fs-11 text-muted">{{ $user->email ?? 'No Email Provided' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fs-13 fw-semibold">
                                        <i class="mdi mdi-phone me-1 text-primary"></i>
                                        <a href="tel:{{ $user->phone }}" class="text-decoration-none text-dark">{{ $user->phone }}</a>
                                    </div>
                                    <div class="fs-11 text-muted">
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}" target="_blank"
                                            class="text-success text-decoration-none">
                                            <i class="mdi mdi-whatsapp me-1"></i> WhatsApp Chat
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1 fs-12">
                                        <i class="mdi mdi-calendar-check me-1 text-primary"></i> {{ $user->inquiries_count }} Booking(s)
                                    </span>
                                </td>
                                <td>
                                    @if ($user->status === 'active')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
                                    @elseif($user->status === 'suspended')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Suspended</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">{{ ucfirst($user->status) }}</span>
                                    @endif
                                </td>
                                <td class="fs-12 text-muted">
                                    <div>{{ $user->created_at->format('d M Y') }}</div>
                                    <div class="text-muted fs-11">{{ $user->created_at->format('h:i A') }}</div>
                                </td>
                                <td class="fs-12 text-muted">
                                    {{ $user->last_active_at ? $user->last_active_at->diffForHumans() : 'Never' }}
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <a href="{{ route('mobile-ops.chat.index', ['user_id' => $user->id]) }}"
                                            class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1"
                                            title="Live Chat">
                                            <i class="mdi mdi-chat-outline"></i> Chat
                                        </a>

                                        <form action="{{ route('mobile-ops.users.toggle-status', $user->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to change this user status?');">
                                            @csrf
                                            @if ($user->status === 'active')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Suspend User">
                                                    <i class="mdi mdi-cancel"></i>
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Activate User">
                                                    <i class="mdi mdi-check"></i>
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="mdi mdi-account-off fs-36 d-block mb-1"></i>
                                    No mobile app users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($users->hasPages())
            <div class="card-footer bg-white py-2 border-top">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
