<?php
/**
 * ILLUME — Receptionist Desk
 * Operational dashboard for client management
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'receptionist') {
    header("Location: login.php");
    exit;
}

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

// Handle Check-In Action
if (isset($_POST['action']) && $_POST['action'] === 'check_in') {
    $aid = intval($_POST['appointment_id']);
    $conn->query("UPDATE appointments SET status = 'Completed', updated_at = NOW() WHERE id = $aid");
    header("Location: receptionist_dash.php?status=checked_in");
    exit;
}

// Handle New Appointment
if (isset($_POST['action']) && $_POST['action'] === 'new_appointment') {
    $name = $conn->real_escape_string($_POST['client_name']);
    // Logic for new appointment could go here
}

// Fetch Today's Appointments
$today = date('Y-m-d');
$appointments_res = $conn->query("SELECT * FROM appointments WHERE appointment_date = '$today' ORDER BY appointment_time ASC");
$appointments = [];
while ($row = $appointments_res ? $appointments_res->fetch_assoc() : null) {
    $appointments[] = $row;
}

// Fetch Inbound Pipeline (Leads)
$leads_res = $conn->query("SELECT * FROM leads ORDER BY created_at DESC LIMIT 10");
$leads = [];
while ($row = $leads_res ? $leads_res->fetch_assoc() : null) {
    $leads[] = $row;
}

function format_time($t) {
    return date('H:i', strtotime($t));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receptionist Desk — ILLUME</title>
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
                            black: '#000000',
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
        body { background-color: #fff; color: #1a1a1a; }
        .card { background: #fff; border: 1px solid rgba(0, 0, 0, 0.05); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02); }
        .sidebar { background: #000; color: #fff; }
        .nav-link { transition: all 0.3s ease; }
        .nav-link.active { background: #D4AF37; color: #000; }
        .status-badge { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; padding: 4px 12px; border-radius: 999px; }
    </style>
</head>
<body class="antialiased font-sans bg-brand-gray min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 sidebar hidden lg:flex flex-col p-6 sticky top-0 h-screen">
        <div class="mb-12 flex items-center gap-3">
            <span class="font-serif italic text-3xl text-brand-gold">I</span>
            <span class="text-sm tracking-[0.3em] font-light">ILLUME</span>
        </div>
        
        <nav class="flex-grow space-y-2">
            <a href="#" class="nav-link active flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16" /></svg>
                Front Desk
            </a>
            <a href="appointments_manage.php" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white hover:bg-white/5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                Appointments
            </a>
            <a href="clients_manage.php" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white hover:bg-white/5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                Clients
            </a>
        </nav>

        <div class="mt-auto pt-6 border-t border-white/10 space-y-4">
            <a href="index.php" class="text-xs text-gray-500 hover:text-brand-gold flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Back to Site
            </a>
            <button onclick="openChangePasswordModal()" class="w-full text-left text-xs text-gray-500 hover:text-brand-gold flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Change Password
            </button>
            <a href="login.php" class="text-xs text-red-500 hover:text-red-700 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Logout
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow p-8">
        <!-- Top Bar -->
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-2xl font-serif text-brand-black">Receptionist Desk</h1>
                <p class="text-xs text-gray-500 font-light uppercase tracking-widest mt-1"><?= date('l, jS F Y') ?></p>
            </div>
            <div class="flex items-center gap-4">
                <button onclick="openBookingModal()" class="bg-brand-black text-white px-6 py-2.5 rounded-full text-xs uppercase tracking-widest font-medium hover:bg-brand-gold hover:text-brand-black transition-all shadow-lg">Book New Fitting</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Appointments Column -->
            <div class="lg:col-span-2 space-y-6">
                <div class="card p-8 rounded-3xl">
                    <div class="flex justify-between items-center mb-8">
                        <h2 class="text-xs uppercase tracking-widest font-bold text-brand-black">Today's Schedule</h2>
                        <span class="text-[10px] bg-brand-gold/10 text-brand-gold px-3 py-1 rounded-full font-bold"><?= count($appointments) ?> Appointments</span>
                    </div>

                    <div class="space-y-4">
                        <?php if (empty($appointments)): ?>
                            <!-- Sample Appointments if DB is empty -->
                            <div class="flex items-center gap-6 p-4 rounded-2xl border border-brand-gold/20 bg-brand-gold/5">
                                <div class="w-20 text-center shrink-0">
                                    <p class="text-lg font-medium text-brand-black">10:30</p>
                                    <p class="text-[10px] text-gray-500 uppercase">AM</p>
                                </div>
                                <div class="flex-grow">
                                    <p class="text-sm font-semibold text-brand-black">Bespoke Bridal Fitting</p>
                                    <p class="text-xs text-gray-500">Adaeze Okeke · Abuja Atelier</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="status-badge bg-green-100 text-green-700">Checked In</span>
                                    <button class="p-2 text-gray-400 hover:text-brand-black transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" /></svg></button>
                                </div>
                            </div>
                            <div class="flex items-center gap-6 p-4 rounded-2xl border border-brand-border">
                                <div class="w-20 text-center shrink-0">
                                    <p class="text-lg font-medium text-brand-black">02:00</p>
                                    <p class="text-[10px] text-gray-500 uppercase">PM</p>
                                </div>
                                <div class="flex-grow">
                                    <p class="text-sm font-semibold text-brand-black">Suits Consult</p>
                                    <p class="text-xs text-gray-500">Mr. Chinedu · Virtual</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="status-badge bg-yellow-100 text-yellow-700">Awaiting</span>
                                    <button class="bg-brand-black text-white px-4 py-2 rounded-xl text-[10px] uppercase tracking-widest font-medium">Check In</button>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($appointments as $app): ?>
                            <div class="flex items-center gap-6 p-4 rounded-2xl border border-brand-border hover:border-brand-gold/30 transition-all">
                                <div class="w-20 text-center shrink-0">
                                    <p class="text-lg font-medium text-brand-black"><?= format_time($app['appointment_time']) ?></p>
                                    <p class="text-[10px] text-gray-500 uppercase"><?= date('A', strtotime($app['appointment_time'])) ?></p>
                                </div>
                                <div class="flex-grow">
                                    <p class="text-sm font-semibold text-brand-black"><?= htmlspecialchars($app['appointment_type']) ?></p>
                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($app['client_name']) ?> · <?= htmlspecialchars($app['atelier_location']) ?></p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <?php if ($app['status'] == 'Completed'): ?>
                                        <span class="status-badge bg-gray-100 text-gray-500">Done</span>
                                    <?php else: ?>
                                        <span class="status-badge bg-yellow-100 text-yellow-700"><?= $app['status'] ?></span>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="check_in">
                                            <input type="hidden" name="appointment_id" value="<?= $app['id'] ?>">
                                            <button type="submit" class="bg-brand-black text-white px-4 py-2 rounded-xl text-[10px] uppercase tracking-widest font-medium hover:bg-brand-gold hover:text-brand-black transition-colors">Check In</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="card p-6 rounded-3xl">
                        <h3 class="text-[10px] uppercase tracking-widest font-bold text-gray-400 mb-6">Atelier Status</h3>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-medium">Abuja Staff</span>
                            <span class="text-xs text-green-500 font-bold">5 Online</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium">Ebonyi Staff</span>
                            <span class="text-xs text-brand-gold font-bold">3 Online</span>
                        </div>
                    </div>
                    <div class="card p-6 rounded-3xl bg-brand-purple text-white">
                        <h3 class="text-[10px] uppercase tracking-widest font-bold text-brand-gold mb-6">Internal Note</h3>
                        <p class="text-xs font-light leading-relaxed">"The Silk shipment from Lagos is delayed by 2 days. Please update all Bridal clients with fittings scheduled for Friday."</p>
                    </div>
                </div>
            </div>

            <!-- Pipeline Column -->
            <div class="space-y-6">
                <div class="card p-6 rounded-3xl h-full">
                    <div class="flex justify-between items-center mb-8">
                        <h2 class="text-xs uppercase tracking-widest font-bold text-brand-black">Inbound Pipeline</h2>
                        <div class="flex items-center gap-1">
                            <div class="w-1.5 h-1.5 rounded-full bg-brand-gold animate-pulse"></div>
                            <span class="text-[10px] text-brand-gold font-bold uppercase tracking-wider">Live</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <?php if (empty($leads)): ?>
                            <p class="text-xs text-gray-400 italic py-8 text-center">No active leads in pipeline.</p>
                        <?php else: ?>
                            <?php foreach ($leads as $lead): 
                                $source = $lead['source'];
                                $icon_bg = ($source == 'AI Concierge') ? 'bg-brand-gold' : (($source == 'Instagram DM') ? 'bg-brand-purple' : 'bg-black');
                            ?>
                            <div class="p-5 bg-brand-gray rounded-2xl border border-brand-border relative group hover:border-brand-gold/40 transition-all">
                                <div class="flex justify-between items-start mb-3">
                                    <span class="text-[9px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-md bg-white border border-brand-border text-brand-black"><?= htmlspecialchars($source) ?></span>
                                    <span class="text-[10px] text-gray-400"><?= date('H:i', strtotime($lead['created_at'])) ?></span>
                                </div>
                                <p class="text-sm font-semibold text-brand-black mb-1"><?= htmlspecialchars($lead['service_interest'] ?: 'Inquiry') ?></p>
                                <p class="text-xs text-gray-500 mb-4"><?= htmlspecialchars($lead['name'] ?: 'Prospect') ?></p>
                                <div class="flex gap-2">
                                    <button onclick="openAssignModal('<?= htmlspecialchars($lead['name']) ?>')" class="flex-1 bg-white border border-brand-border text-[10px] uppercase tracking-widest font-bold py-2 rounded-lg hover:border-brand-gold hover:text-brand-gold transition-colors">Assign</button>
                                    <button onclick="openReplyModal('<?= htmlspecialchars($lead['name']) ?>')" class="flex-1 bg-brand-black text-white text-[10px] uppercase tracking-widest font-bold py-2 rounded-lg hover:bg-brand-gold hover:text-brand-black transition-colors">Reply</button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Sample Lead if empty -->
                        <?php if (empty($leads)): ?>
                        <div class="p-5 bg-brand-gray rounded-2xl border border-brand-border relative opacity-60">
                            <div class="flex justify-between items-start mb-3">
                                <span class="text-[9px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-md bg-white border border-brand-border text-brand-black">AI Concierge</span>
                                <span class="text-[10px] text-gray-400">10 mins ago</span>
                            </div>
                            <p class="text-sm font-semibold text-brand-black mb-1">Bridal Dress Inquiry</p>
                            <p class="text-xs text-gray-500 mb-4">Sarah Johnson</p>
                            <div class="flex gap-2">
                                <button class="flex-1 bg-white border border-brand-border text-[10px] uppercase tracking-widest font-bold py-2 rounded-lg">Assign</button>
                                <button class="flex-1 bg-brand-black text-white text-[10px] uppercase tracking-widest font-bold py-2 rounded-lg">Reply</button>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Booking Modal -->
    <div id="booking-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-6">
        <div class="bg-white w-full max-w-md rounded-[32px] p-10 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-brand-gold"></div>
            <button onclick="closeBookingModal()" class="absolute top-6 right-6 text-gray-400 hover:text-brand-black">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <h2 class="font-serif text-2xl mb-2">Book Fitting</h2>
            <p class="text-xs text-gray-400 uppercase tracking-widest mb-8">Manual Entry Portal</p>
            
            <form method="POST" class="space-y-6">
                <input type="hidden" name="action" value="new_appointment">
                <div>
                    <label class="text-[10px] uppercase tracking-widest text-gray-400 block mb-2 font-bold">Client Name</label>
                    <input type="text" name="client_name" required class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-4 text-sm focus:border-brand-gold outline-none transition-all">
                </div>
                <div>
                    <label class="text-[10px] uppercase tracking-widest text-gray-400 block mb-2 font-bold">Service Type</label>
                    <select name="type" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-4 text-sm focus:border-brand-gold outline-none transition-all">
                        <option>Bridal Fitting</option>
                        <option>Suits Consult</option>
                        <option>African Luxury Review</option>
                    </select>
                </div>
                <div class="pt-4">
                    <button type="submit" class="w-full bg-brand-black text-white py-4 rounded-2xl text-xs uppercase tracking-[0.2em] font-bold hover:bg-brand-gold hover:text-brand-black transition-all">Create Appointment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reply Modal -->
    <div id="reply-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-6">
        <div class="bg-white w-full max-w-lg rounded-[32px] p-10 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-brand-purple"></div>
            <button onclick="closeReplyModal()" class="absolute top-6 right-6 text-gray-400 hover:text-brand-black">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <h2 class="font-serif text-2xl mb-2">Secure Channel</h2>
            <p class="text-xs text-gray-400 uppercase tracking-widest mb-8">Replying to: <span id="reply-client-name" class="text-brand-gold font-bold"></span></p>
            
            <div class="space-y-6">
                <div>
                    <label class="text-[10px] uppercase tracking-widest text-gray-400 block mb-2 font-bold">Message Template</label>
                    <select class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-xs outline-none">
                        <option>Bridal Consultation Invite</option>
                        <option>Bespoke Suit Pricing Guide</option>
                        <option>Atelier Visit Confirmation</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] uppercase tracking-widest text-gray-400 block mb-2 font-bold">Personalized Note</label>
                    <textarea rows="4" class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-4 text-sm focus:border-brand-gold outline-none transition-all" placeholder="Type your personal message here..."></textarea>
                </div>
                <div class="pt-4 flex gap-4">
                    <button onclick="closeReplyModal()" class="flex-1 border border-brand-border py-4 rounded-2xl text-[10px] uppercase tracking-[0.2em] font-bold hover:bg-gray-50 transition-all">Save Draft</button>
                    <button onclick="alert('Message encrypted and sent.'); closeReplyModal();" class="flex-1 bg-brand-black text-white py-4 rounded-2xl text-[10px] uppercase tracking-[0.2em] font-bold hover:bg-brand-gold hover:text-brand-black transition-all">Send Message</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Modal -->
    <div id="assign-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-6">
        <div class="bg-white w-full max-w-md rounded-[32px] p-10 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-brand-gold"></div>
            <button onclick="closeAssignModal()" class="absolute top-6 right-6 text-gray-400 hover:text-brand-black">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <h2 class="font-serif text-2xl mb-2">Assign Prospect</h2>
            <p class="text-xs text-gray-400 uppercase tracking-widest mb-8">Directing: <span id="assign-client-name" class="text-brand-gold font-bold"></span></p>
            
            <div class="space-y-4">
                <button onclick="confirmAssignment('Sales Team · Head Office')" class="w-full text-left p-4 rounded-2xl border border-gray-100 hover:border-brand-gold hover:bg-brand-gold/5 transition-all group">
                    <p class="text-xs font-bold text-brand-black group-hover:text-brand-gold transition-colors uppercase tracking-widest">Sales Team</p>
                    <p class="text-[10px] text-gray-400">Head Office · Abuja</p>
                </button>
                <button onclick="confirmAssignment('Design Team · Bespoke')" class="w-full text-left p-4 rounded-2xl border border-gray-100 hover:border-brand-gold hover:bg-brand-gold/5 transition-all group">
                    <p class="text-xs font-bold text-brand-black group-hover:text-brand-gold transition-colors uppercase tracking-widest">Design Team</p>
                    <p class="text-[10px] text-gray-400">Bespoke Studio · Ebonyi</p>
                </button>
                <button onclick="confirmAssignment('Atelier Manager')" class="w-full text-left p-4 rounded-2xl border border-gray-100 hover:border-brand-gold hover:bg-brand-gold/5 transition-all group">
                    <p class="text-xs font-bold text-brand-black group-hover:text-brand-gold transition-colors uppercase tracking-widest">Atelier Manager</p>
                    <p class="text-[10px] text-gray-400">Direct Escalation</p>
                </button>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div id="password-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-6">
        <div class="bg-white w-full max-w-md rounded-[32px] p-10 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-brand-gold"></div>
            <button onclick="closeChangePasswordModal()" class="absolute top-6 right-6 text-gray-400 hover:text-brand-black transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <h2 class="font-serif text-2xl mb-2">Security Settings</h2>
            <p class="text-xs text-gray-400 uppercase tracking-widest mb-8">Update Access Key</p>
            
            <div id="password-alert" class="hidden mb-6 p-4 rounded-2xl text-[10px] uppercase tracking-widest text-center font-bold"></div>

            <form id="password-form" class="space-y-6">
                <div>
                    <label class="text-[10px] uppercase tracking-widest text-gray-400 block mb-2 font-bold">Current Password</label>
                    <input type="password" name="current_password" required class="w-full bg-gray-50 border border-brand-border rounded-2xl px-5 py-4 text-sm focus:border-brand-gold outline-none transition-all placeholder:text-gray-300">
                </div>
                <div>
                    <label class="text-[10px] uppercase tracking-widest text-gray-400 block mb-2 font-bold">New Password</label>
                    <input type="password" name="new_password" required class="w-full bg-gray-50 border border-brand-border rounded-2xl px-5 py-4 text-sm focus:border-brand-gold outline-none transition-all placeholder:text-gray-300">
                </div>
                <div>
                    <label class="text-[10px] uppercase tracking-widest text-gray-400 block mb-2 font-bold">Confirm New Password</label>
                    <input type="password" name="confirm_password" required class="w-full bg-gray-50 border border-brand-border rounded-2xl px-5 py-4 text-sm focus:border-brand-gold outline-none transition-all placeholder:text-gray-300">
                </div>
                <div class="pt-4">
                    <button type="submit" id="password-submit-btn" class="w-full bg-brand-black text-white py-4 rounded-2xl text-xs uppercase tracking-[0.2em] font-bold hover:bg-brand-gold hover:text-brand-black transition-all">Update Password</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openBookingModal() {
            document.getElementById('booking-modal').style.display = 'flex';
        }
        function closeBookingModal() {
            document.getElementById('booking-modal').style.display = 'none';
        }
        function openReplyModal(name) {
            document.getElementById('reply-client-name').innerText = name;
            document.getElementById('reply-modal').style.display = 'flex';
        }
        function closeReplyModal() {
            document.getElementById('reply-modal').style.display = 'none';
        }
        function openAssignModal(name) {
            document.getElementById('assign-client-name').innerText = name;
            document.getElementById('assign-modal').style.display = 'flex';
        }
        function closeAssignModal() {
            document.getElementById('assign-modal').style.display = 'none';
        }
        function confirmAssignment(team) {
            alert('Lead successfully routed to ' + team);
            closeAssignModal();
        }
        function openChangePasswordModal() {
            document.getElementById('password-modal').style.display = 'flex';
            document.getElementById('password-alert').classList.add('hidden');
            document.getElementById('password-form').reset();
        }
        function closeChangePasswordModal() {
            document.getElementById('password-modal').style.display = 'none';
        }

        document.getElementById('password-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('password-submit-btn');
            const alertBox = document.getElementById('password-alert');
            
            btn.innerHTML = 'PROCESSING...';
            btn.disabled = true;

            const formData = new FormData(this);

            fetch('change_password.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                btn.innerHTML = 'UPDATE PASSWORD';
                btn.disabled = false;
                
                alertBox.classList.remove('hidden', 'bg-red-50', 'text-red-500', 'border-red-100', 'bg-green-50', 'text-green-600', 'border-green-100');
                
                if (data.success) {
                    alertBox.classList.add('bg-green-50', 'text-green-600', 'border', 'border-green-100');
                    alertBox.innerText = data.message;
                    setTimeout(() => { closeChangePasswordModal(); }, 2000);
                } else {
                    alertBox.classList.add('bg-red-50', 'text-red-500', 'border', 'border-red-100');
                    alertBox.innerText = data.message;
                }
            })
            .catch(err => {
                btn.innerHTML = 'UPDATE PASSWORD';
                btn.disabled = false;
                alertBox.classList.remove('hidden');
                alertBox.classList.add('bg-red-50', 'text-red-500', 'border', 'border-red-100');
                alertBox.innerText = 'An error occurred. Please try again.';
            });
        });

        // Handle Sidebar "Coming Soon"
        document.querySelectorAll('.nav-link:not(.active)[href="#"]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                alert(link.innerText.trim() + " management is coming in the next update.");
            });
        });
    </script>
</body>
</html>
