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
            cursor: none;
        }

        /* =================== CUSTOM CURSOR =================== */
        .cursor-dot {
            position: fixed;
            width: 8px; height: 8px;
            background: #dc2626;
            border-radius: 50%;
            pointer-events: none;
            z-index: 9999;
            transform: translate(-50%, -50%);
            transition: transform 0.1s ease, background 0.2s;
        }

        .cursor-ring {
            position: fixed;
            width: 36px; height: 36px;
            border: 1.5px solid rgba(220,38,38,.6);
            border-radius: 50%;
            pointer-events: none;
            z-index: 9998;
            transform: translate(-50%, -50%);
            transition: width 0.3s ease, height 0.3s ease, border-color 0.3s, transform 0.12s ease;
        }

        .cursor-ring.hover {
            width: 56px; height: 56px;
            border-color: rgba(220,38,38,.9);
            background: rgba(220,38,38,.05);
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
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .nav-logo:hover .nav-logo-icon {
            transform: rotate(10deg) scale(1.1);
            box-shadow: 0 6px 20px rgba(220,38,38,.7);
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
            position: relative;
        }

        .nav-links a:not(.nav-login-btn)::after {
            content: '';
            position: absolute;
            bottom: -3px; left: 0;
            width: 0; height: 1.5px;
            background: #dc2626;
            transition: width 0.3s ease;
        }

        .nav-links a:not(.nav-login-btn):hover::after { width: 100%; }
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

        /* =================== PARTICLE CANVAS =================== */
        #particle-canvas {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 2;
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

        .hero-bg {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 50% 0%,   rgba(220,38,38,.28) 0%,   transparent 65%),
                radial-gradient(ellipse 50% 40% at 15% 70%,  rgba(185,28,28,.22) 0%,   transparent 55%),
                radial-gradient(ellipse 40% 50% at 85% 30%,  rgba(220,38,38,.15) 0%,   transparent 50%),
                linear-gradient(180deg, #0c0e1a 0%, #1a0a0a 50%, #0c0e1a 100%);
        }

        .hero-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: radial-gradient(ellipse 90% 90% at 50% 50%, black 20%, transparent 80%);
            animation: gridShift 20s linear infinite;
        }

        @keyframes gridShift {
            0% { background-position: 0 0; }
            100% { background-position: 48px 48px; }
        }

        .hero-slash {
            position: absolute;
            bottom: -2px; left: 0; right: 0;
            height: 120px;
            overflow: hidden;
        }

        .hero-slash svg { width: 100%; height: 100%; display: block; }

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
            0%,100% { opacity: 1; transform: scale(1); box-shadow: 0 0 0 0 rgba(239,68,68,0); }
            50% { opacity: .6; transform: scale(1.4); box-shadow: 0 0 0 6px rgba(239,68,68,0); }
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
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 4s linear infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
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
            cursor: none;
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
            cursor: none;
            transition: all .3s ease;
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,.12);
            color: white;
            transform: translateY(-3px);
            border-color: rgba(255,255,255,.25);
        }

        /* =================== COUNTER ANIMATION =================== */
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
            cursor: default;
        }

        .hero-stat:hover .hero-stat-num {
            transform: scale(1.08);
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
            transition: transform 0.3s ease;
        }

        .hero-stat-num span { color: #ef4444; }

        .hero-stat-label {
            font-size: .75rem;
            color: rgba(255,255,255,.45);
            text-transform: uppercase;
            letter-spacing: .1em;
            font-weight: 600;
        }

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
            transition: opacity 0.3s;
        }

        .scroll-indicator:hover { opacity: 1; }

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
            overflow: hidden;
        }

        /* Animated background blobs */
        .plans-section::before, .plans-section::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            pointer-events: none;
            opacity: 0.06;
        }

        .plans-section::before {
            width: 500px; height: 500px;
            background: #dc2626;
            top: -100px; left: -150px;
            animation: blobMove1 18s ease-in-out infinite;
        }

        .plans-section::after {
            width: 400px; height: 400px;
            background: #dc2626;
            bottom: -100px; right: -100px;
            animation: blobMove2 14s ease-in-out infinite;
        }

        @keyframes blobMove1 {
            0%,100% { transform: translate(0,0) scale(1); }
            33% { transform: translate(80px, 60px) scale(1.1); }
            66% { transform: translate(20px, -40px) scale(.9); }
        }

        @keyframes blobMove2 {
            0%,100% { transform: translate(0,0) scale(1); }
            50% { transform: translate(-60px, -80px) scale(1.15); }
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
            transition: all .45s cubic-bezier(0.175,0.885,0.32,1.275);
            cursor: none;
        }

        /* Shine sweep on hover */
        .plan-card::after {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 60%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.35), transparent);
            transform: skewX(-15deg);
            transition: left 0.6s ease;
            pointer-events: none;
        }

        .plan-card:hover::after { left: 160%; }

        .plan-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #e5e7eb, #e5e7eb);
            transition: background .3s;
        }

        .plan-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 28px 70px rgba(0,0,0,.12);
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
            background-size: 200% auto;
            animation: gradientShift 3s linear infinite;
        }

        .plan-card.featured:hover {
            transform: translateY(-16px) scale(1.02);
            box-shadow: 0 36px 80px rgba(220,38,38,.28);
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
            animation: badgePop 0.5s cubic-bezier(0.175,0.885,0.32,1.275) both;
        }

        @keyframes badgePop {
            from { transform: scale(0) rotate(-15deg); opacity: 0; }
            to { transform: scale(1) rotate(0deg); opacity: 1; }
        }

        .plan-icon {
            width: 48px; height: 48px;
            border-radius: 14px;
            background: #f1f5f9;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 1.4rem;
            transition: all .4s cubic-bezier(0.175,0.885,0.32,1.275);
        }

        .plan-card.featured .plan-icon { background: #fee2e2; }

        .plan-card:hover .plan-icon {
            transform: scale(1.15) rotate(-8deg);
            box-shadow: 0 8px 24px rgba(220,38,38,.2);
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
            transition: transform 0.3s ease;
        }

        .plan-card:hover .plan-price { transform: scale(1.05); }

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

        .subscribe-btn, .view-details-btn {
            cursor: none;
        }

        .plan-buttons {
            display: flex;
            gap: 0.6rem;
            margin-top: 1.5rem;
        }

        /* View Details button */
        .view-details-btn {
            flex: 1;
            padding: .75rem 1rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: .82rem;
            border: 1.5px solid #e5e7eb;
            background: #f9fafb;
            color: #374151;
            transition: all .25s ease;
            font-family: 'DM Sans', sans-serif;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            white-space: nowrap;
        }
        .view-details-btn:hover {
            border-color: #dc2626;
            color: #dc2626;
            background: #fff5f5;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220,38,38,.12);
        }
        .plan-card.featured .view-details-btn {
            border-color: rgba(255,255,255,.4);
            background: rgba(255,255,255,.15);
            color: white;
        }
        .plan-card.featured .view-details-btn:hover {
            background: rgba(255,255,255,.25);
            border-color: white;
            transform: translateY(-2px);
        }

        /* Apply Now button */
        .subscribe-btn {
            flex: 1.4;
            padding: .75rem 1rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: .85rem;
            border: none;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
            transition: all .25s ease;
            font-family: 'DM Sans', sans-serif;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            white-space: nowrap;
            box-shadow: 0 4px 14px rgba(220,38,38,.35);
            position: relative;
            overflow: hidden;
        }
        .subscribe-btn::after {
            content: '';
            position: absolute;
            top: -50%; left: -60%;
            width: 40%; height: 200%;
            background: rgba(255,255,255,.18);
            transform: skewX(-20deg);
            transition: left .4s ease;
        }
        .subscribe-btn:hover::after { left: 120%; }
        .subscribe-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(220,38,38,.45);
        }
        .plan-card.featured .subscribe-btn {
            background: white;
            color: #dc2626;
            box-shadow: 0 4px 14px rgba(0,0,0,.1);
        }
        .plan-card.featured .subscribe-btn:hover {
            background: #fff5f5;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,.15);
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
            animation: gridShift 25s linear infinite;
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
            transition: all .4s ease;
            position: relative;
            overflow: hidden;
            cursor: none;
        }

        /* Glow corner on hover */
        .feature-card::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(220,38,38,.3), transparent 70%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .feature-card:hover::before { opacity: 1; }

        .feature-card:hover {
            background: rgba(255,255,255,.07);
            border-color: rgba(220,38,38,.3);
            transform: translateY(-10px);
            box-shadow: 0 28px 60px rgba(220,38,38,.12);
        }

        .feature-icon-wrap {
            width: 56px; height: 56px;
            border-radius: 16px;
            background: rgba(220,38,38,.12);
            border: 1px solid rgba(220,38,38,.2);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 1.6rem;
            transition: all .4s cubic-bezier(0.175,0.885,0.32,1.275);
        }

        .feature-card:hover .feature-icon-wrap {
            background: rgba(220,38,38,.22);
            transform: scale(1.15) rotate(-8deg);
            box-shadow: 0 8px 24px rgba(220,38,38,.3);
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

        .contact-wrap { max-width: 600px; margin: 0 auto; }

        .contact-form { margin-top: 2.5rem; }

        .form-field { margin-bottom: 1.25rem; }

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
            transition: all .3s;
            outline: none;
            cursor: none;
        }

        .form-field input:focus,
        .form-field textarea:focus {
            border-color: #dc2626;
            background: white;
            box-shadow: 0 0 0 3px rgba(220,38,38,.1);
            transform: translateY(-1px);
        }

        .form-field input::placeholder,
        .form-field textarea::placeholder { color: #94a3b8; }

        .form-field textarea { resize: vertical; min-height: 140px; }

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
            cursor: none;
            transition: all .3s;
            box-shadow: 0 8px 24px rgba(220,38,38,.3);
            position: relative;
            overflow: hidden;
        }

        .contact-submit::after {
            content: '';
            position: absolute;
            top: -50%; left: -60%;
            width: 40%; height: 200%;
            background: rgba(255,255,255,.18);
            transform: skewX(-20deg);
            transition: left .5s ease;
        }

        .contact-submit:hover::after { left: 120%; }

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

        .modal-box.wide { max-width: 650px; }

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
            animation: gridShift 15s linear infinite;
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
            cursor: none;
            display: flex; align-items: center; justify-content: center;
            transition: all .3s;
            z-index: 2;
            line-height: 1;
        }

        .modal-close:hover {
            background: rgba(255,255,255,.28);
            transform: rotate(90deg) scale(1.1);
        }

        .modal-head-content { position: relative; z-index: 1; }

        .modal-head h3 {
            font-family: 'Syne', sans-serif;
            font-size: 1.6rem;
            font-weight: 800;
            color: white;
            margin-bottom: .3rem;
        }

        .modal-head p { color: rgba(255,255,255,.6); font-size: .9rem; }

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
            cursor: none;
            transition: all .3s;
            font-family: 'DM Sans', sans-serif;
        }

        .modal-tab.active {
            background: white;
            color: #dc2626;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
        }

        .modal-form { display: none; }
        .modal-form.active { display: block; }

        .modal-field { margin-bottom: 1rem; }

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
            cursor: none;
        }

