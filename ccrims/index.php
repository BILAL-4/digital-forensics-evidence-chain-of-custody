<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        $error = 'Your session expired. Please try again.';
    } else {
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password'] ?? '';
    $userType = $_POST['user_type'] ?? 'citizen'; // citizen | staff

    $db = getDB();

    if ($userType === 'citizen') {
        $stmt = $db->prepare("SELECT CitizenID AS id, Fname, Lname, Password FROM CITIZEN WHERE Email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        if ($row && password_verify($password, $row['Password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $row['id'];
            $_SESSION['user_name'] = $row['Fname'] . ' ' . $row['Lname'];
            $_SESSION['user_role'] = 'Citizen';
            $_SESSION['user_type'] = 'citizen';
            header('Location: /ccrims/pages/citizen/dashboard.php');
            exit;
        }
    } else {
        $stmt = $db->prepare("SELECT StaffID AS id, Fname, Lname, Role, Password FROM STAFF WHERE Email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        if ($row && password_verify($password, $row['Password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $row['id'];
            $_SESSION['user_name'] = $row['Fname'] . ' ' . $row['Lname'];
            $_SESSION['user_role'] = $row['Role'];
            $_SESSION['user_type'] = 'staff';
            header('Location: /ccrims/pages/officer/dashboard.php');
            exit;
        }
    }
    if (!$error) {
        $error = 'Invalid email or password.';
    }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Digital Forensics Evidence System — Secure Login</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=IBM+Plex+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/ccrims/css/main.css">
<style>
body {
  display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0;
  background:
    radial-gradient(circle at 15% 15%,rgba(34,211,238,.08),transparent 22rem),
    radial-gradient(circle at 85% 85%,rgba(99,102,241,.10),transparent 24rem),
    linear-gradient(145deg,#060913,#0b1020);
}
body::before {
  content:""; position:fixed; inset:0; pointer-events:none;
  background-image:linear-gradient(rgba(148,163,184,.025) 1px,transparent 1px),linear-gradient(90deg,rgba(148,163,184,.025) 1px,transparent 1px);
  background-size:36px 36px; mask-image:radial-gradient(circle at center,black,transparent 82%);
}
.site-header, .site-footer { display:none; }
.site-main { padding:0; display:flex; align-items:center; justify-content:center; width:100%; }
.auth-card {
  position:relative; width:min(440px,calc(100% - 32px)); max-width:none;
  border:1px solid rgba(148,163,184,.12); border-radius:24px;
  background:linear-gradient(145deg,rgba(15,23,42,.96),rgba(8,12,26,.96));
  box-shadow:0 30px 90px rgba(0,0,0,.48);
  overflow:hidden;
}
.auth-card::before { content:""; position:absolute; height:2px; left:14%; right:14%; top:0; background:linear-gradient(90deg,transparent,#22d3ee,transparent); }
.auth-logo { margin-bottom:24px; }
.logo-badge {
  width:54px;height:54px;margin:0 auto 12px;display:grid;place-items:center;
  border:1px solid rgba(34,211,238,.28);border-radius:16px;
  color:#67e8f9;background:rgba(34,211,238,.05);
  box-shadow:0 0 28px rgba(34,211,238,.08);
}
.auth-logo .logo-text { color:#f8fafc; letter-spacing:.12em; }
.auth-logo .logo-sub { max-width:340px;margin:6px auto 0;line-height:1.5; }
.auth-tabs { border:1px solid rgba(148,163,184,.10);border-radius:12px;padding:4px;margin-bottom:20px; }
.auth-tab { flex:1;text-align:center;border-radius:8px;padding:9px; }
.auth-tab.active { background:rgba(34,211,238,.10);color:#67e8f9;box-shadow:inset 0 0 0 1px rgba(34,211,238,.12); }
.form-group input { height:48px;border-radius:11px!important;background:rgba(2,6,18,.64)!important;border-color:rgba(148,163,184,.12)!important; }
.form-group input:focus { border-color:rgba(34,211,238,.45)!important;box-shadow:0 0 0 4px rgba(34,211,238,.07)!important; }
.btn { border:0;border-radius:11px!important;background:linear-gradient(135deg,#22d3ee,#818cf8)!important;color:#071018!important;font-weight:800!important; }
.auth-switch { margin-top:18px;text-align:center;color:#7d8ca4; }
.auth-switch a { color:#67e8f9!important; }
</style>
</head>
<body>
<main class="site-main">
<div class="auth-card">
  <div class="auth-logo">
    <span class="logo-badge">⬡</span>
    <span class="logo-text">CCRIMS</span>
    <span class="logo-sub">Cyber Crime Reporting &amp; Investigation Management System</span>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <?php if (isset($_GET['error']) && $_GET['error'] === 'unauthorized'): ?>
    <div class="alert alert-warn">You are not authorized to access that page.</div>
  <?php endif; ?>

  <div class="auth-tabs">
    <div class="auth-tab active" onclick="switchTab('citizen',this)">Citizen</div>
    <div class="auth-tab" onclick="switchTab('staff',this)">Staff / Officer</div>
  </div>

  <form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="user_type" id="user_type" value="citizen">
    <div style="display:flex;flex-direction:column;gap:14px;">
      <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email" required placeholder="your@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required placeholder="••••••••">
      </div>
      <button type="submit" class="btn" style="width:100%;justify-content:center;margin-top:4px;">Sign In</button>
    </div>
  </form>

  <div class="auth-switch">
    Don't have an account? <a href="/ccrims/register.php">Register as Citizen</a>
  </div>

  <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border2);">
    

  </div>
</div>
</main>

<script>
function switchTab(type, el) {
  document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
  document.getElementById('user_type').value = type;
}
</script>
</body>
</html>
