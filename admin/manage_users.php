<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../includes/db.php';
include '../includes/auth.php';

// Ensure the user is logged in and has the 'admin' role
requireRole('admin'); // Only admins can access this page

// Fetch all users except the admin
$stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE role != 'admin'");
$stmt->execute();
$users = $stmt->fetchAll();

// Handle the POST request to update user roles
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['role'])) {
    $user_id = (int)$_POST['user_id'];
    $role = $_POST['role'];

    // Ensure only valid roles can be set
    if (in_array($role, ['manager', 'employee'])) {
        $updateStmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ? AND role != 'admin'");
        $updateStmt->execute([$role, $user_id]);
        $success = "User role updated successfully.";
    } else {
        $error = "Invalid role selected.";
    }
}

// Fetch the updated users list
$stmt->execute();
$users = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<div class="admin-panel">
    <h2>Manage Users</h2>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <select name="role">
                                <option value="employee" <?= $user['role'] === 'employee' ? 'selected' : '' ?>>Employee</option>
                                <option value="manager" <?= $user['role'] === 'manager' ? 'selected' : '' ?>>Manager</option>
                            </select>
                            <button type="submit">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>