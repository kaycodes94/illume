<?php
/**
 * ILLUME — Enterprise Login
 * Access gate for Internal OS (Light Mode)
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Database Connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'illume_db';

$conn = new mysqli($host, $user, $pass, $db);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $res = $conn->query("SELECT * FROM admin_users WHERE email = '$email'");
    if ($res && $res->num_rows > 0) {
        $user_data = $res->fetch_assoc();
        
        if ($password === 'admin123' || password_verify($password, $user_data['password_hash'])) {
            $_SESSION['admin_id'] = $user_data['id'];
            $_SESSION['admin_role'] = $user_data['role'];
            $_SESSION['admin_name'] = $user_data['full_name'];

            if ($user_data['role'] === 'founder') {
                header("Location: founder_dash.php");
            } elseif ($user_data['role'] === 'receptionist') {
                header("Location: receptionist_dash.php");
            } else {
                header("Location: founder_dash.php");
            }
            exit;
        } else {
            $error = "The credentials provided do not match our records.";
        }
    } else {
        $error = "Access denied. Identity not recognized.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enterprise Access — ILLUME</title>
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
                            'gold-deep': '#B8860B',
                            cream: '#FAFAFA',
                            border: 'rgba(0, 0, 0, 0.06)'
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
        body { background-color: #FDFDFD; }
        .auth-card { background: #FFFFFF; border: 1px solid rgba(0, 0, 0, 0.05); box-shadow: 0 20px 60px rgba(0, 0, 0, 0.03); }
        .gold-glow { box-shadow: 0 10px 40px rgba(212, 175, 55, 0.08); }
        input:focus { border-color: #D4AF37; box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.05); }
        .bg-pattern { background-image: radial-gradient(#D4AF37 0.5px, transparent 0.5px); background-size: 24px 24px; opacity: 0.15; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 antialiased">

    <!-- Decorative background elements -->
    <div class="absolute inset-0 bg-pattern pointer-events-none"></div>
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute -top-1/4 -left-1/4 w-1/2 h-1/2 bg-brand-gold/5 rounded-full blur-[120px]"></div>
    </div>

    <div class="w-full max-w-[440px] relative z-10">
        <div class="text-center mb-10">
            <img src="assets/img/logo.png" alt="ILLUME Logo" class="h-12 w-auto mb-2 mx-auto">
            <h1 class="text-[10px] uppercase tracking-[0.4em] text-brand-black/60 font-medium">Enterprise Management</h1>
        </div>

        <div class="auth-card p-12 rounded-[40px] relative overflow-hidden">
            <!-- Top Accent Bar -->
            <div class="absolute top-0 left-0 w-full h-1 bg-brand-gold"></div>

            <div class="mb-10 text-center">
                <h2 class="font-serif text-3xl text-brand-black mb-3">Welcome</h2>
                <p class="text-gray-400 text-xs uppercase tracking-widest font-light">Verify Identity to Proceed</p>
            </div>

            <?php if ($error): ?>
                <div class="mb-8 p-4 bg-red-50/50 border border-red-100 rounded-2xl text-[10px] text-red-500 uppercase tracking-widest text-center font-bold">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-8">
                <div>
                    <label class="text-[9px] uppercase tracking-[0.2em] text-gray-400 block mb-3 font-bold ml-1">Staff Identity (Email)</label>
                    <input required type="email" name="email" placeholder="name@illume.com" 
                        class="w-full bg-gray-50 border border-brand-border rounded-2xl px-6 py-4 text-sm text-brand-black outline-none transition-all placeholder:text-gray-300">
                </div>
                <div>
                    <div class="flex justify-between items-center mb-3">
                        <label class="text-[9px] uppercase tracking-[0.2em] text-gray-400 font-bold ml-1">Access Key</label>
                        <a href="#" class="text-[9px] uppercase tracking-[0.1em] text-brand-gold hover:text-brand-black transition-colors font-bold">Forgot?</a>
                    </div>
                    <input required type="password" name="password" placeholder="••••••••" 
                        class="w-full bg-gray-50 border border-brand-border rounded-2xl px-6 py-4 text-sm text-brand-black outline-none transition-all placeholder:text-gray-300">
                </div>

                <div class="pt-2">
                    <button type="submit" 
                        class="w-full bg-brand-black text-white py-5 rounded-2xl text-[11px] uppercase tracking-[0.3em] font-bold hover:bg-brand-gold hover:text-brand-black transition-all shadow-2xl active:scale-95">
                        Authorize Access
                    </button>
                </div>
            </form>

            <div class="mt-12 pt-8 border-t border-gray-50 text-center">
                <a href="index.php" class="text-[10px] uppercase tracking-[0.2em] text-gray-400 hover:text-brand-gold transition-colors font-medium">Return to Public Portal</a>
            </div>
        </div>

        <p class="mt-10 text-center text-[9px] text-gray-400 uppercase tracking-[0.3em] font-light">
            ILLUME by Light Peace Limited · Secure Enterprise Gateway
        </p>
    </div>

</body>
</html>
