<?php
require_once __DIR__ . '/../src/config.php';
if (isLoggedIn()) {
    redirect_to_role_dashboard($_SESSION['role'] ?? 'customer');
}

$error = flash_message('flash_error');
$success = flash_message('flash_success');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        verify_csrf_or_die();
        enforce_login_throttle();

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            throw new RuntimeException('Enter a valid email and password.');
        }

        $stmt = getPDO()->prepare('SELECT user_id, name, email, password, role FROM Users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            register_failed_login_attempt();
            throw new RuntimeException('Invalid email or password.');
        }

        complete_login($user);
    } catch (RuntimeException $e) {
        $error = $e->getMessage();
    } catch (Throwable $e) {
        error_log('Login failed unexpectedly: ' . $e->getMessage());
        $error = 'Unable to log in right now. Please try again later.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
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
            <h2>Login</h2>
            <p class="muted">Enter your account details below.</p>
            <?php if ($error): ?>
                <div class="alert-inline error"><?= esc($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert-inline success"><?= esc($success) ?></div>
            <?php endif; ?>
            <form class="form-stack" method="POST" style="margin-top:1rem;">
                <?= csrf_input() ?>
                <div>
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" required placeholder="name@example.com">
                </div>
                <div>
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required placeholder="Enter your password">
                </div>
                <button type="submit" class="button">Login</button>
            </form>
            <p class="auth-footer-link muted">Need an account? <a class="auth-link-inline" href="register.php">Create one here</a>.</p>
        </section>

        <section class="auth-info">
            <h3>Why use our pharmacy portal?</h3>
            <ul>
                <li><strong>Place orders easily:</strong> Browse medicines, add items to cart, and complete your order in a few steps.</li>
                <li><strong>Upload prescriptions:</strong> Send the required file when your order includes prescription-only medicine.</li>
                <li><strong>Track progress:</strong> Check order status, view details, and keep your profile information up to date.</li>
            </ul>
        </section>
    </div>
</div>
</body>
</html>
