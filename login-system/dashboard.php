<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="page-login">
<main class="stage">
    <section class="card welcome">
        <h1 class="card-title">Welcome, <?= e($_SESSION['username']) ?></h1>
        <p class="card-sub">You're logged in.</p>
        <a class="btn" href="logout.php">Log out</a>
    </section>
</main>
</body>
</html>
