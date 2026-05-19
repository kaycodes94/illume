<?php
/**
 * ILLUME — Clients Directory
 * Central database for bespoke clientele
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

// Handle New Client
if (isset($_POST['action']) && $_POST['action'] === 'new_client') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $conn->query("INSERT INTO clients (full_name, email, phone) VALUES ('$name', '$email', '$phone')");
    header("Location: clients_manage.php?status=created");
    exit;
}

// Fetch All Clients
$sql = "SELECT c.*, (SELECT COUNT(*) FROM orders WHERE client_id = c.id) as order_count FROM clients c ORDER BY c.full_name ASC";
$res = $conn->query($sql);
$clients = [];
while ($row = $res ? $res->fetch_assoc() : null) {
    $clients[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients Directory — ILLUME</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            black: '#1a1a1a',
                            gold: '#D4AF37',
                            gray: '#F9F9F9',
                            border: 'rgba(0, 0, 0, 0.05)'
                        },
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'], serif: ['Playfair Display', 'serif'] },
                },
            },
        };
    </script>
    <style>
        .sidebar { background: #000; color: #fff; }
        .nav-link { transition: all 0.3s ease; }
        .nav-link.active { background: #D4AF37; color: #000; }
        .avatar { width: 40px; height: 40px; border-radius: 999px; background: #f0f0f0; display: flex; items-center; justify-center; font-serif; italic; color: #D4AF37; font-size: 14px; border: 1px solid rgba(0,0,0,0.05); }
    </style>
</head>
<body class="antialiased font-sans bg-brand-gray min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 sidebar hidden lg:flex flex-col p-6 sticky top-0 h-screen">
        <div class="mb-12 flex items-center gap-3">
            <img src="assets/img/logo.png" alt="ILLUME Logo" class="h-8 w-auto">
            <span class="text-sm tracking-[0.3em] font-light">ILLUME</span>
        </div>
        <nav class="flex-grow space-y-2">
            <a href="receptionist_dash.php" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white hover:bg-white/5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16" /></svg>
                Front Desk
            </a>
            <a href="appointments_manage.php" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white hover:bg-white/5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                Appointments
            </a>
            <a href="clients_manage.php" class="nav-link active flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                Clients
            </a>
        </nav>
        <div class="mt-auto pt-6 border-t border-white/10">
            <a href="index.php" class="text-xs text-gray-500 hover:text-brand-gold flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Back to Site
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow p-8 lg:p-12">
        <header class="flex justify-between items-end mb-12">
            <div>
                <h1 class="text-3xl font-serif text-brand-black">Clients</h1>
                <p class="text-xs text-gray-400 uppercase tracking-widest mt-2">Bespoke House Directory</p>
            </div>
            <button onclick="openClientModal()" class="bg-brand-black text-white px-8 py-3 rounded-full text-xs uppercase tracking-widest font-bold hover:bg-brand-gold hover:text-brand-black transition-all shadow-xl">Add New Client</button>
        </header>

        <div class="bg-white rounded-[40px] border border-brand-border shadow-sm overflow-hidden">
            <div class="px-10 py-8 border-b border-gray-50 flex justify-between items-center">
                <div class="relative w-full max-w-md">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" placeholder="Search by name or email..." class="w-full bg-gray-50 border-none rounded-2xl pl-12 pr-6 py-3 text-sm focus:ring-1 ring-brand-gold outline-none">
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">Total Clients:</span>
                    <span class="text-sm font-serif italic text-brand-gold"><?= count($clients) ?></span>
                </div>
            </div>

            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-brand-border">
                        <th class="px-10 py-5 text-[10px] uppercase tracking-widest font-bold text-gray-400">Identity</th>
                        <th class="px-10 py-5 text-[10px] uppercase tracking-widest font-bold text-gray-400">Contact Information</th>
                        <th class="px-10 py-5 text-[10px] uppercase tracking-widest font-bold text-gray-400">Portfolio</th>
                        <th class="px-10 py-5 text-[10px] uppercase tracking-widest font-bold text-gray-400">Status</th>
                        <th class="px-10 py-5 text-[10px] uppercase tracking-widest font-bold text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if (empty($clients)): ?>
                        <tr><td colspan="5" class="px-10 py-16 text-center text-gray-400 italic">No clients registered in the house.</td></tr>
                    <?php else: ?>
                        <?php foreach ($clients as $c): ?>
                        <tr class="hover:bg-brand-gray/50 transition-colors group">
                            <td class="px-10 py-8">
                                <div class="flex items-center gap-4">
                                    <div class="avatar"><?= substr($c['full_name'], 0, 1) ?></div>
                                    <p class="text-sm font-bold text-brand-black"><?= htmlspecialchars($c['full_name']) ?></p>
                                </div>
                            </td>
                            <td class="px-10 py-8">
                                <p class="text-xs text-brand-black mb-1"><?= htmlspecialchars($c['email'] ?: 'No email') ?></p>
                                <p class="text-[10px] text-gray-400 uppercase tracking-widest"><?= htmlspecialchars($c['phone'] ?: 'No phone') ?></p>
                            </td>
                            <td class="px-10 py-8">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] bg-brand-gold/10 text-brand-gold px-2 py-0.5 rounded font-bold uppercase tracking-wider"><?= $c['order_count'] ?> Orders</span>
                                </div>
                            </td>
                            <td class="px-10 py-8">
                                <span class="w-2 h-2 rounded-full bg-green-500 inline-block mr-2"></span>
                                <span class="text-[10px] uppercase tracking-widest text-gray-500 font-bold">Active</span>
                            </td>
                            <td class="px-10 py-8">
                                <div class="flex items-center gap-4 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button onclick="alert('Viewing full profile...')" class="text-[10px] uppercase tracking-widest font-bold text-brand-gold hover:text-brand-black">Profile</button>
                                    <button onclick="alert('Creating new order...')" class="text-[10px] uppercase tracking-widest font-bold text-brand-black hover:text-brand-gold">Invoice</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- New Client Modal -->
    <div id="client-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-6">
        <div class="bg-white w-full max-w-md rounded-[32px] p-10 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-brand-gold"></div>
            <button onclick="closeClientModal()" class="absolute top-6 right-6 text-gray-400 hover:text-brand-black">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <h2 class="font-serif text-2xl mb-2">Register Client</h2>
            <p class="text-xs text-gray-400 uppercase tracking-widest mb-8">Onboarding Portal</p>
            <form method="POST" class="space-y-6">
                <input type="hidden" name="action" value="new_client">
                <div>
                    <label class="text-[10px] uppercase tracking-widest text-gray-400 block mb-2 font-bold ml-1">Full Name</label>
                    <input type="text" name="name" required class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-4 text-sm focus:border-brand-gold outline-none transition-all">
                </div>
                <div>
                    <label class="text-[10px] uppercase tracking-widest text-gray-400 block mb-2 font-bold ml-1">Email Address</label>
                    <input type="email" name="email" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-4 text-sm focus:border-brand-gold outline-none transition-all">
                </div>
                <div>
                    <label class="text-[10px] uppercase tracking-widest text-gray-400 block mb-2 font-bold ml-1">Phone Number</label>
                    <input type="text" name="phone" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-4 text-sm focus:border-brand-gold outline-none transition-all">
                </div>
                <div class="pt-4">
                    <button type="submit" class="w-full bg-brand-black text-white py-4 rounded-2xl text-[10px] uppercase tracking-[0.2em] font-bold hover:bg-brand-gold hover:text-brand-black transition-all">Complete Registration</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openClientModal() { document.getElementById('client-modal').style.display = 'flex'; }
        function closeClientModal() { document.getElementById('client-modal').style.display = 'none'; }
    </script>
</body>
</html>
