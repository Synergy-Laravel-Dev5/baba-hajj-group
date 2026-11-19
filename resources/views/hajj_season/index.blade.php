@extends('layout.master')
@section('title', 'Hajj Seasons')
@section('content')

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h4 class="fs-18 fw-semibold m-0">Hajj Seasons</h4>
                    <p class="text-muted small mb-0">Manage Hajj seasons and active system operating year</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#addSeasonModal">
                        <i class="mdi mdi-plus-circle-outline"></i> Add Season
                    </button>
                </div>
            </div>

            <!-- Alerts -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-check-circle-outline me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-alert-circle-outline me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Seasons Table Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered dt-responsive nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Season / Year</th>
                                    <th>Hijri Year</th>
                                    <th>Season Name / Title</th>
                                    <th>Duration Dates</th>
                                    <th class="text-center">Packages</th>
                                    <th class="text-center">Bookings / Pax</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" style="width: 140px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($seasons as $season)
                                    @php
                                        $stats = $seasonStats[$season->id] ?? ['packages' => 0, 'bookings' => 0, 'pilgrims' => 0, 'applications' => 0];
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong class="text-dark">{{ $season->year }}</strong>
                                        </td>
                                        <td>
                                            {{ $season->hijri_year ? $season->hijri_year . 'H' : '—' }}
                                        </td>
                                        <td>
                                            <strong>{{ $season->name }}</strong>
                                            @if($season->notes)
                                                <div class="text-muted small">{{ Str::limit($season->notes, 40) }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($season->start_date || $season->end_date)
                                                <span class="small text-muted">
                                                    {{ $season->start_date ? formatDate($season->start_date) : '—' }}
                                                    to
                                                    {{ $season->end_date ? formatDate($season->end_date) : '—' }}
                                                </span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border">{{ $stats['packages'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border">{{ $stats['bookings'] }} ({{ $stats['pilgrims'] }} Pax)</span>
                                        </td>
                                        <td class="text-center">
                                            @if($season->is_active)
                                                <span class="badge bg-success">
                                                    Active
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                @if(!$season->is_active)
                                                    <form action="{{ route('hajj-season.activate', $season->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Activate {{ $season->year }}?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Activate Season">
                                                            Activate
                                                        </button>
                                                    </form>
                                                @endif

                                                <button type="button" class="btn btn-sm btn-outline-primary btn-edit-season"
                                                    data-id="{{ $season->id }}"
                                                    data-year="{{ $season->year }}"
                                                    data-hijri="{{ $season->hijri_year }}"
                                                    data-name="{{ $season->name }}"
                                                    data-start="{{ $season->start_date ? $season->start_date->format('Y-m-d') : '' }}"
                                                    data-end="{{ $season->end_date ? $season->end_date->format('Y-m-d') : '' }}"
                                                    data-notes="{{ $season->notes }}"
                                                    data-active="{{ $season->is_active ? '1' : '0' }}"
                                                    title="Edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>

                                                @if(!$season->is_active)
                                                    <form action="{{ route('hajj-season.delete', $season->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete Hajj Season {{ $season->year }}?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            No Hajj seasons configured yet. Click "Add Season" above to create one.
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

<!-- Modal: Add Hajj Season -->
<div class="modal fade" id="addSeasonModal" tabindex="-1" aria-labelledby="addSeasonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom py-3">
                <h5 class="modal-title fw-bold text-dark" id="addSeasonModalLabel">
                    Add Hajj Season
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('hajj-season.store') }}" method="POST">
                @csrf
                <div class="modal-body p-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Year <span class="text-danger">*</span></label>
                            <input type="number" name="year" id="add_year" class="form-control" placeholder="2027" required min="2020" max="2100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Hijri Year</label>
                            <input type="text" name="hijri_year" id="add_hijri_year" class="form-control" placeholder="1448">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Season Title <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="add_name" class="form-control" placeholder="Hajj Season 2027 (1448H)" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Start Date</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">End Date</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" name="is_active" id="add_is_active" value="1">
                                <label class="form-check-label fw-semibold small" for="add_is_active">
                                    Set as Active Season immediately
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2 px-3">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        Save Season
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Hajj Season -->
<div class="modal fade" id="editSeasonModal" tabindex="-1" aria-labelledby="editSeasonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom py-3">
                <h5 class="modal-title fw-bold text-dark" id="editSeasonModalLabel">
                    Edit Hajj Season
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSeasonForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Year <span class="text-danger">*</span></label>
                            <input type="number" name="year" id="edit_year" class="form-control" required min="2020" max="2100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Hijri Year</label>
                            <input type="text" name="hijri_year" id="edit_hijri_year" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Season Title <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Start Date</label>
                            <input type="date" name="start_date" id="edit_start_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">End Date</label>
                            <input type="date" name="end_date" id="edit_end_date" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Notes</label>
                            <textarea name="notes" id="edit_notes" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1">
                                <label class="form-check-label fw-semibold small" for="edit_is_active">
                                    Active Season
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2 px-3">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-3">
                        Update Season
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const addYearInput = document.getElementById('add_year');
    const addHijriInput = document.getElementById('add_hijri_year');
    const addNameInput = document.getElementById('add_name');

    if (addYearInput) {
        addYearInput.addEventListener('input', function () {
            const yr = parseInt(this.value, 10);
            if (yr && yr > 2000) {
                const approxHijri = Math.round((yr - 622) * (33 / 32));
                if (!addHijriInput.value || addHijriInput.dataset.touched !== '1') {
                    addHijriInput.value = approxHijri;
                }
                if (!addNameInput.value || addNameInput.dataset.touched !== '1') {
                    addNameInput.value = `Hajj Season ${yr} (${approxHijri}H)`;
                }
            }
        });

        addHijriInput.addEventListener('input', function () {
            this.dataset.touched = '1';
        });
        addNameInput.addEventListener('input', function () {
            this.dataset.touched = '1';
        });
    }

    const editModalEl = document.getElementById('editSeasonModal');
    const editForm = document.getElementById('editSeasonForm');
    const editModal = new bootstrap.Modal(editModalEl);

    document.querySelectorAll('.btn-edit-season').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const year = this.dataset.year;
            const hijri = this.dataset.hijri;
            const name = this.dataset.name;
            const start = this.dataset.start;
            const end = this.dataset.end;
            const notes = this.dataset.notes;
            const active = this.dataset.active === '1';

            editForm.action = `{{ url('admin/hajj-seasons/update') }}/${id}`;
            document.getElementById('edit_year').value = year || '';
            document.getElementById('edit_hijri_year').value = hijri || '';
            document.getElementById('edit_name').value = name || '';
            document.getElementById('edit_start_date').value = start || '';
            document.getElementById('edit_end_date').value = end || '';
            document.getElementById('edit_notes').value = notes || '';
            document.getElementById('edit_is_active').checked = active;

            editModal.show();
        });
    });
});
</script>
@endpush

@endsection
