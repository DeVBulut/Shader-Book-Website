<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shader Book — Chapters</title>
    <link rel="stylesheet" href="styles.css">
  </head>
  <body>
    <?php include __DIR__ . '/header.php'; ?>
    <main class="container">
      <section>
        <h2>Contents</h2>
        <p>Browse the list of chapters below. Each chapter covers a shader topic.</p>
        <ul>
          <li><a href="chapter-1.php">Chapter 1: Getting Started</a></li>
          <li><a href="chapter-2.php">Chapter 2: Fragment Shaders</a></li>
        </ul>
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

