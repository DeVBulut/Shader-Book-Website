<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shader Book - Canvas Previews</title>
    <link rel="stylesheet" href="styles.css">
    <style>
      .report-grid { display:grid; gap:1rem; grid-template-columns: 1fr; }
      @media (min-width: 900px) { .report-grid { grid-template-columns: 2fr 1fr; } }
      .callout { background:#0f1524; border:1px solid var(--border); border-radius:.75rem; padding:1rem; }
      .muted { color: var(--muted); }
      .badge { display:inline-block; padding:.25rem .5rem; border:1px solid var(--border); border-radius:.5rem; font-size:.85rem; color:var(--muted); }
      .fig { display:grid; gap:.75rem; }
      .fig > canvas { width:100%; height:auto; display:block; border-radius:.5rem; border:1px solid rgba(255,255,255,.06); background:#0b101c; }
      .kicker { font-weight:700; color:var(--accent); letter-spacing:.04em; text-transform:uppercase; font-size:.85rem; }
      .list-tight { margin:.25rem 0 0 1.25rem; color:#d6dee6; }
      .meta-table { display:grid; grid-template-columns: 140px 1fr; gap:.25rem .75rem; }
      .meta-table .key { color:var(--muted); }
      .soft { background:#ffffff08; border:1px solid rgba(255,255,255,.08); border-radius:.5rem; padding:.75rem; }
    </style>
  </head>
  <body>
    <?php include __DIR__ . '/header.php'; ?>

    <main class="container">
      <section class="callout" aria-labelledby="livePreview">
        <h2 id="livePreview">Live Canvas Previews</h2>
        <p class="muted">These previews reuse the real canvas code from the site to render authentically.</p>
        <div class="fig">
          <!-- Reuse the home sine-wave canvas with defaults (no controls) -->
          <section id="js-demo-waves" aria-label="Sine wave preview">
            <canvas id="wavesCanvas" width="720" height="220"></canvas>
          </section>
          <p class="soft">Home page sine wave renderer (<a href="index.php#js-demo-waves">view in context</a>).</p>

          <!-- Plotter mini-preview. functions.js is tolerant to missing controls -->
          <canvas id="plotCanvas" width="720" height="220" aria-label="Function plot preview"></canvas>
          <p class="soft">Function plotter from the Playground (<a href="functions.php#plotter">open full demo</a>).</p>
        </div>
      </section>
    </main>

    <footer class="site-footer">
      <div class="container">
        <p>&copy; <span id="year"></span> Shader Book</p>
      </div>
    </footer>
    <script>document.getElementById('year').textContent = new Date().getFullYear();</script>
    <!-- Reuse existing demos so previews render authentically -->
    <script src="js/waves.js" defer></script>
    <script src="js/functions.js" defer></script>
  </body>
  </html>

