<?php
/**
 * ILLUME by Light Peace — Database Initializer
 * Run once: http://localhost/illume_update/init_db.php
 */

$host   = 'localhost';
$user   = 'root';
$pass   = '';          // Change if your MySQL root has a password

// ── Disable strict exception mode so next_result() never throws ───────────────
mysqli_report(MYSQLI_REPORT_OFF);

// ── Connect without selecting a DB first ──────────────────────────────────────
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die('<p style="color:red">Connection failed: ' . htmlspecialchars($conn->connect_error) . '</p>');
}

// ── Read the SQL file ─────────────────────────────────────────────────────────
$sql_file = __DIR__ . '/database.sql';
if (!file_exists($sql_file)) {
    die('<p style="color:red">database.sql not found.</p>');
}

$sql = file_get_contents($sql_file);

// ── Execute multi-statement SQL ───────────────────────────────────────────────
$errors  = [];
$success = 0;

if ($conn->multi_query($sql)) {
    do {
        // Free any result set (SELECT-type statements produce one)
        if ($result = $conn->store_result()) {
            $result->free();
        }
        // Capture per-statement errors
        if ($conn->errno) {
            $errors[] = 'Statement ' . ($success + 1) . ': [' . $conn->errno . '] ' . $conn->error;
        } else {
            $success++;
        }
    } while ($conn->more_results() && $conn->next_result());
} else {
    $errors[] = 'multi_query failed: [' . $conn->errno . '] ' . $conn->error;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ILLUME — DB Init</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap');
  *{margin:0;padding:0;box-sizing:border-box}
  body{font-family:'Inter',sans-serif;background:#0a0a0a;color:#e8e0d4;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem}
  .card{background:#161616;border:1px solid #2a2a2a;border-radius:16px;padding:2.5rem 3rem;max-width:640px;width:100%}
  h1{font-size:1.4rem;font-weight:600;letter-spacing:.05em;margin-bottom:.25rem;color:#c9a96e}
  .sub{font-size:.85rem;color:#666;margin-bottom:2rem}
  .badge{display:inline-block;padding:.25rem .75rem;border-radius:999px;font-size:.75rem;font-weight:600;margin-bottom:1.5rem}
  .ok{background:#1a3a1a;color:#5cba5c}
  .fail{background:#3a1a1a;color:#e05555}
  ul{list-style:none;padding:0}
  li{padding:.5rem .75rem;background:#1e1e1e;border-radius:8px;margin-bottom:.5rem;font-size:.85rem}
  li.good{color:#5cba5c}
  li.bad{color:#e05555;word-break:break-word}
  .note{margin-top:1.5rem;font-size:.8rem;color:#555;line-height:1.6}
</style>
</head>
<body>
<div class="card">
  <h1>ILLUME by Light Peace</h1>
  <p class="sub">Database Initialization Report</p>

  <?php if (empty($errors)): ?>
    <span class="badge ok">✓ Success — <?= $success ?> statements executed</span>
    <p style="margin-bottom:1rem">The <strong>illume_db</strong> database was created with all tables and seed data.</p>
    <ul>
      <?php
        $tables = [
          'admin_users','clients','services','orders','invoices',
          'payment_milestones','appointments','leads','guide_downloads',
          'fitting_requests','revenue_snapshots','revenue_category_breakdown',
          'activity_logs','site_settings'
        ];
        foreach ($tables as $t): ?>
      <li class="good">✓ <?= $t ?></li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <span class="badge <?= $success > 0 ? 'ok' : 'fail' ?>">
      <?= $success ?> statements ran &nbsp;·&nbsp; <?= count($errors) ?> error(s)
    </span>
    <ul style="margin-top:1rem">
      <?php foreach ($errors as $e): ?>
      <li class="bad">✗ <?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <p class="note">
    ⚠ Delete or restrict access to this file after a successful run.<br>
    Default admin password hash is a placeholder — update it before going live.
  </p>
</div>
</body>
</html>
