@extends('layout.master')

@section('title', 'Bookings')
@section('header-title', 'Bookings')

@section('content')

    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <!-- PAGE TITLE -->
                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Dashboard</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">

                            <!-- HEADER -->
                            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">

                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <h5 class="mb-0 me-2">{{ ucfirst($package) }} Booking List</h5>

                                    {{-- Hajj / Umrah Filter Pills --}}
                                    <div class="btn-group btn-group-sm me-2" role="group">
                                        <a href="{{ route('booking.index', ['package' => 'hajj']) }}"
                                           class="btn {{ $package === 'hajj' ? 'btn-warning text-dark fw-bold' : 'btn-outline-secondary' }}">
                                            <i class="mdi mdi-kaaba me-1"></i>Hajj ({{ $hajjCount ?? 0 }})
                                        </a>
                                        <a href="{{ route('booking.index', ['package' => 'umrah']) }}"
                                           class="btn {{ $package === 'umrah' ? 'btn-success text-white fw-bold' : 'btn-outline-secondary' }}">
                                            <i class="mdi mdi-mosque me-1"></i>Umrah ({{ $umrahCount ?? 0 }})
                                        </a>
                                    </div>

                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-12 px-2 py-1">
                                        <i class="mdi mdi-book-open-outline me-1"></i>Total Bookings: <strong>{{ $bookings->count() }}</strong>
                                    </span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle fs-12 px-2 py-1">
                                        <i class="mdi mdi-account-group me-1"></i>Total Pax: <strong>{{ $totalPax ?? $bookings->sum('no_of_pax') }}</strong>
                                    </span>
                                </div>

                                <div class="d-flex gap-2">

                                    @can('booking_create')
                                        <a href="{{ route('booking.create', ['package_type' => $package]) }}"
                                            class="btn btn-primary btn-sm d-flex gap-1 align-items-center">
                                            <i class="mdi mdi-plus"></i> Add {{ ucfirst($package) }} Booking
                                        </a>
                                    @endcan
                                    @can('booking_trash_view')
                                        <a href="{{ route('booking.trash') }}"
                                            class="btn btn-danger btn-sm d-flex gap-1 align-items-center">
                                            Trash <span>{{ $trashCount ?? 0 }}</span>
                                        </a>
                                    @endcan
                                </div>

                            </div>

                            <!-- BODY -->
                            <div class="card-body">

                                <div class="table-responsive">
                                    <table id="datatable" class="table table-bordered table-hover align-middle w-100 mb-0">

                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" style="width: 55px;">S:NO</th>
                                                <th class="text-center" style="width: 110px;">Booking Date</th>
                                                <th class="text-center" style="width: 100px;">HB #</th>
                                                <th style="min-width: 160px;">Client / Company</th>
                                                <th style="min-width: 180px;">Package Type</th>
                                                <th class="text-center" style="width: 90px;">No of Pax</th>
                                                <th class="text-end" style="width: 125px;">Total Amount</th>
                                                <th class="text-end" style="width: 115px;">Balance</th>
                                                <th class="text-center" style="width: 85px;">Year</th>
                                                <th class="text-center" style="width: 170px; min-width: 170px;">Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($bookings as $booking)
                                                <tr>

                                                    <td class="text-center fw-semibold text-muted">{{ $loop->iteration }}</td>

                                                    <td class="text-center">
                                                        <span class="badge bg-light text-dark border px-2 py-1" style="font-size:11.5px; font-weight:600;">
                                                            <i class="mdi mdi-calendar-blank-outline me-1 text-primary"></i>
                                                            {{ $booking->created_at ? $booking->created_at->format('d M Y') : '—' }}
                                                        </span>
                                                    </td>

                                                    <td class="text-center">
                                                        @php
                                                            $hbList = $booking->persons->pluck('hb_number')->filter()->map(function($val) {
                                                                return \App\Models\BookingPerson::formatHbNumber($val);
                                                            })->filter()->unique()->values();
                                                        @endphp
                                                        @if($hbList->count() > 0)
                                                            @foreach($hbList as $hb)
                                                                <span class="badge bg-dark text-white fw-bold px-2 py-1 me-1 mb-1" style="font-size:11px; letter-spacing:0.5px;">{{ $hb }}</span>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <strong>
                                                            {{ $booking->booking_for === 'company' ? $booking->company->name ?? 'N/A' : $booking->client->name ?? 'N/A' }}
                                                        </strong>
                                                        @if ($booking->booking_for === 'company')
                                                            <span class="badge bg-secondary ms-1"
                                                                style="font-size:10px;">Company</span>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <div>
                                                            @if ($booking->package && ($booking->package->code || $booking->package->package_number))
                                                                <span class="badge bg-dark text-light me-1" style="font-size:10px;">
                                                                    {{ $booking->package->code ?? $booking->package->package_number }}
                                                                </span>
                                                            @endif
                                                            <span class="badge bg-primary">
                                                                {{ $booking->package->package_title ?? $booking->package->name ?? $booking->package_name ?? ucfirst($booking->package_type) }}
                                                            </span>
                                                            @if ($booking->camp)
                                                                <span class="badge bg-info-subtle text-info border border-info-subtle d-block mt-1 text-truncate" style="font-size:10.5px; max-width:180px;">
                                                                    <i class="mdi mdi-tent"></i> {{ $booking->camp }}
                                                                </span>
                                                            @endif
                                                            @if ($booking->qurbani_charges > 0 || ($booking->qurbani_option && $booking->qurbani_option !== 'not_included'))
                                                                <span class="badge bg-success-subtle text-success border border-success-subtle d-block mt-1" style="font-size:10px;">
                                                                    <i class="mdi mdi-sheep"></i> {{ ucfirst(str_replace('_', ' ', $booking->qurbani_option)) }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </td>

                                                    <td class="text-center fw-semibold">{{ $booking->no_of_pax ?? 0 }}</td>

                                                    <td class="text-end fw-semibold">
                                                        {{ number_format($booking->total_amount) }}
                                                    </td>

                                                    <td class="text-end">
                                                        <span class="badge bg-warning text-dark fw-semibold px-2 py-1">
                                                            {{ number_format($booking->balance) }}
                                                        </span>
                                                    </td>

                                                    <td class="text-center">{{ $booking->package_year ?? '—' }}</td>

                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center align-items-center gap-1">

                                                            <!-- VIEW -->
                                                            @can('booking_show')
                                                                <a href="{{ route('booking.show', $booking->id) }}"
                                                                    class="btn btn-sm btn-outline-primary" title="View Details">
                                                                    <i class="mdi mdi-eye"></i>
                                                                </a>
                                                            @endcan

                                                            <!-- EDIT -->
                                                            @can('booking_edit')
                                                                <a href="{{ route('booking.edit', $booking->id) }}"
                                                                    class="btn btn-sm btn-outline-success" title="Edit Booking">
                                                                    <i class="mdi mdi-pencil"></i>
                                                                </a>
                                                            @endcan

                                                            <a href="{{ route('booking.voucher', $booking->id) }}"
                                                                target="_blank" class="btn btn-sm btn-outline-warning"
                                                                title="Hotel Voucher">
                                                                <i class="mdi mdi-file-document-outline"></i>
                                                            </a>

                                                            @can('pilgrim_badge_view')
                                                            <a href="{{ route('pilgrim.bulk-badges', $booking->id) }}"
                                                                target="_blank" class="btn btn-sm btn-outline-dark"
                                                                title="Hajji ID Card">
                                                                <i class="mdi mdi-badge-account-horizontal"></i>
                                                            </a>
                                                            @endcan

                                                            <!-- DELETE -->
                                                            @can('booking_trash')
                                                                <form action="{{ route('booking.delete', $booking->id) }}"
                                                                    method="POST" onsubmit="return confirm('Are you sure?');">
                                                                    @csrf
                                                                    @method('DELETE')

                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-outline-danger" title="Move to Trash">
                                                                        <i class="mdi mdi-delete"></i>
                                                                    </button>
                                                                </form>
                                                            @endcan
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-light fw-bold">
                                            <tr>
                                                <td colspan="5" class="text-end text-uppercase" style="font-size:12px; letter-spacing:0.5px;">Grand Total:</td>
                                                <td class="text-primary fw-bold fs-13 text-center">
                                                    <i class="mdi mdi-account-group me-1"></i>{{ $totalPax ?? $bookings->sum('no_of_pax') }}
                                                </td>
                                                <td class="fw-bold text-end">{{ number_format($bookings->sum('total_amount')) }}</td>
                                                <td class="fw-bold text-danger text-end">{{ number_format($bookings->sum('balance')) }}</td>
                                                <td class="text-center text-muted">—</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#datatable')) {
            $('#datatable').DataTable().columns.adjust();
        }
    });
</script>
@endpush
