<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from zoyothemes.com/silva/html/ by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 14 Apr 2026 05:08:02 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->

<head>

    <meta charset="utf-8" />
    <title>Dashboard | Baba Hajj Group</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Baba Hajj Group Management System" />
    <meta name="author" content="Baba Hajj Group" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- App css -->
    <link href="{{ asset('assets/css/app.min.css') }}?v={{ filemtime(public_path('assets/css/app.min.css')) }}" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons -->
    <link href="{{ asset('assets/css/icons.min.css') }}?v={{ filemtime(public_path('assets/css/icons.min.css')) }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-keytable-bs5/css/keyTable.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}"
        rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-select-bs5/css/select.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Core Scripts needed early by inline view scripts -->
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>

    <!-- Custom Styles for Sidebar & Branding (Modern Executive React SaaS Light Theme) -->
    <style>
        /* ══════════════════════════════════════════════════════════════
           LUXURY EXECUTIVE BLACK SIDEBAR & WHITE HEADER THEME
        ══════════════════════════════════════════════════════════════ */
        /* 1. Sidebar Container (Sleek Black Luxury Theme) */
        .app-sidebar-menu {
            padding-top: 76px !important;
            background: #090D16 !important; /* Deep Luxury Black */
            border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: 2px 0 16px rgba(0, 0, 0, 0.35) !important;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        /* 2. Sidebar Logo Box (Deep Black Seamless Card) */
        .logo-box {
            height: 76px !important;
            width: 260px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 8px 16px !important;
            background: #090D16 !important; /* Matches sidebar black */
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            z-index: 1001 !important;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .logo-box .logo {
            line-height: normal !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            height: 100% !important;
            text-decoration: none !important;
        }
        .logo-box .logo span.logo-lg {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            height: 100% !important;
        }
        .sidebar-brand-pill {
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            transition: transform 0.2s ease !important;
        }
        .sidebar-brand-pill:hover {
            transform: scale(1.02) !important;
        }
        .sidebar-brand-pill-sm {
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
            width: 38px !important;
            height: 38px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        /* 3. Ultra-Crisp White Logo Rendering on Black Background */
        .sidebar-logo-full {
            height: 56px !important;
            max-height: 56px !important;
            width: auto !important;
            max-width: 215px !important;
            object-fit: contain !important;
            display: block !important;
            margin: 0 auto !important;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.5)) !important;
            transition: transform 0.2s ease !important;
        }
        .sidebar-logo-full:hover {
            transform: scale(1.03) !important;
        }
        .sidebar-logo-small {
            height: 38px !important;
            max-height: 38px !important;
            width: 38px !important;
            object-fit: contain !important;
        }

        /* ── CRITICAL: Hide logo-box and collapse cleanly when sidebar is closed/hidden ── */
        body[data-sidebar="hidden"] .logo-box,
        .left-side-menu.condensed .logo-box,
        body[data-sidebar="hidden"] .sidebar-logo-full,
        body[data-sidebar="hidden"] .sidebar-logo-small {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            width: 0 !important;
            height: 0 !important;
            pointer-events: none !important;
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

        /* ── Navigation Items on Deep Black Sidebar ── */
        #sidebar-menu {
            padding: 12px 0 32px 0 !important;
        }
        #sidebar-menu .menu-title {
            font-size: 11px !important;
            font-weight: 700 !important;
            letter-spacing: 0.8px !important;
            text-transform: uppercase !important;
            color: #64748B !important;
            padding: 16px 20px 6px 20px !important;
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
        }
        #sidebar-menu .menu-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.08) !important;
            margin-left: 8px;
        }

        /* Menu Items */
        #sidebar-menu > ul > li {
            margin: 3px 12px !important;
        }
        #sidebar-menu > ul > li > a {
            display: flex !important;
            align-items: center !important;
            padding: 9.5px 14px !important;
            color: #CBD5E1 !important; /* Crisp light silver on black */
            font-size: 13.5px !important;
            font-weight: 500 !important;
            border-radius: 9px !important;
            transition: all 0.2s ease !important;
            text-decoration: none !important;
        }
        #sidebar-menu > ul > li > a i,
        #sidebar-menu > ul > li > a svg {
            width: 17px !important;
            height: 17px !important;
            margin-right: 12px !important;
            color: #94A3B8 !important;
            stroke-width: 2 !important;
            transition: all 0.2s ease !important;
            flex-shrink: 0 !important;
        }
        #sidebar-menu > ul > li > a:hover {
            background: rgba(255, 255, 255, 0.08) !important;
            color: #FFFFFF !important;
            transform: translateX(3px) !important;
        }
        #sidebar-menu > ul > li > a:hover i,
        #sidebar-menu > ul > li > a:hover svg {
            color: #D4AF37 !important; /* Gold on hover */
            stroke-width: 2.2 !important;
        }

        /* Active / Selected Menu Item with Brand Gold Accent */
        #sidebar-menu > ul > li.menuitem-active > a,
        #sidebar-menu > ul > li > a.active {
            background: linear-gradient(90deg, rgba(212, 175, 55, 0.18) 0%, rgba(212, 175, 55, 0.04) 100%) !important;
            color: #F7E7BE !important;
            font-weight: 700 !important;
            border-left: 3.5px solid #D4AF37 !important;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
        }
        #sidebar-menu > ul > li.menuitem-active > a i,
        #sidebar-menu > ul > li > a.active i,
        #sidebar-menu > ul > li.menuitem-active > a svg,
        #sidebar-menu > ul > li > a.active svg {
            color: #D4AF37 !important;
            stroke-width: 2.2 !important;
        }

        /* Arrow / Chevron for collapse */
        #sidebar-menu .menu-arrow {
            margin-left: auto !important;
            color: #64748B !important;
            font-size: 11px !important;
            transition: transform 0.2s ease !important;
        }

        /* Submenus on Black Sidebar */
        .nav-second-level {
            padding-left: 0 !important;
            margin: 3px 0 6px 24px !important;
            border-left: 1.5px solid rgba(255, 255, 255, 0.1) !important;
        }
        .nav-second-level li {
            margin: 1px 0 !important;
        }
        .nav-second-level li a {
            padding: 6.5px 14px !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            color: #94A3B8 !important;
            border-radius: 6px !important;
            display: block !important;
            transition: all 0.15s ease !important;
            text-decoration: none !important;
        }
        .nav-second-level li a:hover {
            color: #FFFFFF !important;
            background: rgba(255, 255, 255, 0.06) !important;
            transform: translateX(2px) !important;
        }
        .nav-second-level li a.active,
        .nav-second-level li.active > a {
            color: #F7E7BE !important;
            font-weight: 700 !important;
            background: rgba(212, 175, 55, 0.12) !important;
            border-left: 2px solid #D4AF37 !important;
        }

        /* Scrollbar on black sidebar */
        .app-sidebar-menu [data-simplebar] .simplebar-scrollbar:before {
            background: rgba(255, 255, 255, 0.25) !important;
            border-radius: 4px !important;
        }

        /* ── Header Topbar: Clean White Theme ── */
        .topbar-custom {
            background: #FFFFFF !important;
            border-bottom: 1px solid #E2E8F0 !important;
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.06) !important;
            height: 76px !important;
            padding: 0 20px !important;
            transition: all 0.2s ease-out !important;
        }

        /* Topbar Baba Hajj Group Badge & User text */
        .topbar-custom .topbar-badge-baba {
            background: #F8FAFC !important;
            color: #8C6D1F !important;
            border: 1px solid #E2E8F0 !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            padding: 5px 12px !important;
            border-radius: 20px !important;
            letter-spacing: 0.5px !important;
        }
        .topbar-custom .topbar-user-heading {
            color: #0F172A !important;
            font-size: 15px !important;
            font-weight: 700 !important;
            letter-spacing: 0.3px !important;
        }

        /* Live Pulsing Dot for Mobile Ops */
        .live-pulse-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            background: #10B981;
            border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulseGreen 1.8s infinite;
            margin-right: 4px;
        }
        @keyframes pulseGreen {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* ════ Topbar Brand Pill, Search & Date Capsule ════ */
        .topbar-portal-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: #090D16;
            color: #FFFFFF;
            border: 1px solid rgba(212, 175, 55, 0.4);
            padding: 5px 12px;
            border-radius: 9px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }
        .portal-pulse-gold {
            width: 7px;
            height: 7px;
            background: #D4AF37;
            border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.7);
            animation: pulseGold 2s infinite;
        }
        @keyframes pulseGold {
            0% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.7); }
            70% { box-shadow: 0 0 0 6px rgba(212, 175, 55, 0); }
            100% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0); }
        }
        .portal-brand-text {
            font-size: 11.5px;
            font-weight: 800;
            letter-spacing: 0.6px;
            color: #F7E7BE;
        }
        .portal-badge-year {
            font-size: 10px;
            font-weight: 700;
            background: rgba(212, 175, 55, 0.2);
            color: #ECC870;
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid rgba(212, 175, 55, 0.3);
        }

        /* Topbar Global Search */
        .topbar-search-wrap {
            position: relative;
            width: 280px;
        }
        .topbar-search-input {
            width: 100%;
            height: 38px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            padding: 6px 65px 6px 36px;
            font-size: 12.5px;
            color: #0F172A;
            transition: all 0.2s ease;
            outline: none;
        }
        .topbar-search-input:focus {
            background: #FFFFFF;
            border-color: #D4AF37;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15);
        }
        .topbar-search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            font-size: 14px;
            pointer-events: none;
        }
        .topbar-search-badge {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: #FFFFFF;
            border: 1px solid #CBD5E1;
            color: #64748B;
            font-size: 10.5px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 6px;
            letter-spacing: 0.3px;
        }

        /* Topbar Date Capsule */
        .topbar-date-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
        }
        .topbar-hijri-year {
            color: #8F6F25;
            font-family: serif;
            font-size: 13px;
            font-weight: 700;
        }

        /* Topbar Booking Button */
        .topbar-btn-booking {
            background: linear-gradient(135deg, #090D16 0%, #172033 100%);
            color: #F7E7BE !important;
            border: 1px solid #C5A059;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 9px;
            box-shadow: 0 2px 6px rgba(9, 13, 22, 0.15);
            transition: all 0.22s ease;
            text-decoration: none;
        }
        .topbar-btn-booking:hover {
            background: linear-gradient(135deg, #D4AF37 0%, #B88E28 100%);
            color: #090D16 !important;
            border-color: #D4AF37;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(212, 175, 55, 0.35);
        }

        /* Hamburger Sidebar Toggle Button on White Header */
        .topbar-custom .button-toggle-menu {
            background: #F8FAFC !important;
            border: 1px solid #E2E8F0 !important;
            border-radius: 9px !important;
            color: #1E293B !important;
            width: 38px !important;
            height: 38px !important;
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
            transform: translateY(-1px) !important;
            box-shadow: 0 3px 10px rgba(212, 175, 55, 0.35) !important;
        }
        .topbar-custom .button-toggle-menu i,
        .topbar-custom .button-toggle-menu svg {
            width: 20px !important;
            height: 20px !important;
            stroke-width: 2 !important;
            color: inherit !important;
        }

        /* Topbar User Profile Pill on White Header */
        .topbar-custom .nav-user {
            background: #F8FAFC !important;
            border: 1px solid #E2E8F0 !important;
            padding: 4px 14px 4px 6px !important;
            border-radius: 30px !important;
            display: inline-flex !important;
            align-items: center !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
        }
        .topbar-custom .nav-user:hover {
            background: #FFFFFF !important;
            border-color: #D4AF37 !important;
            box-shadow: 0 3px 10px rgba(212, 175, 55, 0.25) !important;
            transform: translateY(-1px) !important;
        }
        .topbar-custom .nav-user .user-avatar-top,
        .topbar-custom .nav-user img {
            width: 32px !important;
            height: 32px !important;
            object-fit: cover !important;
            border: 1.5px solid #D4AF37 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
        }
        .topbar-custom .nav-user .pro-user-name {
            color: #0F172A !important;
            font-weight: 700 !important;
            font-size: 13.5px !important;
        }
        .topbar-custom .nav-user i {
            color: #64748B !important;
            font-size: 14px !important;
            margin-left: 3px !important;
        }

        /* Profile Dropdown & Logout Menu Item */
        .profile-dropdown {
            background: #FFFFFF !important;
            border-radius: 12px !important;
            border: 1px solid #E2E8F0 !important;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.12) !important;
            padding: 8px !important;
            min-width: 210px !important;
        }
        .profile-dropdown .noti-title {
            background: #F8FAFC !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
        }
        .profile-dropdown .noti-title small {
            color: #94A3B8 !important;
        }
        .profile-dropdown .noti-title h6 {
            color: #0F172A !important;
        }
        .profile-dropdown .dropdown-divider {
            border-color: #F1F5F9 !important;
        }
        .profile-dropdown .dropdown-item {
            border-radius: 8px !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            color: #334155 !important;
            padding: 8px 12px !important;
            transition: all 0.18s ease !important;
        }
        .profile-dropdown .dropdown-item:hover {
            background: #FEF8EC !important;
            color: #8C6D1F !important;
        }
        .profile-dropdown .dropdown-item i.mdi-lock-reset {
            color: #D4AF37 !important;
        }
        .profile-dropdown .logout-menu-btn {
            color: #EF4444 !important;
            font-weight: 600 !important;
            background: #FEF2F2 !important;
            border: none !important;
            width: 100% !important;
            text-align: left !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
            transition: all 0.18s ease !important;
        }
        .profile-dropdown .logout-menu-btn:hover {
            background: #DC2626 !important;
            color: #FFFFFF !important;
        }
        .profile-dropdown .logout-menu-btn:hover i {
            color: #FFFFFF !important;
        }

        /* ══════════════════════════════════════════════════════════════
           GLOBAL BUTTON THEME & HOVER EFFECTS (MATCHING LOGO GOLD ACCENTS)
        ══════════════════════════════════════════════════════════════ */
        .btn {
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        /* Primary / Create / Update / Submit / Next Buttons */
        .btn-primary,
        .btn-primary:active,
        .btn-primary:focus,
        .btn-next,
        .btn-save-booking {
            background: #090D16 !important;
            color: #F7E7BE !important;
            border: 1px solid #D4AF37 !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25) !important;
            transition: all 0.2s ease !important;
        }
        .btn-primary:hover,
        .btn-next:hover,
        .btn-save-booking:hover {
            background: linear-gradient(135deg, #D4AF37 0%, #B88E28 100%) !important;
            color: #0F172A !important;
            border-color: #B88B28 !important;
            box-shadow: 0 4px 14px rgba(212, 175, 55, 0.45) !important;
            transform: translateY(-1.5px) !important;
        }

        /* Secondary / Back / Cancel Buttons */
        .btn-secondary,
        .btn-outline-secondary {
            background: #F8FAFC !important;
            border: 1px solid #CBD5E1 !important;
            color: #334155 !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
            transition: all 0.2s ease !important;
        }
        .btn-secondary:hover,
        .btn-outline-secondary:hover {
            background: #090D16 !important;
            color: #D4AF37 !important;
            border-color: #D4AF37 !important;
            transform: translateY(-1px) !important;
        }

        /* Warning / Gold Action Buttons */
        .btn-warning,
        .btn-outline-warning {
            background: linear-gradient(135deg, #D4AF37 0%, #B88E28 100%) !important;
            color: #0F172A !important;
            border: 1px solid #B88E28 !important;
            font-weight: 700 !important;
            border-radius: 6px !important;
            box-shadow: 0 2px 8px rgba(212, 175, 55, 0.3) !important;
            transition: all 0.2s ease !important;
        }
        .btn-warning:hover,
        .btn-outline-warning:hover {
            background: #090D16 !important;
            color: #D4AF37 !important;
            border-color: #D4AF37 !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35) !important;
            transform: translateY(-1px) !important;
        }

        /* Success / Excel / Download Buttons */
        .btn-success,
        .btn-outline-success {
            background: linear-gradient(135deg, #064E3B 0%, #047857 100%) !important;
            color: #ECFDF5 !important;
            border: 1px solid #10B981 !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
            box-shadow: 0 2px 8px rgba(4, 120, 87, 0.25) !important;
            transition: all 0.2s ease !important;
        }
        .btn-success:hover,
        .btn-outline-success:hover {
            background: linear-gradient(135deg, #D4AF37 0%, #B88E28 100%) !important;
            color: #0F172A !important;
            border-color: #B88B28 !important;
            box-shadow: 0 4px 14px rgba(212, 175, 55, 0.45) !important;
            transform: translateY(-1.5px) !important;
        }

        /* Outlined Buttons on Dark / Table Context */
        .btn-outline-primary {
            background: #FFFFFF !important;
            color: #090D16 !important;
            border: 1.5px solid #CBD5E1 !important;
            font-weight: 600 !important;
        }
        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #D4AF37 0%, #B88E28 100%) !important;
            color: #0F172A !important;
            border-color: #B88B28 !important;
            box-shadow: 0 4px 14px rgba(212, 175, 55, 0.4) !important;
            transform: translateY(-1.5px) !important;
        }

        /* ── GLOBAL INPUT FOCUS & TAB NAVIGATION HIGHLIGHT (Active Blue Accent) ── */
        .form-control:focus,
        .form-select:focus,
        input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]):not([type="reset"]):focus,
        textarea:focus {
            border-color: #0d6efd !important;
            background-color: #f0f7ff !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25), 0 0 8px rgba(13, 110, 253, 0.3) !important;
            outline: 0 !important;
            transition: all 0.15s ease-in-out !important;
        }

        /* Select2 Bootstrap 5 Crisp Styling */
        /* In-page control: must fill 100% width of parent column/card */
        .select2.select2-container,
        .select2.select2-container--bootstrap-5,
        .select2.select2-container--default {
            width: 100% !important;
            max-width: 100% !important;
            display: block !important;
        }

        .select2-container--bootstrap-5 .select2-selection,
        .select2-container--default .select2-selection--single {
            min-height: 38px !important;
            height: 38px !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 0.375rem !important;
            padding: 0.25rem 0.5rem !important;
            font-size: 0.875rem !important;
            display: flex !important;
            align-items: center !important;
            background-color: #fff !important;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out !important;
        }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered,
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #212529 !important;
            line-height: 34px !important;
            padding-left: 6px !important;
            font-size: 13.5px !important;
        }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow,
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
            top: 1px !important;
            right: 8px !important;
        }
        .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .select2-container--bootstrap-5.select2-container--open .select2-selection,
        .select2-container--default.select2-container--focus .select2-selection,
        .select2-container--default.select2-container--open .select2-selection {
            border-color: #0d6efd !important;
            background-color: #fff !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
            outline: 0 !important;
        }

        /* Small select variants for table/repeater rows (sm) */
        .form-select-sm + .select2-container .select2-selection,
        .select2-container--bootstrap-5.select2-container--sm .select2-selection {
            min-height: 31px !important;
            height: 31px !important;
            padding: 0.15rem 0.4rem !important;
            font-size: 0.8125rem !important;
        }
        .form-select-sm + .select2-container .select2-selection .select2-selection__rendered,
        .select2-container--bootstrap-5.select2-container--sm .select2-selection .select2-selection__rendered {
            line-height: 28px !important;
            font-size: 12.5px !important;
        }
        .form-select-sm + .select2-container .select2-selection .select2-selection__arrow,
        .select2-container--bootstrap-5.select2-container--sm .select2-selection .select2-selection__arrow {
            height: 29px !important;
        }

        /* Open dropdown popup container: must use Select2 calculated width and NEVER collapse to 0 */
        .select2-container--open {
            z-index: 99999 !important;
        }
        .select2-container.select2-container--open:not(.select2) {
            max-width: 100vw !important;
        }
        .select2-container--open .select2-dropdown {
            box-sizing: border-box !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
            background-color: #ffffff !important;
            z-index: 99999 !important;
        }
        .select2-search--dropdown .select2-search__field {
            border: 1px solid #dee2e6 !important;
            border-radius: 0.25rem !important;
            padding: 6px 10px !important;
            font-size: 13px !important;
        }
        .select2-results__option {
            padding: 6px 12px !important;
            font-size: 13px !important;
            color: #212529 !important;
            white-space: normal !important;
            word-break: break-word !important;
        }
        .select2-results__option--highlighted {
            background-color: #0d6efd !important;
            color: #fff !important;
        }

        div:focus-within > label.form-label,
        .col-md-2:focus-within > label.form-label,
        .col-md-3:focus-within > label.form-label,
        .col-md-4:focus-within > label.form-label,
        .col-md-6:focus-within > label.form-label,
        .col-md-12:focus-within > label.form-label,
        .mb-2:focus-within > label.form-label,
        .mb-3:focus-within > label.form-label {
            color: #0d6efd !important;
            font-weight: 700 !important;
            transition: color 0.15s ease-in-out !important;
        }
    </style>
</head>

<!-- body start -->

<body data-menu-color="light" data-sidebar="default">
    <div id="app-layout">


        @include('layout.header')

        @yield('content')

        @include('layout.footer')

        <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
        <script src="{{ asset('assets/libs/waypoints/lib/jquery.waypoints.min.js') }}"></script>
        <script src="{{ asset('assets/libs/jquery.counterup/jquery.counterup.min.js') }}"></script>
        <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>


        <!-- Apexcharts JS -->
        <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

        <!-- for basic area chart -->
        <script src="{{ asset('assets/apexcharts.com/samples/assets/stock-prices.js') }}"></script>

        <!-- Widgets Init Js -->
        <script src="{{ asset('assets/js/pages/crm-dashboard.init.js') }}"></script>

        <!-- App js-->
        <script src="{{ asset('assets/js/app.js') }}"></script>
        <!-- Datatable Demo App Js -->
        <script src="{{ asset('assets/js/pages/datatable.init.js') }}"></script>

        <!-- App js-->

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                if (typeof feather !== "undefined") {
                    feather.replace();
                }
            });

            // Auto-focus search input when Select2 dropdown opens
            $(document).on('select2:open', function() {
                setTimeout(function() {
                    const searchInput = document.querySelector('.select2-container--open .select2-search__field');
                    if (searchInput) {
                        searchInput.focus();
                    }
                }, 10);
            });

            // Ensure Select2 selection dispatches native change event for vanilla event listeners
            $(document).on('select2:select select2:unselect select2:clear', 'select', function() {
                this.dispatchEvent(new Event('change', { bubbles: true }));
            });
        </script>

        @stack('scripts')
</body>


</html>
