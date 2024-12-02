<?php
include '../includes/db.php';
session_start();
checkRole('manager');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $assigned_to = $_POST['assigned_to'];
    $deadline = $_POST['deadline'];
    $priority = $_POST['priority'];
    $created_by = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO tasks (title, description, assigned_to, created_by, deadline, priority) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $assigned_to, $created_by, $deadline, $priority]);

    header('Location: dashboard.php');
}
?>
