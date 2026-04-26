<?php
require_once "../../includes/db.php";
require_once "../../includes/auth.php";
require_once "../../includes/admin/admin_header.php";

$result = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<h1 class="mb-4">Users Management</h1>

<a href="user_edit.php" class="btn btn-success mb-3">Add New User</a>

<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>Email</th>
        <th>Name</th>
        <th>Role</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['role']) ?></td>
        <td>
            <a class="btn btn-sm btn-primary" href="user_edit.php?id=<?= $row['id'] ?>">Edit</a>
            <a class="btn btn-sm btn-danger" href="user_delete.php?id=<?= $row['id'] ?>"
               onclick="return confirm('Delete this user?');">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<?php require_once "../../includes/admin/admin_footer.php"; ?>