.modal-field input:focus {
            border-color: #dc2626;
            background: white;
            box-shadow: 0 0 0 3px rgba(220,38,38,.1);
        }

        .modal-field select {
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
            cursor: none;
        }

        .modal-field select:focus {
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
            cursor: none;
            transition: all .3s;
            margin-top: .5rem;
            box-shadow: 0 6px 20px rgba(220,38,38,.3);
            position: relative;
            overflow: hidden;
        }

        .modal-submit::after {
            content: '';
            position: absolute;
            top: -50%; left: -60%;
            width: 40%; height: 200%;
            background: rgba(255,255,255,.18);
            transform: skewX(-20deg);
            transition: left .5s ease;
        }

        .modal-submit:hover::after { left: 120%; }

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

        .modal-foot a { color: #dc2626; font-weight: 600; text-decoration: none; }

        /* Plan Details Grid */
        .plan-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .plan-detail-item {
            padding: 1rem;
            background: #f8fafc;
            border-radius: 12px;
            transition: all 0.25s;
        }

        .plan-detail-item:hover {
            background: #fff5f5;
            transform: translateY(-2px);
        }

        .plan-detail-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .plan-detail-value { font-weight: 700; color: #0f172a; }
        .plan-detail-value.price { color: #dc2626; font-size: 1.25rem; }

        /* =================== TOAST =================== */
        .toast {
            position: fixed;
            bottom: 2rem; right: 2rem;
            background: #1a1a2e;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 14px;
            font-size: .88rem;
            font-weight: 500;
            box-shadow: 0 12px 40px rgba(0,0,0,.4);
            border: 1px solid rgba(255,255,255,.1);
            z-index: 9000;
            transform: translateX(200%);
            transition: transform 0.4s cubic-bezier(0.175,0.885,0.32,1.275);
            display: flex; align-items: center; gap: .75rem;
            pointer-events: none;
        }

        .toast.show { transform: translateX(0); }

        .toast-icon {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: #22c55e;
            display: flex; align-items: center; justify-content: center;
            font-size: .85rem;
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
            animation: gridShift 30s linear infinite;
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
            transition: transform 0.3s ease;
        }

        .footer-brand-name:hover .footer-brand-icon { transform: rotate(15deg) scale(1.1); }

        .footer-brand-icon svg { width: 16px; height: 16px; color: white; }

        .footer-desc { color: rgba(255,255,255,.45); font-size: .88rem; line-height: 1.8; margin-bottom: 1.5rem; }

        .footer-socials { display: flex; gap: .6rem; }

        .social-btn {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.1);
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            text-decoration: none;
            transition: all .3s;
            cursor: none;
        }

        .social-btn:hover {
            background: rgba(220,38,38,.2);
            border-color: rgba(220,38,38,.3);
            transform: translateY(-4px) rotate(-5deg);
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
            transition: all .3s;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }

        .footer-links a:hover { color: rgba(255,255,255,.85); padding-left: .4rem; }

        .footer-contact-item { display: flex; align-items: flex-start; gap: .8rem; margin-bottom: .9rem; }

        .footer-contact-icon {
            width: 32px; height: 32px;
            border-radius: 9px;
            background: rgba(220,38,38,.1);
            border: 1px solid rgba(220,38,38,.2);
            display: flex; align-items: center; justify-content: center;
            font-size: .85rem;
            flex-shrink: 0;
            transition: all 0.3s;
        }

        .footer-contact-item:hover .footer-contact-icon {
            background: rgba(220,38,38,.2);
            transform: scale(1.1);
        }

        .footer-contact-text { font-size: .85rem; color: rgba(255,255,255,.45); line-height: 1.6; }

        .footer-divider { height: 1px; background: rgba(255,255,255,.07); margin-bottom: 2rem; }

        .footer-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .footer-copy { font-size: .82rem; color: rgba(255,255,255,.3); }

        .footer-bottom-links { display: flex; gap: 1.5rem; list-style: none; }

        .footer-bottom-links a {
            font-size: .82rem;
            color: rgba(255,255,255,.3);
            text-decoration: none;
            transition: color .25s;
        }

        .footer-bottom-links a:hover { color: rgba(255,255,255,.65); }

        /* =================== HOVER ANIMATIONS =================== */

        /* Tilt card */
        .tilt-card { transform-style: preserve-3d; will-change: transform; }

        /* Letter pop on headings */
        .letter-pop span {
            display: inline-block;
            transition: transform 0.2s cubic-bezier(0.34,1.56,0.64,1), color 0.2s;
        }
        .letter-pop span:hover {
            transform: translateY(-6px) scale(1.15);
            color: #dc2626;
        }

        /* Floating tooltip */
        #cursor-tooltip {
            position: fixed;
            pointer-events: none;
            z-index: 9997;
            background: rgba(12,14,26,.92);
            color: #f1f5f9;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .04em;
            padding: .35rem .85rem;
            border-radius: 50px;
            border: 1px solid rgba(220,38,38,.35);
            white-space: nowrap;
            opacity: 0;
            transform: translate(-50%, -140%) scale(0.85);
            transition: opacity 0.18s ease, transform 0.18s cubic-bezier(0.34,1.56,0.64,1);
            box-shadow: 0 8px 24px rgba(0,0,0,.3);
        }
        #cursor-tooltip.show {
            opacity: 1;
            transform: translate(-50%, -140%) scale(1);
        }

        /* Glow on stat hover */
        .hero-stat {
            transition: background 0.3s ease;
            border-radius: 16px;
        }
        .hero-stat:hover {
            background: rgba(220,38,38,.08);
        }
        .hero-stat:hover .hero-stat-label {
            color: #fca5a5;
        }

        /* Nav link underline pop */
        .nav-links a:not(.nav-login-btn) {
            transition: color .25s, letter-spacing .2s;
        }
        .nav-links a:not(.nav-login-btn):hover {
            letter-spacing: .06em;
        }

        /* Feature card text pop */
        .feature-card:hover .feature-title {
            color: #fca5a5;
            transition: color 0.3s;
        }

        /* Plan name pop */
        .plan-card:hover .plan-name {
            color: #dc2626;
            transition: color 0.3s;
        }

        /* Section eyebrow bounce */
        .section-eyebrow {
            transition: letter-spacing 0.3s ease, transform 0.3s ease;
            display: inline-block;
        }
        .section-eyebrow:hover {
            letter-spacing: .22em;
            transform: scale(1.05);
        }

        /* Footer link arrow pop */
        .footer-links a::before {
            content: '→';
            opacity: 0;
            margin-right: 0;
            transition: opacity 0.2s, margin-right 0.2s;
            font-size: .8rem;
        }
        .footer-links a:hover::before {
            opacity: 1;
            margin-right: .3rem;
        }

        /* Scroll indicator pulse on hover */
        .scroll-indicator:hover .scroll-mouse {
            border-color: rgba(220,38,38,.6);
            box-shadow: 0 0 12px rgba(220,38,38,.3);
            transition: all 0.3s;
        }

        /* =================== SCROLL REVEAL =================== */
        .reveal {
            opacity: 0;
            transform: translateY(32px);
            transition: opacity .7s ease, transform .7s ease;
        }

        .reveal.visible {
            opacity: 1;
            transform: none;
        }

        /* Stagger children inside revealed parents */
        .reveal-stagger > * {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        .reveal-stagger.visible > *:nth-child(1) { opacity:1; transform:none; transition-delay: 0.05s; }
        .reveal-stagger.visible > *:nth-child(2) { opacity:1; transform:none; transition-delay: 0.15s; }
        .reveal-stagger.visible > *:nth-child(3) { opacity:1; transform:none; transition-delay: 0.25s; }
        .reveal-stagger.visible > *:nth-child(4) { opacity:1; transform:none; transition-delay: 0.35s; }

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
            .plan-details-grid { grid-template-columns: 1fr; }
            body { cursor: auto; }
            .cursor-dot, .cursor-ring { display: none; }
            * { cursor: auto !important; }
            button { cursor: pointer !important; }
        }

        @media (max-width: 560px) {
            .nav-links li:not(:last-child):not(:nth-last-child(2)) { display: none; }
            .hero-ctas { flex-direction: column; align-items: stretch; text-align: center; }
            .btn-primary, .btn-secondary { justify-content: center; }
            .plan-buttons { gap: 0.4rem; }
            .view-details-btn { flex: 0 0 auto; padding: .65rem .75rem; font-size: .78rem; }
            .subscribe-btn { flex: 1; padding: .65rem .75rem; font-size: .82rem; }
        }
    </style>
