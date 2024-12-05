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
    <div class="container">
        <div class="dashboard">
            <img src="hihoo.gif" alt="Business GIF" class="greetings-image">
            <h2>Welcome, <?= htmlspecialchars($username) ?>!</h2>
            <p>Your role: <strong><?= ucfirst($role) ?></strong></p>

            <div class="dashboard-content">
            <?php if ($role === 'admin'): ?>
    <div class="panel-container">
        <div class="panel admin-panel">
            <h3 class="admin-header">Admin Panel</h3>
            <ul class="button-grid">
    <li>
        <a href="admin/manage_users.php" class="button">
            <i class="fas fa-user"></i> Manage Users
        </a>
    </li>
    <li>
        <a href="admin/view_tasks.php" class="button">
            <i class="fas fa-tasks"></i> View All Tasks
        </a>
    </li>
    <li>
        <a href="admin/view_logs.php" class="button">
            <i class="fas fa-book"></i> View Logs
        </a>
    </li>
    <li>
        <a href="admin/user_task_activity_summary.php" class="button">
            <i class="fas fa-file-alt"></i> User Activity Summary
        </a>
    </li>
</ul>


        </div>
    </div>



                <?php elseif ($role === 'manager'): ?>
                    <!-- Manager panel container -->
                    <div class="panel-container">
                        <div class="panel manager-panel">
                            <h3 class="manager-header">Manager Panel</h3> <!-- Custom class for Manager -->
                            <ul>
                                <li><a href="manager/create_task.php">Create Task</a></li>
                                <li><a href="manager/view_tasks.php">View Tasks</a></li>
                            </ul>
                        </div>
                    </div>
                <?php elseif ($role === 'employee'): ?>
                    <!-- Employee panel container -->
                    <div class="panel-container">
                        <div class="panel employee-panel">
                            <h3 class="employee-header">Employee Panel</h3> <!-- Custom class for Employee -->
                            <ul>
                                <li><a href="employee/view_tasks.php">View Assigned Tasks</a></li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<link href="https://fonts.googleapis.com/css2?family=Roboto+Slab&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">




<?php include 'includes/footer.php'; ?>