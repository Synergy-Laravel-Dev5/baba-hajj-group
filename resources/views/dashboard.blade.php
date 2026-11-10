@extends('layout.master')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap');

        :root {
            --react-bg: #F8FAFC;
            --react-card: #FFFFFF;
            --react-border: #E2E8F0;
            --react-border-hover: #CBD5E1;
            --react-text-main: #0F172A;
            --react-text-muted: #64748B;
            --react-text-sub: #94A3B8;

            --gold-primary: #C5A059;
            --gold-light: #F4E5BE;
            --gold-subtle: #FEF9EE;
            --gold-dark: #8F6F25;
            --gold-gradient: linear-gradient(135deg, #C5A059 0%, #DFBE76 50%, #B88E28 100%);

            --emerald-primary: #059669;
            --emerald-subtle: #ECFDF5;
            --emerald-dark: #047857;
            --emerald-gradient: linear-gradient(135deg, #059669 0%, #10B981 50%, #047857 100%);

            --indigo-primary: #4F46E5;
            --indigo-subtle: #EEF2FF;
            --cyan-primary: #0284C7;
            --cyan-subtle: #F0F9FF;
            --amber-primary: #D97706;
            --amber-subtle: #FFFBEB;
            --rose-primary: #E11D48;
            --rose-subtle: #FFF1F2;

            /* Package active dynamic accent */
            --pkg-accent: {{ $package === 'hajj' ? '#C5A059' : '#059669' }};
            --pkg-subtle: {{ $package === 'hajj' ? '#FEF9EE' : '#ECFDF5' }};
            --pkg-border: {{ $package === 'hajj' ? 'rgba(197, 160, 89, 0.4)' : 'rgba(5, 150, 105, 0.4)' }};
            --pkg-glow: {{ $package === 'hajj' ? 'rgba(197, 160, 89, 0.18)' : 'rgba(5, 150, 105, 0.18)' }};
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            background-color: var(--react-bg) !important;
            color: var(--react-text-main) !important;
            -webkit-font-smoothing: antialiased;
        }

        .content-page {
            background-color: var(--react-bg) !important;
        }

        /* ══════════════════════════════════════════════════════════════
           KEYFRAME ANIMATIONS
        ══════════════════════════════════════════════════════════════ */
        @keyframes dashFadeInUp {
            0% {
                opacity: 0;
                transform: translateY(18px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulseRadar {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 7px rgba(16, 185, 129, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        @keyframes shimmerSlow {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        @keyframes floatGentle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }

        .anim-stagger-1 { animation: dashFadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) both; animation-delay: 0.05s; }
        .anim-stagger-2 { animation: dashFadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) both; animation-delay: 0.12s; }
        .anim-stagger-3 { animation: dashFadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) both; animation-delay: 0.18s; }
        .anim-stagger-4 { animation: dashFadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) both; animation-delay: 0.24s; }
        .anim-stagger-5 { animation: dashFadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) both; animation-delay: 0.30s; }

        /* ══════════════════════════════════════════════════════════════
           EXECUTIVE HERO WELCOME BANNER
        ══════════════════════════════════════════════════════════════ */
        .react-hero-banner {
            background: #FFFFFF;
            border: 1px solid var(--react-border);
            border-radius: 20px;
            padding: 24px 28px;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03), 0 12px 24px -6px rgba(15, 23, 42, 0.02);
            transition: all 0.25s ease;
        }

        .react-hero-banner:hover {
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05), 0 16px 32px -8px var(--pkg-glow);
            border-color: var(--pkg-border);
        }

        .react-hero-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3.5px;
            background: {{ $package === 'hajj' ? 'var(--gold-gradient)' : 'var(--emerald-gradient)' }};
        }

        .hero-left-content {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        .hero-emblem-badge {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            background: var(--pkg-subtle);
            border: 1.5px solid var(--pkg-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            box-shadow: 0 4px 12px var(--pkg-glow);
            animation: floatGentle 3s ease-in-out infinite;
            flex-shrink: 0;
        }

        .hero-text h2 {
            font-size: 22px;
            font-weight: 800;
            color: var(--react-text-main);
            letter-spacing: -0.4px;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .hero-text p {
            font-size: 13.5px;
            color: var(--react-text-muted);
            margin: 0;
            font-weight: 500;
        }

        .hero-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            color: #15803D;
            font-size: 11.5px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            letter-spacing: 0.3px;
        }

        .hero-pulse-dot {
            width: 7px;
            height: 7px;
            background: #10B981;
            border-radius: 50%;
            animation: pulseRadar 1.8s infinite;
        }

        /* Hero Actions Group */
        .hero-actions-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            position: relative;
            z-index: 10;
        }

        /* React Segmented Tab Toggle */
        .react-segmented-nav {
            display: inline-flex;
            background: #F1F5F9;
            border-radius: 14px;
            padding: 4px;
            gap: 4px;
            border: 1px solid #E2E8F0;
            position: relative;
            z-index: 10;
        }

        .react-segmented-nav a {
            padding: 7px 18px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            color: #64748B;
            transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer !important;
            user-select: none;
            position: relative;
            z-index: 11;
        }

        .react-segmented-nav a:hover {
            color: var(--react-text-main);
        }

        .react-segmented-nav a.active-hajj {
            background: #FFFFFF;
            color: var(--gold-dark);
            border: 1px solid rgba(197, 160, 89, 0.4);
            box-shadow: 0 4px 12px rgba(197, 160, 89, 0.2), 0 1px 3px rgba(0, 0, 0, 0.05);
            font-weight: 700;
            transform: scale(1.02);
        }

        .react-segmented-nav a.active-umrah {
            background: #FFFFFF;
            color: var(--emerald-dark);
            border: 1px solid rgba(5, 150, 105, 0.4);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2), 0 1px 3px rgba(0, 0, 0, 0.05);
            font-weight: 700;
            transform: scale(1.02);
        }

        /* Year Picker */
        .react-year-control {
            display: inline-flex;
            align-items: center;
            background: #FFFFFF;
            border: 1.5px solid var(--react-border);
            border-radius: 12px;
            padding: 2px 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            transition: all 0.2s ease;
        }

        .react-year-control:hover {
            border-color: var(--pkg-accent);
        }

        .react-year-control select {
            border: none;
            background: transparent;
            font-size: 13px;
            font-weight: 700;
            color: var(--react-text-main);
            padding: 6px 4px;
            cursor: pointer;
            outline: none;
        }

        /* Action Create Button */
        .btn-hero-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 12px;
            text-decoration: none;
            background: linear-gradient(135deg, #071527 0%, #0E233E 55%, #15325B 100%);
            color: #F3DC9B;
            border: 1px solid #C9A84C;
            box-shadow: 0 4px 12px rgba(7, 21, 39, 0.2);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .btn-hero-action:hover {
            background: linear-gradient(135deg, #D4AF37 0%, #B88E28 100%);
            color: #071527;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(201, 168, 76, 0.4);
        }

        /* ══════════════════════════════════════════════════════════════
           EXECUTIVE KPI CARDS (LUXURY FINTECH & ISLAMIC SUITE)
        ══════════════════════════════════════════════════════════════ */
        .bento-card {
            background: #FFFFFF;
            background-image: radial-gradient(circle at 100% 0%, rgba(212, 175, 55, 0.05) 0%, transparent 65%);
            border-radius: 18px;
            border: 1.5px solid #E2E8F0;
            padding: 22px 22px 18px 22px;
            position: relative;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03), 0 6px 16px -4px rgba(15, 23, 42, 0.04);
            transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        .bento-card:hover {
            transform: translateY(-3px);
            border-color: #D4AF37;
            box-shadow: 0 14px 30px -8px rgba(9, 13, 22, 0.1), 0 0 20px rgba(212, 175, 55, 0.16);
        }

        .bento-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .bento-title {
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748B;
            margin: 0;
        }

        .bento-icon-wrapper {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .bento-card:hover .bento-icon-wrapper {
            transform: scale(1.08) rotate(2deg);
        }

        /* Sophisticated Executive Icon Badges (Subtle, Rich, Distinct) */
        .bento-icon-bookings {
            background: #090D16;
            color: #D4AF37;
            border: 1.5px solid rgba(212, 175, 55, 0.4);
            box-shadow: 0 3px 10px rgba(9, 13, 22, 0.15);
        }
        .bento-icon-revenue {
            background: linear-gradient(135deg, #FEF8EC 0%, #FDF1D6 100%);
            color: #9A7422;
            border: 1.5px solid #ECC870;
            box-shadow: 0 3px 10px rgba(197, 160, 89, 0.15);
        }
        .bento-icon-collected {
            background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%);
            color: #047857;
            border: 1.5px solid #A7F3D0;
            box-shadow: 0 3px 10px rgba(5, 150, 105, 0.15);
        }
        .bento-icon-profit {
            background: linear-gradient(135deg, #FEF9EE 0%, #FEEBC8 100%);
            color: #B45309;
            border: 1.5px solid #FCD34D;
            box-shadow: 0 3px 10px rgba(245, 158, 11, 0.15);
        }
        .bento-icon-expenses {
            background: #FFFBEB;
            color: #D97706;
            border: 1.5px solid #FDE68A;
            box-shadow: 0 3px 10px rgba(217, 119, 6, 0.12);
        }
        .bento-icon-saudi {
            background: #F5F3FF;
            color: #7C3AED;
            border: 1.5px solid #DDD6FE;
            box-shadow: 0 3px 10px rgba(124, 58, 237, 0.12);
        }
        .bento-icon-dues {
            background: #FFF1F2;
            color: #E11D48;
            border: 1.5px solid #FECDD3;
            box-shadow: 0 3px 10px rgba(225, 29, 72, 0.12);
        }
        .bento-icon-team {
            background: #F1F5F9;
            color: #0F172A;
            border: 1.5px solid #CBD5E1;
            box-shadow: 0 3px 10px rgba(15, 23, 42, 0.08);
        }

        .bento-value-row {
            margin-bottom: 10px;
        }

        .bento-value {
            font-size: 27px;
            font-weight: 800;
            color: #0F172A;
            letter-spacing: -0.6px;
            line-height: 1.15;
            display: flex;
            align-items: baseline;
            gap: 6px;
            flex-wrap: wrap;
        }

        .bento-unit {
            font-size: 13.5px;
            font-weight: 700;
            color: #94A3B8;
            letter-spacing: normal;
        }

        .bento-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            font-size: 12px;
            color: #64748B;
            padding-top: 12px;
            border-top: 1px solid #F1F5F9;
        }

        .trend-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 6px;
            white-space: nowrap;
        }

        .trend-pill.up {
            background: #ECFDF5;
            color: #047857;
            border: 1px solid #A7F3D0;
        }

        .trend-pill.down {
            background: #FEF2F2;
            color: #B91C1C;
            border: 1px solid #FECDD3;
        }

        .trend-pill.neutral {
            background: #F1F5F9;
            color: #475569;
            border: 1px solid #E2E8F0;
        }

        /* Collection Progress Bar */
        .collection-progress-wrap {
            margin-top: 10px;
            background: #F1F5F9;
            height: 7px;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            border: 1px solid #E2E8F0;
        }

        .collection-progress-bar {
            height: 100%;
            border-radius: 10px;
            background: linear-gradient(90deg, #D4AF37 0%, #10B981 100%);
            transition: width 1s ease-in-out;
        }

        /* ══════════════════════════════════════════════════════════════
           REACT PANELS (CHART & OPERATIONS & TABLES)
        ══════════════════════════════════════════════════════════════ */
        .react-panel-box {
            background: #FFFFFF;
            border-radius: 18px;
            border: 1px solid var(--react-border);
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03), 0 4px 12px rgba(15, 23, 42, 0.02);
            overflow: hidden;
            transition: all 0.25s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .react-panel-box:hover {
            box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.06);
            border-color: var(--react-border-hover);
        }

        .panel-box-header {
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--react-border);
            background: #FFFFFF;
            flex-wrap: wrap;
            gap: 12px;
        }

        .panel-box-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--react-text-main);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .panel-box-subtitle {
            font-size: 12px;
            color: var(--react-text-muted);
            margin-top: 2px;
            font-weight: 500;
        }

        /* Chart Controls Toggle */
        .chart-toggle-group {
            display: inline-flex;
            background: #F1F5F9;
            border-radius: 10px;
            padding: 3px;
            gap: 3px;
            border: 1px solid #E2E8F0;
        }

        .btn-chart-toggle {
            border: none;
            background: transparent;
            font-size: 11.5px;
            font-weight: 600;
            color: #64748B;
            padding: 4px 10px;
            border-radius: 7px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-chart-toggle.active {
            background: #FFFFFF;
            color: var(--react-text-main);
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        /* Chart Meta Stats Bar */
        .chart-meta-chips {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px 0 24px;
            flex-wrap: wrap;
        }

        .chart-chip {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            color: var(--react-text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .chart-chip strong {
            color: var(--react-text-main);
            font-weight: 800;
        }

        /* Quick Service Tiles */
        .service-tile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 12px;
            background: #F8FAFC;
            border: 1px solid #F1F5F9;
            text-decoration: none;
            color: inherit;
            transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
            margin-bottom: 10px;
        }

        .service-tile:last-child {
            margin-bottom: 0;
        }

        .service-tile:hover {
            background: #FFFFFF;
            border-color: #D4AF37;
            transform: translateX(3px);
            box-shadow: 0 6px 16px rgba(9, 13, 22, 0.05);
            color: inherit;
        }

        .service-tile-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            color: #0F172A;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .service-tile:hover .service-tile-icon {
            background: #090D16;
            color: #D4AF37;
            border-color: #090D16;
            transform: scale(1.05);
        }

        /* ════ Operational Quick Actions Ribbon ════ */
        .quick-action-strip {
            background: #FFFFFF;
            border: 1px solid var(--react-border);
            border-radius: 14px;
            padding: 11px 16px;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        }

        .quick-action-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            font-weight: 700;
            color: var(--react-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        .quick-action-buttons {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .quick-action-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 13px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 8px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            color: var(--react-text-main);
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .quick-action-pill:hover {
            background: #090D16;
            color: #D4AF37;
            border-color: #090D16;
            transform: translateY(-1.5px);
            box-shadow: 0 4px 12px rgba(9, 13, 22, 0.12);
        }

        .quick-action-pill i {
            font-size: 14px;
            color: #C5A059;
            transition: color 0.2s ease;
        }

        .quick-action-pill:hover i {
            color: #D4AF37;
        }

        .service-tile-title {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--react-text-main);
            margin-bottom: 2px;
        }

        .service-tile-desc {
            font-size: 12px;
            color: var(--react-text-muted);
            margin: 0;
        }

        .service-tile-arrow {
            color: #94A3B8;
            font-size: 16px;
            transition: transform 0.2s ease;
            margin-left: auto;
        }

        .service-tile:hover .service-tile-arrow {
            color: var(--gold-primary);
            transform: translateX(3px);
        }

        /* ══════════════════════════════════════════════════════════════
           REACT DATAGRID (RECENT BOOKINGS TABLE)
        ══════════════════════════════════════════════════════════════ */
        .table-search-input {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            padding: 6px 14px 6px 32px;
            font-size: 12.5px;
            font-weight: 500;
            outline: none;
            width: 220px;
            transition: all 0.2s ease;
        }

        .table-search-input:focus {
            background: #FFFFFF;
            border-color: var(--gold-primary);
            box-shadow: 0 0 0 3px rgba(197, 160, 89, 0.15);
            width: 260px;
        }

        .react-table {
            width: 100%;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .react-table th {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--react-text-muted);
            background: #F8FAFC;
            padding: 12px 18px;
            border-bottom: 1px solid var(--react-border);
            white-space: nowrap;
        }

        .react-table td {
            padding: 13px 18px;
            font-size: 13.5px;
            color: var(--react-text-main);
            vertical-align: middle;
            border-bottom: 1px solid #F1F5F9;
            transition: background 0.15s ease;
        }

        .react-table tr:last-child td {
            border-bottom: none;
        }

        .react-table tr:hover td {
            background: #F8FAFC;
        }

        .avatar-initial-react {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 12px;
            color: #FFFFFF;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
        }

        .badge-entity-tag {
            font-size: 9.5px;
            font-weight: 800;
            letter-spacing: 0.4px;
            padding: 2px 6px;
            border-radius: 6px;
            margin-left: 5px;
        }

        .badge-entity-co { background: #EEF2FF; color: #4F46E5; }
        .badge-entity-cl { background: #FEF8EC; color: #B4882F; }

        .react-pkg-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: capitalize;
            display: inline-block;
        }

        .react-pkg-badge.hajj {
            background: #FEF8EC;
            color: #927329;
            border: 1px solid #F5E5BE;
        }

        .react-pkg-badge.umrah {
            background: #ECFDF5;
            color: #065F46;
            border: 1px solid #A7F3D0;
        }

        .status-pill-paid {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11.5px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            background: #ECFDF5;
            color: #047857;
            border: 1px solid #A7F3D0;
        }

        .status-pill-balance {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11.5px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            background: #FEF2F2;
            color: #B91C1C;
            border: 1px solid #FECACA;
        }

        .action-icon-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #F1F5F9;
            color: #475569;
            text-decoration: none;
            transition: all 0.18s ease;
            font-size: 15px;
        }

        .action-icon-btn:hover {
            background: #0E233E;
            color: #F3DC9B;
            transform: translateY(-1px);
        }
    </style>

    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                {{-- ══════════════════════════════════════════════════════════════
                   1. EXECUTIVE HERO WELCOME BANNER (Animated)
                ══════════════════════════════════════════════════════════════ --}}
                <div class="react-hero-banner anim-stagger-1 mt-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="hero-left-content">
                            <div class="hero-emblem-badge">
                                {{ $package === 'hajj' ? '🕋' : '🌙' }}
                            </div>
                            <div class="hero-text">
                                <h2>
                                    <span>Assalam-o-Alaikum, {{ Auth::user()->name ?? 'Administrator' }}!</span>
                                    <span class="hero-status-pill">
                                        <span class="hero-pulse-dot"></span>
                                        <span>LIVE CONNECTED • BABA HAJJ CLOUD</span>
                                    </span>
                                </h2>
                                <p>
                                    Real-time pilgrim manifests, logistics velocity & financial intelligence &bull;
                                    <strong>{{ ucfirst($package) }} Cycle {{ $year }}</strong>
                                </p>
                            </div>
                        </div>

                        <div class="hero-actions-bar">
                            {{-- React Segmented Switcher (Hajj vs Umrah) --}}
                            <div class="react-segmented-nav">
                                <a href="{{ route('dashboard', ['package' => 'hajj', 'year' => $year]) }}"
                                   class="{{ $package === 'hajj' ? 'active-hajj' : '' }}">
                                    <span>🕋</span>
                                    <span>Hajj</span>
                                </a>
                                <a href="{{ route('dashboard', ['package' => 'umrah', 'year' => $year]) }}"
                                   class="{{ $package === 'umrah' ? 'active-umrah' : '' }}">
                                    <span>🌙</span>
                                    <span>Umrah</span>
                                </a>
                            </div>

                            {{-- Year Picker --}}
                            <form method="GET" action="{{ route('dashboard') }}" id="yearForm" class="m-0">
                                <input type="hidden" name="package" value="{{ $package }}">
                                <div class="react-year-control">
                                    <i class="ri-calendar-event-line text-muted me-1"></i>
                                    <select name="year" onchange="document.getElementById('yearForm').submit()">
                                        @foreach ($availableYears as $yr)
                                            <option value="{{ $yr }}" {{ $yr == $year ? 'selected' : '' }}>
                                                Year {{ $yr }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>

                            {{-- Quick Action: New Booking --}}
                            @can('booking_create')
                                <a href="{{ route('booking.create') }}" class="btn-hero-action">
                                    <i class="ri-add-circle-line fs-16"></i>
                                    <span>New Booking</span>
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════════
                   2. CORE REVENUE & PROFIT INTELLIGENCE (Row 1 - Bento Cards)
                ══════════════════════════════════════════════════════════════ --}}
                <div class="row g-3 mb-3 anim-stagger-2">

                    {{-- Card 1: Bookings & Pax --}}
                    <div class="col-6 col-xl-3">
                        <div class="bento-card">
                            <div>
                                <div class="bento-header">
                                    <span class="bento-title">{{ ucfirst($package) }} Bookings</span>
                                    <div class="bento-icon-wrapper bento-icon-bookings">
                                        <i class="ri-calendar-check-line"></i>
                                    </div>
                                </div>
                                <div class="bento-value-row">
                                    <div class="bento-value">
                                        <span class="counter-number" data-target="{{ $totalBookings }}">0</span>
                                        <span class="badge" style="background: #090D16; color: #D4AF37; font-weight: 700; font-size: 11.5px; padding: 4px 9px; border-radius: 6px; border: 1px solid rgba(212, 175, 55, 0.35);">
                                            {{ number_format($totalPax ?? 0) }} Pax
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="bento-footer">
                                <span class="trend-pill {{ $bookingsTrend >= 0 ? 'up' : 'down' }}">
                                    <i class="ri-arrow-{{ $bookingsTrend >= 0 ? 'up' : 'down' }}-line"></i>
                                    {{ abs($bookingsTrend) }}%
                                </span>
                                <span>Confirmed files</span>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2: Gross Invoiced Revenue --}}
                    <div class="col-6 col-xl-3">
                        <div class="bento-card">
                            <div>
                                <div class="bento-header">
                                    <span class="bento-title">Gross Revenue</span>
                                    <div class="bento-icon-wrapper bento-icon-revenue">
                                        <i class="ri-line-chart-line"></i>
                                    </div>
                                </div>
                                <div class="bento-value-row">
                                    <div class="bento-value">
                                        <span class="bento-unit">PKR</span>
                                        <span class="counter-number" data-target="{{ $totalRevenue }}">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="bento-footer">
                                <span class="trend-pill {{ $revenueTrend >= 0 ? 'up' : 'down' }}">
                                    <i class="ri-arrow-{{ $revenueTrend >= 0 ? 'up' : 'down' }}-line"></i>
                                    {{ abs($revenueTrend) }}%
                                </span>
                                <span>Total billed</span>
                            </div>
                        </div>
                    </div>

                    {{-- Card 3: Total Collections Received --}}
                    <div class="col-6 col-xl-3">
                        <div class="bento-card">
                            <div>
                                <div class="bento-header">
                                    <span class="bento-title">Total Collected</span>
                                    <div class="bento-icon-wrapper bento-icon-collected">
                                        <i class="ri-wallet-3-line"></i>
                                    </div>
                                </div>
                                <div class="bento-value-row">
                                    <div class="bento-value">
                                        <span class="bento-unit">PKR</span>
                                        <span class="counter-number" data-target="{{ $totalReceived }}">0</span>
                                    </div>
                                    @php
                                        $collectionRate = $totalRevenue > 0 ? min(100, round(($totalReceived / $totalRevenue) * 100, 1)) : 0;
                                    @endphp
                                    <div class="collection-progress-wrap" title="Collection Rate: {{ $collectionRate }}%">
                                        <div class="collection-progress-bar" style="width: {{ $collectionRate }}%;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="bento-footer">
                                <span class="trend-pill {{ $receivedTrend >= 0 ? 'up' : 'down' }}">
                                    <i class="ri-arrow-{{ $receivedTrend >= 0 ? 'up' : 'down' }}-line"></i>
                                    {{ abs($receivedTrend) }}%
                                </span>
                                <span><strong>{{ $collectionRate }}%</strong> recovery rate</span>
                            </div>
                        </div>
                    </div>

                    {{-- Card 4: Operating Profit / Loss --}}
                    <div class="col-6 col-xl-3">
                        <div class="bento-card">
                            <div>
                                <div class="bento-header">
                                    <span class="bento-title">{{ $isProfit ? 'Net Operating Profit' : 'Operating Deficit' }}</span>
                                    <div class="bento-icon-wrapper bento-icon-profit">
                                        <i class="ri-{{ $isProfit ? 'funds-line' : 'error-warning-line' }}"></i>
                                    </div>
                                </div>
                                <div class="bento-value-row">
                                    <div class="bento-value" style="color: {{ $isProfit ? '#059669' : '#DC2626' }};">
                                        <span class="bento-unit" style="color: {{ $isProfit ? '#059669' : '#DC2626' }};">{{ $isProfit ? 'PKR' : '- PKR' }}</span>
                                        <span class="counter-number" data-target="{{ abs($profit) }}">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="bento-footer">
                                <span class="trend-pill {{ $isProfit ? 'up' : 'down' }}">
                                    {{ $isProfit ? 'Profitable Cycle' : 'Review Expenses' }}
                                </span>
                                <span>Revenue minus costs</span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ══════════════════════════════════════════════════════════════
                   3. COST CONTROLS, RECEIVABLES & TEAM (Row 2 - 4 Bento Cards)
                ══════════════════════════════════════════════════════════════ --}}
                <div class="row g-3 mb-3 anim-stagger-3">

                    {{-- Expenses PKR --}}
                    <div class="col-6 col-xl-3">
                        <div class="bento-card">
                            <div>
                                <div class="bento-header">
                                    <span class="bento-title">Pakistan Expenses</span>
                                    <div class="bento-icon-wrapper bento-icon-expenses">
                                        <i class="ri-bank-card-line"></i>
                                    </div>
                                </div>
                                <div class="bento-value-row">
                                    <div class="bento-value">
                                        <span class="bento-unit">PKR</span>
                                        <span class="counter-number" data-target="{{ $totalExpensesPkr }}">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="bento-footer">
                                <span class="trend-pill {{ $expensesTrend >= 0 ? 'down' : 'up' }}">
                                    <i class="ri-arrow-{{ $expensesTrend >= 0 ? 'up' : 'down' }}-line"></i>
                                    {{ abs($expensesTrend) }}% 7-day
                                </span>
                                <span>Local operations</span>
                            </div>
                        </div>
                    </div>

                    {{-- Expenses SAR --}}
                    <div class="col-6 col-xl-3">
                        <div class="bento-card">
                            <div>
                                <div class="bento-header">
                                    <span class="bento-title">Saudi Operations (SAR)</span>
                                    <div class="bento-icon-wrapper bento-icon-saudi">
                                        <i class="ri-exchange-dollar-line"></i>
                                    </div>
                                </div>
                                <div class="bento-value-row">
                                    <div class="bento-value">
                                        <span class="counter-number" data-target="{{ $totalExpensesSar }}">0</span>
                                        <span class="bento-unit" style="color: #7C3AED;">SAR</span>
                                    </div>
                                    <div class="mt-1">
                                        <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 11.5px;">
                                            ≈ PKR <span class="counter-number" data-target="{{ $totalExpensesSarInPkr }}">0</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="bento-footer">
                                <span class="trend-pill neutral">Saudi Maktab & Hotels</span>
                                <span>Converted to PKR</span>
                            </div>
                        </div>
                    </div>

                    {{-- Pending Receivables / Due Balance --}}
                    @php
                        $outstandingReceivables = max(0, $totalRevenue - $totalReceived);
                        $dueRate = $totalRevenue > 0 ? min(100, round(($outstandingReceivables / $totalRevenue) * 100, 1)) : 0;
                    @endphp
                    <div class="col-6 col-xl-3">
                        <div class="bento-card">
                            <div>
                                <div class="bento-header">
                                    <span class="bento-title">Pending Receivables</span>
                                    <div class="bento-icon-wrapper bento-icon-dues">
                                        <i class="ri-hand-coin-line"></i>
                                    </div>
                                </div>
                                <div class="bento-value-row">
                                    <div class="bento-value" style="color: {{ $outstandingReceivables > 0 ? '#E11D48' : '#0F172A' }};">
                                        <span class="bento-unit" style="color: {{ $outstandingReceivables > 0 ? '#E11D48' : '#94A3B8' }};">PKR</span>
                                        <span class="counter-number" data-target="{{ $outstandingReceivables }}">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="bento-footer">
                                <span class="trend-pill {{ $outstandingReceivables > 0 ? 'down' : 'up' }}">
                                    {{ $dueRate }}% uncollected
                                </span>
                                <span>Pilgrim dues pending</span>
                            </div>
                        </div>
                    </div>

                    {{-- System Users & Staff --}}
                    <div class="col-6 col-xl-3">
                        <div class="bento-card">
                            <div>
                                <div class="bento-header">
                                    <span class="bento-title">Active Team & Agents</span>
                                    <div class="bento-icon-wrapper bento-icon-team">
                                        <i class="ri-team-line"></i>
                                    </div>
                                </div>
                                <div class="bento-value-row">
                                    <div class="bento-value">
                                        <span class="counter-number" data-target="{{ $totalUsers }}">0</span>
                                        <span class="bento-unit">Operators</span>
                                    </div>
                                </div>
                            </div>
                            <div class="bento-footer">
                                <span class="trend-pill {{ $usersTrend >= 0 ? 'up' : 'down' }}">
                                    <i class="ri-arrow-{{ $usersTrend >= 0 ? 'up' : 'down' }}-line"></i>
                                    {{ abs($usersTrend) }}%
                                </span>
                                <span>Portal users active</span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ══════════════════════════════════════════════════════════════
                   OPERATIONAL QUICK ACTIONS RIBBON
                ══════════════════════════════════════════════════════════════ --}}
                <div class="quick-action-strip anim-stagger-3">
                    <div class="quick-action-label">
                        <i class="ri-flashlight-line text-warning"></i>
                        <span>Executive Shortcuts:</span>
                    </div>
                    <div class="quick-action-buttons">
                        @can('booking_create')
                            <a href="{{ route('booking.create') }}" class="quick-action-pill">
                                <i class="ri-add-circle-line"></i> New Booking
                            </a>
                        @endcan
                        @can('client_Transactions_view')
                            <a href="{{ route('transaction.create') }}" class="quick-action-pill">
                                <i class="ri-wallet-3-line"></i> Record Payment
                            </a>
                        @endcan
                        @can('expense_view')
                            <a href="{{ route('expense.transaction.create') }}" class="quick-action-pill">
                                <i class="ri-file-add-line"></i> Log Expense
                            </a>
                        @endcan
                        @can('booking_view')
                            <a href="{{ route('booking.index') }}" class="quick-action-pill">
                                <i class="ri-file-list-3-line"></i> Pilgrim Manifests
                            </a>
                        @endcan
                        @can('hotel_view')
                            <a href="{{ route('hotel.index') }}" class="quick-action-pill">
                                <i class="ri-hotel-bed-line"></i> Hotel Allocations
                            </a>
                        @endcan
                        @can('flight_view')
                            <a href="{{ route('flight.index') }}" class="quick-action-pill">
                                <i class="ri-plane-line"></i> Flight Itineraries
                            </a>
                        @endcan
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════════
                   4. MONTHLY BOOKING VELOCITY & LOGISTICS SERVICES (Row 3)
                ══════════════════════════════════════════════════════════════ --}}
                <div class="row g-3 mb-4 anim-stagger-4">

                    {{-- Left 8 Cols: ApexCharts Booking Velocity --}}
                    <div class="col-12 col-xl-8">
                        <div class="react-panel-box">
                            <div class="panel-box-header">
                                <div>
                                    <h5 class="panel-box-title">
                                        <i class="ri-bar-chart-grouped-line text-warning"></i>
                                        <span>{{ ucfirst($package) }} Booking Velocity — {{ $year }}</span>
                                    </h5>
                                    <div class="panel-box-subtitle">12-Month cyclical pilgrim intake and seasonal booking trends</div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="chart-toggle-group">
                                        <button type="button" class="btn-chart-toggle active" id="btnChartBar" onclick="setChartType('bar')">
                                            <i class="ri-bar-chart-fill"></i> Column
                                        </button>
                                        <button type="button" class="btn-chart-toggle" id="btnChartArea" onclick="setChartType('area')">
                                            <i class="ri-line-chart-fill"></i> Spline
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Chart Summary Chips --}}
                            @php
                                $totalAnnualBookings = array_sum($monthlyData);
                                $peakMonthlyValue = !empty($monthlyData) ? max($monthlyData) : 0;
                                $peakMonthIndex = !empty($monthlyData) ? array_search($peakMonthlyValue, $monthlyData) : 0;
                                $monthsList = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                $peakMonthName = $monthsList[$peakMonthIndex] ?? 'N/A';
                            @endphp
                            <div class="chart-meta-chips">
                                <div class="chart-chip">
                                    <i class="ri-archive-line text-muted"></i>
                                    <span>Annual Volume:</span>
                                    <strong>{{ number_format($totalAnnualBookings) }} Files</strong>
                                </div>
                                <div class="chart-chip">
                                    <i class="ri-fire-line text-warning"></i>
                                    <span>Peak Month:</span>
                                    <strong>{{ $peakMonthName }} ({{ number_format($peakMonthlyValue) }})</strong>
                                </div>
                                <div class="chart-chip">
                                    <i class="ri-bar-chart-2-line text-info"></i>
                                    <span>Monthly Avg:</span>
                                    <strong>{{ round($totalAnnualBookings / 12, 1) }} / mo</strong>
                                </div>
                            </div>

                            <div class="p-3">
                                <div id="bookings-chart" style="min-height: 290px;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Right 4 Cols: Logistics Pipeline Services --}}
                    <div class="col-12 col-xl-4">
                        <div class="react-panel-box">
                            <div class="panel-box-header">
                                <div>
                                    <h5 class="panel-box-title">
                                        <i class="ri-pulse-line text-success"></i>
                                        <span>Logistics Operations</span>
                                    </h5>
                                    <div class="panel-box-subtitle">Live service allocations & pipelines</div>
                                </div>
                            </div>

                            <div class="p-3">
                                {{-- Flight Itineraries --}}
                                @can('flight_view')
                                    <a href="{{ route('flight.index') }}" class="service-tile">
                                        <div class="service-tile-icon">
                                            <i class="ri-plane-line"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="service-tile-title">Flight Itineraries</div>
                                            <div class="service-tile-desc">{{ number_format($flightsThisMonth) }} flights active this month</div>
                                        </div>
                                        <i class="ri-arrow-right-s-line service-tile-arrow"></i>
                                    </a>
                                @else
                                    <div class="service-tile">
                                        <div class="service-tile-icon">
                                            <i class="ri-plane-line"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="service-tile-title">Flight Itineraries</div>
                                            <div class="service-tile-desc">{{ number_format($flightsThisMonth) }} flights active this month</div>
                                        </div>
                                    </div>
                                @endcan

                                {{-- Hotel Allocations --}}
                                @can('hotel_view')
                                    <a href="{{ route('hotel.index') }}" class="service-tile">
                                        <div class="service-tile-icon">
                                            <i class="ri-hotel-bed-line"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="service-tile-title">Hotels & Maktabs</div>
                                            <div class="service-tile-desc">{{ number_format($hotelsThisMonth) }} room allocations logged</div>
                                        </div>
                                        <i class="ri-arrow-right-s-line service-tile-arrow"></i>
                                    </a>
                                @else
                                    <div class="service-tile">
                                        <div class="service-tile-icon">
                                            <i class="ri-hotel-bed-line"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="service-tile-title">Hotels & Maktabs</div>
                                            <div class="service-tile-desc">{{ number_format($hotelsThisMonth) }} room allocations logged</div>
                                        </div>
                                    </div>
                                @endcan

                                {{-- Visa Approvals --}}
                                @canany(['visa_country_view', 'visa_inquiry_view'])
                                    <a href="{{ Route::has('mobile-ops.visa-countries.index') ? route('mobile-ops.visa-countries.index') : route('mobile-ops.dashboard') }}" class="service-tile">
                                        <div class="service-tile-icon">
                                            <i class="ri-passport-line"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="service-tile-title">Visa E-Passes</div>
                                            <div class="service-tile-desc">{{ number_format($visasApproved) }} approvals issued</div>
                                        </div>
                                        <i class="ri-arrow-right-s-line service-tile-arrow"></i>
                                    </a>
                                @else
                                    <div class="service-tile">
                                        <div class="service-tile-icon">
                                            <i class="ri-passport-line"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="service-tile-title">Visa E-Passes</div>
                                            <div class="service-tile-desc">{{ number_format($visasApproved) }} approvals issued</div>
                                        </div>
                                    </div>
                                @endcan

                                {{-- Pilgrim Groups --}}
                                <a href="{{ route('booking.index') }}" class="service-tile">
                                    <div class="service-tile-icon">
                                        <i class="ri-group-line"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="service-tile-title">{{ ucfirst($package) }} Group Manifests</div>
                                        <div class="service-tile-desc">{{ number_format($totalBookings) }} Files &bull; {{ number_format($totalPax ?? 0) }} Pax</div>
                                    </div>
                                    <i class="ri-arrow-right-s-line service-tile-arrow"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ══════════════════════════════════════════════════════════════
                   5. RECENT BOOKINGS DATAGRID (Row 4 - With Client Filter)
                ══════════════════════════════════════════════════════════════ --}}
                <div class="row g-3 mb-4 anim-stagger-5">
                    <div class="col-12">
                        <div class="react-panel-box">
                            <div class="panel-box-header">
                                <div>
                                    <h5 class="panel-box-title">
                                        <i class="ri-file-list-3-line text-primary"></i>
                                        <span>Recent Confirmed Bookings</span>
                                    </h5>
                                    <div class="panel-box-subtitle">Latest pilgrim applications and company allocations</div>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    {{-- Instant Filter Input --}}
                                    <div class="position-relative">
                                        <i class="ri-search-line position-absolute top-50 translate-middle-y ms-2 text-muted" style="font-size: 13px;"></i>
                                        <input type="text" id="bookingSearchInput" class="table-search-input" placeholder="Search client name...">
                                    </div>

                                    <a href="{{ route('booking.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 10px; font-weight: 600; font-size: 12.5px;">
                                        <span>View All</span>
                                        <i class="ri-arrow-right-line ms-1"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="react-table" id="recentBookingsTable">
                                    <thead>
                                        <tr>
                                            <th>Client / Entity</th>
                                            <th>Package Type</th>
                                            <th>Pax</th>
                                            <th>Total (PKR)</th>
                                            <th>Payment Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentBookings as $booking)
                                            @php
                                                $isCompanyBooking = $booking->booking_for === 'company';
                                                $clientName = $isCompanyBooking
                                                    ? optional($booking->company)->name ?? 'N/A'
                                                    : optional($booking->client)->name ?? 'N/A';

                                                $clientEmail = $isCompanyBooking
                                                    ? optional($booking->company)->email ?? ''
                                                    : optional($booking->client)->email ?? '';

                                                $words = explode(' ', trim($clientName));
                                                $initials = strtoupper(
                                                    substr($words[0] ?? '', 0, 1) . substr($words[1] ?? '', 0, 1),
                                                );

                                                $gradMap = [
                                                    'hajj' => 'linear-gradient(135deg, #C5A059, #DFBE76)',
                                                    'umrah' => 'linear-gradient(135deg, #059669, #34D399)',
                                                    'other' => 'linear-gradient(135deg, #4F46E5, #818CF8)',
                                                ];
                                                $grad = $gradMap[$booking->package_type] ?? 'linear-gradient(135deg, #0F172A, #334155)';
                                            @endphp
                                            <tr class="booking-row" data-client="{{ strtolower($clientName) }}">
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="avatar-initial-react" style="background: {{ $grad }};">
                                                            {{ $initials ?: 'BH' }}
                                                        </div>
                                                        <div>
                                                            <div style="font-weight: 700; font-size: 13.5px; color: var(--react-text-main);">
                                                                {{ $clientName }}
                                                                @if ($isCompanyBooking)
                                                                    <span class="badge-entity-tag badge-entity-co">CO</span>
                                                                @else
                                                                    <span class="badge-entity-tag badge-entity-cl">CL</span>
                                                                @endif
                                                            </div>
                                                            <div style="font-size: 11.5px; color: var(--react-text-muted);">
                                                                {{ $clientEmail ?: 'Ref #' . $booking->id }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="react-pkg-badge {{ $booking->package_type }}">
                                                        {{ ucfirst($booking->package_type) }}
                                                    </span>
                                                </td>
                                                <td style="font-weight: 700;">
                                                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 12px; border-radius: 6px;">
                                                        {{ $booking->no_of_pax ?? '1' }}
                                                    </span>
                                                </td>
                                                <td style="font-weight: 800; color: var(--react-text-main);">
                                                    {{ number_format($booking->total_amount) }}
                                                </td>
                                                <td>
                                                    @if (($booking->balance ?? 0) <= 0)
                                                        <span class="status-pill-paid">
                                                            <i class="ri-checkbox-circle-fill"></i> Paid in Full
                                                        </span>
                                                    @else
                                                        <span class="status-pill-balance">
                                                            <i class="ri-time-line"></i> Due: {{ number_format($booking->balance) }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-inline-flex align-items-center gap-1">
                                                        @can('booking_view')
                                                            <a href="{{ route('booking.show', $booking->id) }}" class="action-icon-btn" title="View Booking File">
                                                                <i class="ri-eye-line"></i>
                                                            </a>
                                                        @endcan
                                                        <a href="{{ route('booking.voucher', $booking->id) }}" class="action-icon-btn" title="View Voucher" target="_blank">
                                                            <i class="ri-file-text-line"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5">
                                                    <div class="py-4 text-center">
                                                        <div class="mb-3">
                                                            <div class="d-inline-flex align-items-center justify-content-center"
                                                                 style="width: 56px; height: 56px; border-radius: 16px; background: #F8FAFC; border: 1px solid #E2E8F0; font-size: 24px; color: #94A3B8;">
                                                                <i class="ri-inbox-line"></i>
                                                            </div>
                                                        </div>
                                                        <h6 class="fw-bold mb-1" style="color: #475569;">No {{ ucfirst($package) }} Bookings Recorded</h6>
                                                        <p class="text-muted fs-12 mb-3">No pilgrim booking files found for cycle {{ $year }}.</p>
                                                        @can('booking_create')
                                                            <a href="{{ route('booking.create') }}" class="btn btn-sm btn-dark" style="border-radius: 8px; font-weight: 600; padding: 6px 16px;">
                                                                <i class="ri-add-line me-1"></i> Create First Booking
                                                            </a>
                                                        @endcan
                                                    </div>
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
    </div>

    {{-- ══════════════════════════════════════════════════════════════
       ANIMATED COUNTERS & APEXCHARTS INTERACTIVE JAVASCRIPT
    ══════════════════════════════════════════════════════════════ --}}
    <script>
        let bookingsChartInstance = null;

        document.addEventListener('DOMContentLoaded', function() {
            // ── 1. NUMBER COUNT-UP ANIMATION ──
            const counters = document.querySelectorAll('.counter-number');
            const duration = 1200; // ms

            counters.forEach(counter => {
                const target = parseFloat(counter.getAttribute('data-target')) || 0;
                if (target === 0) {
                    counter.innerText = '0';
                    return;
                }

                let startTimestamp = null;
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    // Ease out expo
                    const easeProgress = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                    const currentVal = Math.floor(easeProgress * target);
                    counter.innerText = currentVal.toLocaleString('en-US');

                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    } else {
                        counter.innerText = Math.round(target).toLocaleString('en-US');
                    }
                };

                window.requestAnimationFrame(step);
            });

            // ── 2. INSTANT SEARCH FILTER FOR RECENT BOOKINGS ──
            const searchInput = document.getElementById('bookingSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase().trim();
                    const rows = document.querySelectorAll('#recentBookingsTable .booking-row');
                    rows.forEach(row => {
                        const client = row.getAttribute('data-client') || '';
                        row.style.display = client.includes(query) ? '' : 'none';
                    });
                });
            }

            // ── 3. APEXCHARTS WITH DYNAMIC ANIMATIONS ──
            const isHajj = '{{ $package }}' === 'hajj';
            const primaryColor = isHajj ? '#C5A059' : '#059669';
            const secondaryColor = isHajj ? '#ECC870' : '#34D399';
            const monthlyData = {!! json_encode($monthlyData) !!};

            const chartOptions = {
                series: [{
                    name: '{{ ucfirst($package) }} Bookings',
                    data: monthlyData
                }],
                chart: {
                    type: 'bar',
                    height: 290,
                    toolbar: { show: false },
                    fontFamily: 'Plus Jakarta Sans, sans-serif',
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                        animateGradually: {
                            enabled: true,
                            delay: 120
                        },
                        dynamicAnimation: {
                            enabled: true,
                            speed: 350
                        }
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 8,
                        columnWidth: '42%',
                        dataLabels: { position: 'top' }
                    }
                },
                colors: [primaryColor],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: 'vertical',
                        gradientToColors: [secondaryColor],
                        stops: [0, 100]
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: {
                            colors: '#64748B',
                            fontSize: '12px',
                            fontWeight: 600
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#64748B',
                            fontSize: '12px',
                            fontWeight: 500
                        }
                    }
                },
                grid: {
                    borderColor: '#E2E8F0',
                    strokeDashArray: 4,
                    xaxis: { lines: { show: false } }
                },
                tooltip: {
                    theme: 'light',
                    style: { fontFamily: 'Plus Jakarta Sans, sans-serif' },
                    y: {
                        formatter: function(val) {
                            return val + " Bookings";
                        }
                    }
                }
            };

            const chartElement = document.querySelector("#bookings-chart");
            if (chartElement) {
                bookingsChartInstance = new ApexCharts(chartElement, chartOptions);
                bookingsChartInstance.render();
            }
        });

        // ── 4. CHART TOGGLE (BAR VS SPLINE AREA) ──
        function setChartType(type) {
            if (!bookingsChartInstance) return;

            const isHajj = '{{ $package }}' === 'hajj';
            const primaryColor = isHajj ? '#C5A059' : '#059669';
            const secondaryColor = isHajj ? '#ECC870' : '#34D399';

            document.querySelectorAll('.btn-chart-toggle').forEach(btn => btn.classList.remove('active'));

            if (type === 'bar') {
                document.getElementById('btnChartBar').classList.add('active');
                bookingsChartInstance.updateOptions({
                    chart: { type: 'bar' },
                    plotOptions: {
                        bar: {
                            borderRadius: 8,
                            columnWidth: '42%'
                        }
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: 'vertical',
                            gradientToColors: [secondaryColor],
                            stops: [0, 100]
                        }
                    }
                });
            } else if (type === 'area') {
                document.getElementById('btnChartArea').classList.add('active');
                bookingsChartInstance.updateOptions({
                    chart: { type: 'area' },
                    stroke: {
                        show: true,
                        curve: 'smooth',
                        width: 3,
                        colors: [primaryColor]
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: 'vertical',
                            shadeIntensity: 0.5,
                            gradientToColors: [secondaryColor],
                            inverseColors: false,
                            opacityFrom: 0.45,
                            opacityTo: 0.05,
                            stops: [0, 100]
                        }
                    }
                });
            }
        }
    </script>
@endsection
