<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
$u = current_user($pdo);
?>
<header class="site-header">
  <div class="container site-header__inner">
    <a class="site-brand" href="index.php">SUU Forms</a>
    <nav class="nav" aria-label="Main">
      <a class="nav__link" href="index.php">Home</a>
      <?php if ($u): ?>
        <a class="nav__link" href="profile.php">Profile</a>
        <span class="nav__text">Hello, <?= htmlspecialchars($u['first_name'], ENT_QUOTES, 'UTF-8') ?></span>
        <a class="nav__link" href="logout.php">Logout</a>
      <?php else: ?>
        <a class="nav__link" href="login.php">Login</a>
        <a class="nav__link" href="register.php">Register</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

