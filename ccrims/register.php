<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $fname  = trim($_POST['fname'] ?? '');
    $lname  = trim($_POST['lname'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $nid    = trim($_POST['national_id'] ?? '');
    $phone  = trim($_POST['phone'] ?? '');
    $city   = trim($_POST['city'] ?? '');
    $pass   = $_POST['password'] ?? '';
    $pass2  = $_POST['password2'] ?? '';

    if ($pass !== $pass2) {
        $error = 'Passwords do not match.';
    } elseif (strlen($pass) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $db = getDB();
        try {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $id   = bin2hex(random_bytes(16));
            $uid  = substr($id,0,8).'-'.substr($id,8,4).'-'.substr($id,12,4).'-'.substr($id,16,4).'-'.substr($id,20);

            $db->prepare("INSERT INTO CITIZEN (CitizenID,Fname,Lname,Email,NationalID,Phone,City,Password) VALUES (?,?,?,?,?,?,?,?)")
               ->execute([$uid,$fname,$lname,$email,$nid,$phone?:null,$city?:null,$hash]);

            $success = 'Account created! You can now log in.';
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'Duplicate')) {
                $error = 'Email or National ID already registered.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Digital Forensics Evidence System — Registration</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=IBM+Plex+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/ccrims/css/main.css">
<style>
body{
display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;
background:radial-gradient(circle at 15% 15%,rgba(34,211,238,.08),transparent 22rem),radial-gradient(circle at 85% 85%,rgba(99,102,241,.10),transparent 24rem),linear-gradient(145deg,#060913,#0b1020);
}
body::before{content:"";position:fixed;inset:0;pointer-events:none;background-image:linear-gradient(rgba(148,163,184,.025) 1px,transparent 1px),linear-gradient(90deg,rgba(148,163,184,.025) 1px,transparent 1px);background-size:36px 36px;mask-image:radial-gradient(circle at center,black,transparent 82%);}
.site-main{padding:0;display:flex;align-items:center;justify-content:center;width:100%;}
.auth-card{position:relative;width:min(520px,calc(100% - 32px));max-width:none;border:1px solid rgba(148,163,184,.12);border-radius:24px;background:linear-gradient(145deg,rgba(15,23,42,.96),rgba(8,12,26,.96));box-shadow:0 30px 90px rgba(0,0,0,.48);overflow:hidden;}
.auth-card::before{content:"";position:absolute;height:2px;left:14%;right:14%;top:0;background:linear-gradient(90deg,transparent,#22d3ee,transparent);}
.logo-badge{width:54px;height:54px;margin:0 auto 12px;display:grid;place-items:center;border:1px solid rgba(34,211,238,.28);border-radius:16px;color:#67e8f9;background:rgba(34,211,238,.05);}
.auth-logo .logo-text{color:#f8fafc;letter-spacing:.12em;}
.form-group input{height:46px;border-radius:11px!important;background:rgba(2,6,18,.64)!important;border-color:rgba(148,163,184,.12)!important;}
.form-group input:focus{border-color:rgba(34,211,238,.45)!important;box-shadow:0 0 0 4px rgba(34,211,238,.07)!important;}
.btn{border:0;border-radius:11px!important;background:linear-gradient(135deg,#22d3ee,#818cf8)!important;color:#071018!important;font-weight:800!important;}
</style>
</head>
<body>
<main class="site-main">
<div class="auth-card">
  <div class="auth-logo">
    <span class="logo-badge">⬡</span>
    <span class="logo-text">CCRIMS</span>
    <span class="logo-sub">Citizen Registration</span>
  </div>

  <?php if ($error):   ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?> <a href="/ccrims/index.php">Login →</a></div><?php endif; ?>

  <?php if (!$success): ?>
  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
    <div style="display:flex;flex-direction:column;gap:14px;">
      <div class="form-grid" style="grid-template-columns:1fr 1fr;">
        <div class="form-group">
          <label>First Name</label>
          <input type="text" name="fname" required value="<?= htmlspecialchars($_POST['fname']??'') ?>">
        </div>
        <div class="form-group">
          <label>Last Name</label>
          <input type="text" name="lname" required value="<?= htmlspecialchars($_POST['lname']??'') ?>">
        </div>
      </div>
      <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email']??'') ?>">
      </div>
      <div class="form-group">
        <label>National ID (CNIC)</label>
        <input type="text" name="national_id" required placeholder="3520112345671" value="<?= htmlspecialchars($_POST['national_id']??'') ?>">
      </div>
      <div class="form-grid" style="grid-template-columns:1fr 1fr;">
        <div class="form-group">
          <label>Phone <span style="font-weight:400;color:var(--text3)">(optional)</span></label>
          <input type="text" name="phone" placeholder="+923001234567" value="<?= htmlspecialchars($_POST['phone']??'') ?>">
        </div>
        <div class="form-group">
          <label>City <span style="font-weight:400;color:var(--text3)">(optional)</span></label>
          <input type="text" name="city" placeholder="Lahore" value="<?= htmlspecialchars($_POST['city']??'') ?>">
        </div>
      </div>
      <div class="form-grid" style="grid-template-columns:1fr 1fr;">
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" required minlength="6">
        </div>
        <div class="form-group">
          <label>Confirm Password</label>
          <input type="password" name="password2" required>
        </div>
      </div>
      <button type="submit" class="btn" style="width:100%;justify-content:center;margin-top:4px;">Create Account</button>
    </div>
  </form>
  <?php endif; ?>

  <div class="auth-switch">Already have an account? <a href="/ccrims/index.php">Sign in</a></div>
</div>
</main>
</body>
</html>
