<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['first_name'] ?? '');
    $last = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirm'] ?? '';
    $fav = trim($_POST['favorite_chapter'] ?? '');

    if ($first === '') $errors[] = 'First name is required';
    if ($last === '') $errors[] = 'Last name is required';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
    if ($pass === '' || strlen($pass) < 6) $errors[] = 'Password must be at least 6 characters';
    if ($pass !== $confirm) $errors[] = 'Passwords must match';

    if (!$errors) {
        $exists = $pdo->prepare('SELECT id FROM users WHERE email = :e');
        $exists->execute([':e' => $email]);
        if ($exists->fetch()) {
            $errors[] = 'An account with that email already exists';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $ins = $pdo->prepare('INSERT INTO users(first_name,last_name,email,password_hash,favorite_chapter) VALUES(:f,:l,:e,:p,:c)');
            $ins->execute([':f'=>$first, ':l'=>$last, ':e'=>$email, ':p'=>$hash, ':c'=>$fav !== '' ? $fav : null]);
            $_SESSION['user_id'] = (int)$pdo->lastInsertId();
            header('Location: profile.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create Account</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <?php include __DIR__ . '/header.php'; ?>
  <main class="container">
    <header class="page-header">
      <h1 class="page-title">Create Account</h1>
    </header>

    <?php if ($errors): ?>
      <aside class="panel" role="alert">
        <h2 class="panel__title">Please fix the following</h2>
        <ul>
          <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?>
        </ul>
      </aside>
    <?php endif; ?>

    <form class="form" method="post" action="register.php" autocomplete="on">
      <div class="form__field">
        <label class="form__label" for="first_name">First Name</label>
        <input class="form__control" type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required autocomplete="given-name">
      </div>
      <div class="form__field">
        <label class="form__label" for="last_name">Last Name</label>
        <input class="form__control" type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required autocomplete="family-name">
      </div>
      <div class="form__field">
        <label class="form__label" for="email">Email</label>
        <input class="form__control" type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required autocomplete="email">
      </div>
      <div class="form__field">
        <label class="form__label" for="password">Password</label>
        <input class="form__control" type="password" id="password" name="password" required autocomplete="new-password">
      </div>
      <div class="form__field">
        <label class="form__label" for="password_confirm">Confirm Password</label>
        <input class="form__control" type="password" id="password_confirm" name="password_confirm" required autocomplete="new-password">
      </div>
      <div class="form__field">
        <label class="form__label" for="favorite_chapter">Favorite Chapter (optional)</label>
        <input class="form__control" type="text" id="favorite_chapter" name="favorite_chapter" value="<?= htmlspecialchars($_POST['favorite_chapter'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
      </div>
      <div class="form__actions">
        <button class="btn btn--primary" type="submit">Create Account</button>
        <a class="btn" href="login.php">I already have an account</a>
      </div>
    </form>
  </main>
</body>
</html>
