<?php
/**
 * ILLUME — Internal OS & Landing Page
 * Integrated with PHP/MySQL backend
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Connection
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'illume_db';

$conn = new mysqli($host, $user, $pass, $db);

// Default site settings
$settings = [
    'brand_name' => 'ILLUME by Light Peace',
    'tagline'    => 'Crafted in Light',
    'email_contact' => 'lightpeacelimited@gmail.com',
    'whatsapp_number' => '+2349039963415'
];

if (!$conn->connect_error) {
    // Fetch site settings
    $res = $conn->query("SELECT setting_key, value FROM site_settings");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['value'];
        }
    }

    // Fetch latest revenue snapshot
    $revenue_res = $conn->query("SELECT * FROM revenue_snapshots ORDER BY year DESC, month DESC LIMIT 1");
    $stats = $revenue_res ? $revenue_res->fetch_assoc() : null;

    // Fetch service mix
    $mix = [];
    if ($stats) {
        $mix_res = $conn->query("SELECT * FROM revenue_category_breakdown WHERE snapshot_id = " . $stats['id']);
        while ($row = $mix_res->fetch_assoc()) {
            $mix[$row['category']] = $row;
        }
    }

    // Fetch latest leads
    $leads_res = $conn->query("SELECT * FROM leads ORDER BY created_at DESC LIMIT 5");
    $db_leads = [];
    while ($row = $leads_res ? $leads_res->fetch_assoc() : null) {
        if ($row) $db_leads[] = $row;
    }
}

// Format currency helper
function naira($val) {
    return '₦' . number_format((float)$val);
}
?>
<!DOCTYPE html>

<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($settings['brand_name']) ?> — <?= htmlspecialchars($settings['tagline']) ?></title>
    <meta name="description"
        content="ILLUME by Light Peace is an African luxury fashion house — bespoke bridals, asoebi, suits, and fashion consultancy. Crafted in light, in Abuja & Ebonyi, shipping globally." />
    <meta name="theme-color" content="#000000" />

    <!-- Open Graph -->
    <meta property="og:type" content="website" />
    <meta property="og:title" content="ILLUME by Light Peace — Crafted in Light" />
    <meta property="og:description"
        content="African luxury fashion. Bespoke bridals, asoebi, suits & consultancy. Radiance you can wear." />
    <meta property="og:locale" content="en_NG" />

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="ILLUME by Light Peace — Crafted in Light" />
    <meta name="twitter:description" content="African luxury fashion. Bespoke bridals, asoebi, suits & consultancy." />

    <!-- Schema.org LocalBusiness -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ClothingStore",
      "name": "ILLUME by Light Peace",
      "image": "",
      "description": "African luxury fashion house — bespoke bridals, asoebi, suits, and fashion consultancy.",
      "founder": "Ikedichukwu Peace",
      "foundingDate": "2018",
      "telephone": "+234 903 996 3415",
      "email": "lightpeacelimited@gmail.com",
      "address": [
        { "@type": "PostalAddress", "addressLocality": "Kubwa", "addressRegion": "Abuja", "addressCountry": "NG" },
        { "@type": "PostalAddress", "addressLocality": "Abakaliki", "addressRegion": "Ebonyi", "addressCountry": "NG" }
      ],
      "areaServed": ["Nigeria", "United Kingdom", "United States", "United Arab Emirates"]
    }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap"
        rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            black: '#000000',
                            gold: '#D4AF37',
                            'gold-deep': '#B8860B',
                            purple: '#301934',
                            milk: '#F5F5DC',
                            gray: '#1A1A1A',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 1s ease-out forwards',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        shimmer: 'shimmer 3s linear infinite',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        shimmer: {
                            '0%': { backgroundPosition: '-200% 0' },
                            '100%': { backgroundPosition: '200% 0' },
                        },
                    },
                },
            },
        };
    </script>

    <style>
        :root {
            color-scheme: light;
        }

        body {
            background-color: #FFFFFF;
            color: #1A1A1A;
            overflow-x: hidden;
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #F9F9F9;
        }

        ::-webkit-scrollbar-thumb {
            background: #E5E5E5;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #D4AF37;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .gold-gradient-text {
            background: linear-gradient(to right, #B8860B, #D4AF37, #B8860B);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .shimmer-text {
            background: linear-gradient(90deg, #B8860B 0%, #F5E6A8 50%, #B8860B 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .view-section {
            display: none;
            opacity: 0;
            transition: opacity 0.4s ease-in-out;
        }

        .view-section.active {
            display: block;
            opacity: 1;
        }

        .service-card {
            transition: transform 0.4s ease, box-shadow 0.4s ease, border-color 0.4s ease;
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            border-color: rgba(212, 175, 55, 0.4);
        }

        /* Decorative CSS-art "lookbook" tiles — replace with real imagery when ready */
        .lookbook-tile {
            position: relative;
            aspect-ratio: 3 / 4;
            border-radius: 14px;
            overflow: hidden;
            isolation: isolate;
        }

        .lookbook-tile::after {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(120% 80% at 20% 10%, rgba(212, 175, 55, 0.18), transparent 60%),
                radial-gradient(120% 80% at 80% 90%, rgba(48, 25, 52, 0.55), transparent 60%);
            mix-blend-mode: screen;
            pointer-events: none;
        }

        .lookbook-1 {
            background: linear-gradient(135deg, #1a1a1a 0%, #301934 70%, #B8860B 130%);
        }

        .lookbook-2 {
            background: linear-gradient(160deg, #F5F5DC 0%, #D4AF37 60%, #1a1a1a 130%);
        }

        .lookbook-3 {
            background: linear-gradient(200deg, #301934 0%, #1a1a1a 60%, #D4AF37 140%);
        }

        .lookbook-4 {
            background: linear-gradient(120deg, #D4AF37 0%, #F5F5DC 50%, #1a1a1a 130%);
        }

        .dash-card {
            background: #FFFFFF;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        }

        /* Chat widget */
        .chat-widget {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 100;
            pointer-events: none;
        }

        .chat-widget button {
            pointer-events: auto;
        }

        .chat-window {
            width: min(380px, calc(100vw - 32px));
            height: 520px;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 18px;
            box-shadow: 0 12px 50px rgba(0, 0, 0, 0.12);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transform: translateY(20px);
            opacity: 0;
            pointer-events: none;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .chat-window.open {
            transform: translateY(0);
            opacity: 1;
            pointer-events: auto;
        }

        .chat-header {
            background: #1A1A1A;
            color: #D4AF37;
            padding: 16px 20px;
            font-family: 'Playfair Display', serif;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-body {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
            background: #FAFAFA;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .chat-message {
            max-width: 85%;
            padding: 12px 16px;
            border-radius: 14px;
            font-size: 13px;
            line-height: 1.55;
            animation: fadeInUp 0.4s ease-out both;
        }

        .msg-bot {
            background: #FFFFFF;
            border: 1px solid rgba(0, 0, 0, 0.06);
            color: #1A1A1A;
            align-self: flex-start;
            border-bottom-left-radius: 3px;
        }

        .msg-user {
            background: #F5F5DC;
            color: #1A1A1A;
            align-self: flex-end;
            border-bottom-right-radius: 3px;
        }

        .chat-options {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 4px;
        }

        .chat-option-btn {
            background: transparent;
            border: 1px solid #D4AF37;
            color: #B8860B;
            padding: 9px 14px;
            border-radius: 999px;
            font-size: 12px;
            text-align: left;
            transition: background 0.2s, color 0.2s;
            cursor: pointer;
        }

        .chat-option-btn:hover,
        .chat-option-btn:focus-visible {
            background: #D4AF37;
            color: #FFFFFF;
            outline: none;
        }

        /* Typing indicator */
        .typing {
            display: inline-flex;
            gap: 4px;
            padding: 4px 0;
        }

        .typing span {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #D4AF37;
            opacity: 0.4;
            animation: typing 1.2s infinite;
        }

        .typing span:nth-child(2) {
            animation-delay: 0.15s;
        }

        .typing span:nth-child(3) {
            animation-delay: 0.3s;
        }

        @keyframes typing {

            0%,
            60%,
            100% {
                transform: translateY(0);
                opacity: 0.4
            }

            30% {
                transform: translateY(-4px);
                opacity: 1
            }
        }

        /* Modal */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 999999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 16px;
            animation: fadeInUp 0.3s ease-out;
        }

        .modal-backdrop.open {
            display: flex;
        }

        .modal-card {
            background: #fff;
            border-radius: 18px;
            max-width: 520px;
            width: 100%;
            padding: 32px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.2);
        }

        /* Focus rings, a11y */
        a:focus-visible,
        button:focus-visible,
        input:focus-visible,
        textarea:focus-visible,
        select:focus-visible {
            outline: 2px solid #D4AF37;
            outline-offset: 2px;
            border-radius: 4px;
        }

        /* Sticky mobile CTA */
        .sticky-cta {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            display: none;
            z-index: 90;
        }

        @media (max-width: 640px) {
            .sticky-cta {
                display: block;
            }

            main {
                padding-bottom: 90px;
            }
        }

        /* Pipeline pulse */
        .new-lead {
            animation: newLead 1.6s ease-out;
        }

        @keyframes newLead {
            0% {
                box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.45);
                transform: scale(0.98);
            }

            70% {
                box-shadow: 0 0 0 12px rgba(212, 175, 55, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(212, 175, 55, 0);
                transform: scale(1);
            }
        }
    </style>
</head>

<body class="antialiased font-sans flex flex-col min-h-screen bg-white text-brand-black">

    <a href="#main"
        class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:bg-brand-black focus:text-brand-gold focus:px-4 focus:py-2 focus:rounded">Skip
        to content</a>

    <!-- Booking Modal -->
    <div id="booking-modal" class="modal-backdrop" style="z-index: 1000000;" role="dialog" aria-modal="true" aria-labelledby="booking-title">
        <div class="modal-card">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <p class="text-brand-gold uppercase tracking-widest text-xs mb-2 font-medium">Begin Your Journey</p>
                    <h3 id="booking-title" class="font-serif text-2xl text-brand-black">Request a Fitting</h3>
                </div>
                <button onclick="closeBookingModal()" aria-label="Close" class="text-gray-400 hover:text-brand-black">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="booking-form" class="space-y-4">
                <div>
                    <label class="text-xs uppercase tracking-widest text-gray-500 block mb-1">Full Name</label>
                    <input required name="name" type="text"
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm focus:border-brand-gold focus:bg-white outline-none" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs uppercase tracking-widest text-gray-500 block mb-1">Email</label>
                        <input required name="email" type="email"
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm focus:border-brand-gold focus:bg-white outline-none" />
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-widest text-gray-500 block mb-1">WhatsApp</label>
                        <input required name="phone" type="tel" placeholder="+234..."
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm focus:border-brand-gold focus:bg-white outline-none" />
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs uppercase tracking-widest text-gray-500 block mb-1">Service</label>
                        <select name="service" id="booking-service"
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm focus:border-brand-gold focus:bg-white outline-none">
                            <option>Bridals &amp; Asoebi</option>
                            <option>Suits &amp; Dinner</option>
                            <option>African Luxury</option>
                            <option>Consultancy</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-widest text-gray-500 block mb-1">Timeline</label>
                        <select name="timeline"
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm focus:border-brand-gold focus:bg-white outline-none">
                            <option>Less than 3 months</option>
                            <option>3 — 6 months</option>
                            <option>6 — 12 months</option>
                            <option>Flexible</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-xs uppercase tracking-widest text-gray-500 block mb-1">Notes (optional)</label>
                    <textarea name="notes" rows="3"
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm focus:border-brand-gold focus:bg-white outline-none"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-brand-black text-white py-3 rounded-full uppercase tracking-widest text-xs hover:bg-brand-gold hover:text-brand-black transition-all">Send
                        Request</button>
                    <a href="https://wa.me/2349039963415" target="_blank" rel="noopener"
                        class="flex-1 text-center border border-brand-black/15 py-3 rounded-full uppercase tracking-widest text-xs hover:border-brand-gold hover:text-brand-gold transition-colors">WhatsApp
                        Instead</a>
                </div>
                <p class="text-[10px] text-gray-400 text-center">Your details are kept private and routed only to the
                    receptionist desk.</p>
            </form>
            <div id="booking-success" class="hidden text-center py-8">
                <div class="w-14 h-14 mx-auto rounded-full bg-brand-gold/10 border border-brand-gold flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="font-serif text-2xl text-brand-black mb-2">Beautifully received.</h3>
                <p class="text-sm text-gray-500 mb-6">Your request is now with our receptionist desk. Expect a personal
                    reply within the day.</p>
                <button onclick="closeBookingModal()"
                    class="text-xs uppercase tracking-widest text-brand-gold hover:underline">Close</button>
            </div>
        </div>
    </div>

    <!-- Lead Magnet Modal -->
    <div id="leadmag-modal" class="modal-backdrop" style="z-index: 1000001;" role="dialog" aria-modal="true" aria-labelledby="leadmag-title">
        <div class="modal-card">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <p class="text-brand-gold uppercase tracking-widest text-xs mb-2 font-medium">The Silhouette Guide</p>
                    <h3 id="leadmag-title" class="font-serif text-2xl text-brand-black">Receive your complimentary copy.</h3>
                </div>
                <button onclick="closeLeadMagnet()" aria-label="Close" class="text-gray-400 hover:text-brand-black">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="leadmag-form" class="space-y-4">
                <input required name="name" type="text" placeholder="Full name"
                    class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm focus:border-brand-gold focus:bg-white outline-none" />
                <input required name="email" type="email" placeholder="Email address"
                    class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm focus:border-brand-gold focus:bg-white outline-none" />
                <button type="submit"
                    class="w-full bg-brand-black text-white py-3 rounded-full uppercase tracking-widest text-xs hover:bg-brand-gold hover:text-brand-black transition-all">Send
                    Me the Guide</button>
                <p class="text-[10px] text-gray-400 text-center">We will email the guide and add you to our private
                    edition.</p>
            </form>
            <div id="leadmag-success" class="hidden text-center py-8">
                <h3 class="font-serif text-2xl text-brand-black mb-2">Sent with care.</h3>
                <p class="text-sm text-gray-500">Check your inbox — the guide is on its way.</p>
                <button onclick="closeLeadMagnet()"
                    class="mt-6 text-xs uppercase tracking-widest text-brand-gold hover:underline">Close</button>
            </div>
        </div>
    </div>

    <nav id="topnav" class="fixed w-full z-[99999] glass-nav transition-all duration-300" aria-label="Primary">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="flex justify-between items-center h-20 md:h-24">
                <button class="flex items-center cursor-pointer" onclick="switchView('home')" aria-label="<?= htmlspecialchars($settings['brand_name']) ?> — Home">
                    <img src="assets/img/logo.png" alt="ILLUME" class="h-8 md:h-10 w-auto mr-3">
                    <span
                        class="font-sans text-lg md:text-xl tracking-[0.3em] font-light text-brand-black mt-1">ILLUME</span>
                </button>
                <div class="hidden md:flex space-x-10 items-center">
                    <button onclick="switchView('home')"
                        class="cursor-pointer text-sm uppercase tracking-widest text-brand-black hover:text-brand-gold transition-colors font-light">The
                        House</button>
                    <button onclick="switchView('lookbook')"
                        class="cursor-pointer text-sm uppercase tracking-widest text-brand-black hover:text-brand-gold transition-colors font-light">Lookbook</button>
                    <button onclick="switchView('founder')"
                        class="cursor-pointer text-sm uppercase tracking-widest text-brand-black hover:text-brand-gold transition-colors font-light">Legacy</button>
                    <button onclick="openBookingModal()"
                        class="cursor-pointer text-sm uppercase tracking-widest text-brand-black hover:text-brand-gold transition-colors font-light">Book</button>
                    <button onclick="window.location.assign('login.php')"
                        class="cursor-pointer text-sm uppercase tracking-widest text-brand-black hover:text-brand-gold transition-colors font-light">Internal OS</button>
                </div>


                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-brand-black focus:outline-none" aria-expanded="false"
                        aria-controls="mobile-menu" aria-label="Open menu">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100 absolute w-full shadow-lg">
            <div class="px-6 pt-4 pb-6 space-y-3 flex flex-col">
                <button onclick="switchView('home'); toggleMobileMenu()"
                    class="text-left text-sm uppercase tracking-widest py-2">The House</button>
                <button onclick="switchView('lookbook'); toggleMobileMenu()"
                    class="text-left text-sm uppercase tracking-widest py-2">Lookbook</button>
                <button onclick="switchView('founder'); toggleMobileMenu()"
                    class="text-left text-sm uppercase tracking-widest py-2">Legacy</button>
                <button onclick="openBookingModal(); toggleMobileMenu()"
                    class="text-left text-sm uppercase tracking-widest py-2">Book a Fitting</button>
                <button onclick="window.location.assign('login.php')"
                    class="text-left text-sm uppercase tracking-widest py-2">Internal OS</button>
            </div>
        </div>
    </nav>

    <main id="main" class="flex-grow pt-20 md:pt-24">

        <!-- HOME -->
        <section id="home" class="view-section active" aria-label="Home">
            <div class="relative min-h-[85vh] flex items-center justify-center overflow-hidden">
                <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-brand-purple rounded-full mix-blend-multiply filter blur-[120px] opacity-5 animate-pulse-slow"
                    aria-hidden="true"></div>
                <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-brand-gold rounded-full mix-blend-multiply filter blur-[120px] opacity-10"
                    aria-hidden="true"></div>

                <div class="relative z-10 text-center px-6 max-w-4xl mx-auto opacity-0 animate-fade-in-up"
                    style="animation-delay: 0.15s;">
                    <p class="text-brand-gold uppercase tracking-[0.4em] text-xs md:text-sm mb-6 font-medium"><?= htmlspecialchars($settings['brand_name']) ?> — African Luxury</p>
                    <h1 class="text-5xl md:text-7xl lg:text-8xl font-serif text-brand-black mb-8 leading-tight">
                        Crafted in <span class="gold-gradient-text italic">Light.</span>
                    </h1>
                    <p class="text-lg md:text-2xl text-gray-500 font-light mb-12 max-w-2xl mx-auto">
                        Radiance you can wear. We don't just design fashion — we illuminate identity.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <button id="hero-book-btn"
                            class="bg-brand-black text-white px-8 md:px-10 py-4 rounded-full uppercase tracking-widest text-sm hover:bg-brand-gold hover:text-brand-black transition-all duration-300 shadow-lg hover:shadow-xl relative z-[100000]">
                            Begin Your Journey
                        </button>
                        <button id="hero-guide-btn"
                            class="border border-brand-black/20 text-brand-black px-8 py-4 rounded-full uppercase tracking-widest text-xs hover:border-brand-gold hover:text-brand-gold transition-colors relative z-[100000]">
                            Download the Silhouette Guide
                        </button>
                    </div>
                </div>
            </div>

            <!-- Brand Promise -->
            <div class="bg-brand-milk/30 py-24 md:py-32 px-6 border-y border-gray-100">
                <div class="max-w-5xl mx-auto text-center">
                    <h2 class="font-serif text-3xl md:text-5xl text-brand-black mb-10 leading-relaxed">
                        "Fashion should not just be seen, <span class="text-brand-gold italic">but felt."</span>
                    </h2>
                    <p class="text-gray-600 font-light leading-loose md:text-lg max-w-3xl mx-auto">
                        Born from Light Peace Limited, ILLUME exists at the intersection of heritage and modernity,
                        translating African narratives into timeless, wearable art. Each garment is intentionally
                        designed — honoring tradition while embracing contemporary global standards. To wear ILLUME is
                        to carry light: quietly, confidently, proudly.
                    </p>
                </div>
            </div>

            <!-- Services -->
            <div class="py-24 md:py-32 px-6 max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row justify-between items-end mb-12 md:mb-16">
                    <div>
                        <p class="text-brand-gold uppercase tracking-widest text-xs mb-2 font-medium">Our Ecosystem</p>
                        <h2 class="font-serif text-4xl md:text-5xl text-brand-black">The Bespoke Experience</h2>
                    </div>
                    <p class="mt-6 md:mt-0 text-gray-500 font-light max-w-md text-sm md:text-right">
                        Precision tailoring, culturally rooted elegance, and mastered silhouettes for the corridors of
                        power.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
                    <article
                        class="service-card border border-gray-100 p-8 rounded-2xl bg-white flex flex-col h-full shadow-sm">
                        <div
                            class="h-12 w-12 rounded-full border border-brand-gold flex items-center justify-center text-brand-gold font-serif text-xl mb-8 bg-brand-gold/5">
                            1</div>
                        <h3 class="text-xl font-serif text-brand-black mb-4">Bridals &amp; Asoebi</h3>
                        <p class="text-sm text-gray-500 font-light flex-grow">Culturally rooted elegance for life's most
                            sacred moments. Masterful beading and custom dressmaking.</p>
                        <button onclick="openBookingModal('Bridals & Asoebi')"
                            class="mt-6 text-xs uppercase tracking-widest text-brand-black hover:text-brand-gold transition-colors text-left">Request
                            a fitting →</button>
                    </article>
                    <article
                        class="service-card border border-gray-100 p-8 rounded-2xl bg-white flex flex-col h-full lg:mt-8 shadow-sm">
                        <div
                            class="h-12 w-12 rounded-full border border-brand-purple flex items-center justify-center text-brand-purple font-serif text-xl mb-8 bg-brand-purple/5">
                            2</div>
                        <h3 class="text-xl font-serif text-brand-black mb-4">Suits &amp; Dinner</h3>
                        <p class="text-sm text-gray-500 font-light flex-grow">Mastered silhouettes for the corridors of
                            power. Sharp, intentional, undeniably confident.</p>
                        <button onclick="openBookingModal('Suits & Dinner')"
                            class="mt-6 text-xs uppercase tracking-widest text-brand-black hover:text-brand-gold transition-colors text-left">Commission
                            a suit →</button>
                    </article>
                    <article
                        class="service-card border border-gray-100 p-8 rounded-2xl bg-white flex flex-col h-full shadow-sm">
                        <div
                            class="h-12 w-12 rounded-full border border-brand-gold flex items-center justify-center text-brand-gold font-serif text-xl mb-8 bg-brand-gold/5">
                            3</div>
                        <h3 class="text-xl font-serif text-brand-black mb-4">African Luxury</h3>
                        <p class="text-sm text-gray-500 font-light flex-grow">Traditional forms reimagined for the
                            global stage. Authentic identity meets unparalleled craftsmanship.</p>
                        <button onclick="openBookingModal('African Luxury')"
                            class="mt-6 text-xs uppercase tracking-widest text-brand-black hover:text-brand-gold transition-colors text-left">Begin
                            a piece →</button>
                    </article>
                    <article
                        class="service-card border border-gray-100 p-8 rounded-2xl bg-white flex flex-col h-full lg:mt-8 shadow-sm">
                        <div
                            class="h-12 w-12 rounded-full border border-brand-purple flex items-center justify-center text-brand-purple font-serif text-xl mb-8 bg-brand-purple/5">
                            4</div>
                        <h3 class="text-xl font-serif text-brand-black mb-4">Consultancy</h3>
                        <p class="text-sm text-gray-500 font-light flex-grow">Expert guidance on visual identity,
                            fashion illustration, and building a wardrobe of quiet power.</p>
                        <button onclick="openBookingModal('Consultancy')"
                            class="mt-6 text-xs uppercase tracking-widest text-brand-black hover:text-brand-gold transition-colors text-left">Book
                            a consult →</button>
                    </article>
                </div>
            </div>

            <!-- Testimonials -->
            <div class="bg-brand-black text-white py-24 md:py-32 px-6">
                <div class="max-w-6xl mx-auto">
                    <p class="text-brand-gold uppercase tracking-widest text-xs mb-2 font-medium text-center">In Their
                        Words</p>
                    <h2 class="font-serif text-3xl md:text-4xl text-center mb-16">Worn by those who carry light.</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <figure class="border border-white/10 p-8 rounded-2xl bg-white/5">
                            <blockquote class="font-serif italic text-lg mb-6 leading-relaxed">"My wedding dress was not
                                made — it was composed. Every bead held meaning."</blockquote>
                            <figcaption class="text-xs uppercase tracking-widest text-brand-gold">— Adaeze · Abuja
                            </figcaption>
                        </figure>
                        <figure class="border border-white/10 p-8 rounded-2xl bg-white/5">
                            <blockquote class="font-serif italic text-lg mb-6 leading-relaxed">"The suit walked into the
                                boardroom before I did. That is ILLUME."</blockquote>
                            <figcaption class="text-xs uppercase tracking-widest text-brand-gold">— Mr. C. · London
                            </figcaption>
                        </figure>
                        <figure class="border border-white/10 p-8 rounded-2xl bg-white/5">
                            <blockquote class="font-serif italic text-lg mb-6 leading-relaxed">"Their consultancy
                                reshaped not just my wardrobe — but my sense of presence."</blockquote>
                            <figcaption class="text-xs uppercase tracking-widest text-brand-gold">— Mrs. O. · Lagos
                            </figcaption>
                        </figure>
                    </div>
                </div>
            </div>

            <!-- Lead magnet strip -->
            <div class="py-20 px-6 max-w-7xl mx-auto">
                <div
                    class="rounded-3xl bg-gradient-to-br from-brand-milk/40 via-white to-brand-gold/10 border border-brand-gold/20 p-10 md:p-16 flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="max-w-xl">
                        <p class="text-brand-gold uppercase tracking-widest text-xs mb-3 font-medium">The Silhouette
                            Guide</p>
                        <h3 class="font-serif text-3xl md:text-4xl text-brand-black mb-4">Discover the silhouette that
                            carries your light.</h3>
                        <p class="text-gray-600 font-light">A complimentary guide — body geometry, fabric language, and
                            the architecture of presence. Used in our consultancy intake.</p>
                    </div>
                    <button id="strip-guide-btn"
                        class="shrink-0 bg-brand-black text-white px-8 py-4 rounded-full uppercase tracking-widest text-xs hover:bg-brand-gold hover:text-brand-black transition-all relative z-[100000]">Receive
                        the Guide</button>
                </div>
            </div>
        </section>

        <!-- LOOKBOOK -->
        <section id="lookbook" class="view-section pt-8 pb-32 px-6" aria-label="Lookbook">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <p class="text-brand-gold uppercase tracking-widest text-xs mb-2 font-medium">The Atelier Lookbook
                    </p>
                    <h2 class="font-serif text-4xl md:text-5xl text-brand-black">Compositions in light.</h2>
                    <p class="text-gray-500 font-light mt-4 max-w-xl mx-auto">A curated edition. Final imagery is
                        reserved for our private clients — request the full archive on booking.</p>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                    <figure class="lookbook-tile lookbook-1 relative overflow-hidden">
                        <img src="assets/img/bridalspics.png" alt="Bridal · 01" class="absolute inset-0 w-full h-full object-cover object-top">
                        <figcaption
                            class="absolute bottom-3 left-4 text-white/80 text-xs uppercase tracking-widest z-10">Bridal
                            · 01</figcaption>
                    </figure>
                    <figure class="lookbook-tile lookbook-2 mt-8 relative overflow-hidden">
                        <img src="assets/img/asoebipics.png" alt="Asoebi · 02" class="absolute inset-0 w-full h-full object-cover object-top">
                        <figcaption
                            class="absolute bottom-3 left-4 text-brand-black/80 text-xs uppercase tracking-widest z-10">
                            Asoebi · 02</figcaption>
                    </figure>
                    <figure class="lookbook-tile lookbook-3 relative overflow-hidden">
                        <img src="assets/img/suitpics.png" alt="Suit · 03" class="absolute inset-0 w-full h-full object-cover object-top">
                        <figcaption
                            class="absolute bottom-3 left-4 text-white/80 text-xs uppercase tracking-widest z-10">Suit ·
                            03</figcaption>
                    </figure>
                    <figure class="lookbook-tile lookbook-4 mt-8 relative overflow-hidden">
                        <img src="assets/img/luxurypics.png" alt="Luxury · 04" class="absolute inset-0 w-full h-full object-cover object-top">
                        <figcaption
                            class="absolute bottom-3 left-4 text-brand-black/80 text-xs uppercase tracking-widest z-10">
                            Luxury · 04</figcaption>
                    </figure>
                    <figure class="lookbook-tile lookbook-2 mt-4 relative overflow-hidden">
                        <img src="assets/img/dinnerpics.png" alt="Dinner · 05" class="absolute inset-0 w-full h-full object-cover object-top">
                        <figcaption
                            class="absolute bottom-3 left-4 text-brand-black/80 text-xs uppercase tracking-widest z-10">
                            Dinner · 05</figcaption>
                    </figure>
                    <figure class="lookbook-tile lookbook-3 relative overflow-hidden">
                        <img src="assets/img/couturepics.png" alt="Couture · 06" class="absolute inset-0 w-full h-full object-cover object-top">
                        <figcaption
                            class="absolute bottom-3 left-4 text-white/80 text-xs uppercase tracking-widest z-10">
                            Couture · 06</figcaption>
                    </figure>
                    <figure class="lookbook-tile lookbook-1 mt-4 relative overflow-hidden">
                        <img src="assets/img/heritagepics.png" alt="Heritage · 07" class="absolute inset-0 w-full h-full object-cover object-top">
                        <figcaption
                            class="absolute bottom-3 left-4 text-white/80 text-xs uppercase tracking-widest z-10">
                            Heritage · 07</figcaption>
                    </figure>
                    <figure class="lookbook-tile lookbook-4 relative overflow-hidden">
                        <img src="assets/img/editorialpics.png" alt="Editorial · 08" class="absolute inset-0 w-full h-full object-cover object-top">
                        <figcaption
                            class="absolute bottom-3 left-4 text-brand-black/80 text-xs uppercase tracking-widest z-10">
                            Editorial · 08</figcaption>
                    </figure>
                </div>
                <div class="text-center mt-16">
                    <button onclick="openBookingModal()"
                        class="bg-brand-black text-white px-10 py-4 rounded-full uppercase tracking-widest text-sm hover:bg-brand-gold hover:text-brand-black transition-all">Request
                        the Private Archive</button>
                </div>
            </div>
        </section>

        <!-- FOUNDER -->
        <section id="founder" class="view-section pt-12 pb-32" aria-label="Founder Legacy">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div
                        class="relative h-[500px] md:h-[600px] rounded-2xl overflow-hidden bg-gray-50 flex items-center justify-center border border-gray-100 shadow-sm">
                        <img src="assets/img/founder.jpg" alt="Ikedichukwu Peace" class="absolute inset-0 w-full h-full object-cover object-top">
                        <div class="absolute bottom-0 left-0 w-full p-8 text-white z-10">
                            <span class="font-serif italic text-3xl text-brand-gold opacity-90 block mb-1">I.P.</span>
                            <p class="text-white tracking-widest uppercase text-xs font-bold opacity-90">Ikedichukwu Peace</p>
                            <p class="text-xs text-gray-300 mt-1">Founder · ILLUME</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-brand-gold uppercase tracking-widest text-xs mb-4 font-medium">The Visionary</p>
                        <h2 class="font-serif text-4xl md:text-5xl text-brand-black mb-8">From Creation <span
                                class="italic text-gray-400">to Curation.</span></h2>

                        <div class="space-y-6 text-gray-500 font-light leading-relaxed">
                            <p>ILLUME traces its roots back to early 2018, when what is now a growing fashion house
                                began as a simple but deeply intentional vision. It began with conviction — a belief
                                that fashion could be more than fabric and trends, that it could become a language of
                                identity, dignity, and quiet confidence.</p>
                            <p>The years that followed were marked by refinement and clarity. Skills were sharpened.
                                Standards were raised. Out of this evolution, ILLUME emerged as the defining expression
                                of that vision — a dedicated arm focused on African luxury, cultural identity, and
                                refined craftsmanship.</p>
                            <blockquote
                                class="border-l-2 border-brand-gold pl-6 my-8 text-brand-black font-serif italic text-xl">
                                "We do not create by chance — every detail is deliberate, every finish refined. True
                                luxury does not shout. It radiates."
                            </blockquote>
                            <p>Today, ILLUME stands as a brand built on years of intentional growth, remaining grounded
                                in its core philosophy: to express excellence through timeless, purposeful design.</p>
                        </div>

                        <div class="mt-12 grid grid-cols-2 gap-8 border-t border-gray-100 pt-8">
                            <div>
                                <h4 class="text-brand-black font-medium mb-2 text-xs uppercase tracking-widest">Founded
                                </h4>
                                <p class="text-brand-gold font-serif text-2xl italic">2018</p>
                            </div>
                            <div>
                                <h4 class="text-brand-black font-medium mb-2 text-xs uppercase tracking-widest">Ateliers
                                </h4>
                                <p class="text-brand-gold font-serif text-2xl italic">Abuja &amp; Ebonyi</p>
                            </div>
                        </div>

                        <div class="mt-10">
                            <button onclick="openBookingModal()"
                                class="bg-brand-black text-white px-8 py-3 rounded-full uppercase tracking-widest text-xs hover:bg-brand-gold hover:text-brand-black transition-all">Meet
                                the Atelier</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- DASHBOARDS -->
        <section id="dashboards" class="view-section py-12 px-6 bg-gray-50 min-h-screen"
            aria-label="Internal Dashboards">
            <div class="max-w-7xl mx-auto">
                <div class="mb-10 text-center">
                    <span class="font-serif italic text-3xl text-brand-gold mb-2 inline-block">I</span>
                    <h2 class="font-serif text-3xl text-brand-black">ILLUME Internal OS</h2>
                    <p class="text-xs text-gray-500 tracking-widest uppercase mt-2">Enterprise Management Workspace</p>
                    <div class="mt-6 flex justify-center gap-4">
                        <a href="login.php" class="text-[10px] uppercase tracking-[0.2em] px-8 py-3 rounded-full bg-brand-black text-white hover:bg-brand-gold hover:text-brand-black transition-all">Enter Enterprise OS</a>
                    </div>
                </div>

                <div class="flex justify-center mb-8 border-b border-gray-200 overflow-x-auto" role="tablist">
                    <button onclick="switchTab('founder-dash')" id="btn-founder-dash" role="tab" aria-selected="true"
                        class="tab-btn whitespace-nowrap pb-4 px-6 text-sm font-medium uppercase tracking-widest border-b-2 border-brand-black text-brand-black transition-colors">Founder</button>
                    <button onclick="switchTab('receptionist-dash')" id="btn-receptionist-dash" role="tab"
                        aria-selected="false"
                        class="tab-btn whitespace-nowrap pb-4 px-6 text-sm font-medium uppercase tracking-widest border-b-2 border-transparent text-gray-400 hover:text-brand-black transition-colors">Receptionist</button>
                    <button onclick="switchTab('finance-dash')" id="btn-finance-dash" role="tab" aria-selected="false"
                        class="tab-btn whitespace-nowrap pb-4 px-6 text-sm font-medium uppercase tracking-widest border-b-2 border-transparent text-gray-400 hover:text-brand-black transition-colors">Finance</button>
                </div>

                <!-- Founder -->
                <div id="founder-dash" class="dash-tab" role="tabpanel">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="dash-card">
                            <h4 class="text-xs uppercase tracking-widest text-gray-400 mb-2">Total Monthly Revenue</h4>
                            <p class="text-3xl font-serif text-brand-black"><?= $stats ? naira($stats['gross_revenue']) : '₦0' ?></p>
                            <p class="text-xs text-green-500 mt-2">+12% from last month</p>
                        </div>
                        <div class="dash-card">
                            <h4 class="text-xs uppercase tracking-widest text-gray-400 mb-2">Active Bespoke Pieces</h4>
                            <p class="text-3xl font-serif text-brand-black">18</p>
                            <p class="text-xs text-brand-gold mt-2">6 in final hand-beading</p>
                        </div>
                        <div class="dash-card">
                            <h4 class="text-xs uppercase tracking-widest text-gray-400 mb-2">Lead Conversion Rate</h4>
                            <p class="text-3xl font-serif text-brand-black"><?= htmlspecialchars($settings['lead_conversion_rate'] ?? '0') ?>%</p>
                            <p class="text-xs text-gray-500 mt-2">High demand in Bridal &amp; Suits</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                        <div class="dash-card lg:col-span-2">
                            <div class="flex justify-between items-center mb-6">
                                <h4 class="text-xs uppercase tracking-widest text-brand-black font-medium">Revenue ·
                                    Last 6 Months</h4>
                                <span class="text-xs text-gray-400">in ₦M</span>
                            </div>
                            <div class="flex items-end gap-3 md:gap-5 h-40">
                                <!-- Pure CSS bar chart -->
                                <div class="flex-1 flex flex-col items-center gap-2">
                                    <div class="w-full bg-brand-gold/20 rounded-t-md" style="height: 45%"></div>
                                    <span class="text-[10px] text-gray-400">Dec</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center gap-2">
                                    <div class="w-full bg-brand-gold/30 rounded-t-md" style="height: 58%"></div>
                                    <span class="text-[10px] text-gray-400">Jan</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center gap-2">
                                    <div class="w-full bg-brand-gold/40 rounded-t-md" style="height: 50%"></div>
                                    <span class="text-[10px] text-gray-400">Feb</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center gap-2">
                                    <div class="w-full bg-brand-gold/50 rounded-t-md" style="height: 70%"></div>
                                    <span class="text-[10px] text-gray-400">Mar</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center gap-2">
                                    <div class="w-full bg-brand-gold/70 rounded-t-md" style="height: 80%"></div>
                                    <span class="text-[10px] text-gray-400">Apr</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center gap-2">
                                    <div class="w-full bg-brand-gold rounded-t-md" style="height: 92%"></div>
                                    <span class="text-[10px] text-brand-black font-medium">May</span>
                                </div>
                            </div>
                        </div>
                        <div class="dash-card">
                            <h4 class="text-xs uppercase tracking-widest text-brand-black font-medium mb-6">Service Mix
                            </h4>
                            <ul class="space-y-4 text-sm">
                                <?php 
                                $categories = [
                                    ['name' => 'Bridals & Asoebi', 'color' => 'bg-brand-gold'],
                                    ['name' => 'Suits & Dinner',   'color' => 'bg-brand-purple'],
                                    ['name' => 'African Luxury',   'color' => 'bg-brand-gold-deep'],
                                    ['name' => 'Consultancy',      'color' => 'bg-gray-700']
                                ];
                                foreach ($categories as $cat): 
                                    $p = $mix[$cat['name']]['percentage'] ?? 0;
                                ?>
                                <li>
                                    <div class="flex justify-between mb-1">
                                        <span><?= $cat['name'] ?></span>
                                        <span class="text-gray-400"><?= $p ?>%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                                        <div class="<?= $cat['color'] ?> h-1.5 rounded-full" style="width:<?= $p ?>%"></div>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="dash-card">
                        <h4 class="text-xs uppercase tracking-widest text-brand-black mb-6 font-medium">Recent Atelier
                            Activity</h4>
                        <div class="space-y-4">
                            <div
                                class="flex flex-wrap gap-3 justify-between items-center pb-4 border-b border-gray-100 text-sm">
                                <span class="font-medium text-brand-black">New Asoebi Order (Group of 5)</span>
                                <span class="text-gray-500">Abuja Atelier</span>
                                <span class="text-brand-gold italic">Just Now</span>
                            </div>
                            <div
                                class="flex flex-wrap gap-3 justify-between items-center pb-4 border-b border-gray-100 text-sm">
                                <span class="font-medium text-brand-black">Consultancy Session Completed</span>
                                <span class="text-gray-500">Virtual (London Client)</span>
                                <span class="text-gray-400">2 hours ago</span>
                            </div>
                            <div class="flex flex-wrap gap-3 justify-between items-center text-sm">
                                <span class="font-medium text-brand-black">Bridal Fitting Approved</span>
                                <span class="text-gray-500">Ebonyi Atelier</span>
                                <span class="text-gray-400">Yesterday</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Receptionist -->
                <div id="receptionist-dash" class="dash-tab hidden" role="tabpanel">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2 dash-card">
                            <div class="flex justify-between items-center mb-6 flex-wrap gap-2">
                                <h4 class="text-xs uppercase tracking-widest text-brand-black font-medium">Today's
                                    Appointments</h4>
                                <span id="today-label" class="text-xs bg-gray-100 px-3 py-1 rounded-full"></span>
                            </div>
                            <div class="space-y-4">
                                <div
                                    class="flex flex-wrap items-center p-4 border border-brand-gold/30 bg-brand-gold/5 rounded-xl gap-3">
                                    <div class="w-16 text-center border-r border-gray-200 pr-4 mr-2">
                                        <p class="text-sm font-medium">10:00</p>
                                        <p class="text-xs text-gray-500">AM</p>
                                    </div>
                                    <div class="flex-grow min-w-[160px]">
                                        <p class="text-sm font-medium text-brand-black">First Fitting · Wedding Suit</p>
                                        <p class="text-xs text-gray-500">Mr. Chinedu · Abuja Atelier</p>
                                    </div>
                                    <button
                                        class="text-xs bg-brand-black text-white px-4 py-2 rounded-lg hover:bg-brand-gold hover:text-brand-black transition-colors">Check
                                        In</button>
                                </div>
                                <div class="flex flex-wrap items-center p-4 border border-gray-100 rounded-xl gap-3">
                                    <div class="w-16 text-center border-r border-gray-100 pr-4 mr-2">
                                        <p class="text-sm font-medium">01:30</p>
                                        <p class="text-xs text-gray-500">PM</p>
                                    </div>
                                    <div class="flex-grow min-w-[160px]">
                                        <p class="text-sm font-medium text-brand-black">Initial Consultation</p>
                                        <p class="text-xs text-gray-500">Mrs. Amina · Virtual Call</p>
                                    </div>
                                    <button
                                        class="text-xs border border-gray-200 text-gray-600 px-4 py-2 rounded-lg">Pending</button>
                                </div>
                                <div class="flex flex-wrap items-center p-4 border border-gray-100 rounded-xl gap-3">
                                    <div class="w-16 text-center border-r border-gray-100 pr-4 mr-2">
                                        <p class="text-sm font-medium">04:00</p>
                                        <p class="text-xs text-gray-500">PM</p>
                                    </div>
                                    <div class="flex-grow min-w-[160px]">
                                        <p class="text-sm font-medium text-brand-black">Final Bead-Work Review</p>
                                        <p class="text-xs text-gray-500">Ms. Nneka · Ebonyi Atelier</p>
                                    </div>
                                    <button
                                        class="text-xs border border-gray-200 text-gray-600 px-4 py-2 rounded-lg">Scheduled</button>
                                </div>
                            </div>
                        </div>

                        <div class="dash-card">
                            <div class="flex justify-between items-center mb-6">
                                <h4 class="text-xs uppercase tracking-widest text-brand-black font-medium">Inbound
                                    Pipeline</h4>
                                <span id="lead-count"
                                    class="text-[10px] font-bold uppercase tracking-wider text-brand-gold bg-brand-gold/10 px-2 py-1 rounded">Live</span>
                            </div>
                            <div id="pipeline" class="space-y-4">
                                <?php if (empty($db_leads)): ?>
                                    <p class="text-xs text-gray-400 italic py-4 text-center">No active leads in pipeline.</p>
                                <?php else: ?>
                                    <?php foreach ($db_leads as $lead): 
                                        $source = $lead['source'];
                                        $stripe = ($source == 'AI Concierge') ? 'bg-brand-gold' : (($source == 'Instagram DM') ? 'bg-brand-purple' : 'bg-brand-black');
                                        $tag = ($source == 'AI Concierge') ? 'text-brand-gold bg-brand-gold/10' : (($source == 'Instagram DM') ? 'text-brand-purple bg-brand-purple/10' : 'text-brand-black bg-gray-100');
                                    ?>
                                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 relative overflow-hidden">
                                        <div class="absolute top-0 left-0 w-1 h-full <?= $stripe ?>"></div>
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded <?= $tag ?>"><?= htmlspecialchars($source) ?></span>
                                            <span class="text-xs text-gray-400"><?= date('H:i', strtotime($lead['created_at'])) ?></span>
                                        </div>
                                        <p class="text-sm font-medium text-brand-black mb-1"><?= htmlspecialchars($lead['service_interest'] ?? 'New Inquiry') ?></p>
                                        <p class="text-xs text-gray-500 mb-3">Timeline: <?= htmlspecialchars($lead['timeline'] ?? '—') ?></p>
                                        <p class="text-xs font-mono text-gray-600 mb-3"><?= htmlspecialchars($lead['phone'] ?: $lead['email'] ?: 'No Contact') ?></p>
                                        <button class="text-xs bg-white border border-gray-200 text-brand-black w-full py-2 rounded hover:border-brand-gold transition-colors">Assign & Reply</button>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Finance -->
                <div id="finance-dash" class="dash-tab hidden" role="tabpanel">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="dash-card bg-brand-black text-white">
                            <h4 class="text-xs uppercase tracking-widest text-gray-400 mb-2">Pending Invoice Approvals
                            </h4>
                            <p class="text-3xl font-serif mb-1">₦1,150,000</p>
                            <p class="text-xs text-brand-gold">Awaiting 30% final payments</p>
                        </div>
                        <div class="dash-card">
                            <h4 class="text-xs uppercase tracking-widest text-gray-400 mb-2">Material Cost (COGS) · This Month</h4>
                            <p class="text-3xl font-serif text-brand-black"><?= $stats ? naira($stats['cogs']) : '₦0' ?></p>
                            <p class="text-xs text-gray-500">Premium Fabrics &amp; Custom Beading</p>
                        </div>
                    </div>
                    <div class="dash-card overflow-x-auto">
                        <h4 class="text-xs uppercase tracking-widest text-brand-black mb-6 font-medium">Payment
                            Milestones</h4>
                        <table class="w-full text-left text-sm min-w-[520px]">
                            <thead>
                                <tr class="text-xs uppercase tracking-widest text-gray-400 border-b border-gray-100">
                                    <th class="pb-3 font-medium">Client / Project</th>
                                    <th class="pb-3 font-medium">Amount</th>
                                    <th class="pb-3 font-medium">Status</th>
                                    <th class="pb-3 font-medium text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-gray-50">
                                    <td class="py-4 font-medium text-brand-black">ILM-042 · Bridal Dress</td>
                                    <td class="py-4">₦650,000</td>
                                    <td class="py-4"><span
                                            class="bg-green-50 text-green-600 px-2 py-1 rounded-md text-xs">70%
                                            Paid</span></td>
                                    <td class="py-4 text-right"><button
                                            class="text-brand-gold text-xs font-medium hover:underline">Send
                                            Invoice</button></td>
                                </tr>
                                <tr class="border-b border-gray-50">
                                    <td class="py-4 font-medium text-brand-black">ILM-045 · Dinner Suit</td>
                                    <td class="py-4">₦250,000</td>
                                    <td class="py-4"><span
                                            class="bg-yellow-50 text-yellow-700 px-2 py-1 rounded-md text-xs">Pending
                                            Initial</span></td>
                                    <td class="py-4 text-right"><button
                                            class="text-brand-gold text-xs font-medium hover:underline">Remind</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-4 font-medium text-brand-black">ILM-047 · Asoebi (×5)</td>
                                    <td class="py-4">₦480,000</td>
                                    <td class="py-4"><span
                                            class="bg-blue-50 text-blue-600 px-2 py-1 rounded-md text-xs">Deposit
                                            Received</span></td>
                                    <td class="py-4 text-right"><button
                                            class="text-brand-gold text-xs font-medium hover:underline">Schedule</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <footer class="bg-[#FAFAFA] border-t border-gray-100 pt-16 pb-8 relative z-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
                <div>
                    <div class="flex items-center mb-6">
                        <img src="assets/img/logo.png" alt="ILLUME" class="h-6 md:h-8 w-auto mr-3">
                        <span class="font-sans text-sm tracking-[0.3em] font-light text-brand-black mt-1">ILLUME</span>
                    </div>
                    <p class="text-gray-500 text-sm font-light max-w-xs">
                        Translating African narratives into timeless, wearable art. Crafted with intention.
                    </p>
                </div>
                <div>
                    <h4 class="text-brand-black font-medium mb-6 uppercase tracking-widest text-xs">Ateliers</h4>
                    <ul class="text-gray-500 text-sm font-light space-y-3">
                        <li>Kubwa, Abuja</li>
                        <li>Abakaliki, Ebonyi State</li>
                        <li>Active in: Enugu &amp; Owerri</li>
                        <li class="text-brand-gold font-medium mt-2">Global Shipping Available</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-brand-black font-medium mb-6 uppercase tracking-widest text-xs">Connect</h4>
                    <ul class="text-gray-500 text-sm font-light space-y-3 relative z-50">
                        <li><a href="mailto:<?= htmlspecialchars($settings['email_contact']) ?>" class="hover:text-brand-gold hover:underline transition-colors cursor-pointer inline-block"><?= htmlspecialchars($settings['email_contact']) ?></a></li>
                        <li><a href="https://wa.me/<?= str_replace(['+', ' '], '', $settings['whatsapp_number']) ?>" target="_blank" rel="noopener" class="hover:text-brand-gold hover:underline transition-colors cursor-pointer inline-block"><?= htmlspecialchars($settings['whatsapp_number']) ?> (WhatsApp)</a></li>
                        <li class="pt-2">
                            <a href="https://instagram.com/<?= str_replace('@', '', $settings['instagram_handle'] ?? '') ?>" target="_blank" rel="noopener" class="inline-block border border-gray-200 px-4 py-2 rounded-full hover:border-brand-gold hover:text-brand-gold text-brand-black transition-colors text-xs">Instagram</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div
                class="text-center border-t border-gray-100 pt-8 text-xs text-gray-400 font-light uppercase tracking-widest">
                © <span id="copyright-year">2026</span> ILLUME by Light Peace Ltd. All Rights Reserved.
            </div>
        </div>
    </footer>

    <!-- Sticky mobile CTA -->
    <div class="sticky-cta">
        <div class="flex gap-2">
            <a href="https://wa.me/2349039963415"
                class="flex-1 bg-brand-black text-white text-center py-3 rounded-full text-xs uppercase tracking-widest">WhatsApp</a>
            <button onclick="openBookingModal()"
                class="flex-1 bg-brand-gold text-brand-black py-3 rounded-full text-xs uppercase tracking-widest font-medium">Book</button>
        </div>
    </div>



    <!-- Chat Widget -->
    <div class="chat-widget">
        <div id="chat-window" class="chat-window mb-4" aria-live="polite">
            <div class="chat-header">
                <span class="italic text-lg">ILLUME Concierge</span>
                <button onclick="toggleChat()" aria-label="Close chat"
                    class="text-brand-gold hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="chat-body" class="chat-body">
                <div class="chat-message msg-bot">Welcome to ILLUME. How may we illuminate your wardrobe today?</div>
                <div class="chat-options" id="initial-options">
                    <button class="chat-option-btn" onclick="handleChatChoice('Bridals & Asoebi')">Bridals &amp; Asoebi
                        Curation</button>
                    <button class="chat-option-btn" onclick="handleChatChoice('Suits & Dinner')">Bespoke Suits &amp;
                        Dinner</button>
                    <button class="chat-option-btn" onclick="handleChatChoice('Consultancy')">Fashion
                        Consultancy</button>
                </div>
            </div>
            <div class="p-3 border-t border-gray-100 bg-white">
                <form id="chat-text-form"
                    class="flex items-center bg-gray-50 rounded-full px-4 py-1.5 border border-gray-200">
                    <input id="chat-text-input" type="text" placeholder="Type a message…"
                        class="bg-transparent text-sm w-full focus:outline-none text-brand-black py-1.5" />
                    <button type="submit" aria-label="Send" class="text-brand-gold hover:text-brand-black ml-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M3.105 3.105a.75.75 0 01.815-.16l13.5 5.625a.75.75 0 010 1.385l-13.5 5.625a.75.75 0 01-1.04-.85l1.62-4.86a.75.75 0 01.59-.5l6.41-1.07-6.41-1.07a.75.75 0 01-.59-.5l-1.62-4.86a.75.75 0 01.225-.765z" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        <div class="flex justify-end">
            <button onclick="toggleChat()" aria-label="Open ILLUME concierge"
                class="w-14 h-14 bg-brand-black rounded-full flex items-center justify-center shadow-2xl hover:bg-brand-gold hover:text-brand-black text-brand-gold transition-all duration-300 border border-brand-gold/20">
                <span class="font-serif italic text-2xl">I</span>
            </button>
        </div>
    </div>

    <script>
        // Global state
        const STORE_KEY = 'illume_leads_v1';
        const chatState = { service: null, timeline: null, contact: null };

        // Helper functions
        const getLeads = () => { 
            try { 
                return JSON.parse(localStorage.getItem(STORE_KEY)) || []; 
            } catch { 
                return []; 
            } 
        };

        const escapeHtml = (s) => {
            return String(s).replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));
        };

        // ===== Navigation Functions (Global Scope) =====
        function switchView(viewId) {
            console.log('Switching to view:', viewId);
            const sections = document.querySelectorAll('.view-section');
            sections.forEach(v => {
                v.classList.remove('active');
                v.style.display = 'none';
            });
            
            const target = document.getElementById(viewId);
            if (target) {
                target.style.display = 'block';
                // Force reflow for animation
                void target.offsetWidth;
                target.classList.add('active');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const btn = document.getElementById('mobile-menu-btn');
            if (menu && btn) {
                const isHidden = menu.classList.toggle('hidden');
                btn.setAttribute('aria-expanded', !isHidden);
            }
        }

        function switchTab(tabId) {
            document.querySelectorAll('.dash-tab').forEach(el => el.classList.add('hidden'));
            const target = document.getElementById(tabId);
            if (target) target.classList.remove('hidden');

            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('border-brand-black', 'text-brand-black');
                el.classList.add('border-transparent', 'text-gray-400');
                el.setAttribute('aria-selected', 'false');
            });
            
            const btn = document.getElementById('btn-' + tabId);
            if (btn) {
                btn.classList.remove('border-transparent', 'text-gray-400');
                btn.classList.add('border-brand-black', 'text-brand-black');
                btn.setAttribute('aria-selected', 'true');
            }
        }

        // ===== Modal Functions (Global Scope) =====
        function openBookingModal(preselect) {
            const modal = document.getElementById('booking-modal');
            if (modal) {
                modal.style.setProperty('display', 'flex', 'important');
                modal.classList.add('open');
                document.getElementById('booking-form')?.classList.remove('hidden');
                document.getElementById('booking-success')?.classList.add('hidden');
                
                if (preselect) {
                    const sel = document.getElementById('booking-service');
                    if (sel) {
                        for (const opt of sel.options) {
                            if (opt.textContent.trim().toLowerCase().includes(preselect.toLowerCase())) {
                                sel.value = opt.value;
                                break;
                            }
                        }
                    }
                }
            }
        }

        function closeBookingModal() {
            const modal = document.getElementById('booking-modal');
            if (modal) {
                modal.style.display = 'none';
                modal.classList.remove('open');
            }
        }

        function openLeadMagnet() {
            const modal = document.getElementById('leadmag-modal');
            if (modal) {
                modal.style.setProperty('display', 'flex', 'important');
                modal.classList.add('open');
                document.getElementById('leadmag-form')?.classList.remove('hidden');
                document.getElementById('leadmag-success')?.classList.add('hidden');
            }
        }

        function closeLeadMagnet() {
            const modal = document.getElementById('leadmag-modal');
            if (modal) {
                modal.style.display = 'none';
                modal.classList.remove('open');
            }
        }

        function toggleChat() {
            document.getElementById('chat-window')?.classList.toggle('open');
        }

        // ===== Lead & Pipeline Functions =====
        async function saveLead(lead) {
            const leads = getLeads();
            leads.unshift({ id: 'L' + Date.now(), ts: new Date().toISOString(), ...lead });
            localStorage.setItem(STORE_KEY, JSON.stringify(leads.slice(0, 25)));
            renderPipeline();

            try {
                const response = await fetch('save_lead.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(lead)
                });
                const result = await response.json();
                console.log('Server response:', result);
            } catch (err) {
                console.warn('Backend sync failed:', err);
            }
        }

        function renderPipeline() {
            const container = document.getElementById('pipeline');
            if (!container) return;
            
            const leads = getLeads();
            container.innerHTML = '';

            leads.forEach((l, idx) => {
                const isConcierge = l.source === 'AI Concierge';
                const isIG = l.source === 'IG Lead Magnet';
                const stripe = isConcierge ? 'bg-brand-gold' : (isIG ? 'bg-brand-purple' : 'bg-brand-black');
                const tagColor = isConcierge ? 'text-brand-gold bg-brand-gold/10' : (isIG ? 'text-brand-purple bg-brand-purple/10' : 'text-brand-black bg-gray-100');
                const minsAgo = Math.max(0, Math.round((Date.now() - new Date(l.ts).getTime()) / 60000));
                const ago = minsAgo < 1 ? 'Just now' : `${minsAgo} mins ago`;
                
                const card = document.createElement('div');
                card.className = 'p-4 bg-gray-50 rounded-lg border border-gray-100 relative overflow-hidden' + (idx === 0 ? ' new-lead' : '');
                card.innerHTML = `
                    <div class="absolute top-0 left-0 w-1 h-full ${stripe}"></div>
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded ${tagColor}">${l.source}</span>
                        <span class="text-xs text-gray-400">${ago}</span>
                    </div>
                    <p class="text-sm font-medium text-brand-black mb-1">${escapeHtml(l.service || 'Inquiry')}</p>
                    <p class="text-xs text-gray-500 mb-3">Timeline: ${escapeHtml(l.timeline || '—')}</p>
                    <p class="text-xs font-mono text-gray-600 mb-3">${escapeHtml(l.contact || 'No contact provided')}</p>
                    <button class="text-xs bg-white border border-gray-200 text-brand-black w-full py-2 rounded hover:border-brand-gold transition-colors">Assign & Reply</button>
                `;
                container.appendChild(card);
            });

            container.insertAdjacentHTML('beforeend', `
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 relative overflow-hidden opacity-60">
                    <div class="absolute top-0 left-0 w-1 h-full bg-brand-gold"></div>
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-brand-gold bg-brand-gold/10 px-2 py-1 rounded">AI Concierge</span>
                        <span class="text-xs text-gray-400">Sample</span>
                    </div>
                    <p class="text-sm font-medium text-brand-black mb-1">Bridal Dress Inquiry</p>
                    <p class="text-xs text-gray-500 mb-3">Timeline: Late 2026</p>
                    <button class="text-xs bg-white border border-gray-200 text-brand-black w-full py-2 rounded hover:border-brand-gold transition-colors">Assign</button>
                </div>
            `);

            const countLabel = document.getElementById('lead-count');
            if (countLabel) countLabel.textContent = leads.length > 0 ? `${leads.length} live` : 'Live';
        }

        // ===== Chat Functions =====
        function appendMessage(role, html) {
            const body = document.getElementById('chat-body');
            if (!body) return;
            const div = document.createElement('div');
            div.className = 'chat-message ' + (role === 'user' ? 'msg-user' : 'msg-bot');
            div.innerHTML = html;
            body.appendChild(div);
            body.scrollTop = body.scrollHeight;
            return div;
        }

        function appendTyping() {
            const body = document.getElementById('chat-body');
            if (!body) return;
            const div = document.createElement('div');
            div.className = 'chat-message msg-bot';
            div.innerHTML = '<div class="typing"><span></span><span></span><span></span></div>';
            body.appendChild(div);
            body.scrollTop = body.scrollHeight;
            return div;
        }

        function appendOptions(options) {
            const body = document.getElementById('chat-body');
            if (!body) return;
            const wrap = document.createElement('div');
            wrap.className = 'chat-options';
            options.forEach(opt => {
                const btn = document.createElement('button');
                btn.className = 'chat-option-btn';
                btn.textContent = opt.label;
                btn.onclick = () => { wrap.remove(); opt.action(); };
                wrap.appendChild(btn);
            });
            body.appendChild(wrap);
            body.scrollTop = body.scrollHeight;
            return wrap;
        }

        function handleChatChoice(choice) {
            chatState.service = choice;
            document.getElementById('initial-options')?.remove();
            appendMessage('user', choice);

            const typing = appendTyping();
            setTimeout(() => {
                typing?.remove();
                if (choice === 'Consultancy') {
                    appendMessage('bot', 'Excellent. Our consultancy spans visual identity and wardrobe architecture. What is the focus?');
                    appendOptions([
                        { label: 'Visual Identity', action: () => askTimeline('Visual Identity') },
                        { label: 'Wardrobe Strategy', action: () => askTimeline('Wardrobe Strategy') },
                    ]);
                } else {
                    appendMessage('bot', `An exquisite choice. What is your timeline for <strong>${choice}</strong>?`);
                    appendOptions([
                        { label: 'Less than 3 months', action: () => askContact('Less than 3 months') },
                        { label: '3 — 6 months', action: () => askContact('3 — 6 months') },
                        { label: 'Flexible', action: () => askContact('Flexible') },
                    ]);
                }
            }, 700);
        }

        function askTimeline(sub) {
            chatState.service += ' · ' + sub;
            appendMessage('user', sub);
            const t = appendTyping();
            setTimeout(() => {
                t?.remove();
                appendMessage('bot', 'When would you like to begin?');
                appendOptions([
                    { label: 'Immediately', action: () => askContact('Immediately') },
                    { label: 'Within a month', action: () => askContact('Within a month') },
                ]);
            }, 600);
        }

        function askContact(timeline) {
            chatState.timeline = timeline;
            appendMessage('user', timeline);
            const t = appendTyping();
            setTimeout(() => {
                t?.remove();
                appendMessage('bot', 'Please share your WhatsApp number or email below, and we will reach out personally.');
                document.getElementById('chat-text-input')?.focus();
            }, 600);
        }

        // Initialization & DOM Event Listeners
        document.addEventListener('DOMContentLoaded', () => {
            // Mobile Menu
            document.getElementById('mobile-menu-btn')?.addEventListener('click', toggleMobileMenu);

            // Chat Form
            document.getElementById('chat-text-form')?.addEventListener('submit', (e) => {
                e.preventDefault();
                const input = document.getElementById('chat-text-input');
                const val = input?.value.trim();
                if (!val) return;
                input.value = '';
                appendMessage('user', escapeHtml(val));

                if (!chatState.service) {
                    const t = appendTyping();
                    setTimeout(() => {
                        t?.remove();
                        appendMessage('bot', 'Thank you. What is your primary interest?');
                        appendOptions([
                            { label: 'Bridals & Asoebi', action: () => handleChatChoice('Bridals & Asoebi') },
                            { label: 'Suits & Dinner', action: () => handleChatChoice('Suits & Dinner') },
                        ]);
                    }, 600);
                    return;
                }

                chatState.contact = val;
                const t = appendTyping();
                setTimeout(() => {
                    t?.remove();
                    appendMessage('bot', 'Perfectly received. We will reach out personally within the day.');
                    saveLead({
                        source: 'AI Concierge',
                        service: chatState.service,
                        timeline: chatState.timeline,
                        contact: chatState.contact,
                    });
                }, 800);
            });

            // Booking Form
            document.getElementById('booking-form')?.addEventListener('submit', (e) => {
                e.preventDefault();
                const fd = new FormData(e.target);
                saveLead({
                    source: 'Website Booking',
                    service: fd.get('service'),
                    timeline: fd.get('timeline'),
                    contact: `${fd.get('name')} · ${fd.get('email')} · ${fd.get('phone')}`,
                    notes: fd.get('notes') || '',
                });
                e.target.classList.add('hidden');
                document.getElementById('booking-success')?.classList.remove('hidden');
            });

            // Lead Magnet Form
            document.getElementById('leadmag-form')?.addEventListener('submit', (e) => {
                e.preventDefault();
                const fd = new FormData(e.target);
                saveLead({
                    source: 'IG Lead Magnet',
                    service: 'Downloaded Silhouette Guide',
                    contact: `${fd.get('name')} · ${fd.get('email')}`,
                });
                e.target.classList.add('hidden');
                document.getElementById('leadmag-success')?.classList.remove('hidden');
            });

            // Modal Clicks (backdrop close)
            ['booking-modal', 'leadmag-modal'].forEach(id => {
                document.getElementById(id)?.addEventListener('click', (e) => {
                    if (e.target.id === id) {
                        id === 'booking-modal' ? closeBookingModal() : closeLeadMagnet();
                    }
                });
            });

            // Keyboard Shortcuts
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeBookingModal();
                    closeLeadMagnet();
                    document.getElementById('chat-window')?.classList.remove('open');
                }
            });

            // Scroll Listener for Nav
            window.addEventListener('scroll', () => {
                const nav = document.getElementById('topnav');
                if (nav) {
                    if (window.scrollY > 20) {
                        nav.style.background = 'rgba(255,255,255,0.95)';
                        nav.style.boxShadow = '0 4px 30px rgba(0,0,0,0.03)';
                    } else {
                        nav.style.background = 'rgba(255,255,255,0.85)';
                        nav.style.boxShadow = 'none';
                    }
                }
            });

            // Set Copyright Year
            const copy = document.getElementById('copyright-year');
            if (copy) copy.textContent = new Date().getFullYear();

            // Initial Pipeline Render
            renderPipeline();

            // Set up explicit listeners for Lead Magnet & Booking
            document.getElementById('hero-guide-btn')?.addEventListener('click', openLeadMagnet);
            document.getElementById('strip-guide-btn')?.addEventListener('click', openLeadMagnet);
            document.getElementById('hero-book-btn')?.addEventListener('click', (e) => openBookingModal());
        });
    </script>
</body>

</html>