<?php
session_start();
include 'includes/db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid credentials.";
    }
}
?>
<?php include 'includes/header.php'; ?>
<div class="main-content">
    <div class="content-wrapper">
        <div class="welcome-message">
            <img src="huhuhu.gif" alt="Business GIF" class="welcome-image">
            <h3>Welcome to ManageMate, the starting line of your productivity journey! 
             Together, we’ll turn challenges into opportunities and goals into achievements. 
             Let's get to it!</h3>
        </div>

        <div class="form-container">
            <h2>Login</h2>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
            <p>Don't have an account? <a href="register.php">Register</a></p>
        </div>
    </div>
</div>

<link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">

<?php include 'includes/footer.php'; ?>
