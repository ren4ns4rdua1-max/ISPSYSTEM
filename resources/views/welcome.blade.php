<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISP Billing Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600&display=swap" rel="stylesheet">
    <style>
        :root {
            --red:        #dc2626;
            --red-dark:   #991b1b;
            --red-deeper: #450a0a;
            --red-glow:   rgba(220,38,38,.35);
            --navy:       #0c0e1a;
            --navy2:      #111827;
            --white:      #ffffff;
            --off-white:  #f8fafc;
            --muted:      #64748b;
            --border:     #e2e8f0;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; overflow-x: hidden; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--off-white);
            color: #0f172a;
            line-height: 1.6;
        }

        .font-display { font-family: 'Syne', sans-serif; }

        /* =================== NAV =================== */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            padding: 0;
            transition: all 0.4s ease;
        }

        nav.scrolled {
            background: rgba(12,14,26,.96);
            backdrop-filter: blur(20px);
            box-shadow: 0 4px 30px rgba(0,0,0,.3);
        }

        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.25rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.1rem;
            color: white;
            letter-spacing: -.3px;
        }

        .nav-logo-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 12px rgba(220,38,38,.4);
            flex-shrink: 0;
        }

        .nav-logo-icon svg { width: 18px; height: 18px; color: white; }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2.5rem;
            list-style: none;
        }

        .nav-links a {
            color: rgba(255,255,255,.7);
            text-decoration: none;
            font-size: .9rem;
            font-weight: 500;
            transition: color .25s;
            letter-spacing: .02em;
        }

        .nav-links a:hover { color: white; }

        .nav-login-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: .55rem 1.4rem;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white !important;
            border-radius: 50px;
            font-weight: 600 !important;
            font-size: .88rem !important;
            transition: all .3s ease !important;
            box-shadow: 0 4px 14px rgba(220,38,38,.4);
        }

        .nav-login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(220,38,38,.5) !important;
        }

        /* =================== HERO =================== */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #0c0e1a;
        }

        /* Deep layered background */
        .hero-bg {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 50% 0%,   rgba(220,38,38,.28) 0%,   transparent 65%),
                radial-gradient(ellipse 50% 40% at 15% 70%,  rgba(185,28,28,.22) 0%,   transparent 55%),
                radial-gradient(ellipse 40% 50% at 85% 30%,  rgba(220,38,38,.15) 0%,   transparent 50%),
                linear-gradient(180deg, #0c0e1a 0%, #1a0a0a 50%, #0c0e1a 100%);
        }

        /* Grid texture */
        .hero-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: radial-gradient(ellipse 90% 90% at 50% 50%, black 20%, transparent 80%);
        }

        /* Diagonal slash decoration */
        .hero-slash {
            position: absolute;
            bottom: -2px; left: 0; right: 0;
            height: 120px;
            overflow: hidden;
        }

        .hero-slash svg { width: 100%; height: 100%; display: block; }

        /* Floating orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
        }

        .orb-1 {
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(220,38,38,.18), transparent 70%);
            top: -200px; left: -100px;
            animation: orbDrift1 12s ease-in-out infinite;
        }

        .orb-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(185,28,28,.15), transparent 70%);
            bottom: -100px; right: -100px;
            animation: orbDrift2 15s ease-in-out infinite;
        }

        .orb-3 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(239,68,68,.1), transparent 70%);
            top: 40%; left: 60%;
            animation: orbDrift3 9s ease-in-out infinite;
        }

        @keyframes orbDrift1 {
            0%,100% { transform: translate(0,0); }
            50% { transform: translate(40px, 60px); }
        }
        @keyframes orbDrift2 {
            0%,100% { transform: translate(0,0); }
            50% { transform: translate(-30px, -40px); }
        }
        @keyframes orbDrift3 {
            0%,100% { transform: translate(0,0) scale(1); }
            50% { transform: translate(20px, -30px) scale(1.15); }
        }

        /* Animated signal rings */
        .signal-rings {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }

        .ring {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(220,38,38,.2);
            transform: translate(-50%,-50%);
            animation: ringExpand 4s ease-out infinite;
        }

        .ring:nth-child(1) { width: 300px; height: 300px; animation-delay: 0s; }
        .ring:nth-child(2) { width: 550px; height: 550px; animation-delay: 1.3s; }
        .ring:nth-child(3) { width: 820px; height: 820px; animation-delay: 2.6s; }

        @keyframes ringExpand {
            0%   { opacity: .6; transform: translate(-50%,-50%) scale(.7); }
            100% { opacity: 0;  transform: translate(-50%,-50%) scale(1.1); }
        }

        /* Hero content */
        .hero-content {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 0 1.5rem;
            max-width: 900px;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: .4rem 1.1rem;
            border-radius: 50px;
            background: rgba(220,38,38,.12);
            border: 1px solid rgba(220,38,38,.3);
            color: #fca5a5;
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            margin-bottom: 2rem;
            animation: heroFadeUp .8s ease both;
        }

        .hero-eyebrow-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #ef4444;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%,100% { opacity: 1; transform: scale(1); }
            50% { opacity: .6; transform: scale(1.4); }
        }

        .hero-title {
            font-family: 'Syne', sans-serif;
            font-size: clamp(2.8rem, 7vw, 5.2rem);
            font-weight: 800;
            line-height: 1.08;
            color: white;
            margin-bottom: 1.8rem;
            letter-spacing: -.03em;
            animation: heroFadeUp .9s .15s ease both;
        }

        .hero-title .line-accent {
            display: block;
            background: linear-gradient(90deg, #f87171 0%, #dc2626 40%, #fca5a5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: clamp(1rem, 2vw, 1.25rem);
            color: rgba(255,255,255,.6);
            font-weight: 400;
            max-width: 560px;
            margin: 0 auto 3rem;
            line-height: 1.7;
            animation: heroFadeUp 1s .3s ease both;
        }

        .hero-ctas {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
            animation: heroFadeUp 1s .45s ease both;
            margin-bottom: 4.5rem;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: .6rem;
            padding: .9rem 2.2rem;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
            border-radius: 50px;
            font-weight: 700;
            font-size: .95rem;
            text-decoration: none;
            border: none;
            cursor: pointer;
            box-shadow: 0 8px 28px rgba(220,38,38,.45), 0 0 0 1px rgba(220,38,38,.3);
            transition: all .3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::after {
            content: '';
            position: absolute;
            top: -50%; left: -60%;
            width: 40%; height: 200%;
            background: rgba(255,255,255,.18);
            transform: skewX(-20deg);
            transition: left .4s ease;
        }

        .btn-primary:hover::after { left: 120%; }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 40px rgba(220,38,38,.55), 0 0 0 1px rgba(220,38,38,.4);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: .6rem;
            padding: .9rem 2.2rem;
            background: rgba(255,255,255,.07);
            color: rgba(255,255,255,.85);
            border-radius: 50px;
            font-weight: 600;
            font-size: .95rem;
            text-decoration: none;
            border: 1px solid rgba(255,255,255,.15);
            cursor: pointer;
            transition: all .3s ease;
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,.12);
            color: white;
            transform: translateY(-3px);
            border-color: rgba(255,255,255,.25);
        }

        /* Hero stats bar */
        .hero-stats {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            animation: heroFadeUp 1s .6s ease both;
            flex-wrap: wrap;
        }

        .hero-stat {
            padding: 1.4rem 2.5rem;
            text-align: center;
            position: relative;
        }

        .hero-stat + .hero-stat::before {
            content: '';
            position: absolute;
            left: 0; top: 25%; bottom: 25%;
            width: 1px;
            background: rgba(255,255,255,.1);
        }

        .hero-stat-num {
            font-family: 'Syne', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            color: white;
            line-height: 1;
            margin-bottom: .3rem;
        }

        .hero-stat-num span {
            color: #ef4444;
        }

        .hero-stat-label {
            font-size: .75rem;
            color: rgba(255,255,255,.45);
            text-transform: uppercase;
            letter-spacing: .1em;
            font-weight: 600;
        }

        /* Scroll indicator */
        .scroll-indicator {
            position: absolute;
            bottom: 2.5rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .5rem;
            z-index: 10;
            animation: heroFadeUp 1s .8s ease both;
            opacity: .5;
        }

        .scroll-indicator span {
            font-size: .7rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: rgba(255,255,255,.5);
        }

        .scroll-mouse {
            width: 22px; height: 34px;
            border: 2px solid rgba(255,255,255,.3);
            border-radius: 11px;
            display: flex;
            justify-content: center;
            padding-top: 6px;
        }

        .scroll-wheel {
            width: 3px; height: 6px;
            background: rgba(255,255,255,.5);
            border-radius: 2px;
            animation: scrollDown 2s ease-in-out infinite;
        }

        @keyframes scrollDown {
            0%,100% { transform: translateY(0); opacity: 1; }
            50% { transform: translateY(8px); opacity: 0; }
        }

        @keyframes heroFadeUp {
            from { opacity: 0; transform: translateY(28px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* =================== PLANS =================== */
        .plans-section {
            padding: 7rem 2rem;
            background: var(--off-white);
            position: relative;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-eyebrow {
            display: inline-block;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .14em;
            color: #dc2626;
            margin-bottom: 1rem;
        }

        .section-title {
            font-family: 'Syne', sans-serif;
            font-size: clamp(2rem, 4vw, 2.8rem);
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -.03em;
            margin-bottom: .8rem;
            line-height: 1.1;
        }

        .section-subtitle {
            font-size: 1rem;
            color: var(--muted);
            max-width: 480px;
            margin: 0 auto;
        }

        .plans-grid {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            align-items: start;
        }

        .plan-card {
            background: white;
            border-radius: 24px;
            padding: 2.5rem;
            border: 1.5px solid var(--border);
            position: relative;
            overflow: hidden;
            transition: all .35s cubic-bezier(0.175,0.885,0.32,1.275);
        }

        .plan-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #e5e7eb, #e5e7eb);
            transition: background .3s;
        }

        .plan-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 24px 60px rgba(0,0,0,.1);
            border-color: #fecaca;
        }

        .plan-card:hover::before {
            background: linear-gradient(90deg, #dc2626, #ef4444);
        }

        .plan-card.featured {
            border-color: #dc2626;
            box-shadow: 0 20px 60px rgba(220,38,38,.15);
            transform: translateY(-8px) scale(1.02);
            background: linear-gradient(160deg, #fff 0%, #fff5f5 100%);
        }

        .plan-card.featured::before {
            background: linear-gradient(90deg, #dc2626, #ef4444, #dc2626);
        }

        .plan-card.featured:hover {
            transform: translateY(-14px) scale(1.02);
            box-shadow: 0 30px 70px rgba(220,38,38,.25);
        }

        .plan-badge {
            position: absolute;
            top: 1.5rem; right: 1.5rem;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
            padding: .3rem .9rem;
            border-radius: 50px;
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .plan-icon {
            width: 48px; height: 48px;
            border-radius: 14px;
            background: #f1f5f9;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 1.4rem;
            transition: all .3s;
        }

        .plan-card.featured .plan-icon {
            background: #fee2e2;
        }

        .plan-card:hover .plan-icon {
            transform: scale(1.1) rotate(-5deg);
        }

        .plan-name {
            font-family: 'Syne', sans-serif;
            font-size: 1.3rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: .5rem;
        }

        .plan-speed {
            font-size: .85rem;
            color: var(--muted);
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .plan-price {
            font-family: 'Syne', sans-serif;
            font-size: 3rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
            margin-bottom: 1.5rem;
        }

        .plan-price sup {
            font-size: 1.2rem;
            font-weight: 600;
            vertical-align: super;
            line-height: 0;
            color: #dc2626;
        }

        .plan-card.featured .plan-price { color: #dc2626; }

        .plan-divider {
            height: 1px;
            background: #f1f5f9;
            margin-bottom: 1.5rem;
        }

        .plan-description {
            font-size: .875rem;
            color: var(--muted);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .plan-description svg { color: #22c55e; flex-shrink: 0; }

        .subscribe-btn {
            display: block;
            width: 100%;
            padding: .85rem;
            border-radius: 14px;
            font-weight: 700;
            font-size: .9rem;
            cursor: pointer;
            border: 2px solid var(--border);
            background: white;
            color: #374151;
            transition: all .3s ease;
            font-family: 'DM Sans', sans-serif;
        }

        .subscribe-btn:hover {
            border-color: #dc2626;
            color: #dc2626;
            background: #fff5f5;
        }

        .plan-card.featured .subscribe-btn {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            border-color: transparent;
            color: white;
            box-shadow: 0 8px 24px rgba(220,38,38,.35);
        }

        .plan-card.featured .subscribe-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(220,38,38,.45);
        }

        /* =================== FEATURES =================== */
        .features-section {
            background: var(--navy);
            padding: 7rem 2rem;
            position: relative;
            overflow: hidden;
        }

        .features-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.035) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .features-section .section-title { color: white; }
        .features-section .section-subtitle { color: rgba(255,255,255,.5); }

        .features-grid {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .feature-card {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 24px;
            padding: 2.5rem;
            transition: all .35s ease;
        }

        .feature-card:hover {
            background: rgba(255,255,255,.07);
            border-color: rgba(220,38,38,.3);
            transform: translateY(-8px);
            box-shadow: 0 24px 50px rgba(220,38,38,.1);
        }

        .feature-icon-wrap {
            width: 56px; height: 56px;
            border-radius: 16px;
            background: rgba(220,38,38,.12);
            border: 1px solid rgba(220,38,38,.2);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 1.6rem;
            transition: all .3s;
        }

        .feature-card:hover .feature-icon-wrap {
            background: rgba(220,38,38,.2);
            transform: scale(1.1) rotate(-6deg);
        }

        .feature-title {
            font-family: 'Syne', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: white;
            margin-bottom: .75rem;
        }

        .feature-desc {
            font-size: .9rem;
            color: rgba(255,255,255,.5);
            line-height: 1.7;
        }

        /* =================== CONTACT =================== */
        .contact-section {
            background: white;
            padding: 7rem 2rem;
        }

        .contact-wrap {
            max-width: 600px;
            margin: 0 auto;
        }

        .contact-form {
            margin-top: 2.5rem;
        }

        .form-field {
            margin-bottom: 1.25rem;
        }

        .form-field input,
        .form-field textarea {
            width: 100%;
            padding: .85rem 1.1rem;
            border: 1.5px solid var(--border);
            border-radius: 14px;
            font-size: .92rem;
            font-family: 'DM Sans', sans-serif;
            color: #0f172a;
            background: #f9fafb;
            transition: all .25s;
            outline: none;
        }

        .form-field input:focus,
        .form-field textarea:focus {
            border-color: #dc2626;
            background: white;
            box-shadow: 0 0 0 3px rgba(220,38,38,.1);
        }

        .form-field input::placeholder,
        .form-field textarea::placeholder { color: #94a3b8; }

        .form-field textarea {
            resize: vertical;
            min-height: 140px;
        }

        .contact-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 1rem;
            font-weight: 700;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: all .3s;
            box-shadow: 0 8px 24px rgba(220,38,38,.3);
        }

        .contact-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 36px rgba(220,38,38,.4);
        }

        /* =================== MODAL =================== */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 2000;
            background: rgba(12,14,26,.85);
            backdrop-filter: blur(12px);
            align-items: center;
            justify-content: center;
        }

        .modal.active { display: flex; }

        .modal-box {
            background: white;
            border-radius: 24px;
            width: 90%;
            max-width: 460px;
            overflow: hidden;
            box-shadow: 0 40px 100px rgba(0,0,0,.5);
            animation: modalPop .4s cubic-bezier(0.175,0.885,0.32,1.275) both;
            max-height: 95vh;
            display: flex;
            flex-direction: column;
        }

        @keyframes modalPop {
            from { opacity: 0; transform: scale(.88) translateY(-30px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        .modal-head {
            background: linear-gradient(135deg, #1c0a0a 0%, #450a0a 40%, #7f1d1d 100%);
            padding: 2.2rem 2rem;
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
        }

        .modal-head-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px);
            background-size: 28px 28px;
        }

        .modal-head-glow {
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(220,38,38,.35), transparent 70%);
            top: -100px; right: -100px;
            animation: orbDrift1 6s ease-in-out infinite;
        }

        .modal-close {
            position: absolute;
            top: 1rem; right: 1rem;
            width: 36px; height: 36px;
            border-radius: 50%;
            background: rgba(255,255,255,.15);
            border: none;
            color: white;
            font-size: 1.3rem;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all .25s;
            z-index: 2;
            line-height: 1;
        }

        .modal-close:hover {
            background: rgba(255,255,255,.25);
            transform: rotate(90deg);
        }

        .modal-head-content { position: relative; z-index: 1; }

        .modal-head h3 {
            font-family: 'Syne', sans-serif;
            font-size: 1.6rem;
            font-weight: 800;
            color: white;
            margin-bottom: .3rem;
        }

        .modal-head p {
            color: rgba(255,255,255,.6);
            font-size: .9rem;
        }

        .modal-body {
            padding: 1.8rem 2rem;
            overflow-y: auto;
            flex: 1;
        }

        .modal-tabs {
            display: flex;
            gap: .5rem;
            margin-bottom: 1.75rem;
            background: #f8fafc;
            border-radius: 12px;
            padding: .3rem;
        }

        .modal-tab {
            flex: 1;
            padding: .6rem;
            border: none;
            background: none;
            border-radius: 9px;
            font-size: .88rem;
            font-weight: 600;
            color: var(--muted);
            cursor: pointer;
            transition: all .25s;
            font-family: 'DM Sans', sans-serif;
        }

        .modal-tab.active {
            background: white;
            color: #dc2626;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
        }

        .modal-form { display: none; }
        .modal-form.active { display: block; }

        .modal-field {
            margin-bottom: 1rem;
        }

        .modal-field label {
            display: block;
            font-size: .8rem;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: .4rem;
        }

        .modal-field input {
            width: 100%;
            padding: .8rem 1rem;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            font-size: .92rem;
            font-family: 'DM Sans', sans-serif;
            color: #0f172a;
            background: #f9fafb;
            transition: all .25s;
            outline: none;
        }

        .modal-field input:focus {
            border-color: #dc2626;
            background: white;
            box-shadow: 0 0 0 3px rgba(220,38,38,.1);
        }

        .modal-submit {
            width: 100%;
            padding: .85rem;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: .95rem;
            font-weight: 700;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: all .3s;
            margin-top: .5rem;
            box-shadow: 0 6px 20px rgba(220,38,38,.3);
        }

        .modal-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(220,38,38,.4);
        }

        .modal-foot {
            padding: 0 2rem 1.5rem;
            text-align: center;
            font-size: .85rem;
            color: var(--muted);
            flex-shrink: 0;
        }

        .modal-foot a {
            color: #dc2626;
            font-weight: 600;
            text-decoration: none;
        }

        /* =================== FOOTER =================== */
        footer {
            background: linear-gradient(180deg, var(--navy) 0%, #080a12 100%);
            color: white;
            padding: 5rem 2rem 2.5rem;
            position: relative;
            overflow: hidden;
        }

        footer::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        .footer-inner {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .footer-top {
            display: grid;
            grid-template-columns: 1.4fr 1fr 1fr;
            gap: 4rem;
            margin-bottom: 4rem;
        }

        .footer-brand-name {
            font-family: 'Syne', sans-serif;
            font-size: 1.1rem;
            font-weight: 800;
            color: white;
            display: flex;
            align-items: center;
            gap: .6rem;
            margin-bottom: 1.2rem;
        }

        .footer-brand-icon {
            width: 32px; height: 32px;
            border-radius: 9px;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            display: flex; align-items: center; justify-content: center;
        }

        .footer-brand-icon svg { width: 16px; height: 16px; color: white; }

        .footer-desc {
            color: rgba(255,255,255,.45);
            font-size: .88rem;
            line-height: 1.8;
            margin-bottom: 1.5rem;
        }

        .footer-socials {
            display: flex;
            gap: .6rem;
        }

        .social-btn {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.1);
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            text-decoration: none;
            transition: all .25s;
        }

        .social-btn:hover {
            background: rgba(220,38,38,.2);
            border-color: rgba(220,38,38,.3);
            transform: translateY(-3px);
        }

        .footer-col h4 {
            font-family: 'Syne', sans-serif;
            font-size: .85rem;
            font-weight: 700;
            color: rgba(255,255,255,.9);
            text-transform: uppercase;
            letter-spacing: .1em;
            margin-bottom: 1.25rem;
        }

        .footer-links { list-style: none; }
        .footer-links li { margin-bottom: .65rem; }

        .footer-links a {
            color: rgba(255,255,255,.4);
            text-decoration: none;
            font-size: .88rem;
            transition: all .25s;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }

        .footer-links a:hover { color: rgba(255,255,255,.8); padding-left: .35rem; }

        .footer-contact-item {
            display: flex;
            align-items: flex-start;
            gap: .8rem;
            margin-bottom: .9rem;
        }

        .footer-contact-icon {
            width: 32px; height: 32px;
            border-radius: 9px;
            background: rgba(220,38,38,.1);
            border: 1px solid rgba(220,38,38,.2);
            display: flex; align-items: center; justify-content: center;
            font-size: .85rem;
            flex-shrink: 0;
        }

        .footer-contact-text {
            font-size: .85rem;
            color: rgba(255,255,255,.45);
            line-height: 1.6;
        }

        .footer-divider {
            height: 1px;
            background: rgba(255,255,255,.07);
            margin-bottom: 2rem;
        }

        .footer-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .footer-copy {
            font-size: .82rem;
            color: rgba(255,255,255,.3);
        }

        .footer-bottom-links {
            display: flex;
            gap: 1.5rem;
            list-style: none;
        }

        .footer-bottom-links a {
            font-size: .82rem;
            color: rgba(255,255,255,.3);
            text-decoration: none;
            transition: color .25s;
        }

        .footer-bottom-links a:hover { color: rgba(255,255,255,.65); }

        /* =================== SCROLL REVEAL =================== */
        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity .7s ease, transform .7s ease;
        }

        .reveal.visible {
            opacity: 1;
            transform: none;
        }

        /* =================== RESPONSIVE =================== */
        @media (max-width: 768px) {
            .nav-links { gap: 1.5rem; }
            .hero-stats { gap: 0; }
            .hero-stat { padding: 1rem 1.5rem; }
            .hero-stat-num { font-size: 1.5rem; }
            .plans-grid { grid-template-columns: 1fr; }
            .plan-card.featured { transform: none; }
            .footer-top { grid-template-columns: 1fr; gap: 2.5rem; }
            .footer-bottom { flex-direction: column; text-align: center; }
            .footer-bottom-links { justify-content: center; }
        }

        @media (max-width: 560px) {
            .nav-links li:not(:last-child):not(:nth-last-child(2)) { display: none; }
            .hero-ctas { flex-direction: column; align-items: stretch; text-align: center; }
            .btn-primary, .btn-secondary { justify-content: center; }
        }
    </style>
</head>
<body>

    <!-- =================== NAV =================== -->
    <nav id="main-nav">
        <div class="nav-inner">
            <a href="#" class="nav-logo">
                <div class="nav-logo-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                    </svg>
                </div>
                ISP BILLING SYSTEM
            </a>
            <ul class="nav-links">
                <li><a href="#plans">Plans</a></li>
                <li><a href="#features">Features</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="#" onclick="openModal(); return false;" class="nav-login-btn">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Login
                </a></li>
            </ul>
        </div>
    </nav>

    <!-- =================== HERO =================== -->
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-grid"></div>

        <!-- Orbs -->
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        <!-- Signal rings -->
        <div class="signal-rings">
            <div class="ring"></div>
            <div class="ring"></div>
            <div class="ring"></div>
        </div>

        <!-- Content -->
        <div class="hero-content">
            <div class="hero-eyebrow">
                <div class="hero-eyebrow-dot"></div>
                Fast · Reliable · Affordable
            </div>

            <h1 class="hero-title font-display">
                Reliable Internet for<br>
                <span class="line-accent">Every Connection</span>
            </h1>

            <p class="hero-subtitle">
                Fiber &amp; wireless internet built for homes and businesses.
                Blazing speeds, zero downtime, and support that actually picks up.
            </p>

            <div class="hero-ctas">
                <button class="btn-primary" onclick="document.getElementById('plans').scrollIntoView({behavior:'smooth'})">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    View Plans
                </button>
                <button class="btn-secondary" onclick="openModal()">
                    Sign In to Portal
                </button>
            </div>

            <!-- Stats -->
            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="hero-stat-num">99<span>.9%</span></div>
                    <div class="hero-stat-label">Uptime SLA</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-num">100<span>Mbps</span></div>
                    <div class="hero-stat-label">Max Speed</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-num">24<span>/7</span></div>
                    <div class="hero-stat-label">Support</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-num">₱<span>999</span></div>
                    <div class="hero-stat-label">Starts At</div>
                </div>
            </div>
        </div>

        <!-- Scroll hint -->
        <div class="scroll-indicator">
            <div class="scroll-mouse">
                <div class="scroll-wheel"></div>
            </div>
            <span>Scroll</span>
        </div>

        <!-- Bottom wave -->
        <div class="hero-slash">
            <svg viewBox="0 0 1440 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,80 C360,140 1080,20 1440,80 L1440,120 L0,120 Z" fill="#f8fafc"/>
            </svg>
        </div>
    </section>

    <!-- =================== PLANS =================== -->
    <section id="plans" class="plans-section">
        <div class="section-header reveal">
            <span class="section-eyebrow">Internet Plans</span>
            <h2 class="section-title">Pick Your Perfect Plan</h2>
            <p class="section-subtitle">Transparent pricing. No hidden fees. Cancel anytime.</p>
        </div>

        <div class="plans-grid">
            <div class="plan-card reveal" style="transition-delay:.1s">
                <div class="plan-icon">🏠</div>
                <h3 class="plan-name">Basic</h3>
                <p class="plan-speed">Up to 25 Mbps · Fiber</p>
                <div class="plan-price"><sup>₱</sup>999</div>
                <div class="plan-divider"></div>
                <p class="plan-description">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    No data cap, great for solo use
                </p>
                <button class="subscribe-btn" onclick="handleSubscribe('Basic')">Get Basic Plan</button>
            </div>

            <div class="plan-card featured reveal" style="transition-delay:.2s">
                <div class="plan-badge">Most Popular</div>
                <div class="plan-icon">👨‍👩‍👧‍👦</div>
                <h3 class="plan-name">Standard</h3>
                <p class="plan-speed">Up to 50 Mbps · Fiber</p>
                <div class="plan-price"><sup>₱</sup>1499</div>
                <div class="plan-divider"></div>
                <p class="plan-description">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Ideal for families with multiple devices
                </p>
                <button class="subscribe-btn" onclick="handleSubscribe('Standard')">Get Standard Plan</button>
            </div>

            <div class="plan-card reveal" style="transition-delay:.3s">
                <div class="plan-icon">🏢</div>
                <h3 class="plan-name">Premium</h3>
                <p class="plan-speed">Up to 100 Mbps · Fiber</p>
                <div class="plan-price"><sup>₱</sup>2499</div>
                <div class="plan-divider"></div>
                <p class="plan-description">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Best for businesses and power users
                </p>
                <button class="subscribe-btn" onclick="handleSubscribe('Premium')">Get Premium Plan</button>
            </div>
        </div>
    </section>

    <!-- =================== FEATURES =================== -->
    <section id="features" class="features-section">
        <div class="section-header reveal" style="position:relative;z-index:1;">
            <span class="section-eyebrow" style="color:#fca5a5;">Why Choose Us</span>
            <h2 class="section-title">Built Different</h2>
            <p class="section-subtitle">We're not just another ISP. Here's what sets us apart.</p>
        </div>

        <div class="features-grid">
            <div class="feature-card reveal" style="transition-delay:.1s">
                <div class="feature-icon-wrap">⚡</div>
                <h3 class="feature-title">Blazing Speeds</h3>
                <p class="feature-desc">Low latency, high throughput. Stream 4K, game online, and video call — all at the same time without a hitch.</p>
            </div>
            <div class="feature-card reveal" style="transition-delay:.2s">
                <div class="feature-icon-wrap">📡</div>
                <h3 class="feature-title">Wide Coverage</h3>
                <p class="feature-desc">Our fiber network spans homes and businesses across the entire region. Reliable connectivity wherever you are.</p>
            </div>
            <div class="feature-card reveal" style="transition-delay:.3s">
                <div class="feature-icon-wrap">🔧</div>
                <h3 class="feature-title">24/7 Support</h3>
                <p class="feature-desc">Real humans answer your calls. Technical assistance available around the clock, every day of the year.</p>
            </div>
        </div>
    </section>

    <!-- =================== CONTACT =================== -->
    <section id="contact" class="contact-section">
        <div class="section-header reveal">
            <span class="section-eyebrow">Get In Touch</span>
            <h2 class="section-title">Let's Get You Connected</h2>
            <p class="section-subtitle">Drop us a message and we'll reach out within 24 hours.</p>
        </div>

        <div class="contact-wrap reveal" style="transition-delay:.15s;">
            <form class="contact-form" onsubmit="handleContact(event)">
                <div class="form-field">
                    <input type="text" placeholder="Your Full Name" required>
                </div>
                <div class="form-field">
                    <input type="email" placeholder="Email Address" required>
                </div>
                <div class="form-field">
                    <textarea placeholder="Tell us how we can help you…" required></textarea>
                </div>
                <button type="submit" class="contact-submit">Send Message →</button>
            </form>
        </div>
    </section>

    <!-- =================== MODAL =================== -->
    <div id="signInModal" class="modal">
        <div class="modal-box">
            <div class="modal-head">
                <div class="modal-head-grid"></div>
                <div class="modal-head-glow"></div>
                <button class="modal-close" onclick="closeModal()">&times;</button>
                <div class="modal-head-content">
                    <h3>Welcome Back</h3>
                    <p>Sign in to your NetManager account</p>
                </div>
            </div>
            <div class="modal-body">
                <div class="modal-tabs">
                    <button class="modal-tab active" onclick="switchTab('login')">Sign In</button>
                    <button class="modal-tab" onclick="switchTab('register')">Create Account</button>
                </div>

                <form id="loginForm" class="modal-form active" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="modal-field">
                        <label>Email Address</label>
                        <input type="email" name="email" required placeholder="you@example.com">
                    </div>
                    <div class="modal-field">
                        <label>Password</label>
                        <input type="password" name="password" required placeholder="••••••••">
                    </div>
                    <button type="submit" class="modal-submit">Sign In →</button>
                </form>

                <form id="registerForm" class="modal-form" method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="modal-field">
                        <label>Full Name</label>
                        <input type="text" name="name" required placeholder="Juan dela Cruz">
                    </div>
                    <div class="modal-field">
                        <label>Email Address</label>
                        <input type="email" name="email" required placeholder="you@example.com">
                    </div>
                    <div class="modal-field">
                        <label>Password</label>
                        <input type="password" name="password" required placeholder="Min. 8 characters">
                    </div>
                    <div class="modal-field">
                        <label>Confirm Password</label>
                        <input type="password" name="password_confirmation" required placeholder="Re-enter password">
                    </div>
                    <button type="submit" class="modal-submit">Create Account →</button>
                </form>
            </div>
            <div class="modal-foot">
                Need help? <a href="#contact" onclick="closeModal()">Contact support</a>
            </div>
        </div>
    </div>

    <!-- =================== FOOTER =================== -->
    <footer>
        <div class="footer-inner">
            <div class="footer-top">
                <div>
                    <div class="footer-brand-name">
                        <div class="footer-brand-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                      d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                            </svg>
                        </div>
                        NetManager
                    </div>
                    <p class="footer-desc">Your trusted partner for reliable internet. Fast, secure, and affordable connectivity for every home and business.</p>
                    <div class="footer-socials">
                        <a href="#" class="social-btn" title="Facebook">📘</a>
                        <a href="#" class="social-btn" title="Twitter">🐦</a>
                        <a href="#" class="social-btn" title="Instagram">📷</a>
                    </div>
                </div>

                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="#plans">Internet Plans</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#contact">Contact Us</a></li>
                        <li><a href="#" onclick="openModal();return false;">Customer Portal</a></li>
                        <li><a href="#">Payment Options</a></li>
                        <li><a href="#">Support Center</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Contact</h4>
                    <div class="footer-contact-item">
                        <div class="footer-contact-icon">📍</div>
                        <div class="footer-contact-text">123 Internet Street<br>Tech City, TC 12345</div>
                    </div>
                    <div class="footer-contact-item">
                        <div class="footer-contact-icon">📞</div>
                        <div class="footer-contact-text">+1 (555) 123-4567</div>
                    </div>
                    <div class="footer-contact-item">
                        <div class="footer-contact-icon">✉️</div>
                        <div class="footer-contact-text">support@netmanager.com</div>
                    </div>
                </div>
            </div>

            <div class="footer-divider"></div>

            <div class="footer-bottom">
                <p class="footer-copy">&copy; 2024 ISP Billing Management System. All rights reserved.</p>
                <ul class="footer-bottom-links">
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Cookie Policy</a></li>
                </ul>
            </div>
        </div>
    </footer>

    <script>
        // Sticky nav
        window.addEventListener('scroll', () => {
            document.getElementById('main-nav').classList.toggle('scrolled', window.scrollY > 40);
        });

        // Scroll reveal
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('visible');
                    revealObserver.unobserve(e.target);
                }
            });
        }, { threshold: .12, rootMargin: '0px 0px -60px 0px' });

        document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

        // Modal
        function openModal(tab = 'login') {
            document.getElementById('signInModal').classList.add('active');
            document.body.style.overflow = 'hidden';
            switchTab(tab);
        }

        function closeModal() {
            document.getElementById('signInModal').classList.remove('active');
            document.body.style.overflow = '';
        }

        document.getElementById('signInModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

        function switchTab(tab) {
            const tabs = document.querySelectorAll('.modal-tab');
            const forms = document.querySelectorAll('.modal-form');
            tabs.forEach((t, i) => t.classList.toggle('active', (i === 0) === (tab === 'login')));
            forms.forEach((f, i) => f.classList.toggle('active', (i === 0) === (tab === 'login')));
        }

        // Subscribe
        function handleSubscribe(plan) {
            openModal('register');
        }

        // Contact
        function handleContact(e) {
            e.preventDefault();
            const btn = e.target.querySelector('.contact-submit');
            btn.textContent = '✓ Message Sent!';
            btn.style.background = 'linear-gradient(135deg,#059669,#047857)';
            setTimeout(() => {
                btn.textContent = 'Send Message →';
                btn.style.background = '';
                e.target.reset();
            }, 3000);
        }
    </script>
</body>
</html>