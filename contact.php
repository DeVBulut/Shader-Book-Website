<?php
declare(strict_types=1);

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '') $errors[] = 'Name is required';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
    if ($message === '' || strlen($message) < 10) $errors[] = 'Message must be at least 10 characters';

    if (!$errors) {
        $entry = [
            'ts' => date('c'),
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
        ];
        $dir = __DIR__ . DIRECTORY_SEPARATOR . 'data';
        if (!is_dir($dir)) @mkdir($dir, 0775, true);
        $file = $dir . DIRECTORY_SEPARATOR . 'contact_messages.jsonl';
        $line = json_encode($entry, JSON_UNESCAPED_SLASHES) . PHP_EOL;
        file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
        $success = 'Thanks! Your message has been received.';
        // Clear POST values after success
        $_POST = [];
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shader Book — Contact</title>
    <link rel="stylesheet" href="styles.css">
  </head>
  <body>
    <?php include __DIR__ . '/header.php'; ?>
    <main class="container">
      <header class="page-header">
        <h1 class="page-title">Contact</h1>
        <p class="page-subtitle">Send us a note — this form is processed with PHP (no database).</p>
      </header>

      <?php if ($success): ?>
        <aside class="panel" role="status"><p class="panel__desc"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p></aside>
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
        <h2 class="panel__title">Message</h2>
        <form class="form" method="post" action="contact.php" novalidate>
          <div class="form__field">
            <label class="form__label" for="name">Name</label>
            <input class="form__control" type="text" id="name" name="name" required autocomplete="name" value="<?= htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          </div>
          <div class="form__field">
            <label class="form__label" for="email">Email</label>
            <input class="form__control" type="email" id="email" name="email" required autocomplete="email" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          </div>
          <div class="form__field">
            <label class="form__label" for="subject">Subject (optional)</label>
            <input class="form__control" type="text" id="subject" name="subject" value="<?= htmlspecialchars($_POST['subject'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          </div>
          <div class="form__field">
            <label class="form__label" for="message">Message</label>
            <textarea class="form__control" id="message" name="message" rows="6" required><?= htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
          </div>
          <div class="form__actions">
            <button class="btn btn--primary" type="submit">Send</button>
            <a class="btn" href="index.php">Cancel</a>
          </div>
        </form>
      </section>
    </main>
    <footer class="site-footer">
      <div class="container">
        <p>&copy; <span id="year"></span> Shader Book</p>
      </div>
    </footer>
    <script>document.getElementById('year').textContent = new Date().getFullYear();</script>
  </body>
  </html>