</head>
<body>

    <!-- Custom Cursor -->
    <div class="cursor-dot" id="cursorDot"></div>
    <div class="cursor-ring" id="cursorRing"></div>
    <div id="cursor-tooltip"></div>

    <!-- Toast -->
    <div class="toast" id="toast">
        <div class="toast-icon">✓</div>
        <span id="toastMessage">Message sent!</span>
    </div>

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
                {{ $s['welcome_nav_logo'] ?? 'ISP BILLING SYSTEM' }}
            </a>
            <ul class="nav-links">
                <li><a href="#plans" data-tip="View our internet plans">Plans</a></li>
                <li><a href="#features" data-tip="Why choose us">Features</a></li>
                <li><a href="#contact" data-tip="Get in touch">Contact</a></li>
                <li><a href="#" onclick="openModal(); return false;" class="nav-login-btn">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    {{ $s['welcome_nav_login_btn'] ?? 'Login' }}
                </a></li>
            </ul>
        </div>
    </nav>

    <!-- =================== HERO =================== -->
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-grid"></div>
        <canvas id="particle-canvas"></canvas>
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <div class="signal-rings">
            <div class="ring"></div>
            <div class="ring"></div>
            <div class="ring"></div>
        </div>
        <div class="hero-content">
            <div class="hero-eyebrow">
                <div class="hero-eyebrow-dot"></div>
                Fast · Reliable · Affordable
            </div>
            <h1 class="hero-title font-display">
                {{ $s['welcome_hero_title_1'] ?? 'Reliable Internet for' }}<br>
                <span class="line-accent letter-pop" id="scrambleText">{{ $s['welcome_hero_title_2'] ?? 'Every Connection' }}</span>
            </h1>
            <p class="hero-subtitle">
                {{ $s['welcome_hero_subtitle'] ?? 'Fiber & wireless internet built for homes and businesses. Blazing speeds, zero downtime, and support that actually picks up.' }}
            </p>
            <div class="hero-ctas">
                <button class="btn-primary" data-tip="Browse all plans" onclick="document.getElementById('plans').scrollIntoView({behavior:'smooth'})">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    {{ $s['welcome_hero_cta_primary'] ?? 'View Plans' }}
                </button>
                <button class="btn-secondary" data-tip="Access your account" onclick="openModal()">
                    {{ $s['welcome_hero_cta_secondary'] ?? 'Sign In to Portal' }}
                </button>
            </div>
            <div class="hero-stats">
                <div class="hero-stat" data-tip="99.9% guaranteed uptime">
                    <div class="hero-stat-num"><span class="count" data-target="99">0</span><span>.9%</span></div>
                    <div class="hero-stat-label">{{ $s['welcome_hero_stat_uptime_label'] ?? 'Uptime SLA' }}</div>
                </div>
                <div class="hero-stat" data-tip="Up to 100 Mbps fiber speed">
                    <div class="hero-stat-num"><span class="count" data-target="100">0</span><span>Mbps</span></div>
                    <div class="hero-stat-label">{{ $s['welcome_hero_stat_speed_label'] ?? 'Max Speed' }}</div>
                </div>
                <div class="hero-stat" data-tip="Round-the-clock support">
                    <div class="hero-stat-num"><span class="count" data-target="24">0</span><span>/7</span></div>
                    <div class="hero-stat-label">{{ $s['welcome_hero_stat_support_label'] ?? 'Support' }}</div>
                </div>
                <div class="hero-stat" data-tip="Plans starting at ₱999/mo">
                    <div class="hero-stat-num">₱<span class="count" data-target="999">0</span></div>
                    <div class="hero-stat-label">{{ $s['welcome_hero_stat_price_label'] ?? 'Starts At' }}</div>
                </div>
            </div>
        </div>
        <div class="scroll-indicator">
            <div class="scroll-mouse">
                <div class="scroll-wheel"></div>
            </div>
            <span>Scroll</span>
        </div>
        <div class="hero-slash">
            <svg viewBox="0 0 1440 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,80 C360,140 1080,20 1440,80 L1440,120 L0,120 Z" fill="#f8fafc"/>
            </svg>
        </div>
    </section>

    <!-- =================== PLANS =================== -->
    <section id="plans" class="plans-section">
        <div class="section-header reveal">
            <span class="section-eyebrow">{{ $s['welcome_plans_eyebrow'] ?? 'Internet Plans' }}</span>
            <h2 class="section-title letter-pop">{{ $s['welcome_plans_title'] ?? 'Pick Your Perfect Plan' }}</h2>
            <p class="section-subtitle">{{ $s['welcome_plans_subtitle'] ?? 'Transparent pricing. No hidden fees. Cancel anytime.' }}</p>
        </div>

        <div class="plans-grid">
            @forelse($subscriptionRates as $index => $rate)
                @php
                    $isFeatured = $index === 1 || ($index === 0 && $subscriptionRates->count() === 2);
                    $delay = .1 + ($index * .1);
                    $icons = ['🏠', '👨‍👩‍👧‍👦', '🏢', '🚀', '💼', '⚡'];
                    $icon = $icons[$index % count($icons)];
                @endphp
                <div class="plan-card {{ $isFeatured ? 'featured' : '' }} reveal" style="transition-delay:{{ $delay }}s">
                    @if($isFeatured)
                        <div class="plan-badge">Most Popular</div>
                    @endif
                    <div class="plan-icon">{{ $icon }}</div>
                    <h3 class="plan-name">{{ $rate->plan_name }}</h3>
                    <p class="plan-speed">{{ $rate->speed }} · {{ $rate->plan_type }}</p>
                    <div class="plan-price"><sup>₱</sup>{{ number_format($rate->monthly_fee, 0) }}</div>
                    <div class="plan-divider"></div>
