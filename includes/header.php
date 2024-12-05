<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/style.css?v=1.0">
<link rel="stylesheet" href="../assets/style.css?v=1.0">
    <title>ManageMate</title>
</head>
<body>
<header>
    <h1>ManageMate</h1>
    <?php if (isset($_SESSION['user_id'])): ?>
        <nav>
        <a href="/task_management/dashboard.php">Dashboard</a>
        <a href="/task_management/logout.php">Logout</a>
        </nav>
    <?php endif; ?>
</header>
