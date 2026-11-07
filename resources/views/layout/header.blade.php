    <!-- Topbar Start -->
    <div class="topbar-custom">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
                    <li>
                        <button class="button-toggle-menu nav-link" aria-label="Toggle Sidebar">
                            <i data-feather="menu" class="noti-icon"></i>
                        </button>
                    </li>
                    <li class="d-none d-lg-block ms-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge"
                                style="background: rgba(201, 168, 76, 0.18); color: #F3DC9B; border: 1px solid rgba(201, 168, 76, 0.45); font-size: 11px; font-weight: 700; padding: 5px 12px; border-radius: 20px; letter-spacing: 0.5px;">
                                🕋 BABA HAJJ GROUP
                            </span>
                            <h5 class="mb-0 text-white fw-bold" style="font-size: 15px; letter-spacing: 0.3px;">
                                {{ Auth::user()->name ?? 'Admin User' }}
                            </h5>
                        </div>
                    </li>
                </ul>

                <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center gap-2">
                    @canany(['mobile_ops_dashboard_view', 'umrah_package_view', 'domestic_package_view', 'international_package_view', 'mobile_ops_inquiry_view', 'gallery_view', 'book_view', 'guideline_view', 'video_view', 'visa_country_view', 'visa_inquiry_view'])
                    <li class="d-none d-lg-block">
                        <div class="position-relative">
                            <a href="{{ route('mobile-ops.dashboard') }}"
                                class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1 me-3"
                                style="border-radius: 20px; font-weight: 600; padding: 5px 14px; border-color: rgba(16, 185, 129, 0.4);">
                                <span class="live-pulse-dot"></span>
                                <span>Mobile App Portal</span>
                            </a>
                        </div>
                    </li>
                    @endcanany

                    <li class="dropdown notification-list topbar-dropdown">
                        <a class="nav-link dropdown-toggle nav-user me-0" data-bs-toggle="dropdown" href="#"
                            role="button" aria-haspopup="false" aria-expanded="false">
                            <img src="{{ asset('assets/images/users/user-5.jpg') }}" alt="user-image"
                                class="rounded-circle user-avatar-top">
                            <span class="pro-user-name ms-1">
                                {{ Auth::user()->name ?? 'Admin User' }}
                                <i class="mdi mdi-chevron-down"></i>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end profile-dropdown shadow-lg">
                            <div class="dropdown-header noti-title py-2 px-3">
                                <small class="text-uppercase fw-bold text-muted"
                                    style="letter-spacing: 0.5px; font-size: 10.5px;">Signed in as</small>
                                <h6 class="text-overflow m-0 fw-bold text-dark" style="font-size: 13.5px;">
                                    {{ Auth::user()->name ?? 'Admin' }}</h6>
                            </div>

                            <div class="dropdown-divider my-1"></div>

                            <a href="{{ route('change.password') }}"
                                class="dropdown-item notify-item d-flex align-items-center gap-2 py-2">
                                <i class="mdi mdi-lock-reset fs-16 text-primary"></i>
                                <span>Change Password</span>
                            </a>

                            <div class="dropdown-divider my-1"></div>

                            <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                                @csrf
                                <button type="submit"
                                    class="dropdown-item notify-item logout-menu-btn d-flex align-items-center gap-2 py-2">
                                    <i class="mdi mdi-logout fs-16 text-danger"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="app-sidebar-menu">
        <div class="h-100" data-simplebar>
            <div id="sidebar-menu">
                <div class="logo-box text-center">
                    <a class="logo logo-dark" href="{{ route('dashboard') }}">
                        <span class="logo-lg">
                            <span class="sidebar-brand-pill">
                                <img src="{{ asset('assets/images/baba_hajj_logo_ultra.png') }}?v={{ time() }}"
                                    alt="Baba Hajj Group" class="sidebar-logo-full">
                            </span>
                        </span>
                        <span class="logo-sm">
                            <span class="sidebar-brand-pill-sm">
                                <img src="{{ asset('assets/images/favicon-emblem.png') }}?v={{ time() }}"
                                    alt="Baba Hajj Group" class="sidebar-logo-small">
                            </span>
                        </span>
                    </a>
                </div>
                <ul id="side-menu">
                    <li class="menu-title">Menu</li>
                    <li>
                        <a href="{{ route('dashboard') }}">
                            <i data-feather="home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    @canany(['role_view', 'user_view', 'user_activity_view'])
                    <li>
                        <a href="#sidebarUserManagement" data-bs-toggle="collapse">
                            <i data-feather="users"></i>
                            <span>User Management</span>
                            <span class="menu-arrow"></span>
                        </a>

                        <div class="collapse" id="sidebarUserManagement">
                            <ul class="nav-second-level">

                                @can('role_view')
                                    <li>
                                        <a class="tp-link" href="{{ route('role.index') }}">
                                            Role
                                        </a>
                                    </li>
                                @endcan

                                @can('user_view')
                                    <li>
                                        <a class="tp-link" href="{{ route('user.index') }}">
                                            User
                                        </a>
                                    </li>
                                @endcan
                                @can('user_activity_view')
                                    <li>
                                        <a class="tp-link" href="{{ route('user_activity.index') }}">
                                            User Activity
                                        </a>
                                    </li>
                                @endcan

                            </ul>
                        </div>
                    </li>
                    @endcanany

                    @canany(['lead_view', 'lead_create'])
                    <li>
                        <a href="#sidebarLeadManagement" data-bs-toggle="collapse">
                            <i data-feather="user-plus"></i>
                            <span>Lead Management</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarLeadManagement">
                            <ul class="nav-second-level">
                                @can('lead_view')
                                <li>
                                    <a class="tp-link" href="{{ route('lead.index') }}">
                                        All Leads
                                    </a>
                                </li>
                                @endcan
                                @can('lead_create')
                                <li>
                                    <a class="tp-link" href="{{ route('lead.create') }}">
                                        Add Lead
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                    @endcanany

                    @can('client_view')
                        <li>
                            <a href="{{ route('client.index') }}">
                                <i data-feather="user-plus"></i>
                                <span>Clients</span>
                            </a>
                        </li>
                    @endcan

                    {{-- Invoices --}}
                    @canany(['invoice_view', 'invoice_create'])
                    @php
                        $isInvoiceActive = request()->routeIs('invoice.*');
                    @endphp
                    <li class="{{ $isInvoiceActive ? 'menuitem-active' : '' }}">
                        <a href="#sidebarInvoices" data-bs-toggle="collapse" class="{{ $isInvoiceActive ? 'active' : '' }}"
                            aria-expanded="{{ $isInvoiceActive ? 'true' : 'false' }}">
                            <i data-feather="file-text"></i>
                            <span>Invoices</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse {{ $isInvoiceActive ? 'show' : '' }}" id="sidebarInvoices">
                            <ul class="nav-second-level">
                                @can('invoice_view')
                                <li class="{{ request()->routeIs('invoice.index') ? 'menuitem-active' : '' }}">
                                    <a class="tp-link {{ request()->routeIs('invoice.index') ? 'active' : '' }}"
                                        href="{{ route('invoice.index') }}">
                                        <span>All Invoices</span>
                                    </a>
                                </li>
                                @endcan
                                @can('invoice_create')
                                <li class="{{ request()->routeIs('invoice.create') ? 'menuitem-active' : '' }}">
                                    <a class="tp-link {{ request()->routeIs('invoice.create') ? 'active' : '' }}"
                                        href="{{ route('invoice.create') }}">
                                        <span>+ Create Invoice</span>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                    @endcanany
                    @can('company_view')
                    <li>
                        <a href="{{ route('company.index') }}">
                            <i data-feather="briefcase"></i>
                            <span>Referrals</span>
                        </a>
                    </li>
                    @endcan
                    @can('supplier_view')
                    <li>
                        <a href="{{ route('supplier.index') }}">
                            <i data-feather="truck"></i>
                            <span>Suppliers / Vendors</span>
                        </a>
                    </li>
                    @endcan
                    @canany(['package_view', 'hotel_view', 'room_type_view', 'flight_view', 'route_view', 'travel_route_view', 'train_view', 'vehicle_view', 'training_session_view'])
                    <li>
                        <a href="#sidebarPackageManagement" data-bs-toggle="collapse">
                            <i data-feather="package"></i>
                            <span>Package Management</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarPackageManagement">
                            <ul class="nav-second-level">
                                @can('package_view')
                                <li>
                                    <a class="tp-link" href="{{ route('package.index') }}">
                                        Packages
                                    </a>
                                </li>
                                @endcan
                                @can('hotel_view')
                                    <li>
                                        <a class="tp-link" href="{{ route('hotel.index') }}">
                                            Hotels
                                        </a>
                                    </li>
                                @endcan
                                @can('room_type_view')
                                <li>
                                    <a class="tp-link" href="{{ route('room-type.index') }}">
                                        Room Types
                                    </a>
                                </li>
                                @endcan

                                @can('flight_view')
                                    <li>
                                        <a class="tp-link" href="{{ route('flight.index') }}">
                                            Flights
                                        </a>
                                    </li>
                                @endcan
                                @can('route_view')
                                    <li>
                                        <a class="tp-link" href="{{ route('route.index') }}">
                                            Routes
                                        </a>
                                    </li>
                                @endcan
                                @can('travel_route_view')
                                    <li>
                                        <a class="tp-link" href="{{ route('travel-route.index') }}">
                                            Travel Routes
                                        </a>
                                    </li>
                                @endcan
                                @can('train_view')
                                    <li>
                                        <a class="tp-link" href="{{ route('train.index') }}">
                                            Trains
                                        </a>
                                    </li>
                                @endcan
                                @can('vehicle_view')
                                    <li>
                                        <a class="tp-link" href="{{ route('vehicle.index') }}">
                                            Vehicles
                                        </a>
                                    </li>
                                @endcan
                                @can('training_session_view')
                                <li>
                                    <a class="tp-link" href="{{ route('training-session.index') }}">
                                        Training Sessions
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                    @endcanany
                    @can('booking_view')
                        <li>
                            <a href="{{ route('booking.index') }}">
                                <i data-feather="calendar"></i>
                                <span>Booking</span>
                            </a>
                        </li>
                    @endcan
                    @can('person_report_view')
                    <li>
                        <a href="{{ route('report.person') }}">
                            <i data-feather="user-check"></i>
                            <span>Person Report</span>
                        </a>
                    </li>
                    @endcan
                    @can('flight_report_view')
                    <li>
                        <a href="{{ route('report.flight') }}">
                            <i data-feather="navigation"></i>
                            <span>Flight Report</span>
                        </a>
                    </li>
                    @endcan
                    @can('haji_group_view')
                    <li>
                        <a href="{{ route('haji-group.index') }}">
                            <i data-feather="users"></i>
                            <span>Haji Pilgrim Groups</span>
                        </a>
                    </li>
                    @endcan
                    @can('room_inventory_view')
                    <li>
                        <a href="{{ route('room-inventory.index') }}">
                            <i data-feather="home"></i>
                            <span>Rooming List & Stock</span>
                        </a>
                    </li>
                    @endcan
                    @can('hotel_sale_view')
                    <li>
                        <a href="{{ route('hotel-sales.index') }}">
                            <i data-feather="file-text"></i>
                            <span>Hotel Sale Invoices</span>
                        </a>
                    </li>
                    @endcan
                    @can('rooming_list_report_view')
                    <li>
                        <a href="{{ route('report.rooming-list') }}">
                            <i data-feather="grid"></i>
                            <span>Room Allocation Report</span>
                        </a>
                    </li>
                    @endcan
                    @can('bus_assignment_view')
                    <li>
                        <a href="{{ route('transport.bus-assignment') }}">
                            <i data-feather="truck"></i>
                            <span>Bus Assignment & Manifest</span>
                        </a>
                    </li>
                    @endcan
                    @can('check_in_report_view')
                    <li>
                        <a href="{{ route('report.check-in') }}">
                            <i data-feather="check-square"></i>
                            <span>Check-In Report</span>
                        </a>
                    </li>
                    @endcan
                    @can('hotel_person_report_view')
                    <li>
                        <a href="{{ route('report.hotel-person') }}">
                            <i data-feather="home"></i>
                            <span>Hotel Person Report</span>
                        </a>
                    </li>
                    @endcan
                    @can('maktab_report_view')
                    <li>
                        <a href="{{ route('report.maktab') }}">
                            <i data-feather="layers"></i>
                            <span>Maktab - report</span>
                        </a>
                    </li>
                    @endcan
                    @can('arrival_group_view')
                    <li>
                        <a href="{{ route('arrival-group.index') }}">
                            <i data-feather="arrow-down-left"></i>
                            <span>Arrival Groups</span>
                        </a>
                    </li>
                    @endcan
                    @can('departure_group_view')
                    <li>
                        <a href="{{ route('departure-group.index') }}">
                            <i data-feather="arrow-up-right"></i>
                            <span>Departure Groups</span>
                        </a>
                    </li>
                    @endcan
                    @can('hajj_application_view')
                    <li>
                        <a href="{{ route('hajj-application.index') }}">
                            <i data-feather="file-text"></i>
                            <span>Hajj Applications</span>
                        </a>
                    </li>
                    @endcan
                    @can('expense_view')
                        <li>
                            <a href="{{ route('expense.index') }}">
                                <i data-feather="dollar-sign"></i>
                                <span>Add Expense</span>
                            </a>
                        </li>
                    @endcan
                    @can('expense_transaction_view')
                        <li>
                            <a href="{{ route('expense.transaction.index') }}">
                                <i data-feather="credit-card"></i>
                                <span>Expense Transactions</span>
                            </a>
                        </li>
                    @endcan

                    @can('client_Transactions_view')
                        <li>
                            <a href="{{ route('transaction.index') }}">
                                <i data-feather="list"></i>
                                <span>Transactions</span>
                            </a>
                        </li>
                    @endcan
                    @can('Ledger_Filter_view')
                        <li>
                            <a href="{{ route('transaction.ledger.filter') }}">
                                <i data-feather="filter"></i>
                                <span>Ledger Filter</span>
                            </a>
                        </li>
                    @endcan
                    @can('expense_filter_view')
                    <li>
                        <a href="{{ route('expense.transaction.report.filter') }}">
                            <i data-feather="filter"></i>
                            <span>Expense Filter</span>
                        </a>
                    </li>
                    @endcan

                    @can('company_ledger_filter_view')
                    <li>
                        <a href="{{ route('transaction.company-ledger.filter') }}">
                            <i data-feather="filter"></i>
                            <span>Referral Filter</span>
                        </a>
                    </li>
                    @endcan

                    {{-- Banks & Reports --}}
                    @canany(['bank_view', 'bank_create', 'bank_report_view'])
                    @php
                        $isBankActive = request()->routeIs('bank.*') || request()->routeIs('bank-report.*');
                    @endphp
                    <li class="{{ $isBankActive ? 'menuitem-active' : '' }}">
                        <a href="#sidebarBanks" data-bs-toggle="collapse" class="{{ $isBankActive ? 'active' : '' }}"
                            aria-expanded="{{ $isBankActive ? 'true' : 'false' }}">
                            <i data-feather="dollar-sign"></i>
                            <span>Bank Accounts</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse {{ $isBankActive ? 'show' : '' }}" id="sidebarBanks">
                            <ul class="nav-second-level">
                                @can('bank_view')
                                <li class="{{ request()->routeIs('bank.index') ? 'menuitem-active' : '' }}">
                                    <a class="tp-link {{ request()->routeIs('bank.index') ? 'active' : '' }}"
                                        href="{{ route('bank.index') }}">
                                        <span>Banks Management</span>
                                    </a>
                                </li>
                                @endcan
                                @can('bank_report_view')
                                <li class="{{ request()->routeIs('bank-report.*') ? 'menuitem-active' : '' }}">
                                    <a class="tp-link {{ request()->routeIs('bank-report.*') ? 'active' : '' }}"
                                        href="{{ route('bank-report.filter') }}">
                                        <span>Bank Invoices Report</span>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                    @endcanany
                </ul>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
