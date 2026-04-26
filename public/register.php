<?php
require_once __DIR__ . '/../src/config.php';
if (isLoggedIn()) {
    redirect_to('products.php');
}
$error = flash_message('flash_error');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .auth-page { min-height: 100vh; padding: 22px 18px 90px; box-sizing: border-box; }
        .auth-shell { max-width: 760px; margin: 0 auto; display: grid; gap: 22px; }
        .auth-header { text-align: center; margin-top: 4px; }
        .auth-logo { width: 82px; height: 82px; margin: 0 auto 12px; border-radius: 50%; background: linear-gradient(135deg, #2f7d4b, #1f5a34); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.45rem; font-weight: 800; box-shadow: 0 16px 30px rgba(31, 90, 52, 0.18); }
        .auth-title { margin: 0; font-size: clamp(2.1rem, 5vw, 3.2rem); color: #173f2a; }
        .auth-phone { margin: 10px 0 0; color: #2f7d4b; font-size: 1.05rem; font-weight: 700; }
        .auth-card-local, .auth-info { background: rgba(255,255,255,0.96); border: 1px solid #d8e8de; border-radius: 26px; box-shadow: 0 18px 36px rgba(18, 63, 42, 0.08); }
        .auth-card-local { max-width: 520px; width: 100%; margin: 0 auto; padding: 30px; }
        .auth-card-local h2 { margin: 0 0 10px; font-size: 2.2rem; color: #173f2a; }
        .auth-card-local .button { width: 100%; }
        .auth-info { max-width: 700px; margin: 0 auto; padding: 26px 28px; }
        .auth-info h3 { margin: 0 0 14px; color: #173f2a; font-size: 1.45rem; text-align: center; }
        .auth-info ul { margin: 0; padding-left: 22px; color: #345545; }
        .auth-info li { margin-bottom: 14px; line-height: 1.55; }
        .auth-footer-link { margin-top: 18px; text-align: center; }
        .auth-link-inline { color: #1f5a34; font-weight: 700; text-decoration: none; }
        .auth-social { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; margin-top: 8px; }
        .auth-social a { padding: 11px 16px; border-radius: 999px; background: #1f5a34; color: #fff; text-decoration: none; font-weight: 700; box-shadow: 0 10px 22px rgba(31, 90, 52, 0.18); }
        .auth-social a:hover { background: #2f7d4b; }
        @media (max-width: 700px) {
            .auth-page { padding: 18px 14px 50px; }
            .auth-card-local, .auth-info { padding: 22px; }
        }
    </style>
</head>
<body>
<div class="auth-page">
    <div class="auth-shell">
        <div class="auth-header">
            <div class="auth-logo">JJ</div>
            <h1 class="auth-title">Johnson&Johnson</h1>
            <p class="auth-phone">+94 11 234 5678</p>
        </div>

        <section class="auth-card-local">
            <h2>Create Account</h2>
            <p class="muted">Fill in your details to create a customer account.</p>
            <?php if ($error): ?>
                <div class="alert-inline error"><?= esc($error) ?></div>
            <?php endif; ?>
            <form class="form-stack" action="../src/auth_register.php" method="post" novalidate style="margin-top:1rem;">
                <?= csrf_input() ?>
                <div>
                    <label for="fullname">Full Name</label>
                    <input id="fullname" required name="fullname" type="text" maxlength="100" placeholder="Your full name">
                </div>
                <div>
                    <label for="email">Email</label>
                    <input id="email" required name="email" type="email" maxlength="150" placeholder="name@example.com">
                </div>
                <div>
                    <label for="password">Password</label>
                    <input id="password" required name="password" type="password" minlength="8" placeholder="At least 8 characters">
                </div>
                <button type="submit" class="button">Create Account</button>
            </form>
            <p class="auth-footer-link muted">Already registered? <a class="auth-link-inline" href="login.php">Login here</a>.</p>
        </section>

        <section class="auth-info">
            <h3>What you can do here</h3>
            <ul>
                <li><strong>Create a customer account:</strong> Order medicines, review purchases, and upload prescription files when needed.</li>
                <li><strong>Manage your orders:</strong> Track order progress, update your profile, and keep your account details current.</li>
                <li><strong>Get started quickly:</strong> Fill in your details below and begin using the pharmacy portal right away.</li>
            </ul>
        </section>

        <div class="auth-social">
            <a href="https://instagram.com" target="_blank" rel="noopener noreferrer">Instagram</a>
            <a href="https://facebook.com" target="_blank" rel="noopener noreferrer">Facebook</a>
        </div>
    </div>
</div>
</body>
</html>
