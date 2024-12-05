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

$user_id = $_SESSION['user_id']; // Get the logged-in user ID
$pdo->exec("SET myapp.current_user_id = '$user_id'");

// Handle the POST request to update user roles
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['role'])) {
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
 
// Fetch the updated users list
$stmt->execute();
$users = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>
<!-- Container for the Manage Users section -->
<div class="container" style="max-width: 100%; margin: 30px auto; padding: 40px; font-family: 'Dancing Script', cursive; font-size: 0.9rem; text-align: center; display: flex; flex-direction: column; align-items: center;">
    <h2 style="font-size: 2.0rem; margin-top: -10px; margin-bottom: 0px;"><i class="fas fa-user"></i> Manage Users</h2> <!-- Adjust font size here -->
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

    <!-- Table Container -->
    <div class="table-container" style="margin-top: 10px; max-width: 1200px; margin: 10px; padding: 20px; border: 1px solid #ddd; border-radius: 20px; background-color: #ebd3f8;">
        <table style="width: 100%; border-collapse: collapse; text-align: center; background-color: #ebd3f8; border: 2px solid #333;">
            <thead>
                <tr>
                    <th style="padding: 10px; border: 2px solid #2e073f; background-color: #ebd3f8; text-align: center; color: #2e073f;">Username</th>
                    <th style="padding: 10px; border: 2px solid #2e073f; background-color: #ebd3f8; text-align: center; color: #2e073f;">Role</th>
                    <th style="padding: 10px; border: 2px solid #2e073f; background-color: #ebd3f8; text-align: center; color: #2e073f;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td style="padding: 10px; border: 2px solid #2e073f; color: #2e073f; font-family: 'Dancing Script', cursive;"><?= htmlspecialchars($user['username']) ?></td>
                        <td style="padding: 10px; border: 2px solid #2e073f; color: #2e073f; font-family: 'Dancing Script', cursive;"><?= htmlspecialchars($user['role']) ?></td>
                        <td style="padding: 10px; border: 2px solid #2e073f; ">
    <form method="POST" style="display: flex; align-items: center; gap: 5px; color: #2e073f;">
        <input type="hidden" name="user_id" value="<?= $user['id'] ?>" style="color: #000;">
        <select name="role" style="padding: 5px;  border: 1.5px solid #2e073f;color: #2e073f; border-radius: 4px; background-color: #ebd3f8; margin-right: 10px; font-family: 'Dancing Script', cursive;">
            <option value="employee" <?= $user['role'] === 'employee' ? 'selected' : '' ?> style="color: #ebd3f8 font-family: 'Dancing Script', cursive;">Employee</option>
            <option value="manager" <?= $user['role'] === 'manager' ? 'selected' : '' ?> style="color: #2eo73f; font-family: 'Dancing Script', cursive;">Manager</option>
        </select>
        <button type="submit" style=" padding: 5px 10px; background-color: #2e073f; color: #ebd3f8; border: none; border-radius: 4px; cursor: pointer; font-family: 'Dancing Script', cursive;">Update</button>
    </form>
</td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">