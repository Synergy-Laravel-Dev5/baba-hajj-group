@extends('layout.master')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap');

        :root {
            /* ── Neutral Ramp (Graphite Light) ── */
            --dash-bg: #F8FAFC;
            --dash-card: #FFFFFF;
            --dash-raised: #F1F5F9;
            --dash-border: #E2E8F0;
            --dash-border-subtle: #F1F5F9;
            --dash-border-hover: #CBD5E1;

            /* ── Three Text Roles ── */
            --dash-text-primary: #0F172A;
            --dash-text-secondary: #475569;
            --dash-text-muted: #64748B;

            /* ── Single Deliberate Accent (Derived from pilgrimage world) ── */
            /* Hajj: Warm Brass Gold; Umrah: Deep Spruce Emerald. Both reach > 5:1 contrast */
            --dash-accent: {{ $package === 'hajj' ? '#8A6618' : '#047857' }};
            --dash-accent-hover: {{ $package === 'hajj' ? '#735412' : '#065F46' }};
            --dash-accent-subtle: {{ $package === 'hajj' ? '#FEF8EC' : '#ECFDF5' }};
            --dash-accent-border: {{ $package === 'hajj' ? '#ECC870' : '#A7F3D0' }};

            /* ── Semantic Colors (Toned to sit inside the palette) ── */
            --dash-success: #16A34A;
            --dash-success-subtle: #F0FDF4;
            --dash-success-border: #DCFCE7;
            --dash-danger: #DC2626;
            --dash-danger-subtle: #FEF2F2;
            --dash-danger-border: #FEE2E2;

            /* ── Nested Radius Scale ── */
            --radius-card: 14px;
            --radius-control: 8px;
            --radius-pill: 20px;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            background-color: var(--dash-bg) !important;
            color: var(--dash-text-primary) !important;
            -webkit-font-smoothing: antialiased;
        }

        .content-page {
            background-color: var(--dash-bg) !important;
        }

        /* Tabular figures for exact alignment of financial data */
        .tabular-nums {
            font-variant-numeric: tabular-nums;
            font-feature-settings: "tnum";
        }

        /* ══════════════════════════════════════════════════════════════
           1. CONTEXT HEADER BAR (Quiet, Clear, Context-First)
        ══════════════════════════════════════════════════════════════ */
        .dash-header-bar {
            background: var(--dash-card);
            border: 1px solid var(--dash-border);
            border-radius: var(--radius-card);
            padding: 16px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
        }

        .header-title-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-icon-box {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-control);
            background: var(--dash-accent-subtle);
            border: 1px solid var(--dash-accent-border);
            color: var(--dash-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .header-title-text h1 {
            font-size: 18px;
            font-weight: 600;
            color: var(--dash-text-primary);
            margin: 0;
            line-height: 1.25;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .header-title-text p {
            font-size: 13px;
            color: var(--dash-text-muted);
            margin: 2px 0 0 0;
            font-weight: 400;
        }

        .header-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 500;
            color: var(--dash-text-secondary);
            background: var(--dash-raised);
            border: 1px solid var(--dash-border);
            padding: 2px 8px;
            border-radius: 6px;
        }

        .header-status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--dash-accent);
        }

        .header-controls {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* Cycle Switcher (Segmented Control) */
        .cycle-segmented-control {
            display: inline-flex;
            background: var(--dash-raised);
            border: 1px solid var(--dash-border);
            border-radius: var(--radius-control);
            padding: 3px;
            gap: 2px;
        }

        .cycle-segmented-control a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 500;
            color: var(--dash-text-secondary);
            text-decoration: none;
            border-radius: 6px;
            transition: color 150ms ease, background-color 150ms ease;
        }

        .cycle-segmented-control a:hover {
            color: var(--dash-text-primary);
        }

        .cycle-segmented-control a.active {
            background: var(--dash-card);
            color: var(--dash-accent);
            font-weight: 600;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
            border: 1px solid var(--dash-border);
        }

        /* Year Select Input */
        .dash-select-wrap {
            display: inline-flex;
            align-items: center;
            background: var(--dash-card);
            border: 1px solid var(--dash-border);
            border-radius: var(--radius-control);
            padding: 0 8px;
            height: 36px;
        }

        .dash-select-wrap select {
            border: none;
            background: transparent;
            font-size: 13px;
            font-weight: 500;
            color: var(--dash-text-primary);
            padding: 4px 6px;
            cursor: pointer;
            outline: none;
        }

        /* Primary Action Button (Single Accent) */
        .btn-dash-primary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 0 16px;
            height: 36px;
            font-size: 13px;
            font-weight: 600;
            border-radius: var(--radius-control);
            text-decoration: none;
            background: var(--dash-accent);
            color: #FFFFFF;
            border: 1px solid var(--dash-accent);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
            transition: background-color 150ms ease, border-color 150ms ease;
        }

        .btn-dash-primary:hover {
            background: var(--dash-accent-hover);
            border-color: var(--dash-accent-hover);
            color: #FFFFFF;
        }

        /* ══════════════════════════════════════════════════════════════
           2. KPI METRIC CARDS (Equal height, quiet surfaces, tabular figures)
        ══════════════════════════════════════════════════════════════ */
        .dash-stat-card {
            background: var(--dash-card);
            border: 1px solid var(--dash-border);
            border-radius: var(--radius-card);
            padding: 20px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
            transition: border-color 150ms ease, box-shadow 150ms ease;
        }

        .dash-stat-card:hover {
            border-color: var(--dash-border-hover);
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        }

        .stat-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .stat-card-title {
            font-size: 13px;
            font-weight: 500;
            color: var(--dash-text-secondary);
            margin: 0;
        }

        .stat-card-icon {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-control);
            background: var(--dash-raised);
            color: var(--dash-text-muted);
            border: 1px solid var(--dash-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .stat-card-value-block {
            margin-bottom: 12px;
        }

        .stat-card-value {
            font-size: 26px;
            font-weight: 600;
            color: var(--dash-text-primary);
            letter-spacing: -0.4px;
            line-height: 1.15;
            display: flex;
            align-items: baseline;
            gap: 6px;
            flex-wrap: wrap;
        }

        .stat-card-unit {
            font-size: 13px;
            font-weight: 500;
            color: var(--dash-text-muted);
        }

        .stat-card-sublabel {
            font-size: 12px;
            color: var(--dash-text-muted);
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .stat-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            font-size: 12px;
            color: var(--dash-text-muted);
            padding-top: 12px;
            border-top: 1px solid var(--dash-border-subtle);
        }

        .trend-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11.5px;
            font-weight: 500;
            padding: 2px 8px;
            border-radius: 6px;
            white-space: nowrap;
        }

        .trend-pill.up {
            background: var(--dash-success-subtle);
            color: var(--dash-success);
            border: 1px solid var(--dash-success-border);
        }

        .trend-pill.down {
            background: var(--dash-danger-subtle);
            color: var(--dash-danger);
            border: 1px solid var(--dash-danger-border);
        }

        .trend-pill.neutral {
            background: var(--dash-raised);
            color: var(--dash-text-secondary);
            border: 1px solid var(--dash-border);
        }

        /* Recovery Rate Progress Bar */
        .recovery-track {
            background: var(--dash-raised);
            height: 6px;
            border-radius: 4px;
            overflow: hidden;
            border: 1px solid var(--dash-border);
            margin-top: 8px;
        }

        .recovery-fill {
            height: 100%;
            background: var(--dash-accent);
            border-radius: 4px;
            transition: width 500ms ease;
        }

        /* ══════════════════════════════════════════════════════════════
           3. QUICK ACTIONS STRIP (Quiet, Functional Navigation)
        ══════════════════════════════════════════════════════════════ */
        .quick-actions-bar {
            background: var(--dash-card);
            border: 1px solid var(--dash-border);
            border-radius: var(--radius-card);
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
        }

        .quick-actions-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 500;
            color: var(--dash-text-secondary);
        }

        .quick-actions-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-quick-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            font-size: 12.5px;
            font-weight: 500;
            border-radius: var(--radius-control);
            background: var(--dash-raised);
            border: 1px solid var(--dash-border);
            color: var(--dash-text-primary);
            text-decoration: none;
            transition: background-color 150ms ease, border-color 150ms ease;
        }

        .btn-quick-link i {
            font-size: 15px;
            color: var(--dash-text-muted);
        }

        .btn-quick-link:hover {
            background: #FFFFFF;
            border-color: var(--dash-border-hover);
            color: var(--dash-accent);
        }

        .btn-quick-link:hover i {
            color: var(--dash-accent);
        }

        /* ══════════════════════════════════════════════════════════════
           4. PANELS (Chart & Operations List)
        ══════════════════════════════════════════════════════════════ */
        .dash-panel {
            background: var(--dash-card);
            border: 1px solid var(--dash-border);
            border-radius: var(--radius-card);
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .dash-panel-header {
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--dash-border);
            flex-wrap: wrap;
            gap: 12px;
        }

        .panel-heading h2 {
            font-size: 15px;
            font-weight: 600;
            color: var(--dash-text-primary);
            margin: 0;
            line-height: 1.25;
        }

        .panel-heading p {
            font-size: 12.5px;
            color: var(--dash-text-muted);
            margin: 2px 0 0 0;
            font-weight: 400;
        }

        /* Chart Type Toggle */
        .chart-toggle-control {
            display: inline-flex;
            background: var(--dash-raised);
            border-radius: var(--radius-control);
            border: 1px solid var(--dash-border);
            padding: 2px;
            gap: 2px;
        }

        .btn-toggle-type {
            border: none;
            background: transparent;
            font-size: 12px;
            font-weight: 500;
            color: var(--dash-text-secondary);
            padding: 4px 10px;
            border-radius: 6px;
            cursor: pointer;
            transition: color 150ms ease, background-color 150ms ease;
        }

        .btn-toggle-type.active {
            background: var(--dash-card);
            color: var(--dash-text-primary);
            font-weight: 600;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
            border: 1px solid var(--dash-border);
        }

        /* Chart Meta Stats */
        .chart-meta-bar {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px 20px 0 20px;
            flex-wrap: wrap;
            font-size: 12px;
            color: var(--dash-text-muted);
        }

        .chart-meta-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .chart-meta-item strong {
            color: var(--dash-text-primary);
            font-weight: 600;
        }

        /* Logistics Operations Tiles */
        .service-row-tile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: var(--radius-control);
            background: var(--dash-card);
            border: 1px solid var(--dash-border);
            text-decoration: none;
            color: inherit;
            margin-bottom: 8px;
            transition: border-color 150ms ease, background-color 150ms ease;
        }

        .service-row-tile:last-child {
            margin-bottom: 0;
        }

        .service-row-tile:hover {
            border-color: var(--dash-border-hover);
            background: var(--dash-raised);
            color: inherit;
        }

        .service-tile-symbol {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-control);
            background: var(--dash-raised);
            border: 1px solid var(--dash-border);
            color: var(--dash-text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            flex-shrink: 0;
        }

        .service-tile-title {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--dash-text-primary);
            margin-bottom: 2px;
        }

        .service-tile-caption {
            font-size: 12px;
            color: var(--dash-text-muted);
            margin: 0;
        }

        .service-tile-arrow {
            color: var(--dash-text-muted);
            font-size: 16px;
            margin-left: auto;
        }

        /* ══════════════════════════════════════════════════════════════
           5. RECENT BOOKINGS TABLE (Quiet typography, tabular figures)
        ══════════════════════════════════════════════════════════════ */
        .table-filter-input {
            background: var(--dash-raised);
            border: 1px solid var(--dash-border);
            border-radius: var(--radius-control);
            padding: 6px 12px 6px 32px;
            font-size: 13px;
            font-weight: 400;
            color: var(--dash-text-primary);
            outline: none;
            width: 220px;
            transition: border-color 150ms ease, background-color 150ms ease;
        }

        .table-filter-input:focus {
            background: var(--dash-card);
            border-color: var(--dash-accent);
        }

        .dash-table {
            width: 100%;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .dash-table th {
            font-size: 12px;
            font-weight: 500;
            color: var(--dash-text-secondary);
            background: var(--dash-raised);
            padding: 10px 16px;
            border-bottom: 1px solid var(--dash-border);
            white-space: nowrap;
        }

        .dash-table td {
            padding: 12px 16px;
            font-size: 13.5px;
            color: var(--dash-text-primary);
            vertical-align: middle;
            border-bottom: 1px solid var(--dash-border-subtle);
        }

        .dash-table tr:last-child td {
            border-bottom: none;
        }

        .dash-table tr:hover td {
            background: var(--dash-raised);
        }

        .avatar-initial-dash {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--dash-raised);
            border: 1px solid var(--dash-border);
            color: var(--dash-text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 12px;
            flex-shrink: 0;
        }

        .client-tag {
            font-size: 10px;
            font-weight: 600;
            padding: 1px 5px;
            border-radius: 4px;
            margin-left: 6px;
            border: 1px solid var(--dash-border);
            background: var(--dash-raised);
            color: var(--dash-text-secondary);
        }

        .package-badge-tag {
            display: inline-block;
            font-size: 11.5px;
            font-weight: 500;
            padding: 2px 8px;
            border-radius: 6px;
            background: var(--dash-raised);
            border: 1px solid var(--dash-border);
            color: var(--dash-text-secondary);
            text-transform: capitalize;
        }

        .package-badge-tag.hajj {
            background: var(--dash-accent-subtle);
            color: var(--dash-accent);
            border-color: var(--dash-accent-border);
        }

        .status-badge-paid {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11.5px;
            font-weight: 500;
            padding: 2px 8px;
            border-radius: 6px;
            background: var(--dash-success-subtle);
            color: var(--dash-success);
            border: 1px solid var(--dash-success-border);
        }

        .status-badge-due {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11.5px;
            font-weight: 500;
            padding: 2px 8px;
            border-radius: 6px;
            background: var(--dash-danger-subtle);
            color: var(--dash-danger);
            border: 1px solid var(--dash-danger-border);
        }

        .btn-table-action {
            width: 30px;
            height: 30px;
            border-radius: var(--radius-control);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--dash-card);
            border: 1px solid var(--dash-border);
            color: var(--dash-text-secondary);
            text-decoration: none;
            font-size: 14px;
            transition: border-color 150ms ease, color 150ms ease;
        }

        .btn-table-action:hover {
            border-color: var(--dash-border-hover);
            color: var(--dash-text-primary);
        }
    </style>

    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                {{-- ══════════════════════════════════════════════════════════════
                   1. CONTEXT HEADER BAR
                ══════════════════════════════════════════════════════════════ --}}
                <div class="dash-header-bar mt-3">
                    <div class="header-title-group">
                        <div class="header-icon-box">
                            <i class="{{ $package === 'hajj' ? 'ri-compass-3-line' : 'ri-flight-takeoff-line' }}"></i>
                        </div>
                        <div class="header-title-text">
                            <h1>
                                <span>Operations Dashboard</span>
                                <span class="header-status-badge">
                                    <span class="header-status-dot"></span>
                                    <span>{{ ucfirst($package) }} Cycle {{ $year }}</span>
                                </span>
                            </h1>
                            <p>Pilgrim manifests, logistics velocity, and multi-currency ledgers</p>
                        </div>
                    </div>

                    <div class="header-controls">
                        {{-- Segmented Cycle Switcher --}}
                        <div class="cycle-segmented-control">
                            <a href="{{ route('dashboard', ['package' => 'hajj', 'year' => $year]) }}"
                               class="{{ $package === 'hajj' ? 'active' : '' }}">
                                <i class="ri-government-line"></i>
                                <span>Hajj</span>
                            </a>
                            <a href="{{ route('dashboard', ['package' => 'umrah', 'year' => $year]) }}"
                               class="{{ $package === 'umrah' ? 'active' : '' }}">
                                <i class="ri-building-line"></i>
                                <span>Umrah</span>
                            </a>
                        </div>

                        {{-- Year Selector --}}
                        <form method="GET" action="{{ route('dashboard') }}" id="yearForm" class="m-0">
                            <input type="hidden" name="package" value="{{ $package }}">
                            <div class="dash-select-wrap">
                                <i class="ri-calendar-line text-muted me-1"></i>
                                <select name="year" onchange="document.getElementById('yearForm').submit()">
                                    @foreach ($availableYears as $yr)
                                        <option value="{{ $yr }}" {{ $yr == $year ? 'selected' : '' }}>
                                            Year {{ $yr }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>

                        {{-- Primary Action: New Booking --}}
                        {{-- @can('booking_create')
                            <a href="{{ route('booking.create') }}" class="btn-dash-primary">
                                <i class="ri-add-line"></i>
                                <span>New booking</span>
                            </a>
                        @endcan --}}
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════════
                   2. CORE METRICS (Row 1 - Bookings, Revenue, Collections, Profit)
                ══════════════════════════════════════════════════════════════ --}}
                <div class="row g-3 mb-3">

                    {{-- Metric 1: Confirmed Bookings --}}
                    <div class="col-12 col-md-4">
                        <div class="dash-stat-card">
                            <div>
                                <div class="stat-card-header">
                                    <span class="stat-card-title">{{ ucfirst($package) }} bookings</span>
                                    <div class="stat-card-icon">
                                        <i class="ri-calendar-check-line"></i>
                                    </div>
                                </div>
                                <div class="stat-card-value-block">
                                    <div class="stat-card-value tabular-nums">
                                        <span class="counter-number" data-target="{{ $totalBookings }}">0</span>
                                        <span class="stat-card-unit">files</span>
                                    </div>
                                    <div class="stat-card-sublabel tabular-nums">
                                        <i class="ri-group-line"></i>
                                        <span>{{ number_format($totalPax ?? 0) }} registered pilgrims</span>
                                    </div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span class="trend-pill {{ $bookingsTrend >= 0 ? 'up' : 'down' }} tabular-nums">
                                    <i class="ri-arrow-{{ $bookingsTrend >= 0 ? 'up' : 'down' }}-line"></i>
                                    {{ abs($bookingsTrend) }}% vs last month
                                </span>
                                <span>Confirmed files</span>
                            </div>
                        </div>
                    </div>

                    {{-- Metric: Total Clients --}}
                    <div class="col-12 col-md-4">
                        <div class="dash-stat-card">
                            <div>
                                <div class="stat-card-header">
                                    <span class="stat-card-title">Total clients</span>
                                    <div class="stat-card-icon">
                                        <i class="ri-user-star-line"></i>
                                    </div>
                                </div>
                                <div class="stat-card-value-block">
                                    <div class="stat-card-value tabular-nums">
                                        <span class="counter-number" data-target="{{ $totalClients ?? 0 }}">0</span>
                                        <span class="stat-card-unit">clients</span>
                                    </div>
                                    <div class="stat-card-sublabel">
                                        <i class="ri-contacts-line"></i>
                                        <span>Registered portal clients</span>
                                    </div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span class="trend-pill {{ ($clientsTrend ?? 0) >= 0 ? 'up' : 'down' }} tabular-nums">
                                    <i class="ri-arrow-{{ ($clientsTrend ?? 0) >= 0 ? 'up' : 'down' }}-line"></i>
                                    {{ abs($clientsTrend ?? 0) }}% vs last month
                                </span>
                                <span>Active client base</span>
                            </div>
                        </div>
                    </div>

                    {{-- Metric: Total Client Invoices --}}
                    <div class="col-12 col-md-4">
                        <div class="dash-stat-card">
                            <div>
                                <div class="stat-card-header">
                                    <span class="stat-card-title">Total client invoices</span>
                                    <div class="stat-card-icon">
                                        <i class="ri-file-text-line"></i>
                                    </div>
                                </div>
                                <div class="stat-card-value-block">
                                    <div class="stat-card-value tabular-nums">
                                        <span class="counter-number" data-target="{{ $totalInvoices ?? 0 }}">0</span>
                                        <span class="stat-card-unit">invoices</span>
                                    </div>
                                    <div class="stat-card-sublabel tabular-nums">
                                        <i class="ri-money-dollar-circle-line"></i>
                                        <span>PKR {{ number_format($totalInvoicesAmount ?? 0, 2) }} billed</span>
                                    </div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span class="trend-pill {{ ($invoicesTrend ?? 0) >= 0 ? 'up' : 'down' }} tabular-nums">
                                    <i class="ri-arrow-{{ ($invoicesTrend ?? 0) >= 0 ? 'up' : 'down' }}-line"></i>
                                    {{ abs($invoicesTrend ?? 0) }}% vs last month
                                </span>
                                <span>Generated billing</span>
                            </div>
                        </div>
                    </div>

                    {{-- [TEMPORARILY COMMENTED CARDS - KEPT IN CODE]
                    <!-- Metric 2: Gross Invoiced Revenue -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="dash-stat-card">
                            <div>
                                <div class="stat-card-header">
                                    <span class="stat-card-title">Gross revenue</span>
                                    <div class="stat-card-icon">
                                        <i class="ri-file-list-3-line"></i>
                                    </div>
                                </div>
                                <div class="stat-card-value-block">
                                    <div class="stat-card-value tabular-nums">
                                        <span class="stat-card-unit">PKR</span>
                                        <span class="counter-number" data-target="{{ $totalRevenue }}">0</span>
                                    </div>
                                    <div class="stat-card-sublabel">
                                        <span>Total billed receivables</span>
                                    </div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span class="trend-pill {{ $revenueTrend >= 0 ? 'up' : 'down' }} tabular-nums">
                                    <i class="ri-arrow-{{ $revenueTrend >= 0 ? 'up' : 'down' }}-line"></i>
                                    {{ abs($revenueTrend) }}% vs last month
                                </span>
                                <span>Invoiced volume</span>
                            </div>
                        </div>
                    </div>

                    <!-- Metric 3: Total Collections -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="dash-stat-card">
                            <div>
                                <div class="stat-card-header">
                                    <span class="stat-card-title">Total collected</span>
                                    <div class="stat-card-icon">
                                        <i class="ri-wallet-3-line"></i>
                                    </div>
                                </div>
                                <div class="stat-card-value-block">
                                    <div class="stat-card-value tabular-nums">
                                        <span class="stat-card-unit">PKR</span>
                                        <span class="counter-number" data-target="{{ $totalReceived }}">0</span>
                                    </div>
                                    @php
                                        $collectionRate = $totalRevenue > 0 ? min(100, round(($totalReceived / $totalRevenue) * 100, 1)) : 0;
                                    @endphp
                                    <div class="recovery-track" title="Recovery rate: {{ $collectionRate }}%">
                                        <div class="recovery-fill" style="width: {{ $collectionRate }}%;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span class="trend-pill {{ $receivedTrend >= 0 ? 'up' : 'down' }} tabular-nums">
                                    <i class="ri-arrow-{{ $receivedTrend >= 0 ? 'up' : 'down' }}-line"></i>
                                    {{ abs($receivedTrend) }}%
                                </span>
                                <span class="tabular-nums"><strong>{{ $collectionRate }}%</strong> recovery rate</span>
                            </div>
                        </div>
                    </div>

                    <!-- Metric 4: Net Operating Margin -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="dash-stat-card">
                            <div>
                                <div class="stat-card-header">
                                    <span class="stat-card-title">{{ $isProfit ? 'Net operating margin' : 'Operating deficit' }}</span>
                                    <div class="stat-card-icon">
                                        <i class="ri-scales-3-line"></i>
                                    </div>
                                </div>
                                <div class="stat-card-value-block">
                                    <div class="stat-card-value tabular-nums" style="color: {{ $isProfit ? 'var(--dash-success)' : 'var(--dash-danger)' }};">
                                        <span class="stat-card-unit" style="color: inherit;">{{ $isProfit ? 'PKR' : '- PKR' }}</span>
                                        <span class="counter-number" data-target="{{ abs($profit) }}">0</span>
                                    </div>
                                    <div class="stat-card-sublabel">
                                        <span>Revenue minus operational costs</span>
                                    </div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span class="trend-pill {{ $isProfit ? 'up' : 'down' }}">
                                    {{ $isProfit ? 'Profitable cycle' : 'Deficit review' }}
                                </span>
                                <span>Cycle balance</span>
                            </div>
                        </div>
                    </div>
                    --}}

                </div>

                {{-- [TEMPORARILY COMMENTED: 3. EXPENSES, RECEIVABLES & TEAM (Row 2)]
                <div class="row g-3 mb-3">

                    <!-- Pakistan Expenses -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="dash-stat-card">
                            <div>
                                <div class="stat-card-header">
                                    <span class="stat-card-title">Pakistan expenses</span>
                                    <div class="stat-card-icon">
                                        <i class="ri-bank-card-line"></i>
                                    </div>
                                </div>
                                <div class="stat-card-value-block">
                                    <div class="stat-card-value tabular-nums">
                                        <span class="stat-card-unit">PKR</span>
                                        <span class="counter-number" data-target="{{ $totalExpensesPkr }}">0</span>
                                    </div>
                                    <div class="stat-card-sublabel">
                                        <span>Local operational costs</span>
                                    </div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span class="trend-pill {{ $expensesTrend >= 0 ? 'down' : 'up' }} tabular-nums">
                                    <i class="ri-arrow-{{ $expensesTrend >= 0 ? 'up' : 'down' }}-line"></i>
                                    {{ abs($expensesTrend) }}% vs last month
                                </span>
                                <span>Disbursements</span>
                            </div>
                        </div>
                    </div>

                    <!-- Saudi Operations (SAR) -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="dash-stat-card">
                            <div>
                                <div class="stat-card-header">
                                    <span class="stat-card-title">Saudi operations</span>
                                    <div class="stat-card-icon">
                                        <i class="ri-exchange-dollar-line"></i>
                                    </div>
                                </div>
                                <div class="stat-card-value-block">
                                    <div class="stat-card-value tabular-nums">
                                        <span class="counter-number" data-target="{{ $totalExpensesSar }}">0</span>
                                        <span class="stat-card-unit">SAR</span>
                                    </div>
                                    <div class="stat-card-sublabel tabular-nums">
                                        <span>≈ PKR <strong class="counter-number" data-target="{{ $totalExpensesSarInPkr }}">0</strong></span>
                                    </div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span class="trend-pill neutral">Saudi maktab & hotels</span>
                                <span>Converted to PKR</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Receivables -->
                    @php
                        $outstandingReceivables = max(0, $totalRevenue - $totalReceived);
                        $dueRate = $totalRevenue > 0 ? min(100, round(($outstandingReceivables / $totalRevenue) * 100, 1)) : 0;
                    @endphp
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="dash-stat-card">
                            <div>
                                <div class="stat-card-header">
                                    <span class="stat-card-title">Pending receivables</span>
                                    <div class="stat-card-icon">
                                        <i class="ri-hand-coin-line"></i>
                                    </div>
                                </div>
                                <div class="stat-card-value-block">
                                    <div class="stat-card-value tabular-nums" style="color: {{ $outstandingReceivables > 0 ? 'var(--dash-danger)' : 'var(--dash-text-primary)' }};">
                                        <span class="stat-card-unit" style="color: inherit;">PKR</span>
                                        <span class="counter-number" data-target="{{ $outstandingReceivables }}">0</span>
                                    </div>
                                    <div class="stat-card-sublabel tabular-nums">
                                        <span>{{ $dueRate }}% uncollected balance</span>
                                    </div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span class="trend-pill {{ $outstandingReceivables > 0 ? 'down' : 'up' }}">
                                    {{ $outstandingReceivables > 0 ? 'Awaiting payment' : 'Settled' }}
                                </span>
                                <span>Pilgrim dues pending</span>
                            </div>
                        </div>
                    </div>

                    <!-- Team & Operators -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="dash-stat-card">
                            <div>
                                <div class="stat-card-header">
                                    <span class="stat-card-title">Active team</span>
                                    <div class="stat-card-icon">
                                        <i class="ri-team-line"></i>
                                    </div>
                                </div>
                                <div class="stat-card-value-block">
                                    <div class="stat-card-value tabular-nums">
                                        <span class="counter-number" data-target="{{ $totalUsers }}">0</span>
                                        <span class="stat-card-unit">operators</span>
                                    </div>
                                    <div class="stat-card-sublabel">
                                        <span>Authorized portal staff</span>
                                    </div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span class="trend-pill {{ $usersTrend >= 0 ? 'up' : 'down' }} tabular-nums">
                                    <i class="ri-arrow-{{ $usersTrend >= 0 ? 'up' : 'down' }}-line"></i>
                                    {{ abs($usersTrend) }}%
                                </span>
                                <span>Active users</span>
                            </div>
                        </div>
                    </div>

                </div>
                --}}

                {{-- ══════════════════════════════════════════════════════════════
                   4. QUICK ACTIONS STRIP
                ══════════════════════════════════════════════════════════════ --}}
                <div class="quick-actions-bar">
                    <div class="quick-actions-label">
                        <i class="ri-flashlight-line text-muted"></i>
                        <span>Quick actions:</span>
                    </div>
                    <div class="quick-actions-group">
                        @can('booking_create')
                            <a href="{{ route('booking.create', ['package_type' => $package]) }}" class="btn-quick-link">
                                <i class="ri-add-line"></i> New booking
                            </a>
                        @endcan
                        @can('client_Transactions_view')
                            <a href="{{ route('transaction.create') }}" class="btn-quick-link">
                                <i class="ri-wallet-line"></i> Record payment
                            </a>
                        @endcan
                        @can('expense_view')
                            <a href="{{ route('expense.transaction.create') }}" class="btn-quick-link">
                                <i class="ri-file-add-line"></i> Log expense
                            </a>
                        @endcan
                        @can('booking_view')
                            <a href="{{ route('booking.index', ['package' => $package]) }}" class="btn-quick-link">
                                <i class="ri-file-list-line"></i> Pilgrim manifests
                            </a>
                        @endcan
                        @can('hotel_view')
                            <a href="{{ route('hotel.index') }}" class="btn-quick-link">
                                <i class="ri-hotel-bed-line"></i> Hotel allocations
                            </a>
                        @endcan
                        @can('flight_view')
                            <a href="{{ route('flight.index') }}" class="btn-quick-link">
                                <i class="ri-plane-line"></i> Flight itineraries
                            </a>
                        @endcan
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════════
                   5. MONTHLY INTAKE CHART & LOGISTICS OPERATIONS
                ══════════════════════════════════════════════════════════════ --}}
                <div class="row g-3 mb-4">

                    {{-- Left 8 Cols: ApexCharts Booking Intake --}}
                    <div class="col-12 col-xl-8">
                        <div class="dash-panel">
                            <div class="dash-panel-header">
                                <div class="panel-heading">
                                    <h2>{{ ucfirst($package) }} booking intake — {{ $year }}</h2>
                                    <p>12-month cyclical intake and pilgrim registration volume</p>
                                </div>
                                <div class="chart-toggle-control">
                                    <button type="button" class="btn-toggle-type active" id="btnChartBar" onclick="setChartType('bar')">
                                        Columns
                                    </button>
                                    <button type="button" class="btn-toggle-type" id="btnChartArea" onclick="setChartType('area')">
                                        Spline
                                    </button>
                                </div>
                            </div>

                            @php
                                $totalAnnualBookings = array_sum($monthlyData);
                                $peakMonthlyValue = !empty($monthlyData) ? max($monthlyData) : 0;
                                $peakMonthIndex = !empty($monthlyData) ? array_search($peakMonthlyValue, $monthlyData) : 0;
                                $monthsList = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                $peakMonthName = $monthsList[$peakMonthIndex] ?? 'N/A';
                            @endphp
                            <div class="chart-meta-bar tabular-nums">
                                <div class="chart-meta-item">
                                    <span>Annual volume:</span>
                                    <strong>{{ number_format($totalAnnualBookings) }} files</strong>
                                </div>
                                <div class="chart-meta-item">
                                    <span>Peak month:</span>
                                    <strong>{{ $peakMonthName }} ({{ number_format($peakMonthlyValue) }})</strong>
                                </div>
                                <div class="chart-meta-item">
                                    <span>Monthly average:</span>
                                    <strong>{{ round($totalAnnualBookings / 12, 1) }} / mo</strong>
                                </div>
                            </div>

                            <div class="p-3">
                                <div id="bookings-chart" style="min-height: 290px;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Right 4 Cols: Logistics Allocations --}}
                    <div class="col-12 col-xl-4">
                        <div class="dash-panel">
                            <div class="dash-panel-header">
                                <div class="panel-heading">
                                    <h2>Logistics allocations</h2>
                                    <p>Active service capacity for cycle {{ $year }}</p>
                                </div>
                            </div>

                            <div class="p-3">
                                {{-- Flight Itineraries --}}
                                @can('flight_view')
                                    <a href="{{ route('flight.index') }}" class="service-row-tile">
                                        <div class="service-tile-symbol">
                                            <i class="ri-plane-line"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="service-tile-title">Flight itineraries</div>
                                            <div class="service-tile-caption tabular-nums">{{ number_format($flightsThisMonth) }} flights active this month</div>
                                        </div>
                                        <i class="ri-arrow-right-s-line service-tile-arrow"></i>
                                    </a>
                                @else
                                    <div class="service-row-tile">
                                        <div class="service-tile-symbol">
                                            <i class="ri-plane-line"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="service-tile-title">Flight itineraries</div>
                                            <div class="service-tile-caption tabular-nums">{{ number_format($flightsThisMonth) }} flights active this month</div>
                                        </div>
                                    </div>
                                @endcan

                                {{-- Hotel Allocations --}}
                                @can('hotel_view')
                                    <a href="{{ route('hotel.index') }}" class="service-row-tile">
                                        <div class="service-tile-symbol">
                                            <i class="ri-hotel-bed-line"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="service-tile-title">Hotels & maktabs</div>
                                            <div class="service-tile-caption tabular-nums">{{ number_format($hotelsThisMonth) }} rooms assigned</div>
                                        </div>
                                        <i class="ri-arrow-right-s-line service-tile-arrow"></i>
                                    </a>
                                @else
                                    <div class="service-row-tile">
                                        <div class="service-tile-symbol">
                                            <i class="ri-hotel-bed-line"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="service-tile-title">Hotels & maktabs</div>
                                            <div class="service-tile-caption tabular-nums">{{ number_format($hotelsThisMonth) }} rooms assigned</div>
                                        </div>
                                    </div>
                                @endcan

                                {{-- Visa Approvals --}}
                                @canany(['visa_country_view', 'visa_inquiry_view'])
                                    <a href="{{ Route::has('mobile-ops.visa-countries.index') ? route('mobile-ops.visa-countries.index') : route('mobile-ops.dashboard') }}" class="service-row-tile">
                                        <div class="service-tile-symbol">
                                            <i class="ri-passport-line"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="service-tile-title">Visa e-passes</div>
                                            <div class="service-tile-caption tabular-nums">{{ number_format($visasApproved) }} approvals issued</div>
                                        </div>
                                        <i class="ri-arrow-right-s-line service-tile-arrow"></i>
                                    </a>
                                @else
                                    <div class="service-row-tile">
                                        <div class="service-tile-symbol">
                                            <i class="ri-passport-line"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="service-tile-title">Visa e-passes</div>
                                            <div class="service-tile-caption tabular-nums">{{ number_format($visasApproved) }} approvals issued</div>
                                        </div>
                                    </div>
                                @endcan

                                {{-- Pilgrim Manifests --}}
                                <a href="{{ route('booking.index', ['package' => $package]) }}" class="service-row-tile">
                                    <div class="service-tile-symbol">
                                        <i class="ri-group-line"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="service-tile-title">{{ ucfirst($package) }} group manifests</div>
                                        <div class="service-tile-caption tabular-nums">{{ number_format($totalBookings) }} files &bull; {{ number_format($totalPax ?? 0) }} pilgrims</div>
                                    </div>
                                    <i class="ri-arrow-right-s-line service-tile-arrow"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ══════════════════════════════════════════════════════════════
                   6. RECENT CONFIRMED BOOKINGS TABLE
                ══════════════════════════════════════════════════════════════ --}}
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <div class="dash-panel">
                            <div class="dash-panel-header">
                                <div class="panel-heading">
                                    <h2>Recent confirmed bookings</h2>
                                    <p>Latest pilgrim registrations and agency allocations</p>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <div class="position-relative">
                                        <i class="ri-search-line position-absolute top-50 translate-middle-y ms-2 text-muted" style="font-size: 13px;"></i>
                                        <input type="text" id="bookingSearchInput" class="table-filter-input" placeholder="Filter by client...">
                                    </div>

                                    <a href="{{ route('booking.index', ['package' => $package]) }}" class="btn btn-sm btn-outline-secondary" style="border-radius: var(--radius-control); font-size: 13px; font-weight: 500;">
                                        <span>View all</span>
                                        <i class="ri-arrow-right-line ms-1"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="dash-table" id="recentBookingsTable">
                                    <thead>
                                        <tr>
                                            <th>Client or entity</th>
                                            <th>Package</th>
                                            <th>Pax</th>
                                            <th>Total (PKR)</th>
                                            <th>Payment status</th>
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
                                            @endphp
                                            <tr class="booking-row" data-client="{{ strtolower($clientName) }}">
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="avatar-initial-dash">
                                                            {{ $initials ?: 'BH' }}
                                                        </div>
                                                        <div>
                                                            <div style="font-weight: 500; font-size: 13.5px; color: var(--dash-text-primary);">
                                                                {{ $clientName }}
                                                                @if ($isCompanyBooking)
                                                                    <span class="client-tag">Company</span>
                                                                @else
                                                                    <span class="client-tag">Individual</span>
                                                                @endif
                                                            </div>
                                                            <div style="font-size: 12px; color: var(--dash-text-muted);">
                                                                {{ $clientEmail ?: 'Ref #' . $booking->id }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="package-badge-tag {{ $booking->package_type }}">
                                                        {{ ucfirst($booking->package_type) }}
                                                    </span>
                                                </td>
                                                <td class="tabular-nums" style="font-weight: 500;">
                                                    {{ $booking->no_of_pax ?? '1' }}
                                                </td>
                                                <td class="tabular-nums" style="font-weight: 600; color: var(--dash-text-primary);">
                                                    {{ number_format($booking->total_amount) }}
                                                </td>
                                                <td>
                                                    @if (($booking->balance ?? 0) <= 0)
                                                        <span class="status-badge-paid">
                                                            <i class="ri-checkbox-circle-line"></i> Paid in full
                                                        </span>
                                                    @else
                                                        <span class="status-badge-due tabular-nums">
                                                            <i class="ri-time-line"></i> Due: {{ number_format($booking->balance) }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-inline-flex align-items-center gap-1">
                                                        @can('booking_view')
                                                            <a href="{{ route('booking.show', $booking->id) }}" class="btn-table-action" title="View booking">
                                                                <i class="ri-eye-line"></i>
                                                            </a>
                                                        @endcan
                                                        <a href="{{ route('booking.voucher', $booking->id) }}" class="btn-table-action" title="Voucher" target="_blank">
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
                                                                 style="width: 44px; height: 44px; border-radius: var(--radius-control); background: var(--dash-raised); border: 1px solid var(--dash-border); font-size: 20px; color: var(--dash-text-muted);">
                                                                <i class="ri-inbox-line"></i>
                                                            </div>
                                                        </div>
                                                        <div style="font-weight: 500; font-size: 14px; color: var(--dash-text-primary);" class="mb-1">
                                                            No {{ ucfirst($package) }} bookings found
                                                        </div>
                                                        <p class="text-muted fs-12 mb-3">No pilgrim booking records found for cycle {{ $year }}.</p>
                                                        @can('booking_create')
                                                            <a href="{{ route('booking.create', ['package_type' => $package]) }}" class="btn-dash-primary" style="display: inline-flex;">
                                                                <i class="ri-add-line"></i> Record first booking
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
       INTERACTIVE LOGIC: COUNTERS, FILTER & APEXCHARTS
    ══════════════════════════════════════════════════════════════ --}}
    <script>
        let bookingsChartInstance = null;

        document.addEventListener('DOMContentLoaded', function() {
            // ── 1. NUMBER COUNT-UP (Tabular formatting) ──
            const counters = document.querySelectorAll('.counter-number');
            const duration = 750; // ms

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

            // ── 2. LIVE FILTER FOR RECENT BOOKINGS TABLE ──
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

            // ── 3. APEXCHARTS (Deliberate single accent, quiet hairline grid) ──
            const isHajj = '{{ $package }}' === 'hajj';
            const accentColor = isHajj ? '#8A6618' : '#047857';
            const monthlyData = {!! json_encode($monthlyData) !!};

            const chartOptions = {
                series: [{
                    name: '{{ ucfirst($package) }} bookings',
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
                        speed: 400
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '38%',
                        dataLabels: { position: 'top' }
                    }
                },
                colors: [accentColor],
                fill: {
                    opacity: 0.95
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 0,
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
                            fontWeight: 500
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
                    strokeDashArray: 3,
                    xaxis: { lines: { show: false } }
                },
                tooltip: {
                    theme: 'light',
                    style: { fontFamily: 'Plus Jakarta Sans, sans-serif' },
                    y: {
                        formatter: function(val) {
                            return val + " files";
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

        // ── 4. CHART TOGGLE (COLUMNS VS SPLINE) ──
        function setChartType(type) {
            if (!bookingsChartInstance) return;

            const isHajj = '{{ $package }}' === 'hajj';
            const accentColor = isHajj ? '#8A6618' : '#047857';

            document.querySelectorAll('.btn-toggle-type').forEach(btn => btn.classList.remove('active'));

            if (type === 'bar') {
                document.getElementById('btnChartBar').classList.add('active');
                bookingsChartInstance.updateOptions({
                    chart: { type: 'bar' },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            columnWidth: '38%'
                        }
                    },
                    stroke: {
                        show: false,
                        width: 0
                    },
                    fill: {
                        type: 'solid',
                        opacity: 0.95
                    }
                });
            } else if (type === 'area') {
                document.getElementById('btnChartArea').classList.add('active');
                bookingsChartInstance.updateOptions({
                    chart: { type: 'area' },
                    stroke: {
                        show: true,
                        curve: 'smooth',
                        width: 2.5,
                        colors: [accentColor]
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: 'vertical',
                            shadeIntensity: 0.2,
                            opacityFrom: 0.25,
                            opacityTo: 0.02,
                            stops: [0, 100]
                        }
                    }
                });
            }
        }
    </script>
@endsection
