<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';

// Check if the user has the required role
requireRole('admin');

// Check if the task ID is provided
if (isset($_GET['id'])) {
    $task_id = (int)$_GET['id'];

    // Delete the task from the database
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$task_id]);

    // Log the action
    logAction($pdo, $_SESSION['user_id'], 'DELETE', 'tasks', null, "Deleted task: {$task['title']}");
    // Redirect to the tasks list
    header('Location: view_tasks.php');
    exit;
} else {
    die('Task ID not provided.');
}
?>
