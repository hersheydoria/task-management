<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

$user_id = $_SESSION['user_id']; // Get the logged-in user ID
$pdo->exec("SET myapp.current_user_id = '$user_id'");

// Check if the task ID is provided
if (isset($_GET['id'])) {
    $task_id = (int)$_GET['id'];

    // Delete the task from the database
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$task_id]);


    // Redirect to the tasks list
    header('Location: view_tasks.php');
    exit;
} else {
    die('Task ID not provided.');
}
?>
