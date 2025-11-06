<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_login_redirect('login.php');

$user = current_user($pdo);
if (!$user) { header('Location: login.php'); exit; }

$msg = '';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['first_name'] ?? '');
    $last = trim($_POST['last_name'] ?? '');
    $fav = trim($_POST['favorite_chapter'] ?? '');
    if ($first === '') $errors[] = 'First name is required';
    if ($last === '') $errors[] = 'Last name is required';
    if (!$errors) {
        $upd = $pdo->prepare('UPDATE users SET first_name = :f, last_name = :l, favorite_chapter = :c WHERE id = :id');
        $upd->execute([':f'=>$first, ':l'=>$last, ':c'=>($fav !== '' ? $fav : null), ':id'=>$user['id']]);
        $msg = 'Profile updated';
        $user = current_user($pdo);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Your Profile</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <?php include __DIR__ . '/header.php'; ?>
  <main class="container">
    <header class="page-header">
      <h1 class="page-title">Welcome, <?= htmlspecialchars($user['first_name'].' '.$user['last_name'], ENT_QUOTES, 'UTF-8') ?></h1>
      <p class="page-subtitle">Signed in as <?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></p>
    </header>

    <?php if ($msg): ?>
      <aside class="panel" role="status"><p class="panel__desc"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></p></aside>
    <?php endif; ?>
    <?php if ($errors): ?>
      <aside class="panel" role="alert">
        <h2 class="panel__title">Please fix the following</h2>
        <ul>
          <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?>
        </ul>
      </aside>
    <?php endif; ?>

    <section class="panel">
      <h2 class="panel__title">Profile</h2>
      <form class="form" method="post" action="profile.php">
        <div class="form__field">
          <label class="form__label" for="first_name">First Name</label>
          <input class="form__control" type="text" id="first_name" name="first_name" required value="<?= htmlspecialchars($user['first_name'], ENT_QUOTES, 'UTF-8') ?>" autocomplete="given-name">
        </div>
        <div class="form__field">
          <label class="form__label" for="last_name">Last Name</label>
          <input class="form__control" type="text" id="last_name" name="last_name" required value="<?= htmlspecialchars($user['last_name'], ENT_QUOTES, 'UTF-8') ?>" autocomplete="family-name">
        </div>
        <div class="form__field">
          <label class="form__label" for="favorite_chapter">Favorite Chapter</label>
          <input class="form__control" type="text" id="favorite_chapter" name="favorite_chapter" value="<?= htmlspecialchars((string)($user['favorite_chapter'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form__actions">
          <button class="btn btn--primary" type="submit">Save</button>
          <a class="btn" href="logout.php">Sign out</a>
        </div>
      </form>
    </section>
  </main>
</body>
</html>