<p class="plan-description">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        {{ $rate->data_limit ?? 'Unlimited data' }} · {{ $rate->billing_cycle }}
                    </p>
                    <div class="plan-buttons">
                        <button class="view-details-btn" onclick="showPlanDetails('{{ $rate->plan_name }}', '{{ $rate->speed }}', '{{ $rate->plan_type }}', {{ $rate->monthly_fee }}, '{{ $rate->billing_cycle }}', '{{ $rate->data_limit ?? 'Unlimited' }}', {{ $rate->installation_fee ?? 0 }}, {{ $rate->activation_fee ?? 0 }}, {{ $rate->router_fee ?? 0 }}, '{{ $rate->lock_in_period ?? 'None' }}', {{ $rate->late_penalty ?? 0 }}, {{ $rate->reconnection_fee ?? 0 }})">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Details
                        </button>
                        <button class="subscribe-btn" onclick="handleSubscribe('{{ $rate->plan_name }}')">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Apply Now
                        </button>
                    </div>
                </div>
            @empty
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
                    <div class="plan-buttons">
                        <button class="view-details-btn" onclick="showPlanDetails('Basic','25 Mbps','Fiber',999,'Monthly','Unlimited',0,0,0,'None',0,0)">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Details
                        </button>
                        <button class="subscribe-btn" onclick="handleSubscribe('Basic')">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Apply Now
                        </button>
                    </div>
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
                    <div class="plan-buttons">
                        <button class="view-details-btn" onclick="showPlanDetails('Standard','50 Mbps','Fiber',1499,'Monthly','Unlimited',0,0,0,'None',0,0)">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Details
                        </button>
                        <button class="subscribe-btn" onclick="handleSubscribe('Standard')">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Apply Now
                        </button>
                    </div>
                </div>

                <div class="plan-card reveal" style="transition-delay:.3s">
                    <div class="plan-icon">🏢</div>
                    <h3 class="plan-name">Premium</h3>
                    <p class="plan-speed">Up to 100 Mbps · Fiber</p>
                    <div class="plan-price"><sup>₱</sup>2499</div>
                    <div class="plan-divider"></div>
                    <p class="plan-description">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Best for power users and businesses
                    </p>
                    <div class="plan-buttons">
                        <button class="view-details-btn" onclick="showPlanDetails('Premium','100 Mbps','Fiber',2499,'Monthly','Unlimited',0,0,0,'None',0,0)">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Details
                        </button>
                        <button class="subscribe-btn" onclick="handleSubscribe('Premium')">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Apply Now
                        </button>
                    </div>
                </div>
            @endforelse
        </div>
    </section>

    <!-- =================== FEATURES =================== -->
    <section id="features" class="features-section">
        <div class="section-header reveal" style="position:relative;z-index:1;">
            <span class="section-eyebrow" style="color:#fca5a5;">{{ $s['welcome_features_eyebrow'] ?? 'Why Choose Us' }}</span>
            <h2 class="section-title letter-pop">{{ $s['welcome_features_title'] ?? 'Built Different' }}</h2>
            <p class="section-subtitle">{{ $s['welcome_features_subtitle'] ?? "We're not just another ISP. Here's what sets us apart." }}</p>
        </div>

        <div class="features-grid reveal-stagger">
            <div class="feature-card" data-tip="Stream 4K without buffering">
                <div class="feature-icon-wrap">⚡</div>
                <h3 class="feature-title">{{ $s['welcome_features_blazing_title'] ?? 'Blazing Speeds' }}</h3>
                <p class="feature-desc">{{ $s['welcome_features_blazing_desc'] ?? 'Low latency, high throughput. Stream 4K, game online, and video call — all at the same time without a hitch.' }}</p>
            </div>
            <div class="feature-card" data-tip="Fiber across the entire region">
                <div class="feature-icon-wrap">📡</div>
                <h3 class="feature-title">{{ $s['welcome_features_coverage_title'] ?? 'Wide Coverage' }}</h3>
                <p class="feature-desc">{{ $s['welcome_features_coverage_desc'] ?? 'Our fiber network spans homes and businesses across the entire region. Reliable connectivity wherever you are.' }}</p>
            </div>
            <div class="feature-card" data-tip="Real humans, always available">
                <div class="feature-icon-wrap">🔧</div>
                <h3 class="feature-title">{{ $s['welcome_features_support_title'] ?? '24/7 Support' }}</h3>
                <p class="feature-desc">{{ $s['welcome_features_support_desc'] ?? 'Real humans answer your calls. Technical assistance available around the clock, every day of the year.' }}</p>
            </div>
        </div>
    </section>

    <!-- =================== CONTACT =================== -->
    <section id="contact" class="contact-section">
        <div class="section-header reveal">
            <span class="section-eyebrow">{{ $s['welcome_contact_eyebrow'] ?? 'Get In Touch' }}</span>
            <h2 class="section-title letter-pop">{{ $s['welcome_contact_title'] ?? "Let's Get You Connected" }}</h2>
            <p class="section-subtitle">{{ $s['welcome_contact_subtitle'] ?? "Drop us a message and we'll reach out within 24 hours." }}</p>
        </div>

        <div class="contact-wrap reveal" style="transition-delay:.15s;">
            <form class="contact-form" onsubmit="handleContact(event)">
                <div class="form-field">
                    <input type="text" placeholder="{{ $s['welcome_contact_form_name_ph'] ?? 'Your Full Name' }}" required>
                </div>
                <div class="form-field">
                    <input type="email" placeholder="{{ $s['welcome_contact_form_email_ph'] ?? 'Email Address' }}" required>
                </div>
                <div class="form-field">
                    <textarea placeholder="{{ $s['welcome_contact_form_message_ph'] ?? 'Tell us how we can help you…' }}" required></textarea>
                </div>
                <button type="submit" class="contact-submit">{{ $s['welcome_contact_submit'] ?? 'Send Message →' }}</button>
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
                    <h3>{{ $s['welcome_modal_welcome_back'] ?? 'Welcome Back' }}</h3>
                    <p>{{ $s['welcome_modal_signin_sub'] ?? 'Sign in to your NetManager account' }}</p>
                </div>
            </div>
            <div class="modal-body">
                <div class="modal-tabs">
                    <button class="modal-tab active" onclick="switchTab('login')">{{ $s['welcome_modal_tab_signin'] ?? 'Sign In' }}</button>
                    <button class="modal-tab" onclick="switchTab('register')">{{ $s['welcome_modal_tab_register'] ?? 'Create Account' }}</button>
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

