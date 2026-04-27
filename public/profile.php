<?php
require_once __DIR__ . '/../src/config.php';
requireLogin();

$stmt = getPDO()->prepare('SELECT user_id, name, email, phone, role FROM Users WHERE user_id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    exit('User not found.');
}

$error = flash_message('flash_error');
$success = flash_message('flash_success');
$display_name = strip_tags(trim($user['name']));
if ($display_name === '') {
    $display_name = 'User';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include __DIR__ . '/navbar.php'; ?>
<div class="page-container customer-shell">
    <section class="customer-hero">
        <div class="hero-copy">
            <p class="eyebrow">Account Settings</p>
            <h1>My Profile</h1>
            <p class="muted">Update your contact information and change your password here.</p>
        </div>
        <div class="profile-meta">
            <div class="stat-chip">
                <strong><?= esc($display_name) ?></strong>
                <span>Signed-in user</span>
            </div>
            <div class="stat-chip">
                <strong><?= esc($user['role']) ?></strong>
                <span>Account role</span>
            </div>
        </div>
    </section>

    <?php if ($error): ?>
        <div class="alert-inline error"><?= esc($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert-inline success"><?= esc($success) ?></div>
    <?php endif; ?>

    <div class="profile-grid">
        <section class="profile-card">
            <h2>Edit Details</h2>
            <form class="form-stack" action="../src/profile_update.php" method="POST">
                <?= csrf_input() ?>
                <input type="hidden" name="id" value="<?= (int) $user['user_id'] ?>">
                <div>
                    <label for="full_name">Full Name</label>
                    <input id="full_name" type="text" name="full_name" value="<?= esc($display_name) ?>" required>
                </div>
                <div>
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="<?= esc($user['email']) ?>" required>
                </div>
                <div>
                    <label for="phone">Phone</label>
                    <input id="phone" type="text" name="phone" value="<?= esc($user['phone'] ?? '') ?>">
                </div>
                <button type="submit" class="button">Update Profile</button>
            </form>
        </section>

        <section class="profile-card">
            <h2>Change Password</h2>
            <p class="muted">Use at least 8 characters.</p>
            <form class="form-stack" action="../src/password_update.php" method="POST">
                <?= csrf_input() ?>
                <div>
                    <label for="new_password">New Password</label>
                    <input id="new_password" type="password" name="new_password" minlength="8" required>
                </div>
                <button type="submit" class="button">Change Password</button>
            </form>
        </section>
    </div>
</div>
</body>
</html>
<?php include __DIR__ . '/../src/templates/footer.php'; ?>