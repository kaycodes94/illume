<?php
/**
 * ILLUME — Founder Strategic Workspace
 * High-fidelity dashboard for Ikedichukwu Peace (Light Mode)
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'illume_db';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch Latest Stats
$revenue_res = $conn->query("SELECT * FROM revenue_snapshots ORDER BY year DESC, month DESC LIMIT 1");
$stats = $revenue_res ? $revenue_res->fetch_assoc() : null;

// Fetch Service Mix
$mix = [];
if ($stats) {
    $mix_res = $conn->query("SELECT * FROM revenue_category_breakdown WHERE snapshot_id = " . $stats['id']);
    while ($row = $mix_res->fetch_assoc()) {
        $mix[$row['category']] = $row;
    }
}

// Fetch Active Orders Count
$orders_res = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status != 'Delivered'");
$active_orders = $orders_res ? $orders_res->fetch_assoc()['total'] : 0;

// Fetch Recent Activity
$activity_res = $conn->query("SELECT * FROM activity_logs ORDER BY logged_at DESC LIMIT 5");
$activities = [];
while ($row = $activity_res ? $activity_res->fetch_assoc() : null) {
    $activities[] = $row;
}

function naira($val) {
    return '₦' . number_format((float)$val);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Founder Workspace — ILLUME</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            black: '#1a1a1a',
                            gold: '#D4AF37',
                            'gold-deep': '#B8860B',
                            purple: '#301934',
                            gray: '#F9F9F9',
                            border: 'rgba(0, 0, 0, 0.05)'
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    },
                },
            },
        };
    </script>
    <style>
        body { background-color: #fdfdfd; color: #1a1a1a; }
        .card { background: #fff; border: 1px solid rgba(0, 0, 0, 0.05); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02); }
        .gold-gradient { background: linear-gradient(135deg, #B8860B 0%, #D4AF37 50%, #B8860B 100%); }
        .shimmer { background: linear-gradient(90deg, transparent 0%, rgba(212, 175, 55, 0.03) 50%, transparent 100%); background-size: 200% 100%; animation: shimmer 3s infinite; }
        @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
    </style>
</head>
<body class="antialiased font-sans min-h-screen">

    <!-- Navigation -->
    <nav class="border-b border-brand-border px-8 py-4 flex justify-between items-center sticky top-0 z-50 bg-white/80 backdrop-blur-md">
        <div class="flex items-center gap-4">
            <span class="font-serif italic text-2xl text-brand-gold">I</span>
            <div class="h-6 w-[1px] bg-brand-border"></div>
            <span class="text-xs uppercase tracking-widest font-bold text-gray-400">Strategic Workspace</span>
        </div>
        <div class="flex items-center gap-6">
            <a href="index.php" class="text-[10px] uppercase tracking-widest text-gray-500 hover:text-brand-gold transition-colors">Return to Site</a>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest leading-none mb-1">Founder</p>
                    <p class="text-xs font-bold text-brand-black">Ikedichukwu Peace</p>
                </div>
                <div class="w-8 h-8 rounded-full border border-brand-gold/30 bg-brand-gold/10 flex items-center justify-center font-serif italic text-brand-gold">P</div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-8 lg:p-12">
        <!-- Header -->
        <header class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="font-serif text-4xl md:text-5xl text-brand-black mb-2">Morning, <span class="italic text-brand-gold">Peace.</span></h1>
                <p class="text-gray-400 font-light text-sm tracking-wide uppercase tracking-[0.15em]">Enterprise Overview · <?= date('F Y') ?></p>
            </div>
            <div class="bg-brand-black text-white px-6 py-3 rounded-2xl flex items-center gap-4 shadow-xl">
                <div class="text-right border-r border-white/10 pr-4">
                    <p class="text-[9px] uppercase tracking-widest text-gray-400 mb-1">Active Bespoke</p>
                    <p class="text-lg font-serif italic text-brand-gold"><?= $active_orders ?></p>
                </div>
                <a href="orders_manage.php" class="text-[10px] uppercase tracking-widest font-bold hover:text-brand-gold transition-colors">View All Orders</a>
            </div>
        </header>

        <!-- Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <div class="card p-8 rounded-[32px] relative overflow-hidden">
                <div class="shimmer absolute inset-0 pointer-events-none"></div>
                <p class="text-[10px] uppercase tracking-[0.2em] text-gray-400 mb-6 font-bold">Gross Revenue</p>
                <h2 class="text-4xl font-serif text-brand-black mb-3"><?= $stats ? naira($stats['gross_revenue']) : '₦0' ?></h2>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold text-green-500 uppercase tracking-widest">+12% Growth</span>
                </div>
            </div>
            <div class="card p-8 rounded-[32px]">
                <p class="text-[10px] uppercase tracking-[0.2em] text-gray-400 mb-6 font-bold">Acquisition Cost</p>
                <h2 class="text-4xl font-serif text-brand-black mb-3"><?= ($stats && isset($stats['cac'])) ? naira($stats['cac']) : '₦0' ?></h2>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold text-brand-gold uppercase tracking-widest">Optimized</span>
                </div>
            </div>
            <div class="card p-8 rounded-[32px]">
                <p class="text-[10px] uppercase tracking-[0.2em] text-gray-400 mb-6 font-bold">Lead Conversion</p>
                <h2 class="text-4xl font-serif text-brand-black mb-3"><?= ($stats && isset($stats['conversion_rate'])) ? $stats['conversion_rate'] . '%' : '0%' ?></h2>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold text-green-500 uppercase tracking-widest">In-Target</span>
                </div>
            </div>
            <div class="card p-8 rounded-[32px]">
                <p class="text-[10px] uppercase tracking-[0.2em] text-gray-400 mb-6 font-bold">Profit Margin</p>
                <h2 class="text-4xl font-serif text-brand-black mb-3">72%</h2>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold text-brand-purple uppercase tracking-widest">Elite Tier</span>
                </div>
            </div>
        </div>

        <!-- Main Charts Area -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            <div class="lg:col-span-2 card p-10 rounded-[40px]">
                <div class="flex justify-between items-center mb-10">
                    <div>
                        <h3 class="text-[10px] uppercase tracking-[0.2em] text-brand-gold font-bold mb-2">Revenue Trajectory</h3>
                        <p class="text-brand-black font-serif text-2xl">H1 Performance</p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="alert('Viewing detailed Monthly revenue breakdown.')" class="text-[9px] uppercase tracking-widest px-4 py-2 rounded-xl bg-brand-black text-white font-bold">Monthly</button>
                        <button onclick="alert('Quarterly reporting is currently being synthesized.')" class="text-[9px] uppercase tracking-widest px-4 py-2 rounded-xl border border-brand-border text-gray-400 font-bold">Quarterly</button>
                    </div>
                </div>
                <div class="h-[350px]">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="card p-10 rounded-[40px]">
                <h3 class="text-[10px] uppercase tracking-[0.2em] text-brand-gold font-bold mb-10">Service Portfolio</h3>
                <div class="space-y-8">
                    <?php 
                    $categories = [
                        ['name' => 'Bridals & Asoebi', 'color' => '#D4AF37', 'icon' => 'B'],
                        ['name' => 'Suits & Dinner',   'color' => '#301934', 'icon' => 'S'],
                        ['name' => 'African Luxury',   'color' => '#B8860B', 'icon' => 'A'],
                        ['name' => 'Consultancy',      'color' => '#888888', 'icon' => 'C']
                    ];
                    foreach ($categories as $cat): 
                        $p = $mix[$cat['name']]['percentage'] ?? 0;
                        $amt = $mix[$cat['name']]['amount'] ?? 0;
                    ?>
                    <div>
                        <div class="flex justify-between items-end mb-3">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-[11px] font-serif border border-brand-border" style="color: <?= $cat['color'] ?>; background: <?= $cat['color'] ?>10"><?= $cat['icon'] ?></div>
                                <div>
                                    <p class="text-xs font-bold text-brand-black uppercase tracking-widest"><?= $cat['name'] ?></p>
                                    <p class="text-[10px] text-gray-400"><?= naira($amt) ?></p>
                                </div>
                            </div>
                            <span class="text-sm font-serif italic text-brand-gold"><?= $p ?>%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                            <div class="h-full rounded-full" style="width: <?= $p ?>%; background-color: <?= $cat['color'] ?>;"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Activity & Ateliers -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="card p-10 rounded-[40px]">
                <h3 class="text-[10px] uppercase tracking-[0.2em] text-brand-gold font-bold mb-10">Live Activity Feed</h3>
                <div class="space-y-8">
                    <?php if (empty($activities)): ?>
                        <p class="text-xs text-gray-400 italic py-4">No recent signals detected.</p>
                    <?php else: ?>
                        <?php foreach ($activities as $log): ?>
                        <div class="flex gap-6 relative group">
                            <div class="w-2.5 h-2.5 rounded-full bg-brand-gold mt-1.5 shrink-0 group-hover:scale-150 transition-transform"></div>
                            <div class="pb-8 border-l border-brand-border pl-8 -ml-[13px]">
                                <p class="text-sm font-bold text-brand-black mb-1"><?= htmlspecialchars($log['action']) ?></p>
                                <p class="text-xs text-gray-400 mb-3"><?= htmlspecialchars($log['details']) ?></p>
                                <span class="text-[9px] text-brand-gold font-bold uppercase tracking-[0.2em] bg-brand-gold/5 px-2 py-1 rounded-md"><?= date('H:i', strtotime($log['logged_at'])) ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card p-10 rounded-[40px] relative overflow-hidden bg-brand-black text-white">
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-brand-gold/10 rounded-full blur-[100px]"></div>
                <h3 class="text-[10px] uppercase tracking-[0.2em] text-brand-gold font-bold mb-10">Regional Atelier Load</h3>
                <div class="space-y-10">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-base font-bold text-white mb-1 uppercase tracking-widest">Abuja Hub</p>
                            <p class="text-xs text-gray-500">Managing Bridal Intensive Peak</p>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-serif text-brand-gold italic">85%</p>
                            <p class="text-[10px] text-yellow-500 uppercase tracking-widest mt-1 font-bold">Critical Load</p>
                        </div>
                    </div>
                    <div class="flex justify-between items-center border-t border-white/10 pt-10">
                        <div>
                            <p class="text-base font-bold text-white mb-1 uppercase tracking-widest">Ebonyi Studio</p>
                            <p class="text-xs text-gray-500">Ready for African Luxury Intake</p>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-serif text-white italic">42%</p>
                            <p class="text-[10px] text-green-500 uppercase tracking-widest mt-1 font-bold">High Elasticity</p>
                        </div>
                    </div>
                    <div class="bg-white/5 border border-white/10 p-6 rounded-2xl mt-10">
                        <p class="text-[10px] uppercase tracking-[0.2em] text-brand-gold mb-3 font-bold">Actionable Insight</p>
                        <p class="text-xs text-gray-400 leading-relaxed italic">"Divert new suits and dinner consults to Ebonyi virtual desks to preserve Abuja's capacity for high-margin bridal fittings."</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Chart.js Configuration (Optimized for Light Mode)
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(212, 175, 55, 0.15)');
        gradient.addColorStop(1, 'rgba(212, 175, 55, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Revenue (M)',
                    data: [1.2, 1.8, 1.5, 2.1, 2.8, 3.2],
                    borderColor: '#D4AF37',
                    borderWidth: 4,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#FFF',
                    pointBorderColor: '#D4AF37',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.03)', borderColor: 'transparent' },
                        ticks: { color: '#AAA', font: { size: 11, weight: '600' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#AAA', font: { size: 11, weight: '600' } }
                    }
                }
            }
        });
    </script>
</body>
</html>