<!-- =================== CLIENT APPLICATION MODAL =================== -->
    <div id="applyClientModal" class="modal">
        <div class="modal-box" style="max-width:500px;">
            <div class="modal-head">
                <div class="modal-head-grid"></div>
                <div class="modal-head-glow"></div>
                <button class="modal-close" onclick="closeApplyModal()">&times;</button>
                <div class="modal-head-content">
                    <h3>Apply for Internet Service</h3>
                    <p>Fill out the form below to submit your application</p>
                </div>
            </div>
            <div class="modal-body">
                <form id="applyClientForm" onsubmit="submitApplication(event)">
                    <div class="modal-field">
                        <label>Full Name</label>
                        <input type="text" name="name" required placeholder="Juan Dela Cruz">
                    </div>
                    <div class="modal-field">
                        <label>Email Address</label>
                        <input type="email" name="email" required placeholder="juan@example.com">
                    </div>
                    <div class="modal-field">
                        <label>Phone Number</label>
                        <input type="text" name="phone_number" required placeholder="09123456789">
                    </div>
                    <div class="modal-field">
                        <label>Profile Photo <span style="font-weight:400;text-transform:none;color:#94a3b8;">(Optional)</span></label>
                        <div style="display:flex;align-items:flex-start;gap:12px;margin-top:4px;">
                            <div id="apply-photo-preview-wrap" style="width:72px;height:72px;flex-shrink:0;border-radius:12px;overflow:hidden;border:2px dashed #e2e8f0;background:#f9fafb;display:flex;align-items:center;justify-content:center;">
                                <img id="apply-photo-preview" src="" alt="" style="width:100%;height:100%;object-fit:cover;display:none;">
                                <svg id="apply-photo-placeholder" style="width:28px;height:28px;color:#cbd5e1;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div style="flex:1;">
                                <input type="file" name="photo" id="apply-photo-input" accept="image/*" onchange="previewApplyPhoto(this)" style="width:100%;padding:.6rem .8rem;border:1.5px solid #e2e8f0;border-radius:10px;font-size:.85rem;font-family:'DM Sans',sans-serif;background:#f9fafb;cursor:pointer;">
                                <button type="button" id="apply-photo-remove" onclick="removeApplyPhoto()" style="display:none;margin-top:6px;font-size:.78rem;font-weight:600;color:#ef4444;background:none;border:none;cursor:pointer;">✕ Remove photo</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-field">
                        <label>PPPoE Name / Username</label>
                        <input type="text" name="pppoe_name" required placeholder="juandelacruz123">
                    </div>
<div class="modal-field">
                        <label>Barangay</label>
                        <input type="text" name="barangay" required placeholder="Poblacion">
                    </div>
                    <div class="modal-field">
                        <label>Select Plan</label>
