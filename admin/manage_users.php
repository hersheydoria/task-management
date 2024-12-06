<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

// Ensure the user is logged in and has the 'admin' role
requireRole('admin'); // Only admins can access this page

// Fetch all users except the admin
$stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE role != 'admin'");
$stmt->execute();
$users = $stmt->fetchAll();

$user_id = $_SESSION['user_id']; // Get the logged-in user ID
$pdo->exec("SET myapp.current_user_id = '$user_id'");

// Handle the POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update user role
    if (isset($_POST['user_id'], $_POST['role'])) {
        $user_id = (int)$_POST['user_id'];
        $new_role = $_POST['role'];

        // Ensure only valid roles can be set
        if (in_array($new_role, ['manager', 'employee'])) {
            // Fetch the old role for logging
            $roleStmt = $pdo->prepare("SELECT role, username FROM users WHERE id = ? AND role != 'admin'");
            $roleStmt->execute([$user_id]);
            $user = $roleStmt->fetch();

            if ($user) {
                $old_role = $user['role'];
                $username = $user['username'];

                // Update the user's role
                $updateStmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ? AND role != 'admin'");
                $updateStmt->execute([$new_role, $user_id]);

                $success = "User role updated successfully.";
            } else {
                $error = "User not found.";
            }
        } else {
            $error = "Invalid role selected.";
        }
    }

    if (isset($_POST['delete_user_id'])) {
        $delete_user_id = (int)$_POST['delete_user_id'];
    
        try {
            // Reassign tasks to another user or nullify relationships
            $pdo->prepare("UPDATE tasks SET created_by = NULL WHERE created_by = ?")->execute([$delete_user_id]);
            $pdo->prepare("UPDATE tasks SET assigned_to = NULL WHERE assigned_to = ?")->execute([$delete_user_id]);
    
            // Delete the user
            $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
            $deleteStmt->execute([$delete_user_id]);
    
            if ($deleteStmt->rowCount() > 0) {
                $success = "User deleted successfully.";
            } else {
                $error = "Failed to delete user or user does not exist.";
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
    
}

// Fetch the updated users list
$stmt->execute();
$users = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<!-- Container for the Manage Users section -->
<div class="container">
    <h2 style="color: white; font-family: 'Roboto Slab', serif;"><i class="fas fa-user"></i> Manage Users</h2>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

    <!-- Table Container -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="color: white;">Username</th>
                    <th style="color: white;">Role</th>
                    <th style="color: white;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td>
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <select name="role">
                                    <option value="employee" <?= $user['role'] === 'employee' ? 'selected' : '' ?>>Employee</option>
                                    <option value="manager" <?= $user['role'] === 'manager' ? 'selected' : '' ?>>Manager</option>
                                </select>
                                <button type="submit">Update</button>
                            </form>
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="delete_user_id" value="<?= $user['id'] ?>">
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <a href="add_user.php" class="button" style="margin-top: 10px;">Add New User</a>
</div>
<?php include '../includes/footer.php'; ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
