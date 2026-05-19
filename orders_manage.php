<?php
/**
 * ILLUME — Bespoke Order Management
 * Strategic oversight of active commissions
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'illume_db';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch All Orders
$sql = "SELECT o.*, c.full_name as client_name FROM orders o JOIN clients c ON o.client_id = c.id ORDER BY o.created_at DESC";
$res = $conn->query($sql);
$orders = [];
while ($row = $res ? $res->fetch_assoc() : null) {
    $orders[] = $row;
}

function naira($val)
{
    return '₦' . number_format((float) $val);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management — ILLUME</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            black: '#1a1a1a',
                            gold: '#D4AF37',
                            border: 'rgba(0, 0, 0, 0.05)'
                        },
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'], serif: ['Playfair Display', 'serif'] },
                },
            },
        };
    </script>
    <style>
        body {
            background-color: #fdfdfd;
        }

        .card {
            background: #fff;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }

        .status-pill {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            padding: 4px 12px;
            border-radius: 999px;
        }
    </style>
</head>

<body class="antialiased font-sans min-h-screen pb-20">

    <!-- Navigation -->
    <nav
        class="border-b border-brand-border px-8 py-4 flex justify-between items-center sticky top-0 z-50 bg-white/80 backdrop-blur-md">
        <div class="flex items-center gap-4">
            <a href="founder_dash.php"><img src="assets/img/logo.png" alt="ILLUME Logo" class="h-6 w-auto"></a>
            <div class="h-6 w-[1px] bg-brand-border"></div>
            <span class="text-xs uppercase tracking-widest font-bold text-gray-400">Order Management</span>
        </div>
        <div class="flex items-center gap-6">
            <a href="founder_dash.php"
                class="text-[10px] uppercase tracking-widest text-gray-500 hover:text-brand-gold transition-colors font-bold">Dashboard</a>
            <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 rounded-full border border-brand-gold/30 bg-brand-gold/10 flex items-center justify-center font-serif italic text-brand-gold">
                    P</div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-8 lg:p-12">
        <header class="mb-12 flex justify-between items-end">
            <div>
                <h1 class="text-4xl font-serif text-brand-black mb-2">Bespoke Orders</h1>
                <p class="text-xs text-gray-400 uppercase tracking-[0.2em] font-medium">Strategic Commission Tracking
                </p>
            </div>
            <div
                class="bg-brand-black text-white px-6 py-2.5 rounded-full text-[10px] uppercase tracking-widest font-bold">
                <?= count($orders) ?> Total Commissions
            </div>
        </header>

        <div class="grid grid-cols-1 gap-6">
            <?php if (empty($orders)): ?>
                <div class="card p-20 text-center rounded-[40px]">
                    <p class="text-gray-400 italic font-light">No active commissions currently logged in the house.</p>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $o):
                    $status_color = ($o['status'] == 'Delivered') ? 'bg-green-50 text-green-600' : (($o['status'] == 'Cancelled') ? 'bg-red-50 text-red-500' : 'bg-brand-gold/10 text-brand-gold');
                    $paid_pct = ($o['total_amount'] > 0) ? round(($o['deposit_paid'] / $o['total_amount']) * 100) : 0;
                    ?>
                    <div class="card p-8 rounded-[40px] hover:shadow-xl transition-all group">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                            <!-- Identity -->
                            <div class="flex items-center gap-6">
                                <div
                                    class="w-16 h-16 rounded-3xl bg-gray-50 flex items-center justify-center border border-brand-border">
                                    <span
                                        class="font-serif italic text-xl text-brand-gold"><?= substr($o['order_number'], 0, 1) ?></span>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase tracking-widest text-brand-gold font-bold mb-1">
                                        <?= htmlspecialchars($o['order_number']) ?></p>
                                    <h3 class="text-xl font-serif text-brand-black mb-1">
                                        <?= htmlspecialchars($o['client_name']) ?></h3>
                                    <p class="text-xs text-gray-400 uppercase tracking-widest">
                                        <?= htmlspecialchars($o['service_type']) ?></p>
                                </div>
                            </div>

                            <!-- Financials -->
                            <div class="flex flex-col lg:flex-row gap-12">
                                <div>
                                    <p class="text-[9px] uppercase tracking-widest text-gray-400 mb-2 font-bold">Commission
                                        Value</p>
                                    <p class="text-lg font-bold text-brand-black"><?= naira($o['total_amount']) ?></p>
                                </div>
                                <div class="w-48">
                                    <div class="flex justify-between items-center mb-2">
                                        <p class="text-[9px] uppercase tracking-widest text-gray-400 font-bold">Funding</p>
                                        <span class="text-[10px] font-bold text-brand-gold"><?= $paid_pct ?>%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1">
                                        <div class="h-1 rounded-full bg-brand-gold" style="width: <?= $paid_pct ?>%"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status & Action -->
                            <div class="flex items-center gap-8">
                                <span class="status-pill <?= $status_color ?>"><?= $o['status'] ?></span>
                                <button onclick="alert('Viewing production specs...')"
                                    class="w-12 h-12 rounded-2xl border border-brand-border flex items-center justify-center text-gray-400 hover:text-brand-black hover:border-brand-black transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

</body>

</html>