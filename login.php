<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    if ($email === '' || $pass === '') {
        $error = 'Email and password are required';
    } else {
        $q = $pdo->prepare('SELECT id, password_hash FROM users WHERE email = :e');
        $q->execute([':e' => $email]);
        $row = $q->fetch();
        if ($row && password_verify($pass, $row['password_hash'])) {
            $_SESSION['user_id'] = (int)$row['id'];
            header('Location: profile.php');
            exit;
        } else {
            $error = 'Invalid email or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign In</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <?php include __DIR__ . '/header.php'; ?>
  <main class="container">
    <header class="page-header">
      <h1 class="page-title">Sign In</h1>
    </header>

    <?php if ($error): ?>
      <aside class="panel" role="alert">
        <h2 class="panel__title">Sign in failed</h2>
        <p class="panel__desc"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
      </aside>
    <?php endif; ?>

    <form class="form" method="post" action="login.php" autocomplete="on">
      <div class="form__field">
        <label class="form__label" for="email">Email</label>
        <input class="form__control" type="email" id="email" name="email" required autocomplete="email" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
      </div>
      <div class="form__field">
        <label class="form__label" for="password">Password</label>
        <input class="form__control" type="password" id="password" name="password" required autocomplete="current-password">
      </div>
      <div class="form__actions">
        <button class="btn btn--primary" type="submit">Sign In</button>
        <a class="btn" href="register.php">Create an account</a>
      </div>
    </form>
  </main>
</body>
</html>
