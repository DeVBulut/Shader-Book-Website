<?php
declare(strict_types=1);

// Lightweight header that does not require the database.
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$isAuthed = isset($_SESSION['user_id']);
?>
<header class="site-header">
  <div class="container site-header__inner">
    <a class="site-brand" href="index.php">Shader Book</a>
    <nav class="nav" aria-label="Main">
      <a class="nav__link" href="index.php">Home</a>
      <a class="nav__link" href="about.php">About</a>
      <a class="nav__link" href="chapters.php">Chapters</a>
      <a class="nav__link" href="chapter-1.php">Chapter 1</a>
      <a class="nav__link" href="chapter-2.php">Chapter 2</a>
      <a class="nav__link" href="glossary.php">Glossary</a>
      <a class="nav__link" href="video.php">Video</a>
      <a class="nav__link" href="css-demo.php">CSS Demo</a>
      <a class="nav__link" href="assignment.php">Assignment/Report</a>
      <a class="nav__link" href="functions.php">Function Playground</a>
      <a class="nav__link" href="contact.php">Contact</a>
      <?php if ($isAuthed): ?>
        <a class="nav__link" href="profile.php">Profile</a>
        <a class="nav__link" href="logout.php">Logout</a>
      <?php else: ?>
        <a class="nav__link" href="login.php">Login</a>
        <a class="nav__link" href="register.php">Register</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
