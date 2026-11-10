<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Baba Hajj Group — Executive Management Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Baba Hajj Group (Pvt.) Ltd. - Official Management & Operations Portal" />
    <meta name="author" content="Baba Hajj Group" />

    <!-- App Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Cinzel:wght@600;700;800;900&family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&display=swap" rel="stylesheet">

    <!-- CSS & Icons -->
    <link href="{{ asset('assets/css/app.min.css') }}?v={{ filemtime(public_path('assets/css/app.min.css')) }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}?v={{ filemtime(public_path('assets/css/icons.min.css')) }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/7.2.96/css/materialdesignicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        :root {
            --gold-primary: #C5A059;
            --gold-light: #F4E5BE;
            --gold-bright: #E5C368;
            --gold-dark: #8F6F25;
            --gold-gradient: linear-gradient(135deg, #ECC870 0%, #D4AF37 50%, #B88E28 100%);
            --navy-midnight: #060C17;
            --navy-deep: #0B1424;
            --navy-card: #121F35;
            --text-dark: #0F172A;
            --text-muted: #64748B;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            background-color: var(--navy-midnight);
            color: #FFFFFF;
            overflow-x: hidden;
            position: relative;
        }

        /* ════ BACKGROUND HERO ATMOSPHERE (VIDEO & POSTER) ════ */
        .portal-bg-container {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
            background-color: var(--navy-midnight);
        }

        .portal-video-bg {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            min-width: 100%;
            min-height: 100%;
            object-fit: cover;
            object-position: center 25%;
            transform: translate(-50%, -50%);
            z-index: 1;
            filter: brightness(0.95) contrast(1.08) saturate(1.15);
            pointer-events: none;
            transition: opacity 0.8s ease;
        }

        .portal-bg-fallback {
            position: absolute;
            inset: 0;
            background: url('/assets/images/logo/omer-f-arslan-W0FhhtnMd8k-unsplash.jpg') center 25% no-repeat;
            background-size: cover;
            z-index: 0;
            animation: slowZoom 24s ease-in-out infinite alternate;
        }

        @keyframes slowZoom {
            0% { transform: scale(1.0); }
            100% { transform: scale(1.06); }
        }

        @media (prefers-reduced-motion: reduce) {
            .portal-video-bg {
                display: none;
            }
        }

        /* Vignette Dark Gradient Overlay - Soft & Luminous so Kaaba video is clearly visible */
        .portal-overlay {
            position: fixed;
            inset: 0;
            z-index: 2;
            background: radial-gradient(circle at 50% 35%, rgba(6, 12, 23, 0.20) 0%, rgba(6, 12, 23, 0.45) 55%, rgba(3, 7, 14, 0.72) 100%);
        }

        /* Subtle Ambient Gold Light Ray */
        .portal-overlay::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 900px;
            height: 500px;
            background: radial-gradient(ellipse at top, rgba(212, 175, 55, 0.22) 0%, rgba(212, 175, 55, 0.05) 50%, transparent 75%);
            pointer-events: none;
            animation: pulseAura 6s ease-in-out infinite alternate;
        }

        @keyframes pulseAura {
            0% { opacity: 0.7; transform: translateX(-50%) scale(0.95); }
            100% { opacity: 1; transform: translateX(-50%) scale(1.05); }
        }

        /* ════ FLOATING SPIRITUAL PARTICLES (NOOR PARTICLES) ════ */
        .particles-wrap {
            position: fixed;
            inset: 0;
            z-index: 3;
            pointer-events: none;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            bottom: -20px;
            background: radial-gradient(circle, rgba(244, 229, 190, 0.85) 0%, rgba(212, 175, 55, 0.4) 60%, transparent 100%);
            border-radius: 50%;
            pointer-events: none;
            animation: floatUpward linear infinite;
        }

        .p1 { left: 12%; width: 5px; height: 5px; animation-duration: 14s; animation-delay: 0s; }
        .p2 { left: 24%; width: 7px; height: 7px; animation-duration: 18s; animation-delay: 3s; }
        .p3 { left: 38%; width: 4px; height: 4px; animation-duration: 12s; animation-delay: 1s; }
        .p4 { left: 52%; width: 6px; height: 6px; animation-duration: 16s; animation-delay: 5s; }
        .p5 { left: 68%; width: 5px; height: 5px; animation-duration: 13s; animation-delay: 2s; }
        .p6 { left: 82%; width: 8px; height: 8px; animation-duration: 20s; animation-delay: 4s; }
        .p7 { left: 91%; width: 4px; height: 4px; animation-duration: 15s; animation-delay: 6s; }

        @keyframes floatUpward {
            0% {
                transform: translateY(0) scale(0.6);
                opacity: 0;
            }
            15% {
                opacity: 0.75;
            }
            85% {
                opacity: 0.75;
            }
            100% {
                transform: translateY(-105vh) scale(1.2);
                opacity: 0;
            }
        }

        /* ════ MAIN PORTAL SHELL ════ */
        .portal-container {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
        }

        /* ════ TOP TALBIYAH / BISMILLAH RIBBON ════ */
        .top-spiritual-ribbon {
            text-align: center;
            padding: 4px 0;
            animation: fadeInDown 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .talbiyah-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(212, 175, 55, 0.35);
            border-radius: 40px;
            padding: 5px 18px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.35), 0 0 16px rgba(212, 175, 55, 0.12);
            transition: all 0.3s ease;
        }

        .talbiyah-pill:hover {
            border-color: var(--gold-bright);
            box-shadow: 0 8px 30px rgba(212, 175, 55, 0.25);
            transform: translateY(-1px);
        }

        .talbiyah-pill span.arabic {
            font-family: 'Amiri', serif;
            font-size: 17px;
            color: var(--gold-light);
            letter-spacing: 1px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.6);
        }

        .talbiyah-pill span.divider {
            color: rgba(212, 175, 55, 0.5);
            font-size: 13px;
        }

        .talbiyah-pill span.sub {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #E2E8F0;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* ════ CENTRAL AUTH CARD (COMPACT LUXURY HEIGHT) ════ */
        .auth-card-wrap {
            width: 100%;
            max-width: 390px;
            margin: 0 auto;
            animation: cardEntrance 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes cardEntrance {
            0% {
                opacity: 0;
                transform: translateY(25px) scale(0.97);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.93);
            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);
            border-radius: 18px;
            border: 1.5px solid rgba(212, 175, 55, 0.48);
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.45),
                        0 0 0 1px rgba(255, 255, 255, 0.9),
                        0 0 30px rgba(212, 175, 55, 0.16);
            padding: 16px 24px 14px 24px; /* Ultra-compact height padding */
            position: relative;
            overflow: hidden;
            color: var(--text-dark);
            transition: box-shadow 0.4s ease, transform 0.4s ease;
        }

        .auth-card:hover {
            box-shadow: 0 26px 70px rgba(0, 0, 0, 0.55),
                        0 0 0 1px rgba(255, 255, 255, 1),
                        0 0 45px rgba(212, 175, 55, 0.25);
        }

        /* Top Ornate Gold Bar */
        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #8F6F25 0%, #D4AF37 25%, #F4E5BE 50%, #D4AF37 75%, #8F6F25 100%);
        }

        /* ════ LOGO MEDALLION (LUXURY CONTRAST) ════ */
        .logo-medallion {
            text-align: center;
            margin-bottom: 8px;
        }

        .logo-frame {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 24px;
            background: #090D16;
            border-radius: 16px;
            border: 1.5px solid rgba(212, 175, 55, 0.45);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3), 0 0 16px rgba(212, 175, 55, 0.18);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .logo-frame:hover {
            transform: translateY(-2px) scale(1.02);
            border-color: var(--gold-bright);
            box-shadow: 0 8px 26px rgba(212, 175, 55, 0.35);
        }

        .portal-logo-img {
            height: 75px; /* Prominent, bold & razor-sharp */
            width: auto;
            max-width: 250px;
            object-fit: contain;
            display: block;
            transition: transform 0.3s ease;
        }

        /* ════ BRAND HEADER (COMPACT) ════ */
        .brand-header-text {
            text-align: center;
            margin-bottom: 8px;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(197, 160, 89, 0.12);
            color: #8F6F25;
            border: 1px solid rgba(197, 160, 89, 0.38);
            padding: 2px 9px;
            border-radius: 20px;
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            margin-bottom: 3px;
            transition: all 0.3s ease;
        }

        .brand-badge:hover {
            background: rgba(197, 160, 89, 0.2);
            border-color: var(--gold-bright);
        }

        .brand-title-main {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            font-weight: 800;
            color: #08111E;
            letter-spacing: 0.2px;
            margin-bottom: 2px;
        }

        .brand-subtitle-main {
            font-size: 11px;
            color: var(--text-muted);
            line-height: 1.3;
        }

        /* ════ FORM CONTROLS WITH ANIMATION (COMPACT) ════ */
        .form-floating-custom {
            margin-bottom: 8px;
            position: relative;
        }

        .form-label-portal {
            font-size: 11.5px;
            font-weight: 700;
            color: #1E293B;
            margin-bottom: 3px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            letter-spacing: 0.2px;
        }

        .input-wrapper-portal {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon-portal {
            position: absolute;
            left: 11px;
            color: #94A3B8;
            font-size: 16px;
            pointer-events: none;
            transition: all 0.25s ease;
        }

        .input-control-portal {
            width: 100%;
            height: 38px; /* Compact sleek height */
            padding: 6px 12px 6px 36px;
            font-size: 13.5px;
            font-weight: 500;
            color: #08111E;
            background: #F8FAFC;
            border: 1.5px solid #E2E8F0;
            border-radius: 9px;
            transition: all 0.25s ease;
            outline: none;
        }

        .input-control-portal:focus {
            background: #FFFFFF;
            border-color: var(--gold-primary);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.22);
            transform: translateY(-1px);
        }

        .input-control-portal:focus + .input-icon-portal,
        .input-control-portal:focus ~ .input-icon-portal {
            color: var(--gold-dark);
            transform: scale(1.08);
        }

        .btn-toggle-eye {
            position: absolute;
            right: 10px;
            background: none;
            border: none;
            color: #94A3B8;
            cursor: pointer;
            font-size: 16px;
            padding: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .btn-toggle-eye:hover {
            color: #08111E;
        }

        /* ════ CHECKBOX & SECURITY ROW ════ */
        .remember-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .remember-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            font-weight: 500;
            color: #475569;
            cursor: pointer;
            user-select: none;
            transition: color 0.2s ease;
        }

        .remember-label:hover {
            color: #0F172A;
        }

        .remember-input {
            width: 14px;
            height: 14px;
            border-radius: 3px;
            accent-color: var(--gold-primary);
            cursor: pointer;
        }

        /* ════ METALLIC LUXURY SUBMIT BUTTON ════ */
        .btn-portal-submit {
            width: 100%;
            height: 39px; /* Compact luxury height */
            background: linear-gradient(135deg, #08111E 0%, #112038 50%, #1A3052 100%);
            color: #FFFFFF;
            border: 1.5px solid rgba(212, 175, 55, 0.55);
            border-radius: 9px;
            font-size: 13.5px;
            font-weight: 700;
            letter-spacing: 0.4px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(8, 17, 30, 0.3);
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Continuous light sheen sweep */
        .btn-portal-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -120%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.4), transparent);
            animation: lightSheen 4.5s ease-in-out infinite;
        }

        @keyframes lightSheen {
            0%, 55% { left: -120%; }
            100% { left: 140%; }
        }

        .btn-portal-submit:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #D4AF37 0%, #ECC870 50%, #B88E28 100%);
            color: #070D18;
            border-color: #D4AF37;
            box-shadow: 0 12px 28px rgba(212, 175, 55, 0.45);
        }

        .btn-portal-submit:hover .btn-icon-arrow {
            transform: translateX(4px);
        }

        .btn-icon-arrow {
            transition: transform 0.25s ease;
        }

        .btn-portal-submit:active {
            transform: translateY(0);
        }

        /* ════ SECURITY TRUST BADGES ════ */
        .security-badges {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-top: 8px;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.75);
            animation: fadeInUp 0.9s ease-out;
        }

        .security-item {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-weight: 500;
        }

        .security-item i {
            color: var(--gold-bright);
            font-size: 13px;
        }

        /* ════ FOOTER ════ */
        .portal-footer {
            text-align: center;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.7);
            padding: 4px 0;
            animation: fadeInUp 1s ease-out;
        }

        .portal-footer a {
            color: var(--gold-light);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .portal-footer a:hover {
            color: #FFFFFF;
            text-decoration: underline;
        }

        /* Alerts */
        .alert-portal-danger {
            background: #FEF2F2;
            border: 1px solid #FCA5A5;
            color: #991B1B;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 20px;
            animation: shakeAlert 0.4s ease-in-out;
        }

        @keyframes shakeAlert {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-6px); }
            75% { transform: translateX(6px); }
        }

        .alert-portal-success {
            background: #ECFDF5;
            border: 1px solid #6EE7B7;
            color: #065F46;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-18px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive Design */
        @media (max-width: 580px) {
            .auth-card-wrap {
                max-width: 100%;
            }
            .auth-card {
                padding: 30px 22px 26px 22px;
                border-radius: 22px;
            }
            .talbiyah-pill {
                padding: 8px 18px;
            }
            .talbiyah-pill span.sub,
            .talbiyah-pill span.divider {
                display: none;
            }
            .portal-logo-img {
                height: 95px;
            }
            .brand-title-main {
                font-size: 24px;
            }
            .security-badges {
                flex-wrap: wrap;
                gap: 10px;
            }
        }
    </style>
</head>

<body>

    <!-- Atmospheric Panorama Video & Image Background -->
    <div class="portal-bg-container">
        <video id="loginBgVideo" class="portal-video-bg" autoplay muted loop playsinline poster="{{ asset('assets/images/logo/omer-f-arslan-W0FhhtnMd8k-unsplash.jpg') }}">
            <source src="{{ asset('assets/videos/kaaba-bg.webm') }}" type="video/webm">
            <source src="{{ asset('assets/videos/kabah-calligraphy.mp4') }}" type="video/mp4">
        </video>
        <div class="portal-bg-fallback"></div>
    </div>
    <div class="portal-overlay"></div>

    <!-- Floating Noor Particles -->
    <div class="particles-wrap">
        <div class="particle p1"></div>
        <div class="particle p2"></div>
        <div class="particle p3"></div>
        <div class="particle p4"></div>
        <div class="particle p5"></div>
        <div class="particle p6"></div>
        <div class="particle p7"></div>
    </div>

    <div class="portal-container">

        <!-- ════ TOP SPIRITUAL RIBBON ════ -->
        <header class="top-spiritual-ribbon">
            <div class="talbiyah-pill">
                <span class="arabic">لَبَّيْكَ اللَّهُمَّ لَبَّيْكَ &bull; بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</span>
                <span class="divider">|</span>
                <span class="sub"><i class="ri-shield-star-fill text-warning"></i> Official Portal</span>
            </div>
        </header>

        <!-- ════ AUTHENTICATION CARD (EXPANDED & ANIMATED) ════ -->
        <main class="auth-card-wrap">
            <div class="auth-card">

                <!-- Logo Medallion Frame (High-Contrast, Clean & Large) -->
                <div class="logo-medallion">
                    <div class="logo-frame">
                        <img src="{{ asset('assets/images/logo.png') }}?v={{ time() }}"
                             alt="Baba Hajj Group (Pvt.) Ltd."
                             class="portal-logo-img">
                    </div>
                </div>

                <!-- Brand Header -->
                <div class="brand-header-text">
                    <div class="brand-badge">
                        <i class="ri-government-line"></i> HAJJ & UMRAH PORTAL
                    </div>
                    <h1 class="brand-title-main">Sign In to Dashboard</h1>
                    <p class="brand-subtitle-main">Enter your credentials to access Baba Hajj Group operations</p>
                </div>

                <!-- Status & Alerts -->
                @if (session('success'))
                    <div class="alert-portal-success d-flex align-items-center gap-2">
                        <i class="ri-checkbox-circle-fill fs-16"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert-portal-danger">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="ri-error-warning-fill fs-16"></i>
                            <strong>Authentication Failed</strong>
                        </div>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login.post') }}">
                    @csrf

                    <!-- Email Field -->
                    <div class="form-floating-custom">
                        <label class="form-label-portal" for="email">
                            <span>Email Address</span>
                            <span class="text-muted fw-normal" style="font-size: 11px;">Authorized Staff</span>
                        </label>
                        <div class="input-wrapper-portal">
                            <input type="email" id="email" name="email"
                                class="input-control-portal @error('email') is-invalid @enderror"
                                value="{{ old('email') }}" required autofocus
                                placeholder="admin@babahajjgroup.com">
                            <i class="ri-mail-line input-icon-portal"></i>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="form-floating-custom">
                        <label class="form-label-portal" for="password">
                            <span>Password</span>
                            <span class="text-muted fw-normal" style="font-size: 11px;">Case-sensitive</span>
                        </label>
                        <div class="input-wrapper-portal">
                            <input type="password" id="password" name="password"
                                class="input-control-portal @error('password') is-invalid @enderror"
                                required placeholder="••••••••••••">
                            <i class="ri-lock-line input-icon-portal"></i>
                            <button type="button" class="btn-toggle-eye" onclick="togglePasswordVisibility()" aria-label="Toggle password visibility">
                                <i id="togglePasswordIcon" class="ri-eye-line"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me Option -->
                    <div class="remember-wrap">
                        <label class="remember-label">
                            <input type="checkbox" name="remember" class="remember-input">
                            <span>Keep me signed in</span>
                        </label>
                        <span class="text-muted" style="font-size: 12px;">
                            <i class="ri-shield-keyhole-line text-warning"></i> Secure Auth
                        </span>
                    </div>

                    <!-- Submit Button with Sheen Animation -->
                    <button type="submit" class="btn-portal-submit">
                        <span>Sign In to Hajj Portal</span>
                        <i class="ri-arrow-right-line fs-18 btn-icon-arrow"></i>
                    </button>
                </form>

            </div>

            <!-- Security Badges -->
            <div class="security-badges">
                <span class="security-item">
                    <i class="ri-lock-2-line"></i> 256-Bit Encrypted
                </span>
                <span class="security-item">&bull;</span>
                <span class="security-item">
                    <i class="ri-shield-check-line"></i> Ministry Authorized
                </span>
                <span class="security-item">&bull;</span>
                <span class="security-item">
                    <i class="ri-refresh-line"></i> Real-Time Sync
                </span>
            </div>
        </main>

        <!-- ════ FOOTER ════ -->
        <footer class="portal-footer">
            <div>
                &copy; {{ date('Y') }} <strong>Baba Hajj Group (Pvt.) Ltd.</strong> &bull; All Rights Reserved.
            </div>
            <div style="font-size: 11px; margin-top: 4px; opacity: 0.85;">
                Licensed Hajj & Umrah Tour Operator &bull; System Designed &amp; Developed by <a href="#!">Synergy Integrated Solutions</a>
            </div>
        </footer>

    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.className = 'ri-eye-off-line';
            } else {
                passwordInput.type = 'password';
                icon.className = 'ri-eye-line';
            }
        }

        // Smooth autoplay handling for Kaaba background video
        document.addEventListener('DOMContentLoaded', function () {
            const bgVideo = document.getElementById('loginBgVideo');
            if (bgVideo) {
                bgVideo.playbackRate = 0.85; // Serene, majestic pace
                const playPromise = bgVideo.play();
                if (playPromise !== undefined) {
                    playPromise.catch(function () {
                        // Autoplay policy fallback (poster image remains displayed)
                    });
                }
            }
        });
    </script>

</body>

</html>
