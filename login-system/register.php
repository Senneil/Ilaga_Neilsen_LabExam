<?php
require 'config.php';

if (isset($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }

$errors = [];
$old    = ['username' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['username'] = clean('username');
    $old['email']    = clean('email');
    $password        = $_POST['password'] ?? '';
    $confirm         = $_POST['confirm'] ?? '';

    if (!csrf_valid()) $errors['form'] = 'Your session expired. Please try again.';

    // Username
    if ($old['username'] === '')                                   $errors['username'] = 'Choose a username.';
    elseif (!preg_match('/^[A-Za-z0-9_]{3,20}$/', $old['username'])) $errors['username'] = 'Use 3–20 letters, numbers or underscores.';

    // Email
    if ($old['email'] === '')                                      $errors['email'] = 'Enter your email address.';
    elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL))     $errors['email'] = 'Enter a valid email, like name@example.com.';

    // Password
    if ($password === '')                                          $errors['password'] = 'Create a password.';
    elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/\d/', $password))
                                                                   $errors['password'] = 'Use 8+ characters with upper, lower case and a number.';

    // Confirm
    if ($confirm === '')                                           $errors['confirm'] = 'Re-enter your password.';
    elseif ($password !== $confirm)                                $errors['confirm'] = 'Passwords do not match.';

    // Uniqueness
    if (!isset($errors['username']) && !isset($errors['email'])) {
        $stmt = db()->prepare('SELECT username, email FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$old['username'], $old['email']]);
        foreach ($stmt->fetchAll() as $row) {
            if (strcasecmp($row['username'], $old['username']) === 0) $errors['username'] = 'That username is taken.';
            if (strcasecmp($row['email'], $old['email']) === 0)       $errors['email']    = 'That email is already registered.';
        }
    }

    if (!$errors) {
        $stmt = db()->prepare('INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)');
        $stmt->execute([$old['username'], $old['email'], password_hash($password, PASSWORD_DEFAULT)]);
        flash('Account created. You can log in now.');
        header('Location: login.php'); exit;
    }
}

function field_error(array $errors, string $key): string
{
    return isset($errors[$key]) ? '<p class="error">' . e($errors[$key]) . '</p>' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="page-register">
<main class="stage stage-split">
    <form class="card" method="post" action="register.php" novalidate>
        <h1 class="card-title">Sign Up</h1>
        <p class="card-sub">Join us and create your own story.</p>

        <?php if (isset($errors['form'])): ?><p class="alert alert-error" role="alert"><?= e($errors['form']) ?></p><?php endif; ?>

        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

        <div class="field icon-left">
            <?= icon('user') ?>
            <input type="text" name="username" placeholder="Username" value="<?= e($old['username']) ?>" autocomplete="username" aria-label="Username" <?= isset($errors['username']) ? 'aria-invalid="true"' : '' ?>>
        </div>
        <?= field_error($errors, 'username') ?>

        <div class="field icon-left">
            <?= icon('mail') ?>
            <input type="email" name="email" placeholder="Email" value="<?= e($old['email']) ?>" autocomplete="email" aria-label="Email" <?= isset($errors['email']) ? 'aria-invalid="true"' : '' ?>>
        </div>
        <?= field_error($errors, 'email') ?>

        <div class="field icon-left">
            <button type="button" class="toggle" aria-label="Show password"><?= icon('lock') ?></button>
            <input type="password" name="password" placeholder="Password" autocomplete="new-password" aria-label="Password" <?= isset($errors['password']) ? 'aria-invalid="true"' : '' ?>>
        </div>
        <?= field_error($errors, 'password') ?>

        <div class="field icon-left">
            <button type="button" class="toggle" aria-label="Show confirm password"><?= icon('lock') ?></button>
            <input type="password" name="confirm" placeholder="Confirm Password" autocomplete="new-password" aria-label="Confirm password" <?= isset($errors['confirm']) ? 'aria-invalid="true"' : '' ?>>
        </div>
        <?= field_error($errors, 'confirm') ?>

        <p class="terms">By creating an account you agree to our Terms &amp; Privacy Policy.</p>

        <button type="submit" class="btn">Create Account</button>

        <p class="switch">Already have an account? <a href="login.php">Log in</a></p>
    </form>

    <section class="tagline">
        <h2>A NEW HORIZON<br>AWAITS</h2>
        <span class="rule"></span>
        <p>Create an account and discover what lies beyond the horizon.</p>
    </section>
</main>
<script src="assets/js/app.js"></script>
</body>
</html>
