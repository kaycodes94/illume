<?php
/**
 * ILLUME — Appointments Management
 * Full-scale scheduling control center
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

// Handle Cancel/Delete
if (isset($_GET['cancel'])) {
    $id = intval($_GET['cancel']);
    $conn->query("UPDATE appointments SET status = 'Cancelled' WHERE id = $id");
    header("Location: appointments_manage.php?status=cancelled");
    exit;
}

// Fetch All Appointments
$filter = isset($_GET['atelier']) ? "WHERE atelier_location = '" . $conn->real_escape_string($_GET['atelier']) . "'" : "";
$sql = "SELECT * FROM appointments $filter ORDER BY appointment_date DESC, appointment_time DESC";
$res = $conn->query($sql);
$appointments = [];
while ($row = $res ? $res->fetch_assoc() : null) {
    $appointments[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments Management — ILLUME</title>
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
        .status-badge { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; padding: 4px 12px; border-radius: 999px; }
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
            <a href="appointments_manage.php" class="nav-link active flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                Appointments
            </a>
            <a href="clients_manage.php" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white hover:bg-white/5">
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
                <h1 class="text-3xl font-serif text-brand-black">Appointments</h1>
                <p class="text-xs text-gray-400 uppercase tracking-widest mt-2">Bespoke Fitting & Consultation Schedule</p>
            </div>
            <div class="flex gap-4">
                <a href="appointments_manage.php" class="px-4 py-2 text-[10px] font-bold uppercase tracking-widest border border-brand-border rounded-lg bg-white">All</a>
                <a href="appointments_manage.php?atelier=Abuja Atelier" class="px-4 py-2 text-[10px] font-bold uppercase tracking-widest border border-brand-border rounded-lg bg-white">Abuja</a>
                <a href="appointments_manage.php?atelier=Ebonyi Atelier" class="px-4 py-2 text-[10px] font-bold uppercase tracking-widest border border-brand-border rounded-lg bg-white">Ebonyi</a>
            </div>
        </header>

        <div class="bg-white rounded-[32px] border border-brand-border shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-brand-border">
                        <th class="px-8 py-5 text-[10px] uppercase tracking-widest font-bold text-gray-400">Date & Time</th>
                        <th class="px-8 py-5 text-[10px] uppercase tracking-widest font-bold text-gray-400">Client</th>
                        <th class="px-8 py-5 text-[10px] uppercase tracking-widest font-bold text-gray-400">Service Type</th>
                        <th class="px-8 py-5 text-[10px] uppercase tracking-widest font-bold text-gray-400">Location</th>
                        <th class="px-8 py-5 text-[10px] uppercase tracking-widest font-bold text-gray-400">Status</th>
                        <th class="px-8 py-5 text-[10px] uppercase tracking-widest font-bold text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if (empty($appointments)): ?>
                        <tr><td colspan="6" class="px-8 py-12 text-center text-gray-400 italic">No appointments found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($appointments as $app): 
                            $status_class = ($app['status'] == 'Completed') ? 'bg-green-50 text-green-600' : (($app['status'] == 'Cancelled') ? 'bg-red-50 text-red-500' : 'bg-brand-gold/10 text-brand-gold');
                        ?>
                        <tr class="hover:bg-brand-gray/50 transition-colors group">
                            <td class="px-8 py-6">
                                <p class="text-sm font-bold text-brand-black"><?= date('M j, Y', strtotime($app['appointment_date'])) ?></p>
                                <p class="text-[10px] text-gray-400 uppercase"><?= date('h:i A', strtotime($app['appointment_time'])) ?></p>
                            </td>
                            <td class="px-8 py-6 text-sm font-medium text-brand-black"><?= htmlspecialchars($app['client_name']) ?></td>
                            <td class="px-8 py-6 text-xs text-gray-500 uppercase tracking-wider"><?= htmlspecialchars($app['appointment_type']) ?></td>
                            <td class="px-8 py-6 text-xs font-medium text-gray-600"><?= htmlspecialchars($app['atelier_location']) ?></td>
                            <td class="px-8 py-6">
                                <span class="status-badge <?= $status_class ?>"><?= $app['status'] ?></span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button onclick="alert('Viewing full details...')" class="p-2 text-gray-400 hover:text-brand-black"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                                    <a href="appointments_manage.php?cancel=<?= $app['id'] ?>" onclick="return confirm('Cancel this appointment?')" class="p-2 text-gray-300 hover:text-red-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
