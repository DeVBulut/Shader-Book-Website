<?php
declare(strict_types=1);

// Lightweight header that does not require the database.
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$isAuthed = isset($_SESSION['user_id']);
?>
<header class="site-header">
  <div class="container site-header__inner">
    <a class="site-brand" href="index.php">
      <svg class="site-logo" aria-hidden="true" viewBox="0 0 64 64">
        <defs>
          <linearGradient id="grad-stroke" x1="8" y1="8" x2="56" y2="56" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#5dd6ff"/>
            <stop offset="1" stop-color="#9f7bff"/>
          </linearGradient>
          <linearGradient id="grad-bg" x1="12" y1="8" x2="52" y2="56" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#112037"/>
            <stop offset="1" stop-color="#0c1426"/>
          </linearGradient>
        </defs>
        <rect x="6" y="6" width="52" height="52" rx="14" fill="url(#grad-bg)" stroke="#25314a" stroke-width="2"/>
        <path d="M22 14h16c6.627 0 12 5.373 12 12s-5.373 12-12 12H26c-6.627 0-12 5.373-12 12s5.373 12 12 12h16"
              fill="none" stroke="url(#grad-stroke)" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/>
        <circle cx="22" cy="14" r="3" fill="#5dd6ff" opacity=".9"/>
        <circle cx="42" cy="50" r="3" fill="#9f7bff" opacity=".9"/>
      </svg>
      <span class="site-brand__text">Shader Book</span>
    </a>
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
