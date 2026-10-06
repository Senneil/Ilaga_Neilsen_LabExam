<?php
require 'config.php';

if (isset($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }

$errors   = [];
$username = $_COOKIE['remember_user'] ?? '';
$success  = flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean('username');
    $password = $_POST['password'] ?? '';

    if (!csrf_valid())      $errors['form'] = 'Your session expired. Please try again.';
    if ($username === '')   $errors['username'] = 'Enter your username.';
    if ($password === '')   $errors['password'] = 'Enter your password.';

    if (!$errors) {
        $stmt = db()->prepare('SELECT * FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];

            if (isset($_POST['remember'])) setcookie('remember_user', $user['username'], time() + 60 * 60 * 24 * 30, '/', '', false, true);
            else                           setcookie('remember_user', '', time() - 3600, '/');

            header('Location: dashboard.php'); exit;
        }
        $errors['form'] = 'Incorrect username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="page-login">
<main class="stage">
    <form class="card" method="post" action="login.php" novalidate>
        <h1 class="card-title">Login</h1>

        <?php if ($success): ?><p class="alert alert-success" role="status"><?= e($success) ?></p><?php endif; ?>
        <?php if (isset($errors['form'])): ?><p class="alert alert-error" role="alert"><?= e($errors['form']) ?></p><?php endif; ?>

        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

        <div class="field icon-right">
            <input type="text" name="username" placeholder="Username" value="<?= e($username) ?>" autocomplete="username" aria-label="Username" <?= isset($errors['username']) ? 'aria-invalid="true"' : '' ?>>
            <?= icon('user') ?>
        </div>
        <?php if (isset($errors['username'])): ?><p class="error"><?= e($errors['username']) ?></p><?php endif; ?>

        <div class="field icon-right">
            <input type="password" name="password" placeholder="Password" autocomplete="current-password" aria-label="Password" <?= isset($errors['password']) ? 'aria-invalid="true"' : '' ?>>
            <button type="button" class="toggle" aria-label="Show password"><?= icon('lock') ?></button>
        </div>
        <?php if (isset($errors['password'])): ?><p class="error"><?= e($errors['password']) ?></p><?php endif; ?>

        <div class="row">
            <label class="check"><input type="checkbox" name="remember" <?= isset($_COOKIE['remember_user']) ? 'checked' : '' ?>> Remember me</label>
            <a href="#" class="muted-link">Forgot password?</a>
        </div>

        <button type="submit" class="btn">Log in</button>

        <p class="switch">Don't have account? <a href="register.php">Register</a></p>
    </form>
</main>
<script src="assets/js/app.js"></script>
</body>
</html>
