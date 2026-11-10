<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Mobile Operations | Baba Hajj Group</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- Favicon & Styles matching master.blade.php -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link href="{{ asset('assets/css/app.min.css') }}?v={{ filemtime(public_path('assets/css/app.min.css')) }}" rel="stylesheet" type="text/css" id="app-style" />
    <link href="{{ asset('assets/css/icons.min.css') }}?v={{ filemtime(public_path('assets/css/icons.min.css')) }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet"
        type="text/css" />

    <!-- Scripts needed early -->
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>

    <style>
        /* ── Header Topbar: Clean Elegant White Theme ── */
        .topbar-custom {
            background: #FFFFFF !important;
            border-bottom: 1px solid #E2E8F0 !important;
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.06) !important;
            height: 70px !important;
            padding: 0 20px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            transition: all 0.2s ease-out !important;
        }

        /* Hamburger Sidebar Toggle Button on White Header */
        .topbar-custom .button-toggle-menu {
            background: #F8FAFC !important;
            border: 1px solid #E2E8F0 !important;
            border-radius: 8px !important;
            color: #1E293B !important;
            width: 40px !important;
            height: 40px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 !important;
            margin: 0 !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
        }

        .topbar-custom .button-toggle-menu:hover {
            background: #D4AF37 !important;
            border-color: #D4AF37 !important;
            color: #FFFFFF !important;
            box-shadow: 0 3px 10px rgba(212, 175, 55, 0.35) !important;
            transform: translateY(-1px) !important;
        }

        .topbar-custom .button-toggle-menu i {
            font-size: 20px !important;
            color: inherit !important;
        }

        /* Mobile Ops Badge in Topbar */
        .mobile-ops-badge {
            background: #F8FAFC !important;
            color: #047857 !important;
            border: 1px solid #A7F3D0 !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            padding: 6px 14px !important;
            border-radius: 20px !important;
            letter-spacing: 0.6px !important;
            box-shadow: 0 1px 3px rgba(4, 120, 87, 0.1) !important;
        }

        /* Back to Admin Gold Button */
        .btn-back-admin {
            background: #0F172A !important;
            color: #F8FAFC !important;
            border: 1px solid #0F172A !important;
            font-weight: 700 !important;
            border-radius: 20px !important;
            padding: 6px 16px !important;
            font-size: 13px !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1) !important;
            transition: all 0.2s ease !important;
        }

        .btn-back-admin:hover {
            background: linear-gradient(135deg, #D4AF37 0%, #B88B28 100%) !important;
            color: #0F172A !important;
            border-color: #B88B28 !important;
            box-shadow: 0 4px 14px rgba(212, 175, 55, 0.4) !important;
            transform: translateY(-1px) !important;
        }

        /* ── Sidebar Logo Box & Clean Collapse on Deep Black Sidebar ── */
        .logo-box {
            height: 76px !important;
            width: 260px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 8px 16px !important;
            background: #090D16 !important; /* Deep Luxury Black */
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            z-index: 1001 !important;
            transition: all 0.2s ease-out !important;
        }

        .sidebar-logo-full {
            height: 56px !important;
            max-height: 56px !important;
            width: auto !important;
            max-width: 215px !important;
            object-fit: contain !important;
            display: block !important;
            margin: 0 auto !important;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.5)) !important;
        }

        .app-sidebar-menu {
            padding-top: 76px !important;
            background: #090D16 !important; /* Deep Luxury Black */
            border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
            transition: all 0.2s ease-out !important;
        }

        #sidebar-menu .menu-link {
            color: #CBD5E1 !important;
            transition: all 0.2s ease !important;
        }
        #sidebar-menu .menu-link:hover {
            color: #FFFFFF !important;
            background: rgba(255, 255, 255, 0.08) !important;
        }
        #sidebar-menu .menu-item.menuitem-active .menu-link {
            background: linear-gradient(90deg, rgba(212, 175, 55, 0.18) 0%, rgba(212, 175, 55, 0.04) 100%) !important;
            color: #F7E7BE !important;
            border-left: 3.5px solid #D4AF37 !important;
        }
        #sidebar-menu .menu-item.menuitem-active .menu-link i {
            color: #D4AF37 !important;
        }

        /* Global Button Hover matching Logo Gold */
        .btn {
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .btn-success:hover,
        .btn-primary:hover,
        .btn-outline-primary:hover,
        .btn-outline-success:hover {
            background: linear-gradient(135deg, #D4AF37 0%, #B88B28 100%) !important;
            border-color: #B88B28 !important;
            color: #0F172A !important;
            font-weight: 700 !important;
            transform: translateY(-1.5px) !important;
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.35) !important;
        }

        body[data-sidebar="hidden"] .logo-box,
        .left-side-menu.condensed .logo-box,
        body[data-sidebar="hidden"] .sidebar-logo-full {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            transform: translateX(-100%) !important;
        }

        body[data-sidebar="hidden"] .app-sidebar-menu {
            width: 0 !important;
            overflow: hidden !important;
            visibility: hidden !important;
        }

        body[data-sidebar="hidden"] .topbar-custom {
            left: 0 !important;
        }

        body[data-sidebar="hidden"] .content-page {
            margin-left: 0 !important;
        }

        body[data-sidebar="hidden"] .logo-box,
        .left-side-menu.condensed .logo-box,
        body[data-sidebar="hidden"] .sidebar-logo-full {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            transform: translateX(-100%) !important;
        }

        body[data-sidebar="hidden"] .app-sidebar-menu {
            width: 0 !important;
            overflow: hidden !important;
            visibility: hidden !important;
        }

        body[data-sidebar="hidden"] .topbar-custom {
            left: 0 !important;
        }

        body[data-sidebar="hidden"] .content-page {
            margin-left: 0 !important;
        }
    </style>
</head>

<body data-menu-color="light" data-sidebar="default">
    <div id="app-layout">

        <!-- Topbar Custom -->
        <div class="topbar-custom">
            <div class="d-flex align-items-center gap-3">
                <!-- Hamburger Sidebar Toggle Button -->
                <button class="button-toggle-menu">
                    <i class="mdi mdi-menu"></i>
                </button>

                <span class="mobile-ops-badge">📱 MOBILE OPERATIONS MODE</span>
            </div>

            <!-- Return Button to Full Admin Panel -->
            <div class="d-flex align-items-center">
                <a href="{{ route('dashboard') }}" class="btn-back-admin">
                    <i class="mdi mdi-arrow-left"></i>
                    <span>Back to Admin Panel</span>
                </a>
            </div>
        </div>

        <!-- Left Sidebar (Clean Shell - No Links Yet) -->
        <div class="app-sidebar-menu">
            <div class="h-100" data-simplebar>
                <div id="sidebar-menu">
                    <div class="logo-box">
                        <a href="{{ route('mobile-ops.dashboard') }}" class="logo">
                            <span class="logo-lg">
                                <img src="{{ asset('assets/images/logo-white.png') }}?v={{ time() }}" class="sidebar-logo-full"
                                    alt="Baba Hajj Group Logo">
                            </span>
                        </a>
                    </div>

                    <ul id="side-menu" class="mt-2">
                        <li class="menu-title text-muted text-uppercase fs-11 px-3 py-2">Mobile Ops Modules</li>

                        @can('mobile_ops_dashboard_view')
                        <li
                            class="menu-item {{ request()->routeIs('mobile-ops.dashboard') ? 'menuitem-active' : '' }}">
                            <a href="{{ route('mobile-ops.dashboard') }}"
                                class="menu-link py-2 px-3 d-flex align-items-center gap-2">
                                <i class="mdi mdi-view-dashboard-outline fs-18"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        @endcan

                        @canany(['umrah_package_view', 'domestic_package_view', 'international_package_view'])
                        @php
                            $isPackageActive =
                                request()->routeIs('mobile-ops.umrah-packages.*') ||
                                request()->routeIs('mobile-ops.domestic-packages.*') ||
                                request()->routeIs('mobile-ops.international-packages.*');
                        @endphp
                        <li class="menu-item {{ $isPackageActive ? 'menuitem-active' : '' }}">
                            <a href="#sidebarMobilePackages" data-bs-toggle="collapse"
                                class="menu-link py-2 px-3 {{ $isPackageActive ? '' : 'collapsed' }}"
                                aria-expanded="{{ $isPackageActive ? 'true' : 'false' }}">
                                <i class="mdi mdi-package-variant-closed fs-18"></i>
                                <span>Package Management</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse {{ $isPackageActive ? 'show' : '' }}" id="sidebarMobilePackages">
                                <ul class="nav-second-level list-unstyled ps-4 py-1">
                                    @can('umrah_package_view')
                                    <li class="py-1">
                                        <a href="{{ route('mobile-ops.umrah-packages.index') }}"
                                            class="py-1 px-2 d-flex align-items-center gap-2 {{ request()->routeIs('mobile-ops.umrah-packages.*') ? 'text-primary fw-bold' : 'text-muted' }}">
                                            <span>&#128507;</span>
                                            <span>Umrah Packages</span>
                                        </a>
                                    </li>
                                    @endcan
                                    @can('domestic_package_view')
                                    <li class="py-1">
                                        <a href="{{ route('mobile-ops.domestic-packages.index') }}"
                                            class="py-1 px-2 d-flex align-items-center gap-2 {{ request()->routeIs('mobile-ops.domestic-packages.*') ? 'text-primary fw-bold' : 'text-muted' }}">
                                            <i class="mdi mdi-map-marker-distance fs-15"></i>
                                            <span>Domestic Packages</span>
                                        </a>
                                    </li>
                                    @endcan
                                    @can('international_package_view')
                                    <li class="py-1">
                                        <a href="{{ route('mobile-ops.international-packages.index') }}"
                                            class="py-1 px-2 d-flex align-items-center gap-2 {{ request()->routeIs('mobile-ops.international-packages.*') ? 'text-primary fw-bold' : 'text-muted' }}">
                                            <i class="mdi mdi-earth fs-15"></i>
                                            <span>International Packages</span>
                                        </a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                        @endcanany

                        @can('mobile_ops_inquiry_view')
                        <li class="menu-item {{ request()->routeIs('mobile-ops.inquiries.*') ? 'menuitem-active' : '' }}">
                            <a href="{{ route('mobile-ops.inquiries.index') }}"
                                class="menu-link py-2 px-3 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="mdi mdi-email-fast-outline fs-18"></i>
                                    <span>Package Inquiries</span>
                                </div>
                                @php
                                    $pendingCount = \App\Models\PackageInquiry::where('status', 'pending')->count();
                                @endphp
                                @if ($pendingCount > 0)
                                    <span
                                        class="badge bg-danger rounded-pill px-2 py-1 fs-10">{{ $pendingCount }}</span>
                                @endif
                            </a>
                        </li>
                        @endcan

                        @can('gallery_view')
                        <li
                            class="menu-item {{ request()->routeIs('mobile-ops.gallery.*') ? 'menuitem-active' : '' }}">
                            <a href="{{ route('mobile-ops.gallery.index') }}"
                                class="menu-link py-2 px-3 d-flex align-items-center gap-2">
                                <i class="mdi mdi-image-multiple-outline fs-18"></i>
                                <span>Ziarat Management</span>
                            </a>
                        </li>
                        @endcan

                        @can('book_view')
                        <li class="menu-item {{ request()->routeIs('mobile-ops.books.*') ? 'menuitem-active' : '' }}">
                            <a href="{{ route('mobile-ops.books.index') }}"
                                class="menu-link py-2 px-3 d-flex align-items-center gap-2">
                                <i class="mdi mdi-book-open-page-variant-outline fs-18"></i>
                                <span>Hajj Books</span>
                            </a>
                        </li>
                        @endcan

                        @can('guideline_view')
                        <li
                            class="menu-item {{ request()->routeIs('mobile-ops.guidelines.*') ? 'menuitem-active' : '' }}">
                            <a href="{{ route('mobile-ops.guidelines.index') }}"
                                class="menu-link py-2 px-3 d-flex align-items-center gap-2">
                                <i class="mdi mdi-information-outline fs-18"></i>
                                <span>Hujjaj Guidelines</span>
                            </a>
                        </li>
                        @endcan

                        @can('video_view')
                        <li class="menu-item {{ request()->routeIs('mobile-ops.videos.*') ? 'menuitem-active' : '' }}">
                            <a href="{{ route('mobile-ops.videos.index') }}"
                                class="menu-link py-2 px-3 d-flex align-items-center gap-2">
                                <i class="mdi mdi-play-circle-outline fs-18"></i>
                                <span>Video Tutorials</span>
                            </a>
                        </li>
                        @endcan

                        @can('visa_country_view')
                        <li
                            class="menu-item {{ request()->routeIs('mobile-ops.visa-countries.*') ? 'menuitem-active' : '' }}">
                            <a href="{{ route('mobile-ops.visa-countries.index') }}"
                                class="menu-link py-2 px-3 d-flex align-items-center gap-2">
                                <i class="mdi mdi-passport fs-18"></i>
                                <span>Visa Countries</span>
                            </a>
                        </li>
                        @endcan

                        @can('visa_inquiry_view')
                        <li
                            class="menu-item {{ request()->routeIs('mobile-ops.visa-inquiries.*') ? 'menuitem-active' : '' }}">
                            <a href="{{ route('mobile-ops.visa-inquiries.index') }}"
                                class="menu-link py-2 px-3 d-flex align-items-center gap-2">
                                <i class="mdi mdi-clipboard-text-clock-outline fs-18"></i>
                                <span>Visa Inquiries</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
            @include('layout.footer')
        </div>

    </div>

    <!-- Core Scripts -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof feather !== "undefined") {
                feather.replace();
            }
        });
    </script>
</body>

</html>
