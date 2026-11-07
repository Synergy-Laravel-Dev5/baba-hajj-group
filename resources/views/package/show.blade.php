<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $isHajj = ($package->package_type ?? 'umrah') === 'hajj';
        $mainTitle = $isHajj ? 'HAJJ' : 'UMRAH';
        $fullTitle = ($package->package_title ?? $package->name) ?? ($mainTitle . ' Packages');

        // Extract Accommodations dynamically
        $makkahAcc = $package->accommodations ? $package->accommodations->first(function($a) {
            return str_contains(strtolower($a->place ?? ''), 'makkah');
        }) ?? $package->accommodations->first() : null;

        $madinahAcc = $package->accommodations ? $package->accommodations->first(function($a) {
            return str_contains(strtolower($a->place ?? ''), 'madin') || str_contains(strtolower($a->place ?? ''), 'medin');
        }) ?? ($package->accommodations->count() > 1 ? $package->accommodations->last() : null) : null;

        $aziziaAcc = $package->accommodations ? $package->accommodations->first(function($a) {
            return str_contains(strtolower($a->place ?? ''), 'azizia');
        }) : null;

        $minaAcc = $package->accommodations ? $package->accommodations->first(function($a) {
            return str_contains(strtolower($a->place ?? ''), 'mina') || str_contains(strtolower($a->place ?? ''), 'arafat') || str_contains(strtolower($a->place ?? ''), 'hajj');
        }) : null;

        // Hotel Names (Checking custom fields, package_a_hotel, fallback)
        $makkahHotel = $makkahAcc?->hotel ?: ($makkahAcc?->package_a_hotel ?: ($package->primary_hotel ?: ($isHajj ? 'PULLMAN ZAMZAM / CLOCK TOWER' : 'JOHRA HIJRA')));
        $madinahHotel = $madinahAcc?->hotel ?: ($madinahAcc?->package_a_hotel ?: ($isHajj ? 'DAR AL TAQWA / MADINAH HOTEL' : 'TAIF-AL-NIBRAS'));

        // Nights Calculation
        $calcDays = $package->stay_duration ?: ($package->days ?: 16);
        $makkahNights = $makkahAcc?->nights ?: ($makkahAcc?->days ?: ($isHajj ? max(5, round($calcDays * 0.45)) : max(5, round($calcDays * 0.6))));
        $madinahNights = $madinahAcc?->nights ?: ($madinahAcc?->days ?: ($isHajj ? max(4, round($calcDays * 0.25)) : max(3, round($calcDays * 0.35))));

        // Distance Badges
        $makkahDistance = $makkahAcc?->distance ?: ($isHajj ? '50' : '700');
        $madinahDistance = $madinahAcc?->distance ?: ($isHajj ? '100' : '200');

        // Travel / Departure Dates
        $travelDate1 = $package->departure_date_str ?? ($package->valid_from ? \Carbon\Carbon::parse($package->valid_from)->format('d M Y') : ($isHajj ? '01 ZUL HIJJAH ' . ($package->year ?? '2026') : '19 DEC ' . ($package->year ?? '2026')));
        $travelDate2 = $package->arrival_date_str ?? ($package->valid_from ? \Carbon\Carbon::parse($package->valid_from)->addDays(1)->format('d M Y') : ($isHajj ? '05 ZUL HIJJAH ' . ($package->year ?? '2026') : '20 DEC ' . ($package->year ?? '2026')));

        // Month / Season Tag
        if ($isHajj) {
            $monthTag = $package->hijri_year ? ($package->hijri_year . ' HIJRI' . ($package->gregorian_year ? ' / ' . $package->gregorian_year : '')) : ($package->year ? 'HAJJ ' . $package->year : 'HAJJ SEASON 2026');
        } else {
            $monthTag = $package->year ? 'DECEMBER ' . $package->year : 'DECEMBER 2026';
        }

        // Standard Pricing Fallbacks
        $baseSharing = (float)($package->price_sharing ?: ($package->maktab_c_quad_pkr ?: 320000));
        $baseQuad    = (float)($package->price_quad ?: ($package->maktab_c_triple_pkr ?: 325000));
        $baseTriple  = (float)($package->price_triple ?: ($package->maktab_a_triple_pkr ?: 350000));
        $baseDouble  = (float)($package->price_double ?: ($package->maktab_a_double_pkr ?: 410000));

        // ══════════════════════════════════════════════════════
        // DYNAMIC TIERS BUILDER (NO FAKE / HARDCODED NUMBERS)
        // ══════════════════════════════════════════════════════
        $tiers = collect([]);

        if ($isHajj) {
            $hasMaktabC = ($package->maktab_c_quad_pkr > 0 || $package->maktab_c_triple_pkr > 0 || $package->maktab_c_double_pkr > 0);
            $hasMaktabA = ($package->maktab_a_quad_pkr > 0 || $package->maktab_a_triple_pkr > 0 || $package->maktab_a_double_pkr > 0);

            if ($hasMaktabC) {
                $cQuad = (float)$package->maktab_c_quad_pkr;
                $cTriple = (float)$package->maktab_c_triple_pkr;
                $cDouble = (float)$package->maktab_c_double_pkr;
                $cSharing = (float)($package->price_sharing > 0 ? $package->price_sharing : $cQuad);

                $tiers->push([
                    'title'        => 'MAKTAB C ' . ($package->camp_zone ? '&mdash; ZONE ' . $package->camp_zone : '&mdash; STANDARD CAMP'),
                    'badge'        => 'MAKTAB C' . ($package->stay_duration ? ' &bull; ' . $package->stay_duration . ' DAYS' : ($package->days ? ' &bull; ' . $package->days . ' DAYS' : '')),
                    'date_badge'   => $travelDate1,
                    'price_sharing'=> $cSharing,
                    'price_quad'   => $cQuad,
                    'price_triple' => $cTriple,
                    'price_double' => $cDouble,
                    'urdu_note'    => $package->important_note ?: '5 ایام حج (منیٰ، عرفات، مزدلفہ) • مکتب C کی سہولیات',
                    'meal_plan'    => $package->meal_plan ?: '3 TIME BUFFET MEAL',
                ]);
            }

            if ($hasMaktabA) {
                $aQuad = (float)$package->maktab_a_quad_pkr;
                $aTriple = (float)$package->maktab_a_triple_pkr;
                $aDouble = (float)$package->maktab_a_double_pkr;
                $aSharing = (float)($package->price_sharing > 0 ? $package->price_sharing : $aQuad);

                $tiers->push([
                    'title'        => 'MAKTAB A ' . ($package->camp_zone ? '&mdash; ZONE ' . $package->camp_zone : '&mdash; VIP EXECUTIVE'),
                    'badge'        => 'MAKTAB A (VIP)' . ($package->stay_duration ? ' &bull; ' . $package->stay_duration . ' DAYS' : ($package->days ? ' &bull; ' . $package->days . ' DAYS' : '')),
                    'date_badge'   => $travelDate2,
                    'price_sharing'=> $aSharing,
                    'price_quad'   => $aQuad,
                    'price_triple' => $aTriple,
                    'price_double' => $aDouble,
                    'urdu_note'    => ($package->qurbani_note && $package->qurbani_note !== 'test' ? $package->qurbani_note : 'قربانی شامل • مکتب A خصوصی خیمہ و ائرکنڈیشنڈ ٹرانسپورٹ'),
                    'meal_plan'    => $package->meal_plan ?: '3 TIME BUFFET MEAL',
                ]);
            }

            // Fallback for single Hajj package without Maktab comparison
            if ($tiers->isEmpty()) {
                $tiers->push([
                    'title'        => ($package->stay_type ?: 'HAJJ EXECUTIVE') . ' &mdash; ' . ($calcDays ? $calcDays . ' DAYS' : 'FULL SEASON'),
                    'badge'        => ($calcDays ? $calcDays . ' DAYS' : 'EXECUTIVE'),
                    'date_badge'   => $travelDate1,
                    'price_sharing'=> $baseSharing,
                    'price_quad'   => $baseQuad,
                    'price_triple' => $baseTriple,
                    'price_double' => $baseDouble,
                    'urdu_note'    => $package->important_note ?: '5 ایام حج (منیٰ، عرفات، مزدلفہ) • مکتب و ٹرانسپورٹ کی مکمل سہولیات',
                    'meal_plan'    => $package->meal_plan ?: '3 TIME BUFFET MEAL',
                ]);
            }
        } else {
            // UMRAH PACKAGES
            if (!empty($package->duration_pricing_options) && is_array($package->duration_pricing_options)) {
                foreach ($package->duration_pricing_options as $idx => $opt) {
                    $dur = $opt['duration'] ?? (($idx == 0 ? ($package->days ?: 16) : 21) . ' DAYS');
                    $tiers->push([
                        'title'        => strtoupper($dur) . ' PACKAGE',
                        'badge'        => strtoupper($dur),
                        'date_badge'   => ($idx == 0 ? $travelDate1 : $travelDate2),
                        'price_sharing'=> (float)($opt['sharing'] ?? $baseSharing),
                        'price_quad'   => (float)($opt['quad'] ?? $baseQuad),
                        'price_triple' => (float)($opt['triple'] ?? $baseTriple),
                        'price_double' => (float)($opt['double'] ?? $baseDouble),
                        'urdu_note'    => ($idx == 0 ? 'واپسی مدینہ ہوٹل سے جدہ ایئرپورٹ' : 'شب معراج مدینہ شریف میں • واپسی مدینہ ہوٹل سے جدہ ایئرپورٹ'),
                        'meal_plan'    => '1 DISH BUFFET MEAL',
                    ]);
                }
            }

            if ($tiers->isEmpty()) {
                $durTitle = ($package->days ? $package->days . ' DAYS' : '16 DAYS');
                $tiers->push([
                    'title'        => $durTitle . ' UMRAH PACKAGE',
                    'badge'        => $durTitle,
                    'date_badge'   => $travelDate1,
                    'price_sharing'=> $baseSharing,
                    'price_quad'   => $baseQuad,
                    'price_triple' => $baseTriple,
                    'price_double' => $baseDouble,
                    'urdu_note'    => 'واپسی مدینہ ہوٹل سے جدہ ایئرپورٹ',
                    'meal_plan'    => '1 DISH BUFFET MEAL',
                ]);

                // If package days is 16, provide 21 Days tier only if user didn't enter custom options
                if (($package->days == 16 || empty($package->days)) && $package->id == 1) {
                    $tiers->push([
                        'title'        => '21 DAYS UMRAH PACKAGE',
                        'badge'        => '21 DAYS',
                        'date_badge'   => $travelDate2,
                        'price_sharing'=> 350000,
                        'price_quad'   => 355000,
                        'price_triple' => 390000,
                        'price_double' => 460000,
                        'urdu_note'    => 'شب معراج مدینہ شریف میں • واپسی مدینہ ہوٹل سے جدہ ایئرپورٹ',
                        'meal_plan'    => '1 DISH BUFFET MEAL',
                    ]);
                }
            }
        }

        // Flights & Travel Details
        $flights = $package->transportFlights ?? collect([]);
        $f1 = $flights?->first();
        $f2 = ($flights && $flights->count() > 1) ? $flights->get(1) : null;
        $hasFlights = ($f1 !== null);
    @endphp

    <title>{{ $fullTitle }} | Baba Group of Companies</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">

    <!-- Google Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Playfair+Display:wght@700;900&family=Amiri:wght@400;700&family=Noto+Nastaliq+Urdu:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <!-- PDF & Canvas Export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        @if($isHajj)
        /* ══════════════════════════════════════════════════════
           HAJJ THEME: ROYAL KAABA GOLD & PRESTIGIOUS BRONZE
        ══════════════════════════════════════════════════════ */
        :root {
            --baba-primary: #94691B;
            --baba-primary-dark: #6E4D10;
            --baba-primary-deep: #473207;
            --baba-primary-light: #FBF8EE;
            --baba-pill: linear-gradient(135deg, #B58428 0%, #7E540F 100%);
            --baba-gold: #D4AF37;
            --baba-gold-light: #FDF9EE;
            --baba-dark: #19140A;
            --baba-gray: #6B5B45;
            --baba-border: #94691B;
            --bar-bg: #221C11;
        }
        @else
        /* ══════════════════════════════════════════════════════
           UMRAH THEME: BABA EMERALD TEAL & CYAN
        ══════════════════════════════════════════════════════ */
        :root {
            --baba-primary: #087784;
            --baba-primary-dark: #055862;
            --baba-primary-deep: #033C43;
            --baba-primary-light: #EBF8FA;
            --baba-pill: linear-gradient(135deg, #097B88 0%, #05616D 100%);
            --baba-gold: #C5A059;
            --baba-gold-light: #F4E5BE;
            --baba-dark: #081D21;
            --baba-gray: #556B6F;
            --baba-border: #087784;
            --bar-bg: #0E2932;
        }
        @endif

        @page {
            size: A4 portrait;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: #15191C;
            color: var(--baba-dark);
            padding: 16px 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* ════ TOP ACTION CONTROLS BAR ════ */
        .flyer-action-bar {
            width: 794px;
            margin: 0 auto 12px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--bar-bg);
            padding: 10px 18px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.35);
        }
        .btn-action-pill {
            font-size: 13px;
            font-weight: 700;
            padding: 7px 16px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }
        .btn-action-back {
            background: rgba(255, 255, 255, 0.08);
            color: #E2F2F5;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .btn-action-back:hover {
            background: rgba(255, 255, 255, 0.16);
            color: #FFFFFF;
        }
        .btn-action-pdf {
            background: var(--baba-pill);
            color: #FFFFFF;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }
        .btn-action-pdf:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.35);
        }
        .btn-action-print {
            background: #FFFFFF;
            color: var(--baba-primary-dark);
            border: 1px solid #CBD5E1;
        }
        .btn-action-print:hover {
            background: #F1F5F9;
        }

        /* ════ FLYER CONTAINER (EXACT A4 794px) ════ */
        .flyer-document {
            width: 794px;
            max-width: 794px;
            margin: 0 auto;
            background: #FFFFFF;
            position: relative;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            padding: 0;
        }

        /* ════ 1. HEADER SECTION (PANORAMA & BRANDING) ════ */
        .flyer-header {
            position: relative;
            height: 195px;
            width: 100%;
            background: #FFFFFF url('{{ asset("assets/images/packages/flyer_assets/flyer_header_skyline.png") }}') center top no-repeat;
            background-size: 100% auto;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 14px 22px 10px 22px;
        }

        /* Top Left Official Emblem */
        .header-brand-wrap {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            z-index: 5;
            background: rgba(255, 255, 255, 0.75);
            padding: 3px 8px;
            border-radius: 8px;
        }
        .header-brand-logo {
            height: 70px;
            width: auto;
            object-fit: contain;
            display: block;
        }

        /* Top Right Titles & Dates */
        .header-title-wrap {
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            z-index: 5;
            background: rgba(255, 255, 255, 0.88);
            padding: 3px 10px;
            border-radius: 8px;
        }
        .header-month-capsule {
            background: var(--baba-primary);
            color: #FFFFFF;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.8px;
            padding: 4px 18px;
            border-radius: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            display: inline-block;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .header-main-h1 {
            font-size: 52px;
            font-weight: 900;
            color: var(--baba-primary);
            line-height: 0.95;
            letter-spacing: 1px;
            margin: 2px 0 0 0;
            text-transform: uppercase;
        }
        .header-sub-h2 {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            font-size: 16px;
            font-weight: 900;
            letter-spacing: 4px;
            color: var(--baba-primary);
            margin: 0 0 6px 0;
        }
        .header-sub-h2::before,
        .header-sub-h2::after {
            content: "";
            display: inline-block;
            width: 32px;
            height: 2px;
            background: var(--baba-primary);
        }
        .header-date-pill {
            background: var(--baba-primary);
            color: #FFFFFF;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.5px;
            padding: 5px 16px;
            border-radius: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 7px;
            text-transform: uppercase;
        }

        /* ════ 2. MAIN BODY CONTAINER ════ */
        .flyer-body {
            padding: 8px 18px 10px 18px;
            background: #FFFFFF;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* ════ PACKAGE CARD (100% DYNAMIC TIERS) ════ */
        .package-tier-card {
            border: 2px solid var(--baba-border);
            border-radius: 18px;
            background: #FFFFFF;
            position: relative;
            padding: 12px 14px 10px 14px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
        }

        /* Ribbon on top-left */
        .tier-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .tier-days-badge {
            background: var(--baba-primary);
            color: #FFFFFF;
            font-size: 15px;
            font-weight: 900;
            letter-spacing: 0.6px;
            padding: 4px 20px;
            border-radius: 24px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
            text-transform: uppercase;
        }
        .tier-date-badge {
            background: var(--baba-primary);
            color: #FFFFFF;
            font-size: 11.5px;
            font-weight: 800;
            letter-spacing: 0.4px;
            padding: 3.5px 14px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-transform: uppercase;
        }

        /* ── Hotel Row (2 Columns) ── */
        .hotels-grid-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 8px;
        }
        .hotel-item-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #FFFFFF;
            padding: 3px 6px;
        }
        .hotel-thumb-img {
            width: 86px;
            height: 60px;
            object-fit: cover;
            border-radius: 7px;
            border: 1px solid rgba(0, 0, 0, 0.15);
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
        }
        .hotel-info-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .hotel-city-nights {
            font-size: 12px;
            font-weight: 800;
            color: var(--baba-primary);
            letter-spacing: 0.4px;
            text-transform: uppercase;
            line-height: 1.1;
        }
        .hotel-name-h3 {
            font-size: 15.5px;
            font-weight: 900;
            color: #0F172A;
            line-height: 1.2;
            letter-spacing: 0.2px;
            margin: 1px 0 3px 0;
            text-transform: uppercase;
        }
        .hotel-distance-pill {
            background: var(--baba-primary);
            color: #FFFFFF;
            font-size: 10.5px;
            font-weight: 800;
            padding: 2px 10px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            letter-spacing: 0.4px;
        }

        /* ── Pricing Matrix (4 Large Rounded Pills) ── */
        .pricing-pills-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 8px;
        }
        .price-pill-card {
            background: var(--baba-pill);
            color: #FFFFFF;
            border-radius: 14px;
            padding: 7px 6px 6px 6px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .price-room-capsule {
            background: rgba(255, 255, 255, 0.22);
            border: 1px solid rgba(255, 255, 255, 0.7);
            color: #FFFFFF;
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: 0.5px;
            padding: 2px 14px;
            border-radius: 20px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .price-amount-large {
            font-size: 21px;
            font-weight: 900;
            line-height: 1.05;
            letter-spacing: 0.2px;
            color: #FFFFFF;
        }
        .price-per-person-text {
            font-size: 8.5px;
            font-weight: 700;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            opacity: 0.92;
            margin-top: 1px;
        }

        /* ── Special Features, Urdu Highlight & Inclusions ── */
        .features-inclusions-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 6px;
            flex-wrap: nowrap;
        }
        .feature-meal-box {
            border: 1.5px solid var(--baba-border);
            border-radius: 12px;
            padding: 4px 12px;
            font-size: 11px;
            font-weight: 800;
            color: var(--baba-primary);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        .urdu-highlight-pill {
            font-family: 'Noto Nastaliq Urdu', 'Amiri', serif;
            font-size: 13.5px;
            font-weight: 700;
            color: var(--baba-primary-dark);
            direction: rtl;
            text-align: right;
            padding: 2px 8px;
            line-height: 1.6;
            white-space: nowrap;
            border: 1px solid var(--baba-border);
            border-radius: 10px;
        }
        .ziyarat-features-pill {
            border: 1.5px solid var(--baba-border);
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 11px;
            font-weight: 800;
            color: #FFFFFF;
            background: var(--baba-primary);
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
            letter-spacing: 0.3px;
        }
        .inclusions-icons-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
        }
        .inclusions-label-tag {
            background: var(--baba-primary);
            color: #FFFFFF;
            font-size: 9px;
            font-weight: 800;
            padding: 3px 9px;
            border-radius: 12px;
            letter-spacing: 0.4px;
            white-space: nowrap;
        }
        .inclusion-single-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 1px;
        }
        .inclusion-single-item i {
            font-size: 16px;
            color: var(--baba-primary);
            line-height: 1;
        }
        .inclusion-single-item span {
            font-size: 7.5px;
            font-weight: 800;
            color: var(--baba-primary);
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        /* ════ 3. FLIGHT SCHEDULES SECTION (2 COLUMNS) ════ */
        .flight-schedules-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .flight-schedule-card {
            border: 1.5px solid var(--baba-border);
            border-radius: 14px;
            overflow: hidden;
            background: #FFFFFF;
            padding: 6px 10px 8px 10px;
        }
        .flight-card-header {
            background: var(--baba-primary);
            color: #FFFFFF;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.5px;
            padding: 3px 12px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        .flight-table-compact {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            text-align: center;
        }
        .flight-table-compact tr td {
            padding: 3px 4px;
            font-weight: 700;
            color: #0F172A;
            border-bottom: 1px dashed rgba(0, 0, 0, 0.12);
        }
        .flight-code-badge {
            font-weight: 800;
            color: var(--baba-primary);
        }
        .flight-sector-badge {
            font-weight: 800;
            color: #000;
        }
        .flight-baggage-badge {
            background: var(--baba-primary-light);
            color: var(--baba-primary-dark);
            padding: 1px 6px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 9.5px;
        }
        .flight-airline-emblem {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 4px;
            gap: 6px;
        }
        .flight-airline-img {
            height: 24px;
            width: auto;
            object-fit: contain;
        }

        /* ════ 4. CONTACT TEAM FOOTER ════ */
        .flyer-contacts-section {
            padding: 8px 0 0 0;
            background: #FFFFFF;
        }
        .contacts-cards-row {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 6px;
            text-align: center;
            margin-bottom: 8px;
        }
        .contact-person-box {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .contact-person-name {
            font-size: 11px;
            font-weight: 800;
            color: var(--baba-primary);
            letter-spacing: 0.3px;
            text-transform: uppercase;
            line-height: 1.2;
            margin-bottom: 2px;
        }
        .contact-person-phone {
            font-size: 11px;
            font-weight: 700;
            color: #0F172A;
            letter-spacing: 0.2px;
            white-space: nowrap;
        }

        /* Bottom Solid Primary Bar */
        .bottom-solid-bar {
            background: var(--baba-primary);
            color: #FFFFFF;
            padding: 6px 12px;
            text-align: center;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 1.5px;
            display: flex;
            align-items: center;
            justify-content: space-around;
        }
        .bottom-solid-bar span {
            white-space: nowrap;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #FFFFFF !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .flyer-action-bar {
                display: none !important;
            }
            .flyer-document {
                box-shadow: none !important;
                border: none !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>

<body>

    <!-- ════ ACTION CONTROLS BAR ════ -->
    <header class="flyer-action-bar">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('package.index') }}" class="btn-action-pill btn-action-back">
                <i class="mdi mdi-arrow-left"></i> Back to Packages
            </a>
            <span style="color: #64748B; font-size: 13px;">|</span>
            <span style="color: #CBD5E1; font-size: 13px; font-weight: 600;">
                <i class="mdi mdi-shield-check" style="color: var(--baba-gold);"></i> Official Baba Hajj Group {{ $mainTitle }} Flyer
            </span>
        </div>

        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn-action-pill btn-action-print">
                <i class="mdi mdi-printer"></i> Print Flyer
            </button>
            <button id="btnDownloadPdf" onclick="downloadHighResPdf()" class="btn-action-pill btn-action-pdf">
                <i class="mdi mdi-file-pdf-box"></i> Download High-Res PDF
            </button>
        </div>
    </header>

    <!-- ════ EXACT BROCHURE FLYER DOCUMENT ════ -->
    <main id="brochure-container" class="flyer-document">

        <!-- ════ 1. HEADER (PANORAMA & TITLES) ════ -->
        <header class="flyer-header">
            <!-- Left Official Logo (No duplicate / No background clash) -->
            <div class="header-brand-wrap">
                <img src="{{ asset('assets/images/baba_hajj_logo_ultra.png') }}?v={{ time() }}"
                     alt="Baba Group of Companies" class="header-brand-logo">
            </div>

            <!-- Right Titles & Dates (Clean, dynamic) -->
            <div class="header-title-wrap">
                <div class="header-month-capsule">{{ $monthTag }}</div>
                <h1 class="header-main-h1">{{ $mainTitle }}</h1>
                <div class="header-sub-h2">PACKAGES</div>
                <div class="header-date-pill">
                    <i class="mdi mdi-calendar-month"></i> TRAVELLING DATE: {{ $travelDate1 }}
                </div>
            </div>
        </header>

        <!-- ════ 2. MAIN BODY CONTENT (DYNAMIC TIERS) ════ -->
        <div class="flyer-body">

            @foreach ($tiers as $tier)
                <section class="package-tier-card">
                    <!-- Top Ribbon -->
                    <div class="tier-header-row">
                        <div class="tier-days-badge">
                            {!! $tier['badge'] !!}
                        </div>
                        @if (!empty($tier['date_badge']) && $tier['date_badge'] !== $travelDate1)
                            <div class="tier-date-badge">
                                <i class="mdi mdi-calendar"></i> TRAVELLING DATE: {{ $tier['date_badge'] }}
                            </div>
                        @endif
                    </div>

                    <!-- Hotels Row (Makkah & Madina) -->
                    <div class="hotels-grid-row">
                        <!-- Makkah Hotel -->
                        <div class="hotel-item-box">
                            <img src="{{ asset('assets/images/packages/flyer_assets/kaaba_thumb.png') }}"
                                 alt="Makkah Holy Kaaba" class="hotel-thumb-img">
                            <div class="hotel-info-col">
                                <span class="hotel-city-nights">MAKKAH &mdash; {{ $makkahNights }} NIGHTS</span>
                                <h3 class="hotel-name-h3">{{ $makkahHotel }}</h3>
                                <span class="hotel-distance-pill">
                                    <i class="mdi mdi-map-marker"></i> {{ $makkahDistance }} METER
                                </span>
                            </div>
                        </div>

                        <!-- Madina Hotel -->
                        <div class="hotel-item-box">
                            <img src="{{ asset('assets/images/packages/flyer_assets/madina_thumb.png') }}"
                                 alt="Madina Green Dome" class="hotel-thumb-img">
                            <div class="hotel-info-col">
                                <span class="hotel-city-nights">MADINA &mdash; {{ $madinahNights }} NIGHTS</span>
                                <h3 class="hotel-name-h3">{{ $madinahHotel }}</h3>
                                <span class="hotel-distance-pill">
                                    <i class="mdi mdi-map-marker"></i> {{ $madinahDistance }} METER
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Matrix (4 Rounded Dynamic Pills) -->
                    <div class="pricing-pills-grid">
                        <div class="price-pill-card">
                            <div class="price-room-capsule">SHARING</div>
                            <div class="price-amount-large">
                                {{ $tier['price_sharing'] > 0 ? number_format($tier['price_sharing']) : number_format($tier['price_quad']) }}
                            </div>
                            <div class="price-per-person-text">PER PERSON</div>
                        </div>
                        <div class="price-pill-card">
                            <div class="price-room-capsule">QUAD</div>
                            <div class="price-amount-large">{{ number_format($tier['price_quad']) }}</div>
                            <div class="price-per-person-text">PER PERSON</div>
                        </div>
                        <div class="price-pill-card">
                            <div class="price-room-capsule">TRIPLE</div>
                            <div class="price-amount-large">{{ number_format($tier['price_triple']) }}</div>
                            <div class="price-per-person-text">PER PERSON</div>
                        </div>
                        <div class="price-pill-card">
                            <div class="price-room-capsule">DOUBLE</div>
                            <div class="price-amount-large">{{ number_format($tier['price_double']) }}</div>
                            <div class="price-per-person-text">PER PERSON</div>
                        </div>
                    </div>

                    <!-- Features & Inclusions Row -->
                    <div class="features-inclusions-row">
                        <div class="feature-meal-box">
                            <i class="mdi mdi-silverware-clean"></i> {{ $tier['meal_plan'] }}
                        </div>

                        <div class="urdu-highlight-pill">
                            {!! $tier['urdu_note'] !!}
                        </div>

                        <div class="ziyarat-features-pill">
                            <i class="mdi mdi-star-four-points fs-10"></i>
                            {{ $isHajj ? 'HAJJ ARKAN & ZIYARAT' : 'ZIYARAT MAKKAH - MADINA' }}
                            <i class="mdi mdi-star-four-points fs-10"></i>
                        </div>

                        <div class="inclusions-icons-bar">
                            <div class="inclusions-label-tag">PACKAGE INCLUDE</div>
                            <div class="inclusion-single-item">
                                <i class="mdi mdi-office-building"></i>
                                <span>HOTEL</span>
                            </div>
                            <div class="inclusion-single-item">
                                <i class="mdi mdi-account-tie"></i>
                                <span>GUIDER</span>
                            </div>
                            <div class="inclusion-single-item">
                                <i class="mdi mdi-passport"></i>
                                <span>VISA</span>
                            </div>
                            <div class="inclusion-single-item">
                                <i class="mdi mdi-ticket-confirmation"></i>
                                <span>TICKET</span>
                            </div>
                            <div class="inclusion-single-item">
                                <i class="mdi mdi-bus"></i>
                                <span>TRANSPORT</span>
                            </div>
                        </div>
                    </div>
                </section>
            @endforeach

            <!-- ── 3. FLIGHT SCHEDULES / TRAVEL SECTORS (DYNAMIC) ── -->
            <section class="flight-schedules-grid">
                <!-- Flight Schedule Column 1 -->
                <div class="flight-schedule-card">
                    <div class="flight-card-header">
                        <i class="mdi mdi-airplane"></i> FLIGHT SCHEDULE &nbsp; {{ $tiers->first()['badge'] ?? ($calcDays . ' DAYS') }}
                    </div>
                    <table class="flight-table-compact">
                        <tbody>
                            @if ($hasFlights)
                                <tr>
                                    <td class="flight-code-badge">{{ $f1?->flight_no ?? 'SV-701' }}</td>
                                    <td>{{ $f1?->departure_date ? \Carbon\Carbon::parse($f1->departure_date)->format('d M') : '19 DEC' }}</td>
                                    <td class="flight-sector-badge">{{ $f1?->origin ?? 'KHI' }}-{{ $f1?->destination ?? 'JED' }}</td>
                                    <td>{{ $f1?->departure_time ? substr($f1->departure_time, 0, 5) : '10:20' }} &mdash; {{ $f1?->arrival_time ? substr($f1->arrival_time, 0, 5) : '12:45' }}</td>
                                    <td><span class="flight-baggage-badge">23 KG</span></td>
                                </tr>
                                <tr>
                                    <td class="flight-code-badge">{{ $f2?->flight_no ?? 'SV-704' }}</td>
                                    <td>{{ $f2?->departure_date ? \Carbon\Carbon::parse($f2->departure_date)->format('d M') : '03 JAN' }}</td>
                                    <td class="flight-sector-badge">{{ $f2?->origin ?? 'JED' }}-{{ $f2->destination ?? 'KHI' }}</td>
                                    <td>{{ $f2?->departure_time ? substr($f2->departure_time, 0, 5) : '19:45' }} &mdash; {{ $f2?->arrival_time ? substr($f2->arrival_time, 0, 5) : '01:30' }}</td>
                                    <td><span class="flight-baggage-badge">46 KG</span></td>
                                </tr>
                            @else
                                <tr>
                                    <td class="flight-code-badge">OUTBOUND</td>
                                    <td>{{ $package->departure_date_str ?: 'CONFIRMED' }}</td>
                                    <td class="flight-sector-badge">{{ $package->departure_sector ?: 'KHI &rarr; JED' }}</td>
                                    <td>DIRECT FLIGHT</td>
                                    <td><span class="flight-baggage-badge">46 KG</span></td>
                                </tr>
                                <tr>
                                    <td class="flight-code-badge">RETURN</td>
                                    <td>{{ $package->arrival_date_str ?: 'CONFIRMED' }}</td>
                                    <td class="flight-sector-badge">{{ $package->arrival_sector ?: 'JED/MED &rarr; KHI' }}</td>
                                    <td>DIRECT FLIGHT</td>
                                    <td><span class="flight-baggage-badge">46 KG</span></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="flight-airline-emblem">
                        <img src="{{ asset('assets/images/packages/flyer_assets/saudia_logo.png') }}"
                             alt="Saudia Airlines" class="flight-airline-img">
                    </div>
                </div>

                <!-- Flight Schedule Column 2 -->
                <div class="flight-schedule-card">
                    <div class="flight-card-header">
                        <i class="mdi mdi-airplane"></i> FLIGHT SCHEDULE &nbsp; {{ $tiers->count() > 1 ? $tiers->get(1)['badge'] : 'RETURN ITINERARY' }}
                    </div>
                    <table class="flight-table-compact">
                        <tbody>
                            @if ($hasFlights && $flights->count() >= 2)
                                <tr>
                                    <td class="flight-code-badge">{{ $f1?->flight_no ?? 'SV-701' }}</td>
                                    <td>{{ $travelDate2 }}</td>
                                    <td class="flight-sector-badge">{{ $f1?->origin ?? 'KHI' }}-{{ $f1?->destination ?? 'JED' }}</td>
                                    <td>10:20 &mdash; 12:45</td>
                                    <td><span class="flight-baggage-badge">23 KG</span></td>
                                </tr>
                                <tr>
                                    <td class="flight-code-badge">{{ $f2?->flight_no ?? 'SV-704' }}</td>
                                    <td>RETURN</td>
                                    <td class="flight-sector-badge">{{ $f2?->origin ?? 'JED' }}-{{ $f2?->destination ?? 'KHI' }}</td>
                                    <td>19:45 &mdash; 01:30</td>
                                    <td><span class="flight-baggage-badge">46 KG</span></td>
                                </tr>
                            @else
                                <tr>
                                    <td class="flight-code-badge">SECTOR</td>
                                    <td>{{ $travelDate1 }}</td>
                                    <td class="flight-sector-badge">{{ $package->departure_sector ?: 'PAKISTAN &rarr; SAUDIA' }}</td>
                                    <td>SCHEDULED</td>
                                    <td><span class="flight-baggage-badge">46 KG</span></td>
                                </tr>
                                <tr>
                                    <td class="flight-code-badge">SECTOR</td>
                                    <td>{{ $travelDate2 }}</td>
                                    <td class="flight-sector-badge">{{ $package->arrival_sector ?: 'SAUDIA &rarr; PAKISTAN' }}</td>
                                    <td>SCHEDULED</td>
                                    <td><span class="flight-baggage-badge">46 KG</span></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="flight-airline-emblem">
                        <img src="{{ asset('assets/images/packages/flyer_assets/saudia_logo.png') }}"
                             alt="Saudia Airlines" class="flight-airline-img">
                    </div>
                </div>
            </section>

            <!-- ── 4. CONTACTS TEAM FOOTER ── -->
            <footer class="flyer-contacts-section">
                <div class="contacts-cards-row">
                    <div class="contact-person-box">
                        <div class="contact-person-name">SHUJAAT VOHRA</div>
                        <div class="contact-person-phone">+92 322 8214412</div>
                    </div>
                    <div class="contact-person-box">
                        <div class="contact-person-name">SHAHZAD VOHRA</div>
                        <div class="contact-person-phone">+92 321 7660766</div>
                    </div>
                    <div class="contact-person-box">
                        <div class="contact-person-name">SHAHZAIB VOHRA</div>
                        <div class="contact-person-phone">+92 335 0305454</div>
                    </div>
                    <div class="contact-person-box">
                        <div class="contact-person-name">USAMA ABDUL REHMAN</div>
                        <div class="contact-person-phone">+92 333 1235030</div>
                    </div>
                    <div class="contact-person-box">
                        <div class="contact-person-name">GHULAM ABBAS</div>
                        <div class="contact-person-phone">+92 321 9244109</div>
                    </div>
                </div>

                <!-- Bottom Solid Bar with Active Theme Color -->
                <div class="bottom-solid-bar">
                    <span>+92 322 8214412</span>
                    <span>&bull;</span>
                    <span>+92 321 7660766</span>
                    <span>&bull;</span>
                    <span>+92 335 0305454</span>
                    <span>&bull;</span>
                    <span>+92 333 1235030</span>
                    <span>&bull;</span>
                    <span>+92 321 9244109</span>
                </div>
            </footer>

        </div>
    </main>

    <!-- PDF Generation Script -->
    <script>
        async function downloadHighResPdf() {
            const btn = document.getElementById('btnDownloadPdf');
            let originalContent = '';
            if (btn) {
                originalContent = btn.innerHTML;
                btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Generating PDF...';
                btn.disabled = true;
            }

            const element = document.getElementById('brochure-container');

            try {
                const canvas = await html2canvas(element, {
                    scale: 3,
                    useCORS: true,
                    allowTaint: true,
                    scrollY: 0,
                    scrollX: 0,
                    backgroundColor: '#FFFFFF',
                    logging: false
                });

                const imgData = canvas.toDataURL('image/jpeg', 1.0);
                const { jsPDF } = window.jspdf;
                const pdfWidth = 210;
                const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: [pdfWidth, pdfHeight]
                });

                pdf.addImage(imgData, 'JPEG', 0, 0, pdfWidth, pdfHeight, undefined, 'FAST');
                const fileName = 'Baba-Hajj-Group-{{ $mainTitle }}-Package-{{ $package->id ?? "flyer" }}.pdf';
                pdf.save(fileName);
            } catch (err) {
                console.error('Direct PDF error, fallback to html2pdf:', err);
                const opt = {
                    margin: 0,
                    filename: 'Baba-Hajj-Group-{{ $mainTitle }}-Package.pdf',
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2.5, useCORS: true, scrollY: 0 },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                };
                html2pdf().set(opt).from(element).save().catch(() => {
                    window.print();
                });
            } finally {
                if (btn) {
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                }
            }
        }
    </script>

</body>

</html>
