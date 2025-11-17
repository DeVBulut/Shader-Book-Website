<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shader Book - Function Playground</title>
    <link rel="stylesheet" href="styles.css">
    <style>
      main section { margin-bottom: 1.75rem; }
      .demo-meta { color: #98a6bc; font-size: .95rem; margin-bottom: .75rem; }
      .canvas-frame {
        background: #0b101c;
        border: 1px solid var(--border);
        border-radius: .75rem;
        padding: 1rem;
      }
      canvas.demo-canvas {
        width: 100%;
        display: block;
        max-width: 820px;
        border-radius: .5rem;
        border: 1px solid rgba(255,255,255,.05);
        background: radial-gradient(circle at 20% 20%, rgba(76,201,240,.1), rgba(9,13,22,.85));
      }
      .controls {
        margin-top: 1rem;
        display: grid;
        gap: .75rem 1rem;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        align-items: center;
      }
      .control {
        display: grid;
        gap: .35rem;
        color: #d6dee6;
      }
      .control label {
        font-size: .95rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: var(--muted);
      }
      .control input[type=range],
      .control select {
        width: 100%;
      }
      .control output {
        font-family: 'Fira Code', 'Cascadia Mono', Consolas, monospace;
        font-size: .9rem;
        color: var(--accent);
      }
      .notes {
        margin-top: .75rem;
        color: #93a3bd;
        font-size: .9rem;
      }
      details {
        margin-top: .75rem;
        background: rgba(255,255,255,.04);
        border-radius: .5rem;
        padding: .65rem .85rem;
        border: 1px solid rgba(255,255,255,.08);
      }
      details summary {
        font-weight: 600;
        cursor: pointer;
        color: var(--accent);
      }
      details[open] {
        border-color: var(--accent);
      }
    </style>
  </head>
  <body>
    <?php include __DIR__ . '/header.php'; ?>
    <main class="container">
      <section id="plotter" aria-labelledby="plotterTitle">
        <h2 id="plotterTitle">Function Plotter</h2>
        <p class="demo-meta">Live plot of classic shader helper functions with amplitude, frequency, and phase controls.</p>
        <div class="canvas-frame">
          <canvas id="plotCanvas" class="demo-canvas" width="840" height="280" role="img" aria-label="Animated function plot"></canvas>
        </div>
        <div class="controls" role="group" aria-label="Function plot controls">
          <div class="control">
            <label for="plotFunction">Function</label>
            <select id="plotFunction">
              <option value="sin">sin(x)</option>
              <option value="cos">cos(x)</option>
              <option value="tri">tri(x)</option>
              <option value="saw">saw(x)</option>
            </select>
          </div>
          <div class="control">
            <label for="plotAmplitude">Amplitude</label>
            <input id="plotAmplitude" type="range" min="0.1" max="1.25" step="0.05" value="0.9" aria-describedby="plotAmplitudeOut">
            <output id="plotAmplitudeOut" for="plotAmplitude">0.90</output>
          </div>
          <div class="control">
            <label for="plotFrequency">Frequency (pi)</label>
            <input id="plotFrequency" type="range" min="0.25" max="4" step="0.05" value="1.5" aria-describedby="plotFrequencyOut">
            <output id="plotFrequencyOut" for="plotFrequency">1.50</output>
          </div>
          <div class="control">
            <label for="plotSpeed">Phase Speed</label>
            <input id="plotSpeed" type="range" min="0.1" max="8" step="0.1" value="2.5" aria-describedby="plotSpeedOut">
            <output id="plotSpeedOut" for="plotSpeed">2.5</output>
          </div>
        </div>
        <p class="notes">Use Space to pause/resume. R resets controls.</p>
      </section>
    </main>
    <footer class="site-footer">
      <div class="container">
        <p>&copy; <span id="year"></span> Shader Book</p>
      </div>
    </footer>
    <script>document.getElementById('year').textContent = new Date().getFullYear();</script>
    <script src="js/functions.js" defer></script>
  </body>
  </html>

