<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

// Ensure the user is logged in and has the 'admin' role
requireRole('admin'); // Only admins can access this page

$user_id = $_SESSION['user_id']; // Get the logged-in user ID
$pdo->exec("SET myapp.current_user_id = '$user_id'");

// Handle the POST request to add a new user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input from the form
    $new_username = trim($_POST['new_username']);
    $new_role = $_POST['new_role'];
    $new_password = $_POST['new_password']; // Get the password input

    // Validate input
    if (!empty($new_username) && in_array($new_role, ['manager', 'employee']) && !empty($new_password)) {
        // Hash the password for secure storage
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // Insert the new user into the database with the hashed password
        $insertStmt = $pdo->prepare("INSERT INTO users (username, role, password) VALUES (?, ?, ?)");
        $insertStmt->execute([$new_username, $new_role, $hashed_password]);

        // Success message and redirect to manage_users.php
        $success = "New user added successfully.";
        
        // Redirect to manage_users.php after adding the user
        header("Location: manage_users.php");
        exit(); // Always call exit() after a redirect to stop further script execution
    } else {
        $error = "Please provide a valid username, password, and role.";
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="container">
    <h2 style="color: white; font-family: 'Roboto Slab', serif;"><i class="fas fa-pencil-alt"></i>Add New User</h2>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

    <!-- Add User Form -->
    <form method="POST" style="margin-top: 10px;">
        <label for="new_username" style="color: white;">Username:</label>
        <input type="text" name="new_username" required>

        <label for="new_password" style="color: white;">Password:</label>
        <input type="password" name="new_password" required>

        <label for="new_role" style="color: white;">Role:</label>
        <select name="new_role" required>
            <option value="employee">Employee</option>
            <option value="manager">Manager</option>
        </select>
        <button type="submit">Add User</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
