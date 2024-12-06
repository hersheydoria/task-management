<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Redirect to dashboard or homepage if already logged in
    header('Location: dashboard.php');
    exit();
}

include 'includes/db.php';
include 'includes/auth.php'; // Include the logAction function

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'employee'; // Default role
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            $error = "Username already exists.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $hashed_password, $role])) {
                $success = "Registration successful! You can now log in.";
            } else {
                $error = "Registration failed.";
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>
<div class="main-content">
    <div class="content-wrapper">
        <div class="welcome-message">
          <img src="/task_management/public/intro.png" alt="Task" class="welcome-image">
                <h3>Welcome to ManageMate, the starting line of your productivity journey! 
                Together, we’ll turn challenges into opportunities and goals into achievements. 
                Let's get to it!</h3>
            </div>
        <div class="form-container">
            <h2>Register</h2>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit">Register</button>
            </form>
            <p>Already have an account? <a href="index.php">Log in</a></p>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>