<select name="plan_selected" id="planDescriptionSelect" required class="modal-select">
                            <option value="">-- Select a Plan --</option>
                            @foreach($subscriptionRates as $rate)
                                <option value="{{ $rate->plan_name }} - {{ $rate->speed }}">{{ $rate->plan_name }} - {{ $rate->speed }} (₱{{ number_format($rate->monthly_fee, 0) }}/month)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-field">
                        <label>NAP Box Location</label>
                        <input type="text" name="nap_box" required placeholder="NAP-001">
                    </div>
                    <div class="modal-field">
                        <label>Start Date</label>
                        <input type="date" name="start_date" id="apply-start-date" required onchange="syncApplyDueDate(this.value)">
                    </div>
                    <div class="modal-field">
                        <label>Due Date & Time <span style="font-weight:400;text-transform:none;color:#94a3b8;font-size:.75rem;">(auto: 1 month from start)</span></label>
                        <input type="datetime-local" name="due_date_time" id="apply-due-date" required>
                    </div>
                    <div class="modal-field">
                        <label>Notes (Optional)</label>
                        <input type="text" name="notes" placeholder="Any additional information...">
                    </div>
                    <div class="modal-field">
                        <label>Your Location <span style="font-weight:400;text-transform:none;color:#94a3b8;">(Optional but recommended)</span></label>
                        <input type="hidden" name="latitude" id="apply-latitude">
                        <input type="hidden" name="longitude" id="apply-longitude">
                        <button type="button" id="pin-location-btn" onclick="pinMyLocation()"
                                style="width:100%;padding:.75rem 1rem;border-radius:12px;font-weight:600;font-size:.88rem;border:1.5px dashed #bfdbfe;background:#eff6ff;color:#1d4ed8;font-family:'DM Sans',sans-serif;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .25s;">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span id="pin-location-text">📍 Pin My Location</span>
                        </button>
                        <p id="pin-location-status" style="font-size:.75rem;color:#64748b;margin-top:4px;display:none;"></p>
                    </div>
                    <button type="submit" id="applySubmitBtn" class="modal-submit">Submit Application</button>
                </form>
            </div>
            <div class="modal-foot">
                You will be notified once your application is approved by admin.
            </div>
        </div>
    </div>

    <!-- =================== PLAN DETAILS MODAL =================== -->
    <div id="planDetailsModal" class="modal">
        <div class="modal-box wide">
            <div class="modal-head">
                <div class="modal-head-grid"></div>
                <div class="modal-head-glow"></div>
                <button class="modal-close" onclick="closePlanDetailsModal()">&times;</button>
                <div class="modal-head-content">
                    <h3 id="detailPlanName">Plan Details</h3>
                    <p>Complete plan information</p>
                </div>
            </div>
            <div class="modal-body" id="planDetailsContent"></div>
            <div class="modal-foot">
                <button class="modal-submit" onclick="closePlanDetailsModal(); handleSubscribe('');">Apply Now</button>
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
                    <p class="footer-desc">{{ $s['welcome_footer_desc'] ?? 'Your trusted partner for reliable internet. Fast, secure, and affordable connectivity for every home and business.' }}</p>
                    <div class="footer-socials">
                        <a href="{{ $s['welcome_contact_social_fb'] ?? '#' }}" class="social-btn" title="Facebook">📘</a>
                        <a href="{{ $s['welcome_contact_social_twitter'] ?? '#' }}" class="social-btn" title="Twitter">🐦</a>
                        <a href="{{ $s['welcome_contact_social_instagram'] ?? '#' }}" class="social-btn" title="Instagram">📷</a>
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
                        <div class="footer-contact-text">{{ $s['welcome_contact_address'] ?? '123 Internet Street, Tech City, TC 12345' }}</div>
                    </div>
                    <div class="footer-contact-item">
                        <div class="footer-contact-icon">📞</div>
                        <div class="footer-contact-text">{{ $s['welcome_contact_phone'] ?? '+1 (555) 123-4567' }}</div>
                    </div>
                    <div class="footer-contact-item">
                        <div class="footer-contact-icon">✉️</div>
                        <div class="footer-contact-text">{{ $s['welcome_contact_email'] ?? 'support@netmanager.com' }}</div>
                    </div>
                </div>
            </div>

            <div class="footer-divider"></div>

            <div class="footer-bottom">
                <p class="footer-copy">{{ $s['welcome_footer_copyright'] ?? '© 2024 ISP Billing Management System. All rights reserved.' }}</p>
                <ul class="footer-bottom-links">
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Cookie Policy</a></li>
                </ul>
            </div>
        </div>
    </footer>

    <script>
        /* ===================== LETTER POP — split headings into spans ===================== */
        document.querySelectorAll('.letter-pop').forEach(el => {
            // Don't split if it has child elements (scramble span etc)
            if (el.children.length) return;
            el.innerHTML = el.textContent.split('').map(ch =>
                ch === ' ' ? ' ' : `<span>${ch}</span>`
            ).join('');
        });

        /* ===================== 3D TILT on plan & feature cards ===================== */
        document.querySelectorAll('.plan-card, .feature-card').forEach(card => {
            card.classList.add('tilt-card');
            card.addEventListener('mousemove', function(e) {
                const rect = this.getBoundingClientRect();
                const x = (e.clientX - rect.left) / rect.width  - 0.5;
                const y = (e.clientY - rect.top)  / rect.height - 0.5;
                const tiltX = y * -10;
                const tiltY = x *  10;
                this.style.transform = `perspective(800px) rotateX(${tiltX}deg) rotateY(${tiltY}deg) translateY(-8px) scale(1.02)`;
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = '';
                // restore featured card default
                if (this.classList.contains('featured')) {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                }
            });
        });

        /* ===================== FLOATING TOOLTIP ===================== */
        const tooltip = document.getElementById('cursor-tooltip');
        let tooltipTimeout;

        function showTooltip(text, x, y) {
            clearTimeout(tooltipTimeout);
            tooltip.textContent = text;
            tooltip.style.left = x + 'px';
            tooltip.style.top  = y + 'px';
            tooltip.classList.add('show');
        }
        function hideTooltip() {
            tooltipTimeout = setTimeout(() => tooltip.classList.remove('show'), 80);
        }

        // Elements with data-tip attribute get a floating tooltip
        document.querySelectorAll('[data-tip]').forEach(el => {
            el.addEventListener('mouseenter', function(e) {
                showTooltip(this.dataset.tip, e.clientX, e.clientY);
            });
            el.addEventListener('mousemove', function(e) {
                tooltip.style.left = e.clientX + 'px';
                tooltip.style.top  = e.clientY + 'px';
            });
            el.addEventListener('mouseleave', hideTooltip);
        });

        /* ===================== CURSOR ===================== */
        const dot  = document.getElementById('cursorDot');
        const ring = document.getElementById('cursorRing');
        let mx = 0, my = 0, rx = 0, ry = 0;

        document.addEventListener('mousemove', e => {
            mx = e.clientX; my = e.clientY;
            dot.style.left  = mx + 'px';
            dot.style.top   = my + 'px';
        });

        function animateCursor() {
            rx += (mx - rx) * 0.13;
            ry += (my - ry) * 0.13;
            ring.style.left = rx + 'px';
            ring.style.top  = ry + 'px';
            requestAnimationFrame(animateCursor);
        }
        animateCursor();

        document.querySelectorAll('a, button, input, textarea, .plan-card, .feature-card, .social-btn').forEach(el => {
            el.addEventListener('mouseenter', () => ring.classList.add('hover'));
            el.addEventListener('mouseleave', () => ring.classList.remove('hover'));
        });

        /* ===================== PARTICLE CANVAS ===================== */
        const canvas = document.getElementById('particle-canvas');
        const ctx = canvas.getContext('2d');
        let particles = [];
        let W, H;

        function resizeCanvas() {
            W = canvas.width  = canvas.offsetWidth;
            H = canvas.height = canvas.offsetHeight;
        }

        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        class Particle {
            constructor() { this.reset(true); }
            reset(fresh) {
                this.x = Math.random() * W;
                this.y = fresh ? Math.random() * H : H + 10;
                this.size   = Math.random() * 1.5 + 0.3;
                this.speedY = -(Math.random() * 0.4 + 0.1);
                this.speedX = (Math.random() - 0.5) * 0.2;
                this.alpha  = Math.random() * 0.5 + 0.1;
                this.fadeIn = 0;
            }
            update() {
                this.x += this.speedX;
                this.y += this.speedY;
                this.fadeIn = Math.min(this.fadeIn + 0.02, 1);
                if (this.y < -10) this.reset(false);
            }
            draw() {
                ctx.save();
                ctx.globalAlpha = this.alpha * this.fadeIn;
                ctx.fillStyle = `rgba(239, 68, 68, 1)`;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
                ctx.restore();
            }
        }

        for (let i = 0; i < 90; i++) particles.push(new Particle());

        function animateParticles() {
            ctx.clearRect(0, 0, W, H);
            particles.forEach(p => { p.update(); p.draw(); });
            requestAnimationFrame(animateParticles);
        }
        animateParticles();

        /* ===================== TEXT SCRAMBLE ===================== */
        const CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*';
        const scrambleEl = document.getElementById('scrambleText');
        const originalText = scrambleEl.textContent;
        let scrambleInterval = null;
        let scramblePlaying = false;

        function playScramble(el, original) {
            if (scramblePlaying) return;
            scramblePlaying = true;
            let iteration = 0;
            clearInterval(scrambleInterval);
            scrambleInterval = setInterval(() => {
                el.textContent = original.split('').map((char, idx) => {
                    if (char === ' ') return ' ';
                    if (idx < iteration) return original[idx];
                    return CHARS[Math.floor(Math.random() * CHARS.length)];
                }).join('');
                if (iteration >= original.length) {
                    clearInterval(scrambleInterval);
                    el.textContent = original;
                    scramblePlaying = false;
                }
                iteration += 0.5;
            }, 40);
        }

        // Auto-play on load
        setTimeout(() => playScramble(scrambleEl, originalText), 1200);
        // Replay on hover
        scrambleEl.addEventListener('mouseenter', () => playScramble(scrambleEl, originalText));

        /* ===================== COUNTER ANIMATION ===================== */
        function animateCount(el) {
            const target = parseInt(el.dataset.target);
            const duration = 1600;
            const start = performance.now();

            function step(now) {
                const progress = Math.min((now - start) / duration, 1);
                const ease = 1 - Math.pow(1 - progress, 4);
                el.textContent = Math.floor(ease * target);
                if (progress < 1) requestAnimationFrame(step);
                else el.textContent = target;
            }
            requestAnimationFrame(step);
        }

        const counters = document.querySelectorAll('.count');
        const counterObserver = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    animateCount(e.target);
                    counterObserver.unobserve(e.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(c => counterObserver.observe(c));

        /* ===================== STICKY NAV ===================== */
        window.addEventListener('scroll', () => {
            document.getElementById('main-nav').classList.toggle('scrolled', window.scrollY > 40);
        });

        /* ===================== SCROLL REVEAL ===================== */
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('visible');
                    revealObserver.unobserve(e.target);
                }
            });
        }, { threshold: .1, rootMargin: '0px 0px -60px 0px' });

        document.querySelectorAll('.reveal, .reveal-stagger').forEach(el => revealObserver.observe(el));

        /* ===================== RIPPLE EFFECT ===================== */
        function createRipple(e, el) {
            const rect = el.getBoundingClientRect();
            const ripple = document.createElement('span');
            const size = Math.max(rect.width, rect.height) * 2;
            ripple.style.cssText = `
                position:absolute; border-radius:50%; pointer-events:none;
                width:${size}px; height:${size}px;
                left:${e.clientX - rect.left - size/2}px;
                top:${e.clientY - rect.top - size/2}px;
                background:rgba(255,255,255,.15);
                transform:scale(0); animation:rippleAnim 0.6s ease-out forwards;
            `;
            el.style.position = 'relative';
            el.style.overflow = 'hidden';
            el.appendChild(ripple);
            setTimeout(() => ripple.remove(), 700);
        }

        const style = document.createElement('style');
        style.textContent = `@keyframes rippleAnim { to { transform:scale(1); opacity:0; } }`;
        document.head.appendChild(style);

        document.querySelectorAll('.btn-primary, .subscribe-btn, .modal-submit, .contact-submit').forEach(btn => {
            btn.addEventListener('click', e => createRipple(e, btn));
        });

        /* ===================== MAGNETIC BUTTONS ===================== */
        document.querySelectorAll('.btn-primary, .btn-secondary, .nav-login-btn').forEach(btn => {
            btn.addEventListener('mousemove', function(e) {
                const rect = this.getBoundingClientRect();
                const cx = rect.left + rect.width  / 2;
                const cy = rect.top  + rect.height / 2;
                const dx = (e.clientX - cx) * 0.25;
                const dy = (e.clientY - cy) * 0.25;
                this.style.transform = `translate(${dx}px, ${dy}px) translateY(-3px)`;
            });
            btn.addEventListener('mouseleave', function() {
                this.style.transform = '';
            });
        });

        /* ===================== TOAST ===================== */
        function showToast(msg, color = '#22c55e') {
            const toast = document.getElementById('toast');
            const icon  = toast.querySelector('.toast-icon');
            document.getElementById('toastMessage').textContent = msg;
            icon.style.background = color;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3500);
        }

        /* ===================== MODAL ===================== */
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

        document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeModal(); closePlanDetailsModal(); } });

        function switchTab(tab) {
            const tabs  = document.querySelectorAll('.modal-tab');
            const forms = document.querySelectorAll('.modal-form');
            tabs.forEach((t, i)  => t.classList.toggle('active',  (i === 0) === (tab === 'login')));
            forms.forEach((f, i) => f.classList.toggle('active', (i === 0) === (tab === 'login')));
        }

