<?php
require_once "../../includes/db.php";
require_once "../../includes/auth.php";
require_once "../../includes/admin/admin_header.php";

$id = $_GET['id'] ?? null;
$user = null;

if ($id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
}
?>

<h1><?= $id ? "Edit User" : "Add User" ?></h1>

<form action="user_save.php" method="POST">
    <input type="hidden" name="id" value="<?= $user['id'] ?? '' ?>">

    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control"
               value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control"
               value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-control" required>
            <option value="user" <?= isset($user['role']) && $user['role']=="user" ? "selected":"" ?>>User</option>
            <option value="admin" <?= isset($user['role']) && $user['role']=="admin" ? "selected":"" ?>>Admin</option>
        </select>
    </div>

    <?php if (!$id): ?>
    <div class="mb-3">
        <label>Password (only if creating user)</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <?php endif; ?>

    <button class="btn btn-primary">Save</button>
</form>

<?php require_once "../../includes/admin/admin_footer.php"; ?>