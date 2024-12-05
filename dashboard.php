<?php
session_start();
include 'includes/db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username, role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Check if user exists
if (!$user) {
    header('Location: logout.php');
    exit;
}

// Define user role
$role = $user['role'];
$username = $user['username'];

// Display appropriate content based on role
?>
<?php include 'includes/header.php'; ?>
<div class="main-content">
    <div class="dashboard">
         <img src="hihoo.gif" alt="Business GIF" class="grettings-image">
        <h2>Welcome, <?= htmlspecialchars($username) ?>!</h2>
        <p>Your role: <strong><?= ucfirst($role) ?></strong></p>
        <div class="dashboard-content">
            <?php if ($role === 'admin'): ?>
                <h3>Admin Panel</h3>
                <ul>
                    <li><a href="admin/manage_users.php">Manage Users</a></li>
                    <li><a href="admin/view_tasks.php">View All Tasks</a></li>
                    <li><a href="admin/view_logs.php">View Logs</a></li>
                    <li><a href="admin/user_task_activity_summary.php">User Activity Summary</a></li>
                </ul>
            <?php elseif ($role === 'manager'): ?>
                <h3>Manager Panel</h3>
                <ul>
                    <li><a href="manager/create_task.php">Create Task</a></li>
                    <li><a href="manager/view_tasks.php">View Tasks</a></li>
                </ul>
            <?php elseif ($role === 'employee'): ?>
                <h3>Employee Panel</h3>
                <ul>
                    <li><a href="employee/view_tasks.php">View Assigned Tasks</a></li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<link href="https://fonts.googleapis.com/css2?family=Roboto+Slab&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">

<?php include 'includes/footer.php'; ?>