/* ===================== CLIENT APPLICATION ===================== */
        let selectedPlan = '';
        
        function openApplyModal(plan) {
            selectedPlan = plan;
            document.getElementById('applyClientModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeApplyModal() {
            document.getElementById('applyClientModal').classList.remove('active');
            document.body.style.overflow = '';
            removeApplyPhoto();
        }
        
        document.getElementById('applyClientModal').addEventListener('click', function(e) {
            if (e.target === this) closeApplyModal();
        });
        
function handleSubscribe(plan) {
            selectedPlan = plan;
            openApplyModal(plan);
            
            if (plan) {
                const planSelect = document.getElementById('planDescriptionSelect');
                if (planSelect) {
                    for (let i = 0; i < planSelect.options.length; i++) {
                        if (planSelect.options[i].text.includes(plan)) {
                            planSelect.selectedIndex = i;
                            break;
                        }
                    }
                }
            }

            // Set start date to today and due date to 1 month later
            const today = new Date();
            const startDateInput = document.querySelector('#applyClientForm input[name="start_date"]');
            const dueDateInput   = document.querySelector('#applyClientForm input[name="due_date_time"]');

            if (startDateInput) {
                startDateInput.value = today.toISOString().split('T')[0];
            }
            if (dueDateInput) {
                const due = new Date(today);
                due.setMonth(due.getMonth() + 1);
                due.setHours(12, 0, 0, 0);
                const pad = n => String(n).padStart(2, '0');
                dueDateInput.value = `${due.getFullYear()}-${pad(due.getMonth()+1)}-${pad(due.getDate())}T${pad(due.getHours())}:${pad(due.getMinutes())}`;
            }
        }
        
        // Submit client application via AJAX
        async function submitApplication(event) {
            event.preventDefault();

            const form = document.getElementById('applyClientForm');
            const formData = new FormData(form);

            // Use the start_date from the form and compute due_date as 1 month later
            const startVal = document.querySelector('#applyClientForm input[name="start_date"]').value;
            if (startVal) {
                const due = new Date(startVal);
                due.setMonth(due.getMonth() + 1);
                due.setHours(12, 0, 0, 0);
                const pad = n => String(n).padStart(2, '0');
                formData.set('due_date_time', `${due.getFullYear()}-${pad(due.getMonth()+1)}-${pad(due.getDate())}T${pad(due.getHours())}:${pad(due.getMinutes())}`);
            }
            
            const submitBtn = document.getElementById('applySubmitBtn');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Submitting...';
            submitBtn.disabled = true;
            
            try {
                const response = await fetch('{{ route("clients.storeGuest") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    closeApplyModal();
                    form.reset();
                    showToast('Application submitted! Please check your email to verify your address.', '#22c55e');
                } else {
                    showToast(result.message || 'Error submitting application', '#ef4444');
                }
            } catch (error) {
                showToast('Error submitting application. Please try again.', '#ef4444');
                console.error('Error:', error);
            } finally {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        }

        /* ===================== PLAN DETAILS ===================== */
        function showPlanDetails(planName, speed, planType, monthlyFee, billingCycle, dataLimit,
                                  installationFee, activationFee, routerFee, lockInPeriod, latePenalty, reconnectionFee) {
            const content = `
                <div class="plan-details-grid">
                    ${[
                        ['Plan Name', planName, ''],
                        ['Speed', speed, ''],
                        ['Plan Type', planType, ''],
                        ['Monthly Fee', '₱' + parseFloat(monthlyFee).toLocaleString(), 'price'],
                        ['Billing Cycle', billingCycle, ''],
                        ['Data Limit', dataLimit, ''],
                        ['Installation Fee', '₱' + parseFloat(installationFee).toLocaleString(), ''],
                        ['Activation Fee', '₱' + parseFloat(activationFee).toLocaleString(), ''],
                        ['Router Fee', '₱' + parseFloat(routerFee).toLocaleString(), ''],
                        ['Lock-in Period', lockInPeriod, ''],
                        ['Late Penalty', '₱' + parseFloat(latePenalty).toLocaleString(), ''],
                        ['Reconnection Fee', '₱' + parseFloat(reconnectionFee).toLocaleString(), ''],
                    ].map(([label, val, cls]) => `
                        <div class="plan-detail-item">
                            <div class="plan-detail-label">${label}</div>
                            <div class="plan-detail-value ${cls}">${val}</div>
                        </div>
                    `).join('')}
                </div>
            `;
            document.getElementById('detailPlanName').textContent = planName + ' — Plan Details';
            document.getElementById('planDetailsContent').innerHTML = content;
            document.getElementById('planDetailsModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closePlanDetailsModal() {
            document.getElementById('planDetailsModal').classList.remove('active');
            document.body.style.overflow = '';
        }

        document.getElementById('planDetailsModal').addEventListener('click', function(e) {
            if (e.target === this) closePlanDetailsModal();
        });

        /* ===================== PHOTO PREVIEW ===================== */
        function syncApplyDueDate(startVal) {
            if (!startVal) return;
            const due = new Date(startVal);
            due.setMonth(due.getMonth() + 1);
            due.setHours(12, 0, 0, 0);
            const pad = n => String(n).padStart(2, '0');
            document.getElementById('apply-due-date').value =
                `${due.getFullYear()}-${pad(due.getMonth()+1)}-${pad(due.getDate())}T${pad(due.getHours())}:${pad(due.getMinutes())}`;
        }

        function previewApplyPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('apply-photo-preview');
                    const placeholder = document.getElementById('apply-photo-placeholder');
                    const removeBtn = document.getElementById('apply-photo-remove');
                    const wrap = document.getElementById('apply-photo-preview-wrap');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                    removeBtn.style.display = 'inline-block';
                    wrap.style.border = '2px solid #e2e8f0';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        function removeApplyPhoto() {
            document.getElementById('apply-photo-input').value = '';
            document.getElementById('apply-photo-preview').src = '';
            document.getElementById('apply-photo-preview').style.display = 'none';
            document.getElementById('apply-photo-placeholder').style.display = 'block';
            document.getElementById('apply-photo-remove').style.display = 'none';
            document.getElementById('apply-photo-preview-wrap').style.border = '2px dashed #e2e8f0';
        }

        /* ===================== PIN LOCATION ===================== */
        function pinMyLocation() {
            const btn  = document.getElementById('pin-location-btn');
            const text = document.getElementById('pin-location-text');
            const status = document.getElementById('pin-location-status');

            if (!navigator.geolocation) {
                status.textContent = 'Geolocation is not supported by your browser.';
                status.style.display = 'block'; status.style.color = '#ef4444';
                return;
            }

            text.textContent = 'Detecting location...';
            btn.style.opacity = '.7';

            navigator.geolocation.getCurrentPosition(
                pos => {
                    document.getElementById('apply-latitude').value  = pos.coords.latitude.toFixed(7);
                    document.getElementById('apply-longitude').value = pos.coords.longitude.toFixed(7);
                    text.textContent = '\u2713 Location pinned!';
                    btn.style.background = '#f0fdf4';
                    btn.style.borderColor = '#86efac';
                    btn.style.color = '#15803d';
                    btn.style.opacity = '1';
                    status.textContent = `Lat: ${pos.coords.latitude.toFixed(5)}, Lng: ${pos.coords.longitude.toFixed(5)}`;
                    status.style.display = 'block'; status.style.color = '#15803d';
                },
                err => {
                    text.textContent = '\ud83d\udccd Pin My Location';
                    btn.style.opacity = '1';
                    status.textContent = 'Could not get location. Please allow location access.';
                    status.style.display = 'block'; status.style.color = '#ef4444';
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        }

        /* ===================== CONTACT ===================== */
        function handleContact(e) {
            e.preventDefault();
            const form = e.target;
            const btn  = form.querySelector('.contact-submit');
            const orig = btn.textContent;
            const name    = form.querySelector('input[type="text"]').value.trim();
            const email   = form.querySelector('input[type="email"]').value.trim();
            const message = form.querySelector('textarea').value.trim();

            btn.textContent = '✓ Sending…';
            btn.style.opacity = '.7';
            btn.disabled = true;

            fetch('{{ route("contact.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ name, email, message })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    btn.textContent = '✓ Message Sent!';
                    btn.style.opacity = '1';
                    btn.style.background = 'linear-gradient(135deg,#059669,#047857)';
                    showToast(data.message, '#22c55e');
                    if (data.remaining === 0) {
                        showToast('You have used all 3 message slots for this email.', '#f59e0b');
                    }
                    form.reset();
                    setTimeout(() => {
                        btn.textContent = orig;
                        btn.style.background = '';
                        btn.disabled = false;
                    }, 3000);
                } else {
                    btn.textContent = orig;
                    btn.style.opacity = '1';
                    btn.disabled = false;
                    showToast(data.message || 'Failed to send message.', '#ef4444');
                }
            })
            .catch(() => {
                btn.textContent = orig;
                btn.style.opacity = '1';
                btn.disabled = false;
                showToast('Network error. Please try again.', '#ef4444');
            });
        }
    </script>
</body>
</html>